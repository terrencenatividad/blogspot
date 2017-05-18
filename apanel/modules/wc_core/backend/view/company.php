<section class="content">
	<div class="alert alert-warning alert-dismissable hidden" id="companyAlert">
		<!--<button type="button" class="close" data-dismiss="alert" >&times;</button>-->
		<strong>Error!</strong><p></p>
	</div>
    <div class="box box-primary">
		<!--<div class = "pageheader">
		    <span class = 'pagetitle'> Edit Details </span>
		</div>-->

		<div class="box-body"> 
			<form method = "post" id = "companyForm" class="form-horizontal">
				<h3>Basic Information</h3>
				<hr/>
				<div class="row">
					<label for = "companyimage" class = "control-label col-md-2" align = "right">
						Company Logo
					</label>

					<div class = "col-md-4 field_col">
						<button type="button" class="btn btn-default btn-sm " title="Add a company logo" data-toggle="modal" data-target="#uploadModal"><span class="glyphicon glyphicon-picture"></span></button>
					</div>
				</div>
				<br/>
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Company Name')
								->setSplit('col-md-4', 'col-md-8')
								->setName('companyname')
								->setId('companyname')
								->setValue($companyname)
								->draw();
						?>
					</div>
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Business Type')
								->setSplit('col-md-4', 'col-md-8')
								->setName('businesstype')
								->setId('businesstype')
								->setValue($businesstype)
								->draw();
						?>
					</div>
				</div>
				<br/>
				<h3>Contact Information</h3>
				<hr/>
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Contact Person')
								->setSplit('col-md-4', 'col-md-8')
								->setName('contactname')
								->setId('contactname')
								->setValue($contactname)
								->draw();
						?>
					</div>

					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Contact Person Role')
								->setSplit('col-md-4', 'col-md-8')
								->setName('contactrole')
								->setId('contactrole')
								->setValue($contactrole)
								->draw();
						?>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Phone')
								->setSplit('col-md-4', 'col-md-8')
								->setName('phone')
								->setId('phone')
								->setValue($phone)
								->draw();
						?>
					</div>
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Mobile')
								->setSplit('col-md-4', 'col-md-8')
								->setName('mobile')
								->setId('mobile')
								->setValue($mobile)
								->draw();
						?>
					</div>
				</div>
				<br/>
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('textarea')
								->setLabel('Address')
								->setSplit('col-md-4', 'col-md-8')
								->setName('address')
								->setId('address')
								->setValue($address)
								->draw();
						?>
					</div>
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Email Address')
								->setSplit('col-md-4', 'col-md-8')
								->setName('email')
								->setId('email')
								->setValue($email)
								->draw();
						?>
					</div>
				</div>
				
				<hr/>
				<div class="row">
					<div class="col-md-12 text-center">
						<input type = "submit" name = "add" id="add" value = "Save" class = "btn btn-info btn-flat">
						&nbsp;
						<input type = "submit" name = "cancel" id="cancel" value = "Cancel" class = "btn btn-default btn-flat">
					</div>	
				</div>
			</form>
		</div>
        
	</div>
</section>
<script>
	$(document).ready(function(){
		$('#companyForm').submit(function(e) {
			e.preventDefault();
			$('#companyForm #add').val('Saving...');
			$('#companyForm #add').addClass('disabled');
		
			$.post('<?=BASE_URL?>maintenance/company/ajax/update', $("#companyForm").serialize())
			.done(function( jsondata ) {
				var code 	= jsondata.code;
				
				$('#companyForm #add').val('Save');
				$('#companyForm #add').removeClass('disabled');

				if(code == 1){
					$("#companyAlert").removeClass('alert-warning');
					$("#companyAlert").addClass('alert-info');
					$("#companyAlert").html('<strong>Success!</strong> Changes has been saved.');
				}else{
					$("#companyAlert").removeClass('alert-info');
					$("#companyAlert").addClass('alert-warning');
					$("#companyAlert").html('<strong>Error!</strong> '+message);
				}

				$('html, body').animate({ scrollTop: 0 }, 'slow');
				
				$('#companyAlert').fadeTo(0, 500, function(){
					$(this).removeClass('hidden');
				});
			
				window.setTimeout(function() { 
					$('#companyAlert').fadeTo(500, 0).slideUp(500, function(){
						$(this).addClass('hidden');
					});
				}, 3000);
			});
		});
		
		$('#companyForm #cancel').click(function(e) {
			e.preventDefault();
			location.href = '<?=BASE_URL?>';
		});
	});
</script>