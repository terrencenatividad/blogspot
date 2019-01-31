<section class="content">
		<div class="box box-primary">
			<div class="box-header pb-none">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<div class="">
								<input id="run" type="button" value="Run Depreciation" class="btn btn-primary">
								<input id="simulate" type="button" value="Simulate Depreciation" class="btn btn-info">
								<!-- <input id="table_search" class="form-control pull-right" placeholder="Search" type="text"> -->
								<!-- <div class="input-group-btn">
									<button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
								</div> -->
							</div>
						</div>
					</div>
				</div>
				<!-- <div class="row">
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
				</div> -->
			</div>
			<div class="box-body table-responsive no-padding">
				<table id="tableList" class="table table-hover table-sidepad">
					<?php
						// echo $ui->loadElement('table')
						// 		->setHeaderClass('info')
						// 		->addHeader(
						// 			'<input type="checkbox" class="checkall">',
						// 			array(
						// 				'class' => 'col-md-1 text-center'
						// 			)
						// 		)
						// 		->addHeader('', array('class' => 'col-md-11'), 'sort', 'asset_number')
						// 		// ->addHeader('Asset Class', array('class' => 'col-md-2'), 'sort', 'asset_class', 'asc')
						// 		// ->addHeader('Budget Center', array('class' => 'col-md-2'), 'sort', 'department', 'asc')
						// 		// ->addHeader('Capitalized Cost', array('class' => 'col-md-2'), 'sort', 'capitalized_cost', 'asc')
						// 		// ->addHeader('Depreciation', array('class' => 'col-md-2'), 'sort', 'depreciation_amount', 'asc')
						// 		->draw();
					?>
					<tbody>

					</tbody>
					<!--<tfoot>
						<tr>
							<td colspan="9">Showing 1 to 25 of 57 entries</td>
						</tr>
					</tfoot>-->
				</table>
			</div>
		</div>
		<div id="pagination"></div>
	</section>
	<div class="delete-modal">
		<div class="modal modal-danger">
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
						<button type="button" class="btn btn-outline btn-flat" id = "delete-yes">Yes</button>
						<button type="button" class="btn btn-outline btn-flat" data-dismiss="modal">No</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="assetmodal" tabindex="-1" data-backdrop="static">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
					<h4 class="modal-title">Tag Asset</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-4 col-md-offset-8">
							<div class="input-group">
								<input id="table_search" class="form-control pull-right" placeholder="Search" type="text">
								<div class="input-group-addon">
									<i class="fa fa-search"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
				<input type="hidden" id="checked" class="checkedass">
				<div class="modal-body">
				<table id="asset_modal_list" class="table table-hover table-clickable table-sidepad no-margin-bottom">
						<thead>
							<tr class="info">
								<th class="col-xs-1"></th>
								<th class="col-xs-4">Asset Class</th>
								<th class="col-xs-3">Asset Number</th>
								<th class="col-xs-3">Budget Center</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
					<div class="row">&nbsp;</div>
					<div class="text-center">
					<input type="button" id="sim" class="simulate btn btn-info" value="Simulate Asset">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					</div>

				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="rundep" tabindex="-1" data-backdrop="static">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
					<h4 class="modal-title">Depreciation Run</h4>
				</div>
				<div class="modal-body">
					<div class="row">
					<div class="modal-body">
					<p>This may take a while. Are you sure you want to run it?</p>
					</div>
					<div class="modal-footer text-center">
						<button type="button" class="btn btn-primary" id = "yes_or_yes">Yes</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
					</div>
					</div>
				</div>
		
			</div>
		</div>
	</div>

	<div class="modal fade" id="lockerModal" tabindex="-1"  data-backdrop="static" data-keyboard="false" >
	<div class="modal-dialog ">

		<div class="modal-content">
			<div class="modal-header ">
				<div class="row">
					<div class="col-md-10">
						<h4 class = 'bold'> <span class="glyphicon glyphicon-warning-sign"></span> Notice!</h4>
					</div>
				</div>
			</div>

			<div class="modal-body">
				<div class = 'row'>
					<div class = 'col-md-12'>
						Proceeding with this transaction will prevent other users from logging in.<br><br>
						<strong>Currently Logged In Users are: </strong><br>
						<div id = "logged_users"></div>
						<br>
						Would you like to proceed with your transaction? 
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<div class="row row-dense">
					<div class="col-md-12 right">
						<div class="form-group">
							<button type="button" class="btn btn-warning" id="btnProceed" >Proceed</button>	
							<button type="button" id="btnCancel" class="btn btn-default">Cancel</button> 
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
	<script>
		var ajax = {}
		var ajax_call = '';
		tableSort('#tableList', function(value, getlist) {
			ajax.sort = value;
			ajax.page = 1;
			if (getlist) {
				getList();
			}
		});
		$('#table_search').on('input', function () {
			ajax.page = 1;
			ajax.search = $(this).val();
			getAssetList();
		});
		$('#items').on('change', function() {
			ajax.limit = $(this).val();
			ajax.page = 1;
			getList();
		});
		$('#pagination').on('click', 'a', function(e) {
			e.preventDefault();
			var li = $(this).closest('li');
			if (li.not('.active').length && li.not('.disabled').length) {
				ajax.page = $(this).attr('data-page');
				getList();
			}
		});
		function getList() {
			if (ajax_call != '') {
				ajax_call.abort();
			}
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list', ajax, function(data) {
				$('#tableList tbody').html(data.table);
				$('#pagination').html(data.pagination);
				if (ajax.page > data.page_limit && data.page_limit > 0) {
					ajax.page = data.page_limit;
					getList();
				}
			});
		}
		getList();

		function getList2() {
			if (ajax_call != '') {
				ajax_call.abort();
			}
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_list_2', ajax, function(data) {
				$('#tableList tbody').html(data.table);
				$('#pagination').html(data.pagination);
				if (ajax.page > data.page_limit && data.page_limit > 0) {
					ajax.page = data.page_limit;
					getList2();
				}
			});
		}

		function getAssetList() {
			if (ajax_call != '') {
				ajax_call.abort();
			}
			ajax_call = $.post('<?=MODULE_URL?>ajax/ajax_load_asset', ajax, function(data) {
				$('#asset_modal_list tbody').html(data.table);
				$('#pagination').html(data.pagination);
				if (ajax.page > data.page_limit && data.page_limit > 0) {
					ajax.page = data.page_limit;
					getAssetList();
				}
			});
		}

		$('#run').on('click', function(){
			$('#rundep').modal('show');
			$("#rundep #yes_or_yes").click(function() {
				$.post('<?=MODULE_URL?>ajax/ajax_load_depreciation', ajax, function(data) {
					getList();
				});
				$('#rundep').modal('hide');
			});
			getAssetList();
		});

		$('#simulate').on('click', function(){
			$('#assetmodal').modal('show');
			getAssetList();
		});

		
		$('#sim').on('click', function() {
			checked = [];
			$('.check:checked').each(function() {
				var ass = $(this).attr('data-id');
				checked.push(ass);
				$('.checkedass').val(checked);
				ajax.checked = $('.checkedass').val();
				$('#assetmodal').modal('hide');
				getList2();
			});
		});

		$.post('<?=MODULE_URL?>ajax/retrieve_users', ajax, function(data) {
		$('#lockerModal #logged_users').html(data.user_lists);
	});
	
	$('#lockerModal').modal('show');

	$('#lockerModal').on('click','#btnProceed',function(){
		$.post('<?=MODULE_URL?>ajax/update_locktime', ajax, function(data) {
			if( data.msg == 'success' )
			{
				$('#lockerModal').modal('hide');
				document.getElementById('timer').innerHTML = 10 + ":" + 01;
				startTimer();

				var warehouse 	=	$('#warehouse').val();

				if( warehouse != "" ){
					$('#warehouse').change();
				}
				
			}
		});
		$('#lockerModal').modal('hide');
	});

	$('#lockerModal').on('click','#btnCancel',function(){
		window.history.back();
	});

	function startTimer() {
		var presentTime = document.getElementById('timer').innerHTML;
		var timeArray = presentTime.split(/[:]+/);
		var m = timeArray[0];
		var s = checkSecond((timeArray[1] - 1));

		if(s == 59){
			m = m-1
		}
		
		if( m == 0 && s == 30 )
		{
			$('#timerModal').modal('show');
			window.location = '<?php echo MODULE_URL;?>';
		}

		document.getElementById('timer').innerHTML = m + ":" + s;
		setTimeout(startTimer, 1000);
	}
			
	</script>