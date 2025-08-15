API_URL = '/api/groups'
USER_API_URL = '/api/users'

/**** containers ****/

const groupContainer = document.querySelector('.group-results-container')
const groupDetailsContainer = document.querySelector('.group-details-content')

/**** inputs ****/

const groupNameInput = document.getElementById('group-name')
const searchBtn = document.querySelector('.group-search-btn')
const userInput = document.getElementById('user-name')
const addGroupBtn = document.querySelector('.add-group-btn')
// const userSearchResultsContainer = document.querySelector('.add-members-search-results')

let groups = []
let activeGroup = null

// array of users that are being added to a group through the modal
let createGroupUsers = []

// timeout used to prevent too many api requests too quickly when searching for users
let userSearchTimeout
let userModalSearchTimeout

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
    if (e.target.classList.contains('template-delete-icon')) return

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
            searchAndRenderUserResults(query, '.add-members-search-results', onAddUserToGroupBtnClick)
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
        openModalWithoutInputBlock('Error!', json.message)
        return
    }

    const userEl = e.target.closest('.member')
    renderAddedUserInGroup(userEl, '.group-members', onRemoveFromGroupClick)
}

const onDeleteGroup = async (e) => {
    const groupEl = e.target.closest('.group')
    const name = groupEl.querySelector('.group-name').innerText
    const group = groups.find(curr => curr.name === name)

    const res = await fetch(`/api/groups/${group.id}`, {
        method: 'DELETE'
    })

    groupEl.remove()
    groupDetailsContainer.innerHTML = ''
}

const onAddUserToGroupBtnClickModal = (e) => {
    const id = e.target.dataset.id
    const userEl = e.target.closest('.member')
    renderAddedUserInGroup(userEl, '.modal-group-members', onRemoveFromGroupClickModal)

    createGroupUsers.push(id)
}

const onRemoveFromGroupClickModal = (e) => {
    const member = e.target.closest('.member')
    createGroupUsers = createGroupUsers.filter(curr => curr !== member.dataset.id)

    member.remove()
    groupDetailsContainer.innerHTML = ''
}

