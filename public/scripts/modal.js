function openModalLogs(title, td) {
    if (!td.innerHTML) return

    const overlay = document.getElementById('modalOverlay');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';

    document.querySelector('.modal-title').innerText = title

    const content = decodeURIComponent(td.dataset.content)
    document.querySelector('.modal-text').innerHTML = content
}

function openModal(title, text) {
    const overlay = document.querySelector(`.modal-overlay`);
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';

    overlay.querySelector('.modal-title').innerText = title

    overlay.querySelector('.modal-text').innerHTML = text
}

function openChatGPTModal(title, text) {
    const overlay = document.querySelector(`.modal-overlay--chat`);
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';

    overlay.querySelector('.modal-title').innerText = title

    overlay.querySelector('.modal-text').innerHTML = text
}

function closeModal(event) {
    const overlays = document.querySelectorAll('.modal-overlay');
    overlays.forEach(curr => curr.classList.remove('active'));
    document.body.style.overflow = 'auto';
    return
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});
