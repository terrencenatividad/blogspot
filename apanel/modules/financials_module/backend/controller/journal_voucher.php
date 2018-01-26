<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->jv_model			= new journal_voucher_model();
		$this->session			= new session();
		$this->fields 			= array(
			'voucherno',
			'transactiondate',
			'referenceno',
			'remarks',
			'proformacode',
			'amount'
		);
		$this->fields2			= array(
			'voucherno',
			'accountcode',
			'detailparticulars',
			'debit',
			'credit'
		);
	}

	public function listing() {
		$this->view->title	= 'Journal Voucher List';
		$data['ui']			= $this->ui;
		$this->view->load('journal_voucher/journal_voucher_list', $data);
	}

	public function create() {
		$this->view->title	= 'Journal Voucher Create';
		$data						= $this->input->post($this->fields);
		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
		$data['ui'] = $this->ui;
		$data['proforma_list']		= $this->jv_model->getProformaList();
		$data['chartofaccounts']	= $this->jv_model->getChartOfAccountList();
		$data['voucher_details']	= json_encode(array());
		$data['ajax_task']			= 'ajax_create';
		$data['ajax_post']			= '';
		$data['show_input']			= true;
		$this->view->load('journal_voucher/journal_voucher', $data);
	}

	public function edit($voucherno) {
		$this->view->title			= 'Journal Voucher  Edit';
		$data						= (array) $this->jv_model->getJournalVoucherById($this->fields, $voucherno);
		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
		$data['ui'] = $this->ui;
		$data['proforma_list'] = $this->jv_model->getProformaList();
		$data['chartofaccounts']	= $this->jv_model->getChartOfAccountList();
		$data['voucher_details']	= json_encode($this->jv_model->getJournalVoucherDetails($this->fields2, $voucherno));
		$data['ajax_task']			= 'ajax_edit';
		$data['ajax_post']			= "&voucherno_ref=$voucherno";
		$data['show_input']			= true;
		$this->view->load('journal_voucher/journal_voucher', $data);
	}

	public function view($voucherno) {
		$this->view->title			= 'Journal Voucher View';
		$data						= (array) $this->jv_model->getJournalVoucherById($this->fields, $voucherno);
		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
		$data['ui'] = $this->ui;
		$data['proforma_list']		= $this->jv_model->getProformaList();
		$data['chartofaccounts']	= $this->jv_model->getChartOfAccountList();
		$data['voucher_details']	= json_encode($this->jv_model->getJournalVoucherDetails($this->fields2, $voucherno));
		$data['show_input']			= false;
		$this->view->load('journal_voucher/journal_voucher', $data);
	}

	public function print_preview($voucherno) {
		$documentinfo		= $this->jv_model->getDocumentInfo($voucherno);
		$documentdetails	= $this->jv_model->getDocumentDetails($voucherno);
		$print = new print_voucher_model('P', 'mm', 'Letter');
		$print->setDocumentType('Journal Voucher')
				->setDocumentInfo($documentinfo)
				->setDocumentDetails($documentdetails)
				->drawPDF('jv_voucher_' . $voucherno);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data		= $this->input->post(array('search', 'typeid', 'classid', 'daterangefilter', 'sort'));
		$sort		= $data['sort'];
		$search		= $data['search'];
		$typeid		= $data['typeid'];
		$classid	= $data['classid'];
		$datefilter	= $data['daterangefilter'];

		$pagination	= $this->jv_model->getJournalVoucherPagination($this->fields, $search, $sort, $datefilter);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit()
									->addDelete()
									->addPrint()
									->addCheckbox()
									->setLabels(array('delete' => 'Cancel'))
									->setValue($row->voucherno)
									->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $row->referenceno . '</td>';
			$table .= '<td class="text-right">' . number_format($row->amount, 2) . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_get_proforma() {
		$proformacode	= $this->input->post('proformacode');
		$proforma		= $this->jv_model->getProforma($proformacode);
		return array(
			'proforma' => $proforma
		);
	}

	private function ajax_create() {
		$submit						= $this->input->post('submit');
		$data						= $this->input->post($this->fields);
		$data2						= $this->input->post($this->fields2);
		$seq						= new seqcontrol();
		$data['voucherno']			= $seq->getValue('JV');
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		$result						= $this->jv_model->saveJournalVoucher($data, $data2);
		$redirect_url = MODULE_URL;
		if ($submit == 'save_new') {
			$redirect_url = MODULE_URL . 'create';
		} else if ($submit == 'save_preview') {
			$redirect_url = MODULE_URL . 'view/' . $data['voucherno'];
		}
		return array(
			'redirect'	=> $redirect_url,
			'success'	=> $result
		);
	}

	private function ajax_edit() {
		$data						= $this->input->post($this->fields);
		unset($data['voucherno']);
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		$voucherno					= $this->input->post('voucherno_ref');
		$data2						= $this->input->post($this->fields2);
		$data2['stat']				= 'posted';
		$result						= $this->jv_model->updateJournalVoucher($data, $data2, $voucherno, 'Update');
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		if ($delete_id) {
			$this->jv_model->deleteJournalVouchers($delete_id);
		}
	}

}