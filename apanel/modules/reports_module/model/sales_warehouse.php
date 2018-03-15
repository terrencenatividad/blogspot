<?php

class sales_warehouse extends wc_model {

	public function warehouse_list($startdate, $enddate,$warehouse, $sort) {
		$condition = '';
		if ($startdate && $enddate) {
			$condition .= " AND inv.transactiondate >= '$startdate' AND inv.transactiondate <= '$enddate'";
		}
		
		if($warehouse && ! in_array('none',$warehouse)){
			$condition .= " AND inv.warehouse IN ('" . implode("', '", $warehouse) . "')";
		}

		$fields = array(
			'w.warehousecode warehousecode',
			'w.description warehouse',
			'SUM(dtl.issueqty) as quantity',
			'inv.amount amount',
			'(inv.amount) * SUM(dtl.issueqty) as total'
		);
		
		$result = $this->db->setTable('salesinvoice as inv')
						->setFields($fields)
						->leftJoin('salesinvoice_details dtl ON inv.voucherno = dtl.voucherno')
						->leftJoin('warehouse w ON w.warehousecode = inv.warehouse AND inv.companycode = w.companycode')
						->setWhere(" inv.stat = 'posted'" .$condition)
						->setGroupBy('inv.warehouse')
						->runPagination();
						// echo $this->db->getQuery();
		return $result;
	}

	public function fileExport($data) {
		$condition = '';
		$datefilter	= $data['daterangefilter'];
		$datefilter = explode('-', $datefilter);
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$startdate  = 	$dates[0] ;
		$enddate	= 	$dates[1] ;
		if ($startdate && $enddate) {
			$condition .= "AND inv.transactiondate >= '$startdate' AND inv.transactiondate <= '$enddate'";
		}
		
		$warehouse 	=	$data['warehouse'];

		if($warehouse && ! in_array('none',$warehouse)){
			$condition .= " AND inv.warehouse IN ('" . implode("', '", $warehouse) . "')";
		}

		$fields = array(
			'w.warehousecode warehousecode',
			'w.description warehouse',
			'SUM(dtl.issueqty) as quantity',
			'inv.amount amount',
			'(inv.amount) * SUM(dtl.issueqty) as total'
		);

		$result = $this->db->setTable('salesinvoice as inv')
							->setFields($fields)
							->leftJoin('salesinvoice_details dtl ON inv.voucherno = dtl.voucherno')
							->leftJoin('warehouse w ON w.warehousecode = inv.warehouse AND inv.companycode = w.companycode')
							->setWhere(" inv.stat = 'posted'" .$condition)
							->setGroupBy('inv.warehouse')
							->runSelect()
							->getResult();
					// echo $this->db->getQuery();
		return $result;
	}

	public function fileExport2($data) {
		$condition = '';
		$date = $data['date'];
		$warehouse = $data['warehouse'];
		$filter = explode('-',$date);
		foreach($filter as $date){
			$dates[] = date('Y-m-d',strtotime($date));
		}
		$start = $dates[0];
		$end   = $dates[1];
		
		$fields = array(
			'SUM(dtl.issueqty) quantity',
			'dtl.itemcode',
			'i.itemname',
			'unitprice',
			'issueuom',
			'(SUM(dtl.issueqty) * unitprice) as amount'
		);
		if ($start && $end){
			$condition .=  "AND inv.transactiondate >= '$start' AND inv.transactiondate <= '$end' ";
		}
		if($warehouse){
			$condition .= "AND inv.warehouse = '$warehouse' "; 
		}
		$result = $this->db->setTable('salesinvoice as inv')
						->setFields($fields)
						->leftJoin('salesinvoice_details dtl ON inv.voucherno = dtl.voucherno LEFT JOIN items i ON i.itemcode = dtl.itemcode')
						->setWhere(" inv.stat = 'posted'" .$condition)
						->setGroupBy("dtl.itemcode")
						->setOrderBy("dtl.issueqty DESC")
						->runSelect()
						->getResult();
		return $result;
	}

	public function warehouseDetails($warehouse){
		$fields = array(
			'warehousecode',
			"description",
		);

		if( $warehouse != "" ){
		$result = $this->db->setTable('warehouse')
						->setFields($fields)
						->setWhere("warehousecode = '$warehouse'" )
						->setLimit(1)
						->runSelect()
						->getRow();
		} else {
		$result   =  array(
					"warehousecode"   => "--",
					"description"   => "--"
					);
		}
		
		return $result;
	}

	public function warehouseBreakdown($warehouse,$daterange){
		$condition = '';
		$filter = explode('-',$daterange);
		foreach($filter as $date){
			$dates[] = date('Y-m-d',strtotime($date));
		}
		$start = $dates[0];
		$end   = $dates[1];
		
		$fields = array(
			'SUM(dtl.issueqty) quantity',
			'dtl.itemcode',
			'i.itemname',
			'unitprice',
			'issueuom',
			'(SUM(dtl.issueqty) * unitprice) as amount'
		);
		if ($start && $end){
			$condition .=  "AND inv.transactiondate >= '$start' AND inv.transactiondate <= '$end' ";
		}
		if($warehouse){
			$condition .= "AND inv.warehouse = '$warehouse' "; 
		}
		$result = $this->db->setTable('salesinvoice as inv')
						->setFields($fields)
						->leftJoin('salesinvoice_details dtl ON inv.voucherno = dtl.voucherno LEFT JOIN items i ON i.itemcode = dtl.itemcode')
						->setWhere(" inv.stat = 'posted'" .$condition)
						->setGroupBy("dtl.itemcode")
						->setOrderBy("dtl.issueqty DESC")
						->runPagination();
		return $result;
	}

	public function retrieveCustomerList(){
			$result = $this->db->setTable('partners')
						->setFields("partnercode ind, CONCAT( first_name, ' ', last_name ) val")
						->setWhere("partnercode != '' AND partnertype = 'customer' AND stat = 'active'")
						->setOrderBy("val")
						->runSelect()
						->getResult();
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

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

	public function warehouses($warehouse){
		
		if ($warehouse == ''){
			$wh = "('')";
		} else {
			$wh = "'". implode("','" ,$warehouse)."'";
			$wh = "($wh)";
		}
		
		$result = $this->db->setTable('warehouse')
						->setFields("warehousecode ind, description val")
						->setWhere("stat = 'active' AND warehousecode IN $wh ")
						->runSelect()
						->getResult();
						// echo $this->db->getQuery();
		return $result;
	}

}