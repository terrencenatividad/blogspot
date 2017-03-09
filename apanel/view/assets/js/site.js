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
function linkDeleteMultipleToTable(delete_multiple, table) {
	function countChecked () {
		var count = $(table + ' tbody').find('[type="checkbox"]:checked').length;
		if (count > 0) {
			$(delete_multiple).html('Delete [' + count + ']').attr('disabled', false);
		} else {
			$(delete_multiple).html('Delete').attr('disabled', true);
		}
	}
	$(document).ajaxComplete(function() {
		countChecked();
	});
	$(table).on('ifToggled', '[type="checkbox"]', function() {
		countChecked();
	});
}

function linkDeleteToModal(delete_button, callback) {
	$('body').on('click', delete_button, function() {
		var id = $(this).attr('data-id');
		$('#delete_modal #delete_yes').attr('data-id', id).attr('onclick', callback + '("' + id + '"); $(this).closest("#delete_modal").modal("hide");');
		$('#delete_modal').modal('show');
	});
}

function linkDeleteMultipleToModal(delete_multiple, table, callback) {
	$('body').on('click', delete_multiple, function() {
		var id = [];
		$(table + ' tbody').find('[type="checkbox"]:checked').each(function() {
			id.push($(this).val());
		});
		$('#delete_modal #delete_yes').attr('data-id', id).attr('onclick', callback + '("' + id + '"); $(this).closest("#delete_modal").modal("hide");');
		$('#delete_modal').modal('show');
	});
}

function getDeleteId(ids) {
	var x = ids.split(",");
	return "delete_id[]=" + x.join("&delete_id[]=");
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