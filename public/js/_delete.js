//TO DO

const items = document.getElementById('list');


if(items) {
	items.addEventListener('click', event => {

    /*switch (event.target.className) {
        case 'fa fa-trash-alt user':
            const id = event.target.getAttribute('data-id');
            if(confirm("Do you want delete user number: " + id + " ??")) {           
                fetch('/admin/account/delete/' + id, {
                    method: 'DELETE'
                });
            }
            break;
        case 'fa fa-trash-alt activity':
            const id = event.target.getAttribute('data-id');
            if(confirm("Do you want delete activity number: " + id + " ??")) {
                fetch('/admin/activity/delete/' + id, {
                    method: 'DELETE'
                });
            }   
            break;
        case 'fa fa-trash-alt workout':
            const id = event.target.getAttribute('data-id');
            if(confirm("Do you want delete workout number: " + id + " ??")) {
                fetch('/admin/workout/delete/' + id, {
                    method: 'DELETE'
                });
            }   
            break;*/






		if (event.target.className === "fa fa-trash-alt user" ) {
			const id = event.target.getAttribute('data-id');
			if(confirm("Do you want delete user number: " + id + " ??")) {
				
				fetch('/admin/account/delete/' + id, {
				method: 'DELETE'
			});//.then(res => window.location.reload());
			}	
		} if (event.target.className === "fa fa-trash-alt activity" )
		{
			const id = event.target.getAttribute('data-id');
			if(confirm("Do you want delete activity number: " + id + " ??"))
			{
				fetch('/admin/activity/delete/' + id, {
				method: 'DELETE'
			});//.then(res => window.location.reload());
			}	
		}
		if (event.target.className === "fa fa-trash-alt workout" )
		{
			const id = event.target.getAttribute('data-id');
			if(confirm("Do you want delete workout number: " + id + " ??"))
			{
				fetch('/admin/workout/delete/' + id, {
				method: 'DELETE'
			});//.then(res => window.location.reload());
			}	
		}

	})
}

