function auth() {
	var data = $("#loginform").serializeArray();
	$.ajax({
		type: "POST",
		url: "./backend/auth.php",
		data: data,
		success: function(response) {
			var good = $.cookie('session') && $.cookie('id');
			if (good) {
				redirect();
			}
			else {
				$(".alert").remove();
				$('<div/>', {
				class: 'alert alert-warning',
				text: "Login failed"
			}).insertAfter("#prompt");
			}
		},
		error:  function(){
			$(".alert").remove();
			$('<div/>', {
				class: 'alert alert-danger',
				text: "There are problems with server"
			}).insertAfter("#prompt");
		}
	});
}
function redirect() {
	var good = $.cookie('session') && $.cookie('id');
	if (good) {
		if  ($.cookie('worker') === '1') {
			window.location.replace('./worker.html');
		}
		else if ($.cookie('worker') === '0') {
			window.location.replace('./customer.html');
		}
	}
}