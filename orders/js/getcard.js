var minOrderId = Infinity;
var maxOrderId = -Infinity;
function show(json) {
	redirectIfLoggedOut();
	$('#loadmore').hide();
	if (typeof json !== 'undefined' && json.length > 0) {
		json.forEach( function(cardJson) {
			createCard(cardJson).appendTo('#cards');
			minOrderId = Math.min(minOrderId, cardJson.id);
			maxOrderId = Math.max(maxOrderId, cardJson.id);
		});
		loadingNow = false;
	}
	else {
		$('.alert-info').remove();
		$('#cards').append(
			$('<div>').addClass("alert alert-info").text('Вы просмотрели все заказы.')
		);
	}
}

function getCards(count) {
	loadingNow = true;
	$.post(
		"./backend/orders.php",
		{ 	action: "getorders",
			count: count,
			id: $.cookie('id'),
			session: $.cookie('session')
		},
		show,
		"JSON"
	);
}

function getCards(count, from) {
	loadingNow = true;
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
getCards(15);

function loadmore()
{
    if($(window).scrollTop() >= $(document).height() - $(window).height() && !loadingNow)
    {
        $('#loadmore').show();
		getCards(15, minOrderId);
    }
}

$(window).scroll(loadmore);