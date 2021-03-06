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
			<div class="col-md-11">
				<div class="row">
					<label for = "companyimage" class = "control-label col-md-2" align = "right">
						Company Logo
					</label>

					<div class = "col-md-4 field_col">
					<?php
						if(empty($companyimage)){
							echo '<button type="button" class="btn btn-default btn-sm " title="Add a system logo" data-toggle="modal" data-target="#uploadModal"><span class="glyphicon glyphicon-picture"></span></button>';
						}else{
							echo '<a href="" data-toggle="modal" data-target="#uploadModal" >';
							echo '<img src="../../../uploads/company_logo/'.$companyimage.'" />';
							echo '</a>';
						}
					?>
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
								->setValidation('special required')
								->draw();
						?>
					</div>
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Line of Business')
								->setSplit('col-md-4', 'col-md-8')
								->setName('businessline')
								->setId('businessline')
								->setValue($businessline)
								->setMaxLength(30)
								->draw();
						?>
					</div>
				</div>
				<br/>
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('dropdown')
								->setLabel('Business Type')
								->setSplit('col-md-4', 'col-md-8')
								->setName('businesstype')
								->setId('businesstype')
								->setList($businesstype_list)
								->setValue($businesstype)
								->setMaxLength(50)
								->draw();
						?>
					</div>
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Company TIN')
								->setSplit('col-md-4', 'col-md-8')
								->setName('tin')
								->setId('tin')
								->setValue($tin)
								->setAttribute(array('data-inputmask' => "'mask': '999-999-999-999'"))
								->setPlaceholder('000-000-000-000')
								->setMaxLength(15)
								->draw();
						?>
					</div>
				</div>
				<br/>
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('dropdown')
								->setLabel('RDO Code')
								->setSplit('col-md-4', 'col-md-8')
								->setName('rdo_code')
								->setId('rdo_code')
								->setList($rdo_list)
								->setValue($rdo_code)
								->setMaxLength(3)
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
								->setLabel('First Name')
								->setSplit('col-md-4', 'col-md-8')
								->setName('firstname')
								->setId('firstname')
								->setValue($firstname)
								->setMaxLength(15)
								->draw();
						?>
					</div>

					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Middle Name')
								->setSplit('col-md-4', 'col-md-8')
								->setName('middlename')
								->setId('middlename')
								->setValue($middlename)
								->setMaxLength(10)
								->draw();
						?>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Last Name')
								->setSplit('col-md-4', 'col-md-8')
								->setName('lastname')
								->setId('lastname')
								->setValue($lastname)
								->setMaxLength(15)
								->draw();
						?>
					</div>

					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Contact Role')
								->setSplit('col-md-4', 'col-md-8')
								->setName('contactrole')
								->setId('contactrole')
								->setValue($contactrole)
								->setMaxLength(25)
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
								->setMaxLength(12)
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
								->setMaxLength(15)
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
								->setMaxLength(70)
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
								->setMaxLength(36)
								->draw();
						?>
					</div>
				</div>
				<br/>
				<h3>Authorized Signatory</h3>
				<hr/>
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Signatory Name')
								->setSplit('col-md-4', 'col-md-8')
								->setName('signatory_name')
								->setId('signatory_name')
								->setValue($signatory_name)
								->setMaxLength(50)
								->draw();
						?>
					</div>
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Signatory Role')
								->setSplit('col-md-4', 'col-md-8')
								->setName('signatory_role')
								->setId('signatory_role')
								->setValue($signatory_role)
								->setMaxLength(50)
								->draw();
						?>
					</div>
				</div>
				<br/>
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('text')
								->setLabel('Signatory TIN')
								->setSplit('col-md-4', 'col-md-8')
								->setName('signatory_tin')
								->setId('signatory_tin')
								->setValue($signatory_tin)
								->setAttribute(array('data-inputmask' => "'mask': '999-999-999-999'"))
								->setPlaceholder('000-000-000-000')
								->setMaxLength(15)
								->draw();
						?>
					</div>
				</div>
				<br/>
				<h3>System Settings</h3>
				<hr/>
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('dropdown')
								->setLabel('Accounting Period ')
								->setSplit('col-md-4', 'col-md-8')
								->setName('taxyear')
								->setId('taxyear')
								->setList($taxyear_list)
								->setValue($taxyear)
								->setValidation('required')
								->draw();
						?>
					</div>
					<div class="col-md-6">
						<?php
							echo $ui->formField('dropdown')
								->setLabel('Period Start ')
								->setSplit('col-md-4', 'col-md-8')
								->setName('periodstart')
								->setId('periodstart')
								->setList($period_list)
								->setValue($periodstart)
								->setAttribute(
									array(
										$taxyear_lock
									)
								)
								->setValidation('required')
								->draw();
						?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<?php
							echo $ui->formField('dropdown')
								->setLabel('Apply Withholding Tax On ')
								->setSplit('col-md-4', 'col-md-8')
								->setName('wtax_option')
								->setId('wtax_option')
								->setList($wtax_option_list)
								->setValue($wtax_option)
								->setValidation('required')
								->draw();
						?>
					</div>
				</div>
			</div>	
			<div class="row">
				<div class="col-md-12 text-center">
					<hr/>
				</div>						
				<div class="col-md-12 text-center">
					<input type = "submit" name = "add" id="add" value = "Save" class = "btn btn-info btn-flat">
					&nbsp;
					<input type = "submit" name = "cancel" id="cancel" value = "Cancel" class = "btn btn-default btn-flat">
				</div>	
			</div>
			</form>
		</div>
		<!--UPLOAD MODAL-->
		<div class="modal fade" id="uploadModal" tabindex="-1" data-backdrop="static">
			<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
						Please select a file to be uploaded.
						<button type="button" class="close" data-dismiss="modal">&times;</button>
					</div>
					<form action="<?=BASE_URL?>maintenance/company/ajax/upload" onSubmit="return false" method="post" enctype="multipart/form-data" id="uploadForm">
					<div class="modal-body">
						<!-- <div class="form-group field_col">
							<input type="file"  class="form_iput"    value="" name="upload_logo" id="upload_logo" />					<span class="help-block hidden small"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
						</div> -->
						<?php
							echo $ui->formField('file')
								->setSplit('', 'col-md-12')
								->setName('upload_logo')
								->setId('upload_logo')
								->setValidation('required')
								->draw();
						?>
						<br/>
						<p class="help-block">The file to be imported must be in .jpeg, .jpg, .png and .gif file.</p>
						<div id="progressbox" style="display:none;"><div id="progressbar"></div ><div id="statustxt">0%</div></div>
						<div id="output"></div>
					</div>
					<div class="modal-footer">
						<div class="row row-dense">
							<div class="col-md-12 right">
								<input type="submit" class="btn btn-info" value="Upload" name="upload" id="uploadBtn"/>
								<button type="button" class="btn btn-default hidden" data-dismiss="modal" id="uploadOk">Close</button>
									&nbsp;&nbsp;&nbsp;
								<div class="btn-group">
									<button type="button" class="btn btn-default" data-dismiss="modal" id="uploadCancel" onClick="this.form.upload_logo.value = '';">Cancel</button>
								</div>
							</div>
						</div>
					</div>
					</form>
				</div>
			</div>
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
					// $("#companyAlert").removeClass('alert-warning');
					// $("#companyAlert").addClass('alert-info');
					// $("#companyAlert").html('<strong>Success!</strong> Changes has been saved.');
					$('#delay_modal').modal('show');
					setTimeout(function() {							
						window.location =  "<?=MODULE_URL?>";					
					}, 1000)
				}else{
					$("#companyAlert").removeClass('alert-info');
					$("#companyAlert").addClass('alert-warning');
					$("#companyAlert").html('<strong>Error!</strong> '+message);
				}

				$('html, body').animate({ scrollTop: 0 }, 'slow');
				
				// $('#companyAlert').fadeTo(0, 500, function(){
				// 	$(this).removeClass('hidden');
				// });
			
				window.setTimeout(function() { 
					$('#companyAlert').fadeTo(500, 0).slideUp(500, function(){
						$(this).addClass('hidden');
					});
				}, 3000);
			});
		});

		$('#uploadForm').on('change', '#upload_logo', function() {
			var filename = $(this).val().split("\\");
			$(this).closest('.input-group').find('.form-control').html(filename[filename.length - 1]);
		});
		
		$('#companyForm #cancel').click(function(e) {
			e.preventDefault();
			location.href = '<?=BASE_URL?>';
		});

		$('#companyForm #taxyear').change(function(e) {
			if(e.target.value == 'calendar'){
				$('#companyForm #periodstart').prop('disabled',true);
				$('#companyForm #periodstart').val('Jan').trigger('change');
			}else{
				$('#companyForm #periodstart').prop('disabled',false);
			}
		});

		$('#uploadModal').on('hidden.bs.modal',function(){
        	$('#uploadModal').find("input[type=file]").val("");
			
			if($("#uploadModal #uploadCancel.hidden")[0]){
				location.reload();
			}else{
				$("#uploadModal #uploadBtn").removeClass('hidden');
				$("#uploadModal #uploadOk").addClass('hidden');
				$("#uploadModal #uploadCancel").removeClass('hidden');
				$("#uploadModal #output").html('');
			}
			
        });

		/**UPLOAD INVOICE LOGO**/
		var progressbox     = $('#progressbox');
		var progressbar     = $('#progressbar');
		var statustxt       = $('#statustxt');
		var completed       = '0%';
	
		var options = { 
				target:   '#output',   // target element(s) to be updated with server response 
				beforeSubmit:  beforeSubmit,  // pre-submit callback 
				uploadProgress: OnProgress,
				success:       afterSuccess,  // post-submit callback 
				resetForm: true        // reset the form after successful submit 
			}; 
		
		$('#uploadForm').submit(function() { 
			$(this).ajaxSubmit(options);  			
			// return false to prevent standard browser submit and page navigation 
			return false; 
		});

		//when upload progresses	
		function OnProgress(event, position, total, percentComplete)
		{
			//Progress bar
			progressbar.width(percentComplete + '%') //update progressbar percent complete
			statustxt.html(percentComplete + '%'); //update status text
			if(percentComplete>50)
			{
				statustxt.css('color','#fff'); //change status text to white after 50%
			}
		}
		
		//after succesful upload
		function afterSuccess()
		{
			$('#uploadOk').removeClass('hidden'); //show confirm button
			$('#uploadBtn').addClass('hidden'); //hide submit button
			$('#uploadCancel').addClass('hidden'); //hide cancel button
			
			$('#loading-img').hide(); //hide submit button
			location.reload();
		}

		//function to check file size before uploading.
		function beforeSubmit(){
			//check whether browser fully supports all File API
		   if (window.File && window.FileReader && window.FileList && window.Blob)
			{

				if( !$('#upload_logo').val()) //check empty input filed
				{
					$("#output").html("Please select a file to proceed.");
					return false
				}
		
				var fsize = $('#upload_logo')[0].files[0].size; //get file size
				var ftype = $('#upload_logo')[0].files[0].type; // get file type
		
				//allow only valid image file types 
				switch(ftype)
				{
				    case 'image/png': case 'image/gif': case 'image/jpeg': case 'image/pjpeg':
				        break;
				    default:
				        $("#output").html("<b>"+ftype+"</b> Unsupported file type!");
						return false
				}
		
				//Allowed file size is less than 1 MB (1048576)
				if(fsize>1048576) 
				{
					$("#output").html("<b>"+bytesToSize(fsize) +"</b> Too big Image file! <br>Please reduce the size of your photo using an image editor.");
					return false
				}
		
				//Progress bar
				progressbox.show(); //show progressbar
				progressbar.width(completed); //initial value 0% of progressbar
				statustxt.html(completed); //set status text
				statustxt.css('color','#000'); //initial color of status text

				
				//$('#submit-btn').hide(); //hide submit button
				$('#uploadOk').addClass('hidden'); //hide confirm button
				$('#uploadBtn').removeClass('hidden'); //show submit button
				$('#uploadCancel').removeClass('hidden'); //show cancel button
				$('#loading-img').show(); //hide submit button
				$("#output").html("");  
			}
			else
			{
				//Output error to older unsupported browsers that doesn't support HTML5 File API
				$("#output").html("Please upgrade your browser, because your current browser lacks some new features we need!");
				return false;
			}
		}
	});
</script>