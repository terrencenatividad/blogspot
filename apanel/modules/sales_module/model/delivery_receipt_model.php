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
		$this->updatePackingList($data['voucherno']);

		return $result;
	}
	
	private function getSourceNo($voucherno) {
		$result = $this->db->setTable('deliveryreceipt')
							->setFields(array('packing_no'))
							->setWhere("voucherno = '$voucherno'")
							->setLimit(1)
							->runSelect()
							->getRow();

		if ($result) {
			return $result->packing_no;
		} else {
			return false;
		}
	}

	private function updatePackingList($voucherno) {
		$packing_no	= $this->getSourceNo($voucherno);

		$subquery	= $this->db->setTable('deliveryreceipt dr')
								->setFields('IF(SUM(drd.issueqty) IS NULL, 0, SUM(drd.issueqty)) pr_qty, packing_no, dr.companycode companycode')
								->innerJoin('deliveryreceipt_details drd ON drd.voucherno = dr.voucherno AND drd.companycode = dr.companycode')
								->setWhere("dr.stat != 'Cancelled' AND packing_no = '$packing_no'")
								->setGroupBy('dr.packing_no')
								->buildSelect();

		$result		= $this->db->setTable('packinglist_details pld')
								->setFields('pl.voucherno voucherno, pl.transactiondate transactiondate, pl.netamount netamount, SUM(pld.issueqty) quantity, COALESCE(dr.pr_qty, 0, dr.pr_qty) quantity_x')
								->innerJoin('packinglist pl ON pld.voucherno = pl.voucherno AND pld.companycode = pl.companycode')
								->leftJoin("($subquery) dr ON dr.packing_no = pld.voucherno AND dr.companycode = pld.companycode")
								->setWhere("pl.stat IN ('Packed','Delivered') AND pl.voucherno = '$packing_no'")
								->setGroupBy('pl.voucherno')
								->runSelect()
								->getRow();

		if ($result) {
			$status = 'Packed';
			if ($result->quantity_x == 0) {
				$status = 'Packed';
			} else if ($result->quantity > $result->quantity_x) {
				$status = 'Delivered';
			} else if ($result->quantity = $result->quantity_x) {
				$status = 'Delivered';
			}
			$result = $this->db->setTable('packinglist')
								->setValues(array('stat' => $status))
								->setWhere("voucherno = '$packing_no'")
								->runUpdate();
		}


		return $result;
	}

	public function updateDeliveryReceipt($data, $data2, $voucherno) {
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
		$this->updatePackingList($voucherno);

		return $result;
	}

	public function updateDeliveryReceiptDetails($data, $voucherno) {
		$this->db->setTable('deliveryreceipt_details')
					->setWhere("voucherno = '$voucherno'")
					->runDelete();

		foreach ($data['itemcode'] as $key => $value) {
			$linenum[] = $key + 1;
			$data['convissueqty'][$key] = $data['issueqty'][$key] * $data['conversion'][$key];
		}
		$data['voucherno']	= $voucherno;
		$data['linenum']	= $linenum;
		$result = $this->db->setTable('deliveryreceipt_details')
							->setValuesFromPost($data)
							->runInsert();
							
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
		}

		if ($result) {
			foreach ($data as $voucherno) {
				$this->updatePackingList($voucherno);
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

	public function getDeliveryReceiptPagination($search, $sort, $customer, $filter, $datefilter) {
		$sort = ($sort) ? $sort : 'transactiondate desc';
		$condition = "dr.stat != 'temporary'";
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('voucherno', 'packing_no', 'partnername'));
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
							->setFields("transactiondate, deliverydate, voucherno, packing_no, partnername customer, dr.stat stat")
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

	public function getDeliveryReceiptDetails($fields, $voucherno) {
		$result = $this->db->setTable('deliveryreceipt_details')
							->setFields($fields)
							->setWhere("voucherno = '$voucherno'")
							->setOrderBy('linenum')
							->runSelect()
							->getResult();
		return $result;
	}

	public function getCustomerList() {
		$result = $this->db->setTable('partners')
						->setFields("partnercode ind, partnername val")
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

	public function getPackingPagination($customer = '', $search = '') {
		$condition = '';
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('p.voucherno', 'p.remarks'));
		}
		if ($customer != '') {
			 $condition .= " AND p.customer = '$customer'";
		}

		$subquery	= $this->db->setTable('deliveryreceipt dr')
								->setFields('IF(SUM(drd.issueqty) IS NULL, 0, SUM(drd.issueqty)) dr_qty, packing_no, dr.companycode companycode')
								->innerJoin('deliveryreceipt_details drd ON drd.voucherno = dr.voucherno AND drd.companycode = dr.companycode')
								->setWhere("dr.stat != 'Cancelled'")
								->setGroupBy('dr.packing_no')
								->buildSelect();

		$result		= $this->db->setTable('packinglist_details pd')
								->setFields('p.voucherno voucherno, p.transactiondate transactiondate, remarks, p.netamount netamount, (IF(SUM(pd.issueqty) IS NULL, 0, SUM(pd.issueqty)) - IF(dr.dr_qty IS NULL, 0, dr.dr_qty)) qtyleft, p.customer')
								->innerJoin('packinglist p ON pd.voucherno = p.voucherno AND pd.companycode = p.companycode')
								->leftJoin("($subquery) dr ON dr.packing_no = pd.voucherno AND dr.companycode = pd.companycode")
								->setWhere("p.stat NOT IN ('cancelled','temporary')" . $condition)
								->setGroupBy('p.voucherno')
								->setHaving('qtyleft > 0')
								->runPagination();

		return $result;
	}

	public function getPackingDetails($voucherno) {
		$subquery	= $this->db->setTable('deliveryreceipt dr')
								->setFields('IF(SUM(drd.issueqty) IS NULL, 0, SUM(drd.issueqty)) dr_qty, packing_no, dr.companycode companycode, itemcode, drd.warehouse warehouse')
								->innerJoin('deliveryreceipt_details drd ON drd.voucherno = dr.voucherno AND drd.companycode = dr.companycode')
								->setWhere("dr.stat != 'Cancelled'")
								->setGroupBy('dr.packing_no, itemcode')
								->buildSelect();

		$result		= $this->db->setTable('packinglist_details pd')
								->setFields('pd.itemcode itemcode, pd.detailparticular detailparticular,  (IF(SUM(pd.issueqty) IS NULL, 0, SUM(pd.issueqty)) - IF(dr.dr_qty IS NULL, 0, dr.dr_qty)) issueqty, issueuom, unitprice, taxcode, taxrate, taxamount, amount, convissueqty, convuom, conversion, discounttype, discountamount, pd.warehouse warehouse')
								->leftJoin("($subquery) dr ON dr.packing_no = pd.voucherno AND dr.companycode = pd.companycode AND dr.itemcode = pd.itemcode AND dr.warehouse = pd.warehouse")
								->setWhere("pd.voucherno = '$voucherno'")
								->setGroupBy('pd.itemcode, linenum, pd.warehouse')
								->setHaving('issueqty > 0')
								->runSelect()
								->getResult();

		return $result;
	}

	public function getPackingHeader($fields, $voucherno) {
		$result = $this->db->setTable('packinglist')
						->setFields($fields)
						->setWhere("voucherno = '$voucherno'")
						->runSelect()
						->getRow();

		return $result;
	}

	public function getDocumentInfo($voucherno) {
		$result = $this->db->setTable('deliveryreceipt dr')
							->innerJoin('partners p ON p.partnercode = dr.customer AND p.companycode = dr.companycode')
							->leftJoin('packinglist pl ON pl.voucherno = dr.packing_no AND dr.companycode = pl.companycode')
							->leftJoin('salesorder so ON so.voucherno = pl.source_no AND pl.companycode = so.companycode')
							->setFields("dr.transactiondate documentdate, dr.voucherno voucherno, p.partnername company, CONCAT(p.first_name, ' ', p.last_name) customer, so.voucherno referenceno, dr.remarks remarks, partnercode, dr.packing_no")
							->setWhere("dr.voucherno = '$voucherno'")
							->runSelect()
							->getRow();

		return $result;
	}

	public function getDocumentContent($voucherno) {
		$result = $this->db->setTable('deliveryreceipt_details drd')
							->setFields("itemcode 'Item Code', detailparticular 'Description', issueqty 'Quantity', UPPER(issueuom) 'UOM'")
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