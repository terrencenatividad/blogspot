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

		public function getSourcePagination() {

			$sort = 'transactiondate desc';
			
			$silist = $this->db->setTable("salesinvoice")
								->setFields("voucherno, transactiondate, remarks notes, amount")
								->setWhere('stat="posted"')
								->setOrderBy($sort)
								->runSelect()
								->getResult();

			$drlist = $this->db->setTable("deliveryreceipt")
								
								->setFields("voucherno, transactiondate, deliverydate, remarks notes, amount")
								->setWhere('stat="Delivered"')
								->setOrderBy($sort)
								->runSelect()
								->getResult();

			$result = (object) array_merge( (array)$drlist, (array)$silist);

			return $result;
		}

		public function getSourceDetails($voucherno) {

			$source = substr($voucherno, 0,2);

			if ($source == 'DR') {
				$table = 'deliveryreceipt_details';
				$fields = 'itemcode, detailparticular, warehouse, unitprice, issueqty, issueuom';
			}

			elseif ($source == 'SI') {
				$table = 'salesinvoice_details';
				$fields = 'itemcode, detailparticular, warehouse, unitprice, issueqty, issueuom';
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
				$table = 'deliveryreceipt_details';
			}

			elseif ($source == 'SI') {
				$table = 'salesinvoice_details';
			}



			$fields = $fields;
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


		public function getItemList() {
			$result = $this->db->setTable('items i')
							->setFields("i.itemcode ind, CONCAT(i.itemcode,' - ',i.itemname) val")
							->setWhere("i.stat = 'active'")
							->runSelect()
							->getResult();

			return $result;
		}

		public function getItemDetails($itemcode, $warehouse) {
			$fields = "i.itemname as itemname, i.itemdesc as itemdesc, i.uom_base, COALESCE(pa.price_average,0) as price, COALESCE(invdtl.onhandQty,0) onhandQty";
			
			$cond = $warehouse_cond = "";
			$cond 				.= (!empty($itemcode))? "i.itemcode = '$itemcode' " : "";
			$warehouse_cond   	.= (!empty($warehouse))? " AND invdtl.warehouse = '$warehouse' " : "";

			$result = $this->db->setTable('items i')
								->setFields($fields)
								->leftJoin('items_price p ON p.itemcode = i.itemcode')
								->leftJoin("invfile invdtl ON invdtl.itemcode = i.itemcode $warehouse_cond")
								->leftJoin("price_average pa ON pa.itemcode = i.itemcode")
								->setWhere($cond)
								->setOrderBy("pa.linenum DESC")
								->setLimit('1')
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
								$result1 = $this->db->getQuery();
			return $result1;
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

	
		

		private function getSourceNo($voucherno) {
			$result = $this->db->setTable('stock_approval')
								->setFields(array('source_no'))
								->setWhere("stocktransferno = '$voucherno'")
								->setLimit(1)
								->runSelect()
								->getRow();

			if ($result) {
				return $result->source_no;
			} else {
				return false;
			}
		}

		public function updateStatus($data,$table,$stocktransferno) {
			$result = $this->db->setTable($table)
								->setValues($data)
								->setWhere("stocktransferno = '$stocktransferno'")
								//->buildUpdate();
								->runUpdate();
			//var_dump($this->db->getQuery());	
			if ($result) {
				$result = $this->db->setTable('stock_transfer_details')
								->setValues($data)
								->setWhere("stocktransferno = '$stocktransferno'")
								->runUpdate();
			}

			return $result;
		}

		public function getStat($voucherno, $table){
			$result 	=	$this->db->setTable($table)
									->setFields('stat')
									->setWhere("stocktransferno = '$voucherno'")
									->setLimit(1)
									->runSelect()
									->getRow();
			return $result;
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

		public function load_so_list($customer) 
		{
			$result = $this->db->setTable('salesorder s')
							->setFields('s.voucherno , s.transactiondate , s.netamount')
							->setWhere("s.stat IN ('open') AND s.customer = '$customer'")
							->runSelect()
							->getResult();
							//var_dump($this->db->getQuery());	

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
	
		public function getDocumentRequestContent($voucherno) {
			$result = $this->db->setTable('stock_transfer_details sad')
								->setFields("itemcode 'Item Code', detailparticular 'Description', qtytoapply 'Quantity', UPPER(uom) 'UOM', price price, amount amount")
								->leftJoin('uom u ON u.uomcode = sad.uom AND u.companycode = sad.companycode')								
								// ->leftJoin('chartaccount ON 1=1')
								->setWhere("stocktransferno = '$voucherno'")
								->runSelect()
								->getResult();
			return $result;
		}

		public function getDocumentApprovalInfo($voucherno) {
			$result = $this->db->setTable('stock_approval sa')
								->setFields("sa.transactiondate documentdate, sa.transferdate transferdate, sa.stocktransferno voucherno, w1.description source, w2.description destination, sa.source_no referenceno, sa.reference reference, sa.remarks remarks, sa.approved_by")
								->leftJoin("warehouse w1 ON w1.warehousecode = sa.source")
								->leftJoin("warehouse w2 ON w2.warehousecode = sa.destination")
								->setWhere("sa.stocktransferno = '$voucherno'")
								->runSelect()
								->getRow();
	
			return $result;
		}
	
		public function getDocumentApprovalContent($voucherno) {
			$result = $this->db->setTable('stock_approval_details sad')
								->setFields("sad.itemcode 'Item Code', detailparticular 'Description', qtytransferred 'Quantity', UPPER(uom) 'UOM', happy.serialno 'serialno', happy.engineno 'engineno', happy.chassisno 'chassisno', price price, amount amount")
								->leftJoin('uom u ON u.uomcode = sad.uom AND u.companycode = sad.companycode')
								->leftJoin('stock_approval_serialized happy ON happy.stocktransferno = sad.stocktransferno AND happy.itemcode = sad.itemcode AND happy.linenum = sad.linenum')
								// ->leftJoin('chartaccount ON 1=1')
								->setWhere("sad.stocktransferno = '$voucherno'")
								->runSelect()
								->getResult();

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

		public function retrieveSerial($itemcode){
			$result = $this->db->setTable('items_serialized')
						->setFields("serialno, chassisno, engineno")
						->setWhere("itemcode = '$itemcode' AND stat='Available'")
						->runSelect()
						->getResult();
			return $result;
		}

		public function retrieveApprovedSerial($voucherno, $linenum, $itemcode){
			$result = $this->db->setTable('stock_approval_serialized')
						->setFields("serialno, chassisno, engineno")
						->setWhere("stocktransferno='$voucherno' AND linenum='$linenum' AND itemcode='$itemcode'")
						->runSelect(false)
						->getResult();
			return $result;
		}
		
		public function saveSerializedItems($values){
			foreach ($values['itemcode'] as $key => $value) {
				$where = "itemcode='".$values['itemcode'][$key]."' AND serialno='".$values['serialno'][$key]."' AND 
					chassisno='".$values['chassisno'][$key]."' AND engineno='".$values['engineno'][$key]."'";

				$result = $this->db->setTable('items_serialized')
									->setValues(array('stat'=>'Not Available'))
									->setWhere($where)
									->runUpdate();
			}
			
			if ($result) {
				$result1 = $this->db->setTable('stock_approval_serialized')
	                            ->setValuesFromPost($values)
	                            ->runInsert(false);
	                            
			}
                           
            return $result1;
		}
	}
?>