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
			'total',
			'status'
		);

		$this->budget_details 			= array(
			'id',
			'budget_code',
			'accountcode',
			'description',
			'amount'
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
		$data['ajax_task'] 	= 'ajax_create';
		$data['ajax_post'] 	= '';
		$data['show_input'] = true;
		$this->view->load('budgetting/budgetting',$data);
	}

	public function edit($id) {
		$this->view->title  = 'Edit Budget';
		$data = (array) $this->budgetting->getBudgetById($this->fields, $id);
		$data['budget_center'] = $this->budgetting->getBudgetCenter();
		$data['transactiondate'] = date('m d, Y', strtotime($data['transactiondate']));
		$data['total'] = number_format($data['total'], 2);
		$data["ajax_task"] = "ajax_edit";
		$data['user_list'] = $this->budgetting->getUserList();
		$data['ui'] = $this->ui;
		$data['ajax_post'] = "&id=$id";
		$data['show_input'] = true;
		$this->view->load('budgetting/budgetting',$data);
	}

	public function view($id) {
		$this->view->title  = 'View Budget';
		$data = (array) $this->budgetting->getBudgetById($this->fields, $id);
		$data['budget_center'] = $this->budgetting->getBudgetCenter();
		$data['total'] = number_format($data['total'], 2);
		$data["ajax_task"] = "ajax_view";
		$data['user_list'] = $this->budgetting->getUserList();
		$data['ui'] = $this->ui;
		$data['ajax_post'] = "&id=$id";
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
		$budget_details = $this->input->post($this->budget_details);
		$this->seq = new seqcontrol();
		$year = date('Y');
		$date = date('Y-m-d', strtotime($budget['transactiondate']));
		$budget['transactiondate'] = $date;
		$budget['status'] = 'for approval';
		$budget['period_start'] = $year . '-01-01';
		$budget['period_end'] = $year . '-12-31';
		$budget['effectivity_date'] = $year . '-01-01';
		$budget['budget_code'] = $this->seq->getValue('BUD');
		$budget['total'] = str_replace(',', '', $budget['total']);
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
		$budget = $this->input->post($this->fields);
		$date = date('Y-m-d', strtotime($budget['transactiondate']));
		$year = date('Y');
		$date = date('Y-m-d', strtotime($budget['transactiondate']));
		$budget['status'] = 'for approval';
		$budget['transactiondate'] = $date;
		$budget['period_start'] = $year . '-01-01';
		$budget['period_end'] = $year . '-12-31';
		$budget['effectivity_date'] = $year . '-01-01';
		$budget['total'] = str_replace(',', '', $budget['total']);
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
		$pagination = $this->budgetting->getJobListing($this->fields, $sort, $search, $filter);
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
				'Supplemental',
				'plus-sign',
				$show_supplemental
			)
			->addDelete($show_button)
			->addCheckbox($show_button)
			->setValue($row->id)
			->draw();
			
			$table .= '<tr>';
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $row->budget_code . '</td>';
			$table .= '<td>' . $row->budgetdesc . '</td>';
			$table .= '<td>' . $row->budget_type . '</td>';
			$table .= '<td>' . $row->budget_check . '</td>';
			$table .= '<td>' . $row->owner . '</td>';
			$table .= '<td>' . $row->prepared_by . '</td>';
			$table .= '<td>' . $this->colorStat($row->status) . '</td>';
			$table .= '</tr>';
		}

		$pagination->table = $table;

		return $pagination;
	}

	private function ajax_get_accounts() {
		$type	= $this->input->post('type');
		$list = $this->budgetting->getAccounts($type);
		$table = '';
		foreach($list as $row) {			
			$table .= '<tr>';
			$table .= '<td class = "hidden">' .$this->ui->formField('text')
			->setSplit('col-md-3', 'col-md-12')
			->setName('accountcode[]')
			->setId('accountcode')
			->setClass('hidden')
			->setValue($row->segment5)
			->setAttribute(array('readonly'))
			->draw(true); 
			'</td>';
			$table .= '<td>' .$this->ui->formField('text')
			->setSplit('col-md-3', 'col-md-12')
			->setName('accountname[]')
			->setId('accountname')
			->setValue($row->accountname)
			->setAttribute(array('readonly'))
			->draw(true); 
			'</td>';
			$table .= '<td>' .$this->ui->formField('text')
			->setSplit('col-md-3', 'col-md-12')
			->setName('description[]')
			->setId('description')
			->draw(true); 
			'</td>';
			$table .= '<td>' . 
			$this->ui->formField('text')
			->setSplit('col-md-3', 'col-md-12')
			->setName('amount[]')
			->setId('amount')
			->setClass('text-right')
			->setValidation('integer')
			->draw(true);
			'</td>';
			$table .= '</tr>';
		}

		return array('table' => $table);
	}

	private function ajax_get_accounts_edit() {
		$budgetcode	= $this->input->post('budgetcode');
		$task	= $this->input->post('ajax_task');
		if($task == 'ajax_view') {
		$list = $this->budgetting->getBudgetAccounts($budgetcode);
		} else {
			$list = $this->budgetting->getBudgetAccountsOnEdit($budgetcode);
		}
		$table = '';
		foreach($list as $row) {	
			$show_input = ($task == 'ajax_edit') ? true : false;		
			$table .= '<tr>';
			$table .= '<td class = "hidden">' .$this->ui->formField('text')
			->setSplit('col-md-3', 'col-md-12')
			->setName('accountcode[]')
			->setId('accountcode')
			->setClass('hidden')
			->setValue($row->accountcode)
			->setAttribute(array('readonly'))
			->draw($show_input); 
			'</td>';
			$table .= '<td>' .$this->ui->formField('text')
			->setSplit('col-md-3', 'col-md-12')
			->setName('accountname[]')
			->setId('accountname')
			->setValue($row->accountname)
			->setAttribute(array('readonly'))
			->draw($show_input); 
			'</td>';
			$table .= '<td>' .$this->ui->formField('text')
			->setSplit('col-md-3', 'col-md-12')
			->setName('description[]')
			->setId('description')
			->setValue($row->description)
			->draw($show_input); 
			'</td>';
			$table .= '<td>' . 
			$this->ui->formField('text')
			->setSplit('col-md-3', 'col-md-12')
			->setName('amount[]')
			->setId('amount')
			->setClass('text-right')
			->setValue(number_format($row->amount,2))
			->setValidation('integer')
			->draw($show_input);
			'</td>';
			$table .= '</tr>';
		}

		return array('table' => $table);
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
			if($username == $approver) {
				$fields['status'] = $status;
				$fields['approved_by'] = $username;
				$result = $this->budgetting->updateBudgetStatus($fields, $id);
			} else {
				$result = false;
			}

			return array('success' => $result);
		}


		private function ajax_get_approver() {
			$code = $this->input->post('budget_code');
			$get_approver = $this->budgetting->getApproverName($code);
			return $get_approver;
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