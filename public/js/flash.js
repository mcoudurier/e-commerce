$(document).ready(() => {
    window.setTimeout(() => {
        $('#flash').fadeOut(1000, function() {
            $(this).remove();
        });
    }, 3000);
});
