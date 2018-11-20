<?php
class sawt_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->log = new log();
    }
    
    public function getCompanyDetails($fields) {
        $result = $this->db->setTable("company")
						->setFields($fields)
                        ->setWhere(1)
                        ->runSelect()
                        ->getRow();
						
		return $result;
    }

	public function getSawtPagination($month, $year) {
        $fields = array (
            'p.partnername',
			'p.last_name',
			'p.first_name',
			'p.businesstype',
            'p.tinno',
            'rv.transactiondate',
			'rv.paymenttype',
            'd.taxbase_amount',
			'd.taxcode',
			'd.debit credit',
			'a.atc_code',
			'a.tax_rate'
		);
		$condition = "";
		$condition .= "MONTH(rv.transactiondate) = $month AND YEAR(rv.transactiondate) = $year AND ";

        $result = $this->db->setTable("partners p")
                        ->leftJoin('receiptvoucher rv ON rv.customer = p.partnercode')
                        ->leftJoin('rv_details d ON d.voucherno = rv.voucherno')
                        ->leftJoin('chartaccount c ON c.id = d.accountcode')
						->leftJoin('atccode a ON a.atcId = d.taxcode')
						->setFields($fields)
						->setWhere($condition. "d.taxcode != '' AND rv.stat = 'posted'")
                        //->setOrderBy($sort)
                        ->runSelect()
						->getResult();
						
		return $result;
    }
    
    public function getPartnerDetails($partnercode) {
		$fields = array (
			'partnername',
			'last_name',
			'first_name',
			'businesstype',
			'tinno'
		);
		$result = $this->db->setTable('partners')
							->setFields($fields)
							->setWhere("partnercode = '$partnercode'")
							->runSelect()
							->getRow();

		return $result;
	}

	public function getRV($voucherno) {
		$fields = array (
			'postingdate',
			'paymenttype'
		);
		$result = $this->db->setTable('receiptvoucher')
							->setFields($fields)
							->setWhere("voucherno = '$voucherno'")
							->runSelect()
							->getRow();

		return $result;
	}

	public function getRVDetails($voucherno) {
		$fields = array (
			'taxbase_amount',
			'taxcode',
			'credit',
			'atc_code',
			'tax_rate'
		);
		$result = $this->db->setTable('rv_details rv')
							->leftJoin('chartaccount c ON c.id = rv.accountcode')
							->leftJoin('atccode a ON a.atcId = rv.taxcode')
							->setFields($fields)
							->setWhere("rv.voucherno = '$voucherno' AND c.segment5 = '1401005'")
							->runSelect()
							->getResult();

		return $result;
	}

}