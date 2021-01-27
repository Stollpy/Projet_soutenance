/*
Compteurs pour les produits avent l'ajout au panier
 */

var counter = 1;


var element = document.querySelector('.counters')
element.innerHTML = counter;

var btn1 = document.querySelectorAll('.btnMoin')
var btn2 = document.querySelectorAll('.btnPlus')

for(const btn of btn1){
    btn.addEventListener('click', function(event) {

        const idProduct = event.currentTarget.dataset.id;
        const idCounter = 'Quantity_' + idProduct;
        const idCart = 'Cart_' + idProduct;
        const cartElement = document.getElementById(idCart).href;
        const counterElement = document.getElementById(idCounter);
        let count = counterElement.textContent;
        let href = cartElement.replace('?quantity=' + counterElement.textContent, '?quantity=')
        count--;
        counterElement.innerHTML = count;
        document.getElementById(idCart).href = href + counterElement.innerHTML;
        console.log(document.getElementById(idCart).href);
    })
}

for(const Btn of btn2){
    Btn.addEventListener('click', function(event) {

        const idProduct = event.currentTarget.dataset.id;
        const idCounter = 'Quantity_' + idProduct;
        const idCart = 'Cart_' + idProduct;
        const cartElement = document.getElementById(idCart).href;
        const counterElement = document.getElementById(idCounter);
        let count = counterElement.textContent;
        let href = cartElement.replace('?quantity=' + counterElement.textContent, '?quantity=')
        count++;
        counterElement.innerHTML = count;
        document.getElementById(idCart).href = href + counterElement.innerHTML;
        console.log(document.getElementById(idCart).href);
    })
}
