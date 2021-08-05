jQuery(document).ready(function($) {
    var prices = 0;
    const myPromise = new Promise((resolve,reject) => {
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if(xhttp.readyState == XMLHttpRequest.DONE) {
                resolve(JSON.parse(xhttp.responseText));
            }
        }
        xhttp.open('GET', '/api/v1/products', true);
        xhttp.send(); 
    });

    var nodes = getNodeValues();
    myPromise.then((value) => {prices = value});
    clickHandler();  
    preventTotalPriceInputKeydown();
       
function getNodeValues() {
    let teste = document.querySelectorAll('select')[2];
    let nodes = [];
    for(i = 0; i < teste.options.length; i++) {
        nodes.push({
            name: teste.options[i].innerText,
            nid: teste.options[i].value
        });
    }
    return nodes;
}

function clickHandler() {
    let btn = document.querySelector('.region-content');
    btn.addEventListener('click',setTotalPrice);
}

function getAllProducts(callback) {
    let products = [];
    let productNode = document.querySelectorAll('.chosen-single');
    for(i = 1; i < productNode.length; i++) {
        products.push(productNode[i].innerText);
    }
    
    return products;
}

function getAllAmounts() {
    let amount = [];
    let amounts = document.querySelectorAll('.form-number');
    for(i = 0; i < amounts.length; i++) {
        amount.push(amounts[i].value);
    }
  
    return amount;
}
    
function setTotalPrice() {
    let amounts = getAllAmounts();
    let products = getAllProducts();
    let productNodes = [];
    let totalPrice = 0;
    getProductPrice(products[0]);

    for(j = 0; j < amounts.length; j++) {
        let amount = amounts[j] != "" ? amounts[j] : "0";
        totalPrice += getProductPrice(products[j]) * parseInt(amount);       
    }
    if(totalPrice != "") {
        document.querySelector('#edit-valor-total-0-value').value = `R$${totalPrice.toFixed(2)}`;
    } else {
        document.querySelector('#edit-valor-total-0-value').value = "";
    }
}

function getProductPrice(product) {
    let nid = 0;
    let price = 0;
   for(i = 0; i < nodes.length; i++) {
       if(nodes[i].name == product) {
           nid = nodes[i].nid;
           break;
       }
   }

   for(i = 0; i < prices.length; i++) {
    if(prices[i].nid == nid) {
        price = prices[i].field_valor;
        break;
    }
   }

    return parsePriceValueToFloat(price);   
}

function parsePriceValueToFloat(price) {
   return !!price ? parseFloat(price.split('$')[1]) : 0;
}

function preventTotalPriceInputKeydown() {
    document.querySelector('#edit-valor-total-0-value').addEventListener('keydown', (evt) => {
        evt.preventDefault();
    });
}

});