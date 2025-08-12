export const beautify = (text) => {
    return vkbeautify.xml(text, 2)
}

export const formatDate = (dateStr) => {
    const date = new Date(dateStr)
    const day = String(date.getDate()).padStart(2, '0')
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const year = date.getFullYear()
    const hour = String(date.getHours()).padStart(2, '0')
    const minutes = String(date.getMinutes()).padStart(2, '0')
    return `${day}. ${month}. ${year}. ${hour}:${minutes}`
}

export const setActiveClassForActiveTemplate = (activeTemplate) => {
    document.querySelectorAll('.template').forEach(curr => {
        curr.classList.remove('template--active')
    })
    activeTemplate.classList.add('template--active')
}

export const renderAddress = (address, container, addressListName, listenerFn, theme = 'primary') => {
    const addressDiv = document.createElement('div')
    addressDiv.classList.add('address', `address--${theme}`)
    addressDiv.innerHTML = `
        <span class="address-content">${address}</span>
        <span class="material-symbols-outlined remove-address-btn">
            close
        </span>
    `

    container.appendChild(addressDiv)
    addressDiv.querySelector('.remove-address-btn').addEventListener('click', (e) => {
        listenerFn(e, addressListName)
    })
}

export const renderAddressList = (addresses, container, addressListName, listenerFn, theme = 'primary') => {
    container.innerHTML = ``
    addresses.forEach(addr => {
        renderAddress(addr, container, addressListName, listenerFn, theme)
    })
}

export const showSuccessMessage = () => {
    const msg = document.querySelector('.success-msg')
    msg.style.display = 'flex'
    setTimeout(() => {
        msg.style.display = 'none'
    }, 2000)
}
