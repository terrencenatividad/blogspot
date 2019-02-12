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

		public function getSRheader($voucherno, $fields) {
			$result = $this->db->setTable('inventory_salesreturn')
								->setFields($fields)
								->setWhere("voucherno = '$voucherno'")
								->setLimit(1)
								->runSelect()
								->getRow();
			
			return $result;
		}

		public function getSRdetails($voucherno, $fields, $sourceno) {
			$source = substr($sourceno, 0,2);
			if ($source == 'SI') {
				$table = 'salesinvoice_details tbl';
			} else{
				$table = 'deliveryreceipt_details tbl';
			}
			$fields[] = 'tbl.issueqty - IFNULL((SELECT SUM(issueqty) FROM inventory_salesreturn_details srd LEFT JOIN inventory_salesreturn sr ON sr.voucherno=srd.voucherno WHERE srd.voucherno != "'. $voucherno .'" AND sr.source_no="'. $sourceno .'" AND srd.itemcode=main.itemcode AND srd.linenum=main.linenum), 0) maxqty';
			
			$result = $this->db->setTable('inventory_salesreturn_details main')
								->setFields($fields)
								->leftJoin($table ." ON tbl.voucherno='$sourceno' AND tbl.itemcode=main.itemcode AND tbl.linenum=main.linenum")
								->setWhere("main.voucherno = '$voucherno' AND tbl.voucherno='$sourceno'")
								->setOrderBy('linenum')
								->runSelect()
								->getResult();
			
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

			$exist = $this->db->setTable('inventory_salesreturn')
								->setValues(array('voucherno'=>''))
								->setWhere("voucherno='".$header['voucherno']."'")
								->runSelect()
								->getRow();
								

			$result = $this->db->setTable('inventory_salesreturn')
								->setValues($header);
			if ($exist) {
				$result = $this->db->setWhere("voucherno='".$header['voucherno']."'")
								->runUpdate();
			} else {
				$result = $this->db->runInsert();
			}

			if ($result) {
				$this->log->saveActivity("Create Sales Return [{$header['voucherno']}]");
				$result = $this->updateSalesReturnDetails($details, $header['voucherno'], $header['source_no']);
			}
				
			return $result;
		}

		public function updateSalesReturnDetails($details, $voucherno, $sourceno) {
			$drvalue = array();
			$this->db->setTable('inventory_salesreturn_details')
						->setWhere("voucherno = '$voucherno'")
						->runDelete();
			
			$details['voucherno']	= $voucherno;
			$result = $this->db->setTable('inventory_salesreturn_details')
								->setValuesFromPost($details)
								->runInsert();
			if ($result) {
				$drupdate = $this->updateDRqty($voucherno, $sourceno, $details['linenum']);
			}
			return $result;
		}

		public function updateDRqty($voucherno, $sourceno, $linenum){
			foreach ($linenum as $key => $value) {

				$returned = $this->db->setTable('inventory_salesreturn_details srd')
								->setFields('COALESCE(SUM(issueqty),0) qty')
								->leftJoin('inventory_salesreturn sr ON sr.voucherno=srd.voucherno')
								->setWhere("srd.voucherno='$voucherno' AND source_no='$sourceno' AND linenum='$value'")
								->runSelect()
								->getRow();

				$drvalue['returnedqty'] = $returned->qty;

				$drupdate = $this->db->setTable('deliveryreceipt_details')
								->setValues($drvalue)
								->setWhere("voucherno = '$sourceno' AND linenum='$value'")
								->runUpdate();
			}
			return $drupdate;
		}

		public function updateDRSerial($sourceno, $linenum, $serialid){
			$update_arr = array();
			foreach ($linenum as $key => $value) {
				$update_arr['return_serialnumbers'] = $serialid[$key];
				$where_linenum = $linenum[$key];
				$result = $this->db->setTable('deliveryreceipt_details')
									->setValues($update_arr)
									->setWhere("voucherno='$sourceno' AND linenum='$where_linenum'")
									->runUpdate();
			}
			return $result;
		}

		public function updateItemSerialized($detail_serial, $stat) {
			$update_arr['stat'] = $stat;

			$serialids 		= $this->parseSerialsToArray($detail_serial);
			
			$serialids 		= implode(',',$serialids);

			$result = $this->db->setTable('items_serialized')
						->setFields('stat')
						->setValues($update_arr)
						->setWhere("id IN ($serialids)")
						->runUpdate();

			return $result;
		}

		private function parseSerialsToArray($array){
			$new_array = array();
			foreach ($array as $key => $value) {
				if($array[$key] != ''){
					$temp 	= explode(',', $array[$key]);

					foreach ($temp as $key => $value) {
						array_push($new_array, $value);
					}
				}
			}
			return $new_array;
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

		public function deleteSalesReturn($voucherno) {
			$update_value = array('stat' => 'Cancelled');
			$where = implode(',', $voucherno);
			$result = $this->db->setTable('inventory_salesreturn')
								->setValues($update_value)
								->setWhere("voucherno IN ('$where')")
								->runUpdate();

			return $result;
		}

		public function revertItemSerialized($voucherno) {
			$serial_value = array('stat' => 'Not Available');
			$where = '';
			foreach ($voucherno as $key => $value) {
				$sr_rows = $this->db->setTable('inventory_salesreturn_details srd')
								->setFields('source_no, linenum, serialnumbers')
								->leftJoin('inventory_salesreturn sr ON sr.voucherno=srd.voucherno')
								->setWhere("srd.voucherno='$value'")
								->runSelect()
								->getResult();

				foreach ($sr_rows as $key => $row) {
					if ($row->serialnumbers != '') {

						$dr_values['serialnumbers'] = $row->serialnumbers;
						$sourceno 	= $row->source_no;
						$linenum 	= $row->linenum;

						$update_dr 	= $this->db->setTable('deliveryreceipt_details')
												->setValues($dr_values)
												->setWhere("voucherno='$sourceno' AND linenum='$linenum'" )
												->runUpdate();

						if ($update_dr) {
							$where .= $row->serialnumbers;
							$where .= ',';
						}
					}
				}
			}
			$where = substr($where, 0, -1);

			$result = $this->db->setTable('items_serialized')
							->setValues($serial_value)
							->setWhere("voucherno IN ('$where')")
							->runUpdate();

			return $result;
		}

		public function revertDRchanges($voucherno) {
			$result = true;
			foreach ($voucherno as $key => $value) {
				$sr_rows = $this->db->setTable('inventory_salesreturn_details srd')
									->leftJoin('inventory_salesreturn sr ON sr.voucherno=srd.voucherno')
									->setFields('sr.source_no, linenum, issueqty')
									->setWhere("srd.voucherno='$value'")
									->runSelect()
									->getResult();

				foreach ($sr_rows as $key => $row) {
					$sourceno 	= $row->source_no;
					$linenum 	= $row->linenum;
					$issueqty 	= $row->issueqty;

					$dr_row 	= $this->db->setTable('deliveryreceipt_details')
									->setFields('returnedqty')
									->setWhere("voucherno='$sourceno' AND linenum='$linenum'")
									->runSelect()
									->getRow();
					$dr_value['returnedqty'] = $dr_row->returnedqty - $issueqty;
					$update 	= $this->db->setTable('deliveryreceipt_details')
									->setValues($dr_value)
									->setWhere("voucherno='$sourceno' AND linenum='$linenum'")
									->runUpdate();
									
					if ($update) {
						$result = false;
					}
				}
			}

			return $kwan;
		}

		public function getDRvoucher($sivoucher){
			$result = $this->db->setTable('salesinvoice')
								->setFields('sourceno')
								->setWhere("voucherno='$sivoucher'")
								->runSelect()
								->getRow();
			return $result;
		}

		public function createClearingEntries($voucherno, $sourcetype) {
			$exist = $this->db->setTable('journalvoucher')
								->setFields('voucherno')
								->setWhere("referenceno = '$voucherno'")
								->setLimit(1)
								->runSelect()
								->getRow();

			$jvvoucherno = ($exist) ? $exist->voucherno : '';

			


			// $average_query = $this->db->setTable('price_average p1')
			// 							->setFields('p1.*')
			// 							->leftJoin('price_average p2 ON p1.itemcode = p2.itemcode AND p1.linenum < p2.linenum')
			// 							->setWhere('p2.linenum IS NULL')
			// 							->buildSelect();
			
			// $details = $this->db->setTable('inventory_salesreturn_details srd')
			// 					->setFields($detail_fields)
			// 					->innerJoin('items i ON i.itemcode = srd.itemcode AND i.companycode = srd.companycode')
			// 					->leftJoin('itemclass ic ON ic.id = i.classid AND ic.companycode = i.companycode')
			// 					->leftJoin("($average_query) ac ON ac.itemcode = srd.itemcode")
			// 					->setWhere("srd.voucherno = '$voucherno'")
			// 					->setGroupBy('accountcode')
			// 					->runSelect()
			// 					->getResult();

			$header_fields = array(
				'voucherno referenceno',
				'customer partner',
				'transactiondate',
				'fiscalyear',
				'period',
				'stat'
			);

			$data	= (array) $this->getSalesReturnById($header_fields, $voucherno);
			
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

			$detail_fields = array(
				'SUM(CASE WHEN srd.defective="Yes" AND srd.replacement="Yes" THEN netamount ELSE 0 END) total1',
				'SUM(CASE WHEN srd.defective="No" AND srd.replacement="No" THEN netamount ELSE 0 END) total2'
			);
			if ($sourcetype == 'SI') {
				$detail_fields[] = 'SUM(CASE WHEN srd.defective="Yes" AND srd.replacement="No" THEN netamount ELSE 0 END) total2';
			}

			$details = $this->db->setTable('inventory_salesreturn_details srd')
								->setFields($detail_fields)
								->setWhere("srd.voucherno = '$voucherno'")
								->runSelect()
								->getResult();

			$result = false;
			
			//unset($data[0]);
			// $data['amount']				= 0;
			// $data['convertedamount']	= 0;

			if ( ! $exist) {
				$seq					= new seqcontrol();
				$jvvoucherno			= $seq->getValue('JV');
				$data['voucherno']		= $jvvoucherno;
				$data['transtype']		= 'JV';
				$data['currencycode']	= 'PHP';
				$data['exchangerate']	= '1';
			}
			var_dump($data);
			exit;
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

		public function getTaggedSerial($voucherno, $sourceno, $linenum) {
			$serialno 		= array();
			$explode_serial = array();

			$result = $this->db->setTable('inventory_salesreturn_details srd')
								->setFields('serialnumbers')
								->leftJoin('inventory_salesreturn sr ON sr.voucherno = srd.voucherno')
								->setWhere("srd.voucherno!='$voucherno' AND source_no='$sourceno' AND linenum='$linenum'")
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
		public function getSRPrint($voucherno){
			$fields = array('transactiondate',
						'source_no',
						'partnername AS customer',
						'address1 AS address',
						'tinno',
						'mobile AS contactno',
						'reason',
						'remarks',
						'vat_sales',
						'vat_exempt',
						'vat_zerorated',
						'netamount',
						'taxamount AS tax',
						'amount',
						'discountamount AS discount',
						'sr.stat'
						);

			$header = $this->db->setTable('inventory_salesreturn sr')
								->setFields($fields)
								->leftJoin('partners ON partnercode=customer')
								->setWhere("voucherno='$voucherno'")
								->runSelect()
								->getRow();
			
			$fields_details = array('srd.itemcode',
								'detailparticular',
								'defective',
								'srd.replacement',
								'issueqty AS qty',
								'issueuom AS uom',
								'unitprice AS price',
								'discountamount AS discount',
								'taxcode',
								'taxamount AS tax',
								'netamount AS amount'
								);

			$details = $this->db->setTable('inventory_salesreturn_details srd')
								->setFields($fields_details)
								->leftJoin('items i ON i.itemcode=srd.itemcode')
								->setWhere("voucherno='$voucherno'")
								->runSelect()
								->getResult();

			return array('header' => $header,
						'details' => $details );
		}
	}
?>