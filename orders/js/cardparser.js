function createCard(card) {
	return jQuery('<div/>', {
		id: 'card-'+ card.id,
		class: 'alert alert-success',
		text: card.content
	});
}