(function ($) {
	'use strict';

	function uniqueIndex() {
		return String(Date.now()) + String(Math.floor(Math.random() * 10000));
	}

	$(document).on('click', '.mpro-select-cover', function (event) {
		event.preventDefault();
		var frame = wp.media({
			title: mproPortfolioAdmin.chooseImage,
			button: { text: mproPortfolioAdmin.useImage },
			multiple: false,
			library: { type: 'image' }
		});

		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();
			$('#mpro_portfolio_cover_id').val(attachment.id);
			$('.mpro-cover-preview').html('<img class="mpro-cover-preview__image" src="' + attachment.url + '" alt="">');
			$('.mpro-remove-cover').prop('hidden', false);
		});

		frame.open();
	});

	$(document).on('click', '.mpro-remove-cover', function (event) {
		event.preventDefault();
		$('#mpro_portfolio_cover_id').val('');
		$('.mpro-cover-preview').empty();
		$(this).prop('hidden', true);
	});

	$(document).on('click', '.mpro-repeater__add', function (event) {
		event.preventDefault();
		var repeater = $(this).closest('.mpro-repeater');
		var template = repeater.find('.mpro-repeater__template').first();
		var row = template.clone(false, false);
		var html = row.prop('outerHTML').split('__INDEX__').join(uniqueIndex());
		row = $(html);
		row.removeClass('mpro-repeater__template').prop('hidden', false);
		row.find('input').val('');
		repeater.find('.mpro-repeater__rows').append(row);
	});

	$(document).on('click', '.mpro-repeater__remove', function (event) {
		event.preventDefault();
		$(this).closest('.mpro-repeater__row').remove();
	});
})(jQuery);
