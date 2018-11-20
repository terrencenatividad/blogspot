<?php
class purchase_return_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->log = new log();
	}

	public function savePurchaseReturn($data, $data2) {
		$this->getAmounts($data, $data2);

		$result = $this->db->setTable('purchasereturn')
							->setValues($data)
							->runInsert();
		
		if ($result) {
			if ($result) {
				$this->log->saveActivity("Create Purchase Return [{$data['voucherno']}]");
			}
			$result = $this->updatePurchaseReturnDetails($data2, $data['voucherno']);
		}

		return $result;
	}

	public function updatePurchaseReturn($data, $data2, $voucherno) {
		$this->getAmounts($data, $data2);

		$result = $this->db->setTable('purchasereturn')
							->setValues($data)
							->setWhere("voucherno = '$voucherno'")
							->runUpdate();
		
		if ($result) {
			if ($result) {
				$this->log->saveActivity("Update Purchase Return [$voucherno]");
			}
			$result = $this->updatePurchaseReturnDetails($data2, $voucherno);
		}

		return $result;
	}

	private function getAmounts(&$data, &$data2) {
		foreach ($data2['itemcode'] as $key => $value) {
			$amount							= $data2['receiptqty'][$key] * $data2['unitprice'][$key];
			$data2['taxamount'][$key]		= $data2['taxrate'][$key] * $amount;
			$data2['amount'][$key]			= $amount - $data2['taxamount'][$key];
			$data2['convreceiptqty'][$key]	= $data2['receiptqty'][$key] * $data2['conversion'][$key];
		}

		$data['amount']		= array_sum($data2['amount']);
		$data['taxamount']	= array_sum($data2['taxamount']);
		$data['netamount']	= $data['amount'] + $data['taxamount'];
	}

	public function updatePurchaseReturnDetails($data, $voucherno) {
		$this->db->setTable('purchasereturn_details')
					->setWhere("voucherno = '$voucherno'")
					->runDelete();
		
		$data['voucherno']	= $voucherno;
		$result = $this->db->setTable('purchasereturn_details')
							->setValuesFromPost($data)
							->runInsert();
							
		return $result;
	}

	public function deletePurchaseReturn($data) {
		$ids	= "'" . implode("','", $data) . "'";
		$result	= $this->db->setTable('purchasereturn')
							->setValues(array('stat'=>'Cancelled'))
							->setWhere("voucherno IN ($ids)")
							->setLimit(count($data))
							->runUpdate();
		if ($result) {
			if ($result) {
				$log_id = implode(', ', $data);
				$this->log->saveActivity("Cancelled Purchase Return [$log_id]");
			}
			$result = $this->db->setTable('purchasereturn_details')
							->setValues(array('stat'=>'Cancelled'))
							->setWhere("voucherno IN ($ids)")
							->setLimit(count($data))
							->runUpdate();
		}

		return $result;
	}

	public function getPurchaseReturnPagination($search, $sort, $vendor, $filter, $datefilter) {
		$sort = ($sort) ? $sort : 'transactiondate desc';
		$condition = "prtn.stat != 'temporary'";
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('voucherno'));
		}
		if ($filter && $filter != 'all') {
			$condition .= " AND prtn.stat = '$filter'";
		}
		if ($vendor && $vendor != 'none') {
			$condition .= " AND vendor = '$vendor'";
		}
		$datefilter	= explode('-', $datefilter);
		foreach ($datefilter as $key => $date) {
			$datefilter[$key] = $this->date->dateDbFormat($date);
		}
		if (isset($datefilter[1])) {
			$condition .= " AND transactiondate >= '{$datefilter[0]}' AND transactiondate <= '{$datefilter[1]}'";
		}
		$result = $this->db->setTable("purchasereturn prtn")
							->innerJoin('partners p ON p.partnercode = prtn.vendor AND p.companycode = prtn.companycode AND p.partnertype = "supplier"')
							->setFields("transactiondate, voucherno, source_no, partnername vendor, prtn.stat stat")
							->setWhere($condition)
							->setOrderBy($sort)
							->runPagination();
		return $result;
	}

	public function getPurchaseReturnById($fields, $voucherno) {
		$result = $this->db->setTable('purchasereturn')
							->setFields($fields)
							->setWhere("voucherno = '$voucherno'")
							->setLimit(1)
							->runSelect()
							->getRow();
		
		return $result;
	}

	public function getPurchaseReturnDetails($fields, $voucherno, $view = true) {
		if ($view) {
			$result = $this->db->setTable('purchasereturn_details')
								->setFields($fields)
								->setWhere("voucherno = '$voucherno'")
								->setOrderBy('linenum')
								->runSelect()
								->getResult();
		} else {
			$sourceno = $this->db->setTable('purchasereturn')
								->setFields('source_no')
								->setWhere("voucherno = '$voucherno'")
								->runSelect()
								->getRow();

			$sourceno = ($sourceno) ? $sourceno->source_no : '';

			$result1 = $this->db->setTable('purchasereturn_details')
								->setFields($fields)
								->setWhere("voucherno = '$voucherno'")
								->setOrderBy('linenum')
								->runSelect()
								->getResult();

			$result = $this->getPurchaseReceiptDetails($sourceno, $voucherno);
			$header = $this->getPurchaseReceiptHeader(array('amount', 'discounttype', 'discountamount'), $sourceno);

			$checker	= array();
			foreach ($result1 as $key => $row) {
				$checker[$row->linenum] = (object) $row;
			}

			foreach ($result as $key => $row) {
				$result[$key]->receiptqty = (isset($checker[$row->linenum])) ? $checker[$row->linenum]->receiptqty : 0;
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
				$discount = $row->unitprice * $discountrate;
				$result[$key]->unitprice = $row->unitprice - $discount;
				$result[$key]->taxrate = 0;
				$result[$key]->taxamount = 0;
			}
		}
		return $result;
	}

	public function getVendorList() {
		$result = $this->db->setTable('partners')
						->setFields("partnercode ind,CONCAT(partnercode,' - ',partnername) val")
						->setWhere("partnercode != '' AND partnertype = 'supplier' AND stat = 'active'")
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

	public function getPurchaseReceiptPagination($vendor = '', $search = '') {
		$condition = '';
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('pr.voucherno', 'pr.remarks'));
		}
		if ($vendor != '') {
			 $condition .= " AND pr.vendor = '$vendor'";
		}

		$subquery	= $this->db->setTable('purchasereturn prtn')
								->setFields('IF(SUM(prtnd.receiptqty) IS NULL, 0, SUM(prtnd.receiptqty)) pr_qty, source_no, prtn.companycode companycode')
								->innerJoin('purchasereturn_details prtnd ON prtnd.voucherno = prtn.voucherno AND prtnd.companycode = prtn.companycode')
								->setWhere("prtn.stat != 'Cancelled'")
								->setGroupBy('prtn.source_no')
								->buildSelect();

		$result		= $this->db->setTable('purchasereceipt_details prd')
								->setFields('pr.voucherno voucherno, pr.source_no po_no, pr.transactiondate transactiondate, remarks, pr.netamount netamount, (IF(SUM(prd.receiptqty) IS NULL, 0, SUM(prd.receiptqty)) - IF(prtn.pr_qty IS NULL, 0, prtn.pr_qty)) qtyleft, pr.vendor, invoiceno')
								->innerJoin('purchasereceipt pr ON prd.voucherno = pr.voucherno AND prd.companycode = pr.companycode')
								->leftJoin("($subquery) prtn ON prtn.source_no = prd.voucherno AND prtn.companycode = prd.companycode")
								->setWhere("pr.stat NOT IN ('Cancelled','temporary')" . $condition)
								->setGroupBy('pr.voucherno')
								->setHaving('qtyleft > 0')
								->runPagination();

		return $result;
	}

	public function getPurchaseReceiptDetails($voucherno, $voucherno_ref = false) {
		$result1		= $this->db->setTable('purchasereceipt_details prd')
								->setFields("itemcode, detailparticular, linenum, receiptqty, receiptqty maxqty, prd.warehouse, receiptuom, unitprice, prd.taxcode, prd.taxrate, prd.taxamount, prd.amount, convreceiptqty, convuom, conversion, receiptqty realqty")
								->innerJoin('purchasereceipt pr ON prd.voucherno = pr.voucherno AND prd.companycode = pr.companycode')
								->setWhere("pr.voucherno = '$voucherno'")
								->runSelect()
								->getResult();

		$addcond = ($voucherno_ref) ? " AND prtn.voucherno != '$voucherno_ref'" : '';

		$result2		= $this->db->setTable('purchasereturn_details prtnd')
								->setFields("itemcode, linenum, SUM(receiptqty) receiptqty, prtnd.warehouse")
								->innerJoin('purchasereturn prtn ON prtnd.voucherno = prtn.voucherno AND prtnd.companycode = prtn.companycode')
								->setWhere("source_no = '$voucherno' AND prtn.stat != 'Cancelled'" . $addcond)
								->setGroupBy('linenum')
								->runSelect()
								->getResult();

		$checker	= array();
		$result		= array();
		foreach ($result2 as $key => $row) {
			$checker[$row->linenum] = $row->receiptqty;
		}

		foreach ($result1 as $key => $row) {
			$add_result = true;
			if (isset($checker[$row->linenum])) {
				$quantity = $checker[$row->linenum];

				if ($quantity >= $row->receiptqty) {
					$add_result = false;
				}
				$row->maxqty = ($row->maxqty > $quantity) ? $row->maxqty - $quantity : 0;
				$row->receiptqty = ($row->receiptqty > $quantity) ? $row->receiptqty - $quantity : 0;
				$checker[$row->linenum] -= $row->receiptqty;
			}
			if ( ! $voucherno_ref) {
				$row->receiptqty = 0;
			}
			
			if ($add_result) {
				$result[] = $row;
			}
		}

		return $result;
	}

	public function getPurchaseReceiptHeader($fields, $voucherno) {
		$fields[] = 'discountrate';
		$result = $this->db->setTable('purchasereceipt')
						->setFields($fields)
						->setWhere("voucherno = '$voucherno'")
						->runSelect()
						->getRow();

		return $result;
	}
	
	public function getDocumentInfo($voucherno) {
		$result = $this->db->setTable('purchasereturn prtn')
							->innerJoin('partners p ON p.partnercode = prtn.vendor AND p.companycode = prtn.companycode')
							->setFields("prtn.transactiondate documentdate, prtn.voucherno voucherno, p.partnername company, CONCAT(p.first_name, ' ', p.last_name) vendor, pr.invoiceno, prtn.source_no referenceno, prtn.remarks remarks, partnercode, prtn.wtaxamount wtax, prtn.amount, prtn.discounttype disctype, prtn.discountamount discount, prtn.netamount net, prtn.taxamount vat")
							->leftJoin('purchasereceipt pr ON pr.voucherno = prtn.source_no AND pr.companycode = prtn.companycode')
							->setWhere("prtn.voucherno = '$voucherno'")
							->runSelect()
							->getRow();

		return $result;
	}

	public function getDocumentContent($voucherno) {
		$result = $this->db->setTable('purchasereturn_details prtnd')
							->setFields("itemcode 'Item Code', detailparticular 'Description', receiptqty 'Quantity', UPPER(receiptuom) 'UOM', unitprice 'Price', amount 'Amount', shortname vat")
							->leftJoin('fintaxcode f ON prtnd.taxcode = f.fstaxcode AND f.companycode = prtnd.companycode')
							->setWhere("voucherno = '$voucherno'")
							->runSelect()
							->getResult();

		return $result;
	}
		
	public function getVendorDetails($partnercode) {
		$result = $this->db->setTable('partners')
							->setFields(array('partnername vendor', 'address1 address', 'tinno', 'terms', 'mobile contactno'))
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