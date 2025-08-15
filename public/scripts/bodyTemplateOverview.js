import {
    fetchAndRenderBodyTemplates,
    attachBodyTemplateEventListeners,
    attachDeleteEventListener
} from './bodyTemplateUtils.js'
import * as utils from './emailDataUtils.js'

const API_URL = '/api/email-body'

/**** containers ****/

const templateContainer = document.querySelector('.body-templates-container')
const templateVariablesContainer = document.querySelector('.template-variables')
const templateViewContainer = document.querySelector('.template-view-container')
const changelogEntriesContainer = document.querySelector('.changelog-data-entries')

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

/**** click listener function ****/

const onTemplateResultClick = (e) => {
    // prevent this function from running if the delete button was clicked,
    // since it also belongs to the div
    if (e.target.classList.contains('template-delete-icon')) return

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

        templateVariablesContainer.classList.remove('template-variables--hidden')
        templateVariablesContainer.querySelector('.variables-container').innerHTML = variablesHtml
    }
    else {
        templateVariablesContainer.classList.add('template-variables--hidden')
    }

    renderChangelog()
}

const onDeleteTemplate = async (e) => {
    const parent = e.target.closest('.template')
    const name = parent.querySelector('.template-title').innerText
    const template = templates.find(template => template.name === name)

    const res = await fetch(API_URL+`/${template.id}`, {
        method: 'DELETE'
    })

    if (!res.ok) {
        const json = await res.json()
        openModal('Error', json.details)
        return
    }

    templates = templates.filter(curr => curr.id != template.id)

    parent.remove()
    clearTemplateView()
}

const onChangelogCellClick = (e) => {
    const parent = e.target.closest('.table-entry-cell')
    const row = e.target.closest('.table-entry')
    const logId = parseInt(row.dataset.logId)
    const log = activeTemplate.changelog.find(curr => curr.id === logId)

    if (parent.classList.contains('cell-template-content')) {
        const escapedContent = parseHtml(log.content)

        const content = `<pre>${utils.beautify(escapedContent)}</pre>`

        openModal('Content', content)
        return
    }

    if (parent.classList.contains('cell-template-body')) {
        openModal('Parsed Body', log.parsedBodyHtml)
        return
    }

    if (parent.classList.contains('cell-template-diff')) {
        const diffHtml = formatDiff(log.diff)
        openModal('Diff', diffHtml)
    }
}

/**** render functions ****/

const clearTemplateView = () => {
    window.editor.setContent('Select a template to see the code...')
    templateViewContainer.innerHTML = ''

    // hide variables container
    const container = document.querySelector('.template-variables')
    container.classList.add('template-variables--hidden')
}

const renderChangelog = () => {
    changelogEntriesContainer.innerHTML = ''

    activeTemplate.changelog.forEach(log => {
        changelogEntriesContainer.insertAdjacentHTML('beforeend', `
            <div class="table-entry table-data" data-log-id="${log.id}">
                <div class="table-entry-cell table-entry-cell--changelog cell-template-name">
                    <p class="table-entry-text">${activeTemplate.name}</p>
                </div>
                <div class="table-entry-cell table-entry-cell--changelog cell-template-content table-entry-cell--clickable">
                    <p class="table-entry-text">View content</p>
                </div>
                <div class="table-entry-cell table-entry-cell--changelog cell-template-body table-entry-cell--clickable">
                    <p class="table-entry-text">View parsed body</p>
                </div>
                <div class="table-entry-cell table-entry-cell--changelog cell-template-diff table-entry-cell--clickable">
                    <p class="table-entry-text">View diff</p>
                </div>
                <div class="table-entry-cell table-entry-cell--changelog cell-changedat">
                    <p class="table-entry-text">${utils.formatDate(log.createdAt)}</p>
                </div>
            </div>
        `)
    })

    changelogEntriesContainer.querySelectorAll('.table-entry-cell').forEach(curr => {
        curr.addEventListener('click', onChangelogCellClick)
    })
}

