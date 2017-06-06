<?php
class modal {

	public function __construct() {
		$this->reset();
	}

	public function reset() {
		$this->id		= '';
		$this->content	= '';
		$this->header	= '';
	}

	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	public function setHeader($header) {
		$this->header = $header;
		return $this;
	}

	public function setContent($content) {
		$this->content = $content;
		return $this;
	}

	public function draw() {
		$modal	= '';
		$modal	= '<div class="modal fade" id="' . $this->id . '" tabindex="-1" role="dialog">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title">' . $this->header . '</h4>
								</div>
								<div class="modal-body p-none">
									
								</div>
							</div>
						</div>
					</div>
					<script>
						$.post("' . BASE_URL . $this->content . '", { modal: "modal" }, function(data) {
							$("#' . $this->id . ' .modal-body").html(data);
						});
					</script>
					';

		return $modal;
	}

}