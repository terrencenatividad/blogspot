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
			'classid',
			'weight',
			'weight_type',
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
			'Item Name',
			'Item Description',
			'Item Type',
			'Item Class',
			'Item Class Type',
			'Item Class Parent',
			'Weight',
			'Weight Type',
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
		$data['itemclass_list'] = array_merge(array($all),  $this->item_class_model->getParentClass(''));
		$data['itemtype_list'] = array_merge(array($all), $this->item_model->getItemtypeList());
		$this->view->load('item/item_list', $data);
	}

	public function create() {
		$this->view->title = $this->ui->AddLabel('');
		$data = $this->input->post($this->fields);
		$data['ui']							= $this->ui;
		$data['uom_list']					= $this->item_model->getUOMList();
		$data['itemclass_list']				= $this->item_class_model->getParentClass('');
		$data['itemtype_list']				= $this->item_model->getItemtypeList();
		$data['weight_type_list']			= $this->item_model->getWeightTypeList();
		$data['receivable_account_list']	= $this->item_model->getReceivableAccountList();
		$data['revenue_account_list']		= $this->item_model->getRevenueAccountList();
		$data['expense_account_list']		= $this->item_model->getExpenseAccountList();
		$data['payable_account_list']		= $this->item_model->getPayableAccountList();
		$data['revenuetype_list']			= $this->item_model->getRevenueTypeList();
		$data['expensetype_list']			= $this->item_model->getExpenseTypeList();
		$data['chart_account_list']			= $this->item_model->getChartAccountList();
		$data['ajax_task'] = 'ajax_create';
		$data['ajax_post'] = '';
		$data['show_input'] = true;
		$this->view->load('item/item', $data);
	}

	public function edit($itemcode) {
		$this->view->title = $this->ui->EditLabel('');
		$data = (array) $this->item_model->getItemById($this->fields, $itemcode);
		$data['ui']							= $this->ui;
		$data['uom_list']					= $this->item_model->getUOMList();
		$data['itemclass_list']				= $this->item_class_model->getParentClass('');
		$data['itemtype_list']				= $this->item_model->getItemtypeList();
		$data['weight_type_list']			= $this->item_model->getWeightTypeList();
		$data['receivable_account_list']	= $this->item_model->getReceivableAccountList();
		$data['revenue_account_list']		= $this->item_model->getRevenueAccountList();
		$data['expense_account_list']		= $this->item_model->getExpenseAccountList();
		$data['payable_account_list']		= $this->item_model->getPayableAccountList();
		$data['revenuetype_list']			= $this->item_model->getRevenueTypeList();
		$data['expensetype_list']			= $this->item_model->getExpenseTypeList();
		$data['chart_account_list']			= $this->item_model->getChartAccountList();
		$data['ajax_task'] = 'ajax_edit';
		$data['ajax_post'] = "&itemcode_ref=$itemcode";
		$data['show_input'] = true;
		$this->view->load('item/item', $data);
	}

	public function view($itemcode) {
		$this->view->title = $this->ui->ViewLabel('');
		$data = (array) $this->item_model->getItemById($this->fields, $itemcode);
		$data['ui']							= $this->ui;
		$data['uom_list']					= $this->item_model->getUOMList();
		$data['itemclass_list']				= $this->item_class_model->getParentClass('');
		$data['itemtype_list']				= $this->item_model->getItemtypeList();
		$data['weight_type_list']			= $this->item_model->getWeightTypeList();
		$data['receivable_account_list']	= $this->item_model->getReceivableAccountList();
		$data['revenue_account_list']		= $this->item_model->getRevenueAccountList();
		$data['expense_account_list']		= $this->item_model->getExpenseAccountList();
		$data['payable_account_list']		= $this->item_model->getPayableAccountList();
		$data['revenuetype_list']			= $this->item_model->getRevenueTypeList();
		$data['expensetype_list']			= $this->item_model->getExpenseTypeList();
		$data['chart_account_list']			= $this->item_model->getChartAccountList();
		$data['show_input'] = false;
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
			$csv .= "\n";
			$csv .= '"' . $row->itemcode . '",';
			$csv .= '"' . $row->itemname . '",';
			$csv .= '"' . $row->itemdesc . '",';
			$csv .= '"' . $row->item_type . '",';
			$csv .= '"' . $row->item_class . '",';
			$csv .= '"' . (($row->item_class_parent) ? 'Child' : 'Parent') . '",';
			$csv .= '"' . $row->item_class_parent . '",';
			$csv .= '"' . $row->weight . '",';
			$csv .= '"' . $row->weight_type . '",';
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
			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit()
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
			$table .= '</tr>';
		}
		$item->table = $table;
		return $item;
	}

	private function ajax_create() {
		$data = $this->input->post($this->fields);
		$data = $this->cleanData($data);
		$result = $this->item_model->saveItem($data);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function ajax_edit() {
		$data = $this->input->post($this->fields);
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
		$errors		= array();
		if ($csv_array[0] == $this->csv_header) {
			unset($csv_array[0]);

			if (empty($csv_array)) {
				$error = 'No Data Given';
			} else if (count($csv_array) > 2000) {
				$error = 'Too Many Data. Please Upload Maximum of 2000 Rows';
			} else {
				$check_field = array(
					'Item Code' => array()
				);
				foreach ($csv_array as $key => $row) {
					$row['row_num'] = $key + 1;
					$check_field['Item Code'][$row['row_num']] = $this->getValueCSV('Item Code', $row);
					$values[] = array(
						'itemcode'				=> $this->getValueCSV('Item Code', $row, 'alphanum', $errors),
						'itemname'				=> $this->getValueCSV('Item Name', $row, 'required text', $errors),
						'itemdesc'				=> $this->getValueCSV('Item Description', $row, 'required', $errors),
						'typeid'				=> $this->getValueCSV('Item Type', $row, 'required', $errors, 'getItemTypeList'),
						'classid'				=> $this->getValueCSV('Item Class', $row, 'required', $errors, 'getItemClassList', 'Item Class Parent'),
						'weight'				=> $this->getValueCSV('Weight', $row, 'decimal', $errors),
						'weight_type'			=> $this->getValueCSV('Weight Type', $row, '', $errors, 'getWeightTypeList'),
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
							if (in_array(strtolower($value), $duplicate)) {
								$errors[$num_row]['Item Code']['Duplicate Data'] = $value;
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

}