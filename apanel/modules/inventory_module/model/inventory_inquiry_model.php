<?php
class inventory_inquiry_model extends wc_model {

	public function getInventoryinquiryList($itemcode, $limit, $sort, $warehouse, $brandcode) {
		$condition = '';
		if ($itemcode && $itemcode != 'none') {
			$condition = "inv.itemcode = '$itemcode'";
		}
		if ($warehouse && $warehouse != 'none') {
			$condition .= (empty($condition) ? '' : ' AND ') . "inv.warehouse = '$warehouse'";
		}
		if ($brandcode && $brandcode != 'none') {
			$condition .= " b.brandcode = '$brandcode'";
		}
		// if ($search){
		// 	$condition = "(inv.itemcode LIKE '%$search%' OR w.description LIKE '%$search%'  OR SUM(inv.onhandQty) LIKE '%$search%')";
		// }
		// if ($startdate && $enddate) {
		// 	$condition .= (empty($condition) ? '' : ' AND ') . "inv.entereddate >= '$startdate 00:00:00' AND inv.entereddate <= '$enddate 23:59:59'";
		// }
		$result = $this->db->setTable("invfile as inv")
							->innerJoin('items as items ON inv.itemcode = items.itemcode  ') 
							->setFields("inv.itemcode as itemcode,w.description as des, SUM(inv.onhandQty) as OHQty, inv.warehouse as warehouse , SUM(inv.allocatedQty) as AllocQty, SUM(inv.orderedQty) as OrderQty,SUM(inv.availableQty) as avail,CONCAT(items.itemcode,' - ',items.itemname) as itemname, b.brandname")
							->leftJoin('warehouse w ON inv.warehouse = w.warehousecode')
							->leftJoin('brands b ON b.brandcode = items.brandcode')
							->setWhere($condition)
							->setGroupBy('inv.warehouse,inv.itemcode')
							->setOrderBy($sort)
							->runPagination();
							// echo $this->db->getQuery();
		return $result;
	}

	public function getWarehouseList() {
		$result = $this->db->setTable('warehouse')
						->setFields("warehousecode ind, description val")
						->setWhere("stat = 'active'")
						->runSelect()
						->getResult();
		return $result;
	}


	public function onhand_quantity($table,$itemcode,$warehouse){
		$condition = '';
		if ($itemcode && $warehouse) {
			$condition .= "dtl.itemcode = '$itemcode' AND dtl.warehouse = '$warehouse' " ;
		}
		$result  = $this->db->setTable("invdtlfile dtl")
							->innerJoin('items i ON dtl.itemcode = i.itemcode')
							->setFields('beginningQty bqty,i.itemname itemname,dtl.warehouse  warehouse,dtl.purchasereceiptQty prcqty, dtl.deliveredQty drqty, dtl.salesreturnQty srqty, dtl.purchasereturnQty prtqty, dtl.transferedQty transqty,dtl.adjustmentsQty adqty ')
							->setWhere($condition)
							// ->setLimit($limit)
							->runPagination();
		return $result;
	}

	public function allocated_quantity($table,$itemcode,$warehouse){
		$condition = '';
		if ($itemcode && $warehouse) {
			$condition .= "dtl.itemcode = '$itemcode' AND dtl.warehouse = '$warehouse' " ;
		}
		$result  = $this->db->setTable("invdtlfile dtl")
							->innerJoin('items i ON dtl.itemcode = i.itemcode')
							->setFields('i.itemname itemname,dtl.warehouse  warehouse,dtl.salesorderQty soqty, dtl.deliveredQty drqty, dtl.salesreturnQty srqty')
							->setWhere($condition)
							->runPagination();
		return $result;
	}

	public function order_quantity($table,$itemcode,$warehouse){
		$condition = '';
		if ($itemcode && $warehouse) {
			$condition .= "dtl.itemcode = '$itemcode' AND dtl.warehouse = '$warehouse' " ;
		}
		$result  = $this->db->setTable("invdtlfile dtl")
							->innerJoin('items i ON dtl.itemcode = i.itemcode')
							->setFields('i.itemname itemname,dtl.warehouse  warehouse,dtl.purchaseorderQty poqty, dtl.purchasereturnQty pretQty,dtl.purchasereceiptQty prcQty ')
							->setWhere($condition)
							->runPagination();
		return $result;
		
	}

	public function avail_quantity($table,$itemcode,$warehouse){
		$condition = '';
		if ($itemcode && $warehouse) {
			$condition .= "dtl.itemcode = '$itemcode' AND dtl.warehouse = '$warehouse' " ;
		}
		$result  = $this->db->setTable("invdtlfile dtl")
							->innerJoin('items i ON dtl.itemcode = i.itemcode')
							->setFields('i.itemname itemname,dtl.warehouse  warehouse,dtl.purchasereceiptQty prcqty, dtl.deliveredQty drqty, dtl.salesreturnQty srqty, dtl.purchasereturnQty prtqty, dtl.transferedQty transqty,dtl.adjustmentsQty adqty ')
							->setWhere($condition)
							->runPagination();
		return $result;
	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

	public function export_main($data){
		$limit 		= $data['limit'];
		$itemcode 	= $data['itemcode'];
		$sort 	 	= $data['sort'];
		$warehouse 	= $data['warehouse'];
		$condition = '';
		if ($itemcode && $itemcode != 'none') {
			$condition = "inv.itemcode = '$itemcode'";
		}
		if ($warehouse && $warehouse != 'none') {
			$condition .= (empty($condition) ? '' : ' AND ') . "inv.warehouse = '$warehouse'";
		}
		// if ($startdate && $enddate) {
		// 	$condition .= (empty($condition) ? '' : ' AND ') . "inv.entereddate >= '$startdate 00:00:00' AND inv.entereddate <= '$enddate 23:59:59'";
		// }
		$result = $this->db->setTable("invfile as inv")
							->innerJoin('items as items ON inv.itemcode = items.itemcode  ') 
							->setFields("description warehousename,inv.itemcode as itemcode, SUM(inv.onhandQty) as OHQty, inv.warehouse as warehouse , SUM(inv.allocatedQty) as AllocQty, SUM(inv.orderedQty) as OrderQty,SUM(inv.availableQty) as avail,CONCAT(items.itemcode,' - ',items.itemname) as itemname")
							->leftJoin("warehouse wh on inv.warehouse = wh.warehousecode")
							->setWhere($condition)
							->setGroupBy('inv.warehouse,inv.itemcode')
							->setOrderBy($sort)
							->runSelect()
							->getResult();
		return $result;
	}
}