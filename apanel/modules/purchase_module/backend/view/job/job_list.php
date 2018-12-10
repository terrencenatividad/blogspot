<section class="content">

	<div class="box box-primary">
		<form method="post">
			<div class="box-header">
				<div class="row">
					<div class="col-md-8">
						<div class="form-group">
							<?=	$ui->CreateNewButton(''); ?>
							<button type="button" id="item_multiple_cancel" class="btn btn-danger btn-flat">Cancel</button>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<div class="input-group">
								<input id="table_search" name="table_search" class="form-control pull-right" placeholder="Search" type="text">
								<div class="input-group-btn">
									<button type="submit" class="btn btn-default" id="search_table"><i class="fa fa-search"></i></button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4 col-md-offset-8">
						<div class="row">
							<div class="col-sm-8 col-xs-6 text-right">
								<label for="" class="padded">Items: </label>
							</div>
							<div class="col-sm-4 col-xs-6">
								<div class="form-group">
									<select id="items">
										<option value="10">10</option>
										<option value="20">20</option>
										<option value="50">50</option>
										<option value="100">100</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="container-fluid">
			<div class="alert alert-warning alert-dismissable hidden">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<h4><strong>Error!</strong></h4>
				<div id="errmsg"></div>
			</div>
			</div>


			<div class="panel panel-default">
				<div class="box-body table table-responsive">
					<table id="tableList" class="table table-striped table-condensed table-bordered table-hover">
						<?
						echo $ui->loadElement('table')
						->setHeaderClass('info')
						->addHeader(
							'<input type="checkbox" class="checkall" id="checkkk" value="1">',
							array(
								'class' => 'col-md-1 text-center'
							)
						)
						->addHeader('Job Number',array('class'=>'col-md-3'),'sort','job_no')
                        ->addHeader('Notes',array('class'=>'col-md-5'),'sort','notes')
                        ->addHeader('Status', array('class'=>'col-md-1'),'sort','stat')
                        ->draw();
						?>
						<tbody id="list_container">

						</tbody>
					</table>
					<div id="pagination"></div>

				</div>
			</div>
		</form>
	</div>
</section>




<script>
	var ajax = {};

	// Sorting Script
	tableSort('#tableList', function(value) {
	ajax.sort = value;
	ajax.page = 1;
	showList();
	});

	$("#table_search").keyup(function () {
		var search = $(this).val();
		ajax.search = search;
		showList();
	});

	function show_error(msg) {
		$(".delete-modal").modal("hide");
		$(".alert-warning").removeClass("hidden");
		$("#errmsg").html(msg);
	}

	function showList() {
		$.post('<?=MODULE_URL?>ajax/ajax_list', ajax, function (data) {
			$('.checkall').iCheck('uncheck');
			$('#list_container').html(data.table);
			$('#pagination').html(data.pagination);
			$("#export_id").attr('href', 'data:text/csv;filename=chart_of_accounts.csv;charset=utf-8,' + encodeURIComponent(
				data.csv));

			if (ajax.page > data.page_limit && data.page_limit > 0) {
				ajax.page = data.page_limit;
				showList();
			}

		});
	}

	$(function () {
		showList();

		$("#tableList").on('click', '.delete', function () {
			var id = $(this).attr("data-id");
			if (id != "") {
				$(".delete-modal > .modal").css("display", "inline");
				$(".delete-modal").modal("show");
				
				console.log("a");
				$("#delete-yes").click(function () {
					$.post('<?=MODULE_URL?>ajax/ajax_delete', 'id=' + id, function (data) {
						if (data.msg == 'success') {
							$(".delete-modal").modal("hide");
							showList();
						} else {
							$(".delete-modal").modal("hide");
							show_error("Unable to cancel job.".data.msg);
						}
					});
				});
			}

		});


	});

	$("#selectall").click(function () {
		$('input:checkbox').not(this).prop('checked', this.checked);
	});

	function getSelectedIds() {
		id = [];
		$('.checkbox:checked').each(function () {
			id.push($(this).val());
		});
		return id;
	}

	$('#pagination').on('click', 'a', function (e) {
		e.preventDefault();
		$('.checked').iCheck('uncheck');
		var li = $(this).closest('li');
		if (li.not('.active').length && li.not('.disabled').length) {
			ajax.page = $(this).attr('data-page');
			showList();
		}
	});

	$('#items').on('change', function () {
		ajax.limit = $(this).val();
		ajax.page = 1;
		showList();
	});

	$('#export_id').prop('download', 'atccode.csv');

	function ajaxCallback(id) {
		var ids = getDeleteId(id);
		$.post('<?=MODULE_URL?>ajax/canceljob', 'id=' + id, function (data) {
			if (data.msg == 'success') {
				showList();
				$(".alert-warning").addClass("hidden");
			} else {
				// Call function to display error_get_last
				show_error(data.msg);
			}
		});
	}
	$('#tableList').on('click', '.cancel', function() {
		
	});

	$(function() {
		linkButtonToTable('#item_multiple_cancel', '#tableList');
		// linkButtonToTable('#activateMultipleBtn', '#brand_table');
		// linkButtonToTable('#deactivateMultipleBtn', '#brand_table');
		linkCancelToModal('#tableList .cancel', 'ajaxCallback');
		linkCancelMultipleToModal('#item_multiple_cancel', '#tableList', 'ajaxCallback');
	});

</script>
