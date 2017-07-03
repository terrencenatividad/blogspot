function drawTemplate() {
	$('select:not([multiple])').each(function() {
		var parent = '';
		if ($(this).closest('.content').length) {
			parent = $(this).closest('.content');
		} else if ($(this).closest('.modal-body').length) {
			parent = $(this).closest('.modal-body');
		}
		$(this).select2({
			width: '100%',
			containerCssClass: ':all:',
			dropdownParent: parent
		});
	});
	$('select[multiple]').selectpicker({
		container: '.content-wrapper',
		selectedTextFormat: 'count'
	});
	$('input[type="checkbox"], *:not(.btn) > input[type="radio"]').iCheck({
		checkboxClass: 'icheckbox_square-blue',
		radioClass: 'iradio_square-blue'
	});
	$('[data-inputmask]').inputmask();
}
function linkButtonToTable(button, table) {
	function countChecked () {
		var count = $(table + ' tbody').find('[type="checkbox"]:checked').length;
		if (count > 0) {
			$(button).attr('disabled', false).find('span').html(' [' + count + ']');
		} else {
			$(button).attr('disabled', true).find('span').html('');
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
	format: 'M dd, yyyy',
	autoclose: true
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

function createConfimationLink(link, callback, confimation_question) {
	$('body').on('click', link, function() {
		var id = $(this).attr('data-id');
		$('#confimation_question').html(confimation_question || 'Are you sure?');
		$('#confimation_modal #confirmation_yes').attr('data-id', id).attr('onclick', callback + '("' + id + '"); $(this).closest("#confimation_modal").modal("hide");');
		$('#confimation_modal #confirmation_no').attr('onclick', '');
		$('#confimation_modal').modal('show');
	});
}

function showConfirmationLink(callback_yes, callback_no, confimation_question) {
	$('#confimation_question').html(confimation_question || 'Are you sure?');
	$('#confimation_modal #confirmation_yes').attr('onclick', callback_yes + '; $(this).closest("#confimation_modal").modal("hide");');
		$('#confimation_modal #confirmation_no').attr('onclick', callback_no);
	$('#confimation_modal').modal('show');
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
	NProgress.set(0.1);
	NProgress.start();
	$('body').addClass('ajax_loading');
});
var last_ajax = {};
$(document).ajaxComplete(function(event, xhr, ajax) {
	if (xhr.statusText == 'OK') {
		drawTemplate();
		NProgress.done();
		$('body').removeClass('ajax_loading');
		var data = {};
		try {
			data = $.parseJSON(xhr.responseText)
		} catch (e) {}
		if (data.locked === true) {
			last_ajax = ajax;
			var modal = '#locked_popup';
			if ($('.modal.in:not(#locked_popup):not(#locked_popup_modal)').length) {
				modal = '#locked_popup_modal';
			}
			$(modal).modal('show');
			$(modal + ' #locktime').html(data.locktime);
			setTimeout(function() {
				$.post(data.baseurl, function() {});
			}, (data.locksec * 1000) + 1000);
		} else {
			if ($('#locked_popup.modal.in, #locked_popup_modal.modal.in').length) {
				$.ajax(last_ajax);
				$('#locked_popup').modal('hide');
				$('#locked_popup_modal').modal('hide');
			}
		}
	}
});
$('body').on('hidden.bs.modal', '.modal', function (e) {
	if ($('.modal').hasClass('in')) {
		$('body').addClass('modal-open');
	}
});

// List Caret
$('tbody').on('click', '.list-caret', function() {
	if ($(this).hasClass('glyphicon-triangle-bottom')) {
		$(this).trigger('click-hide');
	} else {
		$(this).trigger('click-show');
	}
});
$('tbody').on('click-hide', '.list-caret', function() {
	var selector = $(this).attr('data-target');
	$(this).removeClass('glyphicon-triangle-bottom');
	$(this).addClass('glyphicon-triangle-right');
	$(selector).closest('tr').hide();
	$(selector).each(function() {
		$(this).closest('tr').find('.list-caret').trigger('click-hide');
	});
});
$('tbody').on('click-show', '.list-caret', function() {
	var selector = $(this).attr('data-target');
	$(this).removeClass('glyphicon-triangle-right');
	$(this).addClass('glyphicon-triangle-bottom');
	$(selector).closest('tr').show();
	$(selector).each(function() {
		$(this).closest('tr').find('.list-caret').trigger('click-show');
	});
});

// Ajax Delete
$('table').on('click', '.ajax_delete', function() {
	var module_url = $('#module_url').val();
	$.post(module_url + 'ajax/ajax_delete', 'delete[]=' + $(this).attr('data-id'), function(data) {

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
$('body').on('focus', '[data-validation~="decimal"], [data-validation~="integer"]', function() {
	$(this).select();
});
$('body').on('input change blur blur_validate', '[data-validation~="required"]', function(e) {
	var error_message = 'This field is required';
	var form_group = $(this).closest('.form-group');
	var val = $(this).val() || '';
	if (((val instanceof Array) && val.length == 0) || ( ! (val instanceof Array) && val.replace(/\s/g, '') == '')) {
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
	if ((keyCode > 31 && (keyCode < 48 || keyCode > 57) && (keyCode < 96 || keyCode > 105) && keyCode != 190 && keyCode != 110 && keyCode != 188) && ! (controlDown && (keyCode == 67 || keyCode == 86)) && keyCode != 116 && (keyCode < 37 || keyCode > 40) && keyCode != 46) 
		return false;
	return true;
});
$('body').on('blur blur_validate', '[data-validation~="decimal"]', function() {
	var value = $(this).val();
	if (value.replace(/\,/g) != '') {
		var decimal = parseFloat(value.replace(/\,/g,''));
		if (isNaN(decimal)) {
			decimal = 0;
		}
		$(this).val(decimal.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
	}
});
$('body').on('keydown', '[data-validation~="integer"]', function(e) {
	var keyCode = e.originalEvent.keyCode;
	if (keyCode == 17) {
		controlDown = true;
	}
	if ((keyCode > 31 && (keyCode < 48 || keyCode > 57) && (keyCode < 96 || keyCode > 105) && keyCode != 110 && keyCode != 188) && ! (controlDown && (keyCode == 67 || keyCode == 86)) && keyCode != 116 && (keyCode < 37 || keyCode > 40) && keyCode != 46) 
		return false;
	return true;
});
$('body').on('blur blur_validate', '[data-validation~="integer"]', function(e) {
	var value = $(this).val();
	if (value.replace(/\,/g,'') != '') {
		var decimal = parseFloat(value.replace(/\,/g,''));
		if (isNaN(decimal)) {
			decimal = 0;
		}
		$(this).val(decimal.toFixed(0).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
	}
});
$('body').on('blur blur_validate', '[data-max]', function(e) {
	var max = parseFloat($(this).attr('data-max').replace(/\,/g,''));
	var value = parseFloat($(this).val().replace(/\,/g,''));
	var decimal_place = 2;
	if ($(this).filter('[data-validation~="integer"]').length) {
		decimal_place = 0;
	}
	if (value != '') {
		if (max < value) {
			value = max;
		}
		if (isNaN(value)) {
			value = 0;
		}
		$(this).val(value.toFixed(decimal_place).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
	}
});
$('body').on('blur blur_validate', '[data-min]', function(e) {
	var min = parseFloat($(this).attr('data-min').replace(/\,/g,''));
	var value = parseFloat($(this).val().replace(/\,/g,''));
	var decimal_place = 2;
	if ($(this).filter('[data-validation~="integer"]').length) {
		decimal_place = 0;
	}
	if (value !== '') {
		if (min > value || isNaN(value)) {
			value = min;
		}
		$(this).val(value.toFixed(decimal_place).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
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
				linkedCalendars: false,
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