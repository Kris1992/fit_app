const items = document.getElementById('list');

if(items)
{
	items.addEventListener('click', ev =>
	{


		if (ev.target.className === "fa fa-trash-alt user" )
		{
			const id = ev.target.getAttribute('data-id');
			if(confirm("Do you want delete user number: " + id + " ??"))
			{
				
				fetch('/admin/account/delete/' + id, {
				method: 'DELETE'
			});//.then(res => window.location.reload());
			}	
		}
		if (ev.target.className === "fa fa-trash-alt activity" )
		{
			const id = ev.target.getAttribute('data-id');
			if(confirm("Do you want delete activity number: " + id + " ??"))
			{
				fetch('/admin/activity/delete/' + id, {
				method: 'DELETE'
			});//.then(res => window.location.reload());
			}	
		}
		if (ev.target.className === "fa fa-trash-alt workout" )
		{
			const id = ev.target.getAttribute('data-id');
			if(confirm("Do you want delete workout number: " + id + " ??"))
			{
				fetch('/admin/workout/delete/' + id, {
				method: 'DELETE'
			});//.then(res => window.location.reload());
			}	
		}

	})
}