const onCreateGroup = async () => {
    const groupName = document.getElementById('modal-group-name').value
    const groupAddress = document.getElementById('group-address').value

    let allDataEntered = true

    if (!groupName) {
        document.getElementById('errmsg-group-name').style.display = 'block'
        allDataEntered = false
    }

    if (!groupAddress) {
        document.getElementById('errmsg-group-address').style.display = 'block'
        allDataEntered = false
    }

    if (!allDataEntered) return false

    const res = await fetch(API_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            name: groupName,
            address: groupAddress,
            recipients: createGroupUsers
        })
    })
    const json = await res.json()

    if (!res.ok) {
        const errorMsgEl = document.getElementById("errmsg-create-error")
        errorMsgEl.style.display = 'block'
        errorMsgEl.innerText = json.details
        return false
    }

    groupContainer.insertAdjacentHTML('beforeend', `
            <div class="group">
                <div class="group-name-container">
                    <h3 class="group-name">${groupName}</h3>
                    <span>&bull;</span>
                    <span class="group-address">${groupAddress}</span>
                </div>
                 <p class="group-member-count">${createGroupUsers.length} members</p>

                 <span class="material-symbols-outlined template-delete-icon">
                    delete
                 </span>
            </div>
        `)

    document.querySelectorAll('.group').forEach(curr => {
        curr.removeEventListener('click', onGroupClick)
        curr.querySelector('.template-delete-icon').removeEventListener('click', onDeleteGroup)
    })
    document.querySelectorAll('.group').forEach(curr => {
        curr.addEventListener('click', onGroupClick)
        curr.querySelector('.template-delete-icon').addEventListener('click', onDeleteGroup)
    })

    createGroupUsers = []
    groups.push(json.data.group)

    return true
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

                 <span class="material-symbols-outlined template-delete-icon">
                    delete
                 </span>
            </div>
        `)
    })

    document.querySelectorAll('.group').forEach(curr => {
        curr.addEventListener('click', onGroupClick)
        curr.querySelector('.template-delete-icon').addEventListener('click', onDeleteGroup)
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

const renderUserSearchResults = (users, query, containerClass, onClick) => {
    const searchResultsContainer = document.querySelector(containerClass)

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
        curr.addEventListener('click', onClick)
    })
}

/**** util functions ****/

const fetchAndRenderGroups = async () => {
    groups = await fetchGroups()
    renderGroups()
    groupDetailsContainer.innerHTML = ''
}

const clearGroupResults = () => {
    groupContainer.innerHTML = ''
}

const searchAndRenderUserResults = async (query, containerClass, onClick) => {
    const users = await fetchUserSearchResults(query)
    renderUserSearchResults(users, query, containerClass, onClick)
}

const highlightResultMatches = (result, query) => {
    return result.replace(new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi'), '<mark>$1</mark>');
}

const renderAddedUserInGroup = (userEl, containerClass, onRemoveFn) => {
    const name = userEl.querySelector('.member-name').innerText
    const email = userEl.querySelector('.member-address').innerText
    const id = userEl.querySelector('.add-to-group-btn').dataset.id

    document.querySelector(containerClass).insertAdjacentHTML('beforeend', `
        <div class="member">
            <div class="member-pfp"></div>
            <h4 class="member-name">${name}</h4>
            <p class="member-address">${email}</p>

            <span class="material-symbols-outlined remove-from-group-btn" id="user-${id}" data-id="${id}">
                remove
            </span>
        </div>
    `)

    document.getElementById(`user-${id}`).addEventListener('click', onRemoveFn)
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

/**** event listeners for modal elements ****/

addGroupBtn.addEventListener('click', () => {
    openModal('Add group', '')

    // clear any values that might have been previously left in the modal
    document.getElementById('modal-group-name').value = ''
    document.getElementById('group-address').value = ''
    document.getElementById('group-search-members').value = ''
    document.querySelector('.add-members-search-results--modal').innerHTML = ''
    document.querySelector('.modal-group-members').innerHTML = ''
    createGroupUsers = []

    // add event listener to close user search results if anything outside the result container is clicked
    // it's done here because this is the only place where the modal 100% isn't null,
    // since openInputModal() is called right before this
    const modal = document.querySelector('.modal')
    modal.addEventListener('click', (e) => {
        const modalSearchResultsContainer = document.querySelector('.add-members-search-results--modal')

        if (modalSearchResultsContainer && !modalSearchResultsContainer.contains(e.target)) {
            modalSearchResultsContainer.innerHTML = ''
        }
    })
})

document.querySelector('.modal-save-btn').addEventListener('click', async (e) => {
    const createStatus = await onCreateGroup()

    if (createStatus) {
        closeModal()
    }
})

document.getElementById('modal-group-name').addEventListener('input', () => {
    document.getElementById('errmsg-group-name').style.display = 'none'
})

document.getElementById('group-address').addEventListener('input', () => {
    document.getElementById('errmsg-group-address').style.display = 'none'
})

document.getElementById('group-search-members').addEventListener('input', async (e) => {
    const existingResults = document.querySelector('.add-members-search-results--modal')
    const searchVal = e.target.value

    if (!searchVal) {
        existingResults.remove()
        return
    }

    if (existingResults) {
        existingResults.remove()
    }

    document.querySelector('.modal-search-container').insertAdjacentHTML('beforeend', '<div class="add-members-search-results--modal"></div>')

    clearTimeout(userModalSearchTimeout)
    userModalSearchTimeout = setTimeout(() => {
        const query = e.target.value.trim()
        if (query.length >= 2) {
            searchAndRenderUserResults(query, '.add-members-search-results--modal', onAddUserToGroupBtnClickModal)
        }
    }, 300)
})
