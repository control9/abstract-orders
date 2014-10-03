function createCard(card) {
	return jQuery('<div/>', {
		id: 'card-'+ card.id,
		class: 'alert alert-success',
		text: "Заглушка карточки заказа " + card.content
	});
}