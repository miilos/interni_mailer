import { fetchAndRenderEmailTemplates, attachEmailTemplateEventListeners } from "./emailTemplateUtils.js";
import { fetchAndRenderBodyTemplates, attachBodyTemplateEventListeners } from "./bodyTemplateUtils.js";
import * as utils from './emailDataUtils.js'

const API_URL = '/api/templates'
const PARSE_API_URL = '/api/email-body/render'
const BODY_TEMPLATES_URL = '/api/email-body'

/**** containers ****/

const variablesContainer = document.querySelector('.variables')

/**** inputs ****/

const emailTemplateNameInput = document.querySelector('.email-template-search-input')
const emailTemplateSearchBtn = document.querySelector('.email-template-search-btn')
const bodyTemplateNameInput = document.querySelector('.body-template-search-input')
const bodyTemplateSearchBtn = document.querySelector('.body-template-search-btn')
const subjectInput = document.getElementById('subject')
const fromInput = document.getElementById('from')
const toInput = document.getElementById('to')
const ccInput = document.getElementById('cc')
const bccInput = document.getElementById('bcc')
const sendBtn = document.querySelector('.send-btn')
const addVariableNameInput = document.getElementById('add-variable-name')
const addVariableValueInput = document.getElementById('add-variable-value')
const addVariableBtn = document.querySelector('.add-variable-btn')
const saveAsTemplateBtn = document.querySelector('.save-as-template-btn')

/**** UI elements ****/

const toAddresses = document.querySelector('.to-address-container')
const ccAddresses = document.querySelector('.cc-address-container')
const bccAddresses = document.querySelector('.bcc-address-container')

const previewSubject = document.querySelector('.preview-subject')
const previewFrom = document.querySelector('.preview-from')
const previewTo = document.querySelector('.preview-to')
const previewBody = document.querySelector('.preview-body')

let templates = []
let bodyTemplates = []
let activeTemplate = null
let activeBodyTemplate = null
const email = {
    subject: '',
    from: '',
    to: [],
    cc: [],
    bcc: [],
    body: '',
    bodyTemplate: '',
    emailTemplate: '',
    variables: {}
}

/**** email state update functions ****/
/**** each change to the email state will also trigger a change to the preview panel ****/

const updateEmailSubject = (subject) => {
    email.subject = subject
    previewSubject.innerHTML = email.subject
}

const updateEmailFrom = (from) => {
    email.from = from
    previewFrom.innerHTML = '<b>From: </b>' + email.from
}

const updateEmailTo = (to, clearFirst = false) => {
    if (clearFirst) {
        email.to = []
    }

    if(!Array.isArray(to)) {
        to = [to]
    }
    email.to.push(...to)

    previewTo.innerHTML = formatAddressList(email.to)
}

const removeEmailTo = (address) => {
    email.to = email.to.filter(addr => addr !== address)
    previewTo.innerHTML = formatAddressList(email.to)
}

const updateEmailCC = (cc, clearFirst = false) => {
    if (clearFirst) {
        email.cc = []
    }

    if(!Array.isArray(cc)) {
        cc = [cc]
    }
    email.cc.push(...cc)
}

const removeEmailCc = (address) => {
    email.cc = email.cc.filter(addr => addr !== address)
}

const updateEmailBCC = (bcc, clearFirst = false) => {
    if (clearFirst) {
        email.bcc = []
    }

    if(!Array.isArray(bcc)) {
        bcc = [bcc]
    }
    email.bcc.push(...bcc)
}

const removeEmailBcc = (address) => {
    email.bcc = email.bcc.filter(addr => addr !== address)
}

const updateEmailBody = async (body) => {
    email.body = body

    const renderedBody = await fetchRenderedEmailBody(email.body)
    previewBody.innerHTML = renderedBody
}

const updateEmailBodyTemplate = (bodyTemplate) => {
    email.bodyTemplate = bodyTemplate
}

const updateEmailTemplate = (emailTemplate) => {
    email.emailTemplate = emailTemplate
}

const updateEmailVariables = (name, value) => {
    email.variables[name] = value
}

const clearEmailVariables = () => {
    email.variables = {}
}

const updateEmail = async (data) => {
    updateEmailSubject(data.subject)
    updateEmailFrom(data.from)
    updateEmailTo(data.to, true)
    updateEmailCC(data.cc, true)
    updateEmailBCC(data.bcc, true)
    await updateEmailBody(data.body)
    updateEmailBodyTemplate(data.bodyTemplate)
    updateEmailTemplate(data.emailTemplate)
}

/**** data fetching ****/

