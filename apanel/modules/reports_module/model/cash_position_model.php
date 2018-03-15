<?php
class cash_position_model extends wc_model {

	public function getCashPosition($datefilter) {
		return (object) array(
			'outstanding_checks'	=> $this->getCheckSum($datefilter, 'released'),
			'check_for_release'		=> $this->getCheckSum($datefilter, 'uncleared'),
			'post_dated_checks'		=> $this->getCheckSum($datefilter, 'postdated')
		);
	}

	private function getCheckSum($datefilter, $stat) {
		$fields	= array('chq.chequeamount amount, chq.companycode');

		$condition = "chq.stat = '$stat' AND chequedate <= '$datefilter'";

		if ($stat == 'postdated') {
			$condition = "chq.stat IN ('released', 'uncleared') AND chequedate > '$datefilter'";
		}

		$pvchq	= $this->db->setTable('pv_cheques chq')
							->setFields($fields)
							->setWhere($condition)
							->buildSelect();

		$inner_query = $pvchq;

		$result = $this->db->setTable("($inner_query) a")
							->setFields('SUM(amount) total_amount')
							->runSelect()
							->getRow();

		$amount = ($result) ? number_format($result->total_amount, 2) : 0;

		return $amount;
	}

	public function getCompanyName() {
		$companyname = '';
		$result = $this->db->setTable('company')
							->setFields('companyname')
							->setLimit(1)
							->runSelect()
							->getRow();

		if ($result) {
			$companyname = $result->companyname;
		}

		return $companyname;
	}

	public function getCashPositionDetails($datefilter, $stat) {
		$query	= $this->getCashPositionDetailsQuery($datefilter, $stat);
		$result	= $query->runSelect()
						->getResult();

		return $result;
	}

	public function getCashPositionDetailsQuery($datefilter, $stat) {
		$fields	= 'chq.releasedate, chq.chequenumber, ap.invoiceno, chq.voucherno, apvoucherno, chq.chequedate, accountname bank, partnername partner, chq.chequeamount, chq.stat, chq.cleardate, pva.transtype, chq.companycode';

		$condition = "chq.stat = '$stat' AND chequedate <= '$datefilter'";

		if ($stat == 'postdated') {
			$condition = "chq.stat IN ('released', 'uncleared') AND chequedate > '$datefilter'";
		}

		$pvchq	= $this->db->setTable("pv_cheques as chq")
							->leftJoin('paymentvoucher pv ON pv.voucherno = chq.voucherno AND pv.companycode = chq.companycode')
							->leftJoin('pv_application pva ON pva.voucherno = pv.voucherno AND pva.companycode = pv.companycode')
							->leftJoin('accountspayable ap ON ap.voucherno = pva.apvoucherno AND ap.companycode = pva.companycode')
							->leftJoin("partners p ON p.partnercode = pv.vendor AND p.companycode = pv.companycode AND p.partnertype = 'supplier' ")
							->leftJoin('chartaccount ca ON ca.id = chq.chequeaccount AND ca.companycode = chq.companycode')
							->setFields($fields)
							->setWhere($condition)
							->setOrderBy('chq.entereddate')
							->buildSelect();

		$inner_query = $pvchq;

		$fields2 	=	array("a.releasedate, a.chequenumber, a.invoiceno, a.voucherno, apvoucherno, a.chequedate, a.bank, a.partner, a.cleardate, a.chequeamount, a.stat, a.transtype");

		$query = $this->db->setTable("($inner_query) a")
							->setFields($fields2);
		
		return $query;
	}

	public function getCashPositionDetailsPagination($datefilter, $stat) {
		$query	= $this->getCashPositionDetailsQuery($datefilter, $stat);
		$result	= $query->runPagination();

		return $result;
	}

}