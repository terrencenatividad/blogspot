<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->item_class_model	= new item_class_model();
		$this->item_model		= new item_model();
		$this->session			= new session();
		$this->fields 			= array(
			'id',
			'label',
			'parentid',
			'receivable_account',
			'revenue_account',
			'expense_account',
			'payable_account',
			'inventory_account',
			'revenuetype',
			'expensetype',
		);
		$this->csv_header		= array(
			'Item Class',
			'Parent Class',
			'Sales Debit Account',
			'Sales Credit Account',
			'Purchase Debit Account',
			'Purchase Credit Account',
			'Inventory Account',
			'Revenue Type',
			'Expense Type'
		);
	}

	public function listing() {
		$this->view->title = $this->ui->ListLabel('');
		$data['ui'] = $this->ui;
		$this->view->load('item/item_class_list', $data);
	}

	public function create() {
		$this->view->title = $this->ui->AddLabel('');
		$data = $this->input->post($this->fields);
		$data['ui'] = $this->ui;
		$data['parents'] = $this->item_class_model->getParentClass('', true);
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
		$this->view->load('item/item_class', $data);
	}

	public function edit($id) {
		$this->view->title = $this->ui->EditLabel('');
		$data = (array) $this->item_class_model->getItemClassById($this->fields, $id);
		$data['ui'] = $this->ui;
		$data['parents'] = $this->item_class_model->getParentClass($id, true);
		$data['receivable_account_list']	= $this->item_model->getReceivableAccountList();
		$data['revenue_account_list']		= $this->item_model->getRevenueAccountList();
		$data['expense_account_list']		= $this->item_model->getExpenseAccountList();
		$data['payable_account_list']		= $this->item_model->getPayableAccountList();
		$data['revenuetype_list']			= $this->item_model->getRevenueTypeList();
		$data['expensetype_list']			= $this->item_model->getExpenseTypeList();
		$data['chart_account_list']			= $this->item_model->getChartAccountList();
		$data['ajax_task'] = 'ajax_edit';
		$data['ajax_post'] = "&id=$id";
		$data['show_input'] = true;
		$this->view->load('item/item_class', $data);
	}

	public function view($id) {
		$this->view->title = $this->ui->ViewLabel('');
		$data = (array) $this->item_class_model->getItemClassById($this->fields, $id);
		$data['ui'] = $this->ui;
		$data['parents'] = $this->item_class_model->getParentClass($id, true);
		$data['receivable_account_list']	= $this->item_model->getReceivableAccountList();
		$data['revenue_account_list']		= $this->item_model->getRevenueAccountList();
		$data['expense_account_list']		= $this->item_model->getExpenseAccountList();
		$data['payable_account_list']		= $this->item_model->getPayableAccountList();
		$data['revenuetype_list']			= $this->item_model->getRevenueTypeList();
		$data['expensetype_list']			= $this->item_model->getExpenseTypeList();
		$data['chart_account_list']			= $this->item_model->getChartAccountList();
		$data['show_input'] = false;
		$this->view->load('item/item_class', $data);
	}

	public function get_import() {
		$csv = $this->csv_header();
		echo $csv;
	}

	public function get_export($search = '') {
		$search = base64_decode($search);
		$csv = $this->csv_header();
		$list = $this->item_class_model->getItemClassList($this->fields, $search);
		$csv .= $this->createCSVList($list);
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
		$data = $this->input->post(array('search', 'typeid', 'classid','limit'));
		$search = $data['search'];
		$limit = $data['limit'];
		$list = $this->item_class_model->getItemClassList($this->fields, $search, $limit);
		$table = $this->createList($list);
		return array(
			'table' => $table
		);
	}

	private function ajax_create() {
		$data = $this->input->post($this->fields);
		$result = $this->item_class_model->saveItemClass($data);
		return array(
			'redirect' => MODULE_URL,
			'success' => $result
		);
	}

	private function ajax_edit() {
		$data = $this->input->post($this->fields);
		$itemcode = $this->input->post('id');
		$result = $this->item_class_model->updateItemClass($data, $itemcode);
		return array(
			'redirect' => MODULE_URL,
			'success' => $result
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		$error_id = array();
		if ($delete_id) {
			$error_id = $this->item_class_model->deleteItemClass($delete_id);
		}
		return array(
			'success'	=> (empty($error_id)),
			'error_id'	=> $error_id
		);
	}

	private function ajax_check_itemclass() {
		$error_message	= '';
		$label			= $this->input->post('label');
		$parentid		= $this->input->post('parentid');
		$id				= $this->input->post('id');
		$result			= $this->item_class_model->checkItemClass($label, $parentid, $id);
		if ($result) {
			if ($result->parent) {
				$error_message = "Item Class already in {$result->parent}";
			} else {
				$error_message = "Item Class already Exist";
			}
		}
		return array(
			'available'	=> ( ! $result),
			'error_message'	=> $error_message
		);
	}

	private function createList($data, $parent = '', $indent = 1) {
		$table = '';
		if (empty($data)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		
		foreach ($data as $id => $row) {
			$stat = $row['stat'];
			if($stat == 'active'){
				$status = '<span class="label label-success">ACTIVE</span>';								
			}else{
				$status = '<span class="label label-warning">INACTIVE</span>';
			}

			$show_activate 		= ($stat != 'inactive');
			$show_deactivate 	= ($stat != 'active');

			$caret = (isset($row['children'])) ? '<small><a role="button" class="list-caret glyphicon glyphicon-triangle-bottom" data-target=\'[data-parent="' . $row['label'] . '"]\'></a></small>' : '' ;
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
									->setValue($id)
									->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . '<span class="category" style="margin-left:' . $indent * 20 . 'px">' . $caret . $row['label'] . '</span>' . '</td>';
			$table .= '<td data-parent="' . $parent . '">' . $parent . '</td>';
			$table .= '<td data-parent="' . $parent . '">' . $status . '</td>';
			$table .= '</tr>';
			if (isset($row['children'])) {
				$table .= $this->createList($row['children'], $row['label'], $indent + 1);
			}
		}
		return $table;
	}

	private function createCSVList($data, $parent = '') {
		$csv = '';
		foreach ($data as $id => $row) {
			$csv .= "\n";
			$csv .= '"' . $row['label'] . '",';
			$csv .= '"' . $parent . '",';
			$csv .= '"' . $row['receivable_account'] . '",';
			$csv .= '"' . $row['revenue_account'] . '",';
			$csv .= '"' . $row['expense_account'] . '",';
			$csv .= '"' . $row['payable_account'] . '",';
			$csv .= '"' . $row['inventory_account'] . '",';
			$csv .= '"' . $row['revenuetype'] . '",';
			$csv .= '"' . $row['expensetype'] . '"';
			if (isset($row['children'])) {
				$csv .= $this->createCSVList($row['children'], $row['label']);
			}
		}
		return $csv;
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
		$invalid	= array();
		if ($csv_array[0] == $this->csv_header) {
			unset($csv_array[0]);

			if (empty($csv_array)) {
				$error = 'No Data Given';
			} else {
				$check_field = array(
					'Item Class' => array()
				);

				foreach ($csv_array as $row) {
					$check_field['Item Class'][] = $this->getValueCSV('Parent Class', $row) . ' - ' . $this->getValueCSV('Item Class', $row);
					$values[] = array(
						'label'		=> $this->getValueCSV('Item Class', $row, 'required', $validity),
						'parentid'	=> $this->getValueCSV('Parent Class', $row),
						'receivable_account'	=> $this->getValueCSV('Sales Debit Account', $row, 'required', $validity, 'getReceivableAccountList', $invalid),
						'revenue_account'		=> $this->getValueCSV('Sales Credit Account', $row, 'required', $validity, 'getRevenueAccountList', $invalid),
						'expense_account'		=> $this->getValueCSV('Purchase Debit Account', $row, 'required', $validity, 'getExpenseAccountList', $invalid),
						'payable_account'		=> $this->getValueCSV('Purchase Credit Account', $row, 'required', $validity, 'getPayableAccountList', $invalid),
						'inventory_account'		=> $this->getValueCSV('Inventory Account', $row, 'required', $validity, 'getChartAccountList', $invalid),
						'revenuetype'			=> $this->getValueCSV('Revenue Type', $row, '', $validity, 'getRevenueTypeList', $invalid),
						'expensetype'			=> $this->getValueCSV('Expense Type', $row, '', $validity, 'getExpenseTypeList', $invalid)
					);
				}

				foreach ($check_field as $key => $row) {
					$data_duplicate = $this->check_duplicate($row);
					if ($data_duplicate) {
						$duplicate[$key]	= array_values($data_duplicate);
					}
				}

				$exist_check = $this->item_class_model->checkExistingItemClass($check_field['Item Class']);
				if ($exist_check) {
					foreach ($exist_check as $row) {
						$exist['Item Class'][] = (($row->parent) ? $row->parent . ' - ' : '') . $row->label;
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
					$result = $this->item_class_model->saveItemClassCSV($values);
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

		$result = $this->item_class_model->updateStat($data,$id);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
			);
	}

	private function ajax_edit_deactivate()
	{
		$id = $this->input->post('id');
		$data['stat'] = 'inactive';

		$result = $this->item_class_model->updateStat($data,$id);
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
			$result 			= 	$this->item_class_model->updateStat($data, $value);
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