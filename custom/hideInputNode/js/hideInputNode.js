
handlePageLoad();
handleButtonClicks();
handleInputChanges();  
hideEnglishText();


function disableDotsAndComasOnInput(evt) {
    if(evt.key == "." || evt.key == "," || evt.key == "-") {
        evt.preventDefault();
    }
}

function handleInputChanges() {
    let inputs = document.querySelectorAll('.form-element--type-number');
    for(let input of inputs) {
        input.addEventListener('keydown', disableDotsAndComasOnInput);                 
    }
}

function handlePageLoad() {
    let input = document.querySelector('.form-element--type-number');
    input.addEventListener('keydown', disableDotsAndComasOnInput);
}

function handleButtonClicks() {
    let btn = document.querySelector('.region-content');
    btn.addEventListener('click', handleInputChanges);
}

function hideEnglishText() {
    document.getElementsByClassName('form-actions')[0].childNodes[1].nodeValue = '';
}


    
