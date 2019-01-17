<?php
class controller extends wc_controller 
{
	public function __construct() {
		parent::__construct();
		$this->url 			= new url();
		$this->budgetting 	= new budgetting();
		$this->input        = new input();
		$this->ui 			= new ui();
		$this->logs  		= new log;
		$this->fields 			= array(
			'id',
			'budget_code',
			'budget_center_code',
			'budgetdesc',
			'transactiondate',
			'budget_type',
			'budget_check',
			'owner',
			'prepared_by',
			'approver',
			'approved_by',
			'period_start',
			'period_end',
			'effectivity_date',
			'status'
		);

		$this->budget_details 			= array(
			'id',
			'budget_code',
			'accountcode',
			'description',
			'amount'
		);

		$this->budgetreport = array(
			'budget_code',
			'accountcode',
			'january',
			'february',
			'march',
			'april',
			'may',
			'june',
			'july',
			'august',
			'september',
			'october',
			'november',
			'december'
		);

		$data = array();
	}

	public function listing() {
		$this->view->title  = MODULE_NAME;
		$data['ui'] = $this->ui;
		$this->view->load('budgetting/budget_list',$data);
	}

	public function create() {
		$this->view->title  = 'Create Budget';
		$session			= new session();
		$get = $session->get('login');
		$data 				= $this->input->post($this->fields);
		$data['budget_center'] = $this->budgetting->getBudgetCenter();
		$data['user_list'] = $this->budgetting->getUserList();
		$data['prepared_by'] = $get['username'];
		$data['ui'] 		= $this->ui;
		$data['transactiondate'] = date('M D, Y');
		$data['ajax_task'] 	= 'ajax_create';
		$data['ajax_post'] 	= '';
		$data['show_input'] = true;
		$this->view->load('budgetting/budgetting',$data);
	}

	public function edit($id) {
		$this->view->title  = 'Edit Budget';
		$data = (array) $this->budgetting->getBudgetById($this->fields, $id);
		$data['budget_details'] = $this->budgetting->getBudgetDetails($this->budget_details, $id);
		$data['budget_center'] = $this->budgetting->getBudgetCenter();
		$data['get_accounts'] = $this->budgetting->getAccounts($data['budget_type']);
		$data['transactiondate'] = date('M d, Y', strtotime($data['transactiondate']));
		$data['effectivity_date'] = date('M d, Y', strtotime($data['effectivity_date']));
		$data['ui'] = $this->ui;
		$data['user_list'] = $this->budgetting->getUserList();
		$data["ajax_task"] = "ajax_edit";
		$data['ajax_post'] = "$id";
		$data['show_input'] = true;
		$this->view->load('budgetting/budgetting',$data);
	}

	public function view($id) {
		$this->view->title  = 'View Budget';
		$data = (array) $this->budgetting->getBudgetById($this->fields, $id);
		$data['budget_details'] = $this->budgetting->getBudgetDetails($this->budget_details, $id);
		$data['get_accounts'] = $this->budgetting->getAccounts($data['budget_type']);
		$data['budget_supplement'] = $this->budgetting->getSupplementAccounts($id);
		$data['budget_center'] = $this->budgetting->getBudgetCenter();
		$data["ajax_task"] = "ajax_view";
		$data['user_list'] = $this->budgetting->getUserList();
		$data['ui'] = $this->ui;
		$data['show_input'] = false;
		$this->view->load('budgetting/budgetting',$data);
	}

	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_create() {
		$budget = $this->input->post($this->fields);
		$post = $this->input->post();
		$budget_details = $this->input->post($this->budget_details);
		$budgetreport = $this->input->post($this->budgetreport);
		$this->seq = new seqcontrol();
		$year = date('Y');
		$date = date('Y-m-d', strtotime($budget['transactiondate']));
		$effectivity_date = date('Y-m-d', strtotime($budget['effectivity_date']));
		$budget['transactiondate'] = $date;
		$budget['effectivity_date'] = $effectivity_date;
		$budget['status'] = 'for approval';
		$budget['period_start'] = $year . '-01-01';
		$budget['period_end'] = $year . '-12-31';
		$budget['budget_code'] = $this->seq->getValue('BUD');
		$budget_details['amount'] = str_replace(',', '', $budget_details['amount']);
		$budget_details['budget_code'] = $budget['budget_code'];
		
		$result = $this->budgetting->saveBudget($budget, $budget_details);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function ajax_edit() {
		$id = $this->input->post('id');
		$post = $this->input->post();
		$budget = $this->input->post($this->fields);
		$date = date('Y-m-d', strtotime($budget['transactiondate']));
		$effectivity_date = date('Y-m-d', strtotime($budget['effectivity_date']));
		$year = date('Y');
		$budget['budget_code'] = $budget['budget_code'];
		$budget['status'] = 'for approval';
		$budget['transactiondate'] = $date;
		$budget['effectivity_date'] = $effectivity_date;
		$budget['period_start'] = $year . '-01-01';
		$budget['period_end'] = $year . '-12-31';
		$budget_details = $this->input->post($this->budget_details);
		$budgetcode = $budget['budget_code'];
		$result = $this->budgetting->updateBudget($budget, $id, $budgetcode);
		$budget_details['budget_code'] = $budgetcode;
		$budget_details['id'] = '';
		$budget_details['amount'] = str_replace(',', '' ,$budget_details['amount']);
		$details = $this->budgetting->saveDetails($budget_details);

		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $details
		);
	}

