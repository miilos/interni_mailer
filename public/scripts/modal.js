function openModal(title, td) {
    if (!td.innerHTML) return

    const overlay = document.getElementById('modalOverlay');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';

    document.querySelector('.modal-title').innerText = title

    const content = decodeURIComponent(td.dataset.content)
    document.querySelector('.modal-text').innerHTML = content
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
