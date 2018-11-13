<?php
class controller extends wc_controller 
{
	public function __construct() {
		parent::__construct();
		$this->url 			= new url();
		$this->bom 	= new bom();
		$this->input        = new input();
		$this->ui 			= new ui();
		$this->logs  		= new log;
		$this->fields 			= array(
			'id',
			'bom_code',
			'bundle_item_code',
			'description',
			'status'
		);

		$this->bomdetails 			= array(
			'id',
			'bom_code_id',
			'item_code',
			'item_name',
			'detailsdesc',
			'percentage',
			'uom'
		);
	}

	public function listing() {
		$this->view->title  = MODULE_NAME;
		$data['ui'] = $this->ui;
		$this->view->load('bom/bom_list',$data);
	}

	public function create() {
		$this->view->title  = 'Add Bill of Materials';
		$data 				= $this->input->post($this->fields);
		$data['ui'] 		= $this->ui;
		$data['ajax_task'] 	= 'ajax_create';
		$data['ajax_post'] 	= '';
		$data['show_input'] = true;
		$this->view->load('bom/bom',$data);
	}

	public function edit($atc_code = "") {
		$this->view->title  = 'Edit Bill of Materials';
		$data["ajax_task"] = "edit";
		$data["task"] = "ajax_edit";
		$data['ui'] = $this->ui;
		$data['sid'] = $atc_code;

		$data["button_name"] = "Save";
		$this->view->load('atc_code/atc_code',$data);
	}

	public function view($atc_code = "") {
		$this->view->title  = $this->ui->ViewLabel('');
		$data["ajax_task"] = "edit";
		$data["ajax_task"] = "view";
		$data["task"] = "ajax_view";
		$data['ui'] = $this->ui;
		$this->view->load('atc_code/atc_code',$data);

	}

	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data	= $this->input->post(array('search', 'sort', 'filter'));
		extract($data);
		$pagination = $this->bom->getBOMListing($this->fields, $sort, $search, $filter);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach($pagination->result as $row) {
			$show_activate 		= ($row->status != 'inactive');
			$show_deactivate 	= ($row->status != 'active');
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
			
			$table .= '<tr>';
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $row->bom_code . '</td>';
			$table .= '<td>' . $row->item_code . '</td>';
			$table .= '<td>' . $row->item_name . '</td>';
			$table .= '<td>' . $row->description . '</td>';
			$table .= '<td>' . $this->colorStat($row->status) . '</td>';
			$table .= '</tr>';
		}

		$pagination->table = $table;
		$pagination->csv = $this->export();

