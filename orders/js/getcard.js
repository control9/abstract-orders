function show(json) {
	json.forEach( function(cardJson) {
		createCard(cardJson).appendTo('#cards');
	});
}

function getCards(count) {
	$.getJSON(
	"./backend/getcards.php", // The server URL 
	{ count: count }, // Data you want to pass to the server.
	show
	);
}
