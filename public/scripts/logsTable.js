const table = document.querySelector('.logs')

/**** search inputs ****/

const subjectInput = document.getElementById('subject')
const fromInput = document.getElementById('from')
const toInput = document.getElementById('to')
const statusInput = document.getElementById('status')
const bodyTemplateInput = document.getElementById('body-template')
const emailTemplateInput = document.getElementById('email-template')
const searchBtn = document.querySelector('.search-btn')

/**** data fetching ****/

const fetchLogs = async () => {
    const res = await fetch('/api/logs')
    const json = await res.json()
    const data = json.data

    return data.logs
}

const fetchStatusValues = async () => {
    const res = await fetch('/api/logs/statuses')
    const statuses = (await res.json()).data.statuses

    statuses.forEach(status => {
        statusInput.insertAdjacentHTML('beforeend', `<option value="${status}">${status.toUpperCase()}</option>`)
    })
}

const fetchSearchResults = async () => {
    const subject = subjectInput.value
    const from = fromInput.value
    const to = toInput.value
    const status = statusInput.value
    const bodyTemplate = bodyTemplateInput.value
    const emailTemplate = emailTemplateInput.value

    const res = await fetch('/api/logs/search', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            subject,
            from,
            to,
            status,
            bodyTemplate,
            emailTemplate
        })
    })

    const json = await res.json()

    return json.data.logs
}

/**** util functions to format data inside the table ****/

const formatArray = (arr) => {
    let res = ''

    arr.forEach(el => {
        res += `${el}<br>`
    })

    return res
}

const formatDate = (date) => {
    const dateObj = new Date(date)
    return `${dateObj.toLocaleDateString().replaceAll('/', '.')}. ${dateObj.toLocaleTimeString().slice(0,-3)}`
}

const formatEmailList = (emailsHtml) => {
    const parts = emailsHtml.split(/<br\s*\/?>/i)
    const visible = parts.slice(0, 2).join('<br>')
    const hidden = parts.slice(2).join('<br>')

    if (hidden.length === 0) return visible

    return `
    ${visible}<br>
    <span class="show-more">...</span>
    <span class="hidden-emails" style="display: none;">${hidden}</span>
  `
}

const resetTableContent = () => {
    table.innerHTML = `
        <tr>
            <th>Id</th>
            <th>Email Id</th>
            <th>Subject</th>
            <th>From</th>
            <th>To</th>
            <th>CC</th>
            <th>BCC</th>
            <th>Body</th>
            <th>Status</th>
            <th>Logged At</th>
            <th>Error</th>
            <th>Body Template</th>
            <th>Email Template</th>
        </tr>
    `
}

/**** main function to render content in table ****/

const renderTable = (logs) => {
    logs.forEach(log => {
        const formattedToAddr = formatArray(log.toAddr)
        const formattedCC = formatArray(log.cc)
        const formattedBCC = formatArray(log.bcc)
        const body = log.body
        const error = log.error || ''

        let statusEl = ''
        if (log.status === 'sent') {
            statusEl = `<span class="status status--sent">${log.status.toUpperCase()}</span>`
        }
        else {
            statusEl = `<span class="status status--failed">${log.status.toUpperCase()}</span>`
        }

        table.insertAdjacentHTML('beforeend',
            `
                <tr>
                    <td>${log.id}</td>
                    <td>${log.emailId}</td>
                    <td>${log.subject}</td>
                    <td>${log.fromAddr}</td>
                    <td class="truncate-cell" data-content="${encodeURIComponent(formattedToAddr)}" onclick="openModal('All Recipient Addresses', this)">${formatEmailList(formattedToAddr)}</td>
                    <td class="truncate-cell" data-content="${encodeURIComponent(formattedCC)}" onclick="openModal('All CC Addresses', this)">${formatEmailList(formattedCC)}</td>
                    <td class="truncate-cell" data-content="${encodeURIComponent(formattedBCC)}" onclick="openModal('All BCC Addresses', this)">${formatEmailList(formattedBCC)}</td>
                    <td class="truncate-cell" data-content="${encodeURIComponent(body)}" onclick="openModal('Full Email Body', this)">View body</td>
                    <td class="status-td">${statusEl}</td>
                    <td>${formatDate(log.loggedAt)}</td>
                    <td class="truncate-cell" data-content="${encodeURIComponent(error)}" onclick="openModal('Full Error', this)">${error}</td>
                    <td>${log.bodyTemplate || ''}</td>
                    <td>${log.emailTemplate || ''}</td>
                </tr>
            `)
    })
}

/**** event listeners ****/

window.addEventListener('load', async () => {
    await fetchStatusValues()

    const logs = await fetchLogs()
    renderTable(logs)
})

searchBtn.addEventListener('click', async (e) => {
    const logs = await fetchSearchResults()

    resetTableContent()
    renderTable(logs)
})
