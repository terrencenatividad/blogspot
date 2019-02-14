<?php
class job_order_model extends wc_model
{
	public function __construct() 
	{
		parent::__construct();
		$this->log = new log();
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
			'job_release_no referenceno',
			// 'customer',
			'transactiondate',
			// 'fiscalyear',
			// 'period',
			'stat'
		);
		$detail_fields = array(
			'IF(i.inventory_account > 0, i.inventory_account, ic.inventory_account) accountcode',
			'ROUND(SUM(IFNULL(price_average, 0) * drd.quantity),2) credit'
		);
		
		$data	= (array) $this->getJRById($header_fields,$voucherno);

		$average_query = $this->db->setTable('price_average p1')
									->setFields('p1.*')
									->leftJoin('price_average p2 ON p1.itemcode = p2.itemcode AND p1.linenum < p2.linenum')
									->setWhere('p2.linenum IS NULL')
									->buildSelect();
		
		$details = $this->db->setTable('job_release drd')
							->setFields($detail_fields)
							->innerJoin('items i ON i.itemcode = drd.itemcode AND i.companycode = drd.companycode')
							->leftJoin('itemclass ic ON ic.id = i.classid AND ic.companycode = i.companycode')
							->leftJoin("($average_query) ac ON ac.itemcode = drd.itemcode")
							->setWhere("drd.job_release_no = '$voucherno'")
							->setGroupBy('accountcode')
							->runSelect()
							->getResult();

		$jr_stat = ($data) ? $data['stat'] : '';
		$data['stat'] = 'posted';

		if ($jr_stat == 'cancelled') {
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
				'detailparticulars',
				'source'
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
		
		$data['amount']				= 0;
		$data['convertedamount']	= 0;

		if ( ! $exist) {
			$seq					= new seqcontrol();
			$jvvoucherno			= $seq->getValue('JV');
			$data['voucherno']		= $jvvoucherno;
			$data['transtype']		= 'JV';
			$data['currencycode']	= 'PHP';
			$data['exchangerate']	= '1';
			$data['source']			= 'jo_release';
		}

		$header = $this->db->setTable('journalvoucher')
							->setValues($data);

		if ($exist) {
			$result = $header->setWhere("voucherno = '$jvvoucherno'")
							->setLimit(1)
							->runUpdate();
		} else {
			$result = $header->runInsert();
		}
		
		if ($result) {
			$this->db->setTable('journaldetails')
					->setWhere("voucherno = '$jvvoucherno'")
					->runDelete();


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
					$details[$key]->transtype			= 'JV';
					$details[$key]->debit				= 0;
					$details[$key]->converteddebit		= 0;
					$details[$key]->convertedcredit		= $row->credit;
					$details[$key]->detailparticulars	= '';
					$details[$key]->stat				= $data['stat'];
					$details[$key]->source				= 'jo_release';

					$details[$key]	= (array) $details[$key];
					$total_amount	+= $row->credit;
				}

				$details[] = array(
					'accountcode'		=> $clearing_account,
					'credit'			=> 0,
					'linenum'			=> $key + 2,
					'voucherno'			=> $jvvoucherno,
					'transtype'			=> 'JV',
					'debit'				=> $total_amount,
					'converteddebit'	=> $total_amount,
					'convertedcredit'	=> 0,
					'detailparticulars'	=> '',
					'stat'				=> $data['stat'],
					'source'			=> 'jo_release'
				);
			}
			$detail_insert  = false;
			$detail_insert = $this->db->setTable('journaldetails')
										->setValues($details)
										->runInsert();

