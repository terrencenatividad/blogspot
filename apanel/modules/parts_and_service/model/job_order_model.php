<?php
class job_order_model extends wc_model
{
	public function __construct() 
	{
		parent::__construct();
		$this->log = new log();
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
						//->setWhere("itemgroup = 'goods'")
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

	public function getSQPagination($customer) {
		$result = $this->db->setTable("servicequotation")
						->setFields("voucherno, transactiondate, notes")
						->setWhere("customer = '$customer' AND stat='Approved'")
						->setOrderBy("voucherno ASC")
						->runPagination();
		return $result;
	}

	public function getServiceQuotationHeader($fields, $voucherno) {
		$result = $this->db->setTable('servicequotation')
						->setFields($fields)
						->setWhere("voucherno = '$voucherno'")
						->runSelect()
						->getRow();

		return $result;
	}

	public function getServiceQuotationDetails($voucherno, $voucherno_ref = false) {
		$result1		= $this->db->setTable('servicequotation_details sqd')
								->setFields("sqd.itemcode, detailparticular, linenum, qty, sqd.warehouse, uom, sqd.parentcode, sqd.childqty, sqd.isbundle, sqd.parentline, i.item_ident_flag")
								->innerJoin('servicequotation sq ON sqd.voucherno = sq.voucherno AND sqd.companycode = sq.companycode')
								->leftJoin('items i ON i.itemcode = sqd.itemcode')
								->leftJoin('invfile inv ON sqd.itemcode = inv.itemcode AND sqd.warehouse = inv.warehouse AND sqd.companycode = inv.companycode')
								->setWhere("sq.voucherno = '$voucherno'")
								->setOrderBy('linenum ASC')
								->runSelect()
								->getResult();


		// $checker	= array();
		// $result		= array();
		// foreach ($result2 as $key => $row) {
		// 	$checker[$row->linenum] = $row->issueqty;
		// }

		// foreach ($result1 as $key => $row) {
		// 	$add_result = true;
		// 	if (isset($checker[$row->linenum])) {
		// 		$quantity = $checker[$row->linenum];

		// 		if ($quantity >= $row->issueqty) {
		// 			$add_result = false;
		// 		}
		// 		$row->maxqty = ($row->maxqty > $quantity) ? $row->maxqty - $quantity : 0;
		// 		$row->issueqty = ($row->issueqty > $quantity) ? $row->issueqty - $quantity : 0;
		// 		$checker[$row->linenum] -= $row->issueqty;
		// 	}
		// 	$row->qtyleft = $row->maxqty;
		// 	if ($row->available < $row->maxqty) {
		// 		$row->maxqty = $row->available;
		// 	}
		// 	$row->maxqty = ($row->maxqty > 0) ? $row->maxqty : 0;
		// 	if ($add_result) {
		// 		$result[] = $row;
		// 	}
		// }

		return $result1;
	}

	public function saveValues($table, $values){
        $result = $this->db->setTable($table)
                        ->setValues($values)
                        ->runInsert();
                        
        return $result;
	}
	
	public function saveFromPost($table, $values, $data){
		$data['job_order_no'];
		$result = $this->db->setTable($table)
                            ->setValuesFromPost($values)
                            ->runInsert();
                           
        return $result;
	}

	public function saveJobOrder($data, $data2) {

		$result = $this->db->setTable('job_order')
							->setValues($data)
							->runInsert();
		if ($result) {
			if ($result) {
				$this->log->saveActivity("Create Job Order [{$data['job_order_no']}]");
			}
			$result = $this->updateJobOrderDetails($data2, $data['job_order_no']);
			if($result && $data['service_quotation'] != '') {
				$result = $this->updateSQ($data['service_quotation']);
				$this->log->saveActivity("Update Service Qoutation with JO [{$data['service_quotation']}]");
			}
		}


		return $result;
	}

	public function updateJobOrderDetails($data, $voucherno) {
		$data['job_order_no']	= $voucherno;
		$this->db->setTable('job_order_details')
					->setWhere("job_order_no = '$voucherno'")
					->runDelete();
					// var_dump($data);
		$result = $this->db->setTable('job_order_details')
							->setValuesFromPost($data)
							->runInsert();
							
		return $result;
	}

	public function getJOList($fields) {
		
		$result = $this->db->setTable("job_order")
							->setFields($fields)
							->setWhere(1)
							->setOrderBy('job_order_no DESC')
							->runPagination();

		return $result;
	}

	public function getJOByID($fields, $voucherno) {
		return $this->db->setTable('job_order')
						->setFields($fields)
						->setWhere("job_order_no = '$voucherno'")
						->runSelect()
						->getRow();
	}

	public function getJobOrderDetails($fields, $voucherno) {
		$result = $this->db->setTable('job_order_details')
							->setFields($fields)
							->setWhere("job_order_no = '$voucherno'")
							->setOrderBy('linenum')
							->runSelect()
							->getResult();
		return $result;
	}

	public function getJobOrder($fields, $voucherno) {
		$result = $this->db->setTable('job_order_details jod')
							->setFields($fields)
							->leftJoin('items i ON i.itemcode = jod.itemcode')
							->leftJoin('bomdetails bom ON bom.item_code = jod.itemcode')
							->setWhere("job_order_no = '$voucherno'")
							->setOrderBy('linenum')
							->runSelect()
							->getResult();
		return $result;
	}
	public function updateSQ($code)
		{
			$data['stat'] = 'With JO';
			$condition 			   = " voucherno = '$code' ";

			$result 			   = $this->db->setTable('servicequotation')
												->setValues($data)
												->setWhere($condition)
												->setLimit(1)
												->runUpdate();

			return $result;
		}

	public function deleteJobOrder($data) {
			$ids	= "'" . implode("','", $data) . "'";
			$result	= $this->db->setTable('job_order')
								->setValues(array('stat'=>'cancelled'))
								->setWhere("job_order_no IN ($ids)")
								->setLimit(count($data))
								->runUpdate();
			if ($result) {
				if ($result) {
					$log_id = implode(', ', $data);
					$this->log->saveActivity("Delete/Cancelled Job Order [$log_id]");
				}
			}
	
			return $result;
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
									// echo $this->db->getQuery();
			return $result;
		}

	public function getJOSerials($itemcode, $voucherno, $linenum) {
			$result = $this->db->setTable('job_release_details') 
							->setFields('serialnumbers')
							->setWhere("itemcode='$itemcode' AND job_item_id='$voucherno' AND linenum='$linenum'")
							->runSelect()
							->getRow();
	
			return $result;
		}
	public function retrieveItemDetails($itemcode)
	{
		$result = $this->db->setTable('items i')
							->leftJoin('uom u ON u.uomcode = i.uom_selling AND u.companycode = i.companycode')
							->leftJoin('items_price p ON p.itemcode = i.itemcode AND p.companycode = i.companycode')
							
							->setFields("i.itemdesc as detailparticular, u.uomcode as uom, i.bundle as isbundle")
							->setWhere("i.itemcode = '$itemcode'")
							->setLimit('1')
							->runSelect()
							->getRow();

		return $result;
	}

	public function retrieveBundleDetails($itemcode) {
		$fields = "bd.item_code as itemcode, bd.quantity as BaseQty, bd.detailsdesc as detailparticular, bd.uom as uom";
			$result = $this->db->setTable('items i')
							->leftJoin('bom b ON b.bundle_item_code = i.itemcode')
							->leftJoin('bomdetails bd ON bd.bom_code = b.bom_code')
							->setFields($fields)
							->setWhere("status = 'active' AND b.bundle_item_code = '$itemcode'")
							->runSelect()
							->getResult();
			return $result;
	}

	public function getItemListforBundle($itemcode) {
		$result = $this->db->setTable('items')
						->setFields("itemcode as code, CONCAT(itemcode, ' - ', itemname) as itemcode, itemdesc as detailparticular, uom_base as uom")
						->setWhere("itemcode = '$itemcode'")
						->runSelect()
						->getRow();
		return $result;
	}

	public function saveJobRelease($data) {
		$voucherno ='';
		$data['stat'] = 'released';	
		
		$result = $this->db->setTable('job_release')
							->setValuesFromPost($data)
							->runInsert();
		return $result;
	}

	public function getIssuedParts($jobno) {
		$result	= $this->db->setTable('job_release j')
								->setFields('job_release_no,j.job_order_no,j.itemcode,detailparticulars,j.warehouse,j.quantity,j.unit')
								// ->leftJoin('job_order_details jod ON jod.job_order_no = j.job_order_no')
								->setWhere("j.job_order_no = '$jobno'")
								// ->setGroupBy('j.job_release_no')
								->setOrderBy('j.job_release_no')
								->runSelect()
								->getResult();
								// echo $this->db->getQuery();
		return $result;
	}

	public function deleteJobRelease($id) {
		$result	= $this->db->setTable('job_release')
				->setWhere("job_release_no = '$id'")
				->runDelete();
		
			if ($result) {
				$this->log->saveActivity("Deleted Job Release [$id]");
			}

		return $result;
	}
		
}