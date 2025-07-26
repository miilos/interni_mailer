const API_URL = '/api/email-body'

/**** containers ****/

const templateContainer = document.querySelector('.templates-container')
const templateContentContainer = document.querySelector('.template-content-container')
const templateViewContainer = document.querySelector('.template-view-container')

/**** search inputs ****/

const templateNameInput = document.getElementById('template-name')
const searchBtn = document.querySelector('.template-search-btn')

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
    // editor.js contains the event listener that updates the editor text
    document.dispatchEvent(new CustomEvent('updateEditor', {
        detail: { content: beautifiedTemplateContent }
    }));

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
    templateViewContainer.innerHTML = ''
}

const renderTemplates = () => {
    templates.forEach(template => {
        // there's 3 formats: html, twig.html and mjml.html,
        // so for twig and mjml the .html extensions need to be removed
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

const fetchAndRenderTemplates = async () => {
    templates = await fetchTemplates()
    renderTemplates()
}

const setActiveClassForActiveTemplate = (activeTemplate) => {
    document.querySelectorAll('.template').forEach(curr => {
        curr.classList.remove('template--active')
    })
    activeTemplate.classList.add('template--active')
}

/**** event listeners ****/

window.addEventListener('load', async (e) => {
    await fetchAndRenderTemplates()
})

document.addEventListener('keydown', async (e) => {
    if (e.key === 'Enter') {
        templateContainer.innerHTML = ''
        await fetchAndRenderTemplates()
    }
});

searchBtn.addEventListener('click', async (e) => {
    templateContainer.innerHTML = ''
    await fetchAndRenderTemplates()
})
