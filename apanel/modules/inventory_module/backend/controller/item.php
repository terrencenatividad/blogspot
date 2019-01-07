<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->item_model		= new item_model();
		$this->item_class_model	= new item_class_model();
		$this->session			= new session();
		$this->fields			= array(
			'itemcode',
			'itemname',
			'itemdesc',
			'typeid',
			'itemgroup',
			'classid',
			'weight',
			'weight_type',
			'barcode',
			'bundle',
			'item_ident_flag',
			'brand' => 'brandcode',
			'replacement_part' => 'replacement',
			'replacement_for'=> 'replacementcode',
			'uom_base',
			'uom_selling',
			'uom_purchasing',
			'selling_conv',
			'purchasing_conv',
			'receivable_account',
			'revenue_account',
			'expense_account',
			'payable_account',
			'inventory_account',
			'revenuetype',
			'expensetype',
		);
		$this->csv_header		= array(
			'Item Code',
			'Barcode',
			'Item Name',
			'Item Description',
			'Item Type',
			'Item Group',
			'Item Class',
			'Item Class Type',
			'Item Class Parent',
			'Weight',
			'Weight Type',
			'Bundle',
			'Serial No.(Y/N)',
			'Engine No.(Y/N)',
			'Chassis No.(Y/N)',
			'Brand Code',
			'Replacement Part (Y/N)',
			'Replacement Code',
			'Base UOM',
			'Purchasing UOM',
			'Converted Purchasing UOM',
			'Selling UOM',
			'Converted Selling UOM',
			'Sales Debit Account',
			'Sales Credit Account',
			'Purchase Debit Account',
			'Purchase Credit Account',
			'Inventory Account',
			'Revenue Type',
			'Expense Type'
		);
		$this->clean_number		= array(
			'weight',
			'selling_conv',
			'purchasing_conv'
		);
	}

	public function listing() {
		$this->view->title = $this->ui->ListLabel('');
		$data['ui'] = $this->ui;
		$all = (object) array('ind' => 'null', 'val' => 'Filter: All');
		$data['itemclass_list']				= $this->item_model->getItemClassList('');
		$data['itemtype_list'] = array_merge(array($all), $this->item_model->getItemtypeList());
		$this->view->load('item/item_list', $data);
	}

	public function create() {
		$this->view->title = $this->ui->AddLabel('');
		$data = $this->input->post($this->fields);
		$data['ui']							= $this->ui;
		$data['uom_list']					= $this->item_model->getUOMList();
		$data['itemclass_list']				= $this->item_model->getItemClassList('');
		$data['groups_list'] 				= $this->item_model->getGroupsList();
		$data['itemtype_list']				= $this->item_model->getItemtypeList();
		$weight = $data['weight_type'];
		$data['weight_type_list']			= $this->item_model->getWeightTypeList($search= '', $weight);
		$data['receivable_account_list']	= $this->item_model->getReceivableAccountList();
		$data['revenue_account_list']		= $this->item_model->getRevenueAccountList();
		$data['expense_account_list']		= $this->item_model->getExpenseAccountList();
		$data['payable_account_list']		= $this->item_model->getPayableAccountList();
		$data['revenuetype_list']			= $this->item_model->getRevenueTypeList();
		$data['expensetype_list']			= $this->item_model->getExpenseTypeList();
		$data['chart_account_list']			= $this->item_model->getChartAccountList();
		$data['existing_item_list'] 		= $this->item_model->getReplacementDropdownList();
		$data['brand_list'] 				= $this->item_model->getBrandDropdownList();
		$data['ajax_task'] 					= 'ajax_create';
		$data['ajax_post'] 					= '';
		$data['show_input'] 				= true;
		$data['serialized'] 				= '0';
		$data['engine'] 					= '0';
		$data['chassis'] 					= '0';
		$this->view->load('item/item', $data);
	}

	public function edit($itemcode) {
		$this->view->title = $this->ui->EditLabel('');
		$data = (array) $this->item_model->getItemById($this->fields, $itemcode);
		$itemtype = $data['typeid'];
		$data['ui']							= $this->ui;
		$result = $this->item_model->getUOMCode($itemcode);
		$base = $result->uom_base;
		$selling = $result->selling;
		$purchasing = $result->purchasing;
		$classid = $data['classid'];
		$weight = $data['weight_type'];
		$replacement = $data['replacementcode'];
		$data['groups_list'] 				= $this->item_model->getGroupsList();
		$data['uom_list']					= $this->item_model->getEditUOMList('', $base, $selling, $purchasing);
		$data['itemclass_list']				= $this->item_model->getEditItemClassList('',$classid);
		$data['itemtype_list']				= $this->item_model->getEditItemtypeList($search = '', $itemtype);
		$data['weight_type_list']			= $this->item_model->getWeightTypeList($search= '', $weight);
		$data['receivable_account_list']	= $this->item_model->getReceivableAccountList();
		$data['revenue_account_list']		= $this->item_model->getRevenueAccountList();
		$data['expense_account_list']		= $this->item_model->getExpenseAccountList();
		$data['payable_account_list']		= $this->item_model->getPayableAccountList();
		$data['revenuetype_list']			= $this->item_model->getRevenueTypeList();
		$data['expensetype_list']			= $this->item_model->getExpenseTypeList();
		$data['chart_account_list']			= $this->item_model->getChartAccountList();
		$data['existing_item_list'] 		= $this->item_model->getEditReplacementDropdownList($itemcode, $replacement);
		$data['brand_list'] 				= $this->item_model->getBrandDropdownList();
		$data['ajax_task'] 					= 'ajax_edit';
		$data['ajax_post'] 					= "&itemcode_ref=$itemcode";
		$data['show_input'] 				= true;
		$data['serialized'] 				= substr($data['item_ident_flag'],0,1);
		$data['engine'] 					= substr($data['item_ident_flag'],1,1);
		$data['chassis'] 					= substr($data['item_ident_flag'],2,1);
		$this->view->load('item/item', $data);
	}

	public function view($itemcode) {
		$this->view->title = $this->ui->ViewLabel('');
		$data = (array) $this->item_model->getItemById($this->fields, $itemcode);
		$itemtype = $data['typeid'];
		$data['ui']							= $this->ui;
		$result = $this->item_model->getUOMCode($itemcode);
		$base = $result->uom_base;
		$selling = $result->selling;
		$purchasing = $result->purchasing;
		$classid = $data['classid'];
		$data['groups_list'] 				= $this->item_model->getGroupsList();
		$data['uom_list']					= $this->item_model->getEditUOMList('', $base, $selling, $purchasing);
		$data['itemclass_list']				= $this->item_model->getEditItemClassList('',$classid);
		$data['itemtype_list']				= $this->item_model->getEditItemtypeList($search = '', $itemtype);	
		$weight = $data['weight_type'];
		$data['weight_type_list']			= $this->item_model->getWeightTypeList($search= '', $weight);
		$data['receivable_account_list']	= $this->item_model->getReceivableAccountList();
		$data['revenue_account_list']		= $this->item_model->getRevenueAccountList();
		$data['expense_account_list']		= $this->item_model->getExpenseAccountList();
		$data['payable_account_list']		= $this->item_model->getPayableAccountList();
		$data['revenuetype_list']			= $this->item_model->getRevenueTypeList();
		$data['expensetype_list']			= $this->item_model->getExpenseTypeList();
		$data['chart_account_list']			= $this->item_model->getChartAccountList();
		$data['existing_item_list'] 		= $this->item_model->getReplacementDropdownList();
		$data['brand_list'] 				= $this->item_model->getBrandDropdownList();
		$data['show_input'] 				= false;
		$data['serialized'] 				= substr($data['item_ident_flag'],0,1);
		$data['engine'] 					= substr($data['item_ident_flag'],1,1);
		$data['chassis'] 					= substr($data['item_ident_flag'],2,1);
		$this->view->load('item/item', $data);
	}

	public function get_import() {
		$csv = $this->csv_header();
		echo $csv;
	}

	public function get_export($search = '', $typeid = '', $classid = '', $sort = '') {
		$search		= base64_decode($search);
		$typeid		= base64_decode($typeid);
		$classid	= base64_decode($classid);
		$sort		= base64_decode($sort);
		$csv		= '';
		$csv		= $this->csv_header();
		$result		= $this->item_model->getItemList($this->fields, $search, $typeid, $classid, $sort);
		
		foreach ($result as $row) {
			$item_ident_flag 	= isset($row->item_ident_flag) ? $row->item_ident_flag : "000";
			$replacement 		= isset($row->replacement) && $row->replacement == '1' ? "Y" : "N";
			$bundle 			= isset($row->bundle) && $row->bundle == '1' ? "Y" : "N";
			$serialized			= (substr($item_ident_flag,0,1) == '1') ? "Y" : "N";
			$engine				= (substr($item_ident_flag,1,1) == '1') ? "Y" : "N";
			$chassis 			= (substr($item_ident_flag,2,1) == '1') ? "Y" : "N";

			$csv .= "\n";
			$csv .= '"' . $row->itemcode . '",';
			$csv .= '"' . $row->barcode . '",';
			$csv .= '"' . $row->itemname . '",';
			$csv .= '"' . $row->itemdesc . '",';
			$csv .= '"' . $row->item_type . '",';
			$csv .= '"' . $row->itemgroup . '",';
			$csv .= '"' . $row->item_class . '",';
			$csv .= '"' . (($row->item_class_parent) ? 'Child' : 'Parent') . '",';
			$csv .= '"' . $row->item_class_parent . '",';
			$csv .= '"' . $row->weight . '",';
			$csv .= '"' . $row->weight_type . '",';
			$csv .= '"' . $bundle . '",';
			$csv .= '"' . $serialized . '",';
			$csv .= '"' . $engine . '",';
			$csv .= '"' . $chassis . '",';
			$csv .= '"' . $row->brandcode . '",';
			$csv .= '"' . $replacement . '",';
			$csv .= '"' . $row->replacementcode . '",';
			$csv .= '"' . $row->base_uom . '",';
			$csv .= '"' . $row->purchasing_uom . '",';
			$csv .= '"' . $row->purchasing_conv . '",';
			$csv .= '"' . $row->selling_uom . '",';
			$csv .= '"' . $row->selling_conv . '",';
			$csv .= '"' . $row->receivable_account . '",';
			$csv .= '"' . $row->revenue_account . '",';
			$csv .= '"' . $row->expense_account . '",';
			$csv .= '"' . $row->payable_account . '",';
			$csv .= '"' . $row->inventory_account . '",';
			$csv .= '"' . $row->revenuetype . '",';
			$csv .= '"' . $row->expensetype . '"';
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
		$data		= $this->input->post(array('search', 'typeid', 'classid', 'sort'));
		$search		= $data['search'];
		$typeid		= $data['typeid'];
		$classid	= $data['classid'];
		$sort		= $data['sort'];

		$item = $this->item_model->getItemListPagination($this->fields, $search, $typeid, $classid, $sort);
		$table = '';
		if (empty($item->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($item->result as $key => $row) {
			$stat = $row->stat;
			if($stat == 'active'){
				$status = '<span class="label label-success">ACTIVE</span>';								
			}else{
				$status = '<span class="label label-warning">INACTIVE</span>';
			}

			$show_activate 		= ($stat != 'inactive');
			$show_deactivate 	= ($stat != 'active');

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
									->setValue($row->itemcode)
									->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $row->itemcode . '</td>';
			$table .= '<td>' . $row->itemname . '</td>';
			$table .= '<td>' . $row->itemclass . '</td>';
			$table .= '<td>' . $row->itemtype . '</td>';
			$table .= '<td>' . $row->weight . '</td>';
			$table .= '<td>' . $status . '</td>';
			$table .= '</tr>';
		}
		$item->table = $table;
		return $item;
	}

	private function ajax_create() {
		$this->fields[] = 'serialized';
		$this->fields[] = 'engine';
		$this->fields[] = 'chassis';

		$data 	= $this->input->post($this->fields);

		$ident 	=	'';
		$ident 	.=	(isset($data['serialized']) && $data['serialized'] == '1') 	?	"1" 	:	"0";
		$ident 	.=	(isset($data['engine']) && $data['engine'] == '1') 		?	"1" 	:	"0";
		$ident 	.=	(isset($data['chassis']) && $data['chassis'] == '1') 	?	"1" 	:	"0";

		unset($data['serialized']);
		unset($data['chassis']);
		unset($data['engine']);

		$data['item_ident_flag'] 	=	$ident; 
		$data = $this->cleanData($data);

		$result = $this->item_model->saveItem($data);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function ajax_edit() {
		$this->fields[] = 'serialized';
		$this->fields[] = 'engine';
		$this->fields[] = 'chassis';
		
		$data = $this->input->post($this->fields);

		$ident 	=	'';
		$ident 	.=	(isset($data['serialized']) && $data['serialized'] == '1') 	?	"1" 	:	"0";
		$ident 	.=	(isset($data['engine']) && $data['engine'] == '1') 		?	"1" 	:	"0";
		$ident 	.=	(isset($data['chassis']) && $data['chassis'] == '1') 	?	"1" 	:	"0";

		unset($data['serialized']);
		unset($data['chassis']);
		unset($data['engine']);

		$data['item_ident_flag'] 	=	$ident; 
		$data = $this->cleanData($data);
		$itemcode = $this->input->post('itemcode_ref');
		$result = $this->item_model->updateItem($data, $itemcode);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		$error_id = array();
		if ($delete_id) {
			$error_id = $this->item_model->deleteItems($delete_id);
		}
		return array(
			'success'	=> (empty($error_id)),
			'error_id'	=> $error_id
		);
	}

	private function ajax_check_itemcode() {
		$itemcode	= $this->input->post('itemcode');
		$reference	= $this->input->post('itemcode_ref');
		$result = $this->item_model->checkItemCode($itemcode, $reference);
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
		$ident 		= "000";
		$errors		= array();
		if ($csv_array[0] == $this->csv_header) {
			unset($csv_array[0]);

			if (empty($csv_array)) {
				$error = 'No Data Given.';
			} else if (count($csv_array) > 2000) {
				$error = 'Too Many Data. Please Upload Maximum of 2000 Rows.';
			} else {
				$check_field = array(
					'Item Code' => array()
				);
				foreach ($csv_array as $key => $row) {
					$row['row_num'] = $key + 1;
					$check_field['Item Code'][$row['row_num']] = $this->getValueCSV('Item Code', $row);
					$bundle 					= $this->getValueCSV('Bundle', $row, '', $errors, '');
					$bundle 					= ($bundle == "Y") 	?	"1" 	:	"0";
					$replacement 				= $this->getValueCSV('Replacement Part (Y/N)', $row, '', $errors, '');
					$replacement 				= ($replacement == "Y") 	?	"1" 	:	"0";
					$serialized 	 			= $this->getValueCSV('Serial No.(Y/N)', $row, '', $errors, '');
					$serialized 				= ($serialized == "Y") 	?	"1" 	:	"0";
					$engine 	 				= $this->getValueCSV('Engine No.(Y/N)', $row, '', $errors, '');
					$engine 					= ($engine == "Y") 	?	"1" 	:	"0";
					$chassis 	 				= $this->getValueCSV('Chassis No.(Y/N)', $row, '', $errors, '');
					$chassis 					= ($chassis == "Y") 	?	"1" 	:	"0";
					$replacement_part 			= $this->getValueCSV('Replacement Code', $row, 'alphanum', $errors, 'getItemDropdownList');

					if($bundle == "0"){
						$ident 					= $serialized.$engine.$chassis;
					} else {
						if($replacement == "1"){
							$errors[$row['row_num']]['Replacement Part (Y/N)']['Invalid Entry'] = "This is a Bundle Item. Unable to set as a Replacement Part.";
						}
						if($serialized == "1"){
							$errors[$row['row_num']]['Serial No.(Y/N)']['Invalid Entry'] = "This is a Bundle Item. Unable to set Serial No.";
						}
						if($engine == "1"){
							$errors[$row['row_num']]['Engine No.(Y/N)']['Invalid Entry'] = "This is a Bundle Item. Unable to set Engine No.";
						}
						if($chassis == "1"){
							$errors[$row['row_num']]['Chassis No.(Y/N)']['Invalid Entry'] = "This is a Bundle Item. Unable to set Chassis No.";
						}
					}

					if($bundle == "0" && $replacement == 1){
						if($replacement_part == ""){
							$errors[$row['row_num']]['Replacement Code']['Missing Entry'] = "The Item is set as a Replacement. Please select its Original Part.";
						} else {
							$check_field['Replacement Code'][$row['row_num']] = $this->getValueCSV('Replacement Code', $row);
						}
					}
					$values[] = array(
						'itemcode'				=> $this->getValueCSV('Item Code', $row, 'alphanum', $errors),
						'barcode'				=> $this->getValueCSV('Barcode', $row, 'alphanum', $errors),
						'itemname'				=> $this->getValueCSV('Item Name', $row, 'required text', $errors),
						'itemdesc'				=> $this->getValueCSV('Item Description', $row, 'required', $errors),
						'typeid'				=> $this->getValueCSV('Item Type', $row, 'required', $errors, 'getItemTypeList'),
						'itemgroup'				=> $this->getValueCSV('Item Group', $row, 'required', $errors, 'getGroupsList'),
						'classid'				=> $this->getValueCSV('Item Class', $row, 'required', $errors, 'getItemClassList', 'Item Class Parent'),
						'weight'				=> $this->getValueCSV('Weight', $row, 'decimal', $errors),
						'weight_type'			=> $this->getValueCSV('Weight Type', $row, '', $errors, 'getWeightTypeList', 'Weight'),
						'bundle' 				=> $bundle,
 						'item_ident_flag'		=> $ident,
						'brandcode'				=> $this->getValueCSV('Brand Code', $row, 'alphanum', $errors, 'getBrandDropdownList'),
						'replacement'			=> $replacement,
						'replacementcode'		=> $this->getValueCSV('Replacement Code', $row, 'alphanum', $errors, 'getReplacementDropdownList'),
						'uom_base'				=> $this->getValueCSV('Base UOM', $row, 'required', $errors, 'getUOMList'),
						'uom_purchasing'		=> $this->getValueCSV('Purchasing UOM', $row, 'required', $errors, 'getUOMList'),
						'purchasing_conv'		=> $this->getValueCSV('Converted Purchasing UOM', $row, 'required integer', $errors),
						'uom_selling'			=> $this->getValueCSV('Selling UOM', $row, 'required', $errors, 'getUOMList'),
						'selling_conv'			=> $this->getValueCSV('Converted Selling UOM', $row, 'required integer', $errors),
						'receivable_account'	=> $this->getValueCSV('Sales Debit Account', $row, '', $errors, 'getReceivableAccountList'),
						'revenue_account'		=> $this->getValueCSV('Sales Credit Account', $row, '', $errors, 'getRevenueAccountList'),
						'expense_account'		=> $this->getValueCSV('Purchase Debit Account', $row, '', $errors, 'getExpenseAccountList'),
						'payable_account'		=> $this->getValueCSV('Purchase Credit Account', $row, '', $errors, 'getPayableAccountList'),
						'inventory_account'		=> $this->getValueCSV('Inventory Account', $row, '', $errors, 'getChartAccountList'),
						'revenuetype'			=> $this->getValueCSV('Revenue Type', $row, '', $errors, 'getRevenueTypeList'),
						'expensetype'			=> $this->getValueCSV('Expense Type', $row, '', $errors, 'getExpenseTypeList')
					);
				}

				foreach ($check_field as $key => $check_row) {
					$data_duplicate = $this->check_duplicate($check_row);
					if ($data_duplicate) {
						$duplicate	= array_values($data_duplicate);
						foreach ($check_row as $num_row => $value) {
							if ($key == "Replacement Code" && $value!=""){
								$errors[$num_row][$key]['Duplicate Data'] = $value;
							} else if ($key == "Item Code" && $value!=""){
								if (in_array(strtolower($value), $duplicate)) {
									$errors[$num_row][$key]['Duplicate Data'] = $value;
								}
							} else {

							}
						}
					}	
				}

				$exist_check = $this->item_model->checkExistingItem($check_field['Item Code']);
				if ($exist_check) {
					foreach ($exist_check as $exist_row) {
						foreach ($check_field['Item Code'] as $num_row => $itemcode) {
							if (strtolower($exist_row->itemcode) == strtolower($itemcode)) {
								$errors[$num_row]['Item Code']['Already Exist'] = $exist_row->itemcode;
							}
						}
					}
				}

				if (empty($errors)) {
					$result = $this->item_model->saveItemCSV($values);
				}
			}
		} else {
			$error = 'Invalid Import File. Please Use our Template for Uploading CSV';
		}

		$json = array(
			'success'	=> $result,
			'errors'	=> $errors,
		);
		return $json;
	}

	private function getValueCSV($field, $array, $checker = '', &$errors = array(), $checker_function = '', $add_args = '') {
		$key	= array_search($field, $this->csv_header);
		$value	= (isset($array[$key])) ? addslashes(implode('', explode("\\", trim(strip_tags($array[$key]))))) : '';
		
		if ($checker != '') {
			$checker_array = explode(' ', $checker);
			if (in_array('integer', $checker_array)) {
				$value = str_replace(',', '', $value);
				if ( ! preg_match('/^[0-9]*$/', $value)) {
					$errors[$array['row_num']][$field]['Not Integer'] = $value;
				}
			}
			if (in_array('decimal', $checker_array)) {
				$value = str_replace(',', '', $value);
				if ( ! preg_match('/^[0-9.]*$/', $value)) {
					$errors[$array['row_num']][$field]['Not Decimal'] = $value;
				}
			}
			if (in_array('alphanum', $checker_array)) {
				$value = str_replace(',', '', $value);
				if ( ! preg_match('/^[a-zA-Z0-9-_]*$/', $value)) {
					$errors[$array['row_num']][$field]['Not Alpha Numeric'] = $value;
				}
			}
			if (in_array('text', $checker_array)) {
				$value = str_replace(',', '', $value);
				if ( ! preg_match('/^[\\a-zA-Z0-9-_ !@#$%^&*()\/<>?,.{}:;=+\r\n"\']*$/', $value)) {
					$errors[$array['row_num']][$field]['Unsupported Character'] = $value;
				}
			}
			if (in_array('required', $checker_array)) {
				if ($value == '') {
					$errors[$array['row_num']][$field]['Required'] = $value;
				}
			}
		}
		if ($checker_function && $value != '') {
			$args = array($value);
			if ($add_args) {
				$key2		= array_search($add_args, $this->csv_header);
				$new_arg	= (isset($array[$key2])) ? trim($array[$key2]) : '';
				$args[]		= $new_arg;
			}
			$result = call_user_func_array(array($this->item_model, $checker_function), $args);
			if ($result) {
				$value = $result[0]->ind;
			} else {
				$errors[$array['row_num']][$field]['Invalid Entry'] = $value;
			}
		}
		return $value;
	}

	private function check_duplicate($array) {
		foreach ($array as $key => $value) {
			$array[$key] = strtolower($value);
		}
		return array_unique(array_diff_assoc($array, array_unique($array)));
	}

	private function ajax_edit_activate()
	{
		$id = $this->input->post('itemcode');
		$data['stat'] = 'active';

		$result = $this->item_model->updateStat($data,$id);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
			);
	}

	private function ajax_edit_deactivate()
	{
		$id = $this->input->post('itemcode');
		$data['stat'] = 'inactive';

		$result = $this->item_model->updateStat($data,$id);
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
			$result 			= 	$this->item_model->updateStat($data, $value);
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
			$result 			= 	$this->item_model->updateStat($data, $value);
		}

		if($result)
		{
			$msg = "success";
		} else {
			$msg = "Failed to Update.";
		}

		return $dataArray = array( "msg" => $msg );
	}

	// private function binaryconvtoamount($binary){
	// 	$amt = 0; 
	// 	$i   = 1;
	// 	$binary_arr = str_split($binary);
	// 	// echo count($binary_arr);
	// 	for($x=count($binary_arr); $x >= 1; $x--){
	// 		echo $x ."<br>";
	// 		if($binary_arr[$x] != 0){
	// 			$amt = $binary_arr[$x] * $i;
	// 		}
	// 		$i++;
	// 	}		
	// 	return $amt;
	// }
}