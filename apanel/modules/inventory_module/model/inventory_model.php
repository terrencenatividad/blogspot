<?php
class inventory_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->reference	= '';
		$this->details		= '';
	}

	public function setReference($reference) {
		$this->reference = $reference;
		return $this;
	}

	public function setDetails($details) {
		$this->details = $details;
		return $this;
	}

	public function generateBalanceTable() {
		$fields = array(
			'companycode',
			'itemcode',
			'warehouse',
			'beginningQty',
			'salesorderQty',
			'deliveredQty',
			'salesinvoiceQty',
			'salesreturnQty',
			'salesscrapQty',
			'purchaseorderQty',
			'purchasereceiptQty',
			'purchasereturnQty',
			'adjustmentsQty',
			'transferedQty'
		);
		$bb = $this->db->setTable('inv_beg_balance b')
						->setFields($this->createFields('bb', 'quantity'))
						->buildSelect();
		// Sales
		$dr_inner = $this->db->setTable('deliveryreceipt a')
							->innerJoin('deliveryreceipt_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
							->setFields($this->createFields('dr', 'linenum, source_no, SUM(convissueqty)'))
							->setWhere("a.stat = 'Prepared' OR a.stat = 'Delivered' OR a.stat = 'With Invoice'")
							->setGroupBy('source_no, itemcode, linenum')
							->buildSelect();

		$so	= $this->db->setTable('salesorder a')
						->innerJoin('salesorder_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
						->leftJoin("($dr_inner) dr ON dr.source_no = a.voucherno AND dr.companycode = a.companycode AND dr.linenum = b.linenum")
						->setFields($this->createFields('so', "IF(a.stat != 'posted', b.convissueqty, dr.dr)"))
						->setWhere("a.stat IN('open', 'partial', 'posted', 'closed')")
						->buildSelect();
						
		$dr = $this->db->setTable('deliveryreceipt a')
						->innerJoin('deliveryreceipt_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
						->setFields($this->createFields('dr', 'convissueqty'))
						->setWhere("a.stat = 'Delivered' OR a.stat = 'With Invoice'")
						->buildSelect();
						
		$si = $this->db->setTable('salesinvoice a')
						->innerJoin('salesinvoice_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
						->setFields($this->createFields('si', 'convissueqty'))
						->setWhere("a.stat = 'open' OR a.stat = 'posted'")
						->buildSelect();
						
		$sr = $this->db->setTable('returns a')
						->innerJoin('returns_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
						->setFields($this->createFields('sr', 'convissueqty'))
						->setWhere("a.stat = 'Returned'")
						->buildSelect();
		
		$xr = $this->db->setTable('returns a')
						->innerJoin('returns_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
						->setFields($this->createFields('xr', 'convissueqty'))
						->setWhere("a.stat = 'Scrapped'")
						->buildSelect();

		// Purchase
		$pr_inner = $this->db->setTable('purchasereceipt a')
							->innerJoin('purchasereceipt_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
							->setFields($this->createFields('pr', 'linenum, source_no, SUM(convreceiptqty)'))
							->setWhere("a.stat = 'Received'")
							->setGroupBy('source_no, itemcode, linenum')
							->buildSelect();

		$po	= $this->db->setTable('purchaseorder a')
						->innerJoin('purchaseorder_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
						->leftJoin("($pr_inner) pr ON pr.source_no = a.voucherno AND pr.companycode = a.companycode AND pr.linenum = b.linenum")
						->setFields($this->createFields('po', "IF(a.stat != 'posted', b.convreceiptqty, pr.pr)"))
						->setWhere("a.stat IN('open', 'partial', 'posted')")
						->buildSelect();
						
		$pr = $this->db->setTable('purchasereceipt a')
						->innerJoin('purchasereceipt_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
						->setFields($this->createFields('pr', 'convreceiptqty'))
						->setWhere("a.stat = 'Received'")
						->buildSelect();
						
		$pt = $this->db->setTable('purchasereturn a')
						->innerJoin('purchasereturn_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
						->setFields($this->createFields('pt', 'convreceiptqty'))
						->setWhere("a.stat = 'Returned'")
						->buildSelect();

		// Inventory
		$ia = $this->db->setTable('inventoryadjustments b')
						->setFields($this->createFields('ia', 'increase + decrease'))
						->buildSelect();

		
		$sts = $this->db->setTable('stock_approval a')
						->innerJoin('stock_approval_details b ON a.stocktransferno = b.stocktransferno AND a.companycode = b.companycode')
						->setFields($this->createFields('st', '-1 * qtytransferred', array('b.warehouse' => 'a.source warehouse')))
						->setWhere("a.stat = 'open'")
						->buildSelect();

		$std = $this->db->setTable('stock_approval a')
						->innerJoin('stock_approval_details b ON a.stocktransferno = b.stocktransferno AND a.companycode = b.companycode')
						->setFields($this->createFields('st', 'qtytransferred', array('b.warehouse' => 'a.destination warehouse')))
						->setWhere("a.stat = 'open'")
						->buildSelect();

		$st = $this->db->setTable("($sts UNION ALL $std) st")
						->setFields('*')
						->buildSelect();

		$inner_query = $bb . ' UNION ALL ' . $so . ' UNION ALL ' . $dr . ' UNION ALL ' . $si . ' UNION ALL ' . $sr . ' UNION ALL ' . $xr;
		$inner_query .= ' UNION ALL ' . $po . ' UNION ALL ' . $pr . ' UNION ALL ' . $pt . ' UNION ALL ' . $ia . ' UNION ALL ' . $st;


		$inner_query = $this->db->setTable("($inner_query) i")
								->setFields('companycode, itemcode ic, warehouse wh, SUM(bb) bb, SUM(so) so, SUM(dr) dr, SUM(si) si, SUM(sr) sr, SUM(xr) xr, SUM(po) po, SUM(pr) pr, SUM(pt) pt, SUM(ia) ia, SUM(st) st')
								->setWhere("warehouse != ''")
								->setGroupBy('warehouse, itemcode')
								->buildSelect();

		$inv_check = $this->db->setTable("($inner_query) i")
								->leftJoin('invdtlfile id ON i.ic = id.itemcode AND i.companycode = id.companycode AND i.wh = id.warehouse')
								->setFields('i.ic itemcode, i.wh warehouse, beginningQty, IFNULL(bb, 0) - IFNULL(beginningQty, 0) bb, salesorderQty, IFNULL(so, 0) - IFNULL(salesorderQty, 0) so, deliveredQty,  IFNULL(dr, 0) - IFNULL(deliveredQty, 0) dr, salesinvoiceQty, IFNULL(si, 0) - IFNULL(salesinvoiceQty, 0) si, salesreturnQty, IFNULL(sr, 0) - IFNULL(salesreturnQty, 0) sr, salesscrapQty, IFNULL(xr, 0) - IFNULL(salesscrapQty, 0) xr, purchaseorderQty, IFNULL(po, 0) - IFNULL(purchaseorderQty, 0) po, purchasereceiptQty,  IFNULL(pr, 0) - IFNULL(purchasereceiptQty, 0) pr, purchasereturnQty, IFNULL(pt, 0) - IFNULL(purchasereturnQty, 0) pt, adjustmentsQty, IFNULL(ia, 0) - IFNULL(adjustmentsQty, 0) ia, transferedQty, IFNULL(st, 0) - IFNULL(transferedQty, 0) st')
								->setHaving('bb != 0 OR so != 0 OR dr != 0 OR si != 0 OR sr != 0 OR xr != 0 OR po != 0 OR pr != 0 OR pt != 0 OR ia != 0 OR st != 0')
								->runSelect()
								->getResult();

		if ($inv_check) {
			$this->log($inv_check);
		}

		if ($inv_check) {
			$result = $this->db->setTable('invdtlfile')
							->setWhere('companycode IS NOT NULL')
							->runDelete();
							
			if ($result) {
				$result = $this->db->setTable('invdtlfile')
									->setFields($fields)
									->setInsertSelect($inner_query)
									->runInsert(false);
				
				if ($result) {
					$result = $this->db->setTable('invfile')
								->setWhere('companycode IS NOT NULL')
								->runDelete();
								
					if ($result) {
						$inner_query = $this->db->setTable('invdtlfile')
												->setFields(
													'companycode,
													itemcode,
													warehouse,
													beginningQty + purchasereceiptQty + salesreturnQty - deliveredQty - purchasereturnQty + adjustmentsQty + transferedQty,
													purchaseorderQty - purchasereceiptQty + purchasereturnQty,
													(beginningQty + purchasereceiptQty + salesreturnQty - deliveredQty - purchasereturnQty + adjustmentsQty + transferedQty) - (salesorderQty - deliveredQty),
													salesorderQty - deliveredQty')
												->buildSelect();

						$result = $this->db->setTable('invfile')
											->setFields('companycode, itemcode, warehouse, onhandQty, orderedQty, availableQty, allocatedQty')
											->setInsertSelect($inner_query)
											->runInsert(false);
					}
				}
			}
		}
	}

	public function recomputePriceAverage() {
		$fields = 'b.itemcode, b.companycode, b.entereddate';
		$bb = $this->db->setTable('inv_beg_balance b')
						->setFields($fields . ", unitprice price, quantity, 'IN' movement, b.voucherno documentno, 'BEG_BAL' doctype")
						->buildSelect();

		$dr = $this->db->setTable('deliveryreceipt a')
						->innerJoin('deliveryreceipt_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
						->setFields($fields . ", (unitprice / conversion) price, convissueqty quantity, 'OUT' movement, a.voucherno documentno, 'DEL_REC' doctype")
						->setWhere("(a.stat = 'Delivered' OR a.stat = 'With Invoice') AND unitprice > 0 AND conversion > 0")
						->buildSelect();
						
		$sr = $this->db->setTable('returns a')
						->innerJoin('returns_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
						->setFields($fields . ", (unitprice / conversion) price, convissueqty quantity, 'IN' movement, a.voucherno documentno, 'INV_RET' doctype")
						->setWhere("a.stat = 'Returned' AND unitprice > 0 AND conversion > 0")
						->buildSelect();
						
		$pr = $this->db->setTable('purchasereceipt a')
						->innerJoin('purchasereceipt_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
						->setFields($fields . ", (unitprice / conversion) price, convreceiptqty quantity, 'IN' movement, a.voucherno documentno, 'PUR_REC' doctype")
						->setWhere("a.stat = 'Received' AND unitprice > 0 AND conversion > 0")
						->buildSelect();
						
		$pt = $this->db->setTable('purchasereturn a')
						->innerJoin('purchasereturn_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
						->setFields($fields . ", (unitprice / conversion) price, convreceiptqty quantity, 'OUT' movement, a.voucherno documentno, 'PUR_RET' doctype")
						->setWhere("a.stat = 'Returned' AND unitprice > 0 AND conversion > 0")
						->buildSelect();

		$ia = $this->db->setTable('inventoryadjustments b')
						->setFields($fields . ", unitprice price, (increase + decrease) quantity, IF((increase + decrease) > 0, 'IN', 'OUT') movement, b.voucherno documentno, 'INV_ADJ' doctype")
						->setWhere('unitprice > 0')
						->buildSelect();

		$inner_query = $bb . ' UNION ALL ' . $dr . ' UNION ALL ' . $sr . ' UNION ALL ' . $pr . ' UNION ALL ' . $pt . ' UNION ALL ' . $ia;
		
		$result = $this->db->setTable("($inner_query) i")
							->setFields('*')
							->setOrderBy('itemcode, entereddate, movement')
							->runSelect()
							->getResult();
		
		$previous_stock_quantity	= array();
		$previous_price_average		= array();
		$values						= array();
		$linenum					= 0;
		foreach ($result as $row) {
			if ( ! isset($previous_stock_quantity[$row->itemcode])) {
				$previous_stock_quantity[$row->itemcode] = 0;
			}
			if ( ! isset($previous_price_average[$row->itemcode])) {
				$previous_price_average[$row->itemcode] = 0;
			}
			$linenum++;
			$previous_quantity	= $previous_stock_quantity[$row->itemcode];
			$previous_average	= $previous_price_average[$row->itemcode];
			$stock_quantity		= $row->quantity * (($row->movement == 'IN') ? 1 : -1);
			if (($previous_quantity + $stock_quantity) == 0) {
				$price_average	= ($previous_quantity * $previous_average) + ($row->price * $stock_quantity);
			} else {
				$price_average	= (($previous_quantity * $previous_average) + ($row->price * $stock_quantity)) / ($previous_quantity + $stock_quantity);
			}
			$price_average		= ($row->movement == 'IN') ? $price_average : $previous_average;
			$values[] = array(
				'linenum'			=> $linenum,
				'companycode'		=> $row->companycode,
				'itemcode'			=> $row->itemcode,
				'documentno'		=> $row->documentno,
				'doctype'			=> $row->doctype,
				'movementdate'		=> $row->entereddate,
				'previous_quantity'	=> $previous_quantity,
				'movement_quantity'	=> $stock_quantity,
				'stock_quantity'	=> $stock_quantity + $previous_quantity,
				'purchase_price'	=> $row->price,
				'price_average'		=> $price_average
			);
			$previous_stock_quantity[$row->itemcode]	= $stock_quantity + $previous_quantity;
			$previous_price_average[$row->itemcode]		= $price_average;
		}

		if ($values) {
			$result = $this->db->setTable('price_average')
								->setWhere('companycode IS NOT NULL')
								->runDelete();

			if ($result) {
				$result = $this->db->setTable('price_average')
									->setValues($values)
									->runInsert(false);
			}
		}
	}

	private function log(array $data) {
		foreach ($data as $row) {
			$itemcode	= $row->itemcode;
			$warehouse	= $row->warehouse;
			foreach ($row as $key => $quantity) {
				$activity	= '';
				$prevqty	= 0;
				if ($quantity > 0 || $quantity < 0) {
					$beginningQty		= $row->beginningQty;
					$salesorderQty		= $row->salesorderQty;
					$deliveredQty		= $row->deliveredQty;
					$salesinvoiceQty	= $row->salesinvoiceQty;
					$salesreturnQty		= $row->salesreturnQty;
					$purchaseorderQty	= $row->purchaseorderQty;
					$purchasereceiptQty	= $row->purchasereceiptQty;
					$purchasereturnQty	= $row->purchasereturnQty;
					$adjustmentsQty		= $row->adjustmentsQty;
					$transferedQty		= $row->transferedQty;

					$prevqty = $beginningQty + $purchasereceiptQty + $salesreturnQty - $deliveredQty - $purchasereturnQty + $adjustmentsQty + $transferedQty;

					switch ($key) {
						case 'bb':
							$activity	= 'Beginning Balance';
							$beginningQty += $quantity;
							break;
						case 'so':
							$activity	= 'Sales Order';
							$salesorderQty += $quantity;
							break;
						case 'dr':
							$activity	= 'Delivery Receipt';
							$deliveredQty += $quantity;
							$quantity = $quantity * -1;
							break;
						case 'si':
							$activity	= 'Sales Invoice';
							$salesinvoiceQty += $quantity;
							break;
						case 'sr':
							$activity	= 'Sales Return';
							$salesreturnQty += $quantity;
							break;
						case 'po':
							$activity	= 'Purchase Order';
							$purchaseorderQty += $quantity;
							break;
						case 'pr':
							$activity	= 'Purchase Receipt';
							$purchasereceiptQty += $quantity;
							break;
						case 'pt':
							$activity	= 'Purchase Return';
							$purchasereturnQty += $quantity;
							$quantity = $quantity * -1;
							break;
						case 'ia':
							$activity	= 'Inventory Adjustment';
							$adjustmentsQty += $quantity;
							break;
						case 'st':
							$activity	= 'Stock Transfer';
							$transferedQty += $quantity;
							break;
					}
					$currentqty = $beginningQty + $purchasereceiptQty + $salesreturnQty - $deliveredQty - $purchasereturnQty + $adjustmentsQty + $transferedQty;
					if ($activity && ($prevqty - $currentqty) != 0) {
						$values = array(
							'itemcode'		=> $itemcode,
							'warehouse'		=> $warehouse,
							'activity'		=> $activity,
							'prevqty'		=> $prevqty,
							'quantity'		=> $quantity,
							'currentqty'	=> $currentqty,
							'reference'		=> $this->reference,
							'details'		=> $this->details
						);
						$this->db->setTable('inventorylogs')
									->setValues($values)
									->runInsert();

						$this->recomputePriceAverage();
					}
				}
			}
		}
	}

	private function createFields($search_field, $value, $fields_replacement = array()) {
		$field_list = array(
			'bb',
			'so',
			'pl',
			'dr',
			'si',
			'sr',
			'xr',
			'po',
			'pr',
			'pt',
			'ia',
			'st'
		);

		$temp = array(
			'b.companycode',
			'b.itemcode',
			'b.warehouse'
		);

		$fields_others = array();

		foreach ($temp as $temp_field) {
			if (array_key_exists($temp_field, $fields_replacement)) {
				$fields_others[] = $fields_replacement[$temp_field];
			} else {
				$fields_others[] = $temp_field;
			}
		}

		$fields = array();

		foreach ($field_list as $field) {
			if ($search_field == $field) {
				$fields[] = "$value $field";
			} else {
				$fields[] = "0 $field";
			}
		}

		return array_merge($fields_others, $fields);
	}

}