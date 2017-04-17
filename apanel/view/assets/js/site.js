function drawTemplate() {
	$('select').select2({
		width: '100%',
		containerCssClass: ':all:',
	});
	$('input[type="checkbox"], *:not(.btn) > input[type="radio"]').iCheck({
		checkboxClass: 'icheckbox_square-blue',
		radioClass: 'iradio_square-blue'
	});
	$('[data-inputmask]').inputmask();
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
	countChecked();
	$(document).ajaxComplete(function() {
		countChecked();
	});
	$(table).on('ifToggled', '[type="checkbox"]', function() {
		countChecked();
	});
}

$('.datepicker-input').datepicker({
	format: 'M d, yyyy'
});

$('.datepicker-input').each(function() {
	var val = $(this).val();
	$(this).datepicker('setDate', val);
});

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
$(document).ajaxStart(function() {
	Pace.restart();
}); 
$(document).ajaxComplete(function() {
	drawTemplate();
	Pace.stop();
});

// Ajax Delete
$('table').on('click', '.ajax_delete', function() {
	var module_url = $('#module_url').val();
	$.post(module_url + 'ajax/ajax_delete', 'delete[]=' + $(this).attr('data-id'), function(data) {
		console.log(data);
	});
});
// Checkall Checkbox
$('table').on('ifToggled', 'tr [type="checkbox"].checkall', function() {
	var checked = $(this).prop('checked');
	$(this).closest('table').find('tbody [type="checkbox"]').prop('checked', checked).iCheck('update');
});


// || Input Validations
// \/
var controlDown = false;
$('body').on('keyup', function(e) {
	if (e.originalEvent.keyCode == '17') {
		controlDown = false;
	}
})
$('body').on('input change blur', '[data-validation~="required"]', function(e) {
	var error_message = 'This field is required';
	var form_group = $(this).closest('.form-group');
	var val = $(this).val() || '';
	if (val.replace(/\s/g, '') == '') {
		form_group.addClass('has-error');
			form_group.find('p.help-block.m-none').html(error_message)
	} else {
		if (form_group.find('p.help-block.m-none').html() == error_message) {
			form_group.removeClass('has-error').find('p.help-block.m-none').html('');
		}
	}
});
$('body').on('keydown', '[data-validation~="decimal"]', function(e) {
	var keyCode = e.originalEvent.keyCode;
	if (keyCode == 17) {
		controlDown = true;
	}
	if ((keyCode > 31 && (keyCode < 48 || keyCode > 57) && (keyCode < 96 || keyCode > 105) && keyCode != 190 && keyCode != 110 && keyCode != 188) && ! (controlDown && (keyCode == 67 || keyCode == 86)) && keyCode != 116) 
		return false;
	return true;
});
$('body').on('blur', '[data-validation~="decimal"]', function() {
	var value = $(this).val();
	if (value.replace(/\,\s/g,'') != '') {
		var decimal = parseFloat(value.replace(/\,/g,''));
		if (isNaN(decimal)) {
			decimal = 0;
		}
		$(this).val(decimal.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
	}
});
$('body').on('keydown', '[data-validation~="integer"]', function(e) {
	var keyCode = e.originalEvent.keyCode;
	if (keyCode > 31 && (keyCode < 48 || keyCode > 57) && (keyCode < 96 || keyCode > 105)) 
		return false;
	return true;
});
$('body').on('blur', '[data-validation~="integer"]', function(e) {
	var value = $(this).val();
	if (value.replace(/\,\s/g,'') != '') {
		var decimal = parseFloat(value.replace(/\,/g,''));
		if (isNaN(decimal)) {
			decimal = 0;
		}
		$(this).val(decimal.toFixed(0).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
	}
});
// /\
// || Input Validations

$('[data-daterangefilter]').each(function() {
	var type = $(this).attr('data-daterangefilter');
	if (type == 'month') {
		var year_filter = moment().year();
		$(this).daterangepicker(
			{
				ranges: {
					'January': [moment().month(0).year(year_filter).startOf('month'), moment().month(0).year(year_filter).endOf('month')],
					'February': [moment().month(1).year(year_filter).startOf('month'), moment().month(1).year(year_filter).endOf('month')],
					'March': [moment().month(2).year(year_filter).startOf('month'), moment().month(2).year(year_filter).endOf('month')],
					'April': [moment().month(3).year(year_filter).startOf('month'), moment().month(3).year(year_filter).endOf('month')],
					'May': [moment().month(4).year(year_filter).startOf('month'), moment().month(4).year(year_filter).endOf('month')],
					'June': [moment().month(5).year(year_filter).startOf('month'), moment().month(5).year(year_filter).endOf('month')],
					'July': [moment().month(6).year(year_filter).startOf('month'), moment().month(6).year(year_filter).endOf('month')],
					'August': [moment().month(7).year(year_filter).startOf('month'), moment().month(7).year(year_filter).endOf('month')],
					'September': [moment().month(8).year(year_filter).startOf('month'), moment().month(8).year(year_filter).endOf('month')],
					'October': [moment().month(9).year(year_filter).startOf('month'), moment().month(9).year(year_filter).endOf('month')],
					'November': [moment().month(10).year(year_filter).startOf('month'), moment().month(10).year(year_filter).endOf('month')],
					'December': [moment().month(11).year(year_filter).startOf('month'), moment().month(11).year(year_filter).endOf('month')]
				},
				startDate: moment().startOf('month'),
				endDate: moment().endOf('month'),
				locale: {
					format: 'MMM DD, YYYY'
				}
			}
		);
	} else {

	}
});