const fetchRenderedEmailBody = async (body) => {
    const res = await fetch(PARSE_API_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            body,
            // always use twig as an extension because the twig parser checks if the body contains mjml, and parses it if it does
            extension: 'twig.html',
            variables: email.variables
        })
    })

    const json = await res.json()
    return json.data.body
}

/**** click listener functions for dynamically generated content & elements that share the same listener ****/

const onSearchResultClick = async (e) => {
    const parent = e.target.closest('.template')
    const name = parent.querySelector('.template-title').innerText
    activeTemplate = templates.find(template => template.name === name)

    clearEmailVariables()
    renderEmailTemplate()

    if (activeTemplate.bodyTemplate) {
        email.variables = activeTemplate.bodyTemplate.variables
    }
    else {
        clearEmailVariables()
    }

    renderVariables()

    await updateEmail({
        subject: activeTemplate.subject,
        from: activeTemplate.fromAddr,
        to: activeTemplate.toAddr,
        cc: activeTemplate.cc,
        bcc: activeTemplate.bcc,
        body: activeTemplate.bodyTemplate ? activeTemplate.bodyTemplate.content : activeTemplate.body,
        bodyTemplate: activeTemplate.bodyTemplate ? activeTemplate.bodyTemplate.name : '',
        emailTemplate: activeTemplate.name || ''
    })
}

const onBodyTemplateResultClick = (e) => {
    const parent = e.target.closest('.template')
    const name = parent.querySelector('.template-title').innerText
    activeBodyTemplate = bodyTemplates.find(template => template.name === name)

    clearEmailVariables()
    const templateVariables = activeBodyTemplate.variables
    for (const [key, value] of Object.entries(templateVariables)) {
        updateEmailVariables(key, value)
    }
    renderVariables()

    const beautifiedContent = utils.beautify(activeBodyTemplate.content)
    window.editor.setContent(beautifiedContent)
    updateEmailBody(activeBodyTemplate.content)
    updateEmailBodyTemplate(name)
}

const addAddressElement = (e, addressListName) => {
    if (e.key === 'Enter') {
        document.getElementById('errmsg-to').style.display = 'none'

        const address = e.target.value
        const parent = e.target.closest('.send-input-container')
        const addressContainer = parent.querySelector('.address-container')

        utils.renderAddress(address, addressContainer, addressListName, onRemoveAddressClick)
        e.target.value = ''

        switch (addressListName) {
            case 'to':
                updateEmailTo(address)
                break
            case 'cc':
                updateEmailCC(address)
                break
            case 'bcc':
                updateEmailBCC(address)
                break
        }
    }
}

const onRemoveAddressClick = (e, addressListName) => {
    const addressEl = e.target.closest('.address')
    const address = addressEl.querySelector('.address-content').innerText

    addressEl.remove()

    switch (addressListName) {
        case 'to':
            removeEmailTo(address)
            break
        case 'cc':
            removeEmailCc(address)
            break
        case 'bcc':
            removeEmailBcc(address)
            break
    }
}

const onVariableChange = async (e) => {
    const newValue = e.target.value
    // all the input elements have ids like: var-[variable name]
    const varName = e.target.id.replace(/^var-/, '')

    email.variables[varName] = newValue

    // rerender email with updated variable values
    const renderedBody = await fetchRenderedEmailBody(email.body)
    previewBody.innerHTML = renderedBody
}

/**** render functions ****/

const renderEmailTemplate = () => {
    subjectInput.value = activeTemplate.subject
    fromInput.value = activeTemplate.fromAddr
    utils.renderAddressList(activeTemplate.toAddr, toAddresses, 'to', onRemoveAddressClick )
    utils.renderAddressList(activeTemplate.cc, ccAddresses, 'cc', onRemoveAddressClick)
    utils.renderAddressList(activeTemplate.bcc, bccAddresses, 'bcc', onRemoveAddressClick)

    let editorText = ''
    if (activeTemplate.bodyTemplate) {
        editorText = utils.beautify(activeTemplate.bodyTemplate.content)
    }
    else {
        editorText = activeTemplate.body
    }

    window.editor.setContent(editorText)
}

const renderVariables = () => {
    clearVariableContainer()
    let variablesHtml = ''

    for (const [key, value] of Object.entries(email.variables)) {
        variablesHtml += `
            <div class="variable-input-container">
                <label for="var-${key}">${key}: </label>
                <input class="variable-input send-input" id="var-${key}" name="var-${key}" value="${value}" />
            </div>
        `
    }

    variablesContainer.innerHTML = variablesHtml

    variablesContainer.querySelectorAll('.send-input').forEach(input => {
        input.addEventListener('change', onVariableChange)
    })
}

