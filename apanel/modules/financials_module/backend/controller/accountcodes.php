<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->accountcodes		= new accountcodes_model();
		$this->session			= new session();
		$this->fields 			= array(
			'fstaxcodes',
			'shortname',
			'longname',
			'taxtype'
		);
	}

	public function view() {
		$this->view->title		= $this->ui->ViewLabel('');
		$data['ui']				= $this->ui;
		$data['accounts']		= $this->accountcodes->getAccountCodes();
		$data['account_list']	= $this->accountcodes->getChartOfAccountsList();
		$data['acounts_check']	= $this->accountcodes->checkAccounts();
		$data['show_input']		= false;
		$this->view->load('accountcodes/accountcodes', $data);
	}

	public function edit() {
		$this->view->title		= $this->ui->EditLabel('');
		$data['ui']				= $this->ui;
		$data['accounts']		= $this->accountcodes->getAccountCodes();
		$data['account_list']	= $this->accountcodes->getChartOfAccountsList();
		$data['acounts_check']	= $this->accountcodes->checkAccounts();
		$data['ajax_task']		= 'ajax_edit';
		$data['show_input']		= true;
		$this->view->load('accountcodes/accountcodes', $data);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	public function ajax_edit() {
		$data	= $this->input->post(array('fstaxcode', 'salesAccount', 'purchaseAccount'));
		$result	= $this->accountcodes->updateAccountCodes($data);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

}