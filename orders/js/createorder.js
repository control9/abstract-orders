function createOrder() {
	if (!validateFields()) return;
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

function validateFields() {
	$('#new-card-summary-group').removeClass("has-error");
	$('#new-card-cost-group').removeClass("has-error");
	$('#new-card-description-group').removeClass("has-error");
	if (! $('#new-card-summary').val()) {
		$('#new-card-summary-group').addClass("has-error");
		return false;
	}
	if (! $('#new-card-cost').val() || ! $.isNumeric($('#new-card-cost').val())) {
		$('#new-card-cost-group').addClass("has-error");
		return false;
	}
	if (! $('#new-card-description').val()) {
		$('#new-card-description-group').addClass("has-error");
		return false;
	}
	return true;
}

function notifyFail(errorMessage) {
	$(".alert").remove();
	$('<div/>', {
		class: 'alert alert-danger',
		text: "Ошибка при создании заказа: "+errorMessage
	}).prependTo("#new-card-form").delay(5000).fadeOut("slow");
}

function notify(response) {
	if (redirectIfLoggedOut()) return;
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
	loadDataFromServer(updateUserData);
}