let $addImageBtn = $('.add-image');

let $collectionHolder;

$(document).ready(() => {
    $collectionHolder = $('div.images');

    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addImageBtn.on('click', e => {
        e.preventDefault();

        addImageForm($collectionHolder);
    });
});

function addImageForm($collectionHolder, $newLinkLi) {
    let $prototype = $collectionHolder.data('prototype');

    let index = $collectionHolder.data('index');
    
    // Correct count for new products
    if (index === 0) {
        index = 1;
    }

    let newForm = $prototype;

    newForm = newForm.replace(/__name__/g, index);

    $collectionHolder.data('index', index + 1);
    console.log(index);

    let $newFormLi = $collectionHolder.append('<div class="col-md-3">'+newForm+'</div>');

    if (index >= 3) {
        $addImageBtn.remove();
    }
}
