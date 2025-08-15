const templatesContainer = document.querySelector('.email-templates-container')

const templateNameInput = document.querySelector('.email-template-search-input')

/**** fetch functions ****/

const fetchTemplates = async (apiUrl) => {
    const name = templateNameInput.value || ''

    const res = await fetch(`${apiUrl}?name=${name}`)
    const json = await res.json()

    return json.data.templates
}

/**** render functions ****/

const renderTemplates = (templates, includeDeleteIcon) => {
    clearTemplateResults()

    templates.forEach(template => {
        templatesContainer.insertAdjacentHTML('beforeend', `
            <div class="template">
                <h3 class="template-title">${template.name}</h3>
                ${template.bodyTemplateName ? `<p class="body-template-name">Body template: ${template.bodyTemplateName}</p>` : ''}
                ${includeDeleteIcon ? '<span class="material-symbols-outlined template-delete-icon">delete</span>' : ''}
            </div>
        `)
    })
}

/**** util functions ****/

const clearTemplateResults = () => {
    templatesContainer.innerHTML = ''
}

/**** export functions ****/

export const fetchAndRenderEmailTemplates = async (apiUrl, includeDeleteIcon = false) => {
    const templates = await fetchTemplates(apiUrl)
    renderTemplates(templates, includeDeleteIcon)
    return templates
}

export const attachEmailTemplateEventListeners = (listenerFn) => {
    templatesContainer.querySelectorAll('.template').forEach(curr => {
        curr.addEventListener('click', listenerFn)
    })
}

export const attachOnDeleteEventListeners = (listenerFn) => {
    document.querySelectorAll('.template-delete-icon').forEach(curr => {
        curr.addEventListener('click', listenerFn)
    })
}