		return $pagination;
	}

	private function export() {
		$data_post = $this->input->post(array("sort", "search"));
		extract($data_post);

		$result = $this->bom->fileExport($this->fields, $sort, $search);

		$header = array("BOM Code","Item Code","Item Name","Description");


		$csv = '';
		$csv .= '"' . implode('","', $header) . '"';
		$csv .= "\n";

		$retrieved 	=	array_filter($result);
		if(!empty($retrieved)){
			foreach ($retrieved as $key => $row){
				$bom_code 			= $row->bom_code;
				$item_code 			= $row->item_code;
				$item_name       	= $row->item_name;
				$description    	 	= $row->description;   	 	

				$csv .= '"' . $bom_code 		. '",';
				$csv .= '"' . $item_code 		. '",';
				$csv .= '"' . $item_name 		. '",';
				$csv .= '"' . $description 		. '",';
				$csv .= "\n";
			}
		}
		return $csv;
	}

	private function ajax_save_import() {
		$csv_array	= array_map('str_getcsv', file($_FILES['file']['tmp_name']));
		$result		= false;
		$duplicate	= array();
		$exist		= array();
		$errors		= array();
		$values		= array();
		$invalid	= array();
		$validity	= array();
		if ($csv_array[0] == $this->csv_header) {
			unset($csv_array[0]);

			if (empty($csv_array)) {
				$errors[] = 'No Data Given';
			} else {
				$check_field = array(
					'Email' => array()
				);
				foreach ($csv_array as $key => $row) {
					$row['row_num'] = $key + 1;
					$check_field['Email'][$row['row_num']] = $this->getValueCSV('Email', $row);
					$values[] = array(
						'username' 				=> $this->generatePassword(9,4),
						'email'					=> $this->getValueCSV('Email', $row, 'required email', $errors),
						'password' 				=> $this->getValueCSV('Password', $row, 'required', $errors),
						'clientcode' 			=> $this->getValueCSV('Client Code', $row, 'required', $errors),
						'firstname' 			=> $this->getValueCSV('First Name', $row, 'required text', $errors),
						'middlename' 			=> $this->getValueCSV('Middle Name', $row, 'required text', $errors),
						'lastname' 				=> $this->getValueCSV('Last Name', $row, 'required text', $errors),
						'civil_status' 			=> $this->getValueCSV('Civil Status', $row, 'required', $errors),
						'birthday' 				=> $this->getValueCSV('Date of Birth (mm/dd/yyyy)', $row, 'required', $errors),
						'home_address' 			=> $this->getValueCSV('Home Address', $row, 'text', $errors),
						'provincial_address' 	=> $this->getValueCSV('Provincial Address', $row, 'text', $errors),
						'company' 				=> $this->getValueCSV('Company', $row, 'required text', $errors),
						'location' 				=> $this->getValueCSV('Location', $row, 'required text', $errors),
						'position' 				=> $this->getValueCSV('Position', $row, 'text', $errors),
						'employee_number' 		=> $this->getValueCSV('Employee/SAP Number', $row, 'text', $errors),
						'date_employment' 		=> $this->getValueCSV('Employment Date (mm/dd/yyyy)', $row, 'required', $errors),
						'contact_number' 		=> $this->getValueCSV('Contact Number', $row, '', $errors)
					);
				}
				foreach ($check_field as $key => $check_row) {
					$data_duplicate = $this->check_duplicate($check_row);
					if ($data_duplicate) {
						$duplicate	= array_values($data_duplicate);
						foreach ($check_row as $num_row => $value) {
							if (in_array(strtolower($value), $duplicate)) {
								$errors[$num_row]['Email']['Duplicate Email'] = $value;
							}
						}
					}
				}
				$exist_check = $this->member_model->checkExistingUser($check_field['Email']);
				if ($exist_check) {
					foreach ($exist_check as $exist_row) {
						foreach ($check_field['Email'] as $num_row => $email) {
							if (strtolower($exist_row->email) == strtolower($email)) {
								$errors[$num_row]['Email']['Already Exist'] = $exist_row->email;
							}
						}
					}
				}

				if (empty($errors)) {
					$result = $this->member_model->saveUserCSV($values);
				}

			}
		} else {
			$errors[] = 'Invalid Import File. Please Use our Template for Uploading CSV';
		}

		$json = array(
			'success'	=> $result,
			'errors'	=> $errors
		);
		return $json;
	}

	// activate/deactivate

	// private function ajax_edit_activate()
	// {
	// 	$code = $this->input->post('id');
	// 	$data['stat'] = 'active';

	// 	$result = $this->atc_code->updateStat($data,$code);
	// 	return array(
	// 		'redirect'	=> MODULE_URL,
	// 		'success'	=> $result
	// 	);
	// }

	// private function ajax_edit_deactivate()
	// {
	// 	$code = $this->input->post('id');
	// 	$data['stat'] = 'inactive';

	// 	$result = $this->atc_code->updateStat($data,$code);
	// 	return array(
	// 		'redirect'	=> MODULE_URL,
	// 		'success'	=> $result
	// 	);
	// }

	// private function update_multiple_deactivate(){
	// 	$posted_data 			=	$this->input->post(array('ids'));

	// 	$data['stat'] 			=	'inactive';

	// 	$posted_ids 			=	$posted_data['ids'];
	// 	$id_arr 				=	explode(',',$posted_ids);

	// 	foreach($id_arr as $key => $value)
	// 	{
	// 		$result 			= 	$this->atc_code->updateStat($data, $value);
	// 	}

	// 	if($result)
	// 	{
	// 		$msg = "success";
	// 	} else {
	// 		$msg = "Failed to Update.";
	// 	}

	// 	return $dataArray = array( "msg" => $msg );
	// }

	// private function update_multiple_activate(){
	// 	$posted_data 			=	$this->input->post(array('ids'));

	// 	$data['stat'] 			=	'active';

	// 	$posted_ids 			=	$posted_data['ids'];
	// 	$id_arr 				=	explode(',',$posted_ids);

	// 	foreach($id_arr as $key => $value)
	// 	{
	// 		$result 			= 	$this->atc_code->updateStat($data, $value);
	// 	}

	// 	if($result)
	// 	{
	// 		$msg = "success";
	// 	} else {
	// 		$msg = "Failed to Update.";
	// 	}

	// 	return $dataArray = array( "msg" => $msg );
	// }
}