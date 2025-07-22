const table = document.querySelector('.logs')

const fetchLogs = async () => {
    const res = await fetch('/api/logs')
    const json = await res.json()
    const data = json.data

    return data.logs
}

const formatArray = (arr) => {
    let res = ''

    arr.forEach(el => {
        res += `${el}<br>`
    })

    return res
}

const htmlToPlainText = (html) => {
    const temp = document.createElement('div');
    temp.innerHTML = html;
    return temp.textContent || '';
}

const formatDate = (date) => {
    const dateObj = new Date(date)
    return `${dateObj.toLocaleDateString().replace('/', '.')} ${dateObj.toLocaleTimeString().slice(0,-3)}`
}

const formatEmailList = (emailsHtml) => {
    const parts = emailsHtml.split(/<br\s*\/?>/i)
    const visible = parts.slice(0, 3).join('<br>')
    const hidden = parts.slice(3).join('<br>')

    if (hidden.length === 0) return visible

    return `
    ${visible}<br>
    <span class="show-more">...</span>
    <span class="hidden-emails" style="display: none;">${hidden}</span>
  `
}

const renderTable = async () => {
    const logs = await fetchLogs()

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
                    <td class="truncate-cell" onclick="openModal('All Recipient Addresses', this)">${formatEmailList(formattedToAddr)}</td>
                    <td class="truncate-cell" onclick="openModal('All CC Addresses', this)">${formatEmailList(formattedCC)}</td>
                    <td class="truncate-cell" onclick="openModal('All BCC Addresses', this)">${formatEmailList(formattedBCC)}</td>
                    <td class="truncate-cell" data-body="${encodeURIComponent(body)}" onclick="openModal('Full Email Body', this)">View body</td>
                    <td class="status-td">${statusEl}</td>
                    <td>${formatDate(log.loggedAt)}</td>
                    <td class="truncate-cell" onclick="openModal('Full Error', this)">${error}</td>
                    <td>${log.bodyTemplate || ''}</td>
                    <td>${log.emailTemplate || ''}</td>
                </tr>
            `)
    })
}

window.addEventListener('load', renderTable)
