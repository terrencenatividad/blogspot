<?php
	class stock_transfer extends wc_model
	{
		public function getWarehouseList($current="") {
		
			$add_cond 	=	(isset($current) && $current != "") ? " AND warehousecode != '$current' " 	: 	"";

			$result = $this->db->setTable('warehouse')
							->setFields("warehousecode ind, description val")
							->setWhere("stat = 'active' $add_cond ")
							->runSelect()
							->getResult();

			return $result;
		}


		public function getItemList() {
			$result = $this->db->setTable('items i')
							->setFields("i.itemcode ind, i.itemcode val")
							->runSelect()
							->getResult();

			return $result;
		}

		public function retrieveItemDetails($itemcode, $warehouse)
		{
			$fields = "i.itemname as itemname, i.itemdesc as itemdesc, i.uom_base, COALESCE(pa.price_average,0) as price, COALESCE(invdtl.onhandQty,0) onhandQty";
			//$fields = "i.itemname, i.itemdesc, i.weight, invdtl.onhandQty";
			
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
			//  echo $this->db->getQuery();
			return $result;
		}

		public function getStockTransfer($fields,$stocktransferno)
		{

			$result = $this->db->setTable('stock_transfer st')
								->setFields($fields)
								->setWhere("st.stocktransferno = '$stocktransferno' ")
								->setLimit('1')
								->runSelect()
								->getRow();
					//var_dump($this->db->getQuery());
			return $result;
		}

		public function getStockTransferDetails($fields,$stocktransferno) {

			$result = $this->db->setTable('stock_transfer_details dtl')
								->setFields($fields)
								->innerJoin('items i ON dtl.itemcode = i.itemcode')
								->setWhere("dtl.stocktransferno = '$stocktransferno'")
								->setOrderBy('dtl.linenum')
								->runSelect()
								->getResult();

			return $result;
		}

		public function saveStockTransferRequest($data,$data2) {
			$result = $this->db->setTable('stock_transfer')
							->setValues($data)
							->runInsert();
				// var_dump($this->db->getQuery());
			if ($result) 
			{
				$result = $this->updateStockTransferRequestDetails($data2, $data['stocktransferno']);
			}

			return $result;
		}

		public function updateStockTransferRequest($data, $data2, $stocktransferno) {
			$result = $this->db->setTable('stock_transfer')
								->setValues($data)
								->setWhere("stocktransferno = '$stocktransferno'")
								->runUpdate();
			  //var_dump($this->db->getQuery());	
			if ($result) {
				$result = $this->updateStockTransferRequestDetails($data2, $stocktransferno);
			}

			return $result;
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

		public function getStat($voucherno){
			$result 	=	$this->db->setTable('stock_transfer p')
									->setFields('stat')
									->setWhere("stocktransferno = '$voucherno'")
									->setLimit(1)
									->runSelect()
									->getRow();
			return $result;
		}

		public function updateStockTransferRequestDetails($data, $stocktransferno) 
		{
			$this->db->setTable('stock_transfer_details')
					->setWhere("stocktransferno = '$stocktransferno'")
					->runDelete();

			foreach ($data['itemcode'] as $key => $value) {
				$linenum[] = $key + 1;
			}
			$data["qtytoapply"] = str_replace(",","",$data["qtytoapply"]);
			$data["price"]  = str_replace(",","",$data["price"]);
			$data["amount"] = str_replace(",","",$data["amount"]);
			$data['linenum']	= $linenum;
			$data['stocktransferno']	= $stocktransferno;
			$result = $this->db->setTable('stock_transfer_details')
								->setValuesFromPost($data)
								->runInsert();
		   //var_dump($this->db->getQuery());	
								
			return $result;
		}
		public function deleteStockTransfer($data) {
			//$ids = "'" . implode("','", $data) . "'";
			$result = $this->db->setTable('stock_transfer')
								->setValues(array('stat'=>'cancelled'))
								->setWhere("stocktransferno = '$data' ")
								->setLimit(count($data))
								->runUpdate();
			if ($result) 
			{
				$result = $this->db->setTable('stock_transfer_details')
								->setValues(array('stat'=>'cancelled'))
								->setWhere("stocktransferno = '$data' ")
								//->setLimit(count($data))
								->runUpdate();
			}

			return $result;
		}


		public function getStockTransferList($search,$filter, $startdate, $enddate, $warehouse, $type) 
		{
			$condition = "st.stat != 'temporary' ";
			if ($search) {
				$condition .= ' AND ' . $this->generateSearch($search, array('st.stocktransferno'));
			}
			if ($filter && $filter != 'all') {
			
				$condition .= " AND st.stat = '$filter'";
			}

			if ($startdate && $enddate) {
				$condition .= " AND st.transactiondate >= '$startdate' AND st.transactiondate <= '$enddate'";
			}  
			
			if ( ($warehouse && $type) && $type == 'to' ) { // Transfers to : Warehouse __ 
				$condition .= " AND st.destination = '$warehouse'";
			} 
			else if ( ($warehouse && $type) && $type == 'from' ) { // Transfers to : Warehouse __
				$condition .= " AND st.source = '$warehouse'";
			}  

			$result = $this->db->setTable("stock_transfer st")
						->setFields("DATE_FORMAT(st.transactiondate, '%b %d, %Y') transactiondate, DATE_FORMAT(st.transferdate, '%b %d, %Y') transferdate, st.stocktransferno,w.description as source,w2.description as destination,
						st.prepared_by,st.remarks,st.total_amount,st.stat")
						->leftJoin("warehouse w ON w.warehousecode = st.source AND w.companycode = st.companycode")
						->leftJoin("warehouse w2 ON w2.warehousecode = st.destination AND w2.companycode = st.companycode")
						->setWhere($condition)
						->runPagination();
						//->runSelect()
						//->getResult();
			//var_dump($this->db->getQuery());	

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
	}
?>