/**** util functions ****/

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

const setActiveClassForActiveTemplate = (activeTemplate) => {
    document.querySelectorAll('.template').forEach(curr => {
        curr.classList.remove('template--active')
    })
    activeTemplate.classList.add('template--active')
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
    if (!templateVariablesContainer.classList.contains('template-variables--hidden')) {
        for (const [key, value] of Object.entries(activeTemplate.variables)) {
            const input = document.getElementById(`var-${key}`)
            variables[key] = input.value
        }
    }

    return variables
}

const formatDiff = (diffObj) => {
    let diffHtml = '<div class="diff">'
    for (const [key, value] of Object.entries(diffObj)) {
        diffHtml += `
               <div class="diff-property">
                    <h4 class="diff-property-name">${key}:</h4>
                    <div class="diff-content-container">
                        <h5 class="diff-content-label">Old:</h5>
                        <div class="diff-content">
                            ${formatChangelogOldAndNewViews(value.old)}
                        </div>
                    </div>
                    <div class="diff-content-container">
                        <h5 class="diff-content-label">New:</h5>
                        <div class="diff-content">
                            ${formatChangelogOldAndNewViews(value.new)}
                        </div>
                    </div>
                    <div class="diff-content-container">
                        <h5 class="diff-content-label">Diff:</h5>
                        <div class="diff-content diff-content-diff-text">
                            ${formatDiffText(value.diff)}
                        </div>
                    </div>
               </div>
        `.trim()
    }

    diffHtml += '</div>'
    return diffHtml
}

const parseHtml = (html) => {
    return html.replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;")
}

const formatChangelogOldAndNewViews = (changelogVal) => {
    let valueHtml = ''

    if (typeof changelogVal === 'object') {
        for (const [key, value] of Object.entries(changelogVal)) {
            valueHtml += `
                <div>
                    <b>${key}: </b>
                    <span>${value}</span>
                </div>
            `
        }
    }
    else {
        valueHtml = changelogVal
    }

    return valueHtml
}

const formatDiffText = (diff) => {
    return parseHtml(diff).split('\n').map(curr => {
        if (curr.startsWith('+')) {
            return `<span class="diff-new">${curr}</span>`
        }

        if (curr.startsWith('-')) {
            return `<span class="diff-original">${curr}</span>`
        }

        return curr
    }).join('\n')
}

/**** event listeners ****/

window.addEventListener('load', async (e) => {
    templates = await fetchAndRenderBodyTemplates(API_URL, true)
    attachBodyTemplateEventListeners(onTemplateResultClick)
    attachDeleteEventListener(onDeleteTemplate)
})

templateNameInput.addEventListener('keydown', async (e) => {
    if (e.key === 'Enter') {
        await fetchAndRenderBodyTemplates(API_URL, true)
        attachBodyTemplateEventListeners(onTemplateResultClick)
        attachDeleteEventListener(onDeleteTemplate)
    }
});

searchBtn.addEventListener('click', async (e) => {
    await fetchAndRenderBodyTemplates(API_URL, true)
    attachBodyTemplateEventListeners(onTemplateResultClick)
    attachDeleteEventListener(onDeleteTemplate)
})

testSendBtn.addEventListener('click', async (e) => {
    if (!activeTemplate) return

    const extension = getActiveTemplateExtension()
    const variables = getTemplateVariableInputValues()

    const res = await fetch(`${API_URL}/render`, {
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
    if (!activeTemplate) return

    activeTemplate.content = window.editor.getContent()
    activeTemplate.variables = getTemplateVariableInputValues()

    const res = await fetch(`${API_URL}/${activeTemplate.id}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            content: activeTemplate.content,
            variables: activeTemplate.variables
        })
    })
    const json = await res.json()

    if (json.status === 'fail') {
        openModal('Error', json.details)
    }

    // also update the parsed output to match the new updates
    activeTemplate.parsedBodyHtml = json.data.body.parsedBodyHtml
    openModal('Success!', json.message)
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
