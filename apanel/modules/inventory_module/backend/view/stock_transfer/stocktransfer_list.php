<section class = "content">
	<div class="box box-primary">
		<div class="box-header">
			<div class="row">
				<div class="col-md-8">
					<div class="form-group">
						<a href="<?= MODULE_URL ?>create" class="btn btn-primary">Create Stock Transfer Request</a>
					</div>

				</div>
				<div class="col-md-4">
					<div class="form-group">
						<div class="input-group">
							<input id="table_search" class="form-control pull-right" placeholder="Search" type="text">
							<div class="input-group-btn">
								<button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<div class="input-group">
							<input type="text" name="daterangefilter" id="daterangefilter" class="form-control" value = "<?=$datefilter?>" data-daterangefilter="month">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-calendar"></i>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-2">
					<?php
						echo $ui->formField('dropdown')
							->setPlaceholder('Filter Transfer')
							->setName('transfertype')
							->setId('transfertype')
							->setList($types)
							->setNone('Filter: None')
							->draw();
					?>
				</div>
				<div class="col-md-3">
					<?php
						echo $ui->formField('dropdown')
							->setPlaceholder('Select Warehouse')
							->setName('warehouse')
							->setId('warehouse')
							->setList($warehouses)
							->setNone('Filter: None')
							->draw();
					?>
				</div>
				<!--<div class="col-md-5">&nbsp;</div>-->
				<div class="col-md-4">
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
			<div class="nav-tabs-custom"  >
				<label class='form-label'><h4 class='bold'>Transfer Out Approval</h4></label>
				<!--<ul id="filter_tabs" class="nav nav-tabs">
					<li class="active"><a href="open" data-toggle="tab">Request</a></li>
					<li><a href="posted" data-toggle="tab">Transferred</a></li>
					<li ><a href="request" data-toggle="tab">Request From Other Site</a></li>
					<li><a href="all" data-toggle="tab">All</a></li>
				</ul>-->
				<div class="table-responsive" >
					<table id="transfer_out" class="table table-hover">
						<thead>
							<tr class="info">
								<th class="text-center" style="width: 100px"></th>
								<th>Transfer No.</th>
								<th>Requesting Warehouse</th>
								<th>Source Warehouse</th>
								<th>Transaction Date</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
					<div id="pagination"></div>
				</div>	
			</div>

			<div class="nav-tabs-custom"  >
				<label class='form-label'><h4 class='bold'>Transfer In Approval</h4></label>
				<!--<ul id="filter_tabs" class="nav nav-tabs">
					<li class="active"><a href="open" data-toggle="tab">Request</a></li>
					<li><a href="posted" data-toggle="tab">Transferred</a></li>
					<li ><a href="request" data-toggle="tab">Request From Other Site</a></li>
					<li><a href="all" data-toggle="tab">All</a></li>
				</ul>-->
				<div class="table-responsive" >
					<table id="transfer_in" class="table table-hover">
						<thead>
							<tr class="info">
								<th class="text-center" style="width: 100px"></th>
								<th>Transfer No.</th>
								<th>Requesting Warehouse</th>
								<th>Source Warehouse</th>
								<th>Transaction Date</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
					<div id="pagination"></div>
				</div>		
			</div>
				    
		</div>
		
	</div>
</section>

    <!--DELETE RECORD CONFIRMATION MODAL-->
	<div class="modal fade" id="deleteModal" tabindex="-1" data-backdrop="static">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<strong>Confirmation </strong>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					Are you sure you want to delete this account?
					<input type="hidden" id="recordId"/>
				</div>
				<div class="modal-footer">
					<div class="row row-dense">
						<div class="col-md-12 center">
							<div class="btn-group">
								<button type="button" class="btn btn-info" id="btnYes">Yes</button>
							</div>
								&nbsp;&nbsp;&nbsp;
							<div class="btn-group">
								<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
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

	<script>
		var ajax = filterFromURL();
		var ajax_call = '';
			
		$('#table_search').on('input', function () {
			ajax.search = $(this).val();
			getList();
		});
		// $('#filter_tabs li').on('click', function() {
		// 	ajax.filter = $(this).find('a').attr('href');
		// 	getList();
		// });
		$('#items').on('change', function() {
			ajax.limit = $(this).val();
			ajax.page = 1;
			getList();
		});
		$('#daterangefilter').on('change', function() {
			ajax.daterangefilter = $(this).val();
			getList();
		}).trigger('change');
		$('#pagination').on('click', 'a', function(e) {
			e.preventDefault();
			ajax.page = $(this).attr('data-page');
			getList();
		});
		$('#warehouse').on('change',function(){
			var type = 	$('#transfertype').val();

			if( type == "" )
			{
				$('#warehouse').val('');
				$('#transfertype').parent().addClass('has-error');
			}
			else
			{
				ajax.type 	 	= type;
				ajax.warehouse 	= $(this).val();
				getList();
			}
		});
		ajax.limit = 5; 
		ajaxToFilter(ajax,{ search: '#table_search', limit: '#items', type: '#transfertype', warehouse: '#warehouse' , daterangefilter: '#daterangefilter'});
		// ajax.filter = ajax.filter || $('#filter_tabs .active a').attr('href');
		// ajaxToFilterTab(ajax, '#filter_tabs','filter');
		function getList() {
			filterToURL();
			if (ajax_call != '') {
				ajax_call.abort();
			}
			ajax_call = $.post('<?=MODULE_URL?>ajax/list', ajax, function(data) {
				$('#transfer_in tbody').html(data.in);
				$('#transfer_out tbody').html(data.out);
				$('#pagination').html(data.pagination);
				if (ajax.page > data.page_limit && data.page_limit > 0) {
					ajax.page = data.page_limit;
					getList();
				}
			});
		}
		getList();
		function ajaxCallback(id) {
			// var ids = getDeleteId(id);
			ajax.voucherno 	=	id;
			$.post('<?=MODULE_URL?>ajax/delete', ajax, function(data) {
				getList();
			});
		}

		function getIds(ids) {
			var x = ids.split(",");
			return "id[]=" + x.join("&id[]=");
		}
		$(function() {
			linkCancelToModal('#transfer_out .delete','ajaxCallback');
			linkCancelToModal('#transfer_in .delete','ajaxCallback');
		});

		$('#transfertype').on('change',function(){
			$('#transfertype').parent().removeClass('has-error');
		});

		$('#transfer_out').on('click','.release',function(){
			var transferno	=	$(this).attr('data-id');
			window.location = '<?=MODULE_URL?>release/'+transferno;
		});

		$('#transfer_in').on('click','.receive',function(){
			var transferno	=	$(this).attr('data-id');
			window.location = '<?=MODULE_URL?>received/'+transferno;
		});

		$(document.body).on("click", "#delete_yes", function() 
		{   
			var id = [];
				id.push($( this ).attr("data-id"));
			
			 if( id != "" )
			 {
				$.post('<?=MODULE_URL?>ajax/delete', 'delete_id=' + id, function(data) 
				{
					if( data.msg == "" )
						window.location.href = "<?=MODULE_URL?>";
					// else
					// {
					// 	// Call function to display error_get_last
					// 	//show_error(data.msg);
					// }
				});	
			}
		});
	</script>