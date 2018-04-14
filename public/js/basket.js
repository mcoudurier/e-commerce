function updateQuantity(e) {
    let t = e.target;
    let id = t.dataset.targetId;
    let quantity = t.value;
    
    const req = new Request('/basket/update', {
        method: 'POST',
        credentials: 'same-origin',
        body: JSON.stringify({
            id: id,
            quantity: quantity
        })
    });
    
    fetch(req)
    .then(response => {
        return response.text();
    })
    .then(text => {
        document.querySelector(`.basket-product__price[data-target-id="${id}"`)
            .innerHTML = `€ ${text}`;
    })
    .catch(error => {});
}

function removeProduct(e)
{

}

quantityElts = document.querySelectorAll('.basket-product__quantity');
quantityElts.forEach (quantityElt => {
    quantityElt.addEventListener('change', updateQuantity);
});

removeElts = document.querySelectorAll('.basket-product__remove');
removeElts.forEach (quantityElt => {
    quantityElt.addEventListener('click', removeProduct);
});
