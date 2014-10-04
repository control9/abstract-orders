function show(json) {
	json.forEach( function(cardJson) {
		createCard(cardJson).appendTo('#cards');
	});
}

function getCards(count) {
	$.post(
		"./backend/getcards.php",
		{ count: count },
		show,
		"JSON"
	);
}

function getCards(count, from) {
	$.post(
		"./backend/getcards.php",
		{ count: count, from: from },
		show,
		"JSON"
	);
}
