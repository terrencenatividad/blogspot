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

			$sort = ($sort) ? $sort : 'transactiondate desc';
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

		public function getSourceDetails($fields, $voucherno) {

			$source = substr($voucherno, 0,2);

			if ($source == 'DR') {
				$table = 'deliveryreceipt_details';
				$fields[15] = 'discountamount';
			}

			elseif ($source == 'SI') {
				$table = 'salesinvoice_details';
				$fields[15] = 'discountedamount';
			}

			$cond = 'voucherno = "'.$voucherno.'"';

			$sort = 'linenum desc';
			
			$result = $this->db->setTable($table)
								->setFields($fields)
								->setWhere($cond)
								->setOrderBy($sort)
								->runSelect()
								->getResult();

			return $result;
		}

		public function getSourceHeader($fields, $voucherno) {
 
			$source = substr($voucherno, 0,2);

			if ($source == 'DR') {
				$table 		= 'deliveryreceipt';
				$fields[2] 	= 'source_no';
			}

			elseif ($source == 'SI') {
				$table 		= 'salesinvoice';
				$fields[2] 	= 'sourceno';
			}



			$fields = $fields;
			$cond = 'voucherno = "'.$voucherno.'"';

			
			$result = $this->db->setTable($table)
								->setFields($fields)
								->setWhere($cond)
								->runSelect()
								->getRow();

			return $result;
		}


		public function saveSalesReturn($data, $data2) {
			$this->getAmounts($data, $data2);

			$result = $this->db->setTable('inventory_salesreturn')
								->setValues($data)
								->runInsert();
			
			
			if ($result) {
				if ($result) {
					$this->log->saveActivity("Create Return [{$data['voucherno']}]");
				}
				$result = $this->updateSalesReturnDetails($data2, $data['voucherno']);
			}

			return $result;
		}

		public function updateSalesReturnDetails($data, $voucherno) {
			$this->db->setTable('inventory_salesreturn_details')
						->setWhere("voucherno = '$voucherno'")
						->runDelete();
			
			$data['voucherno']	= $voucherno;
			$result = $this->db->setTable('inventory_salesreturn_details')
								->setValuesFromPost($data)
								->runInsert();
								
			return $result;
		}

		private function getAmounts(&$data, &$data2) {
			$this->cleanNumber($data, array('amount'));
			$this->cleanNumber($data2, array('issueqty', 'unitprice'));
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

	
		

	

		public function getPackingList($customer) 
		{
			$result = $this->db->setTable('packing p')
							->setFields('p.voucherno voucherno, p.transactiondate transactiondate, p.amount amount, (SUM(pd.issueqty) - SUM(drd.issueqty)) qtyleft')
							->leftJoin('deliveryreceipt dr ON p.voucherno = dr.packing_no AND p.companycode = dr.companycode')
							->leftJoin('deliveryreceipt_details drd ON drd.voucherno = p.voucherno AND drd.companycode = dr.companycode')
							->leftJoin('packing_details pd ON pd.voucherno = p.voucherno AND pd.itemcode = drd.itemcode AND pd.companycode = p.companycode')
							->setWhere("p.stat NOT IN ('cancelled','temporary') AND p.customer = '$customer'")
							->setGroupBy('so.voucherno')
							->setHaving('qtyleft > 0')
							->runSelect()
							->getResult();

			return $result;
		}

		public function getOption($type, $orderby = "")
		{
			$result = $this->db->setTable('wc_option')
						->setFields("code ind, value val")
						->setWhere("type = '$type'")
						->setOrderBy($orderby)
						->runSelect(false)
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

		public function retrieveData($table, $fields = array(), $cond = "", $join = "", $orderby = "", $groupby = "")
		{
			$result = $this->db->setTable($table)
						->setFields($fields)
						->leftJoin($join)
						->setGroupBy($groupby)
						->setWhere($cond)
						->setOrderBy($orderby)
						->runSelect()
						->getResult();
			
			return $result;
		}

		public function getHeaderHidden($fields, $voucherno) {
			$result = $this->db->setTable('packinglist')
								->setFields($fields)
								->setWhere("voucherno = '$voucherno'")
								->setLimit(1)
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

		public function getDocumentRequestInfo($voucherno) {
			$result = $this->db->setTable('stock_transfer st')
								->setFields("st.transactiondate documentdate, st.transferdate transferdate,  st.stocktransferno voucherno, w1.description source, w2.description destination, st.reference reference, st.remarks remarks, st.prepared_by")
								->leftJoin("warehouse w1 ON w1.warehousecode = st.source")
								->leftJoin("warehouse w2 ON w2.warehousecode = st.destination")
								->setWhere("st.stocktransferno = '$voucherno'")
								->runSelect()
								->getRow();
	
			return $result;
		}

		public function getReference($code) {
			$result = $this->db->setTable('wc_reference')
						->setFields("value")
						->setWhere("code = '$code'")
						->setLimit('1')
						->runSelect(false)
						->getResult();

			return $result;
		}
	}
?>