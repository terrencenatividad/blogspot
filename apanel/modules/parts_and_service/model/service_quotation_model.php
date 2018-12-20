<?php
class service_quotation_model extends wc_model
{
	public function __construct() 
	{
		parent::__construct();
		$this->log = new log();
	}

	public function getServiceQuotationPagination($search, $sort, $customer, $filter, $datefilter, $limit){
		$fields 			= array('s.voucherno', 'p.partnername AS customer', 's.transactiondate', 's.jobtype', 's.reference','s.stat');

			$datefilter 		= isset($datefilter) ? htmlentities($datefilter) : ""; 
			$custfilter      	= isset($customer) ? htmlentities($customer) : ""; 
			$searchkey 		 	= isset($search) ? htmlentities($search) : "";
			$filter 		 	= isset($filter) ? htmlentities($filter) : "";
			$sort 		 		= isset($sort) ? htmlentities($sort) : "s.voucherno DESC";	
			$limit 		 		= isset($limit) ? htmlentities($limit) : "10";
		
			$datefilterArr		= explode(' - ',$datefilter);
			$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
			$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";

			$add_query 	= (!empty($searchkey)) ? "AND (s.voucherno LIKE '%$searchkey%' OR p.partnername LIKE '%$searchkey%') " : "";
			$add_query .= (!empty($datefilter) && !is_null($datefilterArr)) ? "AND s.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
			$add_query .= (!empty($custfilter) && $custfilter != 'none') ? "AND p.partnercode = '$custfilter' " : "";
			
			if( !empty($filter) && $filter == 'Pending')
			{
				$add_query 	.=	" AND s.stat = 'Pending' ";
			}
			else if( !empty($filter) && $filter == 'Approved' )
			{
				$add_query 	.= 	" AND s.stat = 'Approved' ";
			}
			else if( !empty($filter) && $filter == 'Partial' )
			{
				$add_query 	.= 	" AND s.stat = 'Partial' ";
			}
			else if( !empty($filter) && $filter == 'With JO' )
			{
				$add_query 	.= 	" AND s.stat = 'With JO' ";
			}
			else if( !empty($filter) && $filter == 'Cancelled' )
			{
				$add_query 	.= 	" AND s.stat = 'Cancelled' ";
			}
			else if( $filter == 'all' )
			{
				$add_query 	.= 	" ";
			}
			
			$result 	=	 $this->db->setTable('servicequotation s')
							->setFields($fields)
							->leftJoin('partners p ON p.partnercode = s.customer AND p.partnertype = "customer"')
							->setWhere(" s.stat != 'temporary' $add_query")
							->setOrderBy($sort)
							->setLimit($limit)
							->runPagination();
						// echo $this->db->getQuery();
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
    public function getCustomerList() {
		$result = $this->db->setTable('partners')
						->setFields("partnercode ind, CONCAT(partnercode,' - ',partnername) val")
						->setWhere("partnercode != '' AND partnertype = 'customer' AND stat = 'active'")
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
	public function getWarehouseList($warehouse='') {
		$add_cond 	= ($warehouse) ? " OR warehousecode = '$warehouse'" : "";
		$result = $this->db->setTable('warehouse')
						->setFields("warehousecode ind, description val")
						->setWhere("stat = 'active'".$add_cond)
						->setOrderBy("val")
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
	public function retrieveItemDetails($itemcode)
	{
		$result = $this->db->setTable('items i')
							->leftJoin('uom u ON u.uomcode = i.uom_selling AND u.companycode = i.companycode')
							->leftJoin('items_price p ON p.itemcode = i.itemcode AND p.companycode = i.companycode')
							
							->setFields("i.itemdesc as itemdesc,p.itemprice as itemprice, u.uomcode as uom, i.bundle as isbundle")
							->setWhere("i.itemcode = '$itemcode'")
							->setLimit('1')
							->runSelect()
							->getRow();

		return $result;
	}

	public function retrieveBundleDetails($itemcode) {
		$fields = "CONCAT(bd.item_code,' - ',bd.item_name) as item_name, bd.quantity, bd.detailsdesc, bd.uom, bd.item_code";
			$result = $this->db->setTable('items i')
							->leftJoin('bom b ON b.bundle_item_code = i.itemcode')
							->leftJoin('bomdetails bd ON bd.bom_code = b.bom_code')
							->setFields($fields)
							->setWhere("status = 'active' AND b.bundle_item_code = '$itemcode'")
							->runSelect()
							->getResult();
			return $result;
	}

	public function retrieveServiceQuotation($voucherno=""){
		$fields = "jobtype, transactiondate, customer, targetdate, discounttype, reference, notes";
		$result = $this->db->setTable('servicequotation sq')
						->setFields($fields)
						->setWhere("voucherno = '$voucherno'")
						->runSelect()
						->getResult();
		return $result;
	}
	public function retrieveServiceQuotationDetails($voucherno=""){
		$fields = "CONCAT(sq.itemcode,' - ',i.itemname) as itemname, sq.itemcode, sq.linenum, sq.haswarranty, sq.isbundle, sq.parentcode, sq.parentline, sq.detailparticular, sq.warehouse, sq.qty, sq.uom, sq.unitprice, sq.taxcode, sq.taxrate, sq.discounttype, sq.discountrate";
		$result = $this->db->setTable('servicequotation_details sq')
						->leftJoin('items i ON sq.itemcode = i.itemcode')
						->setFields($fields)
						->setWhere("voucherno = '$voucherno'")
						->runSelect()
						->getResult();
		return $result;
	}

	public function saveFromPost($table, $values){
		$result = $this->db->setTable($table)
                            ->setValuesFromPost($values)
                            ->runInsert();
                           
        return $result;
	}

	public function saveValues($table, $values){
        $result = $this->db->setTable($table)
                        ->setValues($values)
                        ->runInsert();
                        
        return $result;
    }
	public function updateData($data, $table, $cond)
	{
		$result = $this->db->setTable($table)
				->setValues($data)
				->setWhere($cond)
				->runUpdate();

		return $result;
	}
	public function deleteData($table, $cond){
		$result = $this->db->setTable($table)
				->setWhere($cond)
				->runDelete();

		return $result;
	}
}