<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->asset_class		= new asset_class();
		$this->session			= new session();
		$this->fields			= array(
			'id',
			'code',
			'assetclass',
			'depreciate',
			'useful_life',
			'salvage_value',
			'gl_asset',
			'gl_accdep',
			'gl_depexpense',
			'stat'
		);
	}

	public function listing() {
		$this->view->title = $this->ui->ListLabel('');
		$data['ui'] = $this->ui;
		$this->view->load('asset_class/asset_class_list', $data);
	}

	public function create() {
		$this->view->title = $this->ui->AddLabel('');
		$data = $this->input->post($this->fields);
		$data['ui'] = $this->ui;
		$data['coa_list']		= $this->asset_class->getCOA();
		$data['ajax_task'] = 'ajax_create';
		$data['ajax_post'] = '';
		$data['show_input'] = true;
		$this->view->load('asset_class/asset_class', $data);
	}

	public function edit($id) {
		$this->view->title = $this->ui->EditLabel('');
		$data = (array) $this->asset_class->getAssetClassById($this->fields, $id);
		$data['salvage_value'] 		= number_format($data['salvage_value'], 2);
		$data['coa_list']		= $this->asset_class->getCOA();
		$data['ajax_task'] = 'ajax_edit';
		$data['ajax_post'] = "&id=$id";
		$data['ui'] = $this->ui;
		$data['show_input'] = true;
		$this->view->load('asset_class/asset_class', $data);
	}

	public function view($id) {
		$this->view->title = $this->ui->ViewLabel('');
		$data = (array) $this->asset_class->getAssetClassById($this->fields, $id);
		$data['salvage_value'] 		= number_format($data['salvage_value'], 2);
		$data['coa_list']		= $this->asset_class->getCOA();
		$data['ui'] = $this->ui;
		$data['show_input'] = false;
		$this->view->load('asset_class/asset_class', $data);
	}

	private function get_duplicate(){
		$current = $this->input->post('curr_code');
		$old 	 = $this->input->post('old_code');
		$count 	 = 0;

		if( $current!='' && $current != $old )
		{
			$result = $this->asset_class->check_duplicate($current);

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
		
		$pagination = $this->asset_class->getAssetClassListPagination($this->fields, $search, $sort);
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

			$asset = $this->asset_class->getValue("asset_class am", array("segment5","accountname"),"chartaccount ca ON ca.id = am.gl_asset", " am.gl_asset = '$row->gl_asset'", "",false,false);
			$accdep = $this->asset_class->getValue("asset_class am", array("segment5","accountname"),"chartaccount ca ON ca.id = am.gl_accdep", " am.gl_accdep = '$row->gl_accdep'", "",false,false);
			$depexpense = $this->asset_class->getValue("asset_class am", array("segment5","accountname"),"chartaccount ca ON ca.id = am.gl_depexpense", " am.gl_depexpense = '$row->gl_depexpense'", "",false,false);

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
			$table .= '<td>' . $row->code . '</td>';
			$table .= '<td>' . $row->assetclass . '</td>';
			$table .= '<td>' . $asset->segment5. ' - '. $asset->accountname . '</td>';
			$table .= '<td>' . $accdep->segment5. ' - '. $accdep->accountname . '</td>';
			$table .= '<td>' . $depexpense->segment5. ' - '. $depexpense->accountname . '</td>';
			$table .= '<td>' . $status . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_create() {
		$data = $this->input->post($this->fields);
		$data['salvage_value']	= str_replace(',', '', $data['salvage_value']);
		$data['stat'] = 'active';
		$result = $this->asset_class->saveAssetClass($data);
		return array(
			'redirect' => MODULE_URL,
			'success' => $result
		);
	}

	private function ajax_edit() {
		$data = $this->input->post($this->fields);
		$data['salvage_value']	= str_replace(',', '', $data['salvage_value']);
		$data['stat'] = 'active';
		$code = $this->input->post('id');
		$result = $this->asset_class->updateAssetClass($data, $code);
		return array(
			'redirect' => MODULE_URL,
			'success' => $result
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		$error_id = array();
		if ($delete_id) {
			$error_id = $this->asset_class->deleteAssetClass($delete_id);
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

		$result = $this->asset_class->updateStat($data,$id);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
			);
	}

	private function ajax_edit_deactivate()
	{
		$id = $this->input->post('id');
		$data['stat'] = 'inactive';

		$result = $this->asset_class->updateStat($data,$id);
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
			$result 			= 	$this->asset_class->updateStat($data, $value);
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
			$result 			= 	$this->asset_class->updateStat($data, $value);
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