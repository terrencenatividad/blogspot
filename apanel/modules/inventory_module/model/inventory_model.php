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
						->innerJoin('deliveryreceipt_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno AND b.parentcode = ""')
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

		//Issue Parts
		$jr = $this->db->setTable('job_release b')
						->setFields($this->createFields('jr', 'quantity'))
						->setWhere("stat = 'released'")
						->buildSelect();

		$inner_query = $bb . ' UNION ALL ' . $so . ' UNION ALL ' . $dr . ' UNION ALL ' . $si . ' UNION ALL ' . $sr . ' UNION ALL ' . $xr;
		$inner_query .= ' UNION ALL ' . $po . ' UNION ALL ' . $pr . ' UNION ALL ' . $pt . ' UNION ALL ' . $ia . ' UNION ALL ' . $st . ' UNION ALL ' . $jr;

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

								// var_dump($inv_check);

		if (empty($inv_check)) {
			$check_transactions = $this->db->setTable("($inner_query) i")
											->setFields('*')
											->setLimit(1)
											->runSelect()
											->getRow();

			if (empty($check_transactions)) {
				$this->db->setTable('invdtlfile')
							->setWhere('companycode IS NOT NULL')
							->runDelete();

				$this->db->setTable('invfile')
							->setWhere('companycode IS NOT NULL')
							->runDelete();
			}
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
			'st',
			'jr'
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

	public function prepareInventoryLog($type, $voucherno = '') {
		$this->log_type = $type;
		$this->voucherno = $voucherno;
		$this->fields = array('itemcode', 'warehouse');
		$this->quantity_field = '';
		if ($type == 'Delivery Receipt') {
			$this->table = 'deliveryreceipt';
			$this->table_detail = 'deliveryreceipt_details';
			$this->quantity_field = 'convissueqty';
			$this->inventory_movement = -1;
		} else if ($type == 'Sales Return') {
			$this->table = 'returns';
			$this->table_detail = 'returns_details';
			$this->quantity_field = 'convissueqty';
			$this->inventory_movement = 1;
		} else if ($type == 'Purchase Receipt') {
			$this->table = 'purchasereceipt';
			$this->table_detail = 'purchasereceipt_details';
			$this->quantity_field = 'convreceiptqty';
			$this->inventory_movement = 1;
		} else if ($type == 'Purchase Return') {
			$this->table = 'purchasereturn';
			$this->table_detail = 'purchasereturn_details';
			$this->quantity_field = 'convreceiptqty';
			$this->inventory_movement = -1;
		} else if ($type == 'Beginning Balance') {
			$this->table = 'inv_beg_balance';
			$this->table_detail = 'inv_beg_balance';
			$this->quantity_field = 'quantity';
			$this->inventory_movement = 1;
		} else if ($type == 'Stock Transfer') {
			$this->table = 'stock_approval';
			$this->table_detail = 'stock_approval_details';
			$this->quantity_field = 'qtytransferred';
			$this->inventory_movement = 1;
		} else if ($type == 'Inventory Adjustment') {
			$this->table = 'inventoryadjustments_header';
			$this->table_detail = 'inventoryadjustments';
			$this->quantity_field = 'increase - decrease';
			$this->inventory_movement = 1;
		} else if ($type == 'Job Release') {
			$this->table = 'job_release';
			$this->table_detail = 'job_release';
			$this->quantity_field = 'quantity';
			$this->inventory_movement = 1;
		}
		else if ($type == 'Job Release Parts') {
			$this->table = 'job_release';
			$this->table_detail = 'job_release';
			$this->quantity_field = 'quantity';
			$this->inventory_movement = -1;
		}
		$this->inventory_log_previous = array();

		if ($this->quantity_field) {
			$this->fields[] = $this->quantity_field;
		}

		return $this;
	}

	private function getValues() {
		if ($this->log_type == 'Stock Transfer') {
			$source			= $this->db->setTable($this->table_detail . ' d')
										->innerJoin($this->table . ' h ON d.stocktransferno = h.stocktransferno AND d.companycode = h.companycode')
										->setFields(array('d.itemcode', 'h.source warehouse', 'd.qtytransferred * - 1', 'qtytransferred'))
										->setWhere("h.stocktransferno = '{$this->voucherno}'")
										->buildSelect();

			$destination	= $this->db->setTable($this->table_detail . ' d')
										->innerJoin($this->table . ' h ON d.stocktransferno = h.stocktransferno AND d.companycode = h.companycode')
										->setFields(array('d.itemcode', 'h.source', 'd.qtytransferred', ' qtytransferred'))
										->setWhere("h.stocktransferno = '{$this->voucherno}'")
										->buildSelect();

			$query = $source . ' UNION ' . $destination;

			$result = $this->db->setTable("($query) a")
								->setFields('*')
								->runSelect(false)
								->getResult();

		} else if($this->log_type == 'Job Release') {
			$result = $this->db->setTable($this->table_detail. ' j')
								->setFields('j.itemcode, j.warehouse, j.quantity')
								->leftJoin('job_order_details jod ON jod.job_order_no = j.job_order_no  and jod.itemcode = j.itemcode')
								->setWhere("job_release_no = '{$this->voucherno}' AND (parentcode = '' OR parentcode IS NULL) AND isbundle = 'yes'")
								->runSelect()
								->getResult();
		
		} else if($this->log_type == 'Job Release Parts') {
			$result = $this->db->setTable($this->table_detail. ' j')
								->setFields('j.itemcode, j.warehouse, j.quantity')
								->leftJoin('job_order_details jod ON jod.job_order_no = j.job_order_no  and jod.itemcode = j.itemcode')
								->setWhere("job_release_no = '{$this->voucherno}' AND isbundle = 'no'")
								->runSelect()
								->getResult();
			
		}else {
			$result = $this->db->setTable($this->table_detail)
								->setFields($this->fields)
								->setWhere("voucherno = '{$this->voucherno}'")
								->runSelect()
								->getResult();
		}

		return $result;
	}

	public function preparePreviousValues() {
		$result = $this->getValues();
		
		foreach ($result as $row) {
			if ( ! isset($this->inventory_log_previous[$row->itemcode])) {
				$this->inventory_log_previous[$row->itemcode][$row->warehouse] = 0;
			} else if ( ! isset($this->inventory_log_previous[$row->itemcode][$row->warehouse])) {
				$this->inventory_log_previous[$row->itemcode][$row->warehouse] = 0;
			} 

			$this->inventory_log_previous[$row->itemcode][$row->warehouse] += $row->{$this->quantity_field};
		}

		return $this;
	}

	public function computeValues() {
		$result = $this->getValues();

		$current_values = array();

		foreach ($result as $row) {
			if ( ! isset($current_values[$row->itemcode])) {
				$current_values[$row->itemcode][$row->warehouse] = 0;
			} else if ( ! isset($current_values[$row->itemcode][$row->warehouse])) {
				$current_values[$row->itemcode][$row->warehouse] = 0;
			} 

			$current_values[$row->itemcode][$row->warehouse] += $row->{$this->quantity_field};
		}
		$current_values;

		foreach ($this->inventory_log_previous as $itemcode => $row) {
			foreach ($row as $warehouse => $quantity) {
				if ( ! isset($current_values[$itemcode][$warehouse])) {
					$current_values[$itemcode][$warehouse] = 0;
				}
				$current_values[$itemcode][$warehouse] -= $quantity;
			}
		}

		$this->current_values = $current_values;
		
		return $this;
	}

	public function logChanges($status = '') {
		if ($status == 'Cancelled') {
			$this->inventory_movement = $this->inventory_movement * -1;
		}

		$logs = array();

		foreach ($this->current_values as $itemcode => $row) {
			foreach ($row as $warehouse => $quantity) {
				$quantity = $quantity * $this->inventory_movement;

				$current_quantity = $this->getCurrentQuantity($itemcode, $warehouse);

				if ($quantity != 0) {
					$logs[] = array(
						'itemcode'		=> $itemcode,
						'warehouse'		=> $warehouse,
						'quantity'		=> $quantity,
						'prevqty'		=> $current_quantity,
						'currentqty'	=> $current_quantity + $quantity,
						'activity'		=> $this->log_type,
						'reference'		=> $this->voucherno,
						'details'		=> $this->details
					);
				}
			}
		}

		if ($logs) {
			$this->db->setTable('inventorylogs')
						->setValues($logs)
						->runInsert();
						
			$this->recomputePriceAverage();
		}
	}

	private function getCurrentQuantity($itemcode, $warehouse) {
		$result = $this->db->setTable('inventorylogs')
							->setFields('currentqty')
							->setWhere("itemcode = '$itemcode' AND warehouse = '$warehouse'")
							->setOrderBy('entereddate desc')
							->setLimit(1)
							->runSelect()
							->getRow();

		return ($result) ? $result->currentqty : 0;
	}

}