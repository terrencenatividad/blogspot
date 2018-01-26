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
				<li class="<?echo ($tab == 'output_tab') ? 'active' : '';?>"><a href="#Output" id="output_tab" role="tab" data-toggle="tab">Sales</a></li>
				<li class="<?echo ($tab == 'input_tab') ? 'active' : '';?>"><a href="#Input" id="input_tab" role="tab" data-toggle="tab">Purchase / Billing</a></li>
				<li class="<?echo ($tab == 'summary_tab') ? 'active' : '';?>"><a href="#Summary" id="summary_tab" role="tab" data-toggle="tab">Summary</a></li>
			</ul>
			<div class="tab-content no-padding">
				<div id="Output" class="tab-pane table-responsive scroll <?echo ($tab == 'output_tab') ? 'active' : '';?>">
					<?php echo $sales_view ?>
				</div>
				<div id="Input" class="tab-pane <?echo ($tab == 'input_tab') ? 'active' : '';?>">
					<?php echo $purchase_view ?>
				</div>
				<div id="Summary" class="tab-pane <?echo ($tab == 'summary_tab') ? 'active' : '';?>">
					<?php echo $summary_view ?>
				</div>
			</div>
		</div>
	</section>
	</form>
	<script>
		
		$('#daterangefilter').on('apply.daterangepicker', function(ev, picker) {
			$('#vat_summary_form').submit();
		});

		$('#export').click(function() {
			var daterangefilter = $('#daterangefilter').val();

			window.location = '<?php echo MODULE_URL ?>view_export/' + daterangefilter;
		});

		$('.pagination').on('click', 'a', function(e) {
			e.preventDefault();
			$('#vat_summary_form #page').val($(this).attr('data-page'));
			$('#vat_summary_form').submit();
		});

		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			$('#vat_summary_form #tab').val(this.id);
			$('#vat_summary_form').submit();
		});
		
	</script>