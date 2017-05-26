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
				array('month' => '2017-01', 'expense' => '20', 'revenue' => '66'),
				array('month' => '2017-02', 'expense' => '10', 'revenue' => '40'),
				array('month' => '2017-03', 'expense' => '51', 'revenue' => '42'),
				array('month' => '2017-04', 'expense' => '59', 'revenue' => '45'),
				array('month' => '2017-05', 'expense' => '20', 'revenue' => '46'),
				array('month' => '2017-06', 'expense' => '20', 'revenue' => '40'),
				array('month' => '2017-07', 'expense' => '10', 'revenue' => '15'),
				array('month' => '2017-08', 'expense' => '51', 'revenue' => '23'),
				array('month' => '2017-09', 'expense' => '59', 'revenue' => '45'),
				array('month' => '2017-10', 'expense' => '20', 'revenue' => '46'),
				array('month' => '2017-11', 'expense' => '59', 'revenue' => '33'),
				array('month' => '2017-12', 'expense' => '20', 'revenue' => '46')
			),
			'2016' => array(
				array('month' => '2016-01', 'expense' => '20', 'revenue' => '40'),
				array('month' => '2016-02', 'expense' => '10', 'revenue' => '22'),
				array('month' => '2016-03', 'expense' => '51', 'revenue' => '11'),
				array('month' => '2016-04', 'expense' => '59', 'revenue' => '45'),
				array('month' => '2016-05', 'expense' => '20', 'revenue' => '46'),
				array('month' => '2016-06', 'expense' => '20', 'revenue' => '40'),
				array('month' => '2016-07', 'expense' => '10', 'revenue' => '55'),
				array('month' => '2016-08', 'expense' => '51', 'revenue' => '22'),
				array('month' => '2016-09', 'expense' => '59', 'revenue' => '45'),
				array('month' => '2016-10', 'expense' => '20', 'revenue' => '46'),
				array('month' => '2016-11', 'expense' => '59', 'revenue' => '77'),
				array('month' => '2016-12', 'expense' => '20', 'revenue' => '34')
			)
		);
		return $rae;
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
		return $aging;
	}

	public function getSalesAndPurchases() {
		$aging = array(
			'sales' => array(
				array('month' => '2017-01', 'value' => '40'),
				array('month' => '2017-02', 'value' => '40'),
				array('month' => '2017-03', 'value' => '44'),
				array('month' => '2017-04', 'value' => '45'),
				array('month' => '2017-05', 'value' => '46'),
				array('month' => '2017-06', 'value' => '40'),
				array('month' => '2017-07', 'value' => '40'),
				array('month' => '2017-08', 'value' => '44'),
				array('month' => '2017-09', 'value' => '45'),
				array('month' => '2017-10', 'value' => '46'),
				array('month' => '2017-11', 'value' => '45'),
				array('month' => '2017-12', 'value' => '46')
			),
			'purchases' => array(
				array('month' => '2017-01', 'value' => '40'),
				array('month' => '2017-02', 'value' => '24'),
				array('month' => '2017-03', 'value' => '44'),
				array('month' => '2017-04', 'value' => '22'),
				array('month' => '2017-05', 'value' => '46'),
				array('month' => '2017-06', 'value' => '33'),
				array('month' => '2017-07', 'value' => '40'),
				array('month' => '2017-08', 'value' => '44'),
				array('month' => '2017-09', 'value' => '53'),
				array('month' => '2017-10', 'value' => '11'),
				array('month' => '2017-11', 'value' => '45'),
				array('month' => '2017-12', 'value' => '23')
			)
		);
		return $aging;
	}

}