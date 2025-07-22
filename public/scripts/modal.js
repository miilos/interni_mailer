function openModal(title, td) {
    const content = td.innerHTML

    if (!content) return

    const overlay = document.getElementById('modalOverlay');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';

    document.querySelector('.modal-title').innerText = title

    if (td.dataset.body) {
        document.querySelector('.modal-text').innerHTML = decodeURIComponent(td.dataset.body)
    }
    else {
        document.querySelector('.modal-text').innerHTML = content
    }
}

function closeModal(event) {
    if (!event || event.target === document.getElementById('modalOverlay') || event.target.classList.contains('close-btn')) {
        const overlay = document.getElementById('modalOverlay');
        overlay.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});
