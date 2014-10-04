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
				id: $.cookie("id")
			});
}

function logout() {
	doLogout({
				id: $.cookie("id"),
				session: $.cookie("session")
			});
}