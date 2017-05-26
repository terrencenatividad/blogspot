<?php
class dashboard_model extends wc_model {

	public function getInvoices() {
		return '54';
	}

	public function getPurchases() {
		return '80';
	}

	public function getBillings() {
		return '30';
	}

	public function getJournalVouchers() {
		return '34';
	}

	public function getRevenuesAndExpenses() {
		$rae = array(
			'2017' => array(
				array('year' => '2008', 'expense' => '20', 'revenue' => '40'),
				array('year' => '2009', 'expense' => '10', 'revenue' => '40'),
				array('year' => '2010', 'expense' => '51', 'revenue' => '44'),
				array('year' => '2011', 'expense' => '59', 'revenue' => '45'),
				array('year' => '2012', 'expense' => '20', 'revenue' => '46')
			),
			'2016' => array(
				array('year' => '2008', 'expense' => '25', 'revenue' => '40'),
				array('year' => '2009', 'expense' => '15', 'revenue' => '22'),
				array('year' => '2010', 'expense' => '22', 'revenue' => '55'),
				array('year' => '2011', 'expense' => '44', 'revenue' => '11'),
				array('year' => '2012', 'expense' => '20', 'revenue' => '22')
			)
		);
		return json_encode($rae);
	}

	public function getAging() {
		$aging = array(
			'ap' => array(
				array('label' => '1 to 30 days', 'value' => '20000'),
				array('label' => '31 to 60 days', 'value' => '1011'),
				array('label' => '60 days over', 'value' => '5222')
			),
			'ar' => array(
				array('label' => '1 to 30 days', 'value' => '23000'),
				array('label' => '31 to 60 days', 'value' => '1021'),
				array('label' => '60 days over', 'value' => '512')
			)
		);
		return json_encode($aging);
	}

	public function getSalesAndPurchases() {
		$aging = array(
			'sales' => array(
				array('year' => '2008', 'value' => '20'),
				array('year' => '2009', 'value' => '10'),
				array('year' => '2010', 'value' => '5'),
				array('year' => '2011', 'value' => '5'),
				array('year' => '2012', 'value' => '20')
			),
			'purchases' => array(
				array('year' => '2008', 'value' => '220'),
				array('year' => '2009', 'value' => '101'),
				array('year' => '2010', 'value' => '151'),
				array('year' => '2011', 'value' => '252'),
				array('year' => '2012', 'value' => '214')
			)
		);
		return json_encode($aging);
	}

}