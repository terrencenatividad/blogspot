<?php
class controller extends wc_controller {

	public function index() {
		$this->dashboard			= new dashboard_model();
		$this->view->title			= ('Dashboard (Sample Data, Edit: Dashboard Model)');
		$data						= array();
		$data['invoices']			= $this->dashboard->getInvoices();
		$data['purchases']			= $this->dashboard->getPurchases();
		$data['billings']			= $this->dashboard->getBillings();
		$data['journalvouchers']	= $this->dashboard->getJournalVouchers();
		$data['revenue_expense']	= $this->dashboard->getRevenuesAndExpenses();
		$data['aging']				= $this->dashboard->getAging();
		$data['sales_purchases']	= $this->dashboard->getSalesAndPurchases();
		$this->view->load('home', $data);
	}

}