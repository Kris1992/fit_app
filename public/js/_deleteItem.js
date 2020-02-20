const items = document.getElementById('list');

if(items) {
	items.addEventListener('click', event => {
        const anchor = event.target.closest('a');

        if(anchor) {
            if(anchor.className == "delete-item") {
                event.preventDefault();
                const id = anchor.getAttribute('data-id');
                const itemName = anchor.getAttribute('data-name');
                const url = anchor.getAttribute("href");
                if(confirm("Do you want delete " + itemName + " number: " + id + " ??")) {           
                    fetch(url, {
                        method: 'DELETE'
                    }).then((result) => {
                        window.location.reload();
                    }).catch((error) => {
                        var message =
                        `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert"> 
                                <strong>Error!</strong> 
                                Delete action fails. Check your network connection.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        `;
                        document.getElementById('flash-message').innerHTML = message;
                    });
                }
            }
        }
	})
}

