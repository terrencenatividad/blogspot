<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui					= new ui();
		$this->input				= new input();
		$this->seq 					= new seqcontrol();
		$this->credit_voucher_model	= new credit_voucher_model();
		$this->session				= new session();
		$this->log 					= new log();
		$this->fields 				= array(
			'transactiondate',
			'voucherno',
			'partnername',
            'invoiceno',
			'referenceno',
			'amount',
			'receivableno',
			'source',
			'c.stat stat'
		);
	}

	public function listing() {
		$this->view->title	= 'Credit Voucher';
		$data['ui']				= $this->ui;
		$data['show_input']     = true;
		$this->view->load('credit_voucher/credit_voucher_list', $data);
	}

	public function create() {
		$this->view->title			= 'Create Credit Voucher';
		$data						= $this->input->post($this->fields);
		$data['customer_list']		= $this->credit_voucher_model->getCustomerList();
		$data['transactiondate'] 	= $this->date->dateFormat($data['transactiondate']);
		$data['ui']                 = $this->ui;
		$data['ajax_task']			= 'ajax_create';
		$data['ajax_post']			= '';
		$data['show_input']			= true;
		$this->view->load('credit_voucher/credit_voucher', $data);
	}

	public function view($voucherno) {
		$this->view->title			= 'View Credit Voucher';
		$data						= (array) $this->credit_voucher_model->getCVById($voucherno);
		$balance					= $this->credit_voucher_model->getApplied($data['voucherno']);
		$data['applied']			= $balance->amount;
		$data['customer_list']		= $this->credit_voucher_model->getCustomerList();
		$data['partnername']		= $data['partner'];
		$data['transactiondate'] 	= $this->date->dateFormat($data['transactiondate']);
		$data['ui']                 = $this->ui;
		$data['ajax_task']			= 'ajax_view';	
		$data['show_input']			= false;
		$this->view->load('credit_voucher/credit_voucher', $data);
	}

	public function edit($voucherno) {
		$this->view->title			= 'Edit Credit Voucher';
		$data						= (array) $this->credit_voucher_model->getCVById($voucherno);
		$data['customer_list']		= $this->credit_voucher_model->getCustomerList();
		$data['partnername']		= $data['partner'];
		$data['transactiondate'] 	= $this->date->dateFormat($data['transactiondate']);
		$data['ui']                 = $this->ui;
		$data['ajax_task']			= 'ajax_edit';	
		$data['ajax_post']			= "&voucherno=$voucherno";
		$data['show_input']			= true;
		$this->view->load('credit_voucher/credit_voucher', $data);
	}

	public function print_preview($voucherno) 
	{
		
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data		= $this->input->post(array('search', 'typeid', 'classid', 'daterangefilter', 'sort','source','filter'));
		$sort		= $data['sort'];
		$search		= $data['search'];
		$typeid		= $data['typeid'];
		$classid	= $data['classid'];
		$datefilter	= $data['daterangefilter'];
		$source 	= $data['source'];
		$filter 	= $data['filter'];

		$pagination	= $this->credit_voucher_model->getCreditVoucherPagination($this->fields, $search, $sort, $datefilter, $source, $filter);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$getApplied = $this->credit_voucher_model->getApplied($row->voucherno);
			$balance = $row->amount - $getApplied->amount;
			$dropdown = $this->ui->loadElement('check_task')
									->addView() 
									->addEdit($row->source != 'RV' && $row->stat != 'inactive' && $balance == $row->amount)
									->addDelete($row->source != 'RV' && $row->stat != 'inactive' && $balance == $row->amount)
									->addPrint()
									->addCheckbox($row->source != 'RV' && $row->stat != 'inactive' && $balance == $row->amount)
									->setLabels(array('delete' => 'Cancel'))
									->setValue($row->voucherno)
                                    ->draw();
            $table .= '<tr>';
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $row->partnername . '</td>';
			$table .= '<td>' . $row->invoiceno . '</td>';
			$table .= '<td>' . $row->referenceno . '</td>';
			$table .= '<td align = "right">' . number_format($balance, 2) . '</td>';
			$table .= '<td align = "right">' . number_format($row->amount, 2) . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_create() {
		$submit						= $this->input->post('submit');
		$data['transactiondate']	= $this->input->post('transactiondate');
		$data['partner']			= $this->input->post('customer');
		$data['invoiceno']			= $this->input->post('invoiceno');
		$data['referenceno']		= $this->input->post('referenceno');
		$data['amount']				= $this->input->post('amount');
		$data['receivableno']		= $this->input->post('receivableno');
		$data['balance']			= $data['amount'];
		$seq						= new seqcontrol();
		$data['voucherno']			= $seq->getValue('CV');
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
        $result						= $this->credit_voucher_model->saveCreditVoucher($data);

		$redirect_url = MODULE_URL;
		if ($submit == 'save_new') {
			$redirect_url = MODULE_URL . 'create';
		} else if ($submit == 'save') {
			$redirect_url = MODULE_URL . 'view/' . $data['voucherno'];
		} else if ($submit == 'save_exit') {
			$redirect_url = MODULE_URL;
		}
		return array(
			'redirect'	=> $redirect_url,
			'success'	=> $result
		);
	}

	private function ajax_edit() {
		$voucherno					= $this->input->post('voucherno');
		$data['transactiondate']	= $this->input->post('transactiondate');
		$data['partner']			= $this->input->post('customer');
		$data['invoiceno']			= $this->input->post('invoiceno');
		$data['referenceno']		= $this->input->post('referenceno');
		$data['amount']				= $this->input->post('amount');
		$data['receivableno']		= $this->input->post('receivableno');
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		$result						= $this->credit_voucher_model->updateCreditVoucher($data, $voucherno);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function ajax_load_ar_list() {
		$customer	= $this->input->post('customer');
		$search		= $this->input->post('search');
		$pagination	= $this->credit_voucher_model->getAccountsReceivablePagination($customer, $search);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr data-id="' . $row->voucherno . '" data-invno="' . $row->invoiceno . '">';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->amount, 2) . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		if ($delete_id) {
			$this->credit_voucher_model->deleteCreditVoucher($delete_id);
		}
	}


}