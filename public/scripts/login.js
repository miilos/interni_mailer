const emailInput = document.getElementById('email')
const loginFormEmail = document.getElementById('login_form_email')
const passkeySetupFormEmail = document.getElementById('passkey_setup_form_email')

const loginFormBtn = document.querySelector('.login-btn')
const passkeyFormBtn = document.querySelector('.setup-passkey-btn')

const errorMsgSpan = document.getElementById('errmsg-email')

const checkEmailEntered = () => {
    if (!emailInput.value) {
        errorMsgSpan.style.display = 'block'
        return false
    }

    return true
}

emailInput.addEventListener('keydown', () => {
    errorMsgSpan.style.display = 'none'
})

loginFormBtn.addEventListener('click', () => {
    if(!checkEmailEntered()) return

    loginFormEmail.value = emailInput.value
})

passkeyFormBtn.addEventListener('click', () => {
    if(!checkEmailEntered()) return

    passkeySetupFormEmail.value = emailInput.value
})
