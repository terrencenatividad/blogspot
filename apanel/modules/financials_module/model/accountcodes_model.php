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
							->setWhere("segment5 != '' AND accounttype != 'P' ORDER BY accountname")
							->runSelect()
							->getResult();

		return $result;
	}

}