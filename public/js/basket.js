quantityElts = document.querySelectorAll('.basket-product__quantity');
removeElts = document.querySelectorAll('.basket-product__remove');


function updateQuantity(e) {
    console.log('quantity changed');
}

function removeProduct(e)
{

}

quantityElts.forEach (quantityElt => {
    quantityElt.addEventListener('change', updateQuantity);
});

removeElts.forEach (quantityElt => {
    quantityElt.addEventListener('click', removeProduct);
});
