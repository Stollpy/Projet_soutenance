const rate = document.querySelectorAll("#rate");

window.onload = function () {

    rate.forEach(rate => {
        const productId = rate.getAttribute('data-product-id');
        rate.addEventListener('click', async (e) => {
            e.preventDefault();
            const rating = document.getElementById(`ratingSelect_${productId}`).value;
            // console.log(`${productId} ${rating}`)
            await addRating(productId, rating);
        })
    })

}

async function addRating(pid, rate) {
    const settings = {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
                'product_id': pid,
                'rate': rate
        })
    };
    try {
        const fetchResponse = await fetch(`/shop/product/rating/${pid}`, settings);
        const data = await fetchResponse.json();
        if (data['code'] === 200) {
            document.querySelector(`#thxMsg_${pid}`).classList.remove('d-none');
            document.querySelector(`#ratingdiv_${pid}`).classList.add('d-none');
        }
        console.log(data);
    } catch (error) {
        console.error(error);
    }
}