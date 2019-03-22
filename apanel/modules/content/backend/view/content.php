<script>
	$(function () {
		tinymce.init({
			selector: '#mytextarea',
			height: 500,
			theme: 'modern',
			<?php if($ajax_task == 'ajax_view') : ?>
				readonly: 1,
			<?php endif; ?>
			fontsize_formats: "8pt 10pt 11pt 12pt 14pt 18pt 24pt 36pt",
			plugins: [
			'code advlist autolink lists link image charmap print preview hr anchor pagebreak',
			'searchreplace wordcount visualblocks visualchars code fullscreen',
			'insertdatetime media nonbreaking save table contextmenu directionality',
			'emoticons template paste textcolor colorpicker textpattern imagetools image'
			],
			toolbar1: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image code | numlist bullist | fontsizeselect | fontselect',
			toolbar2: 'print preview media | forecolor backcolor emoticons',
			menubar: "insert",
			browser_spellcheck : true,
			contextmenu: false,
			convert_urls: false,
			images_upload_handler: function (blobInfo, success, failure) { 
				var xhr, formData; 
				var json = '';

				xhr = new XMLHttpRequest();
				xhr.withCredentials = false; 
				xhr.open('POST', '<?=MODULE_URL?>ajax/ajax_create_upload'); 

				xhr.onload = function() {

					if (xhr.status != 200) { 
						failure('HTTP Error: ' + xhr.status); 
						return; 
					} 

					json = JSON.parse(xhr.response);
					success(json);
				};

				formData = new FormData(); 
				formData.append('file', blobInfo.blob(), blobInfo.filename());

				xhr.send(formData); 
			},
			setup: function (editor) {
				editor.on('change', function () {
					if(editor.getContent() != '') {
						$('#editor-checker').addClass('hidden');
					}
					editor.save();
				});
			}
		});	
	});

</script>
<section class="content">
	<div class="box box-primary">
		<div class="box-body">
			<form action="" method="post" class="form-horizontal">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-10">
								<?php
								echo $ui->formField('text')
								->setLabel('Content Title')
								->setSplit('col-md-3', 'col-md-8')
								->setName('title')
								->setId('title')
								->setValue($title)
								->setValidation('required')
								->draw($show_input);
								?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<textarea id="mytextarea" name = "mytextarea"><?php echo $content; ?></textarea>
								<span class = "hidden" style = "color: red; font-weight: bold;" id = "editor-checker">Editor Empty</span>
							</div>
						</div>
						<br>
						<div class = "row">
							<div class="text-center">
								<label for="tags">Tags</label>
								<input <?php echo $disabler ?> type="text" data-role="tagsinput" id = "tags" name = "tags" value = "<?php echo $tags; ?>"/>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-md-12 text-center">
								<?php echo $ui->drawSubmit($show_input); ?>
								<a href="<?=MODULE_URL?>" class="btn btn-default" data-toggle="back_page">Cancel</a>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>

<?php if ($show_input): ?>
	<script>
		$('form').submit(function(e) {
			e.preventDefault();
			var editorContent = tinyMCE.get('mytextarea').getContent();
			$(this).find('.form-group').find('input, textarea, select').trigger('blur');
			if ($(this).find('.form-group.has-error').length == 0 && editorContent != '') {
				$.post('<?=MODULE_URL?>ajax/<?=$ajax_task?>', $(this).serialize() + '<?=$ajax_post?>', function(data) {
					if (data.success) {
						$('#delay_modal').modal('show');
						setTimeout(function() {
							window.location = data.redirect;		
						},1000);
					}
				});
			} else if(editorContent == ''){
				$('#editor-checker').removeClass('hidden');
				$('html,body').animate({
					scrollTop: 0
				}, 500);
			} else {
				$(this).find('.form-group.has-error').first().find('input, textarea, select').focus();
			}
		});
	</script>
<?php endif ?>

<script>
	$('#tags').tagsinput({
		allowDuplicates: false
	});
</script>