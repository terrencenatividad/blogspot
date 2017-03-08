function drawTemplate() {
	$('select').select2({
		width: '100%',
		containerCssClass: ':all:',
	});
	$(':not(table) input[type="checkbox"], :not(table) input[type="radio"]').iCheck({
		checkboxClass: 'icheckbox_square-blue',
		radioClass: 'iradio_square-blue'
	});
}
drawTemplate();
$(document).ajaxComplete(function() {
	drawTemplate();
});
$('table').on('click', '.ajax_delete', function() {
	var module_url = $('#module_url').val();
	$.post(module_url + 'ajax/ajax_delete', 'delete[]=' + $(this).attr('data-id'), function(data) {
		console.log(data);
	});
});
$('table').on('ifToggled', 'tr [type="checkbox"].checkall', function() {
	var checked = $(this).prop('checked');
	$(this).closest('table').find('tbody [type="checkbox"]').prop('checked', checked).iCheck('update');
});