<?php
class general_journal extends wc_model {

	public function general_journalList($startdate, $enddate, $sort) {
		$condition = '';
		if ($startdate && $enddate) {
			$condition .= " AND jv.transactiondate >= '$startdate' AND jv.transactiondate <= '$enddate'";
		}
		$fields = array('jv.transactiondate transactiondate, jvd.voucherno voucherno, jvd.detailparticulars remarks, coa.accountname, jvd.debit, jvd.credit');

		$result = $this->db->setTable('journaldetails jvd')
                        ->setFields($fields)
                        ->leftJoin('journalvoucher jv ON jv.voucherno = jvd.voucherno AND jv.companycode = jvd.companycode')
						->leftJoin('chartaccount coa ON coa.id = jvd.accountcode AND jvd.companycode = coa.companycode')
						->setWhere(" jv.stat NOT IN ('temporary','cancelled') AND jv.transtype = 'JV' " .$condition)
                        ->setOrderBy($sort)
                        ->runPagination();
						// echo $this->db->getQuery();
		return $result;
	}

	public function fileExport($startdate, $enddate, $sort) {
		$condition = '';
		if ($startdate && $enddate) {
			$condition .= " AND jv.transactiondate >= '$startdate' AND jv.transactiondate <= '$enddate'";
		}
		
		$fields = array('jv.transactiondate transactiondate, jvd.voucherno voucherno, jvd.detailparticulars remarks, coa.accountname, jvd.debit, jvd.credit');
        
		$result = $this->db->setTable('journaldetails jvd')
                            ->setFields($fields)
                            ->leftJoin('journalvoucher jv ON jv.voucherno = jvd.voucherno AND jv.companycode = jvd.companycode')
                            ->leftJoin('chartaccount coa ON coa.id = jvd.accountcode AND jvd.companycode = coa.companycode')
                            ->setWhere(" jv.stat NOT IN ('temporary','cancelled') AND jv.transtype = 'JV' " .$condition)
                            ->setOrderBy($sort)
                            ->runSelect()
						    ->getResult();
		return $result;
    }
    
	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

}