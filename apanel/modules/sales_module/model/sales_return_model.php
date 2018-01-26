<?php
class sales_return_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->log = new log();
	}

	public function saveSalesReturn($data, $data2) {
		$this->getAmounts($data, $data2);

		$result = $this->db->setTable('salesreturn')
							->setValues($data)
							->runInsert();
		
		if ($result) {
			if ($result) {
				$this->log->saveActivity("Create Sales Return [{$data['voucherno']}]");
			}
			$result = $this->updateSalesReturnDetails($data2, $data['voucherno']);
		}

		return $result;
	}

	public function updateSalesReturn($data, $data2, $voucherno) {
		$this->getAmounts($data, $data2);

		$result = $this->db->setTable('salesreturn')
							->setValues($data)
							->setWhere("voucherno = '$voucherno'")
							->runUpdate();
		
		if ($result) {
			if ($result) {
				$this->log->saveActivity("Update Sales Return [$voucherno]");
			}
			$result = $this->updateSalesReturnDetails($data2, $voucherno);
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

	public function updateSalesReturnDetails($data, $voucherno) {
		$this->db->setTable('salesreturn_details')
					->setWhere("voucherno = '$voucherno'")
					->runDelete();
		
		$data['voucherno']	= $voucherno;
		$result = $this->db->setTable('salesreturn_details')
							->setValuesFromPost($data)
							->runInsert();
							
		return $result;
	}

	public function deleteSalesReturn($data) {
		$ids	= "'" . implode("','", $data) . "'";
		$result	= $this->db->setTable('salesreturn')
							->setValues(array('stat'=>'Cancelled'))
							->setWhere("voucherno IN ($ids)")
							->setLimit(count($data))
							->runUpdate();
		if ($result) {
			if ($result) {
				$log_id = implode(', ', $data);
				$this->log->saveActivity("Cancelled Sales Return [$log_id]");
			}
			$result = $this->db->setTable('salesreturn_details')
							->setValues(array('stat'=>'Cancelled'))
							->setWhere("voucherno IN ($ids)")
							->setLimit(count($data))
							->runUpdate();
		}

		return $result;
	}

	public function getSalesReturnPagination($search, $sort, $customer, $filter, $datefilter) {
		$sort = ($sort) ? $sort : 'transactiondate desc';
		$condition = "sr.stat != 'temporary'";
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('voucherno'));
		}
		if ($filter && $filter != 'all') {
			$condition .= " AND sr.stat = '$filter'";
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
		$result = $this->db->setTable("salesreturn sr")
							->innerJoin('partners p ON p.partnercode = sr.customer AND p.companycode = sr.companycode')
							->setFields("transactiondate, voucherno, source_no, partnername customer, sr.stat stat")
							->setWhere($condition)
							->setOrderBy($sort)
							->runPagination();
		return $result;
	}

	public function getSalesReturnById($fields, $voucherno) {
		$result = $this->db->setTable('salesreturn')
							->setFields($fields)
							->setWhere("voucherno = '$voucherno'")
							->setLimit(1)
							->runSelect()
							->getRow();
		
		return $result;
	}

	public function getSalesReturnDetails($fields, $voucherno, $view = true) {
		if ($view) {
			$result = $this->db->setTable('salesreturn_details')
								->setFields($fields)
								->setWhere("voucherno = '$voucherno'")
								->setOrderBy('linenum')
								->runSelect()
								->getResult();
		} else {
			$sourceno = $this->db->setTable('salesreturn')
								->setFields('source_no')
								->setWhere("voucherno = '$voucherno'")
								->runSelect()
								->getRow();

			$sourceno = ($sourceno) ? $sourceno->source_no : '';

			$result1 = $this->db->setTable('salesreturn_details')
								->setFields($fields)
								->setWhere("voucherno = '$voucherno'")
								->setOrderBy('linenum')
								->runSelect()
								->getResult();

			$result = $this->getSalesInvoiceDetails($sourceno, $voucherno);
			$header = $this->getSalesInvoiceHeader(array('amount', 'discounttype', 'discountamount'), $sourceno);

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
						->setFields("partnercode ind,partnername val")
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
						->setFields("itemcode ind, itemname val")
						->runSelect()
						->getResult();

		return $result;
	}

	public function getSalesInvoicePagination($customer = '', $search = '') {
		$condition = '';
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('si.voucherno', 'si.remarks'));
		}
		if ($customer != '') {
			 $condition .= " AND si.customer = '$customer'";
		}

		$subquery	= $this->db->setTable('salesreturn sr')
								->setFields('IF(SUM(srd.issueqty) IS NULL, 0, SUM(srd.issueqty)) pr_qty, source_no, sr.companycode companycode')
								->innerJoin('salesreturn_details srd ON srd.voucherno = sr.voucherno AND srd.companycode = sr.companycode')
								->setWhere("sr.stat != 'Cancelled'")
								->setGroupBy('sr.source_no')
								->buildSelect();

		$result		= $this->db->setTable('salesinvoice_details sid')
								->setFields('si.voucherno voucherno, si.transactiondate transactiondate, remarks, si.netamount netamount, (IF(SUM(sid.issueqty) IS NULL, 0, SUM(sid.issueqty)) - IF(sr.pr_qty IS NULL, 0, sr.pr_qty)) qtyleft, si.customer')
								->innerJoin('salesinvoice si ON sid.voucherno = si.voucherno AND sid.companycode = si.companycode')
								->leftJoin("($subquery) sr ON sr.source_no = sid.voucherno AND sr.companycode = sid.companycode")
								->setWhere("si.stat NOT IN ('Cancelled','temporary')" . $condition)
								->setGroupBy('si.voucherno')
								->setHaving('qtyleft > 0')
								->runPagination();

		return $result;
	}

	public function getSalesInvoiceDetails($voucherno, $voucherno_ref = false) {
		$result1		= $this->db->setTable('salesinvoice_details sid')
								->setFields("sid.itemcode, sid.detailparticular, sid.linenum, sid.issueqty, sid.issueqty maxqty, sid.warehouse, sid.issueuom, sid.unitprice, sid.taxcode, sid.taxrate, sid.taxamount, sid.amount, sid.convissueqty, sid.convuom, sid.conversion, sid.issueqty realqty")
								->innerJoin('salesinvoice si ON sid.voucherno = si.voucherno AND sid.companycode = si.companycode')
								->setWhere("si.voucherno = '$voucherno'")
								->runSelect()
								->getResult();

		$addcond = ($voucherno_ref) ? " AND sr.voucherno != '$voucherno_ref'" : '';

		$result2		= $this->db->setTable('salesreturn_details srd')
								->setFields("srd.itemcode, srd.linenum, SUM(srd.issueqty) issueqty, srd.warehouse")
								->innerJoin('salesreturn sr ON srd.voucherno = sr.voucherno AND srd.companycode = sr.companycode')
								->setWhere("sr.source_no = '$voucherno' AND sr.stat != 'Cancelled'" . $addcond)
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
			if ( ! $voucherno_ref) {
				$row->issueqty = 0;
			}
			
			if ($add_result) {
				$result[] = $row;
			}
		}

		return $result;
	}

	public function getSalesInvoiceHeader($fields, $voucherno) {
		$result = $this->db->setTable('salesinvoice')
						->setFields($fields)
						->setWhere("voucherno = '$voucherno'")
						->runSelect()
						->getRow();

		return $result;
	}
	
	public function getDocumentInfo($voucherno) {
		$result = $this->db->setTable('salesreturn sr')
							->innerJoin('partners p ON p.partnercode = sr.customer AND p.companycode = sr.companycode')
							->setFields("sr.transactiondate documentdate, sr.voucherno voucherno, p.partnername company, CONCAT(p.first_name, ' ', p.last_name) customer, source_no referenceno, sr.remarks remarks, partnercode, wtaxamount wtax, amount, discounttype disctype, discountamount discount, netamount net, taxamount vat")
							->setWhere("voucherno = '$voucherno'")
							->runSelect()
							->getRow();

		return $result;
	}

	public function getDocumentContent($voucherno) {
		$result = $this->db->setTable('salesreturn_details srd')
							->setFields("itemcode 'Item Code', detailparticular 'Description', issueqty 'Quantity', UPPER(issueuom) 'UOM', unitprice 'Price', amount 'Amount', shortname vat")
							->leftJoin('fintaxcode f ON srd.taxcode = f.fstaxcode AND f.companycode = srd.companycode')
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