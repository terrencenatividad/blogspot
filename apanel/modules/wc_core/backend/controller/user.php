<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->user_model		= new user_model();
		$this->session			= new session();
		$this->fields 			= array(
			'username',
			'password',
			'email',
			'stat',
			'is_login',
			'useragent',
			'groupname',
			'firstname',
			'lastname',
			'middleinitial',
			'phone',
			'mobile',
			'image'
		);
		$this->csv_header		= array(
			'First Name',
			'Middle Initial',
			'Last Name',
			'Group Access',
			'Email',
			'Phone Number',
			'Mobile',
			'Username',
			'Password'
		);
		$this->view->header_active = 'maintenance/user/';
	}

	public function listing() {
		$this->view->title = $this->ui->ListLabel('');
		$data['ui'] = $this->ui;
		$all = (object) array('ind' => 'null', 'val' => 'Filter: All');
		$data['group_list'] = array_merge(array($all),  $this->user_model->getGroupList(''));
		$this->view->load('user/user_list', $data);
	}

	public function create() {
		$this->view->title = $this->ui->AddLabel('');
		$data = $this->input->post($this->fields);
		$data['ui'] = $this->ui;
		$data['group_list'] = $this->user_model->getGroupList('');
		$data['ajax_task'] = 'ajax_create';
		$data['ajax_post'] = '';
		$data['show_input'] = true;
		$this->view->load('user/user', $data);
	}

	public function edit($username) {
		$this->view->title = $this->ui->EditLabel('');
		$data = (array) $this->user_model->getUserById($this->fields, $username);
		$data['ui'] = $this->ui;
		$data['group_list'] = $this->user_model->getGroupList('');
		$data['ajax_task'] = 'ajax_edit';
		$data['ajax_post'] = "&username_ref=$username";
		$data['show_input'] = true;
		$this->view->load('user/user', $data);
	}

	public function view($username) {
		$this->view->title = $this->ui->ViewLabel('');
		$data = (array) $this->user_model->getUserById($this->fields, $username);
		$data['ui'] = $this->ui;
		$data['ajax_task'] = 'ajax_view';
		$data['group_list'] = $this->user_model->getGroupList('');
		$data['show_input'] = false;
		$this->view->load('user/user', $data);
	}

	public function get_import() {
		$csv = $this->csv_header();
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
		$data	= $this->input->post(array('search', 'sort'));
		$search	= $data['search'];
		$sort	= $data['sort'];

		$pagination = $this->user_model->getUserPagination($this->fields, $search, $sort);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {

			$stat = $row->stat;
			
			$show_activate 		= ($stat != 'inactive');
			$show_deactivate 	= ($stat != 'active');

			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
			->addView()
			->addEdit()
			->addPrint()
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
			->setValue($row->username)
			->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $row->username . '</td>';
			$table .= '<td>' . $row->firstname . ' ' . $row->lastname . '</td>';
			$table .= '<td>' . $row->email . '</td>';
			$table .= '<td>' . $row->groupname . '</td>';
			$table .= '<td>' . $this->colorStat($row->stat) . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function colorStat($stat) {
		$color = 'default';
		switch ($stat) {
			case 'active':
			$color = 'success';
			break;
			case 'inactive':
			$color = 'warning';
			break;
		}
		return '<span class="label label-' . $color . '">' . strtoupper($stat) . '</span>';
	}

	private function ajax_create() {
		$data = $this->input->post($this->fields);
		$image_uploader = new image_uploader();
		$filename = $image_uploader->setSize(array('large','thumb'))
		->setFolderName('../uploads/items')
		->getImage('item_image');
		$data['image'] = $filename;
		$data['stat'] = 'active';
		$result = $this->user_model->saveUser($data);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function ajax_edit() {
		$image_uploader = new image_uploader();
		$filename = $image_uploader->setSize(array('large','thumb'))
		->setFolderName('../uploads/items')
		->getImage('item_image');
		$data = $this->input->post($this->fields);

		if(empty($filename)) {
			$image_name = $this->input->post('image-edit');
			$data['image'] = $image_name;
		} else {
			$data['image'] = $filename;
		}
		$data['stat'] = 'active';
		$username = $this->input->post('username_ref');
		$result = $this->user_model->updateUser($data, $username);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		$error_id = array();
		if ($delete_id) {
			$error_id = $this->user_model->deleteUsers($delete_id);
		}
		return array(
			'success'	=> (empty($error_id)),
			'error_id'	=> $error_id
		);
	}

	private function ajax_check_username() {
		$username	= $this->input->post('username');
		$reference	= $this->input->post('username_ref');
		$result = $this->user_model->checkUsername($username, $reference);
		return array(
			'available'	=> $result
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
		$invalid	= array();
		$validity	= array();
		if ($csv_array[0] == $this->csv_header) {
			unset($csv_array[0]);

			if (empty($csv_array)) {
				$error = 'No Data Given';
			} else {
				$check_field = array(
					'Username' => array()
				);
				foreach ($csv_array as $row) {
					$check_field['Username'][] = $this->getValueCSV('Username', $row);
					$values[] = array(
						'firstname'		=> $this->getValueCSV('First Name', $row, 'required', $validity),
						'middleinitial'	=> $this->getValueCSV('Middle Initial', $row, 'required', $validity),
						'lastname'		=> $this->getValueCSV('Last Name', $row, 'required', $validity),
						'groupname'		=> $this->getValueCSV('Group Access', $row, 'required', $validity, 'getGroupList', $invalid),
						'email'			=> $this->getValueCSV('Email', $row, 'required', $validity),
						'phone'			=> $this->getValueCSV('Phone Number', $row, 'required', $validity),
						'mobile'		=> $this->getValueCSV('Mobile', $row, 'required', $validity),
						'username'		=> $this->getValueCSV('Username', $row, 'required', $validity),
						'password'		=> $this->getValueCSV('Password', $row, 'required', $validity)
					);
				}
				foreach ($check_field as $key => $row) {
					$data_duplicate = $this->check_duplicate($row);
					if ($data_duplicate) {
						$duplicate[$key]	= array_values($data_duplicate);
					}
				}

				$exist_check = $this->user_model->checkExistingUser($check_field['Username']);
				if ($exist_check) {
					foreach ($exist_check as $row) {
						$exist['Username'][] = $row->username;
					}
				}

				if ($duplicate) {
					$error[] = 'Duplicate Entry'; 
				}
				
				if ($exist) {
					$error[] = 'Entry Already Exist';
				}
				
				if ($invalid) {
					$error[] = 'Invalid Entry';
				}
				
				if ($validity) {
					$error[] = 'Invalid Entry';
				}

				$error = implode('. ', $error);

				if (empty($error)) {
					$result = $this->user_model->saveUserCSV($values);
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
			'invalid'	=> $invalid,
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
			$result = $this->user_model->{$checker_function}($value);
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
		$code = $this->input->post('id');
		$data['stat'] = 'active';

		$result = $this->user_model->updateStat($data,$code);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}
	
	private function ajax_edit_deactivate()
	{
		$code = $this->input->post('id');
		$data['stat'] = 'inactive';

		$result = $this->user_model->updateStat($data,$code);
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
			$result 			= 	$this->user_model->updateStat($data, $value);
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
			$result 			= 	$this->user_model->updateStat($data, $value);
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