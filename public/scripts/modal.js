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
    const overlay = document.getElementById('modalOverlay');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';

    document.querySelector('.modal-title').innerText = title

    document.querySelector('.modal-text').innerHTML = text
}

function openInputModal(title, text) {
    const overlay = document.getElementById('modalInputOverlay');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';

    document.querySelector('.modal-title').innerText = title

    document.querySelector('.modal-text').innerHTML = text

    document.querySelectorAll('.error-message--modal').forEach(curr => {
        curr.style.display = 'none'
    })
}

function closeModal(event) {
    // assume the function is being called somewhere from the code, not as a result of some event listener
    if (!event) {
        const overlay = document.getElementById('modalInputOverlay');
        overlay.classList.remove('active');
        document.body.style.overflow = 'auto';
        return
    }

    if (!event || event.target === document.getElementById('modalInputOverlay') || event.target.classList.contains('close-btn')) {
        const overlay = document.getElementById('modalInputOverlay');
        overlay.classList.remove('active');
        document.body.style.overflow = 'auto';
        return
    }

    if (!event || event.target === document.getElementById('modalOverlay') || event.target.classList.contains('close-btn')) {
        const overlay = document.getElementById('modalOverlay');
        overlay.classList.remove('active');
        document.body.style.overflow = 'auto';
        return
    }
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});
