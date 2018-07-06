<?php
class accountcodes_model extends wc_model {

	public function getAccountCodes() {
		$result = $this->db->setTable("fintaxcode")
						->setFields("fstaxcode, shortname, longname, taxrate, taxtype, salesAccount, purchaseAccount")
						->setOrderBy('taxtype, taxrate')
						->runSelect()
						->getResult();
						
		return $result;
	}

	public function updateAccountCodes($data) {
		$return = true;
		foreach ($data['fstaxcode'] as $key => $fstaxcode) {
			$values = array(
				'salesAccount' => $data['salesAccount'][$key],
				'purchaseAccount' => $data['purchaseAccount'][$key]
			);
			$result = $this->db->setTable('fintaxcode')
								->setValues($values)
								->setWhere("fstaxcode = '$fstaxcode'")
								->setLimit(1)
								->runUpdate();
		}

		return $return;
	}

	public function getChartOfAccountsList() {
		$result = $this->db->setTable('chartaccount')
							->setFields('id ind, accountname val')
							->setWhere("segment5 != '' AND accounttype != '' ORDER BY accountname")
							->runSelect()
							->getResult();
		return $result;
	}

	public function checkAccounts() {
		$result = $this->db->setTable("fintaxcode ftc")
							->setFields('fstaxcode')
							->leftJoin("chartaccount coa ON coa.companycode = ftc.companycode AND coa.id = ftc.salesAccount AND coa.segment5 != '' AND coa.accounttype != ''")
							->leftJoin("chartaccount coa2 ON coa2.companycode = ftc.companycode AND ftc.purchaseAccount AND coa2.segment5 != '' AND coa2.accounttype != ''")
							->setWhere("(coa.id IS NULL OR coa2.id IS NULL)")
							->setLimit(1)
							->runSelect()
							->getRow();

		return $result;
	}

}