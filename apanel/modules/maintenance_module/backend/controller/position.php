<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->position			= new position();
		$this->session			= new session();
		$this->fields			= array(
			'id',
			'position',
			'description',
			'stat'
		);
	}

	public function listing() {
		$this->view->title = $this->ui->ListLabel('');
		$data['ui'] = $this->ui;
		$this->view->load('position/position_list', $data);
	}

	public function create() {
		$this->view->title = $this->ui->AddLabel('');
		$data = $this->input->post($this->fields);
		$data['ui'] = $this->ui;
		$data['ajax_task'] = 'ajax_create';
		$data['ajax_post'] = '';
		$data['show_input'] = true;
		$this->view->load('position/position', $data);
	}

	public function edit($id) {
		$this->view->title = $this->ui->EditLabel('');
		$data = (array) $this->position->getPositionById($this->fields, $id);
		$data['ui'] = $this->ui;
		$data['ajax_task'] = 'ajax_edit';
		$data['ajax_post'] = "&id=$id";
		$data['show_input'] = true;
		$this->view->load('position/position', $data);
	}

	public function view($id) {
		$this->view->title = $this->ui->ViewLabel('');
		$data = (array) $this->position->getPositionById($this->fields, $id);
		$data['ui'] = $this->ui;
		$data['show_input'] = false;
		$this->view->load('position/position', $data);
	}

	public function get_import() {
		$csv = $this->csv_header();
		echo $csv;
	}

	public function get_export($search = '', $sort = '') {
		$search	= base64_decode($search);
		$sort	= base64_decode($sort);
		$csv	= $this->csv_header();
		$result = $this->item_type_model->getItemTypeList($this->fields, $search, $sort);
		foreach ($result as $row) {
			$csv .= "\n";
			$csv .= '"' . $row->label . '"';
		}
		echo $csv;
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$search	= $this->input->post('search');
		$sort	= $this->input->post('sort');
	
		$pagination = $this->position->getPositionListPagination($this->fields, $search, $sort);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$stat = $row->stat;
			if($stat == 'active'){
				$status = '<span class="label label-success">ACTIVE</span>';								
			}else{
				$status = '<span class="label label-warning">INACTIVE</span>';
			}

			$show_activate 		= ($stat != 'inactive') && MOD_EDIT;
			$show_deactivate 	= ($stat != 'active') && MOD_EDIT;

			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit()
									->addOtherTask(
										'Activate',
										'arrow-up',
										$show_deactivate
									)
									->addOtherTask(
										'Deactivate',
										'arrow-down',
										$show_activate
									)	
									->addDelete()
									->addCheckbox()
									->setValue($row->id)
									->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $row->position . '</td>';
			$table .= '<td>' . $row->description . '</td>';
			$table .= '<td>' . $status . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_create() {
		$data = $this->input->post($this->fields);
		$data['stat'] = 'active';
		$result = $this->position->savePosition($data);
		return array(
			'redirect' => MODULE_URL,
			'success' => $result
		);
	}

	private function ajax_edit() {
		$data = $this->input->post($this->fields);
		$data['stat'] = 'active';
		$code = $this->input->post('id');
		$result = $this->position->updatePosition($data, $code);
		return array(
			'redirect' => MODULE_URL,
			'success' => $result
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		$error_id = array();
		if ($delete_id) {
			$error_id = $this->position->deletePosition($delete_id);
		}
		return array(
			'success'	=> (empty($error_id)),
			'error_id'	=> $error_id
		);
	}

	private function check_duplicate($array) {
		return array_unique(array_diff_assoc($array, array_unique($array)));
	}

	private function ajax_edit_activate()
	{
		$id = $this->input->post('id');
		$data['stat'] = 'active';

		$result = $this->position->updateStat($data,$id);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
			);
	}

	private function ajax_edit_deactivate()
	{
		$id = $this->input->post('id');
		$data['stat'] = 'inactive';

		$result = $this->position->updateStat($data,$id);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
			);
	}

	private function update_multiple_deactivate(){
		$posted_data 			=	$this->input->post(array('ids'));

		$data['stat'] 			=	'inactive';
		
		$posted_ids 			=	$posted_data['ids'];
		$id_arr 				=	explode(',',$posted_ids);
		
		foreach($id_arr as $key => $value)
		{
			$result 			= 	$this->position->updateStat($data, $value);
		}

		if($result)
		{
			$msg = "success";
		} else {
			$msg = "Failed to Update.";
		}

		return $dataArray = array( "msg" => $msg );
	}

	private function update_multiple_activate(){
		$posted_data 			=	$this->input->post(array('ids'));

		$data['stat'] 			=	'active';
		
		$posted_ids 			=	$posted_data['ids'];
		$id_arr 				=	explode(',',$posted_ids);
		
		foreach($id_arr as $key => $value)
		{
			$result 			= 	$this->position->updateStat($data, $value);
		}

		if($result)
		{
			$msg = "success";
		} else {
			$msg = "Failed to Update.";
		}

		return $dataArray = array( "msg" => $msg );
	}

}