function doLogout(params) {
	$(".loader").fadeIn("fast");
	$.ajax({
		type: "POST",
		url: "./backend/logout.php",
		data: params,
		success: function(response) {
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