/* Edit screens (inc/admin.php): fold long repeaters on load so a retreat reads as a list of
   "Day 1", "Day 2"… rows; click a row's fold arrow to open it. Only repeaters that have a
   title sub field ("collapsed" in inc/fields-spec.json) and more than one row are folded. */
(function ($) {
	if (typeof acf === 'undefined') { return; }
	acf.addAction('ready', function () {
		$('.acf-field-repeater').each(function () {
			var $rows = $(this).find('> .acf-input > .acf-repeater > table > tbody > .acf-row:not(.acf-clone)');
			if ($rows.length < 2 || !$rows.first().find('.-collapsed-target').length) { return; }
			$rows.addClass('-collapsed');
		});
	});
})(jQuery);
