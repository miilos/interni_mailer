const periodSelect = document.querySelector('.period-select')

const numEmailsSentEl = document.querySelector('.num-sent')
const statusesContainer = document.querySelector('.status-container-statuses')
const chart = document.querySelector('.chart')
const mostUsedEmailTemplatesContainer = document.querySelector('.most-used-email-templates')
const mostUsedBodyTemplatesContainer = document.querySelector('.most-used-body-templates')

/**** data fetching functions ****/

const fetchStats = async () => {
    const period = periodSelect.value

    const res = await fetch(`/api/stats?period=${period}`)
    const json = await res.json()

    return json.data.statistics
}

/**** render functions ****/

const renderStatusSection = (statusStats) => {
    statusStats.forEach(stat => {
        statusesContainer.insertAdjacentHTML('beforeend', `
            <div class="status-text">
                <p class="status-nums">
                    <span class="status status--${stat.status}">${stat.status.toUpperCase()}</span> - <b>${stat.count}</b> sent (<b>${stat.percentage}</b>%)
                </p>
            </div>
        `)

        chart.insertAdjacentHTML('beforeend', `
            <div class="chart-column chart-column--${stat.status}" style="height: ${stat.percentage}%">${stat.status.toUpperCase()}</div>
        `)
    })
}

const renderMostUsedTemplates = (templates, container) => {
    templates.forEach((template, i) => {
        container.insertAdjacentHTML('beforeend', `
            <div class="most-used-template">
                <p class="most-used-template-number">${i+1}.</p>
                <p class="most-used-template-name">${template.templateName}</p>
                <p class="most-used-template-usage">Used <span>${template.count}</span> times</p>
            </div>
        `)
    })
}

const renderStats = async () => {
    const stats = await fetchStats()

    clearStatContainers()
    numEmailsSentEl.innerText = stats.totalEmails
    renderStatusSection(stats.numEmailsByStatus)
    renderMostUsedTemplates(stats.mostUsedEmailTemplates, mostUsedEmailTemplatesContainer)
    renderMostUsedTemplates(stats.mostUsedBodyTemplates, mostUsedBodyTemplatesContainer)

}

/**** util functions ****/

const clearStatContainers = () => {
    statusesContainer.innerHTML = ''
    chart.innerHTML = ''
    mostUsedEmailTemplatesContainer.innerHTML = ''
    mostUsedBodyTemplatesContainer.innerHTML = ''
}

/**** event listeners ****/

window.addEventListener('load', renderStats)

periodSelect.addEventListener('change', renderStats)
