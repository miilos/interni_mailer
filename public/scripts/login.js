const emailInput = document.getElementById('email')
const passwordInput = document.getElementById('password')
const loginBtn = document.querySelector('.login-btn')

const errEmailSpan = document.getElementById('errmsg-email')
const errPasswordSpan = document.getElementById('errmsg-password')
const errLoginSpan = document.getElementById('errmsg-login')

const validateInput = (email, password) => {
    let passed = true

    if (!email) {
        errEmailSpan.style.display = 'block'
        errEmailSpan.innerText = 'You have to enter your email!'
        passed = false
    }

    if (!password) {
        errPasswordSpan.style.display = 'block'
        errPasswordSpan.innerText = 'You have to enter your password!'
        passed = false
    }

    return passed
}

loginBtn.addEventListener('click', async () => {
    const email = emailInput.value
    const password = passwordInput.value

    if (!validateInput(email, password)) return


})

emailInput.addEventListener('keydown', () => {
    errEmailSpan.style.display = 'none'
})

passwordInput.addEventListener('keydown', () => {
    errPasswordSpan.style.display = 'none'
})
