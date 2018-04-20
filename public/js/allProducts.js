let trLinks = document.querySelectorAll('.tr-link');

trLinks.forEach(trLink => {
    trLink.addEventListener('click', (e) => {
        window.location = e.currentTarget.dataset.href;
    });
});
