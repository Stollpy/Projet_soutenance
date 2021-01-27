var stripe = Stripe('pk_test_51I8rTvH4q7zkMh9zvmlMAluTl2n2Xuc5ylNeyojZfUMPho6ocd0tO7OOlS2GQavDR2F2AUguihZbqmUexo8O2GPK00jsxyQIxm');
var checkoutButton = document.getElementById('checkout-button');

checkoutButton.addEventListener('click', function() {
    // Create a new Checkout Session using the server-side endpoint you
    // created in step 3.
    fetch('/order/payment/Stripe', {
        method: 'POST',
    })
        .then(function(response) {
            return response.json();
        })
        .then(function(session) {
            return stripe.redirectToCheckout({ sessionId: session.id });
        })
        .then(function(result) {
            // If `redirectToCheckout` fails due to a browser or network
            // error, you should display the localized error message to your
            // customer using `error.message`.
            if (result.error) {
                alert(result.error.message);
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
        });
});