/**** util functions ****/

const formatAddressList = (addresses) => {
    return '<b>To: </b>' + addresses.join(', ')
}

const validateBeforeSend = () => {
    let passedValidation = true

    if (!email.subject) {
        document.getElementById('errmsg-subject').style.display = 'block'
        passedValidation = false
    }

    if (!email.from) {
        document.getElementById('errmsg-from').style.display = 'block'
        passedValidation = false
    }

    if (email.to.length === 0) {
        document.getElementById('errmsg-to').style.display = 'block'
        passedValidation = false
    }

    return passedValidation
}

const clearVariableContainer = () => {
    variablesContainer.innerHTML = ''
}

/**** event listeners ****/

window.addEventListener('load', async () => {
    templates = await fetchAndRenderEmailTemplates(API_URL)
    attachEmailTemplateEventListeners(onSearchResultClick)

    bodyTemplates = await fetchAndRenderBodyTemplates(BODY_TEMPLATES_URL)
    attachBodyTemplateEventListeners(onBodyTemplateResultClick)
})

emailTemplateNameInput.addEventListener('keydown', async (e) => {
    if (e.key === 'Enter') {
        templates = await fetchAndRenderEmailTemplates(API_URL)
        attachEmailTemplateEventListeners(onSearchResultClick)
    }
});

emailTemplateSearchBtn.addEventListener('click', async (e) => {
    templates = await fetchAndRenderEmailTemplates(API_URL)
    attachEmailTemplateEventListeners(onSearchResultClick)
})

bodyTemplateNameInput.addEventListener('keydown', async (e) => {
    if (e.key === 'Enter') {
        templates = await fetchAndRenderBodyTemplates(BODY_TEMPLATES_URL)
        attachBodyTemplateEventListeners(onBodyTemplateResultClick)
    }
})

bodyTemplateSearchBtn.addEventListener('click', async (e) => {
    templates = await fetchAndRenderBodyTemplates(BODY_TEMPLATES_URL)
    attachBodyTemplateEventListeners(onBodyTemplateResultClick)
})

/**** change listeners to update current email object and the preview panel whenever something in the UI changes ****/

subjectInput.addEventListener('input', (e) => {
    updateEmailSubject(e.target.value)
    document.getElementById('errmsg-subject').style.display = 'none'
})

fromInput.addEventListener('input', (e) => {
    updateEmailFrom(e.target.value)
    document.getElementById('errmsg-from').style.display = 'none'
})

toInput.addEventListener('keydown', (e) => {
    addAddressElement(e, 'to')
})

ccInput.addEventListener('keydown', (e) => {
    addAddressElement(e, 'cc')
})

bccInput.addEventListener('keydown', (e) => {
    addAddressElement(e, 'bcc')
})

window.addEventListener('editorUpdate', async (e) => {
    const editorContent = window.editor.getContent()
    await updateEmailBody(editorContent)
})

sendBtn.addEventListener('click', async () => {
    if (!validateBeforeSend()) return

    const res = await fetch('/api/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            ...email
        })
    })

    const json = await res.json()

    if (!res.ok) {
        openModal('Error', json.details)
        return
    }

    utils.showSuccessMessage()
})

addVariableBtn.addEventListener('click', () => {
    const name = addVariableNameInput.value
    const value = addVariableValueInput.value

    updateEmailVariables(name, value)

    renderVariables()

    addVariableNameInput.value = ''
    addVariableValueInput.value = ''
})

saveAsTemplateBtn.addEventListener('click', async () => {
    openInputModal('Save as template', 'Enter the template name:')
})

document.querySelector('.modal-save-btn').addEventListener('click', async (e) => {
    const templateName = document.querySelector('.modal-input').value

    if (!templateName) {
        document.getElementById('errmsg-template-name').style.display = 'block'
        return
    }

    const res = await fetch('/api/templates', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            name: templateName,
            subject: email.subject,
            from: email.from,
            to: email.to,
            cc: email.cc,
            bcc: email.bcc,
            body: email.body,
            bodyTemplateName: email.bodyTemplate
        })
    })
    const json = await res.json()

    if (!res.ok) {
        document.getElementById('errmsg-create-error').style.display = 'block'
        document.getElementById('errmsg-create-error').innerText = json.details
        return
    }

    document.querySelector('.create-template-success-msg').style.display = 'flex';
    setTimeout(() => {
        document.querySelector('.create-template-success-msg').style.display = 'none';
        closeModal()
    }, 2000)
})

document.querySelector('.modal-input').addEventListener('input', () => {
    document.getElementById('errmsg-template-name').style.display = 'none'
})
