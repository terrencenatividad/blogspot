<?php
class return_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->log = new log();
	}

	public function saveReturn($data, $data2) {
		$this->getAmounts($data, $data2);

		$result = $this->db->setTable('returns')
							->setValues($data)
							->runInsert();
		
		if ($result) {
			if ($result) {
				$this->log->saveActivity("Create Return [{$data['voucherno']}]");
			}
			$result = $this->updateReturnDetails($data2, $data['voucherno']);
		}

		return $result;
	}

	public function updateReturn($data, $data2, $voucherno) {
		$this->getAmounts($data, $data2);

		$result = $this->db->setTable('returns')
							->setValues($data)
							->setWhere("voucherno = '$voucherno'")
							->runUpdate();
		
		if ($result) {
			if ($result) {
				$this->log->saveActivity("Update Return [$voucherno]");
			}
			$result = $this->updateReturnDetails($data2, $voucherno);
		}

		return $result;
	}

	private function getAmounts(&$data, &$data2) {
		$this->cleanNumber($data, array('amount', 'netamount'));
		$this->cleanNumber($data2, array('issueqty', 'unitprice'));
		foreach ($data2['itemcode'] as $key => $value) {
			$amount							= $data2['issueqty'][$key] * $data2['unitprice'][$key];
			$data2['taxamount'][$key]		= $data2['taxrate'][$key] * $amount;
			$data2['amount'][$key]			= $amount - $data2['taxamount'][$key];
			$data2['convissueqty'][$key]	= $data2['issueqty'][$key] * $data2['conversion'][$key];
		}

		$data['amount']		= array_sum($data2['amount']);
		$data['taxamount']	= array_sum($data2['taxamount']);
		$data['netamount']	= $data['amount'] + $data['taxamount'];
	}

	public function updateReturnDetails($data, $voucherno) {
		$this->db->setTable('returns_details')
					->setWhere("voucherno = '$voucherno'")
					->runDelete();
		
		$data['voucherno']	= $voucherno;
		$result = $this->db->setTable('returns_details')
							->setValuesFromPost($data)
							->runInsert();
							
		return $result;
	}

	public function deleteReturn($data) {
		$ids	= "'" . implode("','", $data) . "'";
		$result	= $this->db->setTable('returns')
							->setValues(array('stat'=>'Cancelled'))
							->setWhere("voucherno IN ($ids)")
							->setLimit(count($data))
							->runUpdate();
		if ($result) {
			if ($result) {
				$log_id = implode(', ', $data);
				$this->log->saveActivity("Cancelled Return [$log_id]");
			}
			$result = $this->db->setTable('returns_details')
							->setValues(array('stat'=>'Cancelled'))
							->setWhere("voucherno IN ($ids)")
							->setLimit(count($data))
							->runUpdate();
		}

		return $result;
	}

	public function getReturnPagination($search, $sort, $customer, $filter, $datefilter) {
		$sort = ($sort) ? $sort : 'transactiondate desc';
		$condition = "r.stat != 'temporary'";
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('voucherno'));
		}
		if ($filter && $filter != 'all') {
			$condition .= " AND r.stat = '$filter'";
		}
		if ($customer && $customer != 'none') {
			$condition .= " AND customer = '$customer'";
		}
		$datefilter	= explode('-', $datefilter);
		foreach ($datefilter as $key => $date) {
			$datefilter[$key] = $this->date->dateDbFormat($date);
		}
		if (isset($datefilter[1])) {
			$condition .= " AND transactiondate >= '{$datefilter[0]}' AND transactiondate <= '{$datefilter[1]}'";
		}
		$result = $this->db->setTable("returns r")
							->innerJoin('partners p ON p.partnercode = r.customer AND p.companycode = r.companycode AND p.partnertype = "customer"')
							->setFields("transactiondate, voucherno, source_no, partnername customer, r.stat stat")
							->setWhere($condition)
							->setOrderBy($sort)
							->runPagination();
		return $result;
	}

	public function getReturnById($fields, $voucherno) {
		$result = $this->db->setTable('returns')
							->setFields($fields)
							->setWhere("voucherno = '$voucherno'")
							->setLimit(1)
							->runSelect()
							->getRow();
		
		return $result;
	}

	public function getReturnDetails($fields, $voucherno, $view = true) {
		if ($view) {
			$result = $this->db->setTable('returns_details')
								->setFields($fields)
								->setWhere("voucherno = '$voucherno'")
								->setOrderBy('linenum')
								->runSelect()
								->getResult();
		} else {
			$sourceno = $this->db->setTable('returns')
								->setFields('source_no')
								->setWhere("voucherno = '$voucherno'")
								->runSelect()
								->getRow();

			$sourceno = ($sourceno) ? $sourceno->source_no : '';

			$result1 = $this->db->setTable('returns_details')
								->setFields($fields)
								->setWhere("voucherno = '$voucherno'")
								->setOrderBy('linenum')
								->runSelect()
								->getResult();

			$result = $this->getSalesReturnDetails($sourceno, $voucherno);
			$header = $this->getSalesReturnHeader(array('amount', 'discounttype', 'discountamount'), $sourceno);

			$checker	= array();
			foreach ($result1 as $key => $row) {
				$checker[$row->linenum] = (object) $row;
			}

			foreach ($result as $key => $row) {
				$result[$key]->issueqty = (isset($checker[$row->linenum])) ? $checker[$row->linenum]->issueqty : 0;
			}

			$total_amount	= $header->amount;
			$total_discount	= 0;
			$discountrate	= 0;


			if ($header->discounttype == 'perc') {
				$total_discount	= $total_amount * $header->discountamount / 100;
				$discountrate	= $header->discountamount / 100;
			} else {
				$total_discount	= $header->discountamount;
				$discountrate	= $total_discount / $total_amount;
			}
			
			foreach ($result as $key => $row) {
				$taxamount = $row->unitprice - ($row->unitprice / (1 + $row->taxrate));
				$discount = ($row->unitprice - $taxamount) * $discountrate;
				$result[$key]->unitprice = $row->unitprice - $discount;
				$result[$key]->taxrate = 0;
				$result[$key]->taxamount = 0;
			}
		}
		return $result;
	}

	public function getCustomerList() {
		$result = $this->db->setTable('partners')
						->setFields("partnercode ind, CONCAT(partnercode,' - ',partnername) val")
						->setWhere("partnercode != '' AND partnertype = 'customer' AND stat = 'active'")
						->setOrderBy("val")
						->runSelect()
						->getResult();

		return $result;
	}

	public function getWarehouseList() {
		$result = $this->db->setTable('warehouse')
						->setFields("warehousecode ind, description val")
						->setOrderBy("val")
						->runSelect()
						->getResult();

		return $result;
	}

	public function getItemList() {
		$result = $this->db->setTable('items')
						->setFields("itemcode ind, CONCAT(itemcode, ' - ', itemname) val")
						->runSelect()
						->getResult();

		return $result;
	}

	public function getSalesReturnPagination($customer = '', $search = '') {
		$condition = '';
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('sr.voucherno', 'sr.remarks'));
		}
		if ($customer != '') {
			 $condition .= " AND sr.customer = '$customer'";
		}

		$subquery	= $this->db->setTable('returns r')
								->setFields('IF(SUM(rd.issueqty) IS NULL, 0, SUM(rd.issueqty)) pr_qty, source_no, r.companycode companycode')
								->innerJoin('returns_details rd ON rd.voucherno = r.voucherno AND rd.companycode = r.companycode')
								->setWhere("r.stat != 'Cancelled'")
								->setGroupBy('r.source_no')
								->buildSelect();

		$result		= $this->db->setTable('salesreturn_details srd')
								->setFields('sr.voucherno voucherno, sr.transactiondate transactiondate, remarks, sr.netamount netamount, (IF(SUM(srd.issueqty) IS NULL, 0, SUM(srd.issueqty)) - IF(r.pr_qty IS NULL, 0, r.pr_qty)) qtyleft, sr.customer')
								->innerJoin('salesreturn sr ON srd.voucherno = sr.voucherno AND srd.companycode = sr.companycode')
								->leftJoin("($subquery) r ON r.source_no = srd.voucherno AND r.companycode = srd.companycode")
								->setWhere("sr.stat NOT IN ('Cancelled','temporary')" . $condition)
								->setGroupBy('sr.voucherno')
								->setHaving('qtyleft > 0')
								->runPagination();

		return $result;
	}

	public function getSalesReturnDetails($voucherno, $voucherno_ref = false) {
		$result1		= $this->db->setTable('salesreturn_details srd')
								->setFields("itemcode, detailparticular, linenum, issueqty, issueqty maxqty, srd.warehouse, issueuom, unitprice, srd.taxcode, srd.taxrate, srd.taxamount, srd.amount, convissueqty, convuom, conversion, issueqty realqty")
								->innerJoin('salesreturn sr ON srd.voucherno = sr.voucherno AND srd.companycode = sr.companycode')
								->setWhere("sr.voucherno = '$voucherno'")
								->runSelect()
								->getResult();

		$addcond = ($voucherno_ref) ? " AND r.voucherno != '$voucherno_ref'" : '';

		$result2		= $this->db->setTable('returns_details rd')
								->setFields("itemcode, linenum, SUM(issueqty) issueqty, rd.warehouse")
								->innerJoin('returns r ON rd.voucherno = r.voucherno AND rd.companycode = r.companycode')
								->setWhere("source_no = '$voucherno' AND r.stat != 'Cancelled'" . $addcond)
								->setGroupBy('linenum')
								->runSelect()
								->getResult();

		$checker	= array();
		$result		= array();
		foreach ($result2 as $key => $row) {
			$checker[$row->linenum] = $row->issueqty;
		}

		foreach ($result1 as $key => $row) {
			$add_result = true;
			if (isset($checker[$row->linenum])) {
				$quantity = $checker[$row->linenum];

				if ($quantity >= $row->issueqty) {
					$add_result = false;
				}
				$row->maxqty = ($row->maxqty > $quantity) ? $row->maxqty - $quantity : 0;
				$row->issueqty = ($row->issueqty > $quantity) ? $row->issueqty - $quantity : 0;
				$checker[$row->linenum] -= $row->issueqty;
			}
			
			if ($add_result) {
				$result[] = $row;
			}
		}

		return $result;
	}

	public function getSalesReturnHeader($fields, $voucherno) {
		$result = $this->db->setTable('salesreturn')
						->setFields($fields)
						->setWhere("voucherno = '$voucherno'")
						->runSelect()
						->getRow();

		return $result;
	}
	
	public function getDocumentInfo($voucherno) {
		$result = $this->db->setTable('returns r')
							->innerJoin('partners p ON p.partnercode = r.customer AND p.companycode = r.companycode')
							->setFields("r.transactiondate documentdate, r.voucherno voucherno, p.partnername company, CONCAT(p.first_name, ' ', p.last_name) customer, source_no referenceno, r.remarks remarks, partnercode, wtaxamount wtax, amount, discounttype disctype, discountamount discount, netamount net, taxamount vat")
							->setWhere("voucherno = '$voucherno'")
							->runSelect()
							->getRow();

		return $result;
	}

	public function getDocumentContent($voucherno) {
		$result = $this->db->setTable('returns_details rd')
							->setFields("itemcode 'Item Code', detailparticular 'Description', issueqty 'Quantity', UPPER(issueuom) 'UOM', unitprice 'Price', amount 'Amount', shortname vat")
							->leftJoin('fintaxcode f ON rd.taxcode = f.fstaxcode AND f.companycode = rd.companycode')
							->setWhere("voucherno = '$voucherno'")
							->runSelect()
							->getResult();

		return $result;
	}
		
	public function getCustomerDetails($partnercode) {
		$result = $this->db->setTable('partners')
							->setFields(array('partnername customer', 'address1 address', 'tinno', 'terms', 'mobile contactno'))
							->setWhere("partnercode = '$partnercode'")
							->runSelect()
							->getRow();

		return $result;
	}

	private function removeComma($data) {
		if (is_array($data)) {
			$temp = array();
			foreach ($data as $val) {
				$temp[] = $this->removeComma($val);
			}
			return $temp;
		} else {
			return str_replace(',', '', $data);
		}
	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

}