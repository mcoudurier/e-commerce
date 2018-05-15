let $addImageBtn = $('.add-image');

let $collectionHolder;

$(document).ready(() => {
    $collectionHolder = $('div.images');

    $collectionHolder.data('index', $collectionHolder.find(':input').length);
    
    let index = $collectionHolder.data('index');

    $('input[type=file]').change(function() {
        previewImage(this);
    });

    $addImageBtn.on('click', e => {
        e.preventDefault();

        addImageForm($collectionHolder);
    });
});

function previewImage(fileInput) {
    if (fileInput.files && fileInput.files[0]) {
        let reader = new FileReader();
        reader.onload = function(e) {
            $(fileInput).closest('.image').find('.img:first').remove();
            $('<img/>', {
                'class': 'img img-fluid',
                'src': e.target.result,
            }).prependTo(fileInput.closest('.image'));
        }
        reader.readAsDataURL(fileInput.files[0]);
    }
}

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

    $('<div class="col-md-4 image">'+newForm+'</div>').insertBefore($('.add-image-container'))

    $fileInput = $('input[type=file]').last();
    $fileInput.change(function() {
        previewImage(this);
    });

    if (index >= 3) {
        $addImageBtn.remove();
    }
}
