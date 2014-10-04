function show(json) {
	json.forEach( function(cardJson) {
		createCard(cardJson).appendTo('#cards');
	});
}

function getCards(count) {
	$.post(
		"./backend/orders.php",
		{ 	action: "getorders",
			count: count, 
			from: 999999,
			id: $.cookie('id'),
			session: $.cookie('session')
		},
		show,
		"JSON"
	);
}

function getCards(count, from) {
	$.post(
		"./backend/orders.php",
		{ 	action: "getorders",
			count: count, 
			from: from,
			id: $.cookie('id'),
			session: $.cookie('session')
		},
		show,
		"JSON"
	);
}
