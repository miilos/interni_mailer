export const beautify = (text) => {
    return vkbeautify.xml(text, 2)
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
    addressDiv.addEventListener('click', (e) => {
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
