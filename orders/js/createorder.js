function createOrder() {
	var data = $("#new-card-form").serializeArray();
	data.push( {name:"action", value:"createorder"});
	data.push( {name:"id", value:$.cookie('id')});
	data.push( {name:"session", value:$.cookie('session')});
	$("#send-order-button").prop('disabled', true);
	$.post(
		"./backend/orders.php",
		data,
		notify,
		"text"
	).fail(notifyFail);
}

function notifyFail(errorMessage) {
	$("#send-order-button").prop('disabled', false);
	$(".alert").remove();
	$('<div/>', {
		class: 'alert alert-danger',
		text: "Ошибка при создании заказа: "+errorMessage
	}).prependTo("#new-card-form").delay(5000).fadeOut("slow");
}

function notify(response) {
	$("#send-order-button").prop('disabled', false);
	if (redirectIfLoggedOut()) return;
	if ( $.isNumeric(response)) {
		notifySuccess(response);
	} else {
		notifyFail(response);
	}
}

function notifySuccess(id) {
	$("#send-order-button").prop('disabled', false);
	$(".alert").remove();
	$('<div/>', {
		class: 'alert alert-success',
		text: "Заказ №" + id + " создан"
	}).prependTo("#new-card-form").delay(5000).fadeOut("slow");
	loadDataFromServer(updateUserData);
}