	private function colorStat($stat) {
		$color = 'default';
		switch ($stat) {
			case 'approved':
			$color = 'success';
			break;
			case 'rejected':
			$color = 'danger';
			break; 
			case 'for approval':
			$color = 'info';
			break; 
		}
		return '<span class="label label-' . $color . '">' . strtoupper($stat) . '</span>';
	}

	private function ajax_list() {
		$data	= $this->input->post(array('search', 'sort', 'filter'));
		extract($data);
		$pagination = $this->budgetting->getBudgetListing($this->fields, $sort, $search, $filter);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach($pagination->result as $row) {
			$show_button = ($row->status == 'for approval');
			$show_supplemental = ($row->status == 'approved');
			$dropdown = $this->ui->loadElement('check_task')
			->addView()
			->addEdit($show_button)
			->addOtherTask(
				'Approve',
				'thumbs-up',
				$show_button
			)
			->addOtherTask(
				'Reject',
				'thumbs-down',
				$show_button
			)
			->addOtherTask(
				'Manage Budget Supplements',
				'plus-sign',
				$show_supplemental
			)
			->addDelete($show_button)
			->addCheckbox($show_button)
			->setValue($row->id)
			->draw();
			
			$table .= '<tr>';
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td class = "budgetcode">' . $row->budget_code . '</td>';
			$table .= '<td>' . $row->budgetdesc . '</td>';
			$table .= '<td>' . $row->budget_type . '</td>';
			$table .= '<td class = "budgetcheck">' . $row->budget_check . '</td>';
			$table .= '<td>' . $row->owner . '</td>';
			$table .= '<td>' . $row->prepared_by . '</td>';
			$table .= '<td>' . $row->effectivity_date . '</td>';
			$table .= '<td>' . $this->colorStat($row->status) . '</td>';
			$table .= '</tr>';
		}

		$pagination->table = $table;
		$pagination->csv	= $this->get_export();

		return $pagination;
	}

	private function ajax_get_accounts() {
		$type	= $this->input->post('type');
		$list = $this->budgetting->getAccounts($type);
		
		$ret = '';
		foreach($list as $row) {
			$ind = $row->ind;
			$val = $row->val;
			$ret .= "<option value = ".$ind.">".$val."</option>";
		}

		return array('ret' => $ret);
	}

	public function ajax_delete() {
		$data_var = array(
			'id'
		);
		$data = $this->input->post($data_var);
		extract($data);

		$data_var = array('id');
		$id       = $this->input->post($data_var);

			/**
			* Delete Database
			*/
			$result = $this->budgetting->deleteBudget($id);

			$dataArray = array( "msg" => $result );
			return $dataArray;
		}

		private function ajax_get_details() {
			$itemcode = $this->input->post('itemcode');
			$details = $this->budgetting->getItemDetails($itemcode);
			$name = $details->itemname;
			$desc = $details->itemdesc;
			$uom_base = $details->uom_base;

			return array('itemname' => $name, 'itemdesc' => $desc, 'uom' => $uom_base);
		}

		private function ajax_update_status() {
			$fields = $this->input->post(array('approved_by', 'status'));
			$status = $this->input->post('status');
			$id = $this->input->post('id');
			$get_approver = $this->budgetting->getApprover($id);
			$approver = $get_approver->approver;
			$session			= new session();
			$get = $session->get('login');
			$username = $get['username'];
			$return = false;
			if($username == $approver) {
				$fields['status'] = $status;
				$fields['approved_by'] = $username;
				$result = $this->budgetting->updateBudgetStatus($fields, $id);
			}

			return array('success' => $result);
		}


		private function ajax_get_approver() {
			$code = $this->input->post('budget_code');
			$get_approver = $this->budgetting->getApproverName($code);
			return $get_approver;
		}

		private function ajax_get_budget_accounts() {
			$id = $this->input->post('id');
			$getaccounts = $this->budgetting->getBudgetAccountsOnSupplement($id);
			$ret = '';
			foreach ($getaccounts as $row) {
				$in  = $row->ind;
				$val = $row->val;
				$ret .= "<option value=". $in.">" .$val. "</option>";
			}
			return $ret;
		}

