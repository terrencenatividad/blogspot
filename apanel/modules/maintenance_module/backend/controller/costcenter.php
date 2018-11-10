<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->costcenter		= new costcenter();
		$this->session			= new session();
		$this->fields			= array(
			'id',
			'costcenter_code',
			'name',
			'description',
			'approver',
			'stat'
		);
	}

	public function listing() {
		$this->view->title = $this->ui->ListLabel('');
		$data['ui'] = $this->ui;
		$this->view->load('costcenter/costcenter_list', $data);
	}

	public function create() {
		$this->view->title = $this->ui->AddLabel('');
		$data = $this->input->post($this->fields);
		$data['ui'] = $this->ui;
		$data['coa_list']		= $this->costcenter->getCOA();
		$data['users_list']		= $this->costcenter->getUsers();
		$data['ajax_task'] = 'ajax_create';
		$data['ajax_post'] = '';
		$data['show_input'] = true;
		$this->view->load('costcenter/costcenter', $data);
	}

	public function edit($id) {
		$this->view->title = $this->ui->EditLabel('');
		$data = (array) $this->costcenter->getCostCenterById($this->fields, $id);
		$data['ui'] = $this->ui;
		$data['coa_list']		= $this->costcenter->getCOA();
		$data['users_list']		= $this->costcenter->getUsers();
		$data['ajax_task'] = 'ajax_edit';
		$data['ajax_post'] = "&id=$id";
		$data['show_input'] = true;
		$this->view->load('costcenter/costcenter', $data);
	}

	public function view($id) {
		$this->view->title = $this->ui->ViewLabel('');
		$data = (array) $this->costcenter->getCostCenterById($this->fields, $id);
		$data['coa_list']		= $this->costcenter->getCOA();
		$data['users_list']		= $this->costcenter->getUsers();
		$data['ui'] = $this->ui;
		$data['show_input'] = false;
		$this->view->load('costcenter/costcenter', $data);
	}

	private function get_duplicate(){
		$current = $this->input->post('curr_code');
		$old 	 = $this->input->post('old_code');
		$count 	 = 0;

		if( $current!='' && $current != $old )
		{
			$result = $this->costcenter->check_duplicate($current);

			$count = $result[0]->count;
		}else if(( $current!='' && $current != $old ))
		
		$msg   = "";

		if( $count > 0 )
		{	
			$msg = "exists";
		}else{
			$msg = "donut";
		}

		return $dataArray = array("msg" => $msg);
	}
	
	private function get_duplicate_name(){
		$current = $this->input->post('curr_name');
		$old 	 = $this->input->post('old_name');
		$count 	 = 0;

		if( $current!='' && $current != $old )
		{
			$result = $this->costcenter->check_duplicate_name($current);

			$count = $result[0]->count;
		}else if(( $current!='' && $current != $old ))
		
		$msg   = "";

		if( $count > 0 )
		{	
			$msg = "exists";
		}else{
			$msg = "donut";
		}

		return $dataArray = array("msg" => $msg);
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
	
		$pagination = $this->costcenter->getCostCenterListPagination($this->fields, $search, $sort);
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
			$table .= '<td>' . $row->costcenter_code . '</td>';
			$table .= '<td>' . $row->name . '</td>';
			$table .= '<td>' . $row->description . '</td>';
			$table .= '<td>' . $row->approver . '</td>';
			$table .= '<td>' . $status . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_create() {
		$data = $this->input->post($this->fields);
		$data['stat'] = 'active';
		$result = $this->costcenter->saveCostCenter($data);
		return array(
			'redirect' => MODULE_URL,
			'success' => $result
		);
	}

	private function ajax_edit() {
		$data = $this->input->post($this->fields);
		$data['stat'] = 'active';
		$code = $this->input->post('id');
		$result = $this->costcenter->updateCostCenter($data, $code);
		return array(
			'redirect' => MODULE_URL,
			'success' => $result
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		$error_id = array();
		if ($delete_id) {
			$error_id = $this->costcenter->deleteCostCenter($delete_id);
		}
		return array(
			'success'	=> (empty($error_id)),
			'error_id'	=> $error_id
		);
	}

	private function csv_header() {
		header('Content-type: application/csv');

		$csv = '';
		$csv .= '"' . implode('","', $this->csv_header) . '"';

		return $csv;
	}

	private function ajax_save_import() {
		$csv_array	= array_map('str_getcsv', file($_FILES['file']['tmp_name']));
		$result		= false;
		$duplicate	= array();
		$exist		= array();
		$error		= array();
		$values		= array();
		$validity	= array();
		if ($csv_array[0] == $this->csv_header) {
			unset($csv_array[0]);

			if (empty($csv_array)) {
				$error = 'No Data Given';
			} else {
				$check_field = array(
					'Item Type' => array()
				);
				foreach ($csv_array as $row) {
					$check_field['Item Type'][] = $this->getValueCSV('Item Type', $row);
					$values[] = array(
						'label' => $this->getValueCSV('Item Type', $row, 'required', $validity)
					);
				}
				foreach ($check_field as $key => $row) {
					$data_duplicate = $this->check_duplicate($row);
					if ($data_duplicate) {
						$duplicate[$key]	= array_values($data_duplicate);
					}
				}

				$exist_check = $this->item_type_model->checkExistingItemType($check_field['Item Type']);
				if ($exist_check) {
					foreach ($exist_check as $row) {
						$exist['Item Type'][] = $row->label;
					}
				}

				if ($duplicate) {
					$error[] = 'Duplicate Entry'; 
				}
					
				if ($exist) {
					$error[] = 'Entry Already Exist';
				}
						
				if ($validity) {
					$error[] = 'Invalid Entry';
				}

				$error = implode('. ', $error);

				if (empty($error)) {
					$result = $this->item_type_model->saveItemTypeCSV($values);
				}

			}
		} else {
			$error = 'Invalid Import File. Please Use our Template for Uploading CSV';
		}

		$json = array(
			'success'	=> $result,
			'error'		=> $error,
			'duplicate'	=> $duplicate,
			'exist'		=> $exist,
			'validity'	=> $validity
		);
		return $json;
	}

	private function getValueCSV($field, $array, $checker = '', &$error = array(), $checker_function = '', &$error_function = array()) {
		$key	= array_search($field, $this->csv_header);
		$value	= (isset($array[$key])) ? trim($array[$key]) : '';
		if ($checker != '') {
			$checker_array = explode(' ', $checker);
			if (in_array('integer', $checker_array)) {
				$value = str_replace(',', '', $value);
				if ( ! preg_match('/^[0-9]*$/', $value)) {
					$error['Integer'][$field] = 'Integer';
				}
			}
			if (in_array('decimal', $checker_array)) {
				$value = str_replace(',', '', $value);
				if ( ! preg_match('/^[0-9.]*$/', $value)) {
					$error['Decimal'][$field] = 'Decimal';
				}
			}
			if (in_array('required', $checker_array)) {
				if ($value == '') {
					$error['Required'][$field] = 'Required';
				}
			}
		}
		if ($checker_function && $value != '') {
			$result = $this->item_model->{$checker_function}($value);
			if ($result) {
				$value = $result[0]->ind;
			} else {
				$error_function[$field][] = $value;
				$value = '';
			}
		}
		return $value;
	}

	private function check_duplicate($array) {
		return array_unique(array_diff_assoc($array, array_unique($array)));
	}

	private function ajax_edit_activate()
	{
		$id = $this->input->post('id');
		$data['stat'] = 'active';

		$result = $this->costcenter->updateStat($data,$id);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
			);
	}

	private function ajax_edit_deactivate()
	{
		$id = $this->input->post('id');
		$data['stat'] = 'inactive';

		$result = $this->costcenter->updateStat($data,$id);
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
			$result 			= 	$this->costcenter->updateStat($data, $value);
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
			$result 			= 	$this->costcenter->updateStat($data, $value);
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