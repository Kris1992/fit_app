$('.custom-file-input').on('change', function(event) {
    if (window.FileReader) {
  		var inputFile = event.currentTarget;
    
    	var img = document.createElement("img");
    	$(img).addClass('imageForm rounded mx-auto d-block');

    	var reader = new FileReader();
    	reader.onloadend = function() {
        	img.src = reader.result;
    	}
    	reader.readAsDataURL(inputFile.files[0]);

    	$("label[for='user_registration_form_imageFile']").addClass('w-100');
    	$(inputFile).parent()
        	.find('.custom-file-label')
        	.html(img);
    	//    .html(inputFile.files[0].name);
    } else {
        alert('This browser does not support FileReader');
    }
});

$('#delete-image').on('click', function(event) {
    event.preventDefault();

    const anchor = event.target.closest('a');
    if(!anchor.classList.contains('disabled')){
        anchor.classList.add('disabled');
        $('#fileError').remove();
        const url = anchor.getAttribute('href');

        // To be sure we want delete right photo
        const userData = {};

        userData.userId = $('#user_registration_form_id').val();

    
        deleteImage(userData, url).then(result => {
            $('#uploaded_image').fadeOut(1000);
        }).catch(error => {
            var errorMessage = `<span class="color-fire" id="fileError">${error.errorMessage}</span>`;
            $('#uploaded_image').append(errorMessage);
            anchor.classList.remove('disabled');
        });

    }
});


function deleteImage(userId, url) {
   return new Promise(function(resolve, reject){
        $.ajax({
            url,
            method: 'DELETE',
            contentType: "application/json",
            data: JSON.stringify(userId)
        }).then((data) => {
            resolve(data);
        }).catch((jqXHR) => {
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

function getStatusError(jqXHR) {
    if(jqXHR.status === 0) {
        return {
            "errorMessage":"Cannot connect. Verify Network."
        }
    } else if(jqXHR.status == 404) {
        return {
            "errorMessage":"Requested not found."
        }
    } else if(jqXHR.status == 500) {
        return {
            "errorMessage":"Internal Server Error"
        }
    } else if(jqXHR.status > 400) {
        return {
            "errorMessage":"Error. Contact with admin."
        }
    }

    return null;
}




