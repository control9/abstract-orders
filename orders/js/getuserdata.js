$.getJSON(
	"./backend/getuserdata.php",
	{ id: $.cookie('id'),
	  session: $.cookie('session') 	}, 
	completeLoading
).fail(function( jqxhr, textStatus, error ) {
	var err = textStatus + ", " + error;
	console.log( "Request Failed: " + err );
	window.location.replace('./login.html');
});

function completeLoading(userData) {
	if (userData) {
		$("#username").text(userData.real_name).html();
		$("#money").text("Счёт:" + userData.money).html();
		$(".loader").fadeOut("slow");
	}
	else {
		window.location.replace('./login.html');
	}
}