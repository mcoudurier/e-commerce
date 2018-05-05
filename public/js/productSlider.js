$(document).ready(() => {
    let $images = $('.product-single__image');
    const length = $images.length - 1;

    $images.on('click', (e) => {
        let i = 0;
       
        $('.overlay__close').on('click', () => {
            $('.overlay').fadeOut();
            $('.overlay__slide img').remove();
        });

        $('.previous').on('click', () => {
            i--;
            if (i < 0) {
                i = length;
            }
            $('.overlay__slide img').replaceWith($images[i].cloneNode());
        });

        $('.next').on('click', () => {
            i++;
            if (i > length) {
                i = 0;
            }
            $('.overlay__slide img').replaceWith($images[i].cloneNode());
        });

        $('.overlay').fadeIn();
        $('.overlay__slide').append(e.target.cloneNode());
    });
});
