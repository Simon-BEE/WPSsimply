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