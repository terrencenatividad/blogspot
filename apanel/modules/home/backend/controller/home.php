<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->dashboard = new dashboard_model();
		$this->report_model			= $this->checkoutModel('reports_module/report_model');
		$this->report_model->generateBalanceTable();
		$this->report_model->generateSalesReportsTable();
		$this->report_model->generatePurchaseReportsTable();
	}

	public function index() {
		$this->view->title			= ('Dashboard');
		$data						= array();
		$data['year']				= $this->dashboard->getYear();
		$data['invoices']			= $this->dashboard->getInvoices();
		$data['purchases']			= $this->dashboard->getPurchases();
		$data['items']				= $this->dashboard->getItems();
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
		$ajax							= array();
		$ajax['revenue_expense']		= $this->dashboard->getRevenuesAndExpenses();
		$ajax['aging']					= $this->dashboard->getAging();
		$ajax['sales_purchases']		= $this->dashboard->getSalesAndPurchases();
		$ajax['sales']					= $this->dashboard->getSales();
		
		return $ajax;
	}

}