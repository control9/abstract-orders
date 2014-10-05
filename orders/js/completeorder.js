function completeOrder(id) {
	$('#execute-'+id).prop('disabled', true);
	$.post(
		"./backend/orders.php",
		{ 	action: "complete",
			orderid: id,
			id: $.cookie('id'),
			session: $.cookie('session')
		},
		handleResponse
	);
}

function handleResponse(response) {
	redirectIfLoggedOut()
	if ( $.isNumeric(response)) {
		notifySuccess(response);
	} else {
		notifyFail($.parseJSON(response));
	}
}

function notifySuccess(id) {
	$(".alert-success").remove();
	$("#card-"+id).replaceWith(
		$('<div/>', {
			class: 'alert alert-success',
			text: "Заказ №"+id+" выполнен"
		}).delay(5000).fadeOut("slow")
	);
	loadmore(); //We need this to avoid breaking infinite scroll by removing elements
	setTimeout(loadmore, 6000);
	loadDataFromServer(updateUserData);
}

function notifyFail(errorMessage) {
	$(".alert").remove();
	$("#card-"+errorMessage.id).replaceWith(
		$('<div/>', {
			class: 'alert alert-danger',
			text: "Ошибка при выполнении заказа: "+errorMessage.message
		}).delay(5000).fadeOut("slow")
	);
	loadmore(); //We need this to avoid breaking infinite scroll by removing elements
	setTimeout(loadmore, 6000);
	loadDataFromServer(updateUserData);
}