const API_URL = '/api/logs'

const table = document.querySelector('.logs')

/**** search inputs ****/

const subjectInput = document.getElementById('subject')
const fromInput = document.getElementById('from')
const toInput = document.getElementById('to')
const statusInput = document.getElementById('status')
const bodyTemplateInput = document.getElementById('body-template')
const emailTemplateInput = document.getElementById('email-template')
const searchBtn = document.querySelector('.search-btn')
const resetBtn = document.querySelector('.reset-btn')

/**** pagination ****/

const prevBtn = document.getElementById('btn-prev')
const nextBtn = document.getElementById('btn-next')
const pageInfo = document.getElementById('page-info')
let currPage = 1
let limit = 10
let paginationData = null

/**** data fetching ****/

const displayPaginationInfo = (pagination) => {
    paginationData = pagination
    pageInfo.innerText = `Showing page ${pagination.currentPage} of ${pagination.totalPages} (${pagination.totalItems} items total)`
}

const fetchStatusValues = async () => {
    const res = await fetch(API_URL+'/statuses')
    const statuses = (await res.json()).data.statuses

    statuses.forEach(status => {
        statusInput.insertAdjacentHTML('beforeend', `<option value="${status}">${status.toUpperCase()}</option>`)
    })
}

const fetchResults = async () => {
    const subject = subjectInput.value || ''
    const from = fromInput.value || ''
    const to = toInput.value || ''
    const status = statusInput.value || ''
    const bodyTemplate = bodyTemplateInput.value || ''
    const emailTemplate = emailTemplateInput.value || ''

    const res = await fetch(
        `${API_URL}?subject=${subject}&from=${from}&to=${to}&status=${status}&bodyTemplate=${bodyTemplate}&emailTemplate=${emailTemplate}&page=${currPage}&limit=${limit}`
    )

    const json = await res.json()

    displayPaginationInfo(json.pagination)

    return json.data.logs
}

/**** util functions ****/

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
            <th class="column-id">Id</th>
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

const search = async () => {
    currPage = 1
    const logs = await fetchResults()

    if (logs.length === 0) {
        resetTableContent()

        table.querySelector('tbody').insertAdjacentHTML('beforeend', `
            <tr>
                <td colspan="13" class="no-results-msg">
                    No results for this filter
                </td>
            </tr>
        `)

        return
    }

    renderTable(logs)
}

/**** result rendering ****/

const renderTable = (logs) => {
    resetTableContent()

    logs.forEach(log => {
        const formattedCC = formatArray(log.cc)
        const formattedBCC = formatArray(log.bcc)
        const body = log.body
        const error = log.error || ''

        let statusEl = `<span class="status status--${log.status}">${log.status.toUpperCase()}</span>`

        const tbody = table.querySelector('tbody')
        tbody.insertAdjacentHTML('beforeend',
            `
                <tr>
                    <td class="column-id">${log.id}</td>
                    <td>${log.emailId}</td>
                    <td>${log.subject}</td>
                    <td>${log.fromAddr}</td>
                    <td>${log.toAddr}</td>
                    <td class="truncate-cell" data-content="${encodeURIComponent(formattedCC)}" onclick="openModalLogs('All CC Addresses', this)">${formatEmailList(formattedCC)}</td>
                    <td class="truncate-cell" data-content="${encodeURIComponent(formattedBCC)}" onclick="openModalLogs('All BCC Addresses', this)">${formatEmailList(formattedBCC)}</td>
                    <td class="truncate-cell" data-content="${encodeURIComponent(body)}" onclick="openModalLogs('Full Email Body', this)">View body</td>
                    <td class="status-td">${statusEl}</td>
                    <td>${formatDate(log.loggedAt)}</td>
                    <td class="truncate-cell" data-content="${encodeURIComponent(error)}" onclick="openModalLogs('Full Error', this)">${error}</td>
                    <td>${log.bodyTemplate || ''}</td>
                    <td>${log.emailTemplate || ''}</td>
                </tr>
            `)
    })
}

const fetchAndDisplayResults = async () => {
    const logs = await fetchResults()
    renderTable(logs)
}

/**** event listeners ****/

window.addEventListener('load', async () => {
    await fetchStatusValues()
    await fetchAndDisplayResults()
})

searchBtn.addEventListener('click', search)
document.querySelectorAll('.search-input').forEach((curr) => {
    curr.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            search()
        }
    })
})

statusInput.addEventListener('change', search)

resetBtn.addEventListener('click', async (e) => {
    subjectInput.value = ''
    fromInput.value = ''
    toInput.value = ''
    statusInput.selectedIndex = 0
    bodyTemplateInput.value = ''
    emailTemplateInput.value = ''

    currPage = 1
    await fetchAndDisplayResults()
})

prevBtn.addEventListener('click', async (e) => {
    if (currPage === 1) return

    currPage--
    await fetchAndDisplayResults()
})

nextBtn.addEventListener('click', async (e) => {
    if (currPage === paginationData.totalPages) return

    currPage++
    await fetchAndDisplayResults()
})
