	<section class="content">
		<div class="box box-primary">
			<div class="box-header">
				<div class = "row">
						<div class="col-md-3 col-sm-5 col-xs-9">
							<!--<div class="input-group monthlyfilter">
								<input type="text" name="daterangefilter" id="daterangefilter" class="form-control" value = "" data-daterangefilter="month">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-calendar"></i>
								</span>
							</div>-->
							<?php
								echo $ui->formField('text')
										->setName('daterangefilter')
										->setId('daterangefilter')
										->setAttribute(array('data-daterangefilter' => 'month'))
										->setAddon('calendar')
										->setValue($datefilter)
										->setValidation('required')
										->draw(true);
							?>
						</div>
						<div class="visible-xs">&nbsp;<br/></div>
						<div class = "col-md-4 pull-right">
							<div class="input-group input-group-sm" >
								<input name="table_search" id = "search" class="form-control pull-right" placeholder="Search" type="text" style = "height: 34px;">
								<div class="input-group-btn" style = "height: 34px;">
									<button type="submit" class="btn btn-default" id="daterange-btn" style = "height: 34px;"><i class="fa fa-search"></i></button>
								</div>
							</div>
						</div>
					</div>
			</div>
			<div class="box-body table-responsive no-padding">
				<table id="tableList" class="table table-hover table-sidepad">
					<thead>
						<tr class = "info">
							<th class = "col-md-3 text-center">Date and Time</th>
							<th class = "col-md-2 text-center">User</th>
							<th class = "col-md-3 text-center">Activity Done</th>
							<th class = "col-md-2 text-center">Module</th>
							<th class = "col-md-2 text-center">Task</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
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
<script>
var ajax = {};
var ajax_call = {};


function showList(pg){
	ajax.daterangefilter = $("#daterangefilter").val();

	$.post('<?=BASE_URL?>report/audit_trail/ajax/at_listing',ajax, function(data) {
		$('#tableList tbody').html(data.table);
		$('#pagination').html(data.pagination);
	});
};

$( "#search" ).keyup(function() 
{
	var search = $( this ).val();
	ajax.search = search;
	showList();
});

$('#pagination').on('click', 'a', function(e) {
	e.preventDefault();
	ajax.page = $(this).attr('data-page');
	showList();
})

$('#daterangefilter').on('change', function() {
	ajax.daterangefilter = $(this).val();
	ajax.page = 1;
	showList();
});

$('#search').on('change', function() {
	ajax.search = $(this).val();
	showList();
});

$(document).ready(function() 
{
	
	/** -- FOR LOADING DATA -- **/
		showList();
	/** -- FOR LOADING DATA -- end **/
	
	/** -- FOR DELETING DATA -- end **/
	
	/** -- FOR IMPORTING DATA -- **/
		$("#import").click(function() 
		{
			$(".import-modal > .modal").css("display", "inline");
			$('.import-modal').modal();
		});
	/** -- FOR IMPORTING DATA -- end **/

	/** -- FOR EXPORTING DATA -- **/
		$("#export").click(function() 
		{
			window.location = '<?=BASE_URL?>maintenance/taxcodes/ajax/export';
		});
	/** -- FOR EXPORTING DATA -- end **/

});

</script>