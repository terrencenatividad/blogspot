<?php
class delivery_receipt_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->log = new log();
	}

	public function saveDeliveryReceipt($data, $data2) {
		$this->getAmounts($data, $data2);

		$result = $this->db->setTable('deliveryreceipt')
							->setValues($data)
							->runInsert();

		if ($result) {
			if ($result) {
				$this->log->saveActivity("Create Delivery Receipt [{$data['voucherno']}]");
			}
			$result = $this->updateDeliveryReceiptDetails($data2, $data['voucherno']);
		}

		return $result;
	}

	public function updateDeliveryReceipt($data, $data2, $voucherno)                         {
		$this->getAmounts($data, $data2);

		$result = $this->db->setTable('deliveryreceipt')
							->setValues($data)
							->setWhere("voucherno = '$voucherno'")
							->runUpdate();

		if ($result) {
			if ($result) {
				$this->log->saveActivity("Update Delivery Receipt [$voucherno]");
			}
			$result = $this->updateDeliveryReceiptDetails($data2, $voucherno);
		}

		return $result;
	}

	private function getAmounts(&$data, &$data2) {
		$this->cleanNumber($data, array('amount', 'netamount'));
		$this->cleanNumber($data2, array('issueqty', 'unitprice'));
		$data2['amount'] = array();
		foreach ($data2['itemcode'] as $key => $value) {
			$data2['convissueqty'][$key]	= $data2['issueqty'][$key] * $data2['conversion'][$key];
			$data2['amount'][$key]			= $data2['issueqty'][$key] * $data2['unitprice'][$key];
		}
		$data['amount']		= array_sum($data2['amount']);
		$data['netamount']	= $data['amount'];
	}

	public function updateDeliveryReceiptDetails($data, $voucherno) {
		$data['voucherno']	= $voucherno;

		$this->updateSalesOrder($voucherno);

		$this->db->setTable('deliveryreceipt_details')
					->setWhere("voucherno = '$voucherno'")
					->runDelete();

		$result = $this->db->setTable('deliveryreceipt_details')
							->setValuesFromPost($data)
							->runInsert();

		$this->updateSalesOrder($voucherno);
							
		return $result;
	}

	public function deleteDeliveryReceipt($data) {
		$ids	= "'" . implode("','", $data) . "'";
		$result	= $this->db->setTable('deliveryreceipt')
							->setValues(array('stat'=>'Cancelled'))
							->setWhere("voucherno IN ($ids)")
							->setLimit(count($data))
							->runUpdate();
		if ($result) {
			if ($result) {
				$log_id = implode(', ', $data);
				$this->log->saveActivity("Cancelled Delivery Receipt [$log_id]");
			}
			$result = $this->db->setTable('deliveryreceipt_details')
							->setValues(array('stat'=>'Cancelled'))
							->setWhere("voucherno IN ($ids)")
							->setLimit(count($data))
							->runUpdate();

			if ($result) {
				foreach ($data as $voucherno) {
					$this->updateSalesOrder($voucherno);
				} 
			}
		}

		return $result;
	}

	public function tagAsDelivered($data) {
		$ids	= "'" . implode("','", $data) . "'";
		$result	= $this->db->setTable('deliveryreceipt')
							->setValues(array('stat' => 'Delivered'))
							->setWhere("voucherno IN ($ids)")
							->setLimit(count($data))
							->runUpdate();
		if ($result) {
			if ($result) {
				$log_id = implode(', ', $data);
				$this->log->saveActivity("Tag as Delivered [$log_id]");
			}
			$result = $this->db->setTable('deliveryreceipt_details')
							->setValues(array('stat'=>'Delivered'))
							->setWhere("voucherno IN ($ids)")
							->setLimit(count($data))
							->runUpdate();
		}

		return $result;
	}

	public function untagAsDelivered($data) {
		$ids	= "'" . implode("','", $data) . "'";
		$result	= $this->db->setTable('deliveryreceipt')
							->setValues(array('stat'=>'Prepared'))
							->setWhere("voucherno IN ($ids)")
							->setLimit(count($data))
							->runUpdate();
		if ($result) {
			if ($result) {
				$log_id = implode(', ', $data);
				$this->log->saveActivity("Untag as Delivered [$log_id]");
			}
			$result = $this->db->setTable('deliveryreceipt_details')
							->setValues(array('stat'=>'Prepared'))
							->setWhere("voucherno IN ($ids)")
							->setLimit(count($data))
							->runUpdate();
		}

		return $result;
	}

	private function getSourceNo($voucherno) {
		$result = $this->db->setTable('deliveryreceipt')
							->setFields(array('source_no'))
							->setWhere("voucherno = '$voucherno'")
							->setLimit(1)
							->runSelect()
							->getRow();

		if ($result) {
			return $result->source_no;
		} else {
			return false;
		}
	}

	private function updateSalesOrder($voucherno) {
		$source_no	= $this->getSourceNo($voucherno);

		$subquery	= $this->db->setTable('deliveryreceipt dr')
								->setFields('IF(SUM(drd.issueqty) IS NULL, 0, SUM(drd.issueqty)) pr_qty, source_no, dr.companycode companycode')
								->innerJoin('deliveryreceipt_details drd ON drd.voucherno = dr.voucherno AND drd.companycode = dr.companycode')
								->setWhere("dr.stat != 'Cancelled' AND source_no = '$source_no'")
								->setGroupBy('dr.source_no')
								->buildSelect();

		$result		= $this->db->setTable('salesorder_details sod')
								->setFields('so.voucherno voucherno, so.transactiondate transactiondate, so.netamount netamount, SUM(sod.issueqty) quantity, COALESCE(dr.pr_qty, 0, dr.pr_qty) quantity_x')
								->innerJoin('salesorder so ON sod.voucherno = so.voucherno AND sod.companycode = so.companycode')
								->leftJoin("($subquery) dr ON dr.source_no = sod.voucherno AND dr.companycode = sod.companycode")
								->setWhere("so.stat IN ('open','partial', 'posted') AND so.voucherno = '$source_no'")
								->setGroupBy('so.voucherno')
								->runSelect()
								->getRow();

		if ($result) {
			$status = 'open';
			if ($result->quantity_x == 0) {
				$status = 'open';
			} else if ($result->quantity > $result->quantity_x) {
				$status = 'partial';
			} else if ($result->quantity = $result->quantity_x) {
				$status = 'posted';
			}
			$result = $this->db->setTable('salesorder')
								->setValues(array('stat' => $status))
								->setWhere("voucherno = '$source_no'")
								->runUpdate();
		}


		return $result;
	}

	public function getTaxRateList() {
		$result = $this->db->setTable('fintaxcode')
					->setFields('fstaxcode ind, shortname val')
					->setWhere("taxtype = 'VAT'")
					->setOrderBy('fstaxcode')
					->runSelect()
					->getResult();

		return $result;
	}

	public function getWTaxCodeList() {
		$result = $this->db->setTable('fintaxcode')
					->setFields('fstaxcode ind, shortname val')
					->setWhere("taxtype = 'WTX'")
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

	public function getDeliveryReceiptPagination($search, $sort, $customer, $filter, $datefilter) {
		$sort = ($sort) ? $sort : 'transactiondate desc';
		$condition = "dr.stat != 'temporary'";
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('voucherno', 'partnername', 'source_no'));
		}
		if ($filter && $filter != 'all') {
			$condition .= " AND dr.stat = '$filter'";
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
		$result = $this->db->setTable("deliveryreceipt dr")
							->innerJoin('partners p ON p.partnercode = dr.customer AND p.companycode = dr.companycode')
							->setFields("transactiondate, voucherno, source_no, partnername customer, netamount, deliverydate, dr.stat stat")
							->setWhere($condition)
							->setOrderBy($sort)
							->runPagination();
		return $result;
	}

	public function getDeliveryReceiptById($fields, $voucherno) {
		$result = $this->db->setTable('deliveryreceipt')
							->setFields($fields)
							->setWhere("voucherno = '$voucherno'")
							->setLimit(1)
							->runSelect()
							->getRow();
		
		return $result;
	}

	public function getDeliveryReceiptDetails($fields, $voucherno, $view = true) {
		if ($view) {
			$result = $this->db->setTable('deliveryreceipt_details')
								->setFields($fields)
								->setWhere("voucherno = '$voucherno'")
								->setOrderBy('linenum')
								->runSelect()
								->getResult();
		} else {
			$sourceno = $this->db->setTable('deliveryreceipt')
								->setFields('source_no')
								->setWhere("voucherno = '$voucherno'")
								->runSelect()
								->getRow();

			$sourceno = ($sourceno) ? $sourceno->source_no : '';

			$result1 = $this->db->setTable('deliveryreceipt_details')
								->setFields($fields)
								->setWhere("voucherno = '$voucherno'")
								->setOrderBy('linenum')
								->runSelect()
								->getResult();

			$result = $this->getSalesOrderDetails($sourceno, $voucherno);

			$checker	= array();
			foreach ($result1 as $key => $row) {
				$checker[$row->linenum] = (object) $row;
			}

			foreach ($result as $key => $row) {
				$result[$key]->issueqty = (isset($checker[$row->linenum])) ? $checker[$row->linenum]->issueqty : 0;
				if (isset($checker[$row->linenum])) {
					$result[$key]->amount = $checker[$row->linenum]->amount;
				}
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
						->setFields("itemcode ind, CONCAT(itemcode, ' - ', itemname) val")
						->runSelect()
						->getResult();

		return $result;
	}

	public function getSalesOrderPagination($customer = '', $search = '') {
		$condition = '';
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('so.voucherno', 'so.remarks'));
		}
		if ($customer != '') {
			 $condition .= " AND so.customer = '$customer'";
		}

		$subquery	= $this->db->setTable('deliveryreceipt dr')
								->setFields('IF(SUM(drd.issueqty) IS NULL, 0, SUM(drd.issueqty)) pr_qty, source_no, dr.companycode companycode')
								->innerJoin('deliveryreceipt_details drd ON drd.voucherno = dr.voucherno AND drd.companycode = dr.companycode')
								->setWhere("dr.stat != 'Cancelled'")
								->setGroupBy('dr.source_no')
								->buildSelect();

		$result		= $this->db->setTable('salesorder_details sod')
								->setFields('so.voucherno, so.transactiondate transactiondate, remarks, so.netamount netamount, (IF(SUM(sod.issueqty) IS NULL, 0, SUM(sod.issueqty)) - IF(dr.pr_qty IS NULL, 0, dr.pr_qty)) qtyleft, so.customer')
								->innerJoin('salesorder so ON sod.voucherno = so.voucherno AND sod.companycode = so.companycode')
								->leftJoin("($subquery) dr ON dr.source_no = sod.voucherno AND dr.companycode = sod.companycode")
								->setWhere("so.stat IN ('open','partial')" . $condition)
								->setGroupBy('so.voucherno')
								->setHaving('qtyleft > 0')
								->runPagination();

		return $result;
	}

	public function getSalesOrderDetails($voucherno, $voucherno_ref = false) {
		$result1		= $this->db->setTable('salesorder_details sod')
								->setFields("sod.itemcode, detailparticular, linenum, issueqty, issueqty maxqty, sod.warehouse, issueuom, unitprice, sod.taxcode, taxrate, sod.taxamount, sod.amount, convissueqty, convuom, conversion, FLOOR(COALESCE(inv.onhandQty, 0) / conversion) available")
								->innerJoin('salesorder so ON sod.voucherno = so.voucherno AND sod.companycode = so.companycode')
								->leftJoin('invfile inv ON sod.itemcode = inv.itemcode AND sod.warehouse = inv.warehouse AND sod.companycode = inv.companycode')
								->setWhere("so.voucherno = '$voucherno'")
								->runSelect()
								->getResult();

		$addcond = ($voucherno_ref) ? " AND dr.voucherno != '$voucherno_ref'" : '';

		$result2		= $this->db->setTable('deliveryreceipt_details drd')
								->setFields("itemcode, linenum, SUM(issueqty) issueqty, drd.warehouse")
								->innerJoin('deliveryreceipt dr ON drd.voucherno = dr.voucherno AND drd.companycode = dr.companycode')
								->setWhere("source_no = '$voucherno' AND dr.stat != 'Cancelled'" . $addcond)
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
			$row->qtyleft = $row->maxqty;
			if ($row->available < $row->maxqty) {
				$row->maxqty = $row->available;
			}
			$row->maxqty = ($row->maxqty > 0) ? $row->maxqty : 0;
			if ($add_result) {
				$result[] = $row;
			}
		}

		return $result;
	}

	public function getSalesOrderHeader($fields, $voucherno) {
		$result = $this->db->setTable('salesorder')
						->setFields($fields)
						->setWhere("voucherno = '$voucherno'")
						->runSelect()
						->getRow();

		return $result;
	}

	public function getDocumentInfo($voucherno) {
		$result = $this->db->setTable('deliveryreceipt dr')
							->innerJoin('partners p ON p.partnercode = dr.customer AND p.companycode = dr.companycode')
							->setFields("dr.transactiondate documentdate, dr.voucherno voucherno, p.partnername company, CONCAT(p.first_name, ' ', p.last_name) customer, source_no referenceno, dr.remarks remarks, partnercode, wtaxamount wtax, amount, discounttype disctype, discountamount discount, netamount net, taxamount vat")
							->setWhere("voucherno = '$voucherno'")
							->runSelect()
							->getRow();

		return $result;
	}

	public function getDocumentContent($voucherno) {
		$result = $this->db->setTable('deliveryreceipt_details drd')
							->setFields("itemcode 'Item Code', detailparticular 'Description', issueqty 'Quantity', UPPER(issueuom) 'UOM', unitprice price, amount amount")
							->leftJoin('uom u ON u.uomcode = drd.issueuom AND u.companycode = drd.companycode')
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