		private function ajax_get_supplements() {
			$id = $this->input->post('id');
			$pagination = $this->budgetting->getBudgetSupplements($id);
			$table = '';
			if (empty($pagination->result)) {
				$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
			}
			foreach($pagination->result as $row) {
				$status = ($row->status == 'for approval');
				$dropdown = $this->ui->loadElement('check_task')
				->addOtherTask(
					'Edit Supplement',
					'pencil',
					$status
				)
				->addOtherTask(
					'Delete Supplement',
					'trash',
					$status
				)
				->addOtherTask(
					'Approve Supplement',
					'thumbs-up',
					$status
				)
				->addOtherTask(
					'Reject Supplement',
					'thumbs-down',
					$status
				)
				->setValue($row->id)
				->draw();
				$dropdown = ($row->status == 'for approval') ? $dropdown : '';

				$table .= '<tr>';
				$table .= '<td align = "center">' . $dropdown . '</td>';
				$table .= '<td>' . $row->accountname . '</td>';
				$table .= '<td>' . $row->description . '</td>';
				$table .= '<td>' . $row->amount . '</td>';
				$table .= '<td>' . $this->colorStat($row->status) . '</td>';
				$table .= '</tr>';
			}
			$pagination->table = $table;
			return $pagination;
		}

		private function ajax_save_supplement() {
			$supplements = $this->input->post();
			$supplements['amount'] = str_replace(',', '', $supplements['amount']);
			$result = $this->budgetting->saveSupplement($supplements);
			return $result;
		}

		private function ajax_delete_supplement() {
			$id = $this->input->post('delete_id');
			$result = $this->budgetting->deleteSupplement($id);
			return $result;
		}

		private function ajax_edit_supplement() {
			$id = $this->input->post('edit_id');
			$result = $this->budgetting->getSupplementValues($id);
			return $result;
		}

		private function ajax_save_edit_supplement() {
			$id = $this->input->post('edit_id');
			$post = $this->input->post();
			$fields['accountcode'] = $post['code_edit'];
			$fields['description'] = $post['description_edit'];
			$fields['amount'] = str_replace(',','',$post['amount_edit']);
			$result = $this->budgetting->updateSupplement($id, $fields);
			return $result;
		}

		private function ajax_update_approve_status_supplement() {
			$id = $this->input->post('budget_id');
			$arr = array('status');
			$fields = $this->input->post($arr);
			$fields['status'] = 'approved';
			$savereport = $this->budgetting->saveBudgetReportSupplement($id);
			$result = $this->budgetting->updateSupplementAppove($id, $fields);
			return $result;
		}

		private function ajax_update_reject_status_supplement() {
			$id = $this->input->post('budget_id');
			$arr = array('status');
			$fields = $this->input->post($arr);
			$fields['status'] = 'rejected';
			$result = $this->budgetting->updateSupplementReject($id, $fields);
			return $result;
		}

		private function get_export() {
			$data	= $this->input->post(array('search', 'sort', 'filter'));
			extract($data);

			$result			= $this->budgetting->getBudgetListingExport($this->fields, $sort, $search, $filter);

			$header = array(
				'Budget Code',
				'Budget Description',
				'Budget Type',
				'Budget Check',
				'Owner',
				'Prepared By',
				'Effectivity Date',
				'Status'
			);

			$csv = '';
			$csv .= '"' . implode('","', $header) . '"';
			if (empty($result)) {
				$csv .= 'No Records Found';
			}
			foreach ($result as $key => $row) {
				$csv .= "\n";
				$csv .= '"' . $row->budget_code . '",';
				$csv .= '"' . $row->budgetdesc . '",';
				$csv .= '"' . $row->budget_type . '",';
				$csv .= '"' . $row->budget_check . '",';
				$csv .= '"' . $row->owner . '",';
				$csv .= '"' . $row->prepared_by . '",';
				$csv .= '"' . $row->effectivity_date . '",';
				$csv .= '"' . $row->status . '",';
			}

			return $csv;
		}

	// activate/deactivate

		// private function ajax_edit_activate() {
		// 	$code = $this->input->post('id');
		// 	$data['status'] = 'active';

		// 	$result = $this->budgetting->updateStat($data,$code);
		// 	return array(
		// 		'redirect'	=> MODULE_URL,
		// 		'success'	=> $result
		// 	);
		// }

		// private function ajax_edit_deactivate() {
		// 	$code = $this->input->post('id');
		// 	$data['status'] = 'inactive';

		// 	$result = $this->budgetting->updateStat($data,$code);
		// 	return array(
		// 		'redirect'	=> MODULE_URL,
		// 		'success'	=> $result
		// 	);
		// }

		// private function update_multiple_deactivate() {
		// 	$posted_data 			=	$this->input->post(array('ids'));

		// 	$data['status'] 			=	'inactive';

		// 	$posted_ids 			=	$posted_data['ids'];
		// 	$id_arr 				=	explode(',',$posted_ids);

		// 	foreach($id_arr as $key => $value)
		// 	{
		// 		$result 			= 	$this->budgetting->updateStat($data, $value);
		// 	}

		// 	if($result)
		// 	{
		// 		$msg = "success";
		// 	} else {
		// 		$msg = "Failed to Update.";
		// 	}

		// 	return $dataArray = array( "msg" => $msg );
		// }

		// private function update_multiple_activate() {
		// 	$posted_data 			=	$this->input->post(array('ids'));

		// 	$data['status'] 			=	'active';

		// 	$posted_ids 			=	$posted_data['ids'];
		// 	$id_arr 				=	explode(',',$posted_ids);

		// 	foreach($id_arr as $key => $value)
		// 	{
		// 		$result 			= 	$this->budgetting->updateStat($data, $value);
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