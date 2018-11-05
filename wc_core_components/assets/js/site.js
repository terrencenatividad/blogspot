function drawTemplate() {
	$('select:not([multiple])').each(function() {
		var parent = '';
		if ($(this).closest('.content').length) {
			parent = $(this).closest('.content');
		} else if ($(this).closest('.modal-body').length) {
			parent = $(this).closest('.modal-body');
		}
		var minresult = 1;
		var itemsperpage = `<optionvalue="10">10</option><optionvalue="20">20</option><optionvalue="50">50</option><optionvalue="100">100</option>`;
		if ($(this).html().replace(/\s/g, '') == itemsperpage) {
			minresult = 'Infinity';
		}
		$(this).select2({
			width: '100%',
			containerCssClass: ':all:',
			minimumResultsForSearch: minresult,
			dropdownParent: parent
		});
	});
	$('select:not([multiple])').on('select2:select', function() {
		$(this).closest('.form-group').find('select').focus();
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

var nowDate = new Date($('.datepicker-input').attr('data-date-start-date'));
var today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate()+1, 0, 0, 0, 0);

$('.datepicker-input').datepicker({
	startDate: today,
	format: 'M dd, yyyy',
	autoclose: true,
	forceParse: false
});

$('.datepicker-input').each(function() {
	var val = $(this).val();
	$(this).datepicker('setDate', val);
});

$('body').on('click', '[data-link]', function(e) {
	if (e.target == this) {
		$(this).find($(this).attr('data-link')).click();
	}
});

function linkDeleteToModal(delete_button, callback) {
	$('body').on('click', delete_button, function() {
		var id = $(this).attr('data-id');
		$('#delete_modal #delete_yes').attr('data-id', id).attr('onclick', callback + '("' + id + '"); $(this).closest("#delete_modal").modal("hide");');
		$('#delete_modal').modal('show');
	});
}

function linkCancelToModal(cancel_button, callback) {
	$('body').on('click', cancel_button, function() {
		var id = $(this).attr('data-id');
		$('#cancel_modal #cancel_yes').attr('data-id', id).attr('onclick', callback + '("' + id + '"); $(this).closest("#cancel_modal").modal("hide");');
		$('#cancel_modal').modal('show');
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

function linkCancelMultipleToModal(cancel_multiple, table, callback) {
	$('body').on('click', cancel_multiple, function() {
		var id = [];
		$(table + ' tbody').find('[type="checkbox"]:checked').each(function() {
			id.push($(this).val());
		});
		$('#cancel_modal #cancel_yes').attr('data-id', id).attr('onclick', callback + '("' + id + '"); $(this).closest("#cancel_modal").modal("hide");');
		$('#cancel_modal').modal('show');
	});
}

function getDeleteId(ids) {
	var x = ids.split(",");
	return "delete_id[]=" + x.join("&delete_id[]=");
}

drawTemplate();
$(document).ajaxStart(function(ajax) {
	if (typeof window.loading_indicator === 'undefined' || window.loading_indicator) {
		NProgress.set(0.1);
		NProgress.start();
		$('body').addClass('ajax_loading');
	}
	window.loading_indicator = true;
});
var last_ajax = {};
$(document).ajaxComplete(function(event, xhr, ajax) {
	if (xhr.statusText == 'OK') {
		var data = {};
		try {
			data = $.parseJSON(xhr.responseText)
		} catch (e) {}
		if (typeof data.no_access_modal === 'undefined') {
			drawTemplate();
		}
		NProgress.done();
		$('body').removeClass('ajax_loading');
		if (data.show_login_form) {
			if ( ! ($('#login_popup.modal.in').length || $('#login_popup_modal.modal.in').length)) {
				last_ajax = ajax;
			}
			var modal = '#login_popup';
			if ($('.modal.in:not(#login_popup):not(#login_popup_modal)').length) {
				modal = '#login_popup_modal';
			}
			$(modal).modal('show');
		} else if (data.locked === true) {
			if ( ! ($('#locked_popup.modal.in').length || $('#locked_popup_modal.modal.in').length)) {
				last_ajax = ajax;
			}
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
			if ($('#login_popup.modal.in, #login_popup_modal.modal.in').length) {
				$.ajax(last_ajax);
				$('#login_popup').modal('hide');
				$('#login_popup_modal').modal('hide');
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
$('body').on('click', '.input-group-addon', function() {
	$(this).closest('.input-group').find('.form-control').focus();
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
	var check_type = 'ifUnchecked';
	if (checked) {
		check_type = 'ifChecked';
	}
	$(this).closest('table').find('tbody [type="checkbox"]:not(:disabled, .disabled)').prop('checked', checked).iCheck('update').trigger(check_type);
});

// Cancel Button
$('body').on('click', 'a[data-toggle="back_page"]', function(e) {
	e.preventDefault();
	$('#cancelModal').modal('show');
	var url = '#';
	var not_create = document.referrer != ($(this).attr('href') + 'create');
	var not_edit = document.referrer.indexOf($(this).attr('href') + 'edit/');
	if (document.referrer && not_create && not_edit) {
		url = document.referrer;
	} else {
		url = $(this).attr('href');
	}
	$('#cancelModal #btnYes').attr('href', url);
});

// || Input Validations
// \/
var controlDown = false;
$('body').on('keydown', function(e) {
	if (e.originalEvent.keyCode == '17') {
		controlDown = true;
	}
});
$('body').on('keyup', function(e) {
	if (e.originalEvent.keyCode == '17') {
		controlDown = false;
	}
});
var shiftDown = false;
$('body').on('keydown', function(e) {
	if (e.originalEvent.keyCode == '16') {
		shiftDown = true;
	}
});
$('body').on('keyup', function(e) {
	if (e.originalEvent.keyCode == '16') {
		shiftDown = false;
	}
});
$('body').on('focus', '[data-validation*="decimal"], [data-validation~="integer"]', function() {
	$(this).select();
});
$('body').on('input change blur blur_validate', '[data-validation~="required"]', function(e) {
	if ((e.originalEvent ? e.originalEvent.type : e.type) == 'blur' && $(this).is('[data-daterangefilter]')) {
		return false;
	}
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
$('body').on('keydown', '[data-validation*="decimal"]', function(e) {
	var keyCode = e.originalEvent.keyCode;
	if ((keyCode > 31 && (keyCode < 48 || keyCode > 57) && (keyCode < 96 || keyCode > 105) && keyCode != 190 && keyCode != 110 && keyCode != 188) && ! (controlDown && (keyCode == 67 || keyCode == 86)) && keyCode != 116 && (keyCode < 37 || keyCode > 40) && keyCode != 46) 
		return false;
	return true;
});
$('body').on('blur blur_validate', '[data-validation*="decimal"]', function() {
	var decimal_place = 2;
	var validations = $(this).attr('data-validation').split(' ');
	for (var x = 0; x < validations.length; x++) {
		if (validations[x].includes('decimal')) {
			decimal_place = parseInt(validations[x].replace('decimal', '').replace('[', '').replace(']', '')) || 2;
		}
	}
	var value = $(this).val();
	if (value.replace(/\,/g) != '') {
		var decimal = parseFloat(value.replace(/\,/g,''));
		if (isNaN(decimal)) {
			decimal = 0;
		}
		var parts = decimal.toFixed(decimal_place).split('.');
		parts[0] = parts[0].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
		$(this).val(parts.join('.'));
	}
});
$('body').on('keydown', '[data-validation~="integer"]', function(e) {
	var keyCode = e.originalEvent.keyCode;
	if ((keyCode > 31 && (keyCode < 48 || keyCode > 57) && (keyCode < 96 || keyCode > 105) && keyCode != 110 && keyCode != 188) && ! (controlDown && (keyCode == 67 || keyCode == 86)) && keyCode != 116 && (keyCode < 37 || keyCode > 40) && keyCode != 46) 
		return false;
	return true;
});
$('body').on('keydown', '[data-validation~="contactnumber"]', function(e) {
	var keyCode = e.originalEvent.keyCode;
	if ((shiftDown && ((keyCode > 31 && (keyCode < 48 || keyCode > 57) && (keyCode < 96 || keyCode > 105) && keyCode != 110 && keyCode != 188) && keyCode != 116 && (keyCode < 37 || keyCode > 40) && keyCode != 46)) && ! (controlDown && (keyCode == 67 || keyCode == 86)) && ! (shiftDown && (keyCode == 57 || keyCode == 48))) 
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
	var validations = $(this).attr('data-validation').split(' ');
	for (var x = 0; x < validations.length; x++) {
		if (validations[x].includes('decimal')) {
			decimal_place = parseInt(validations[x].replace('decimal', '').replace('[', '').replace(']', '')) || 2;
		}
	}
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
		var parts = value.toFixed(decimal_place).split('.');
		parts[0] = parts[0].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
		$(this).val(parts.join('.')).trigger('recompute');
	}
});
$('body').on('blur blur_validate', '[data-min]', function(e) {
	var min = parseFloat($(this).attr('data-min').replace(/\,/g,''));
	var value = parseFloat($(this).val().replace(/\,/g,''));
	var decimal_place = 2;
	var validations = $(this).attr('data-validation').split(' ');
	for (var x = 0; x < validations.length; x++) {
		if (validations[x].includes('decimal')) {
			decimal_place = parseInt(validations[x].replace('decimal', '').replace('[', '').replace(']', '')) || 2;
		}
	}
	if ($(this).filter('[data-validation~="integer"]').length) {
		decimal_place = 0;
	}
	if (value !== '') {
		if (min > value || isNaN(value)) {
			value = min;
		}
		var parts = value.toFixed(decimal_place).split('.');
		parts[0] = parts[0].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
		$(this).val(parts.join('.')).trigger('recompute');
	}
});
$('body').on('blur blur_validate keyup keydown', '[data-validation~="code"]', function(e) {
	var error_message = `Invalid Input <a href="#invalid_characters" class="glyphicon glyphicon-info-sign" data-toggle="modal" data-error_message="<p><b>Allowed Characters:</b> a-z A-Z 0-9 - _</p><p>Letters, Numbers, Dash, and Underscore</p><p><b>Note:</b> Space is an Invalid Character</p>"></a>`;
	var form_group = $(this).closest('.form-group');
	var val = $(this).val() || '';
	if ( ! (/^[a-zA-Z0-9-_]*$/.test(val))) {
		form_group.addClass('has-error');
		form_group.find('p.help-block.m-none').html(error_message)
	} else {
		if (form_group.find('p.help-block.m-none').html() == error_message) {
			form_group.removeClass('has-error').find('p.help-block.m-none').html('');
		}
	}
});
$('body').on('blur blur_validate keyup keydown', '[data-validation~="alpha"]', function(e) {
	var error_message = `Invalid Input <a href="#invalid_characters" class="glyphicon glyphicon-info-sign" data-toggle="modal" data-error_message="<p><b>Allowed Characters:</b> a-z A-Z</p><p>Letters Only</p><p><b>Note:</b> Space is an Invalid Character</p>"></a>`;
	var form_group = $(this).closest('.form-group');
	var val = $(this).val() || '';
	if ( ! (/^[a-zA-Z]*$/.test(val))) {
		form_group.addClass('has-error');
		form_group.find('p.help-block.m-none').html(error_message)
	} else {
		if (form_group.find('p.help-block.m-none').html() == error_message) {
			form_group.removeClass('has-error').find('p.help-block.m-none').html('');
		}
	}
});
$('body').on('blur blur_validate keyup keydown', '[data-validation~="num"]', function(e) {
	var error_message = `Invalid Input <a href="#invalid_characters" class="glyphicon glyphicon-info-sign" data-toggle="modal" data-error_message="<p><b>Allowed Characters:</b> 0-9</p><p>Numbers Only</p><p><b>Note:</b> Space is an Invalid Character</p>"></a>`;
	var form_group = $(this).closest('.form-group');
	var val = $(this).val() || '';
	if ( ! (/^[0-9]*$/.test(val))) {
		form_group.addClass('has-error');
		form_group.find('p.help-block.m-none').html(error_message)
	} else {
		if (form_group.find('p.help-block.m-none').html() == error_message) {
			form_group.removeClass('has-error').find('p.help-block.m-none').html('');
		}
	}
});
$('body').on('blur blur_validate keyup keydown', '[data-validation~="alpha_num"]', function(e) {
	var error_message = `Invalid Input <a href="#invalid_characters" class="glyphicon glyphicon-info-sign" data-toggle="modal" data-error_message="<p><b>Allowed Characters:</b> a-z A-Z 0-9</p><p>Letters and Numbers Only</p><p><b>Note:</b> Space is an Invalid Character</p>"></a>`;
	var form_group = $(this).closest('.form-group');
	var val = $(this).val() || '';
	if ( ! (/^[a-zA-Z0-9]*$/.test(val))) {
		form_group.addClass('has-error');
		form_group.find('p.help-block.m-none').html(error_message)
	} else {
		if (form_group.find('p.help-block.m-none').html() == error_message) {
			form_group.removeClass('has-error').find('p.help-block.m-none').html('');
		}
	}
});
$('body').on('blur blur_validate keyup keydown', '[data-validation~="special"]', function(e) {
	var error_message = `Invalid Input <a href="#invalid_characters" class="glyphicon glyphicon-info-sign" data-toggle="modal" data-error_message="<p><b>Allowed Characters:</b> a-z A-Z 0-9 . , [space] % & ( ) [ ] _ - + = / $ # @ ! ' &quot; : ;</p><p>Letters, Numbers, Period, Comma, Space, Percent, Ampersand, Left Parenthesis, Right Parenthesis, Left Bracket, Right Bracket, Underscore, Minus, Plus, Equal, Slash, Dollar Sign, Number Sign, At Sign, Exclamation, Single Quote, Double Quote, Colon, and Semicolon</p><p><b>Note:</b> Other Special Characters are Invalid Character</p>"></a>`;
	var form_group = $(this).closest('.form-group');
	var val = $(this).val() || '';
	if ( ! (/^[a-zA-Z0-9., %&()\[\]_\-+=/$#@!'":;]*$/.test(val))) {
		form_group.addClass('has-error');
		form_group.find('p.help-block.m-none').html(error_message)
	} else {
		if (form_group.find('p.help-block.m-none').html().replace('amp;', '') == error_message) {
			form_group.removeClass('has-error').find('p.help-block.m-none').html('');
		}
	}
});
$('body').on('blur blur_validate keyup keydown', '[data-validation~="email"]', function(e) {
	var error_message = `Invalid E-mail Format.`;
	var form_group = $(this).closest('.form-group');
	var val = $(this).val() || '';
	if ( val!="" && ! (/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(val))) {
		form_group.addClass('has-error');
		form_group.find('p.help-block.m-none').html(error_message)
	} else {
		if (form_group.find('p.help-block.m-none').html() == error_message) {
			form_group.removeClass('has-error').find('p.help-block.m-none').html('');
		}
	}
});
// /\
// || Input Validations

$('body').on('show.bs.modal', '#invalid_characters.modal', function (e) {
	var error_message = $(e.relatedTarget).attr('data-error_message') || '<p>Invalid Characters</p>';
	$('#invalid_characters.modal .modal-body').html(error_message);
});


// || Populate Modals
// \/
$('#modal_div').html(`
<div id="invalid_characters" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Invalid Characters</h4>
			</div>
			<div class="modal-body">
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
			</div>
		</div>
	</div>
</div>
<div id="delete_modal" class="modal modal-danger">
	<div class="modal-dialog" style = "width: 300px;">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Confirmation</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to delete this record?</p>
			</div>
			<div class="modal-footer text-center">
				<button type="button" id="delete_yes" class="btn btn-outline btn-flat" onclick="">Yes</button>
				<button type="button" class="btn btn-outline btn-flat" data-dismiss="modal">No</button>
			</div>
		</div>
	</div>
</div>
<div id="warning_modal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title modal-danger"><span class="glyphicon glyphicon-warning-sign"></span> Oops!</h4>
			</div>
			<div class="modal-body">
				<p id = "warning_message"></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Ok</button>
			</div>
		</div>
	</div>
</div>
<div id="success_modal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title modal-success"><span class="glyphicon glyphicon-ok"></span> Success!</h4>
			</div>
			<div class="modal-body">
				<p id = "message"></p>
			</div>
			<div class="modal-footer">
				<!-- <button type="button" class="btn btn-success" data-dismiss="modal">Ok</button> -->
			</div>
		</div>
	</div>
</div>
<div id="cancel_modal" class="modal modal-warning">
	<div class="modal-dialog" style = "width: 300px;">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Confirmation</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to cancel this record?</p>
			</div>
			<div class="modal-footer text-center">
				<button type="button" id="cancel_yes" class="btn btn-outline btn-flat" onclick="">Yes</button>
				<button type="button" class="btn btn-outline btn-flat" data-dismiss="modal">No</button>
			</div>
		</div>
	</div>
</div>
<div id="confimation_modal" class="modal">
	<div class="modal-dialog" style = "width: 300px;">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Confirmation</h4>
			</div>
			<div class="modal-body">
				<p id="confimation_question">Are you sure you want to delete this record?</p>
			</div>
			<div class="modal-footer text-center">
				<button type="button" id="confirmation_yes" class="btn btn-primary btn-flat" onclick="">Yes</button>
				<button type="button" id="confirmation_no" class="btn btn-default btn-flat" data-dismiss="modal">No</button>
			</div>
		</div>
	</div>
</div>
<div class="modal" id="locked_popup_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title text-center">System is Locked for the Moment</h4>
			</div>
			<div class="modal-body">
				<p class="text-red text-center">Locked Time: <span id="locktime"></span></p>
			</div>
		</div>
	</div>
</div>
<div class="modal" id="login_popup_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="form-group has-feedback">
				<input type="text" id="login_form_username" name="login_form_username" class="form-control" placeholder="Username" value="<?php echo USERNAME ?>" readonly>
				<span class="glyphicon glyphicon-user form-control-feedback"></span>
			</div>
			<div class="form-group has-feedback">
				<input type="password" id="login_form_password" name="login_form_password" class="form-control" placeholder="Password">
				<span class="glyphicon glyphicon-lock form-control-feedback"></span>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<button type="button" id="login_form_button" class="btn btn-primary btn-block btn-flat">Sign In</button>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- two seconds delay modal : Added by Sabriella -->
<div id="delay_modal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title modal-success"><span class="glyphicon glyphicon-ok"></span> Success!</h4>
			</div>
			<div class="modal-body">
				<p>Successfully Saved</p>
			</div>
			<div class="modal-footer">
			<!--	<button type="button" class="btn btn-success" data-dismiss="modal">Ok</button> -->
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="cancelModal" tabindex="-1" data-backdrop="static">
<div class="modal-dialog modal-sm">
	<div class="modal-content">
		<div class="modal-header">
			Confirmation
			<button type="button" class="close" data-dismiss="modal">&times;</button>
		</div>
		<div class="modal-body">
			Are you sure you want to cancel this transaction?
		</div>
		<div class="modal-footer">
			<div class="row row-dense">
				<div class="col-md-12 center">
					<div class="btn-group">
						<a href="" class="btn btn-primary btn-flat" id="btnYes">Yes</a>
					</div>
						&nbsp;&nbsp;&nbsp;
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">No</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>

<div id="deactivate_modal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title modal-success">Deactivate!</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to Deactivate this Entry</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="deactyes" data-dismiss="modal">Yes</button>
				<button type="button" class="btn btn-default" id="deactno" data-dismiss="modal">No</button>
			</div>
		</div>
	</div>
</div>

<!--DEACTIVATE RECORDS CONFIRMATION MODAL-->
<div class="modal fade" id="multipleDeactivateModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				Confirmation
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				Are you sure you want to deactivate the selected data?
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 center">
						<div class="btn-group">
							<button type="button" class="btn btn-primary btn-flat" id="btnDeac">Yes</button>
						</div>
							&nbsp;&nbsp;&nbsp;
						<div class="btn-group">
							<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">No</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!--ACTIVATE RECORDS CONFIRMATION MODAL-->
<div class="modal fade" id="multipleActivateModal" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				Confirmation
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				Are you sure you want to activate the selected data?
			</div>
			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 center">
						<div class="btn-group">
							<button type="button" class="btn btn-primary btn-flat" id="btnYes">Yes</button>
						</div>
							&nbsp;&nbsp;&nbsp;
						<div class="btn-group">
							<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">No</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End -->
`);
// /\
// || Populate Modals

$('[data-daterangefilter]').each(function() {
	var type = $(this).attr('data-daterangefilter');
	if (type == 'month') {
		var year_filter = moment().year();
		$(this).daterangepicker({
			linkedCalendars: false,
			ranges: {
				'January': [moment().month(0).year(year_filter).startOf('month'), moment().month(0).year(year_filter).endOf('month')],
				'July': [moment().month(6).year(year_filter).startOf('month'), moment().month(6).year(year_filter).endOf('month')],
				'February': [moment().month(1).year(year_filter).startOf('month'), moment().month(1).year(year_filter).endOf('month')],
				'August': [moment().month(7).year(year_filter).startOf('month'), moment().month(7).year(year_filter).endOf('month')],
				'March': [moment().month(2).year(year_filter).startOf('month'), moment().month(2).year(year_filter).endOf('month')],
				'September': [moment().month(8).year(year_filter).startOf('month'), moment().month(8).year(year_filter).endOf('month')],
				'April': [moment().month(3).year(year_filter).startOf('month'), moment().month(3).year(year_filter).endOf('month')],
				'October': [moment().month(9).year(year_filter).startOf('month'), moment().month(9).year(year_filter).endOf('month')],
				'May': [moment().month(4).year(year_filter).startOf('month'), moment().month(4).year(year_filter).endOf('month')],
				'November': [moment().month(10).year(year_filter).startOf('month'), moment().month(10).year(year_filter).endOf('month')],
				'June': [moment().month(5).year(year_filter).startOf('month'), moment().month(5).year(year_filter).endOf('month')],
				'December': [moment().month(11).year(year_filter).startOf('month'), moment().month(11).year(year_filter).endOf('month')]
			},
			// startDate: moment().startOf('month'),
			// endDate: moment().endOf('month'),
			autoUpdateInput: false,
			locale: {
				format: 'MMM DD, YYYY',
				cancelLabel: 'Clear'
			},
			parentEl: $('#monthly_datefilter')[0]
		}).on('apply.daterangepicker', function(ev, picker) {
			$(this).val(picker.startDate.format('MMM DD, YYYY') + ' - ' + picker.endDate.format('MMM DD, YYYY')).trigger('change');
		}).on('cancel.daterangepicker', function(ev, picker) {
			$(this).val('').trigger('change');
		}).attr('placeholder', 'Date Filter');
	} else {

	}
});