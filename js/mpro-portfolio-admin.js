/**
 * MPRO Portfolio — admin repeater fields
 * Handles dynamic add/remove rows for Tools, People, and Meta Details
 * meta boxes on the Portfolio edit screen.
 */
jQuery(function ($) {
  'use strict';

  $(document).on('click', '.mpro-pf-add-row', function () {
    var $btn   = $(this);
    var target = $btn.data('target');
    var $rows  = $('#' + target + ' .mpro-pf-repeater-rows');

    var rowHtml = '';

    if ($btn.data('people-row')) {
      rowHtml =
        '<div class="mpro-pf-repeater-row mpro-pf-people-row">' +
          '<input type="text" name="mpro_pf_people_name[]" class="regular-text" placeholder="Name">' +
          '<input type="text" name="mpro_pf_people_role[]" class="regular-text" placeholder="Role, e.g. Designer">' +
          '<button type="button" class="button mpro-pf-remove-row">Remove</button>' +
        '</div>';
    } else if ($btn.data('meta-row')) {
      rowHtml =
        '<div class="mpro-pf-repeater-row mpro-pf-meta-row">' +
          '<input type="text" name="mpro_pf_meta_label[]" class="regular-text" placeholder="Label, e.g. Industry">' +
          '<input type="text" name="mpro_pf_meta_value[]" class="regular-text" placeholder="Value, e.g. Real Estate">' +
          '<button type="button" class="button mpro-pf-remove-row">Remove</button>' +
        '</div>';
    } else {
      var $input = $('<input>', {
        type: 'text',
        name: $btn.data('name'),
        'class': 'regular-text',
        placeholder: $btn.data('placeholder') || ''
      });
      var $remove = $('<button>', { type: 'button', 'class': 'button mpro-pf-remove-row' }).text('Remove');
      var $div = $('<div>', { 'class': 'mpro-pf-repeater-row' }).append($input, $remove);
      $rows.append($div);
      return;
    }

    if (rowHtml) $rows.append(rowHtml);
  });

  $(document).on('click', '.mpro-pf-remove-row', function () {
    var $row = $(this).closest('.mpro-pf-repeater-row');
    var $wrap = $row.closest('.mpro-pf-repeater');
    var $allRows = $wrap.find('.mpro-pf-repeater-row');

    // Keep at least one row — clear it instead of removing
    if ($allRows.length <= 1) {
      $row.find('input').val('');
      return;
    }
    $row.remove();
  });
});
