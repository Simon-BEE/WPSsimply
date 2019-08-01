function flash(message, type = 'success') {
	toastr.options = {
		"closeButton": true,
		"debug": false,
		"newestOnTop": false,
		"progressBar": true,
		"positionClass": "toast-top-right",
		"preventDuplicates": false,
		"onclick": null,
		"showDuration": "300",
		"hideDuration": "1000",
		"timeOut": "5000",
		"extendedTimeOut": "1000",
		"showEasing": "swing",
		"hideEasing": "linear",
		"showMethod": "fadeIn",
		"hideMethod": "fadeOut"
	}
	toastr[type](message)
}

function deleting(path, id) {
	if (confirm("Etes vous sur de vouloir supprimer ce produit ?")) {
		$.post('/'+path+'/delete', {id : id}, function(data){
			if (data === 'ok') {
				alert('produit retiré');
				document.location.reload();
			}else{
				alert('error');
			}
		})
	}
}

var acc = document.getElementsByClassName("accordion");
var i;
var panel = this.nextElementSibling;

for (i = 0; i < acc.length; i++) {
	acc[i].addEventListener("click", function() {
		this.classList.toggle("active");
		document.getElementById('panel').classList.toggle("block");
	});
}

function sendMsg(path) {
	message = $('#message').val();
	
	$.post(path, {message : message}, function(data){
		if (data != 'error') {
			
			var msg = document.createElement("DIV")
			msg.className = "card-right";
			var p1 = document.createElement('P');
			var p2 = document.createElement('P');
			p1.className = "text-right small";
			p2.className = "text-right";
			message = document.createTextNode(message);
			let current_datetime = new Date()
			let formatted_date = current_datetime.getDate() + '/' + (current_datetime.getMonth() + 1) + '/' + current_datetime.getFullYear() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes();
			var date = document.createTextNode(formatted_date);
			p1.appendChild(date);
			p2.appendChild(message);
			msg.appendChild(p1);
			msg.appendChild(p2);
			document.getElementById('all-msg').appendChild(msg);
			$('#message').val('');
		}else{
			alert('une erreur s\'est produite');
		}
	})
}