<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->dashboard = new dashboard_model();
	}

	public function index() {
		$this->view->title			= ('Dashboard (Sample Data, Edit: Dashboard Model)');
		$data						= array();
		$data['invoices']			= $this->dashboard->getInvoices();
		$data['purchases']			= $this->dashboard->getPurchases();
		$data['billings']			= $this->dashboard->getBillings();
		$data['journalvouchers']	= $this->dashboard->getJournalVouchers();
		$this->view->load('home', $data);
	}

	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function get_chart() {
		$ajax						= array();
		$ajax['revenue_expense']	= $this->dashboard->getRevenuesAndExpenses();
		$ajax['aging']				= $this->dashboard->getAging();
		$ajax['sales_purchases']	= $this->dashboard->getSalesAndPurchases();

		return $ajax;
	}

}