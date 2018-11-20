<section class="content">
	<!-- Error Message for File Import -->
	<div class="alert alert-danger hidden" id="import_error">
		<button type="button" class="link btn-sm close" >&times;</button>
		<p>Ok, just a few more things we need to adjust for us to proceed :) </p><hr/>
		<ul>

		</ul>
	</div>
		<div class="box box-primary">
			<div class="box-header pb-none">
				<div class="row">
					<div class="col-md-3">
						<div class="row">
							<?php
								echo $ui->formField('text')
								->setSplit('', 'col-md-11')
								->setPlaceholder('Select Date')
								->setId('datepicker')
								->setAttribute(array('readonly' => 'true'))
								->setName('datepicker')
								->setAddon('calendar')
								->draw(true);
							?>
						</div>
					</div>
					<div class = "col-md-5">
						
					</div>
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
                <div class = "row">
                    <div style = "float:right; margin-right:15px">
                        <button type = "button" class="btn btn-primary btn-flat" id = "sawtCsvBtn"><span class="glyphicon glyphicon-export">CSV</button>&nbsp;
                        <button type = "button" class="btn btn-primary btn-flat" id = "sawtDatBtn"><span class="glyphicon glyphicon-export">DAT</button>
                    </div>
                </div>
			</div>
            <div class = "row">
                <div class = "col-md-12" style = "margin-bottom:20px; margin-left:10px; font-size:14px; font-weight:bold">
                    <h4><b>SUMMARY ALPHALIST OF WITHHOLDING TAXES</b></h4>
                    TIN: <?php echo $tin ?><br>
                    PAYEE'S NAME: <?php echo strtoupper($companyname) ?><br>
                </div>
            </div>				
			<div class="nav-tabs-custom">
				<table id="tableList" class="table table-hover table-sidepad">
					<?php
						echo $ui->loadElement('table')
								->setHeaderClass('info')
								->addHeader('Seq No', array('class' => 'col-md-1'))
								->addHeader('Tin No', array('class' => 'col-md-1'))
								->addHeader('Corporation', array('class' => 'col-md-2'))
								->addHeader('Individual', array('class' => 'col-md-2'))
								->addHeader('Atc Code', array('class' => 'col-md-1'))
								->addHeader('Nature of Payment', array('class' => 'col-md-1'))
								->addHeader('Payment Amount', array('class' => 'col-md-1'))
								->addHeader('Tax Rate', array('class' => 'col-md-1'))
								->addHeader('Tax Withheld', array('class' => 'col-md-1'))
								->draw();
					?>
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
		var ajax = {}
		var ajax_call = '';
		var ajax = filterFromURL();
		ajax.filter = ajax.filter || $('#filter_tabs .active a').attr('href');
		ajaxToFilter(ajax, { limit : '#items', datepicker : '#datepicker' });
		ajaxToFilterTab(ajax, '#filter_tabs', 'filter');
		tableSort('#tableList', function(value, getlist) {
			ajax.sort = value;
			ajax.page = 1;
			if (getlist) {
				getList();
			}
		}, ajax);
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
			filterToURL();
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
		$('#datepicker').on('change', function() {
			ajax.page = 1;
			ajax.datepicker = $(this).val();
			getList();
		});
        $('#sawtCsvBtn').click(function(){
            var datepicker = $('#datepicker').val();
            window.open('<?php echo MODULE_URL ?>sawt_csv?datepicker=' + datepicker);
        });
        $('#sawtDatBtn').click(function(){
            var datepicker = $('#datepicker').val();
            window.open('<?php echo MODULE_URL ?>sawt_dat?datepicker=' + datepicker);
        });
		$("#datepicker").datepicker( {
			format: "MM-yyyy",
			viewMode: "months", 
			minViewMode: "months",
		});
		$("#datepicker").datepicker().datepicker("setDate", new Date());
	</script>