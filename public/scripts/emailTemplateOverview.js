import { fetchAndRenderEmailTemplates, attachEmailTemplateEventListeners } from "./emailTemplateUtils.js";

const API_URL = '/api/templates'

const templatesContainer = document.querySelector('.email-templates-container')

const templateNameInput = document.querySelector('.email-template-search-input')
const searchBtn = document.querySelector('.email-template-search-btn')

let templates = []

/**** event listeners for dynamically generated content ****/

const onSearchResultClick = () => {

}

/**** event listeners ****/

window.addEventListener('load', async (e) => {
    templates = await fetchAndRenderEmailTemplates(API_URL)
    attachEmailTemplateEventListeners(onSearchResultClick())
})

templateNameInput.addEventListener('keydown', async (e) => {
    if (e.key === 'Enter') {
        templates = await fetchAndRenderEmailTemplates(API_URL)
        attachEmailTemplateEventListeners(onSearchResultClick())
    }
})

searchBtn.addEventListener('click', async (e) => {
    templates = await fetchAndRenderEmailTemplates(API_URL)
    attachEmailTemplateEventListeners(onSearchResultClick())
})
