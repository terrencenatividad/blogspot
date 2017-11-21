<?php
class onload {

	public function __construct() {
		$this->db = new db();
	}

	public function init() {
		$this->initChartOfAccounts();
	}

	public function initChartOfAccounts() {
		$module = $this->db->setTable('wc_modules')
							->setFields('module_link')
							->setWhere("module_name = 'Account Codes'")
							->setLimit(1)
							->runSelect(false)
							->getRow();

		if ($module) {
			$result = $this->db->setTable("fintaxcode ftc")
								->setFields('fstaxcode')
								->leftJoin("chartaccount coa ON coa.companycode = ftc.companycode AND coa.id = ftc.salesAccount AND coa.segment5 != '' AND coa.accounttype != 'P'")
								->leftJoin("chartaccount coa2 ON coa2.companycode = ftc.companycode AND ftc.purchaseAccount AND coa2.segment5 != '' AND coa2.accounttype != 'P'")
								->setWhere("(coa.id IS NULL OR coa2.id IS NULL)")
								->setLimit(1)
								->runSelect()
								->getRow();

			if ($result) {
				$module_url = str_replace('%', '', $module->module_link);
				if ($_SERVER['QUERY_STRING'] != '/' . $module_url) {
					header('Location: ' . BASE_URL . $module_url);
					exit();
				}
			}
		}
	}

}