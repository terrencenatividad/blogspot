<?php
class purchase_receipt_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->log = new log();
	}

	public function savePurchaseReceipt($data, $data2) {
		$this->getAmounts($data, $data2);

		$result = $this->db->setTable('purchasereceipt')
							->setValues($data)
							->runInsert();

		if ($result) {
			if ($result) {
				$this->log->saveActivity("Create Purchase Receipt [{$data['voucherno']}]");
			}
			$result = $this->updatePurchaseReceiptDetails($data2, $data['voucherno']);
		}

		return $result;
	}

	public function updatePurchaseReceipt($data, $data2, $voucherno) {
		$this->getAmounts($data, $data2);

		$result = $this->db->setTable('purchasereceipt')
							->setValues($data)
							->setWhere("voucherno = '$voucherno'")
							->runUpdate();

		if ($result) {
			if ($result) {
				$this->log->saveActivity("Update Purchase Receipt [$voucherno]");
			}
			$result = $this->updatePurchaseReceiptDetails($data2, $voucherno);
		}

		return $result;
	}

	public function saveSerialNumbers($data, $voucherno){
		$number_of_items = sizeof($data['linenum']);
		$voucherno = $voucherno;
		$source_no = $data['source_no'];

		for ($i = 0 ; $i < ($number_of_items) ; $i++){
			$serialized_flag = $data['item_ident_flag'][$i]; 
			$itemcode = $data['itemcode'][$i];
			$linenum = intval($data['linenum'][$i]);
			$item_quantity = intval($data['receiptqty'][$i]);
			$sn = explode(",",$data['serial_no_list'][$i]);
			$en = explode(",",$data['engine_no_list'][$i]);
			$cn = explode(",",$data['chassis_no_list'][$i]);
			
			// echo $item_quantity." ";

			if ($serialized_flag != '0' && $item_quantity > 0) {
				for ($rowno = 0 ; $rowno < $item_quantity ; $rowno++){
					
					$values = array(
						'voucherno' => $voucherno,
						'source_no' => $source_no,
						'itemcode' => $itemcode,
						'linenum' => $linenum,
						'rowno' => $rowno+1,
						'serialno' => $sn[$rowno],
						'engineno' => $en[$rowno],
						'chassisno' => $cn[$rowno],	
					);
					
					$result = $this->saveSerialToDb($values);
				}
			}
		}
	}

	public function saveSerialToDb($values) {
		$result = $this->db->setTable('items_serialized')
							->setValues($values)
							->runInsert();
		
		return $result;
	}

	private function getAmounts(&$data, &$data2) {
		$this->cleanNumber($data, array('amount', 'netamount', 'discountamount', 'taxamount', 'wtaxamount'));
		$this->cleanNumber($data2, array('receiptqty', 'unitprice', 'taxamount', 'amount'));
		foreach ($data2['itemcode'] as $key => $value) {
			$data2['convreceiptqty'][$key]	= $data2['receiptqty'][$key] * $data2['conversion'][$key];
		}
		$data['amount']		= array_sum($data2['amount']);
		$data['taxamount']	= array_sum($data2['taxamount']);
		$data['netamount']	= $data['amount'] + $data['taxamount'] - intval($data['discountamount']) - intval($data['wtaxamount']);
		// var_dump($data['wtaxamount']);
	}

	public function updatePurchaseReceiptDetails($data, $voucherno) {
		$data['voucherno']	= $voucherno;

		$this->updatePurchaseOrder($voucherno);

		$this->db->setTable('purchasereceipt_details')
					->setWhere("voucherno = '$voucherno'")
					->runDelete();
		
		$result = $this->db->setTable('purchasereceipt_details')
							->setValuesFromPost($data)
							->runInsert();

		$this->updatePurchaseOrder($voucherno);
							
		return $result;
	}

	public function deletePurchaseReceipt($data) {
		$ids	= "'" . implode("','", $data) . "'";
		$result	= $this->db->setTable('purchasereceipt')
							->setValues(array('stat'=>'Cancelled'))
							->setWhere("voucherno IN ($ids)")
							->setLimit(count($data))
							->runUpdate();
		if ($result) {
			if ($result) {
				$log_id = implode(', ', $data);
				$this->log->saveActivity("Cancelled Purchase Receipt [$log_id]");
			}
			$result = $this->db->setTable('purchasereceipt_details')
							->setValues(array('stat'=>'Cancelled'))
							->setWhere("voucherno IN ($ids)")
							->setLimit(count($data))
							->runUpdate();

			if ($result) {
				foreach ($data as $voucherno) {
					$this->updatePurchaseOrder($voucherno);
				} 
			}
		}

		return $result;
	}

	private function getSourceNo($voucherno) {
		$result = $this->db->setTable('purchasereceipt')
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

	public function getTransactionType($source_no) {
		$query_po = $this->db->setTable('purchaseorder po')
							->setFields('po.transtype transtype')
							->setWhere("po.voucherno = '$source_no'")
							->buildSelect();

		$query_ipo = $this->db->setTable('import_purchaseorder ipo')
							->setFields('ipo.transtype transtype')
							->setWhere("ipo.voucherno = '$source_no'")
							->buildSelect();

		$query = $query_po .' UNION ALL '. $query_ipo;

		$result = $this->db->setTable("($query) i")
							->setFields(array('transtype'))
							->runSelect(FALSE)
							->getRow();

		return $result->transtype;
	}

	private function updatePurchaseOrder($voucherno) {
		$source_no	= $this->getSourceNo($voucherno);

		$transtype = $this->getTransactionType($source_no);

		$subquery	= $this->db->setTable('purchasereceipt pr')
								->setFields('IF(SUM(prd.receiptqty) IS NULL, 0, SUM(prd.receiptqty)) pr_qty, source_no, pr.companycode companycode')
								->innerJoin('purchasereceipt_details prd ON prd.voucherno = pr.voucherno AND prd.companycode = pr.companycode')
								->setWhere("pr.stat != 'Cancelled' AND source_no = '$source_no'")
								->setGroupBy('pr.source_no')
								->buildSelect();
								
		if ($transtype == 'IPO') {
			$result		= $this->db->setTable('import_purchaseorder_details ipod')
									->setFields('ipo.voucherno voucherno, ipo.transactiondate transactiondate, ipo.netamount netamount, SUM(ipod.receiptqty) quantity, COALESCE(pr.pr_qty, 0, pr.pr_qty) quantity_x')
									->innerJoin('import_purchaseorder ipo ON ipod.voucherno = ipo.voucherno AND ipod.companycode = ipo.companycode')
									->leftJoin("($subquery) pr ON pr.source_no = ipod.voucherno AND pr.companycode = ipod.companycode")
									->setWhere("ipo.stat IN ('open','partial', 'posted') AND ipo.voucherno = '$source_no'")
									->setGroupBy('ipo.voucherno')
									->runSelect()
									->getRow();	
									// echo $this->db->getQuery();
								
		} elseif ($transtype == 'PO') {
			$result		= $this->db->setTable('purchaseorder_details pod')
									->setFields('po.voucherno voucherno, po.transactiondate transactiondate, po.netamount netamount, SUM(pod.receiptqty) quantity, COALESCE(pr.pr_qty, 0, pr.pr_qty) quantity_x')
									->innerJoin('purchaseorder po ON pod.voucherno = po.voucherno AND pod.companycode = po.companycode')
									->leftJoin("($subquery) pr ON pr.source_no = pod.voucherno AND pr.companycode = pod.companycode")
									->setWhere("po.stat IN ('open','partial', 'posted') AND po.voucherno = '$source_no'")
									->setGroupBy('po.voucherno')
									->runSelect()
									->getRow();	
		}
		

		if ($result) {
			$status = 'open';
			if ($result->quantity_x == 0) {
				$status = 'open';
			} else if ($result->quantity > $result->quantity_x) {
				$status = 'partial';
			} else if ($result->quantity = $result->quantity_x) {
				$status = 'posted';
			}
			
			$table = '';
			$table = ($transtype == 'IPO') ? 'import_purchaseorder' : 'purchaseorder';
			
			$result = $this->db->setTable("$table")
								->setFields('voucherno, stat')
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
					->setOrderBy('taxrate')
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


	public function tagAsDelivered($data) {
		$ids	= "'" . implode("','", $data) . "'";
		$result	= $this->db->setTable('purchasereceipt')
							->setValues(array('stat'=>'Delivered'))
							->setWhere("voucherno IN ($ids)")
							->setLimit(count($data))
							->runUpdate();
		if ($result) {
			$result = $this->db->setTable('purchasereceipt_details')
							->setValues(array('stat'=>'Delivered'))
							->setWhere("voucherno IN ($ids)")
							->setLimit(count($data))
							->runUpdate();
		}

		return $result;
	}

	public function getPurchaseReceiptPagination($search, $sort, $vendor, $filter, $datefilter) {
		$sort = ($sort) ? $sort : 'transactiondate desc';
		$condition = "pr.stat != 'temporary'";
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('voucherno','vendor','source_no','invoiceno'));
		}
		if ($filter && $filter != 'all') {
			$condition .= " AND pr.stat = '$filter'";
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
		$result = $this->db->setTable("purchasereceipt pr")
							->innerJoin('partners po ON po.partnercode = pr.vendor AND po.companycode = pr.companycode AND po.partnertype = "supplier"')
							->setFields("transactiondate, voucherno, source_no, partnername vendor, netamount, pr.stat stat, invoiceno")
							->setWhere($condition)
							->setOrderBy($sort)
							->runPagination();
		return $result;
	}

	public function getPurchaseReceiptById($fields, $voucherno) {
		$result = $this->db->setTable('purchasereceipt')
							->setFields($fields)
							->setWhere("voucherno = '$voucherno'")
							->setLimit(1)
							->runSelect()
							->getRow();
		
		return $result;
	}

	public function getPurchaseReceiptDetails($fields, $voucherno, $view = true) {
		if ($view) {
			$result = $this->db->setTable('purchasereceipt_details')
								->setFields($fields)
								->setWhere("voucherno = '$voucherno'")
								->setOrderBy('linenum')
								->runSelect()
								->getResult();
			
		} else {
			$sourceno = $this->db->setTable('purchasereceipt')
								->setFields('source_no')
								->setWhere("voucherno = '$voucherno'")
								->runSelect()
								->getRow();

			$sourceno = ($sourceno) ? $sourceno->source_no : '';

			$result1 = $this->db->setTable('purchasereceipt_details')
								->setFields($fields)
								->setWhere("voucherno = '$voucherno'")
								->setOrderBy('linenum')
								->runSelect()
								->getResult();
								
			$result = $this->getPurchaseOrderDetails($sourceno, $voucherno);

			$checker	= array();
			foreach ($result1 as $key => $row) {
				$checker[$row->linenum] = (object) $row;
			}

			foreach ($result as $key => $row) {
				$result[$key]->receiptqty = (isset($checker[$row->linenum])) ? $checker[$row->linenum]->receiptqty : 0;
				if (isset($checker[$row->linenum])) {
					$result[$key]->taxcode = $checker[$row->linenum]->taxcode;
					$result[$key]->taxrate = $checker[$row->linenum]->taxrate;
					$result[$key]->taxamount = $checker[$row->linenum]->taxamount;
					$result[$key]->amount = $checker[$row->linenum]->amount;
				}
			}
		}
		
		return $result;
		
	}

	public function getVendorList() {
		$result = $this->db->setTable('partners')
						->setFields("partnercode ind, CONCAT(partnercode,' - ',partnername) val")
						->setWhere("partnercode != '' AND partnertype = 'supplier' AND stat = 'active'")
						->setOrderBy("val")
						->runSelect()
						->getResult();

		return $result;
	}

	public function getWarehouseList($data) {
		// var_dump($data);
		if($data['ajax_task'] == 'ajax_edit'){
			$warehouse = $data['warehouse'];			
			$cond = "stat = 'active' OR warehousecode = '$warehouse'";
		}else{
			$cond = "stat = 'active'";
		}
		$result = $this->db->setTable('warehouse')
						->setFields("warehousecode ind, description val, stat stat")
						->setWhere($cond)
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

	public function getPurchaseOrderPagination($vendor = '', $search = '') {
		$condition = '';
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('voucherno', 'remarks'));
		}
		if ($vendor != '') {
			 $condition .= " AND vendor = '$vendor'";
		}

		$subquery	= $this->db->setTable('purchasereceipt pr')
								// ->setFields('IF(prd.receiptqty IS NULL, 0, prd.receiptqty) pr_qty, source_no, pr.companycode companycode')
								->setFields('IF(SUM(prd.receiptqty) IS NULL, 0, SUM(prd.receiptqty)) pr_qty, source_no, pr.companycode companycode')
								->innerJoin('purchasereceipt_details prd ON prd.voucherno = pr.voucherno AND prd.companycode = pr.companycode')
								->setWhere("pr.stat != 'Cancelled'")
								->setGroupBy('pr.source_no')
								->buildSelect();
							

		$query_po		= $this->db->setTable('purchaseorder_details pod')
								// ->setFields('po.voucherno voucherno, po.transactiondate transactiondate, remarks, po.netamount netamount, (IF(SUM(pod.receiptqty) IS NULL, 0, SUM(pod.receiptqty)) - IF(pr.pr_qty IS NULL, 0, pr.pr_qty)) qtyleft, po.vendor vendor')
								->setFields('po.voucherno voucherno, po.transactiondate transactiondate, remarks, po.netamount netamount, (IF(pod.receiptqty IS NULL, 0, pod.receiptqty) - IF(pr.pr_qty IS NULL, 0, pr.pr_qty)) qtyleft, po.vendor vendor')
								->innerJoin('purchaseorder po ON pod.voucherno = po.voucherno AND pod.companycode = po.companycode')
								->leftJoin("($subquery) pr ON pr.source_no = pod.voucherno AND pr.companycode = pod.companycode")
								->setWhere("po.stat IN ('open','partial')" . $condition)
								// ->setGroupBy('po.voucherno')
								// ->setHaving('qtyleft > 0')
								->buildSelect();
								// ->runPagination();
								
		$query_ipo		= $this->db->setTable('import_purchaseorder_details ipod')
								// ->setFields('ipo.voucherno voucherno, ipo.transactiondate transactiondate, remarks, ipo.netamount netamount, (IF(SUM(ipod.receiptqty) IS NULL, 0, SUM(ipod.receiptqty)) - IF(pr.pr_qty IS NULL, 0, pr.pr_qty)) qtyleft, ipo.vendor vendor')
								->setFields('ipo.voucherno voucherno, ipo.transactiondate transactiondate, remarks, ipo.netamount netamount, (IF(ipod.receiptqty IS NULL, 0, ipod.receiptqty) - IF(pr.pr_qty IS NULL, 0, pr.pr_qty)) qtyleft, ipo.vendor vendor')
								->innerJoin('import_purchaseorder ipo ON ipod.voucherno = ipo.voucherno AND ipod.companycode = ipo.companycode')
								->leftJoin("($subquery) pr ON pr.source_no = ipod.voucherno AND pr.companycode = ipod.companycode")
								->setWhere("ipo.stat IN ('open','partial')" . $condition)
								// ->setGroupBy('ipo.voucherno')
								// ->setHaving('qtyleft > 0')
								->buildSelect();
								// ->runPagination()
								// echo $this->db->getQuery();;
		
		$query	= $query_po .' UNION ALL '. $query_ipo ;

		$result = $this->db->setTable("($query) i")
							->setFields('i.voucherno, i.transactiondate, i.remarks, i.netamount, i.qtyleft, i.vendor')
							->setGroupBy('i.voucherno')
							->setHaving('i.qtyleft > 0')
							->runPagination(FALSE);
							
		return $result;
	}

	public function getPurchaseOrderDetails($voucherno, $voucherno_ref = false) {
		$transtype = $this->getTransactionType($voucherno);
		

		if ($transtype == 'PO'){
			$result1		= $this->db->setTable('purchaseorder_details pod')
									->setFields("pod.itemcode, detailparticular, linenum, receiptqty, receiptqty maxqty, pod.warehouse, receiptuom, unitprice, 'none' taxcode, taxrate, pod.taxamount, pod.amount, convreceiptqty, convuom, conversion, item_ident_flag")
									->innerJoin('purchaseorder po ON pod.voucherno = po.voucherno AND pod.companycode = po.companycode')
									->innerJoin('items i ON i.itemcode = pod.itemcode')
									->setWhere("po.voucherno = '$voucherno'")
									->runSelect()
									->getResult();
		} else {
			$result1		= $this->db->setTable('import_purchaseorder_details ipod')
									->setFields("ipod.itemcode, detailparticular, linenum, receiptqty, receiptqty maxqty, ipod.warehouse, receiptuom, unitprice, 'none' taxcode, taxrate, ipod.taxamount, ipod.amount, convreceiptqty, convuom, conversion, item_ident_flag")
									->innerJoin('import_purchaseorder ipo ON ipod.voucherno = ipo.voucherno AND ipod.companycode = ipo.companycode')
									->innerJoin('items i ON i.itemcode = ipod.itemcode')
									->setWhere("ipo.voucherno = '$voucherno'")
									->runSelect()
									->getResult();
		}
					
		$addcond = ($voucherno_ref) ? " AND pr.voucherno != '$voucherno_ref'" : '';

		
		$result2		= $this->db->setTable('purchasereceipt_details prd')
								->setFields("itemcode, linenum, SUM(receiptqty) receiptqty, prd.warehouse")
								->innerJoin('purchasereceipt pr ON prd.voucherno = pr.voucherno AND prd.companycode = pr.companycode')
								->setWhere("source_no = '$voucherno' AND pr.stat != 'Cancelled'" . $addcond)
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
			
			if ($add_result) {
				$result[] = $row;
			}
		}
		
		return $result;
	}

	public function getImportPurchaseOrderDetails($voucherno, $voucherno_ref = false) {
		$result1		= $this->db->setTable('import_purchaseorder_details ipod')
								->setFields("itemcode, detailparticular, linenum, receiptqty, receiptqty maxqty, ipod.warehouse, receiptuom, unitprice, 'none' taxcode, taxrate, ipod.taxamount, ipod.amount, convreceiptqty, convuom, conversion")
								->innerJoin('import_purchaseorder ipo ON ipod.voucherno = ipo.voucherno AND ipod.companycode = ipo.companycode')
								->setWhere("ipo.voucherno = '$voucherno'")
								->runSelect()
								->getResult();

		$addcond = ($voucherno_ref) ? " AND pr.voucherno != '$voucherno_ref'" : '';

		$result2		= $this->db->setTable('purchasereceipt_details prd')
								->setFields("itemcode, linenum, SUM(receiptqty) receiptqty, prd.warehouse")
								->innerJoin('purchasereceipt pr ON prd.voucherno = pr.voucherno AND prd.companycode = pr.companycode')
								->setWhere("source_no = '$voucherno' AND pr.stat != 'Cancelled'" . $addcond)
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
			
			if ($add_result) {
				$result[] = $row;
			}
		}

		return $result;
	}

	public function getPurchaseOrderHeader($fields, $voucherno) {
		$result = $this->db->setTable('purchaseorder')
						->setFields($fields)
						->setWhere("voucherno = '$voucherno'")
						->runSelect()
						->getRow();

		return $result;
	}

	public function getDocumentInfo($voucherno) {
		$result = $this->db->setTable('purchasereceipt pr')
							->innerJoin('partners p ON p.partnercode = pr.vendor AND p.companycode = pr.companycode AND p.partnertype = "supplier"')
							->setFields("pr.transactiondate documentdate, pr.voucherno voucherno, p.partnername company, CONCAT(p.first_name, ' ', p.last_name) vendor, source_no referenceno, pr.remarks remarks, partnercode, wtaxamount wtax, amount, discounttype disctype, discountamount discount, discountrate, netamount net, taxamount vat, wtaxrate")
							->setWhere("voucherno = '$voucherno'")
							->runSelect()
							->getRow();

		return $result;
	}

	public function getDocumentContent($voucherno) {
		$result = $this->db->setTable('purchasereceipt_details prd')
							->setFields("itemcode 'Item Code', detailparticular 'Description', receiptqty 'Quantity', UPPER(receiptuom) 'UOM', unitprice 'Price', taxamount 'Tax', amount 'Amount', prd.taxrate")
							->leftJoin('fintaxcode f ON prd.taxcode = f.fstaxcode AND f.companycode = prd.companycode')
							->setWhere("voucherno = '$voucherno'")
							->runSelect()
							->getResult();

		return $result;
	}

	public function getVendorDetails($partnercode) {
		$result = $this->db->setTable('partners')
							->setFields(array('partnername vendor', 'address1 address', 'tinno', 'terms', 'mobile contactno'))
							->setWhere("partnercode = '$partnercode' AND partnertype = 'supplier'")
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

	public function getSerialNoFromDb() {
		$result = $this->db->setTable('items_serialized i')
							->setFields('serialno, engineno, chassisno')
							// ->setOrderBy('serialno')
							->runSelect()
							->getResult();
		
		return $result;
	}

}