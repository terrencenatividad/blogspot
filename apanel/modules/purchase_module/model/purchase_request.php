<?php
	class purchase_request extends wc_model
	{
		
		public function __construct() {
			parent::__construct();
			$this->logs  			= new log;
		}
	
		public function retrieverequestorList()
		{
			$result = $this->db->setTable('wc_users')
						->setFields("username ind, CONCAT(firstname, ' ' , lastname) val")
						// ->setWhere("partnercode != '' AND partnertype = 'supplier' AND stat = 'active'")
						->setWhere("stat = 'active'")
						->setOrderBy("val")
						->runSelect()
						->getResult();
			return $result;
		}

		public function retrieverequestorDetails($vendor_code)
		{
			$fields = "address1, tinno, terms, email, partnername AS name";
			$cond 	= "partnercode = '$vendor_code' AND partnertype = 'supplier' ";

			$result = $this->db->setTable('partners')
								->setFields($fields)
								->setWhere($cond)
								->setLimit('1')
								->runSelect()
								->getRow();
								// echo $this->db->getQuery();

			return $result;
		}

		public function retrieveItemDetails($itemcode)
		{
			$fields = "i.itemname as itemname, i.itemdesc as itemdesc, i.weight as weight , i.uom_base, u.uomcode as uomcode";
			$cond 	= "i.itemcode = '$itemcode'";
			$order 	= "p.linenum DESC";

			$result = $this->db->setTable('items i')
								->leftJoin('uom u ON u.uomcode = i.uom_purchasing AND u.companycode = i.companycode')
								// ->leftJoin('price_average p ON p.itemcode = i.itemcode AND p.companycode = i.companycode')
								->setFields($fields)
								->setWhere($cond)
								->setLimit('1')
								->runSelect()
								->getRow();
								// echo $this->db->getQuery();

			return $result;
		}

		public function retrieveProformaList()
		{
			$result = $this->db->setTable('proforma')
						->setFields("proformacode ind, proformadesc val")
						->setOrderBy("val")
						->runSelect()
						->getResult();
			
			return $result;
		}

		public function retrieveCashAccountClassList()
		{
			$result = $this->db->setTable('chartaccount chart')
						->setFields("accountname ind , accountname val")
						->leftJoin("accountclass as class USING(accountclasscode)")
						->setWhere("class.accountclasscode = 'CASH'  AND chart.accounttype != 'P'")
						->setOrderBy("class.accountclass")
						->runSelect()
						->getResult();
			
			return $result;
		}
		
		public function retrieveListing($data)
		{
			$add_query = '';
			$fields 			= array('s.duedate','s.voucherno', 's.requestor', 's.transactiondate','s.netamount','s.department','s.remarks',"IF(s.duedate < CURDATE() AND s.stat = 'open', 'expired', s.stat)  stat");

			$sort 		 		= isset($data['sort']) ? htmlentities($data['sort']) : "";
			$limit 		 		= isset($data['limit']) ? htmlentities($data['limit']) : "";
			$filter 		 	= isset($data['filter']) ? htmlentities($data['filter']) : "";
			$daterangefilter 	= isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
			$custfilter      	= isset($data['requestor']) ? htmlentities($data['requestor']) : ""; 
			$searchkey 		 	= isset($data['search']) ? htmlentities($data['search']) : "";
			$filter 		 	= isset($data['filter']) ? htmlentities($data['filter']) : "";
			$datefilterArr		= explode(' - ',$daterangefilter);
			$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
			$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
			// $add_query   = (!empty($filter) && $filter !="all") ? "AND s.stat = '$filter'" : "";
			$add_query 	.= (!empty($searchkey)) ? "AND (s.voucherno LIKE '%$searchkey%' OR p.username LIKE '%$searchkey%') OR p.firstname LIKE '%$searchkey%' OR p.lastname LIKE '%$searchkey%' " : "";
			$add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND s.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
			$add_query .= (!empty($custfilter) && $custfilter != 'none') ? "AND p.username = '$custfilter' " : "";

			if ($filter == 'all') {
				$add = "s.stat NOT IN ('temporary')";
			} else {
				$add= "IF(s.duedate < CURDATE() AND s.stat = 'open', 'expired', s.stat) = '$filter'";
			}

			$result = $this->db->setTable('purchaserequest s')
							->setFields($fields)
							->leftJoin("wc_users p ON p.username	 = s.requestor  AND p.stat = 'active'")
							->setWhere($add . $add_query)
							->setOrderBy($sort)
							->setGroupBy('s.voucherno')
							->runPagination();
							// echo $this->db->getQuery();
			return $result ;

		}
		
		public function retrieveExistingREQ($voucherno)
		{	 
			$retrieved_data =	array();
			
			$header_fields 	= 	"voucherno, transactiondate, duedate, amount, discounttype, discountamount, netamount, taxamount,department,remarks,requestor";

			$condition 		=	" voucherno = '$voucherno' ";
			
			$retrieved_data['header'] 	= 	$this->db->setTable('purchaserequest')
											// ->innerJoin('')
											->setFields($header_fields)
											->setWhere($condition)
											->setLimit('1')
											->runSelect()
											->getRow();
											// echo $this->db->getQuery();
			
			// Retrieve Vendor Details
			$requestor_code 			  = 	$retrieved_data['header']->requestor;
			
			$retrieved_data['requestor']  =		$this->retrieverequestorDetails($requestor_code);

			// Retrieve Details
			$detail_fields 			= "itemcode, detailparticular, unitprice, round(receiptqty,0) as receiptqty, receiptuom, taxcode, taxrate, amount";
			$condition 				= " voucherno = '$voucherno' ";
			
			$retrieved_data['details'] = 	$this->db->setTable('purchaserequest_details')
											->setFields($detail_fields)
											->setWhere($condition)
											->runSelect()
											->getResult();
											//echo $this->db->getQuery();
											
			return $retrieved_data;
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

		public function getTaxCode($taxtype,  $orderby = "")
		{
			$result = $this->db->setTable('fintaxcode')
						->setFields("taxrate ind, longname val")
						->setWhere("taxtype = '$taxtype'")
						->setOrderBy($orderby)
						->runSelect(false)
						->getResult();

			return $result;
		}

		public function processTransaction($data, $task, $voucher = "")
		{
			$mainInvTable		= "purchaserequest";
			$detailInvTable		= "purchaserequest_details";
		
			$insertResult		= 0;
			$errmsg				= array();

			// Journal Data
			$voucherno			= (isset($data['h_voucher_no']) && (!empty($data['h_voucher_no']))) ? htmlentities(addslashes(trim($data['h_voucher_no']))) : "";
			
			$requestor			= (isset($data['requestor']) && (!empty($data['requestor']))) ? htmlentities(addslashes(trim($data['requestor']))) : "";

			$department			= (isset($data['department']) && (!empty($data['department']))) ? htmlentities(addslashes(trim($data['department']))) : "";
			
			$transactiondate	= (isset($data['transaction_date']) && (!empty($data['transaction_date']))) ? htmlentities(addslashes(trim($data['transaction_date']))) : "";
			
			$duedate 			= (isset($data['due_date']) && (!empty($data['due_date']))) ? htmlentities(addslashes(trim($data['due_date']))) : "";

			$remarks			= (isset($data['remarks']) && (!empty($data['remarks']))) ? htmlentities(addslashes(trim($data['remarks']))) : "";
			
			$terms				= (isset($data['requestor_terms']) && (!empty($data['requestor_terms']))) ? htmlentities(addslashes(trim($data['requestor_terms']))) : 0;
			
			$vat_sales 			= (isset($data['t_vatsales']) && (!empty($data['t_vatsales']))) ? htmlentities(addslashes(trim($data['t_vatsales']))) : "";

			$vat_exempt 		= (isset($data['t_vatexempt']) && (!empty($data['t_vatexempt']))) ? htmlentities(addslashes(trim($data['t_vatexempt']))) : "";

			$vat 	 			= (isset($data['t_vat']) && (!empty($data['t_vat']))) ? htmlentities(addslashes(trim($data['t_vat']))) : "";

			$subtotal 			= (isset($data['t_subtotal']) && (!empty($data['t_subtotal']))) ? htmlentities(addslashes(trim($data['t_subtotal']))) : "";

			$totalamount 		= (isset($data['t_total']) && (!empty($data['t_total']))) ? htmlentities(addslashes(trim($data['t_total']))) : "";

			$discounttype 		= (isset($data['discounttype']) && (!empty($data['discounttype']))) ? htmlentities(addslashes(trim($data['discounttype']))) : "";

			$discount_amount 	= (isset($data['t_discount']) && (!empty($data['t_discount']))) ? htmlentities(addslashes(trim($data['t_discount']))) : "";

			$isExist			= $this->getValue($mainInvTable, array("stat"), "voucherno = '$voucherno' AND stat = 'open'");
			
			$status				= (!empty($isExist)) ? "open" : "temporary";
			
			/**TRIM COMMAS FROM AMOUNTS**/
			$subtotal			= str_replace(',','',$subtotal);
			$totalamount		= str_replace(',','',$totalamount);

			/**FORMAT DATES**/
			$transactiondate	= date("Y-m-d",strtotime($transactiondate));
			$duedate 			= date("Y-m-d",strtotime($duedate));
			$period				= date("n",strtotime($transactiondate));
			$fiscalyear			= date("Y",strtotime($transactiondate));

			$post_header['voucherno'] 			=	$voucherno;
			$post_header['transactiondate'] 	=	$transactiondate;
			$post_header['duedate'] 			= 	$duedate;
			$post_header['requestor'] 			=	$requestor;
			$post_header['department'] 			=	$department;
			$post_header['fiscalyear'] 			=	$fiscalyear;
			$post_header['period'] 				=	$period;
			$post_header['transtype'] 			=	"REQ";
			$post_header['stat'] 		 		= 	$status;
			$post_header['term'] 				= 	$terms;
			$post_header['remarks'] 			=	$remarks;
			$post_header['amount'] 				= 	$subtotal;
			$post_header['discounttype'] 		=	$discounttype;
			$post_header['discountamount'] 		=	$discount_amount;
			$post_header['netamount'] 			=	$totalamount;
			$post_header['taxcode'] 			=	'VAT';
			$post_header['taxamount'] 			=	$vat;
			$post_header['wtaxcode'] 			=	'';
			$post_header['wtaxamount'] 			=	'';
			$post_header['wtaxrate'] 			=	'';
			// $post_header['vat_sales'] 			= 	$vat_sales;
			// $post_header['vat_exempt'] 			=	$vat_exempt;
			// $post_header['vat_zerorated'] 		=	'';

			/**INSERT HEADER**/
			if($status=='temporary' && $task == 'create')
			{	
				// Delete temporary data
				$this->db->setTable($mainInvTable)
					->setWhere("voucherno = '$voucherno'");
				$insertResult = $this->db->runDelete();
				// echo $this->db->getQuery();

				if($insertResult)
				{
					// Handle Insert
					$this->db->setTable($mainInvTable)
						->setValues($post_header);
			
					$insertResult = $this->db->runInsert();
					// echo $this->db->getQuery();
				}
			}
			else if( $task == 'create' )
			{
				$this->db->setTable($mainInvTable)
					->setValues($post_header)
					->setWhere("voucherno = '$voucherno' ");
					
				
				$insertResult = $this->db->runUpdate();
				
			
				// echo $this->db->getQuery();
			}
			else if( $task == 'edit')
			{
				$cond 	=	"voucherno  = '$voucher' ";

				$insertResult = $this->db->setTable($mainInvTable)
								->setValues($post_header)
								->setWhere($cond)
								->runUpdate();
								// echo $this->db->getQuery();
			}
			
			/**INSERT DETAILS**/
			foreach($data as $postIndex => $postValue)
			{
				if($postIndex == 'itemcode' || $postIndex=='detailparticulars' ||  $postIndex == 'quantity' ||  $postIndex == 'uom' ||	$postIndex == 'itemprice' || $postIndex=='taxrate' || $postIndex == 'taxamount' || $postIndex == 'amount' || $postIndex == 'h_amount')
				{
					$a		= '';
					
					foreach($postValue as $postValueIndex => $postValueIndexValue)
					{
						if($postIndex == 'amount' || $postIndex == 'h_amount' || $postIndex == 'taxamount' || $postIndex == 'itemprice')
						{
							$a = str_replace(',', '', $postValueIndexValue);
						}
						else
						{
							$a = htmlentities(addslashes($postValueIndexValue));
						}
						
						$arrayData[$postIndex][$postValueIndex] = $a;
					}	
				}
			}

			/**START OF INSERT QUERY**/
			foreach($arrayData as $arrayDataIndex => $arrayDataValue)
			{
				foreach($arrayDataValue as $postValueIndex => $postValueIndexValue)
				{											
					$tempArray[$postValueIndex][$arrayDataIndex] = $postValueIndexValue;
				}
			}

			$linenum			= 1;
			$tempArr 			= array();
			


			foreach($tempArray as $tempArrayIndex => $tempArrayValue)
			{
			
				if( $tempArrayValue['itemcode'] != "" )
				{
					$data_insert["voucherno"]         	= $voucherno;
					$data_insert['transtype']         	= 'REQ';
					$data_insert['linenum']	        	= $linenum;
					$data_insert['itemcode']	  		= $tempArrayValue['itemcode'];
					$data_insert['detailparticular']	= $tempArrayValue['detailparticulars'];
					$data_insert['receiptqty']			= $tempArrayValue['quantity'];
					$data_insert['receiptuom']			= $tempArrayValue['uom'];
					$data_insert['stat']	  			= $status;

					$linenum++;
					$tempArr[] 						= $data_insert;
				}
					
			}

			/**INSERT IR DETAILS**/
			$isDetailExist	= $this->getValue($detailInvTable, array("COUNT(*) as count"),"voucherno = '$voucherno'");

			if($isDetailExist[0]->count == 0 && $task == 'create' )
			{
				$this->db->setTable($detailInvTable)
					->setValues($tempArr);

				$insertResult = $this->db->runInsert();
			}
			else if( $task == 'create' || $task == 'edit' )
			{
				// Delete data if existing
				$this->db->setTable($detailInvTable)
					->setWhere("voucherno = '$voucherno' ");
				$this->db->runDelete();

				// Then insert data
				$this->db->setTable($detailInvTable)
					->setValues($tempArr);
				$insertResult = $this->db->runInsert();
				// echo $this->db->getQuery();
			}
			
			if(!$insertResult)
			{
				$errmsg[] 		= "The system has encountered an error in saving. Our team is currently checking on this.<br/>";
			}

			return $errmsg;
		}

		public function updateData($data, $table, $cond)
		{
			$result = $this->db->setTable($table)
					->setValues($data)
					->setWhere($cond)
					->runUpdate();
					// echo $this->db->getQuery();
			return $result;
		}

		public function deleteData($table, $cond)
		{	
			$this->db->setTable($table)
					->setWhere($cond);

			$result = $this->db->runDelete();

			return $result;
		}

		public function insertrequestor($data)
		{
			$data['partnertype']   = "requestor";
			$data['stat'] 		   = "active";

			$result = $this->db->setTable('partners')
					->setValues($data)
					->runInsert();
					// echo $this->db->getQuery();
			return $result;
		}

		public function delete_temp_transactions($voucherno, $table, $detailTable)
		{
			$result = $this->db->setTable($table)
					->setWhere(" voucherno = '$voucherno' AND stat = 'temporary' ")
					->runDelete();
				
			if( $result )
			{
				$result = $this->db->setTable($detailTable)
					->setWhere(" voucherno = '$voucherno' AND stat = 'temporary' ")
					->runDelete();
			}

			return $result;
		}

		public function completeREQ($voucherno) {
			
		$result	= $this->db->setTable('purchaserequest')
							->setValues(array('stat'=>'posted'))
							->setWhere("voucherno = '$voucherno'")
							->setLimit(count($voucherno))
							->runUpdate();
		if ($result) {
			if ($result) {
				$log_id = $voucherno;
				$this->logs->saveActivity("Tagged as Complete - Purchase Request [$log_id]");
			}
			$result = $this->db->setTable('purchaserequest_details')
							->setValues(array('stat'=>'posted'))
							->setWhere("voucherno = '$voucherno'")
							->setLimit('')
							->runUpdate();
		}
		return $result;
		}

		public function deleteREQ($voucherno) {
		$result	= $this->db->setTable('purchaserequest')
							->setValues(array('stat'=>'cancelled'))
							->setWhere("voucherno = '$voucherno' ")
							->setLimit(count($voucherno))
							->runUpdate();
		if ($result) {
			if ($result) {
				$log_id = $voucherno;
				$this->logs->saveActivity("Cancelled Request [$log_id]");
			}
			$result = $this->db->setTable('purchaserequest_details')
							->setValues(array('stat'=>'cancelled'))
							->setWhere("voucherno = '$voucherno' ")
							->setLimit('')
							->runUpdate();
		}
		return $result;
		}


		public function export_main($data)
		{
			$add_query = '';
			$fields 			= array('s.duedate','s.voucherno', 's.requestor', 's.transactiondate','s.netamount','s.department','s.remarks',"IF(s.duedate < CURDATE() AND s.stat = 'open', 'expired', s.stat)  stat");

			$sort 		 		= isset($data['sort']) ? htmlentities($data['sort']) : "";
			$limit 		 		= isset($data['limit']) ? htmlentities($data['limit']) : "";
			$filter 		 	= isset($data['filter']) ? htmlentities($data['filter']) : "";
			$daterangefilter 	= isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
			$custfilter      	= isset($data['custfilter']) ? htmlentities($data['custfilter']) : ""; 
			$searchkey 		 	= isset($data['search']) ? htmlentities($data['search']) : "";
			$filter 		 	= isset($data['filter']) ? htmlentities($data['filter']) : "";
			$datefilterArr		= explode(' - ',$daterangefilter);
			$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
			$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
			// $add_query   = (!empty($filter) && $filter !="all") ? "AND s.stat = '$filter'" : "";
			$add_query 	.= (!empty($searchkey)) ? "AND (s.voucherno LIKE '%$searchkey%' OR p.username LIKE '%$searchkey%') " : "";
			$add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND s.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
			$add_query .= (!empty($custfilter) && $custfilter != 'none') ? "AND p.username = '$custfilter' " : "";

			if ($filter == 'all') {
				$add = "s.stat NOT IN ('temporary','cancelled')";
			} else {
				$add= "IF(s.duedate < CURDATE() AND s.stat = 'open', 'expired', s.stat) = '$filter'";
			}

			$result = $this->db->setTable('purchaserequest s')
							->setFields($fields)
							->leftJoin("wc_users p ON p.username	 = s.requestor  AND p.stat = 'active'")
							->setWhere($add . $add_query)
							->setOrderBy($sort)
							->setGroupBy('s.voucherno')
							->runSelect()
							->getResult();

		}
	}
?>