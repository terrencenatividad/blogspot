	<form method="POST" id="vat_summary_form">
	<input name="limit" id="limit" type="hidden" value="10"/>
	<input name="page" id="page" type="hidden" value="1"/>
	<input name="tab" id="tab" type="hidden" value="<?echo $tab?>"/>
	<section class="content">
		<div class="box box-primary">
			<div class="box-header">
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<div class="input-group">
								<input type="text" name="daterangefilter" id="daterangefilter" class="form-control" value = "<?=$daterange?>" data-daterangefilter="month">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-calendar"></i>
								</span>
							</div>
						</div>
					</div>
					<div class="col-md-9 col-sm-9 col-xs-9 text-right">
						<div class="form-group">
							<?php
								echo $ui->setElement('button')
										->setId('export')
										->setPlaceholder('<i class="glyphicon glyphicon-export"></i> Export')
										->draw();
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="nav-tabs-custom">
			<ul id="filter_tabs" class="nav nav-tabs">
				<!-- <li class="<?echo ($tab == 'summary_tab') ? 'active' : '';?>"><a href="#Summary" id="summary_tab" role="tab" data-toggle="tab">Summary</a></li> -->
				<li class="<?echo ($tab == 'output_tab') ? 'active' : '';?>"><a href="output_tab" id="output_tab" role="tab" data-toggle="tab">Sales</a></li>
				<li class="<?echo ($tab == 'input_tab') ? 'active' : '';?>"><a href="input_tab" id="input_tab" role="tab" data-toggle="tab">Purchase / Billing</a></li>	
			</ul>
			<table id="tableList" class="table table-hover table-sidepad">
				<?php
					echo $ui->loadElement('table')
							->setHeaderClass('default')
							->addHeader('Reference', array('class' => 'col-md-1'))
							->addHeader('Partner', array('class' => 'col-md-2'))
							->addHeader('TIN', array('class' => 'col-md-1'))
							->addHeader('Address', array('class' => 'col-md-4'))
							->addHeader('Gross Amount', array('class' => 'col-md-1'))
							->addHeader('Net Amount', array('class' => 'col-md-1'))
							->addHeader('Tax Amount', array('class' => 'col-md-1'))
							->draw();
				?>
				<tbody>

				</tbody>
				<!--<tfoot>
					<tr>
						<td colspan="9">Showing 1 to 25 of 57 entries</td>
					</tr>
				</tfoot>-->
			</table>
			<div id="pagination"></div>
			<!-- <div class="tab-content no-padding">
				<div id="Summary" class="tab-pane <?echo ($tab == 'summary_tab') ? 'active' : '';?>">
					<?php echo $summary_view ?>
				</div>
				<div id="Output" class="tab-pane <?echo ($tab == 'output_tab') ? 'active' : '';?>">
					<?php echo $sales_view ?>
				</div>
				<div id="Input" class="tab-pane <?echo ($tab == 'input_tab') ? 'active' : '';?>">
					<?php echo $purchase_view ?>
				</div>
				
			</div> -->
		</div>
	</section>
	</form>
	<script>
		var ajax = {}
		var ajax_call = '';
		var ajax = filterFromURL();
		ajax.filter = ajax.filter || $('#filter_tabs .active a').attr('href');
		ajaxToFilter(ajax, { limit : '#items', daterangefilter : '#daterangefilter' });
		ajaxToFilterTab(ajax, '#filter_tabs', 'filter');

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
			ajax.daterangefilter 	= $('#daterangefilter').val();
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

		$('#daterangefilter').on('change', function() {
			ajax.page 	= 1;
			ajax.daterangefilter 	= $(this).val();
			getList();
		});
		
		// $('#daterangefilter').on('change', function() {
		// 	$('#vat_summary_form').submit();
		// });
		
		$('#export').click(function() {
			var daterangefilter = $('#daterangefilter').val();

			window.location = '<?php echo MODULE_URL ?>view_export/' + daterangefilter;
		});

		$('#filter_tabs li').on('click', function() {
			ajax.page = 1;
			ajax.filter = $(this).find('a').attr('href');
			getList();
		});
		// $('.pagination').on('click', 'a', function(e) {
		// 	e.preventDefault();
		// 	$('#vat_summary_form #page').val($(this).attr('data-page'));
		// 	$('#vat_summary_form').submit();
		// });
	</script>