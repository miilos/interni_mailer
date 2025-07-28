const API_URL = '/api/email-body'

/**** containers ****/

const templateContainer = document.querySelector('.templates-container')
const templateContentContainer = document.querySelector('.template-content-container')
const templateViewContainer = document.querySelector('.template-view-container')

/**** inputs ****/

const templateNameInput = document.getElementById('template-name')
const searchBtn = document.querySelector('.template-search-btn')
const testSendBtn = document.querySelector('.test-send-btn')

let templates = []

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
    const template = templates.find(template => template.name === name)

    // display the template code in the editor
    const beautifiedTemplateContent = vkbeautify.xml(template.content, 2)
    // editor.js contains the editor definition
    window.editor.setContent(beautifiedTemplateContent)

    // display the rendered template
    if (template.parsedBodyHtml) {
        templateViewContainer.innerHTML = template.parsedBodyHtml
    }
    else {
        templateViewContainer.innerHTML = template.content
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
    const activeTemplate = document.querySelector('.template--active')

    if (!activeTemplate) return

    const extensionElText = activeTemplate.querySelector('.template-format').innerText
    return formatExtension(extensionElText.toLowerCase())
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

    const res = await fetch('/api/email-body/render', {
        method: 'POST',
        headers: {
            'Content-type': 'application/json'
        },
        body: JSON.stringify({
            body: window.editor.getContent(),
            extension
        })
    })

    const json = await res.json()
    templateViewContainer.innerHTML = json.data.body
})
