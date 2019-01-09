<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->asset_master		= new asset_master();
		$this->session			= new session();
		$this->fields			= array(
			'id',
			'itemcode',
			'asset_class',
			'asset_name',
			'asset_number',
			'sub_number',
			'serial_number',
			'description',
			'asset_location',
			'department',
			'accountable_person',
			'commissioning_date',
			'retirement_date',
			'useful_life',
			'depreciation_month',
			'depreciation_amount',
			'capitalized_cost',
			'purchase_value',
			'balance_value',
			'salvage_value',
			'frequency_of_dep',
			'number_of_dep',
			'gl_asset',
			'gl_accdep',
			'gl_depexpense',
			'stat'
		);
		$this->fields2			= array(
			'asset_id',
			'itemcode',
			'depreciation_date',
			'accumulated_dep',
			'gl_asset',
			'gl_accdep',
			'gl_depexpense'
		);
	}

	public function listing() {
		$this->view->title = $this->ui->ListLabel('');
		$data['ui'] = $this->ui;
		$this->view->load('asset_master/asset_master_list', $data);
	}

	public function create() {
		$this->view->title = $this->ui->AddLabel('');
		$data = $this->input->post($this->fields);
		$data['ui'] = $this->ui;
		$data['item_list']		= $this->asset_master->getItems();
		$data['assetclass_list']		= $this->asset_master->getAssetClass();
		$data['coa_list']		= $this->asset_master->getCOA();
		$data['commissioning_date']	= $this->date->dateFormat($data['commissioning_date']);
		$data['retirement_date']	= $this->date->dateFormat($data['retirement_date']);
		$data['depreciation_month']	= $this->date->dateFormat($data['depreciation_month']);
		$data['dept_list']			= $this->asset_master->getDepartment();
		$data['ajax_task'] = 'ajax_create';
		$data['ajax_post'] = '';
		$data['show_input'] = true;
		$this->view->load('asset_master/asset_master', $data);
	}

	public function edit($id) {
		$this->view->title = $this->ui->EditLabel('');
		$data = (array) $this->asset_master->getAssetMasterById($this->fields, $id);
		$data['capitalized_cost'] 	= number_format($data['capitalized_cost'], 2);
		$data['purchase_value'] 	= number_format($data['purchase_value'], 2);
		$data['balance_value'] 		= number_format($data['balance_value'], 2);
		$data['salvage_value'] 		= number_format($data['salvage_value'], 2);
		$data['ui'] = $this->ui;
		$data['item_list']		= $this->asset_master->retrieveItems($data['itemcode']);
		$data['assetclass_list']		= $this->asset_master->getAssetClass();
		$data['coa_list']		= $this->asset_master->getCOA();
		$data['commissioning_date']	= $this->date->dateFormat($data['commissioning_date']);
		$data['retirement_date']	= $this->date->dateFormat($data['retirement_date']);
		$data['depreciation_month']	= $this->date->dateFormat($data['depreciation_month']);
		$data['schedule']			= $this->asset_master->getSchedule($data['asset_number']);
		$data['dept_list']			= $this->asset_master->getDepartment();
		$data['ajax_task'] = 'ajax_edit';
		$data['ajax_post'] = "&id=$id";
		$data['show_input'] = true;
		$this->view->load('asset_master/asset_master', $data);
	}

	public function view($id) {
		$this->view->title = $this->ui->ViewLabel('');
		$data = (array) $this->asset_master->getAssetMasterById($this->fields, $id);
		$data['capitalized_cost'] 	= number_format($data['capitalized_cost'], 2);
		$data['purchase_value'] 	= number_format($data['purchase_value'], 2);
		$data['balance_value'] 		= number_format($data['balance_value'], 2);
		$data['salvage_value'] 		= number_format($data['salvage_value'], 2);
		$data['item_list']			= $this->asset_master->getItems();
		$data['assetclass_list']	= $this->asset_master->getAssetClass();
		$data['coa_list']			= $this->asset_master->getCOA();
		$data['commissioning_date']	= date("M d, Y",strtotime($data['commissioning_date']));
		$data['retirement_date']	= date("M d, Y",strtotime($data['retirement_date']));
		$data['depreciation_month']	= date("M d, Y",strtotime($data['depreciation_month']));
		$data['schedule']			= $this->asset_master->getSchedule($data['asset_number']);
		$data['dept_list']			= $this->asset_master->getDepartment();
		$data['ajax_task'] 			= 'view';
		$data['ui'] = $this->ui;
		$data['show_input'] = false;
		$this->view->load('asset_master/asset_master', $data);
	}

	private function get_duplicate(){
		$current = $this->input->post('curr_code');
		$old 	 = $this->input->post('old_code');
		$count 	 = 0;

		if( $current!='' && $current != $old )
		{
			$result = $this->asset_master->check_duplicate($current);

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
	
		$pagination = $this->asset_master->getAssetMasterListPagination($search, $sort);
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
			
			$show_activate 		= ($stat == 'active') && MOD_EDIT;
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
									->addOtherTask(
										'Tag as Retired',
										'bookmark'
									)	
									->addDelete()
									->addCheckbox()
									->setValue($row->id)
									->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $row->asset_name . '</td>';
			$table .= '<td>' . $row->asset_number . '</td>';
			$table .= '<td>' . $row->description . '</td>';
			$table .= '<td>' . $row->name . '</td>';
			$table .= '<td>' . $row->accountable_person . '</td>';
			$table .= '<td>' . $status . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_create() {
		$data = $this->input->post($this->fields);
		$shh  = $this->input->post($this->fields2);
		$data['frequency_of_dep'] = '1';
		$data['stat'] = 'active';
		$asset_number = $data['asset_number'];

		$data['capitalized_cost']	= str_replace(',', '', $data['capitalized_cost']);
		$data['purchase_value']	   = str_replace(',', '', $data['purchase_value']);
		$data['useful_life']	   		= str_replace(',', '', $data['useful_life']);
		$data['salvage_value']	   		= str_replace(',', '', $data['salvage_value']);
		$data['balance_value']	   		= str_replace(',', '', $data['balance_value']);


		$capitalized_cost	   	= $data['capitalized_cost'];
		$purchase_value	   		= $data['purchase_value'];
		$useful_life 			= $data['useful_life'];
		$balance_value 			= $data['balance_value'];
		$salvage_value 			= $data['salvage_value'];
		$time  					= strtotime($data['depreciation_month']);
		$depreciation = 0;
		
		

		$result = $this->asset_master->saveAssetMaster($data);
		// var_dump(($balance_value - $salvage_value) / $useful_life);


		for($x=1;$x<=$data['useful_life'];$x++){
			$depreciation_amount 	= ($balance_value - $salvage_value) / $useful_life;
			$depreciation += ($balance_value - $salvage_value) / $useful_life;
			$final = date("Y-m-d", strtotime("+$x month", $time));
			$sched = $this->asset_master->saveAssetMasterSchedule($shh,$asset_number,$useful_life,$balance_value,$salvage_value,$final,$depreciation,$depreciation_amount);
			
			}

		return array(
			'redirect' => MODULE_URL,
			'success' => $result
		);
	}

	private function ajax_edit() {
		$data = $this->input->post($this->fields);
		$shh  = $this->input->post($this->fields2);
		$code = $this->input->post('id');
		$data['frequency_of_dep'] = '1';
		$data['stat'] = 'active';
		$asset_number = $data['asset_number'];

		$data['capitalized_cost']		= str_replace(',', '', $data['capitalized_cost']);
		$data['purchase_value']	   		= str_replace(',', '', $data['purchase_value']);
		$data['useful_life']	   		= str_replace(',', '', $data['useful_life']);
		$data['salvage_value']	   		= str_replace(',', '', $data['salvage_value']);
		$data['balance_value']	   		= str_replace(',', '', $data['balance_value']);


		$capitalized_cost	   	= $data['capitalized_cost'];
		$purchase_value	   		= $data['purchase_value'];
		$useful_life 			= $data['useful_life'];
		$balance_value 			= $data['balance_value'];
		$salvage_value 			= $data['salvage_value'];
		$time  					= strtotime($data['depreciation_month']);
		$depreciation = 0;
		
		

		$result = $this->asset_master->updateAssetMaster($data, $code, $asset_number);
		// var_dump(($balance_value - $salvage_value) / $useful_life);
		// $result = $this->asset_master->deletesched($asset_number);


		for($x=1;$x<=$data['useful_life'];$x++){
			$depreciation_amount 	= ($balance_value - $salvage_value) / $useful_life;
			$depreciation += ($balance_value - $salvage_value) / $useful_life;
			$final = date("Y-m-d", strtotime("+$x month", $time));
			$sched = $this->asset_master->saveAssetMasterSchedule($shh,$asset_number,$useful_life,$balance_value,$salvage_value,$final,$depreciation,$depreciation_amount);
			
			}
			
		return array(
			'redirect' => MODULE_URL,
			'success' => $result
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		$error_id = array();
		if ($delete_id) {
			$error_id = $this->asset_master->deleteAssetMaster($delete_id);
		}
		return array(
			'success'	=> (empty($error_id)),
			'error_id'	=> $error_id
		);
	}

	private function ajax_view_schedule() {
		$asset_name	   			= $this->input->post('asset_name');
		$depreciation_amount 	= $this->input->post('depreciation_amount');
		$capitalized_cost 		= $this->input->post('capitalized_cost');
		$purchase_value 		= $this->input->post('purchase_value');
		$balance_value 			= $this->input->post('balance_value');
		$salvage_value 			= $this->input->post('salvage_value');
		$useful_life 			= $this->input->post('useful_life');
		$gl_asset 				= $this->input->post('gl_asset');
		$gl_accdep 				= $this->input->post('gl_accdep');
		$gl_depexpense 			= $this->input->post('gl_depexpense');

		$depreciation_amount	= str_replace(',', '', $depreciation_amount);
		$capitalized_cost	   	= str_replace(',', '', $capitalized_cost);
		$purchase_value	   		= str_replace(',', '', $purchase_value);
		$balance_value	   		= str_replace(',', '', $balance_value);
		$salvage_value	   		= str_replace(',', '', $salvage_value);


		$asset = $this->asset_master->retrieveGLAsset($gl_asset);
		$accdep = $this->asset_master->retrieveGLAccdep($gl_accdep);
		$depexpense = $this->asset_master->retrieveGLDepexpense($gl_depexpense);
		$data = $this->input->post($this->fields);

		$depreciation = 0;
		$table = '';   
		$time  = strtotime($data['depreciation_month']);

		for($x=1;$x<=$useful_life;$x++){
		$depreciation += ($balance_value - $salvage_value) / $useful_life;
		$final = date("M d, Y", strtotime("+$x month", $time));
	
		$table .= '<tr>';
		$table .= '<td class="col-md-2 text-center">'.$final.'</td>';
		$table .= '<td class="col-md-3 text-center">'.number_format(($balance_value - $salvage_value) / $useful_life, 2).'</td>';
		$table .= '<td class="col-md-3 text-center">'.number_format($depreciation, 2).'</td>';
		$table .= '<td class="col-md-3 text-center">'.$asset->segment5.' - '.$asset->accountname.'</td>';
		$table .= '<td class="col-md-3 text-center">'.$accdep->segment5.' - '.$accdep->accountname.'</td>';
		$table .= '<td class="col-md-3 text-center">'.$depexpense->segment5.' - '.$depexpense->accountname.'</td>';
		$table .= '</tr>';

		}
		
		return array('table' => $table);
	}

	private function ajax_get_itemcode() {
		$itemcode	   = $this->input->post('itemcode');

		$result = $this->asset_master->retrieveItemCode($itemcode);
		$po  	= $this->asset_master->retrievePO($itemcode);
		$itemname = $result->itemname;
		$itemdesc = $result->itemdesc;
		$barcode = $result->barcode;
		
		$table             = "";
		foreach($po as $row)
			{
				$table .= "<tr style='cursor:pointer' data-id='$row->unitprice'>";
				// $table.= "<td class='text-center'><input type='radio' name='pono' class='form-control pono' value='$row->unitprice'></td>";								
				$table.= "<td class='text-center'>$row->transactiondate</td>";				
				$table.= "<td class='text-center'>$row->source_no</td>";				
				$table.= "<td class='text-center'>$itemcode</td>";				
				$table.= "<td class='text-center'>$row->unitprice</td>";	
				$table.= "</tr>";
			}

		return array('table' => $table, 'description' => $itemdesc, 'itemname' => $itemname, 'barcode' => $barcode);
	}

	private function ajax_get_assetclass() {
		$asset_class	   = $this->input->post('assetclass');

		$result = $this->asset_master->retrieveAssetClass($asset_class);
		$useful_life = $result->useful_life;
		$salvage_value = $result->salvage_value;
		$gl_asset = $result->gl_asset;
		$gl_accdep = $result->gl_accdep;
		$gl_depexpense = $result->gl_depexpense;

		return array('gl_asset' => $gl_asset, 'gl_accdep' => $gl_accdep, 'gl_depexpense' => $gl_depexpense, 'salvage_value' => $salvage_value, 'useful_life' => $useful_life);
	}

	private function check_duplicate(){
		$old 	 = $this->input->post('old');
		$current 	 = $this->input->post('asset_number');
		$count 	 = 0;
		if( $current!='' && $current != $old )
		{
			$result = $this->asset_master->check_duplicate($current);
			$count = $result[0]->count;
		}

		$msg   = "";

		if( $count > 0 )
		{	
			$msg = "exists";
		}

		return $dataArray = array("msg" => $msg);
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

	// private function check_duplicate($array) {
	// 	return array_unique(array_diff_assoc($array, array_unique($array)));
	// }

	private function ajax_edit_activate()
	{
		$id = $this->input->post('id');
		$data['stat'] = 'active';

		$result = $this->asset_master->updateStat($data,$id);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
			);
	}

	private function ajax_edit_deactivate()
	{
		$id = $this->input->post('id');
		$data['stat'] = 'inactive';

		$result = $this->asset_master->updateStat($data,$id);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
			);
	}

	private function update_retirement_date(){
		$id						=	$this->input->post('id');

		$data['retirement_date'] = $this->date->dateDbFormat();
		$data['stat']			 = 'retired';
		
		$result = $this->asset_master->tagRetired($id, $data);

		if($result)
		{
			$msg = "success";
		} else {
			$msg = "Failed to Update.";
		}

		return $dataArray = array( "msg" => $msg );
	}

	private function update_multiple_deactivate(){
		$posted_data 			=	$this->input->post(array('ids'));

		$data['stat'] 			=	'inactive';
		
		$posted_ids 			=	$posted_data['ids'];
		$id_arr 				=	explode(',',$posted_ids);
		
		foreach($id_arr as $key => $value)
		{
			$result 			= 	$this->asset_master->updateStat($data, $value);
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
			$result 			= 	$this->asset_master->updateStat($data, $value);
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