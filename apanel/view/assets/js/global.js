function tableSort(table, callback) {
	$(table).on('click', 'a[data-sort]', function() {
		var field = $(this).attr('data-field');
		var sort = $(this).attr('data-sort');
		var fields = field.split(',');
		$(this).closest('tr').find('a[data-sort]').attr('data-sort', '');
		var new_sort = '';
		if (sort != 'asc') {
			new_sort = 'asc';
		} else {
			new_sort = 'desc';
		}
		$(this).attr('data-sort', new_sort);
		var value = fields.join(' ' + new_sort + ', ') + ' ' + new_sort;
		callback(value);
	});
}