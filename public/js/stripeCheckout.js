$(document).ready(() => {
    const publicKey = $('#payment-form').data('stripe-pk');
    const stripe = Stripe(publicKey);

    const elements = stripe.elements({
        locale: 'fr',
    });

    const ElementClasses = {};

    const cardNumber = elements.create('cardNumber', {
        classes: ElementClasses,
    });
    cardNumber.mount('#card-number');

    const cardExpiry = elements.create('cardExpiry', {
        classes: ElementClasses,
    });
    cardExpiry.mount('#card-expiry');

    const cardCvc = elements.create('cardCvc', {
        classes: ElementClasses,
    });
    cardCvc.mount('#card-cvc');

    const stripeTokenHandler = (token) => {

        const $hiddenInput = $('<input/>', {
            'type': 'hidden',
            'name': 'stripeToken',
            'value': token.id,
        });

        const $form = $('#payment-form');
        $form.append($hiddenInput);
    
        $form.off('submit').submit();
    }

    $('#payment-form').on('submit', e => {
        e.preventDefault();

        stripe.createToken(cardNumber).then(result => {
            if (result.error) {
                $('#card-errors').text(result.error.message);
                $('#card-errors-container').show();
            } else {
                stripeTokenHandler(result.token);
            }
        });
    });
});

