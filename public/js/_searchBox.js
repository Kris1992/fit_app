//Now its not used
import { getStatusError } from './_apiHelper.js';


const searchButton = document.getElementById('js-search-button');

searchButton.addEventListener('click', event => {
    const searchInput = document.getElementById('js-search-input');
    const value = searchInput.value;
    if(value){
        const url = searchButton.getAttribute('data-url');
        getSearchItems(value, url).then((data) => {
                console.log('pozytyw');
            }).catch((errorData) => {
                console.log('fail');
                //`${errorData.errorMessage}`,
            });
    }
})

function getSearchItems(value, url) {
    return new Promise(function(resolve, reject) {
        $.ajax({
            url,
            method: 'POST',
            data: JSON.stringify(value)
        }).then(function(data) {
            resolve(data);
        }).catch(function(jqXHR){
            let statusError = [];
            statusError = getStatusError(jqXHR);
            if(statusError != null) {
                reject(statusError);
            } else {
                const errorData = JSON.parse(jqXHR.responseText);
                reject(errorData);
            }
        });
    });
}


