const emailInput = document.getElementById('email')
const loginBtn = document.querySelector('.login-btn')

const successSpan = document.getElementById('successmsg-login')
const errEmailSpan = document.getElementById('errmsg-email')
const errLoginSpan = document.getElementById('errmsg-login')

const makeLoginRequest = async () => {
    const email = emailInput.value

    clearMessages()

    if (!validateInput(email)) return

    const res = await fetch('/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email })
    })

    if (!res.ok) {
        const json = await res.json()
        errLoginSpan.innerText = json.message
        errLoginSpan.style.display = 'block'
        return
    }

    successSpan.innerHTML = `The login link has been sent to <b>${email}</b>!`
    successSpan.style.display = 'block'
}

const clearMessages = () => {
    successSpan.style.display = 'none'
    errEmailSpan.style.display = 'none'
    errLoginSpan.style.display = 'none'
}

const validateInput = (email) => {
    if (!email) {
        errEmailSpan.style.display = 'block'
        errEmailSpan.innerText = 'You have to enter your email!'
        return false
    }

    return true
}

loginBtn.addEventListener('click', async () => {
    makeLoginRequest()
})

emailInput.addEventListener('keydown', (e) => {
    errEmailSpan.style.display = 'none'

    if (e.key === 'Enter') {
        makeLoginRequest()
    }
})
