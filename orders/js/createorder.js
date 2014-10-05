function createOrder() {
	var data = $("#new-card-form").serializeArray();
	data.push( {name:"action", value:"createorder"});
	data.push( {name:"id", value:$.cookie('id')});
	data.push( {name:"session", value:$.cookie('session')});
	$.post(
		"./backend/orders.php",
		data,
		notify,
		"text"
	).fail(notifyFail);
}

function notifyFail(errorMessage) {
	$(".alert").remove();
	$('<div/>', {
		class: 'alert alert-danger',
		text: "Ошибка при создании заказа: "+errorMessage
	}).prependTo("#new-card-form").delay(5000).fadeOut("slow");
	window.setTimeout(function() {$(".alert-danger").remove()}, 7000);
}

function notify(response) {
	if ( $.isNumeric(response)) {
		notifySuccess(response);
	} else {
		notifyFail(response);
	}
}

function notifySuccess(id) {
	$(".alert").remove();
	$('<div/>', {
		class: 'alert alert-success',
		text: "Заказ №" + id + " создан"
	}).prependTo("#new-card-form").delay(5000).fadeOut("slow");
	window.setTimeout(function() {$(".alert-success").remove()}, 7000);
	loadDataFromServer(updateUserData);
	
}