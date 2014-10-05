loadDataFromServer(completeLoading);

function loadDataFromServer(callback) {
	$.post("./backend/getuserdata.php",
			{ 	id: $.cookie('id'),
				session: $.cookie('session') 	
			},
			callback,
			"JSON"
		).fail(
			function( jqxhr, textStatus, error ) {
				$.removeCookie("id");
				$.removeCookie("session");
				window.location.replace('./login.html');
			}
	);	
}

function updateUserData(userData) {
	if (userData) {
		$("#username").text(userData.real_name).html();
		$("#money").text("Счёт:" + userData.money).html();
	}
	else {
		$(".alert").remove();
		$('<div/>', {
			class: 'alert alert-warning',
			text: "Не удалось обновить данные"
		}).prependTo("#prompt");
	}
}
	
	
function completeLoading(userData) {
	if (userData) {
		updateUserData(userData);
		$(".loader").fadeOut("slow");
	}
	else {
		$.removeCookie("id");
		$.removeCookie("session");
		window.location.replace("./login.html");
	}
}