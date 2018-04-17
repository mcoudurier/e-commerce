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
        return response.json();
    })
    .then(json => {
        document.querySelector(`.basket-product__price[data-target-id="${id}"`)
            .innerHTML = `€ ${json.price}`;
        document.querySelector('.basket-checkout__total-price')
            .innerHTML = json.totalPrice;
    })
    .catch(error => {});
}

quantityElts = document.querySelectorAll('.basket-product__quantity');
quantityElts.forEach (quantityElt => {
    quantityElt.addEventListener('change', updateQuantity);
});
