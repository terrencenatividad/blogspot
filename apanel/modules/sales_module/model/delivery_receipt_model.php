<?php
class delivery_receipt_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->log = new log();
	}

	public function createClearingEntries($voucherno) {
		$exist = $this->db->setTable('journalvoucher')
							->setFields('voucherno')
							->setWhere("referenceno = '$voucherno'")
							->setLimit(1)
							->runSelect()
							->getRow();

		$jvvoucherno = ($exist) ? $exist->voucherno : '';

		$header_fields = array(
			'voucherno referenceno',
			'customer partner',
			'transactiondate',
			'fiscalyear',
			'period',
			'stat'
		);
		$detail_fields = array(
			'IF(i.inventory_account > 0, i.inventory_account, ic.inventory_account) accountcode',
			'SUM(IFNULL(price_average, 0) * drd.issueqty) credit'
		);

		$data	= (array) $this->getDeliveryReceiptById($header_fields, $voucherno);

		$average_query = $this->db->setTable('price_average p1')
									->setFields('p1.*')
									->leftJoin('price_average p2 ON p1.itemcode = p2.itemcode AND p1.linenum < p2.linenum')
									->setWhere('p2.linenum IS NULL')
									->buildSelect();
		
		$details = $this->db->setTable('deliveryreceipt_details drd')
							->setFields($detail_fields)
							->innerJoin('items i ON i.itemcode = drd.itemcode AND i.companycode = drd.companycode')
							->leftJoin('itemclass ic ON ic.id = i.classid AND ic.companycode = i.companycode')
							->leftJoin("($average_query) ac ON ac.itemcode = drd.itemcode")
							->setWhere("drd.voucherno = '$voucherno'")
							->setGroupBy('accountcode')
							->runSelect()
							->getResult();

		$dr_stat = ($data) ? $data['stat'] : '';
		$data['stat'] = 'posted';

		if ($dr_stat == 'Cancelled') {
			$cancel_data = array('stat' => 'cancelled');

			$this->db->setTable('journalvoucher')
						->setValues($cancel_data)
						->setWhere("voucherno = '$jvvoucherno'")
						->setLimit(1)
						->runUpdate();

			$fields = array(
				'voucherno',
				'transtype',
				'accountcode',
				'debit credit',
				'credit debit',
				'stat',
				'converteddebit convertedcredit',
				'convertedcredit converteddebit',
				'detailparticulars'
			);

			$detail = $this->db->setTable('journaldetails')
								->setFields($fields)
								->setWhere("voucherno = '$jvvoucherno'")
								->runSelect()
								->getResult();

			$linenum = count($detail);

			foreach ($detail as $key => $row) {
				$linenum++;
				$detail[$key]->linenum = $linenum;

				$detail[$key] = (array) $detail[$key];
			}

			$this->db->setTable('journaldetails')
								->setValues($detail)
								->runInsert();

			$this->db->setTable('journaldetails')
					->setValues($cancel_data)
					->setWhere("voucherno = '$jvvoucherno'")
					->runUpdate();

			return true;
		}

		$result = false;
		
		$data['amount']				= 0;
		$data['convertedamount']	= 0;

		if ( ! $exist) {
			$seq					= new seqcontrol();
			$jvvoucherno			= $seq->getValue('JV');
			$data['voucherno']		= $jvvoucherno;
			$data['transtype']		= 'JV';
			$data['currencycode']	= 'PHP';
			$data['exchangerate']	= '1';
		}

		$header = $this->db->setTable('journalvoucher')
							->setValues($data);

		if ($exist) {
			$result = $header->setWhere("voucherno = '$jvvoucherno'")
							->setLimit(1)
							->runUpdate();
		} else {
			$result = $header->runInsert();
		}
		
		if ($result) {
			$this->db->setTable('journaldetails')
					->setWhere("voucherno = '$jvvoucherno'")
					->runDelete();


			$ftax = $this->db->setTable('fintaxcode')
								->setFields('salesAccount account')
								->setWhere("fstaxcode = 'IC'")
								->setLimit(1)
								->runSelect()
								->getRow();

			$clearing_account = ($ftax) ? $ftax->account : '';
			$total_amount	= 0;
			
			if ($details && $clearing_account) {
				$linenum		= array();
				
				foreach ($details as $key => $row) {
					$details[$key]->linenum				= $key + 1;
					$details[$key]->voucherno			= $jvvoucherno;
					$details[$key]->transtype			= 'IT';
					$details[$key]->debit				= 0;
					$details[$key]->converteddebit		= 0;
					$details[$key]->convertedcredit		= $row->credit;
					$details[$key]->detailparticulars	= '';
					$details[$key]->stat				= $data['stat'];

					$details[$key]	= (array) $details[$key];
					$total_amount	+= $row->credit;
				}

				$details[] = array(
					'accountcode'		=> $clearing_account,
					'credit'			=> 0,
					'linenum'			=> $key + 2,
					'voucherno'			=> $jvvoucherno,
					'transtype'			=> 'IT',
					'debit'				=> $total_amount,
					'converteddebit'	=> $total_amount,
					'convertedcredit'	=> 0,
					'detailparticulars'	=> '',
					'stat'				=> $data['stat']
				);
			}
			$detail_insert  = false;
			$detail_insert = $this->db->setTable('journaldetails')
										->setValues($details)
										->runInsert();

			if ($detail_insert) {
				$data = array(
					'amount'			=> $total_amount,
					'convertedamount'	=> $total_amount
				);
				$result = $this->db->setTable('journalvoucher')
									->setValues($data)
									->setWhere("voucherno = '$jvvoucherno'")
									->setLimit(1)
									->runUpdate();

			}
		}

		return $result;
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

	public function getSerials($id) {
		$result = $this->db->setTable('items_serialized')
			->setFields('serialno, engineno, chassisno')
			->setWhere("id='$id'")
			->runSelect()	
			->getRow();

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
	
		foreach ($data['serialnumbers'] as $row) {
			if ($row != "") {
				$ids = explode(",", $row);
				foreach ($ids as $id) {
					//$getData = $this->getSerials($id);
						
					// $itemcode = $row['itemcode'];
					// $itemlinenum = $row['linenum'];

					// $s_data['voucherno'] = $voucherno;
					// $s_data['serialno'] = $getData->serialno;
					// $s_data['engineno'] = $getData->engineno;
					// $s_data['chassisno'] = $getData->chassisno;

					// $this->db->setTable('dr_serialized')
					// 		->setWhere("voucherno = '$voucherno' AND itemcode = '$itemcode' AND itemlinenum = '$itemlinenum'")
					// 		->runDelete();

					// $this->db->setTable('dr_serialized')
					// 					->setValues($s_data)
					// 					->runInsert();

					$this->db->setTable('items_serialized')
										->setValues(array('stat'=>'Not Available'))
										->setWhere("id = '$id'")
										->runUpdate();
				}
			}
		}

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
							->innerJoin('partners p ON p.partnercode = dr.customer AND p.companycode = dr.companycode AND p.partnertype = "customer"')
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
				$result[$key]->serialnumbers = (isset($checker[$row->linenum])) ? $checker[$row->linenum]->serialnumbers : 0;
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

	public function getWarehouseList($data) {
		$warehouse = $data['warehouse'];
		$result = $this->db->setTable('warehouse')
						->setFields("warehousecode ind, description val")
						->setWhere("stat = 'active' OR warehousecode = '$warehouse'")
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
								->setFields('so.voucherno, so.transactiondate transactiondate, remarks, so.netamount netamount, (IF(SUM(sod.issueqty) IS NULL, 0, SUM(sod.issueqty)) - IF(dr.pr_qty IS NULL, 0, dr.pr_qty)) qtyleft, so.customer, so.s_address')
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
								->setFields("sod.itemcode, detailparticular, linenum, issueqty, issueqty maxqty, sod.warehouse, issueuom, unitprice, sod.taxcode, taxrate, sod.taxamount, sod.amount, convissueqty, convuom, conversion, FLOOR(COALESCE(inv.onhandQty, 0) / conversion) available, sod.discounttype, sod.discountrate, sod.discountamount, sod.parentcode, sod.bundle_itemqty, sod.parentline, i.item_ident_flag")
								->innerJoin('salesorder so ON sod.voucherno = so.voucherno AND sod.companycode = so.companycode')
								->leftJoin('items i ON i.itemcode = sod.itemcode')
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
							->innerJoin('partners p ON p.partnercode = dr.customer AND p.companycode = dr.companycode AND p.partnertype = "customer"')
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
							->setWhere("partnercode = '$partnercode' AND partnertype = 'customer'")
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

	public function getSerialList($fields, $itemcode, $search) {
		$condition = '';
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('serialno', 'engineno', 'chassisno'));
		}
		$result	= $this->db->setTable('items_serialized')
								->setFields($fields)
								->setWhere("itemcode = '$itemcode'" .$condition)
								->setOrderBy('voucherno, linenum, rowno')
								->runPagination();
								
		return $result;
	}

	public function getDRSerials($itemcode, $voucherno, $linenum) {
		$result = $this->db->setTable('deliveryreceipt_details') 
						->setFields('serialnumbers')
						->setWhere("itemcode='$itemcode' AND voucherno='$voucherno' AND linenum='$linenum'")
						->runSelect()
						->getRow();

		return $result;
	}

	public function UpdateItemsSerialized($voucherno) {
		$result = $this->db->setTable('deliveryreceipt_details')
						->setFields("GROUP_CONCAT(serialnumbers ORDER BY linenum ASC SEPARATOR ',') as serialnumbers")
						->setWhere("serialnumbers != '' AND stat NOT IN ('Cancelled','temporary')")
						->runSelect()
						->getRow();
		
		$ids = preg_split("/[\s,]+/", $result->serialnumbers);
		$serials = implode(",",$ids);
		if ($serials != "") {
			$this->db->setTable('items_serialized')
							->setValues(array('stat'=>'Available'))
							->setWhere("id NOT IN($serials)")
							->runUpdate();
		}
	}

}