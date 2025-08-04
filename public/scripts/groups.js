API_URL = '/api/groups'
USER_API_URL = '/api/users'

/**** containers ****/

const groupContainer = document.querySelector('.group-results-container')

/**** inputs ****/

const groupNameInput = document.getElementById('group-name')
const searchBtn = document.querySelector('.group-search-btn')
const userInput = document.getElementById('user-name')
// const userSearchResultsContainer = document.querySelector('.add-members-search-results')

let groups = []
let activeGroup = null

// timeout used to prevent too many api requests too quickly when searching for users
let userSearchTimeout

/**** data fetching ****/

const fetchGroups = async () => {
    const name = groupNameInput.value || ''

    const res = await fetch(`${API_URL}?name=${name}`)
    const json = await res.json()

    return json.data.groups
}

const fetchUserSearchResults = async (query) => {
    // enable user to search for any user entity property relating to their name or email
    const res = await fetch(`${USER_API_URL}?username=${query}&firstname=${query}&lastname=${query}&email=${query}`)
    const json = await res.json()

    return json.data.users
}

/**** on click functions for dynamically generated content ****/

const onGroupClick = (e) => {
    const parent = e.target.closest('.group')
    const name = parent.querySelector('.group-name').innerText
    const group = groups.find(curr => curr.name === name)
    renderGroupDetails(group)
    setActiveClassForActiveGroup(parent)
    activeGroup = group
}

const onRemoveFromGroupClick = async (e) => {
    const id = e.target.dataset.id

    const res = await fetch(`${API_URL}/${activeGroup.id}/users/${id}`, {
        method: 'DELETE'
    })
    const json = await res.json()

    if (!res.ok) {
        openModal('Error!', json.message)
        return
    }

    e.target.closest('.member').remove()
}

const onUserSearchBarInput = (e) => {
    const existingResults = document.querySelector('.add-members-search-results')
    const searchVal = e.target.value

    // if the search bar is empty after typing, remove the result container
    if (!searchVal) {
        existingResults.remove()
        return
    }

    if (existingResults) {
        existingResults.remove()
    }

    document.querySelector('.add-members-search-container').insertAdjacentHTML('beforeend', `<div class="add-members-search-results"></div>`)

    clearTimeout(userSearchTimeout)
    // debounce the searchbar - call the api only after the user stops typing for 300ms,
    // and has entered at least 2 characters to stop from fetching large result sets

    userSearchTimeout = setTimeout(() => {
        const query = e.target.value.trim()
        if (query.length >= 2) {
            searchAndRenderUserResults(query)
        }
    }, 300)
}

const onAddUserToGroupBtnClick = async (e) => {
    const id = e.target.dataset.id

    const res = await fetch(`${API_URL}/${activeGroup.id}/users`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            userId: id
        })
    })

    const json = await res.json()

    if (!res.ok) {
        openModal('Error!', json.message)
        return
    }

    const userEl = e.target.closest('.member')
    renderAddedUserInGroup(userEl)
}

/**** render functions ****/

const renderGroups = () => {
    clearGroupResults()

    groups.forEach(group => {
        groupContainer.insertAdjacentHTML('beforeend', `
            <div class="group">
                <div class="group-name-container">
                    <h3 class="group-name">${group.name}</h3>
                    <span>&bull;</span>
                    <span class="group-address">${group.address}</span>
                </div>
                 <p class="group-member-count">${group.users.length} members</p>
            </div>
        `)
    })

    document.querySelectorAll('.group').forEach(curr => {
        curr.addEventListener('click', onGroupClick)
    })
}

const renderGroupDetails = (group) => {
    let membersHtml = ''
    group.users.forEach(user => {
        membersHtml += `
            <div class="member">
                <div class="member-pfp"></div>
                <h4 class="member-name">${user.firstname} ${user.lastname}</h4>
                <p class="member-address">${user.email}</p>

                <span class="material-symbols-outlined remove-from-group-btn" data-id="${user.id}">
                    remove
                </span>
            </div>
        `
    })

    const container = document.querySelector('.group-details-content')
    container.innerHTML = `
        <h1 class="group-details-name">${group.name}</h1>
        <h2 class="group-details-address">${group.address}</h2>

        <div class="group-members-container">
            <div class="group-members-list">
                <h3 class="group-members-title">Members</h3>

                <div class="group-members">
                    ${membersHtml}
                </div>
            </div>

            <div class="add-members-container">
                <h3 class="group-members-title">Add members</h3>

                <div class="add-members-search-container">
                    <div class="add-members-search-bar">
                        <input type="text" name="user-name" id="user-name" class="search-input" placeholder="Search users..." />
                        <button class="btn btn--primary template-search-btn group-search-btn">
                            <span class="material-symbols-outlined">
                                search
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `

    document.querySelectorAll('.remove-from-group-btn').forEach(curr => {
        curr.addEventListener('click', onRemoveFromGroupClick)
    })

    document.querySelector('.add-members-search-container').addEventListener('input', onUserSearchBarInput)
}

const renderUserSearchResults = (users, query) => {
    const searchResultsContainer = document.querySelector('.add-members-search-results')

    users.forEach(user => {
        searchResultsContainer.insertAdjacentHTML('beforeend', `
            <div class="member">
                <div class="member-pfp"></div>
                <h4 class="member-name">${highlightResultMatches(user.firstname, query)} ${highlightResultMatches(user.lastname, query)}</h4>
                <p class="member-address">${highlightResultMatches(user.email, query)}</p>

                <span class="material-symbols-outlined add-to-group-btn" data-id="${user.id}">
                    group_add
                </span>
            </div>
        `)
    })

    document.querySelectorAll('.add-to-group-btn').forEach(curr => {
        curr.addEventListener('click', onAddUserToGroupBtnClick)
    })
}

/**** util functions ****/

const fetchAndRenderGroups = async () => {
    groups = await fetchGroups()
    renderGroups()
}

const clearGroupResults = () => {
    groupContainer.innerHTML = ''
}

const searchAndRenderUserResults = async (query) => {
    const users = await fetchUserSearchResults(query)
    renderUserSearchResults(users, query)
}

const highlightResultMatches = (result, query) => {
    return result.replace(new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi'), '<mark>$1</mark>');
}

const renderAddedUserInGroup = (userEl) => {
    const name = userEl.querySelector('.member-name').innerText
    const email = userEl.querySelector('.member-address').innerText
    const id = userEl.querySelector('.add-to-group-btn').dataset.id

    document.querySelector('.group-members').insertAdjacentHTML('beforeend', `
        <div class="member">
            <div class="member-pfp"></div>
            <h4 class="member-name">${name}</h4>
            <p class="member-address">${email}</p>

            <span class="material-symbols-outlined remove-from-group-btn" data-id="${id}">
                remove
            </span>
        </div>
    `)
}

const setActiveClassForActiveGroup = (activeGroup) => {
    document.querySelectorAll('.group').forEach(curr => {
        curr.classList.remove('group--active')
    })
    activeGroup.classList.add('group--active')
}

/**** event listeners ****/

window.addEventListener('load', fetchAndRenderGroups)

searchBtn.addEventListener('click', fetchAndRenderGroups)

groupNameInput.addEventListener('keydown', async (e) => {
    if (e.key === 'Enter') {
        await fetchAndRenderGroups()
    }
})

// event listener to hide user search results whenever the user clicks outside the result area
document.addEventListener('click', (e) => {
    const searchResultsContainer = document.querySelector('.add-members-search-results')

    if (searchResultsContainer && !searchResultsContainer.contains(e.target)) {
        searchResultsContainer.style.display = 'none';
    }
});
