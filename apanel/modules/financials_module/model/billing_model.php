<?php
class billing_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->log = new log();
	}

	public function saveBilling($data, $data2) {
		$this->getAmounts($data, $data2);

		$result = $this->db->setTable('billing')
							->setValues($data)
							->runInsert();

		if ($result) {
			if ($result) {
				$this->log->saveActivity("Create Billing [{$data['voucherno']}]");
			}

			$result = $this->updateBillingDetails($data2, $data['voucherno'], $data['discounttype']);
		}

		return $result;
	}

	public function updateBilling($data, $data2, $voucherno) {
		$this->getAmounts($data, $data2);

		$result = $this->db->setTable('billing')
							->setValues($data)
							->setWhere("voucherno = '$voucherno'")
							->runUpdate();

		if ($result) {
			if ($result) {
				$this->log->saveActivity("Update Billing [$voucherno]");
			}
			$result = $this->updateBillingDetails($data2, $voucherno, $data['discounttype']);
		}

		return $result;
	}

	private function getAmounts(&$data, &$data2) {
		$this->cleanNumber($data, array('amount', 'netamount', 'discountamount', 'taxamount'));
		$this->cleanNumber($data2, array('issueqty', 'unitprice', 'taxamount', 'amount'));
		$data2['linenum'] = array();
		foreach ($data2['itemcode'] as $key => $value) {
			$data2['convissueqty'][$key]		= $data2['issueqty'][$key] * 1;
			$data2['linenum'][$key]				= $key + 1;
		}
		$data['amount']				= array_sum($data2['amount']);
		$data['taxamount']			= array_sum($data2['taxamount']);
		$data['netamount']			= $data['netamount'];
	}

	public function updateBillingDetails($data, $voucherno, $discounttype) {
		$data['voucherno']	= $voucherno;
		$data['discounttype'] = $discounttype;

		$this->db->setTable('billing_details')
					->setWhere("voucherno = '$voucherno'")
					->runDelete();

		$result = $this->db->setTable('billing_details')
							->setValuesFromPost($data)
							->runInsert();

		return $result;
	}

	public function deleteBilling($data) {
		$ids	= "'" . implode("','", $data) . "'";
		$result	= $this->db->setTable('billing')
							->setValues(array('stat'=>'Cancelled'))
							->setWhere("voucherno IN ($ids)")
							->setLimit(count($data))
							->runUpdate();
		if ($result) {
			if ($result) {
				$log_id = implode(', ', $data);
				$this->log->saveActivity("Delete Billing [$log_id]");
			}
			$result = $this->db->setTable('billing_details')
							->setValues(array('stat'=>'Cancelled'))
							->setWhere("voucherno IN ($ids)")
							->setLimit(count($data))
							->runUpdate();
		}

		return $result;
	}

	public function getTaxRateList() {
		$result = $this->db->setTable('fintaxcode')
					->setFields('fstaxcode ind, shortname val')
					->setWhere("taxtype = 'VAT' AND fstaxcode != 'VATG'")
					->setOrderBy('fstaxcode')
					->runSelect()
					->getResult();

		return $result;
	}

	public function getTaxRates() {
		$result = $this->db->setTable('fintaxcode')
					->setFields('fstaxcode, taxrate')
					->runSelect()
					->getResult();

		$taxrates = array();

		foreach ($result as $row) {
			$taxrates[$row->fstaxcode] = $row->taxrate;
		}

		return json_encode($taxrates);
	}

	public function getBillingPagination($search, $sort, $customer, $filter, $datefilter) {
		$sort = ($sort) ? $sort : 'b.transactiondate desc';
		$condition = "b.stat != 'temporary'";
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('voucherno'));
		}
		if ($filter && $filter != 'all') {
			$condition .= " AND IF(b.stat = 'Cancelled', b.stat, IF(balance = 0, 'Paid', IF(balance = netamount, 'Unpaid', 'With Partial Payment'))) = '$filter'";
		}
		if ($customer && $customer != 'none') {
			$condition .= " AND b.customer = '$customer'";
		}
		$datefilter	= explode('-', $datefilter);
		foreach ($datefilter as $key => $date) {
			$datefilter[$key] = $this->date->dateDbFormat($date);
		}
		if (isset($datefilter[1])) {
			$condition .= " AND b.transactiondate >= '{$datefilter[0]}' AND b.transactiondate <= '{$datefilter[1]}'";
		}
		$result = $this->db->setTable("billing b")
							->leftJoin('accountsreceivable ar ON b.voucherno = ar.referenceno AND b.companycode = ar.companycode')
							->innerJoin('partners p ON p.partnercode = b.customer AND p.companycode = b.companycode')
							->setFields("b.transactiondate, b.voucherno, partnername customer, b.netamount, IF(b.stat = 'Cancelled', b.stat, IF(IFNULL(balance, 0) <= 0, 'Paid', IF(IFNULL(balance, 0) = netamount, 'Unpaid', 'With Partial Payment'))) stat, IFNULL(balance, 0) balance")
							->setWhere($condition)
							->setOrderBy($sort)
							->runPagination();

		return $result;
	}

	public function getBillingById($fields, $voucherno) {
		$fields = array(
			'b.voucherno',
			'b.transactiondate',
			'b.customer',
			'b.remarks',
			'b.amount',
			'b.discounttype',
			'b.discountrate',
			'b.discountamount',
			'b.netamount',
			'b.taxamount',
			'b.vat_sales',
			'b.vat_exempt',
			'b.job_orderno',
			'b.referenceno',
			'b.exchangerate',
			"IF(b.stat = 'Cancelled', b.stat, IF(IFNULL(balance, 0) <= 0, 'Paid', IF(IFNULL(balance, 0) = netamount, 'Unpaid', 'With Partial Payment'))) stat"
		);
		$result = $this->db->setTable('billing b')
							->setFields($fields)
							->leftJoin('accountsreceivable ar ON b.voucherno = ar.referenceno AND b.companycode = ar.companycode')
							->setWhere("b.voucherno = '$voucherno'")
							->setLimit(1)
							->runSelect()
							->getRow();
		
		return $result;
	}

	public function getBillingDetails($fields, $voucherno, $view = true) {
		$result = $this->db->setTable('billing_details')
							->setFields($fields)
							->setWhere("voucherno = '$voucherno'")
							->setOrderBy('linenum')
							->runSelect()
							->getResult();

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
		$result = $this->db->setTable('items i')
						->setFields("itemcode ind, CONCAT(itemcode, ' - ', itemname) val")
						->leftJoin('itemtype it ON it.id = i.typeid AND it.companycode = i.companycode')
						->setWhere("it.label LIKE '%service%'")
						->runSelect()
						->getResult();

		return $result;
	}
	
	public function getItemDetailsList() {
		$result = $this->db->setTable('items i')
						->setFields("i.itemcode, i.itemdesc, uom_base, itemprice")
						->leftJoin('items_price p ON p.itemcode = i.itemcode AND p.companycode = i.companycode')
						->leftJoin('itemtype it ON it.id = i.typeid AND it.companycode = i.companycode')
						->setWhere("it.label LIKE '%service%'")
						->runSelect()
						->getResult();

		return $result;
	}

	public function getDocumentInfo($voucherno) {
		$result = $this->db->setTable('billing b')
							->innerJoin('partners p ON p.partnercode = b.customer AND p.companycode = b.companycode')
							->setFields("b.transactiondate documentdate, b.voucherno voucherno, p.partnername company, CONCAT(p.first_name, ' ', p.last_name) customer, b.remarks remarks, partnercode, wtaxamount wtax, amount, discounttype disctype, discountamount discount, netamount net, taxamount vat, discountrate")
							->setWhere("voucherno = '$voucherno'")
							->runSelect()
							->getRow();

		return $result;
	}

	public function getDocumentContent($voucherno) {
		$result = $this->db->setTable('billing_details bill')
							->setFields("itemcode, detailparticular description, issueqty 'Quantity', unitprice 'Price', amount 'Amount', taxamount 'Tax', bill.taxrate")
							->leftJoin('fintaxcode f ON bill.taxcode = f.fstaxcode AND f.companycode = bill.companycode')
							->setWhere("voucherno = '$voucherno'")
							->runSelect()
							->getResult();

		return $result;
	}

	public function getCustomerDetails($partnercode) {
		$result = $this->db->setTable('partners')
							->setFields(array('partnername customer', 'address1 address', 'tinno','terms','mobile contactno'))
							->setWhere("partnercode = '$partnercode'")
							->runSelect()
							->getRow();

		return $result;
	}

	public function getJOList($customer, $search) {
		$condition = '';
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('job_order_no', 'transactiondate', 'service_quotation'));
		}

		$result = $this->db->setTable('billing')
						->setFields("GROUP_CONCAT(job_orderno SEPARATOR ',') as job_orderno")
						->setWhere("job_orderno != '' AND stat != 'Cancelled'")
						->runSelect()
						->getRow();
		
		$ids = preg_split("/[\s,]+/", $result->job_orderno);
		$jo	= "'" . implode("','", $ids) . "'";
		if ($jo != '') {
			$result		= $this->db->setTable('job_order')
								->setFields('job_order_no, transactiondate, service_quotation')
								->setWhere("customer = '$customer' AND stat = 'completed' AND job_order_no NOT IN ($jo)". $condition)
								->setOrderBy('job_order_no')
								->runPagination();
		}
		else {
			$result		= $this->db->setTable('job_order')
								->setFields('job_order_no, transactiondate, service_quotation')
								->setWhere("customer = '$customer' AND stat = 'completed'". $condition)
								->setOrderBy('job_order_no')
								->runPagination();
		}
		
		return $result;
	}

	public function getJobOrderDetails($job_order_no) {
		$result		= $this->db->setTable('job_order_details jod')
								->setFields("jod.itemcode, detailparticular, linenum, qty issueqty, uom issueuom, i.item_ident_flag")
								->innerJoin('job_order jo ON jod.job_order_no = jo.job_order_no AND jod.companycode = jo.companycode')
								->leftJoin('items i ON i.itemcode = jod.itemcode')
								->leftJoin('invfile inv ON jod.itemcode = inv.itemcode AND jod.warehouse = inv.warehouse AND jod.companycode = inv.companycode')
								->leftJoin('itemtype it ON it.id = i.typeid AND it.companycode = i.companycode')
								->setWhere("jo.job_order_no = '$job_order_no' AND it.label LIKE '%service%' AND parentline = 0")
								->runSelect()
								->getResult();

		return $result;
	}

	public function countServices($voucherno) {
		$result = $this->db->setTable('job_order_details jod')
							->setFields("COUNT('job_order_no') count")
							->leftJoin('items i ON i.itemcode = jod.itemcode')
							->leftJoin('itemtype it ON it.id = i.typeid AND it.companycode = i.companycode')
							->setWhere("jod.job_order_no = '$voucherno' AND it.label LIKE '%service%'")
							->runSelect()
							->getRow();
		
		return $result;
	}

	public function getValue($table, $cols = array(), $cond, $orderby = "", $bool = "")
	{
		$result = $this->db->setTable($table)
					->setFields($cols)
					->setWhere($cond)
					->setOrderBy($orderby)
					->runSelect($bool)
					->getResult();

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