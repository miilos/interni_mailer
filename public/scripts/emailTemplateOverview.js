import { fetchAndRenderEmailTemplates, attachEmailTemplateEventListeners } from './emailTemplateUtils.js'
import * as utils from './emailDataUtils.js'

const API_URL = '/api/templates'

const templatesContainer = document.querySelector('.email-templates-container')
const templateDataContainer = document.querySelector('.email-template-data')

const templateNameInput = document.querySelector('.email-template-search-input')
const searchBtn = document.querySelector('.email-template-search-btn')
const subjectInput = document.getElementById('subject')
const fromInput = document.getElementById('from')
const toInput = document.getElementById('to')
const ccInput = document.getElementById('cc')
const bccInput = document.getElementById('bcc')
const bodyTemplateSelect = document.getElementById('body-template')
const saveBtn = document.querySelector('.save-btn')

const toAddresses = document.querySelector('.to-address-container')
const ccAddresses = document.querySelector('.cc-address-container')
const bccAddresses = document.querySelector('.bcc-address-container')

let activeTemplate = null
let templates = []
let bodyTemplates = []

let to = []
let cc = []
let bcc = []

/**** fetch functions ****/

const fetchBodyTemplates = async () => {
    const res = await fetch('/api/email-body')
    const json = await res.json()
    return json.data.templates
}

/**** event listeners for dynamically generated content ****/

const onSearchResultClick = (e) => {
    const parent = e.target.closest('.template')
    const name = parent.querySelector('.template-title').innerText
    const template = templates.find(curr => curr.name === name)

    utils.setActiveClassForActiveTemplate(parent)
    renderTemplateData(template)

    activeTemplate = template
    to = [...template.toAddr]
    cc = [...template.cc]
    bcc = [...template.bcc]
}

const onRemoveAddressClick = (e, addressListName) => {
    const addressEl = e.target.closest('.address')
    const address = addressEl.querySelector('.address-content').innerText

    switch (addressListName) {
        case 'to':
            to = to.filter(curr => curr !== address)
            break
        case 'cc':
            cc = cc.filter(curr => curr !== address)
            break
        case 'bcc':
            bcc = bcc.filter(curr => curr !== address)
            break
    }



    addressEl.remove()
}

const onAddAddress = (e, addressListName) => {
    if (e.key === 'Enter') {
        const address = e.target.value
        const parent = e.target.closest('.template-data-input-container')
        const addressContainer = parent.querySelector('.address-container')

        utils.renderAddress(address, addressContainer, addressListName, onRemoveAddressClick, 'secondary')
        e.target.value = ''

        switch (addressListName) {
            case 'to':
                to.push(address)
                break
            case 'cc':
                cc.push(address)
                break
            case 'bcc':
                bcc.push(address)
                break
        }
    }
}

/**** render functions ****/

const renderTemplateData = (template) => {
    subjectInput.value = template.subject
    fromInput.value = template.fromAddr
    utils.renderAddressList(template.toAddr, toAddresses, 'to', onRemoveAddressClick, 'secondary')
    utils.renderAddressList(template.cc, ccAddresses, 'cc', onRemoveAddressClick, 'secondary')
    utils.renderAddressList(template.bcc, bccAddresses, 'bcc', onRemoveAddressClick, 'secondary')

    const body = template.bodyTemplate ? template.bodyTemplate.content : template.body
    const beautifiedBody = utils.beautify(body)
    window.editor.setContent(beautifiedBody)

    if (template.bodyTemplate) {
        Array.from(bodyTemplateSelect.options).forEach(option => {
            if (option.textContent === template.bodyTemplate.name) {
                bodyTemplateSelect.selectedIndex = option.index
            }
        })
    }
    else {
        bodyTemplateSelect.selectedIndex = 0
    }
}

const renderBodyTemplateSelectOptions = (templates) => {
    templates.forEach(template => {
        bodyTemplateSelect.insertAdjacentHTML('beforeend', `
            <option value="${template.name}">${template.name}</option>
        `)
    })
}

/**** util functions ****/

const fetchAndRenderBodyTemplateSelectOptions = async () => {
    bodyTemplates = await fetchBodyTemplates()
    renderBodyTemplateSelectOptions(bodyTemplates)
}

const getUpdateData = () => {
    activeTemplate.subject = subjectInput.value
    activeTemplate.fromAddr = fromInput.value
    activeTemplate.toAddr = to
    activeTemplate.cc = cc
    activeTemplate.bcc = bcc

    const bodyTemplateName = bodyTemplateSelect.value
    if (bodyTemplateName !== '-') {
        activeTemplate.bodyTemplateName = bodyTemplateSelect.value
    }
    else {
        activeTemplate.body = window.editor.getContent()
        activeTemplate.bodyTemplateName = null
    }
}

const search = async () => {
    clearData()
    templates = await fetchAndRenderEmailTemplates(API_URL)
    attachEmailTemplateEventListeners(onSearchResultClick)
}

const clearData = () => {
    activeTemplate = null
    subjectInput.value = ''
    fromInput.value = ''
    toInput.value = ''
    toAddresses.innerHTML = ''
    to = []
    ccInput.value = ''
    ccAddresses.innerHTML = ''
    cc = []
    bccInput.value = ''
    bccAddresses.innerHTML = ''
    bcc = []
    bodyTemplateSelect.selectedIndex = 0
    window.editor.setContent('')
}

/**** event listeners ****/

window.addEventListener('load', async (e) => {
    templates = await fetchAndRenderEmailTemplates(API_URL)
    attachEmailTemplateEventListeners(onSearchResultClick)
    await fetchAndRenderBodyTemplateSelectOptions()
})

templateNameInput.addEventListener('keydown', async (e) => {
    if (e.key === 'Enter') {
        await search()
    }
})

searchBtn.addEventListener('click', async (e) => {
    search()
})

toInput.addEventListener('keydown', (e) => onAddAddress(e, 'to'))
ccInput.addEventListener('keydown', (e) => onAddAddress(e, 'cc'))
bccInput.addEventListener('keydown', (e) => onAddAddress(e, 'bcc'))

bodyTemplateSelect.addEventListener('change', (e) => {
    const name = e.target.value
    const template = bodyTemplates.find(curr => curr.name === name)

    if (template) {
        window.editor.setContent(utils.beautify(template.content))
    }
    else {
        window.editor.setContent(activeTemplate.body)
    }
})

saveBtn.addEventListener('click', async (e) => {
    if (!activeTemplate) return

    // add updated data into activeTemplate
    getUpdateData()

    const res = await fetch(`${API_URL}/${activeTemplate.id}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(activeTemplate)
    })
    const json = await res.json()

    if (!res.ok) {
        openModal('Error!', json.details)
        return
    }

    utils.showSuccessMessage()
})
