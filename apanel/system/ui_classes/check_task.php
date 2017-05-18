<?php
class check_task {

	public function __construct() {
		$this->reset();
	}

	public function addView($show = '') {
		$this->view = ($show === '') ? MOD_VIEW : $show;
		return $this;
	}

	public function addEdit($show = '') {
		$this->edit = ($show === '') ? MOD_EDIT : $show;
		return $this;
	}
	public function addPrint($show = '') {
		$this->print = ($show === '') ? MOD_PRINT : $show;
		return $this;
	}

	public function addDelete($show = '') {
		$this->delete = ($show === '') ? MOD_DELETE : $show;
		return $this;
	}

	public function addCheckbox() {
		$this->checkbox = true;
		return $this;
	}

	public function setValue($value) {
		$this->value = $value;
		return $this;
	}
	
	public function addOtherTask($task, $glyphicon, $show = true) {
		$this->addon[] = (object) array(
									'task' => $task,
									'glyphicon' => $glyphicon,
									'show' => $show
								);
		return $this;
	}

	public function draw() {
		$check_task = '';
		$dropdown_top = ($this->edit || $this->view || $this->print);
		$dropdown_addon = (count($this->addon));
		$dropdown = ($dropdown_top || $this->delete || $dropdown_addon);
		if ($this->checkbox || $dropdown > 0) {
			$check_task .= '<div class="btn-group">';
			if ($this->checkbox) {
				$check_task .= '
								<label type="button" class="btn btn-default btn-flat btn-checkbox">
									<input type="checkbox" class="checkbox item_checkbox" value="' . $this->value . '">
								</label>';
			}
			if ($dropdown) {
				$check_task .= '
					<button type="button" class="btn btn-default btn-flat dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<span class="caret"></span>
						<span class="sr-only">Toggle Dropdown</span>
					</button>
					<ul class="dropdown-menu">';

				$check_task .= ($this->view) ? '<li><a class="btn-sm" href="'. MODULE_URL .'view/' . $this->value . '"><i class="glyphicon glyphicon-eye-open"></i> View</a></li>' : '';
				
				$check_task .= ($this->edit) ? '<li><a class="btn-sm" href="'. MODULE_URL .'edit/' . $this->value . '"><i class="glyphicon glyphicon-pencil"></i> Edit</a></li>' : '';
				
				$check_task .= ($this->print) ? '<li><a class="btn-sm" href="'. MODULE_URL .'print_preview/' . $this->value . '"><i class="glyphicon glyphicon-print"></i> Print Voucher</a></li>' : '';

				$check_task .= ($dropdown_top && $dropdown_addon) ? '<li class="divider"></li>' : '';

				foreach ($this->addon as $addon) {
					$class = strtolower(str_replace(' ', '_', $addon->task));
					$check_task .= ($addon->show) ? '<li><a class="btn-sm link ' . $class . '" data-id="' . $this->value . '"><i class="glyphicon glyphicon-' . $addon->glyphicon . '"></i> ' . $addon->task . '</a></li>' : '';
				}
					
				$check_task .= ($this->delete && ($dropdown_top || $dropdown_addon)) ? '<li class="divider"></li>' : '';
				
				$check_task .= ($this->delete) ? '<li><a class="btn-sm delete link" data-id="' . $this->value . '"><i class="glyphicon glyphicon-trash"></i> Delete</a></li>' : '';
					
				$check_task .= '</ul>';
			}

			$check_task .= '</div>';
		}
		$this->reset();
		return $check_task;
	}

	private function reset() {
		$this->checkbox			= false;
		$this->value			= '';
		$this->edit				= false;
		$this->view				= false;
		$this->delete			= false;
		$this->print			= false;
		$this->addon			= array();
	}

}