			if ($detail_insert) {
				$data = array(
					'amount'			=> $total_amount,
					'convertedamount'	=> $total_amount
				);
				$result = $this->db->setTable('journalvoucher')
									->setValues($data)
									->setWhere("voucherno = '$jvvoucherno'")
									->setLimit(1)
									->runUpdate();

			}
		}

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
	public function getItemList($cond = '') {
		$where 	= ($cond != '') ? 'itemgroup = ' + $cond : '';
		$result = $this->db->setTable('items')
						->setFields("itemcode ind, CONCAT(itemcode, ' - ', itemname) val")
						->setWhere($where)
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

	public function getSQPagination($customer,$search) {
		$condition = '';
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('voucherno', 'transactiondate', 'notes'));
		}
		$result = $this->db->setTable("servicequotation")
						->setFields("voucherno, transactiondate, notes")
						->setWhere("customer = '$customer' AND stat='Approved' $condition")
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
								->setFields("sqd.itemcode, detailparticular, linenum, qty as quantity, sqd.warehouse, w.description, uom, sqd.parentcode as parentcode, sqd.childqty, sqd.isbundle as isbundle, sqd.parentline, i.item_ident_flag")
								->innerJoin('servicequotation sq ON sqd.voucherno = sq.voucherno AND sqd.companycode = sq.companycode')
								->leftJoin('items i ON i.itemcode = sqd.itemcode')
								->leftJoin('warehouse w ON w.warehousecode = sqd.warehouse')
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
		//echo $this->db->getQuery();
		return $result1;
	}

	public function getInventoryAccount($data) {
		$result = $this->db->setTable('items')
						->setFields($data)
						->setWhere("itemcode = '$data'")
						->runSelect()
						->getResult();
						// var_dump($result);

		return $result;
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

	public function updateJO($data, $data2)
	{
		$result = $this->db->setTable('job_order')
				->setValues($data)
				->setWhere('job_order_no = "'. $data['job_order_no'] .'"')
				->runUpdate();
		//var_dump($result);
				if ($result) {
					$result = $this->updateJobOrderDetails($data2, $data['job_order_no']);
				}
		return $result;
	}

	public function getJOPagination($search, $sort, $customer, $filter, $datefilter, $limit, $fields) {
		$datefilter 		= isset($datefilter) ? htmlentities($datefilter) : ""; 
		$custfilter      	= isset($customer) ? htmlentities($customer) : ""; 
		$searchkey 		 	= isset($search) ? htmlentities($search) : "";
		$filter 		 	= isset($filter) ? htmlentities($filter) : "";
		$sort 		 		= isset($sort) ? htmlentities($sort) : "j.voucherno DESC";	
		$limit 		 		= isset($limit) ? htmlentities($limit) : "10";
	
		$datefilterArr		= explode(' - ',$datefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";

		$add_query 	= (!empty($searchkey)) ? "AND (j.job_order_no LIKE '%$searchkey%' OR p.partnername LIKE '%$searchkey%') " : "";
		$add_query .= (!empty($datefilter) && !is_null($datefilterArr)) ? "AND j.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
		$add_query .= (!empty($custfilter) && $custfilter != 'none') ? "AND p.partnercode = '$custfilter' " : "";
		
		if( !empty($filter) && $filter == 'prepared')
		{
			$add_query 	.=	" AND j.stat = 'prepared' ";
		}
		else if( !empty($filter) && $filter == 'partial' )
		{
			$add_query 	.= 	" AND j.stat = 'partial' ";
		}
		else if( !empty($filter) && $filter == 'completed' )
		{
			$add_query 	.= 	" AND j.stat = 'completed' ";
		}
		else if( !empty($filter) && $filter == 'cancelled' )
		{
			$add_query 	.= 	" AND j.stat = 'cancelled' ";
		}
		else if( $filter == 'all' )
		{
			$add_query 	.= 	" ";
		}

		$result = $this->db->setTable("job_order j")
							->setFields($fields)
							//->setWhere(1)
							->innerJoin('partners p ON j.customer = p.partnercode')
							->setWhere("j.stat!='temporary' $add_query")
							->setOrderBy($sort)
							->setLimit($limit)
							->runPagination();
							// echo $this->db->getQuery();
		return $result;
	}

	public function getJOByID($fields, $voucherno) {
		return $this->db->setTable('job_order')
						->setFields($fields)
						->setWhere("job_order_no = '$voucherno'")
						->runSelect()
						->getRow();
						//echo $this->db->getQuery();
	}

	public function getJRByID($fields, $voucherno) {
		$result = $this->db->setTable('job_release')
							->setFields($fields)
							->setWhere("job_release_no = '$voucherno'")
							->setLimit(1)
							->runSelect()
							->getRow();
		
		return $result;
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

	public function getJobOrder($fields, $fields2, $voucherno) {
		$count = $this->db->setTable('job_release')
				->setFields('*')
				->setWhere("job_order_no = '$voucherno' AND stat != 'cancelled'")
				->runSelect()
				->getResult();

				if($count){
					$result1 = $this->db->setTable('job_order_details jod')
							->setFields($fields2)
							->leftJoin('items i ON i.itemcode = jod.itemcode')
							->leftJoin('bomdetails bom ON bom.item_code = jod.itemcode')
							->leftJoin('warehouse w ON w.warehousecode = jod.warehouse')
							->leftJoin('job_release jr ON jod.linenum = jr.linenum AND jod.job_order_no = jr.job_order_no AND jr.itemcode = jod.itemcode')
							->setWhere("jod.job_order_no = '$voucherno' AND jr.stat != 'cancelled' AND i.itemgroup = 'goods'")
							->setOrderBy('linenum')
							->setGroupBy('jr.job_order_no , jr.linenum')
							->runSelect()
							->getResult();
				}else{
					$result1 = $this->db->setTable('job_order_details jod')
							->setFields($fields)
							->leftJoin('items i ON i.itemcode = jod.itemcode')
							->leftJoin('bomdetails bom ON bom.item_code = jod.itemcode')
							->leftJoin('warehouse w ON w.warehousecode = jod.warehouse')
							->setWhere("jod.job_order_no = '$voucherno' AND i.itemgroup = 'goods'")
							->setOrderBy('linenum')
							->setGroupBy('jod.linenum')
							->runSelect()
							->getResult();
				}

		return $result1;
}

	public function getQty($jobreleaseno) {
		$result = $this->db->setTable('job_release')
							->setFields('quantity,itemcode,linenum,serialnumbers')
							->setWhere("job_release_no = '$jobreleaseno'")
							->setOrderBy('linenum')
							->runSelect()
							->getResult();
		return $result;
	}

	public function getIssuedQty($job_order_no) {
		$result = $this->db->setTable('job_release')
							->setFields('SUM(quantity) issuedqty, itemcode, linenum')
							->setWhere("job_order_no = '$job_order_no' AND stat != 'cancelled'")
							->setGroupBy('itemcode')
							->setOrderBy('linenum')
							->runSelect()
							->getResult();

		return $result;
	}

	public function updateIssueParts($job_release_no,$data) {
		$data['stat'] = 'released';
		$this->db->setTable('job_release')
					->setWhere("job_release_no = '$job_release_no'")
					->runDelete();
					
		$result = $this->db->setTable('job_release')
							->setValuesFromPost($data)
							->runInsert();
							
		foreach ($data['serialnumbers'] as $row) {
			if ($row != "") {
				$ids = explode(",", $row);
				foreach ($ids as $id) {
					
					$this->db->setTable('items_serialized')
										->setValues(array('stat'=>'Not Available'))
										->setWhere("id = '$id'")
										->runUpdate();
				}
			}
		}
							
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

		public function getSerialList($itemcode, $search, $voucherno, $linenum, $serialnumbers, $task) {
			$cond = '';
			if($task == 'ajax_edit'){
				if($serialnumbers != ''){
					$cond = " OR id IN($serialnumbers)";
				}
			}else{
				$cond = '';
			}
			$condition = '';
			if ($search) {
				$condition .= $this->generateSearch($search, array('serialno', 'engineno', 'chassisno'));
			}
			$result	= $this->db->setTable('items_serialized')
									->setFields(array('companycode', 'id', 'itemcode', 'serialno', 'engineno', 'chassisno', 'stat'))
									->setWhere("itemcode = '$itemcode' AND stat = 'Available' $cond")
									->runPagination();
			
			return $result;
		}

	public function getJOSerials($itemcode, $voucherno, $linenum) {
			$result = $this->db->setTable('job_release') 
							->setFields('serialnumbers')
							->setWhere("itemcode='$itemcode' AND job_order_no='$voucherno' AND linenum='$linenum'")
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
		//echo $this->db->getQuery();
		return $result;
	}

	// public function retrieveIssuedQty($itemcode,$job_order_no)
	// {
	// 	$result = $this->db->setTable('job_release')
	// 						->setFields("SUM(quantity) issuedqty")
	// 						->setWhere("itemcode = '$itemcode' AND job_order_no = '$job_order_no' AND stat != 'cancelled'")
	// 						->setOrderBy('linenum')
	// 						->runSelect()
	// 						->getResult();
	// 	return $result;
	// }

	public function retrieveBundleDetails($itemcode) {
		// $query 		=	"SELECT * FROM 
		// 					(SELECT i.itemcode,0 as BaseQty, i.itemdesc as detailparticular, i.uom_base as uom, '' as parentcode, i.bundle as bundle
		// 					FROM items i
		// 					WHERE i.stat = 'active'
		// 					UNION
		// 					SELECT bd.item_code as itemcode, bd.quantity as BaseQty, bd.detailsdesc as detailparticular, bd.uom as uom, b.bundle_item_code as parentcode, i.bundle as bundle
		// 					FROM items i
		// 					LEFT JOIN bom b ON b.bundle_item_code = i.itemcode AND b.companycode = i.companycode 
		// 					LEFT JOIN bomdetails bd ON bd.bom_code = b.bom_code AND bd.companycode = b.companycode
		// 					WHERE status = 'active' ) a 
		// 				WHERE a.itemcode = '$itemcode' OR a.parentcode =  '$itemcode'";
		
		// $query 		=	"SELECT * FROM 
		// 					( SELECT bd.item_code as itemcode, bd.quantity as BaseQty, bd.detailsdesc as detailparticular, bd.uom as uom, b.bundle_item_code as parentcode, i.bundle as bundle
		// 					FROM items i
		// 					LEFT JOIN bom b ON b.bundle_item_code = i.itemcode AND b.companycode = i.companycode 
		// 					LEFT JOIN bomdetails bd ON bd.bom_code = b.bom_code AND bd.companycode = b.companycode
		// 					WHERE status = 'active' ) a 
		// 				WHERE a.itemcode = '$itemcode' OR a.parentcode =  '$itemcode'";
        //     $result = 	$this->db->setTable("($query) main")
        //                 ->setFields('main.itemcode AS itemcode,main.BaseQty AS BaseQty,main.detailparticular as detailparticular,main.uom as uom, main.parentcode as parentcode, main.bundle as isbundle')
        //                 ->runSelect(false)
		// 				->getResult();

		$result	= $this->db->setTable('bomdetails bd')
								->setFields('bd.item_code as itemcode, bd.quantity as BaseQty, bd.detailsdesc as detailparticular, bd.uom as uom, b.bundle_item_code as parentcode, "0" as isbundle')
								->leftJoin('bom b ON  b.bom_code = bd.bom_code AND bd.companycode = b.companycode ')
								->setWhere("b.bundle_item_code = '$itemcode' AND b.status = 'active' ")
								->runSelect()
								->getResult();
			//echo $this->db->getQuery();
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
		$voucherno 	='';
		$sourceno 	= $data['job_order_no'];
		$linenum 	= implode(',', $data['linenum']);
		$orderstat 	= 'completed';
		$data['stat'] = 'released';	
		
		$result = $this->db->setTable('job_release')
							->setValuesFromPost($data)
							->runInsert();
							
		foreach ($data['serialnumbers'] as $row) {
			if ($row != "") {
				$ids = explode(",", $row);
				foreach ($ids as $id) {
					$this->db->setTable('items_serialized')
										->setValues(array('stat'=>'Not Available'))
										->setWhere("id = '$id'")
										->runUpdate();
				}
			}
		}
		
		$orderqty = $this->db->setTable('job_order_details')
							->setFields('linenum, quantity')
							->setWhere("job_order_no='$sourceno'")
							->runSelect()
							->getResult();
		foreach ($orderqty as $key => $row) {
			foreach ($data['linenum'] as $key => $value) {
				if ($row->linenum == $value) {
					$row->quantity -=  $data['quantity'][$key];
				}
			}
			if ($row->quantity) {
				$orderstat = 'partial';
			}
		}

		$order_value = array('stat' => $orderstat);
		$orderupdate = $this->db->setTable('job_order')
								->setValues($order_value)
								->setWhere("job_order_no='$sourceno'")
								->runUpdate();

		return $result;
	}
	public function getIssuedPartsNo($jobno) {
		$result	= $this->db->setTable('job_release j')
								->setFields('DISTINCT(job_release_no) jrno')
								->leftJoin('job_order_details jod ON jod.job_order_no = j.job_order_no  and jod.itemcode = j.itemcode')
								->leftJoin('warehouse w ON w.warehousecode = j.warehouse')
								->leftJoin('journalvoucher jv ON jv.referenceno = j.job_release_no')
								->setWhere("j.job_order_no = '$jobno' AND (parentcode = '' OR parentcode IS NULL) AND j.stat NOT IN ('cancelled')")
								->setGroupBy('j.job_release_no, j.itemcode')
								->setOrderBy('job_release_no,j.linenum')
								->runSelect()
								->getResult();
								// echo $this->db->getQuery();
		return $result;
	}

	public function getIssuedParts($jobno) {
		$result	= $this->db->setTable('job_release j')
								->setFields('j.job_order_no,j.itemcode,detailparticulars,j.warehouse,j.quantity,j.unit,w.description')
								->leftJoin('job_order_details jod ON jod.job_order_no = j.job_order_no  and jod.itemcode = j.itemcode')
								->leftJoin('warehouse w ON w.warehousecode = j.warehouse')
								->setWhere("j.job_release_no = '$jobno' AND (parentcode = '' OR parentcode IS NULL)")
								->setGroupBy('j.job_release_no, j.itemcode')
								->setOrderBy('job_release_no,j.linenum')
								->runSelect()
								->getResult();
								// echo $this->db->getQuery();
		return $result;
	}

	public function deleteJobRelease($id) {
		$result	= $this->db->setTable('job_release')
				->setValues(array('stat'=>'cancelled'))
				->setWhere("job_release_no = '$id'")
				->runUpdate();

			if ($result) {
				$this->log->saveActivity("Deleted Job Release [$id]");
			}

		return $result;
	}
		
	// public function checkIsBundle($data) {

	// 	$result = $this->db->setTable('bom')
	// 						->setFields('bundle_item_code')
	// 						->setWhere("bundle_item_code IN ('$data')")
	// 						->runSelect()
	// 						->getResult();
		
	// 	return $result;
	// }

	public function getNextId($table,$field,$subcon = "") {
		$result = $this->db->setTable($table)
			->setFields('MAX('.$field.') as current')
			->setWhere(" $field != '' " . $subcon)
			->runSelect()
			->getRow();

		if ($result) {
			$return = $result->current += 1;
		} else {
			$return = '1';
		}
		return $return;
	}

	public function getCurrentId($table,$voucherno) {
		$result = $this->db->setTable($table)
			->setFields('attachment_id')
			->setWhere(" reference='$voucherno'")
			->runSelect()
			->getRow();

		if ($result) {
			$return = $result->attachment_id;
		} else {
			$return = '1';
		}
		return $return;
	}

	public function uploadAttachment($data) {
		$reference = $data['reference'];
		$result = $this->db->setTable('job_order_attachments')
							->setValues($data)
							->runInsert();
		if ($result) {
			$this->log->saveActivity("Approve [$reference] with attachment");		
		}
		return $result;
	}

	public function replaceAttachment($data) {
		$reference = $data['reference'];
		$result = $this->db->setTable('job_order_attachments')
							->setValues($data)
							->setWhere("reference='$reference'")
							->runUpdate();
		if ($result) {
			$this->log->saveActivity("Update attachment with [$reference]");		
		}
		return $result;
	}

	public function deleteAttachment($data) {
		$attachment_file 	= $data['attachment_file'];
		$reference 			= $data['reference'];
		
		if(isset($data['attachment_id']) && !empty($data['attachment_id'])){
			$attachment_id 		= $data['attachment_id'];
		}else{
			
			$attachment 	= $this->db->setTable('job_order_attachments')
								->setFields('attachment_id')
								->setWhere(" reference = '$refrence' AND attachment_name = '$attachment_file' ")
								->runSelect()
								->getRow();

			$attachment_id 	= $attachment->attachment_id;
		}
		
		/**
		 * Try to delete file from directory before running the SQL
		 */
		if (!unlink('files/'.$attachment_file))
		{
			$result = false;
		}
	  	else
		{
			$result = true;
		}
		
		if($result)
		{
			$result = $this->db->setTable('job_order_attachments')
				->setWhere(" reference = '$reference' AND attachment_id = '$attachment_id' ");
			$this->db->runDelete();
			
			if($result)
			{
				$this->log->saveActivity("Delete Attachment [$attachment_file] For [$reference]");
			}
		}
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

	public function selectAttachment($jobno, $fields) {
		$result = $this->db->setTable('job_order_attachments')
						->setFields($fields)
						->setWhere("reference = '$jobno'")
						->runSelect()
						->getRow();
		// 	var_dump($result);
		return $result;
	}

	public function retrieve_bom_qty($bundle){
		$result = $this->db->setTable('bom b')
						   ->leftJoin('bomdetails dtl ON dtl.bom_code = b.bom_code AND dtl.companycode = b.companycode')
						   ->setFields('dtl.item_code itemcode, dtl.quantity')
						   ->setWhere("b.bundle_item_code = '$bundle'")
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

	public function getJOheader($voucherno){
		$result = $this->db->setTable('job_order jo')
						->setFields("job_order_no voucherno, customer, reference, notes, stat, transactiondate")
						->setWhere("job_order_no='$voucherno'")
						->runSelect()
						->getRow();

		return $result;
	}

	public function getJOcontent($voucherno){
		$result = $this->db->setTable('job_order_details jo')
						->setFields("itemcode, detailparticular, quantity, uom")
						->setWhere("job_order_no='$voucherno'")
						->runSelect()
						->getResult();

		return $result;
	}

	public function checkForParts($voucherno){
		$result = $this->db->setTable('job_release')
						->setFields("stat")
						->setWhere("job_order_no='$voucherno' AND stat='released'")
						->runSelect()
						->getRow();

		return $result;
	}
}