;(function ($) {

	$('div.input-group').find('input').on('click', function () {
		var id, displayValue, $root;
		$root = $(this).parents('div.input-group');
		if ($(this).hasClass('js-has-message')) {
			id = $(this).attr('data-message');
			displayValue = 'block';
		} else {
			id = $root.find('input.js-has-message').attr('data-message');
			displayValue = 'none';
		}
		$('.option-message#'+id).css('display', displayValue);
	});

})(jQuery);