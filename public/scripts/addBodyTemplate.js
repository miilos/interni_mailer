const previewContainer = document.querySelector('.preview')
const templateVariablesContainer = document.querySelector('.template-variables')

const templateNameInput = document.getElementById('template-name')
const formatTwigCheckbox = document.getElementById('format-twig')
const formatMjmlCheckbox = document.getElementById('format-mjml')
const saveBtn = document.querySelector('.add-template-save-btn')
const addVariableNameInput = document.getElementById('add-variable-name')
const addVariableValueInput = document.getElementById('add-variable-value')
const addVariableBtn = document.querySelector('.add-variable-btn')
const askChatBtn = document.querySelector('.ask-chat-btn')

// twig is the default format
let template = {
    name: '',
    content: '',
    extension: 'html.twig',
    variables: {}
}

const updateTemplateName = (name) => {
    template.name = name
}

const updateTemplateContent = (content) => {
    template.content = content
}

const updateTemplateExtension = (extension) => {
    template.extension = extension
}

const updateTemplateVariables = (name, value) => {
    template.variables[name] = value
}

/**** event listener functions for dynamically generated content ****/

const onPromptChatGPT = async (e) => {
    const prompt = prepareChatGPTPrompt(document.querySelector('.input-prompt').value)
    const promptingText = document.getElementById('prompting-text')

    if (!prompt) return

    promptingText.style.display = 'block'

    const res = await fetch('/api/prompt', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ prompt })
    })
    const json = await res.json()

    promptingText.style.display = 'none'

    if (!res.ok) {
        const errorMessage = document.getElementById('errmsg-prompt-error')
        errorMessage.style.display = 'block'
        errorMessage.innerText = json.message

        console.log(json)

        return
    }

    const chatResponse = formatChatGPTResponse(json.data.response)
    window.editor.setContent(chatResponse)
    updateTemplateContentAndRender(chatResponse)
    closeModal()
}

/**** render functions ****/

const renderEditorContent = async () => {
    const res = await fetch('/api/email-body/render', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            body: template.content,
            // always use twig as an extension because the twig parser checks if the body contains mjml, and parses it if it does
            extension: 'twig.html',
            variables: template.variables
        })
    })

    const json = await res.json()

    if (!res.ok) {
        previewContainer.innerHTML = `
            <div class="error-msg-container">
                <p class="error-msg">${json.details}</p>
            </div>
        `

        return
    }

    return json.data.body
}

/**** util functions ****/

const validateTemplate = () => {
    let errors = []

    if (!template.name) {
        errors.push('You have to enter a template name!')
    }

    if (!template.content) {
        errors.push('You have to enter a template body!')
    }

    return errors
}

const clearValues = () => {
    template = {
        name: '',
        content: '',
        extension: 'html.twig',
        variables: {}
    }

    templateNameInput.value = ''
    window.editor.setContent('')
    formatTwigCheckbox.checked = true
    templateVariablesContainer.querySelector('.variables-container').innerHTML = ''
    previewContainer.innerHTML = ''
}

const prepareChatGPTPrompt = (prompt) => {
    const existingContent = window.editor.getContent()

    if (existingContent) {
        prompt += `This is the code that you need to perform the previous instructions on: ${existingContent}`
    }

    return prompt
}

const formatChatGPTResponse = (response) => {
    // chatgpt returns the code in markdown format so this strips the markdown tags
    return response
        .replace(/^\s*```[\w-]*\s*\n?/, '')
        .replace(/\n?\s*```[\s]*$/, '');
}

const updateTemplateContentAndRender = async (content) => {
    updateTemplateContent(content)
    const renderedContent = await renderEditorContent()

    if (renderedContent) {
        previewContainer.innerHTML = renderedContent
    }
}

/**** event listeners ****/

window.addEventListener('editorUpdate', async () => {
    const content = window.editor.getContent()
    await updateTemplateContentAndRender(content)
})

templateNameInput.addEventListener('input', (e) => {
    updateTemplateName(e.target.value)
})

formatTwigCheckbox.addEventListener('change', (e) => {
    if (e.target.checked) {
        updateTemplateExtension(e.target.value)
    }
})

formatMjmlCheckbox.addEventListener('change', (e) => {
    if (e.target.checked) {
        updateTemplateExtension(e.target.value)
    }
})

addVariableBtn.addEventListener('click', () => {
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

    updateTemplateVariables(name, value)
})

saveBtn.addEventListener('click', async (e) => {
    const errors = validateTemplate()

    if (errors.length > 0) {
        const errString = errors.join('<br />')
        openModal('Validation error!', errString)
        return
    }

    const res = await fetch('/api/email-body', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(template)
    })

    const json = await res.json()

    if (!res.ok) {
        openModal('Error!', json.details)
        return
    }

    openModal('Success!', 'Body template created successfully!')
    clearValues()
})

askChatBtn.addEventListener('click', async (e) => {
    openChatGPTModal('Ask ChatGPT', 'Ask ChatGPT to generate a template:', 'modal-overlay--chat')

    document.querySelector('.prompt-btn').addEventListener('click', onPromptChatGPT)
})

