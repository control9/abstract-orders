/* Returns card in following form:
<div class="panel panel-primary" id="card-card.id">
	<div class="panel-heading">
		<h3 class="panel-title">Заказ card.id</h3>
	</div>
	<div class="panel-body">
		<div class="col-sm-7">
			<h3> card.summary </h3>
		</div>
		<div class="col-sm-3 col-sm-offset-2">
			<button role="button" class="btn btn-block btn-primary" id="execute-card.id>Выполнить</button>
		</div>
		<div class="col-sm-12"> card.description
		</div>
	</div>
</div>
*/




function createCard(card) {
	return $('<div>').addClass('panel panel-primary').attr('id', 'card-'+card.id).append(
		$('<div>').addClass('panel-heading').append(
			$('<div>').addClass('panel-title').text('Заказ №'+card.id)
		)
	).append(
		$('<div>').addClass('panel-body').append(
			$('<div>').addClass('col-sm-7').append(
				$('<h3>').text(card.summary)
			)
		).append(
			$('<div>').addClass('col-sm-3 col-sm-offset-2').append(
				$('<button>').css('margin-top', '16px').addClass('btn btn-block btn-primary').attr('role','button').attr('id','execute-'+card.id).text('Выполнить')
			)
		).append(
			$('<div>').addClass('col-sm-12').text(card.description)
		)
	);
}