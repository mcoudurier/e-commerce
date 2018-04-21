let $addImageLink = $('<a href="#">Ajouter une image</a>');
let $newLinkLi = $('<li></li>').append($addImageLink);

let $collectionHolder;

$(document).ready(() => {
    $collectionHolder = $('ul.images');

    $collectionHolder.append($newLinkLi);

    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addImageLink.on('click', e => {
        e.preventDefault();

        addImageForm($collectionHolder, $newLinkLi);
    });
});

function addImageForm($collectionHolder, $newLinkLi) {
    let $prototype = $collectionHolder.data('prototype');

    let index = $collectionHolder.data('index');

    let newForm = $prototype;

    newForm = newForm.replace(/__name__/g, index);

    $collectionHolder.data('index', index + 1);

    let $newFormLi = $('<li></li>').append(newForm);

    if (index < 4) {
        $newLinkLi.before($newFormLi);
    }
}
