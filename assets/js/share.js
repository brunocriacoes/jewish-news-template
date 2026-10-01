document.addEventListener('click', function (event) {
    const button = event.target.closest('.share-copy');
    if (!button) return;

    const url = button.dataset.shareUrl;
    if (!url || !navigator.clipboard) return;

    navigator.clipboard.writeText(url).then(function () {
        const original = button.getAttribute('aria-label');
        button.setAttribute('aria-label', 'Link copied');
        button.classList.add('is-copied');
        window.setTimeout(function () {
            button.setAttribute('aria-label', original);
            button.classList.remove('is-copied');
        }, 1800);
    });
});
