const API_URL = '/api/email-body'

/**** containers ****/

const templateContainer = document.querySelector('.templates-container')
const templateVariablesContainer = document.querySelector('.template-variables')
const templateViewContainer = document.querySelector('.template-view-container')

/**** inputs ****/

const templateNameInput = document.getElementById('template-name')
const searchBtn = document.querySelector('.template-search-btn')
const testSendBtn = document.querySelector('.test-send-btn')
const saveBtn = document.querySelector('.save-btn')
const addVariableNameInput = document.getElementById('add-variable-name')
const addVariableValueInput = document.getElementById('add-variable-value')
const addVariableBtn = document.querySelector('.add-variable-btn')

let templates = []

// the currently selected template
let activeTemplate = null

/**** data fetching ****/

const fetchTemplates = async () => {
    const name = templateNameInput.value || ''

    const res = await fetch(API_URL+`?name=${name}`)
    const json = await res.json()

    return json.data.templates
}

/**** click listener function ****/

const onTemplateResultClick = (e) => {
    clearTemplateView()

    const parent = e.target.closest('.template')
    setActiveClassForActiveTemplate(parent)

    const name = parent.querySelector('.template-title').innerText
    activeTemplate = templates.find(template => template.name === name)

    // display the template code in the editor
    const beautifiedTemplateContent = vkbeautify.xml(activeTemplate.content, 2)
    // editor.js contains the editor definition
    window.editor.setContent(beautifiedTemplateContent)

    // display the rendered template
    if (activeTemplate.parsedBodyHtml) {
        templateViewContainer.innerHTML = activeTemplate.parsedBodyHtml
    }
    else {
        templateViewContainer.innerHTML = activeTemplate.content
    }

    // display the template variables
    if (activeTemplate.extension === 'html.twig') {
        let variablesHtml = ``

        for (const [key, value] of Object.entries(activeTemplate.variables)) {
            variablesHtml += `
                <div class="variable">
                    <label for="var-${key}">${key}: </label>
                    <input class="variable-input" id="var-${key}" name="var-${key}" value="${value}" />
                </div>
            `
        }

        templateVariablesContainer.style.display = 'block'
        templateVariablesContainer.querySelector('.variables-container').innerHTML = variablesHtml
    }
    else {
        templateVariablesContainer.style.display = 'none'
    }
}

/**** render functions ****/

const clearTemplateView = () => {
    window.editor.setContent('Select a template to see the code...')
    templateViewContainer.innerHTML = ''
}

const renderTemplates = () => {
    templates.forEach(template => {
        // the 2 formats are: html.twig and mjml.html,
        // so the .html extensions need to be removed
        // when displaying the template info
        let extension = template.extension

        switch (extension) {
            case 'html.twig':
                extension = extension.split('.')[1]
                break
            case 'mjml.html':
                extension = extension.split('.')[0]
                break
            default:
                break
        }

        templateContainer.insertAdjacentHTML('beforeend',
            `
                <div class="template">
                    <h3 class="template-title">${template.name}</h3>
                    <p class="template-format template-format--${extension}">${extension !== 'twig' ? extension.toUpperCase() : extension.charAt(0).toUpperCase()+extension.slice(1)}</p>
                    <span class="material-symbols-outlined template-delete-icon">
                        delete
                    </span>
                </div>
            `);
    })

    document.querySelectorAll('.template').forEach(curr => {
        curr.addEventListener('click', onTemplateResultClick)
    })

    clearTemplateView()
}

const setActiveClassForActiveTemplate = (activeTemplate) => {
    document.querySelectorAll('.template').forEach(curr => {
        curr.classList.remove('template--active')
    })
    activeTemplate.classList.add('template--active')
}

/**** util functions ****/

const fetchAndRenderTemplates = async () => {
    templates = await fetchTemplates()
    renderTemplates()
}

const formatExtension = (extension) => {
    let formattedExtension = extension

    switch (extension) {
        case 'twig':
            formattedExtension = 'html.'+extension
            break
        case 'mjml':
            formattedExtension = extension+'.html'
            break
        default:
            break
    }

    return formattedExtension
}

const getActiveTemplateExtension = () => {
    if (!activeTemplate) return

    return formatExtension(activeTemplate.extension)
}

const getTemplateVariableInputValues = () => {
    let variables = {}

    // go through the selected template's variables and get the input values
    // for them instead of just using its variables property
    // in case the user changed the value of a variable
    if (templateVariablesContainer.style.display !== 'none') {
        for (const [key, value] of Object.entries(activeTemplate.variables)) {
            const input = document.getElementById(`var-${key}`)
            variables[key] = input.value
        }
    }

    return variables
}

/**** event listeners ****/

window.addEventListener('load', async (e) => {
    await fetchAndRenderTemplates()
})

templateNameInput.addEventListener('keydown', async (e) => {
    if (e.key === 'Enter') {
        templateContainer.innerHTML = ''
        await fetchAndRenderTemplates()
    }
});

searchBtn.addEventListener('click', async (e) => {
    templateContainer.innerHTML = ''
    await fetchAndRenderTemplates()
})

testSendBtn.addEventListener('click', async (e) => {
    const extension = getActiveTemplateExtension()
    const variables = getTemplateVariableInputValues()

    const res = await fetch('/api/email-body/render', {
        method: 'POST',
        headers: {
            'Content-type': 'application/json'
        },
        body: JSON.stringify({
            body: window.editor.getContent(),
            extension,
            variables
        })
    })

    const json = await res.json()
    console.log(json)

    if (json.status === 'fail') {
        templateViewContainer.innerHTML = `
            <div class="error-msg-container">
                <p class="error-msg">${json.details}</p>
            </div>
        `

        return
    }

    templateViewContainer.innerHTML = json.data.body
})

saveBtn.addEventListener('click', async (e) => {

})

addVariableBtn.addEventListener('click', (e) => {
    const name = addVariableNameInput.value
    const value = addVariableValueInput.value

    if (!name || !value) return

    const variablesContainer = templateVariablesContainer.querySelector('.variables-container')
    variablesContainer.insertAdjacentHTML('beforeend', `
        <div class="variable">
            <label for="var-${name}">${name}: </label>
            <input class="variable-input" id="var-${name}" name="var-${name}" value="${value}" />
        </div>
    `)

    addVariableNameInput.value = ''
    addVariableValueInput.value = ''

    // update the active template and also the variables array which will be sent to the api
    activeTemplate.variables[name] = value
    templates.forEach(template => {
        if (template.name === activeTemplate.name) {
            template.variables = activeTemplate.variables
        }
    })
})
