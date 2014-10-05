function doLogout(params) {
	$(".loader").fadeIn("fast");
	$.ajax({
		type: "POST",
		url: "./backend/logout.php",
		data: params,
		success: function(response) {
			//Remove cookies to evade cyclic redirect in case of backend failure
			$.removeCookie("id");
			$.removeCookie("session");
			window.location.replace('./login.html');
		}
	});
}
	
function logoutAll() {
	doLogout({
				id: $.cookie("id"),
				session: $.cookie("session"),
				all: 1
				
			});
}

function logout() {
	doLogout({
				id: $.cookie("id"),
				session: $.cookie("session")
			});
}

function redirectIfLoggedOut() {
	if (! $.cookie("id") || ! $.cookie("session")) {
		window.location.replace('./login.html');
		return true; //location.replace takes some time, we want to evade additional alerts before redirection
	}
	return false;
}