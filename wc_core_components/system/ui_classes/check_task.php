<?php
class check_task {

	public function __construct() {
		$this->reset();
	}

	public function addSave($show = '') {
		$this->add = ($show === '') ? MOD_ADD : (MOD_ADD && $show);
		return $this;
	}

	public function addView($show = '') {
		$this->view = ($show === '') ? MOD_VIEW : (MOD_VIEW && $show);
		return $this;
	}

	public function addEdit($show = '') {
		$this->edit = ($show === '') ? MOD_EDIT : (MOD_EDIT && $show);
		return $this;
	}
	public function addPrint($show = '') {
		$this->print = ($show === '') ? MOD_PRINT : (MOD_PRINT && $show);
		return $this;
	}

	public function addCancel() {
		$this->cancel = true;
		return $this;
	}

	public function addDelete($show = '') {
		$this->delete = ($show === '') ? MOD_DELETE : (MOD_DELETE && $show);
		return $this;
	}

	public function addCheckbox($show = true) {
		$this->checkbox = $show;
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
		$check_task			= '';
		$check_task_addon	= '';
		$dropdown_top		= ($this->edit || $this->view || $this->print);
		$dropdown_addon		= (count($this->addon));
		$dropdown			= ($dropdown_top || $this->delete || $dropdown_addon);
		$check_class	= ($this->checkbox && $dropdown) ? 'full_task' : '';
		if ($this->checkbox || $dropdown) {
			$check_task .= '<div class="btn-group check_task ' . $check_class . '">';
			if ($this->checkbox !== '') {
				$check_disabled = ($this->checkbox) ? '' : ' diabled';
				$check_task .= '
								<label type="button" class="btn btn-default btn-flat btn-sm btn-checkbox">
									<input type="checkbox" class="checkbox item_checkbox" value="' . $this->value . '"' . $check_disabled . '>
								</label>';
			}
			if ($dropdown) {
				$check_task .= '
					<button type="button" class="btn btn-default btn-flat btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<span class="caret"></span>
						<span class="sr-only">Toggle Dropdown</span>
					</button>
					<ul class="dropdown-menu">';

				$check_task .= ($this->view) ? '<li><a href="'. MODULE_URL .'view/' . $this->value . '" class="btn-sm"><i class="glyphicon glyphicon-eye-open"></i> View</a></li>' : '';
				
				$check_task .= ($this->edit) ? '<li><a href="'. MODULE_URL .'edit/' . $this->value . '" class="btn-sm"><i class="glyphicon glyphicon-pencil"></i> Edit</a></li>' : '';
				
				$check_task .= ($this->print) ? '<li><a href="'. MODULE_URL .'print_preview/' . $this->value . '" target="_blank" class="btn-sm"><i class="glyphicon glyphicon-print"></i> Print Voucher</a></li>' : '';

				foreach ($this->addon as $addon) {
					$class = strtolower(str_replace(' ', '_', $addon->task));
					$check_task_addon .= ($addon->show) ? '<li><a class="btn-sm link ' . $class . '" data-id="' . $this->value . '"><i class="glyphicon glyphicon-' . $addon->glyphicon . '"></i> ' . $addon->task . '</a></li>' : '';
				}

				if ($check_task_addon) {
					$check_task .= ($dropdown_top && $dropdown_addon) ? '<li class="divider"></li>' : '';
					$check_task .= $check_task_addon;
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

	public function draw_button() {

		$task				= '';
		$task_addon	 		= '';
		$default_button		= ( $this->add || $this->edit || $this->delete || $this->print || $this->cancel || $this->addon );

		if( $default_button )
		{
			if( $this->add )
			{
				$task 	.= 	'<div class="btn-group" id="save_group">
								<button type="button" id="btnSave" class="btn btn-info btn-flat">Save</button>
								<button type="button" id="btnSave_toggle" class="btn btn-info dropdown-toggle btn-flat" data-toggle="dropdown">
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu left" role="menu">
									<li style="cursor:pointer;" id="save_new">
										&nbsp;&nbsp;Save & New
										<input type = "hidden" name = "h_save_new" id = "h_save_new"/>
									</li>
									<li class="divider"></li>
									<li style="cursor:pointer;" id="save_preview">
										&nbsp;&nbsp;Save & Preview
										<input type = "hidden" name = "h_save_preview" id = "h_save_new"/>
									</li>
								</ul>
							</div>';
			}

			if( $this->edit )
			{
				$task 	.= ' <a class="btn btn-primary btn-flat" role="button" href="'. MODULE_URL .'edit/' . $this->value . '" >Edit</a>';
			}

			if( $this->delete )
			{
				$task 	.= ' <button type="button" class="btn btn-danger btn-flat" data-id="'. $this->value .'">Delete</button>';
			}

			if( $this->print )
			{	
				$task 	.= 	' <a class="btn btn-default btn-flat" role="button" href="'. MODULE_URL .'print_preview/' . $this->value . '"><i class="glyphicon glyphicon-print"> Print</a>';
			}

			foreach ($this->addon as $addon) {
				$class = strtolower(str_replace(' ', '_', $addon->task));
				if($addon->show)
				{
					$task_addon 	.=	'<button type="button" class="btn btn-default btn-flat '.$class.'" data-id="'. $this->value .'"><i class="glyphicon glyphicon-' . $addon->glyphicon . '"></i>'.$addon->task.'</button>';
				} 
			}

			if ($task_addon) {
				$task .= $task_addon;
			}

			if( $this->cancel )
			{
				$task 	.= ' <button type="button" class="btn btn-default btn-flat" data-id="'. $this->value .'" id="cancelbtn">Cancel</button>';
			}

			$this->reset();
			return $task;
		}

	}

	private function reset() {
		$this->checkbox			= '';
		$this->value			= '';
		$this->add 				= false;
		$this->edit				= false;
		$this->view				= false;
		$this->delete			= false;
		$this->print			= false;
		$this->cancel 			= false;
		$this->addon			= array();
	}

}