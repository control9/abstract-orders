function show(json) {
	jQuery('<div/>', {
		id: 'card-'+ json.id,
		class: 'alert alert-success',
		text: json.content
	}).appendTo('#cards');
}

function run(cardId) {
	$.getJSON(
	"./backend/index.php", // The server URL 
	{ id: cardId }, // Data you want to pass to the server.
	show // The function to call on completion.
	);
}
