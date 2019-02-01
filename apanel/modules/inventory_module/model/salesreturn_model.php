<?php
	class salesreturn_model extends wc_model
	{
		public function __construct() {
			parent::__construct();
			$this->log = new log();
		}

		public function getCustomerList() {

			$result = $this->db->setTable('partners')
							->setFields("partnercode ind, partnername val, stat stat")
							->setWhere("stat = 'active' AND partnertype='customer'")
							->runSelect()
							->getResult();

			return $result;
		}

		public function getWarehouseList($current="") {
		
			$add_cond 	=	(isset($current) && $current != "") ? " AND warehousecode != '$current' " 	: 	"";

			$result = $this->db->setTable('warehouse')
							->setFields("warehousecode ind, description val, stat stat")
							->setWhere("stat = 'active' $add_cond")
							->runSelect()
							->getResult();

			return $result;
		}

		public function getItemList() {
			$result = $this->db->setTable('items i')
							->setFields("i.itemcode ind, CONCAT(i.itemcode,' - ',i.itemname) val")
							->setWhere("i.stat = 'active'")
							->runSelect()
							->getResult();

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

		public function getSalesReturnPagination($search, $sort, $customer, $filter, $datefilter) {

			$sort = ($sort) ? $sort : 'transactiondate desc voucherno desc';
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
			$result = $this->db->setTable("inventory_salesreturn r")
								->innerJoin('partners p ON p.partnercode = r.customer AND p.companycode = r.companycode AND p.partnertype = "customer"')
								->setFields("transactiondate, voucherno, source_no, partnername customer, r.stat stat")
								->setWhere($condition)
								->setOrderBy($sort)
								->runPagination();
			return $result;
		}

		public function getSalesReturn($fields, $voucherno) {
			$result = $this->db->setTable('inventory_salesreturn')
								->setFields($fields)
								->setWhere("voucherno = '$voucherno'")
								->setLimit(1)
								->runSelect()
								->getRow();
			
			return $result;
		}

		public function getSalesReturnDetails($fields, $voucherno, $view = true) {
			if ($view) {
				$result = $this->db->setTable('inventory_salesreturn_details')
									->setFields($fields)
									->setWhere("voucherno = '$voucherno'")
									->setOrderBy('linenum')
									->runSelect()
									->getResult();
			} else {
				$sourceno = $this->db->setTable('inventory_salesreturn')
									->setFields('source_no')
									->setWhere("voucherno = '$voucherno'")
									->runSelect()
									->getRow();

				$sourceno = ($sourceno) ? $sourceno->source_no : '';

				$result1 = $this->db->setTable('inventory_salesreturn_details')
									->setFields($fields)
									->setWhere("voucherno = '$voucherno'")
									->setOrderBy('linenum')
									->runSelect()
									->getResult();

				// $result = $this->getSalesReturnDetails($sourceno, $voucherno);
				// $header = $this->getSalesReturnHeader(array('amount', 'discounttype', 'discountamount'), $sourceno);

				// $checker	= array();
				// foreach ($result1 as $key => $row) {
				// 	$checker[$row->linenum] = (object) $row;
				// }

				// foreach ($result as $key => $row) {
				// 	$result[$key]->issueqty = (isset($checker[$row->linenum])) ? $checker[$row->linenum]->issueqty : 0;
				// }

				// $total_amount	= $header->amount;
				// $total_discount	= 0;
				// $discountrate	= 0;


				// if ($header->discounttype == 'perc') {
				// 	$total_discount	= $total_amount * $header->discountamount / 100;
				// 	$discountrate	= $header->discountamount / 100;
				// } else {
				// 	$total_discount	= $header->discountamount;
				// 	$discountrate	= $total_discount / $total_amount;
				// }
				
				// foreach ($result as $key => $row) {
				// 	$taxamount = $row->unitprice - ($row->unitprice / (1 + $row->taxrate));
				// 	$discount = ($row->unitprice - $taxamount) * $discountrate;
				// 	$result[$key]->unitprice = $row->unitprice - $discount;
				// 	$result[$key]->taxrate = 0;
				// 	$result[$key]->taxamount = 0;
				// }

				$result = $result1;
			}
			return $result;
		}

		public function getSourcePagination($source) {

			$sort = 'transactiondate desc';
			
			if ($source=='Delivery Receipt') {
				$table 	= 'deliveryreceipt';
				$fields = 'voucherno, transactiondate, deliverydate, remarks notes, amount';
				$cond 	= 'stat="Delivered"';
			}
			else{
				$table 	= 'salesinvoice';
				$fields = 'voucherno, transactiondate, remarks notes, amount';
				$cond 	= 'stat="posted"';
			}

			$result = $this->db->setTable($table)
								->setFields($fields)
								->setWhere($cond)
								->setOrderBy($sort)
								->runPagination();

			return $result;
		}

		public function getSource($voucherno) {
			$source = substr($voucherno, 0,2);

			$sql = "(SELECT SUM(srd.issueqty) FROM inventory_salesreturn_details srd LEFT JOIN inventory_salesreturn sr ON sr.voucherno = srd.voucherno WHERE sr.source_no='$voucherno' AND srd.itemcode = tbl.itemcode AND srd.linenum = tbl.linenum)";

			if ($source == 'DR') {
				$table 		= 'deliveryreceipt';
				$fields 	= array(
									'voucherno',
									'customer',
									'transactiondate',
									'remarks',
									'stat',
									'vat_sales',
									'vat_exempt',
									'vat_zerorated',
									'taxamount',
									'netamount',
									'discounttype',
									'discountamount',
									'source_no sourceno'
								);
				
				$table_details 	= 'deliveryreceipt_details tbl';
				$fields_details = array(
									'itemcode',
									'detailparticular',
									'warehouse',
									'linenum',
									'serialnumbers',
									'issueqty - IFNULL('.$sql.', 0) maxqty',
									'issueqty srcqty',
									'issueuom',
									'convuom',
									'convissueqty',
									'conversion',
									'unitprice',
									'taxcode',
									'taxrate',
									'taxamount',
									'discounttype',
									'discountrate',
									'discountamount'
								);
			}

			elseif ($source == 'SI') {
				$table 			= 'salesinvoice';
				$fields 		= array(
									'voucherno',
									'customer',
									'transactiondate',
									'remarks',
									'stat',
									'vat_sales',
									'vat_exempt',
									'vat_zerorated',
									'taxamount',
									'netamount',
									'discounttype',
									'discountamount',
									'sourceno'
								);

				$table_details 	= 'salesinvoice_details tbl';
				$fields_details = array(
									'itemcode',
									'detailparticular',
									'warehouse',
									'linenum',
									'serialno serialnumbers',
									'tbl.issueqty origqty',
									'issueqty - IFNULL('.$sql.', 0) maxqty',
									'issueqty srcqty',
									'issueuom',
									'convuom',
									'convissueqty',
									'conversion',
									'unitprice',
									'taxcode',
									'taxrate',
									'taxamount',
									'discounttype',
									'discountrate',
									'itemdiscount discountamount'
								);
			}

			$result['header'] 	= $this->getSourceHeader($table, $fields, $voucherno);
			$result['details'] 	= $this->getSourceDetails($table_details, $fields_details, $voucherno);

			return $result;
		}

		public function getSourceHeader($table, $fields, $voucherno) {

			$cond = 'voucherno = "'.$voucherno.'"';

			
			$result = $this->db->setTable($table)
								->setFields($fields)
								->setWhere($cond)
								->runSelect()
								->getRow();

			return $result;
		}

		public function getSourceDetails($table, $fields, $voucherno) {

			$cond = 'voucherno = "'.$voucherno.'"';

			$sort = 'linenum asc';
			
			$result = $this->db->setTable($table)
								->setFields($fields)
								->setWhere($cond)
								->setOrderBy($sort)
								->runSelect()
								->getResult();
								$query = $this->db->getQuery();
			return $result;
		}


		public function saveSalesReturn($header, $details) {
			$this->getAmounts($header, $details);

			$result = $this->db->setTable('inventory_salesreturn')
								->setValues($header)
								->runInsert();
			if ($result) {
				$this->log->saveActivity("Create Sales Return [{$header['voucherno']}]");
				$result = $this->updateSalesReturnDetails($details, $header['voucherno']);
			}
				
			return $result;
		}

		public function updateSalesReturnDetails($details, $voucherno) {
			$this->db->setTable('inventory_salesreturn_details')
						->setWhere("voucherno = '$voucherno'")
						->runDelete();
			
			$details['voucherno']	= $voucherno;
			$result = $this->db->setTable('inventory_salesreturn_details')
								->setValuesFromPost($details)
								->runInsert();

			return $result;
		}

		public function updateItemSerialized($serialids, $stat) {
			$update_arr['stat'] = 'Available';

			$serialids = implode(',',$serialids);
			$result = $this->db->setTable('items_serialized')
						->setFields('stat')
						->setValues($update_arr)
						->setWhere("id IN ($serialids)")
						->runUpdate();

			return $result;
		}

		private function getAmounts(&$header, &$details) {
			$this->cleanNumber($header, array('amount','netamount','discountamount','taxamount'));
			$this->cleanNumber($details, array('issueqty', 'unitprice', 'amount'));
			// foreach ($data2['itemcode'] as $key => $value) {
			// 	$amount							= $data2['issueqty'][$key] * $data2['unitprice'][$key];
			// 	$data2['taxamount'][$key]		= $data2['taxrate'][$key] * $amount;
			// 	$data2['amount'][$key]			= $amount - $data2['taxamount'][$key];
			// 	$data2['convissueqty'][$key]	= $data2['issueqty'][$key] * $data2['conversion'][$key];
			// }

			// $data['amount']		= array_sum($data2['amount']);
			// $data['taxamount']	= array_sum($data2['taxamount']);
			// $data['netamount']	= $data['amount'] + $data['taxamount'];
		}

		public function getValue($table, $cols = array(), $cond, $orderby = "",$default = true){
			$result = $this->db->setTable($table)
						->setFields($cols)
						->setWhere($cond)
						->setOrderBy($orderby)
						->runSelect($default)
						->getResult();

			return $result;
		}

		public function getSalesReturnById($fields, $voucherno) {
			$result = $this->db->setTable('inventory_salesreturn')
								->setFields($fields)
								->setWhere("voucherno = '$voucherno'")
								->setLimit(1)
								->runSelect()
								->getRow();
			
			return $result;
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
				'SUM(CASE WHEN defective="Yes" AND srd.replacement="Yes" THEN netamount ELSE 0 END) total1',
				'SUM(CASE WHEN defective="No" AND srd.replacement="No" THEN netamount ELSE 0 END) total2'
			);

			$data	= (array) $this->getSalesReturnById($header_fields, $voucherno);

			$average_query = $this->db->setTable('price_average p1')
										->setFields('p1.*')
										->leftJoin('price_average p2 ON p1.itemcode = p2.itemcode AND p1.linenum < p2.linenum')
										->setWhere('p2.linenum IS NULL')
										->buildSelect();
			
			$details = $this->db->setTable('inventory_salesreturn_details srd')
								->setFields($detail_fields)
								->innerJoin('items i ON i.itemcode = srd.itemcode AND i.companycode = srd.companycode')
								->leftJoin('itemclass ic ON ic.id = i.classid AND ic.companycode = i.companycode')
								->leftJoin("($average_query) ac ON ac.itemcode = srd.itemcode")
								->setWhere("srd.voucherno = '$voucherno'")
								->setGroupBy('accountcode')
								->runSelect()
								->getResult();
			var_dump($details);
			return $details;
			$sr_stat = (isset($data['stat'])) ? $data['stat'] : '';
			$data['stat'] = 'posted';

			if ($sr_stat == 'Cancelled') {
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
			
			//unset($data[0]);
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

		/*
		RUN UPDATE OR INSERT WHETHER JV IS IN DB
		*/

			if ($exist) {
				$result = $header->setWhere("voucherno = '$jvvoucherno'")
								->setLimit(1)
								->runUpdate();
			} else {
				$result = $header->runInsert();
			}
			

		/*
		RUN DELETE AND INSERT UPDATED JV ENTRIES
		*/

			if ($result) {
				// $this->db->setTable('journaldetails')
				// 		->setWhere("voucherno = '$jvvoucherno'")
				// 		->runDelete();


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
						'debit'			=> 0,
						'linenum'			=> $key + 2,
						'voucherno'			=> $jvvoucherno,
						'transtype'			=> 'IT',
						'credit'				=> $total_amount,
						'convertedcredit'	=> $total_amount,
						'converteddebit'	=> 0,
						'detailparticulars'	=> '',
						'stat'				=> $data['stat']
					);
				}
				$detail_insert  = false;
				// $detail_insert = $this->db->setTable('journaldetails')
				// 							->setValues($details)
				// 							->runInsert();

				if ($detail_insert) {
					$data = array(
						'amount'			=> $total_amount,
						'convertedamount'	=> $total_amount
					);
					// $result = $this->db->setTable('journalvoucher')
					// 					->setValues($data)
					// 					->setWhere("voucherno = '$jvvoucherno'")
					// 					->setLimit(1)
					// 					->runUpdate();

				}
			}

			return $result;
		}

		public function getSerialItemList($serialids) {
			$serialno = array();
			foreach ($serialids as $value) {
				$result = $this->db->setTable('items_serialized serial')
									->setFields('serial.id, CONCAT(i.itemcode, " - ", i.itemname) item, serialno, engineno, chassisno')
									->leftJoin('items i ON i.itemcode = serial.itemcode')
									->setWhere("id='$value'")
									->setOrderBy('id')
									->runSelect()
									->getRow();
				array_push($serialno, $result);
			}
			return $serialno;
		}

		public function getTaggedSerial($sourceno, $linenum) {
			$serialno = array();
			$explode_serial = array();
			$result = $this->db->setTable('inventory_salesreturn_details srd')
								->setFields('serialnumbers')
								->leftJoin('inventory_salesreturn sr ON sr.voucherno = srd.voucherno')
								->setWhere("source_no='$sourceno' AND linenum='$linenum'")
								->runSelect()
								->getRow();

			$explode_serial = ($result) ? explode(',', $result->serialnumbers) : '';
			
			return $explode_serial;
		}

		// public function getPackingList($customer) 
		// {
		// 	$result = $this->db->setTable('packing p')
		// 					->setFields('p.voucherno voucherno, p.transactiondate transactiondate, p.amount amount, (SUM(pd.issueqty) - SUM(drd.issueqty)) qtyleft')
		// 					->leftJoin('deliveryreceipt dr ON p.voucherno = dr.packing_no AND p.companycode = dr.companycode')
		// 					->leftJoin('deliveryreceipt_details drd ON drd.voucherno = p.voucherno AND drd.companycode = dr.companycode')
		// 					->leftJoin('packing_details pd ON pd.voucherno = p.voucherno AND pd.itemcode = drd.itemcode AND pd.companycode = p.companycode')
		// 					->setWhere("p.stat NOT IN ('cancelled','temporary') AND p.customer = '$customer'")
		// 					->setGroupBy('so.voucherno')
		// 					->setHaving('qtyleft > 0')
		// 					->runSelect()
		// 					->getResult();

		// 	return $result;
		// }

		// public function getOption($type, $orderby = "")
		// {
		// 	$result = $this->db->setTable('wc_option')
		// 				->setFields("code ind, value val")
		// 				->setWhere("type = '$type'")
		// 				->setOrderBy($orderby)
		// 				->runSelect(false)
		// 				->getResult();

		// 	return $result;
		// }

		// private function removeComma($data) {
		// 	if (is_array($data)) {
		// 		$temp = array();
		// 		foreach ($data as $val) {
		// 			$temp[] = $this->removeComma($val);
		// 		}
		// 		return $temp;
		// 	} else {
		// 		return str_replace(',', '', $data);
		// 	}
		// }

		// private function generateSearch($search, $array) {
		// 	$temp = array();
		// 	foreach ($array as $arr) {
		// 		$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		// 	}
		// 	return '(' . implode(' OR ', $temp) . ')';
		// }

		// public function retrieveData($table, $fields = array(), $cond = "", $join = "", $orderby = "", $groupby = "")
		// {
		// 	$result = $this->db->setTable($table)
		// 				->setFields($fields)
		// 				->leftJoin($join)
		// 				->setGroupBy($groupby)
		// 				->setWhere($cond)
		// 				->setOrderBy($orderby)
		// 				->runSelect()
		// 				->getResult();
			
		// 	return $result;
		// }

		// public function getHeaderHidden($fields, $voucherno) {
		// 	$result = $this->db->setTable('packinglist')
		// 						->setFields($fields)
		// 						->setWhere("voucherno = '$voucherno'")
		// 						->setLimit(1)
		// 						->runSelect()
		// 						->getRow();
			
		// 	return $result;
		// }

		// public function getValue($table, $cols = array(), $cond, $orderby = "", $bool = "")
		// {
		// 	$result = $this->db->setTable($table)
		// 				->setFields($cols)
		// 				->setWhere($cond)
		// 				->setOrderBy($orderby)
		// 				->runSelect($bool)
		// 				->getResult();

		// 	return $result;
		// }

		// public function getDocumentRequestInfo($voucherno) {
		// 	$result = $this->db->setTable('stock_transfer st')
		// 						->setFields("st.transactiondate documentdate, st.transferdate transferdate,  st.stocktransferno voucherno, w1.description source, w2.description destination, st.reference reference, st.remarks remarks, st.prepared_by")
		// 						->leftJoin("warehouse w1 ON w1.warehousecode = st.source")
		// 						->leftJoin("warehouse w2 ON w2.warehousecode = st.destination")
		// 						->setWhere("st.stocktransferno = '$voucherno'")
		// 						->runSelect()
		// 						->getRow();
	
		// 	return $result;
		// }

		// public function getReference($code) {
		// 	$result = $this->db->setTable('wc_reference')
		// 				->setFields("value")
		// 				->setWhere("code = '$code'")
		// 				->setLimit('1')
		// 				->runSelect(false)
		// 				->getResult();

		// 	return $result;
		// }
	}
?>