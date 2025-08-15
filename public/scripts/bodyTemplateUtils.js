const templatesContainer = document.querySelector('.body-templates-container')

const templateNameInput = document.querySelector('.body-template-search-input')

/**** fetch functions ****/

const fetchTemplates = async (apiUrl) => {
    const name = templateNameInput.value || ''

    const res = await fetch(`${apiUrl}?name=${name}`)
    const json = await res.json()

    return json.data.templates
}

/**** render functions ****/

const renderBodyTemplates = (templates, includeDeleteIcon) => {
    clearTemplateResults()

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

        templatesContainer.insertAdjacentHTML('beforeend',
            `
                <div class="template body-template">
                    <h3 class="template-title">${template.name}</h3>
                    <p class="template-format template-format--${extension}">${extension !== 'twig' ? extension.toUpperCase() : extension.charAt(0).toUpperCase() + extension.slice(1)}</p>
                    ${includeDeleteIcon ? '<span class="material-symbols-outlined template-delete-icon">delete</span>' : ''}
                </div>
            `);
    })
}

/**** util functions ****/

const clearTemplateResults = () => {
    templatesContainer.innerHTML = ''
}

/**** export functions ****/

export const fetchAndRenderBodyTemplates = async (apiUrl, includeDeleteIcon = false) => {
    const templates = await fetchTemplates(apiUrl)
    renderBodyTemplates(templates, includeDeleteIcon)
    return templates
}

export const attachBodyTemplateEventListeners = (listenerFn) => {
    document.querySelectorAll('.body-template').forEach(curr => {
        curr.addEventListener('click', listenerFn)
    })
}

export const attachDeleteEventListener = (listenerFn) => {
    document.querySelectorAll('.template-delete-icon').forEach(curr => {
        curr.addEventListener('click', listenerFn)
    })
}
