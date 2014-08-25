;(function ($) {

	$('div.input-group').find('input').on('click', function () {
		var id, displayValue;
		if ($(this).hasClass('js-has-message')) {
			id = $(this).attr('data-message');
			displayValue = 'block';
		} else {
			id = $('input.js-has-message').attr('data-message');
			displayValue = 'none';
		}
		$('.option-message#'+id).css('display', displayValue);
	});

})(jQuery);