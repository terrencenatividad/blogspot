<?php
	class stock_transfer extends wc_model
	{
		public function __construct() {
			parent::__construct();
			$this->log = new log();
		}

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

		public function getStockTransferRequest($fields,$stocktransferno) {
			$result = $this->db->setTable('stock_transfer st')
								->setFields($fields)
								->setWhere("st.stocktransferno = '$stocktransferno' ")
								->setLimit('1')
								->runSelect()
								->getRow();
			return $result;
		}

		public function getStockTransferApproval($fields,$stocktransferno) {
			$result = $this->db->setTable('stock_approval sa')
								->setFields($fields)
								->setWhere("sa.stocktransferno = '$stocktransferno' ")
								->setLimit('1')
								->runSelect()
								->getRow();
			return $result;
		}

		public function getStockTransferDetailsRequest($voucherno, $voucherno_ref = false){
			$result1		= $this->db->setTable('stock_transfer_details std')
									->setFields("std.itemcode, detailparticular, linenum, qtytoapply, qtytoapply maxqty, std.uom, std.price, std.amount, COALESCE(inv.onhandQty, 0) ohqty, 0 qtytransferred, qtytoapply balanceqty")
									->innerJoin('stock_transfer st ON std.stocktransferno = st.stocktransferno AND std.companycode = st.companycode')
									->leftJoin('invfile inv ON std.itemcode = inv.itemcode AND std.companycode = inv.companycode AND inv.warehouse = st.source')
									->setWhere("st.stocktransferno = '$voucherno'")
									->runSelect()
									->getResult();
			// echo $this->db->getQuery();
			$addcond = ($voucherno_ref) ? " AND sa.stocktransferno != '$voucherno_ref'" : '';

			$result2		= $this->db->setTable('stock_approval_details sad')
									->setFields("itemcode, linenum, SUM(qtytransferred) qtytoapply")
									->innerJoin('stock_approval sa ON sad.stocktransferno = sa.stocktransferno AND sad.companycode = sa.companycode')
									->setWhere("source_no = '$voucherno' AND sa.stat != 'Cancelled'" . $addcond)
									->setGroupBy('linenum')
									->runSelect()
									->getResult();
			$checker	= array();
			$result		= array();
			foreach ($result2 as $key => $row) {
				$checker[$row->linenum] = $row->qtytoapply;
			}

			foreach ($result1 as $key => $row) {
				$add_result = true;
				if (isset($checker[$row->linenum])) {
					$quantity = $checker[$row->linenum];

					if ($quantity >= $row->balanceqty) {
						$add_result = false;
					}
					$row->maxqty 		 = ($row->maxqty > $quantity) ? $row->maxqty - $quantity : 0;
					$row->qtytoapply 	 = $row->balanceqty;
					$row->balanceqty = ($row->balanceqty > $quantity) ? $row->balanceqty - $quantity : 0;
					$checker[$row->linenum] -= $row->balanceqty;
				}
				$row->qtyleft = $row->maxqty;

				if ($row->ohqty < $row->maxqty) {
					$row->maxqty = $row->ohqty;
				}
				$row->maxqty = ($row->maxqty > 0) ? $row->maxqty : 0;
				// if ($add_result) {
				// 	$result[] = $row;
				// }	
				$result[] = $row;
				$row->qtytransferred = $row->maxqty;
			}
			// var_dump( $result);
			return $result;
		}

		public function getStockTransferDetailsApproval($fields, $voucherno, $view = true) {
			if ($view) {
				$result = $this->db->setTable('stock_approval_details')
									->setFields($fields)
									->setWhere("stocktransferno = '$voucherno'")
									->setOrderBy('linenum')
									->runSelect()
									->getResult();
			} else {
				$sourceno = $this->db->setTable('stock_approval')
									->setFields('source_no')
									->setWhere("stocktransferno = '$voucherno'")
									->runSelect()
									->getRow();
	
				$sourceno = ($sourceno) ? $sourceno->source_no : '';
	
				$result1 = $this->db->setTable('stock_approval_details')
									->setFields($fields)
									->setWhere("stocktransferno = '$voucherno'")
									->setOrderBy('linenum')
									->runSelect()
									->getResult();
		
				$result = $this->getStockTransferDetailsRequest($sourceno, $voucherno);

				$checker	= array();
				foreach ($result1 as $key => $row) {
					$checker[$row->linenum] = (object) $row;
				}
	
				foreach ($result as $key => $row) {
					$result[$key]->qtytransferred = (isset($checker[$row->linenum])) ? $checker[$row->linenum]->qtytransferred : 0;
					// $result[$key]->qtytoapply 	  = $result[$key]->qtytransferred;
					if (isset($checker[$row->linenum])) {
						$result[$key]->amount = $checker[$row->linenum]->amount;
					}
				}
			}
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

		public function saveStockTransferApproval($data,$data2) {
			$this->getAmounts($data, $data2);
	
			$result = $this->db->setTable('stock_approval')
								->setValues($data)
								->runInsert();
	
			if ($result) {
				if ($result) {
					$this->log->saveActivity("Created Stock Transfer [{$data['stocktransferno']}]");
				}
				$result = $this->updateStockApprovalDetails($data2, $data['stocktransferno']);
			}
	
			return $result;
		}

		public function updateStockApproval($data, $data2, $voucherno)                         {
			$this->getAmounts($data, $data2);
	
			$result = $this->db->setTable('stock_approval')
								->setValues($data)
								->setWhere("stocktransferno = '$voucherno'")
								->runUpdate();

			if ($result) {
				if ($result) {
					$this->log->saveActivity("Updated Stock Transfer [$voucherno]");
				}
				$result = $this->updateStockApprovalDetails($data2, $voucherno);
			}
	
			return $result;
		}

		public function updateStockTransferStatus($data,$voucherno)                         {
	
			$result = $this->db->setTable('stock_transfer')
								->setValues($data)
								->setWhere("stocktransferno = '$voucherno'")
								->runUpdate();

			if ($result) {
				$result = $this->updateStockTransferDetailStatus($data, $voucherno);
				if( $result ){
					$this->log->saveActivity("Updated Status of Stock Transfer [$voucherno]");
				}
			}
	
			return $result;
		}

		public function updateStockTransferDetailStatus($data,$voucherno)                         {
	
			$result = $this->db->setTable('stock_transfer_details')
								->setValues($data)
								->setWhere("stocktransferno = '$voucherno'")
								->runUpdate();
	
			return $result;
		}

		public function updateStockApprovalDetails($data, $voucherno) {
			$data['stocktransferno']	= $voucherno;
	
			$this->updateStockRequest($voucherno);
	
			$this->db->setTable('stock_approval_details')
						->setWhere("stocktransferno = '$voucherno'")
						->runDelete();
	
			$result = $this->db->setTable('stock_approval_details')
								->setValuesFromPost($data)
								->runInsert();
	
			$this->updateStockRequest($voucherno);
								
			return $result;
		}

		private function getAmounts(&$data, &$data2) {
			// $this->cleanNumber($data, array('amount'));
			$this->cleanNumber($data2, array('qtytransferred', 'price'));
			$data2['amount'] = array();
			foreach ($data2['itemcode'] as $key => $value) {
				// $data2['convissueqty'][$key]	= $data2['issueqty'][$key] * $data2['conversion'][$key];
				$data2['amount'][$key]			= $data2['qtytransferred'][$key] * $data2['price'][$key];
			}
			// $data['amount']		= array_sum($data2['amount']);
			// $data['netamount']	= $data['amount'];
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

		private function updateStockRequest($voucherno) {
			$source_no	= $this->getSourceNo($voucherno);
	
			$subquery	= $this->db->setTable('stock_approval sa')
									->setFields('IF(SUM(sad.qtytransferred) IS NULL, 0, SUM(sad.qtytransferred)) pr_qty, source_no, sa.companycode companycode')
									->innerJoin('stock_approval_details sad ON sad.stocktransferno = sa.stocktransferno AND sad.companycode = sa.companycode')
									->setWhere("sa.stat != 'Cancelled' AND source_no = '$source_no'")
									->setGroupBy('sa.source_no')
									->buildSelect();
	
			$result		= $this->db->setTable('stock_transfer_details std')
									->setFields('st.stocktransferno voucherno, st.transactiondate transactiondate, SUM(std.qtytoapply) quantity, COALESCE(sa.pr_qty, 0, sa.pr_qty) quantity_x')
									->innerJoin('stock_transfer st ON std.stocktransferno = st.stocktransferno AND std.companycode = st.companycode')
									->leftJoin("($subquery) sa ON sa.source_no = std.stocktransferno AND sa.companycode = std.companycode")
									->setWhere("st.stat IN ('open','partial', 'posted','approved') AND st.stocktransferno = '$source_no'")
									->setGroupBy('st.stocktransferno')
									->runSelect()
									->getRow();
			// echo $this->db->getQuery();
			if ($result) {
				$status = 'open';
				if ($result->quantity_x == 0) {
					$status = 'open';
				} else if ($result->quantity > $result->quantity_x) {
					$status = 'partial';
				} else if ($result->quantity = $result->quantity_x) {
					$status = 'posted';
				}
				$result = 	$this->db->setTable('stock_transfer')
									 ->setValues(array('stat' => $status))
									 ->setWhere("stocktransferno = '$source_no'")
									 ->runUpdate();

				if( $result ){
					$result = 	$this->db->setTable('stock_transfer_details')
									 ->setValues(array('stat' => $status))
									 ->setWhere("stocktransferno = '$source_no'")
									 ->runUpdate();
				}
			}
	
			return $result;
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
								->runUpdate();
			}

			return $result;
		}

		public function deleteStockTransferApproval($data) {
			// $result = $this->db->setTable('stock_approval')
			// 					->setValues(array('stat'=>'cancelled'))
			// 					->setWhere("stocktransferno = '$data' ")
			// 					->setLimit(count($data))
			// 					->runUpdate();
			// if ($result) 
			// {
			// 	$result = $this->db->setTable('stock_approval_details')
			// 					->setValues(array('stat'=>'cancelled'))
			// 					->setWhere("stocktransferno = '$data' ")
			// 					->runUpdate();
			// }

			// return $result;

			$result	= $this->db->setTable('stock_approval')
								->setValues(array('stat'=>'cancelled'))
								->setWhere("stocktransferno = '$data'")
								->setLimit(count($data))
								->runUpdate();
			if ($result) {
				if ($result) {
					$this->log->saveActivity("Cancelled Approved Stock Transfer [$data]");
				}
				$result = $this->db->setTable('stock_approval_details')
								->setValues(array('stat'=>'cancelled'))
								->setWhere("stocktransferno = '$data'")
								->setLimit(count($data))
								->runUpdate();
			}

			if ($result) {
				$this->updateStockRequest($data);
			}

			return $result;
		}

		public function getStockTransferRequestList($search,$filter, $startdate, $enddate, $warehouse, $type) 
		{
			$condition = "st.stat NOT IN ('closed','temporary','cancelled') ";
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
						st.prepared_by,st.remarks,st.total_amount,st.stat, st.enteredby")
						->leftJoin("warehouse w ON w.warehousecode = st.source AND w.companycode = st.companycode")
						->leftJoin("warehouse w2 ON w2.warehousecode = st.destination AND w2.companycode = st.companycode")
						->setWhere($condition)
						->setOrderBy('st.stocktransferno DESC')
						->runPagination();

			return $result;
		}

		public function getStockTransferApprovalList($search,$filter, $startdate, $enddate, $warehouse, $type) 
		{
			$condition = "sa.stat NOT IN ('temporary','cancelled')  ";
			if ($search) {
				$condition .= ' AND ' . $this->generateSearch($search, array('sa.stocktransferno','sa.source_no'));
			}
			// if ($filter && $filter != 'all') {
			// 	$condition .= " AND sa.stat = '$filter'";
			// }
			if ($startdate && $enddate) {
				$condition .= " AND sa.transactiondate >= '$startdate' AND sa.transactiondate <= '$enddate'";
			}
			if ( ($warehouse && $type) && $type == 'to' ) { // Transfers to : Warehouse __ 
				$condition .= " AND sa.destination = '$warehouse'";
			} 
			else if ( ($warehouse && $type) && $type == 'from' ) { // Transfers to : Warehouse __
				$condition .= " AND sa.source = '$warehouse'";
			}  

			$result = $this->db->setTable("stock_approval sa")
						->setFields("DATE_FORMAT(sa.transactiondate, '%b %d, %Y') transactiondate, DATE_FORMAT(sa.transferdate, '%b %d, %Y') transferdate, sa.stocktransferno,w.description as source,w2.description as destination,
						sa.approved_by,sa.remarks,sa.total_amount,sa.stat, sa.source_no, sa.enteredby, trans.stat request_stat")
						->leftJoin("warehouse w ON w.warehousecode = sa.source AND w.companycode = sa.companycode")
						->leftJoin("warehouse w2 ON w2.warehousecode = sa.destination AND w2.companycode = sa.companycode")
						->leftJoin("stock_transfer trans ON trans.stocktransferno = sa.source_no AND trans.companycode = sa.companycode")
						->setWhere($condition)
						->setOrderBy('sa.stocktransferno DESC')
						->runPagination();

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
								->setFields("itemcode 'Item Code', detailparticular 'Description', qtytransferred 'Quantity', UPPER(uom) 'UOM', price price, amount amount")
								->leftJoin('uom u ON u.uomcode = sad.uom AND u.companycode = sad.companycode')
								// ->leftJoin('chartaccount ON 1=1')
								->setWhere("stocktransferno = '$voucherno'")
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
	}
?>