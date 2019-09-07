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
