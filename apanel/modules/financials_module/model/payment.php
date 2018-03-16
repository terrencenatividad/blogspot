<?php
class payment extends wc_model
{
	public function __construct() 
	{
		parent::__construct();
		$this->logs = new log();
	}

	public function retrieveVendorList()
	{
		$result = $this->db->setTable('partners')
					->setFields("partnercode ind, CONCAT( first_name, ' ', last_name ), partnername val")
					->setWhere("partnercode != '' AND partnertype = 'supplier'  AND stat = 'active'")
					->setOrderBy("val")
					->runSelect()
					->getResult();
		
		return $result;
	}

	public function retrieveVendorDetails($vendor_code)
	{
		$fields = "address1, tinno, terms, email, CONCAT( first_name, ' ', last_name ), partnername AS name";
		$cond 	= "partnercode = '$vendor_code' AND partnertype = 'supplier' ";

		$result = $this->db->setTable('partners')
							->setFields($fields)
							->setWhere($cond)
							->setLimit('1')
							->runSelect()
							->getRow();

		return $result;
	}

	public function retrieveDisursementProformaList()
	{
		$result = $this->db->setTable('proforma')
					->setFields("proformacode ind, proformadesc val")
					->setWhere("transactiontype = 'Disbursement Voucher' ")
					->setOrderBy("val")
					->runSelect()
					->getResult();
		
		return $result;
	}

	public function retrieveCashAccountList()
	{
		$result = $this->db->setTable('chartaccount chart')
							->setFields("id ind , CONCAT(segment5, ' - ', accountname) val")
							->leftJoin("accountclass as class USING(accountclasscode)")
							->setWhere("chart.accounttype != 'P'")
							// ->setWhere("class.accountclasscode = 'CASH' ")
							->setOrderBy("class.accountclass")
							->runSelect()
							->getResult();
		return $result;
	}
	
	public function getValue($table, $cols = array(), $cond, $orderby = "")
	{
		$result = $this->db->setTable($table)
					->setFields($cols)
					->setWhere($cond)
					->setOrderBy($orderby)
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

	public function processTransaction($data, $task, $voucher = "")
	{
		$mainInvTable		= "paymentvoucher";
		$detailInvTable		= "pv_details";
		$chequeTable		= "pv_cheques"; 
		$applicationTable	= "pv_application"; 
	
		$this->datetime     = date("Y-m-d H:i:s");
		
		$insertResult		= 0;
		$errmsg				= array();

		$task 				= isset($data['h_task']) ? $data['h_task'] : "create";

		$voucherno			= (isset($data['h_voucher_no']) && (!empty($data['h_voucher_no']))) ? htmlentities(addslashes(trim($data['h_voucher_no']))) : "";
		
		$vendor				= (isset($data['vendor']) && (!empty($data['vendor']))) ? htmlentities(addslashes(trim($data['vendor']))) : "";
		
		$referenceno		= (isset($data['referenceno']) && (!empty($data['referenceno']))) ? htmlentities(addslashes(trim($data['referenceno']))) : "";

		$proformacode		= (isset($data['proformacode']) && (!empty($data['proformacode']))) ? htmlentities(addslashes(trim($data['proformacode']))) : "";
		
		$transactiondate	= (isset($data['transaction_date']) && (!empty($data['transaction_date']))) ? htmlentities(addslashes(trim($data['transaction_date']))) : "";
		
		$remarks			= (isset($data['remarks']) && (!empty($data['remarks']))) ? htmlentities(addslashes(trim($data['remarks']))) : "";
		
		$terms				= (isset($data['vendor_terms']) && (!empty($data['vendor_terms']))) ? htmlentities(addslashes(trim($data['vendor_terms']))) : 0;
		
		$totalamountdb		= (isset($data['total_debit']) && (!empty($data['total_debit']))) ? htmlentities(addslashes(trim($data['total_debit']))) : "";
		$totalamountcd		= (isset($data['total_credit']) && (!empty($data['total_credit']))) ? htmlentities(addslashes(trim($data['total_credit']))) : "";

		$paymenttype		= (isset($data['paymentmode']) && (!empty($data['paymentmode']))) ? htmlentities(addslashes(trim($data['paymentmode']))) : "";
		$paymentdiscount	= (isset($data['paymentdiscount']) && (!empty($data['paymentdiscount']))) ? htmlentities(addslashes(trim($data['paymentdiscount']))) : "";
		
		$isExist			= $this->getValue($mainInvTable, array("stat"), "voucherno = '$voucherno' AND stat = 'posted'");

		$status				= (!empty($isExist[0]->stat)) ? "posted" : "temporary";

		$totalamount 		= ($totalamountdb == $totalamountcd) ? $totalamountdb : 0;

		/**
		* Add amounts as part of the exchangerate update
		*/
		$amount				= (isset($data['h_amount']) && (!empty($data['h_amount']))) ? htmlentities(addslashes(trim($data['h_amount']))) : "";
		$exchangerate		= (isset($data['exchange_rate']) && (!empty($data['exchange_rate']))) ? htmlentities(addslashes(trim($data['exchange_rate']))) : "1.00";
		$convertedamount	= (isset($data['h_convertedamount']) && (!empty($data['h_convertedamount']) && $data['h_convertedamount'] != "0.00")) ? htmlentities(addslashes(trim($data['h_convertedamount']))) : $totalamount;

		/**TRIM COMMAS FROM AMOUNTS**/
		$totalamount		= str_replace(',','',$totalamount);
		$amount				= str_replace(',','',$amount);
		$exchangerate		= str_replace(',','',$exchangerate);
		$convertedamount	= str_replace(',','',$convertedamount);

		/**FORMAT DATES**/
		$transactiondate	= date("Y-m-d",strtotime($transactiondate));
		$period				= date("n",strtotime($transactiondate));
		$fiscalyear			= date("Y",strtotime($transactiondate));

		// For Payment Voucher header
		$post_header['voucherno'] 			=	$voucherno;
		$post_header['transactiondate'] 	=	$transactiondate;
		$post_header['fiscalyear'] 			=	$fiscalyear;
		$post_header['period'] 				=	$period;
		$post_header['currencycode']	  	= 	'PHP';
		$post_header['exchangerate'] 		=	$exchangerate;
		$post_header['referenceno'] 		=	$referenceno;
		$post_header['amount'] 				=	$totalamount;
		$post_header['convertedamount'] 	=	$convertedamount;
		$post_header['netamount'] 		    =	$totalamount;
		$post_header['vendor'] 				=	$vendor;
		$post_header['transtype'] 			=	"DV";
		$post_header['source'] 				=	"DV";
		$post_header['checkstat'] 			=	"unreleased";	
		$post_header['proformacode'] 		=	$proformacode;
		$post_header['particulars'] 		=	$remarks;
		$post_header['paymenttype'] 		=	$paymenttype;
		$post_header['stat'] 				=	$status;
		$post_header['postedby'] 			=	USERNAME;
		$post_header['postingdate'] 		=	$this->datetime;

		/**INSERT HEADER**/
		if($status=='temporary' && $task == 'create')
		{	
			// Delete temporary data
			$this->db->setTable($mainInvTable)
				->setWhere("voucherno = '$voucherno'");
			$insertResult = $this->db->runDelete();

			if($insertResult)
			{
				// Handle Insert
				// paymentvoucher
				$this->db->setTable($mainInvTable)
					->setValues($post_header);
		
				$insertResult = $this->db->runInsert();

				if(!$insertResult)
					$errmsg[] 		= "<li>Saving in Disbursement Voucher Header.</li>";

			}
		}
		else if( $task == 'create' )
		{
			// paymentvoucher
			$this->db->setTable($mainInvTable)
				->setValues($post_header)
				->setWhere("voucherno = '$voucherno' ");
			
			$insertResult = $this->db->runUpdate();

			if(!$insertResult)
				$errmsg[] 		=  "<li>Saving in Disbursement Voucher Header.</li>";

		}
		else if( $task == 'edit' )
		{
			$cond 	=	"voucherno = '$voucherno' ";

			$this->db->setTable($mainInvTable)
				->setValues($post_header)
				->setWhere($cond);
						
			$insertResult = $this->db->runUpdate();

			if(!$insertResult)
				$errmsg[] 		=  "<li>Updating in Disbursement Voucher Header.</li>";
		}

		/**INSERT TO APPLICATION TABLE**/
		$post_application['voucherno']		 = $voucherno;
		$post_application['transactiondate'] = $transactiondate;
		$post_application['transtype']		 = "DV";
		$post_application['linenum']		 = 1;
		$post_application['discount']		 = $paymentdiscount;
		$post_application['amount']			 = $totalamount;
		$post_application['currencycode']	 = 'PHP';
		$post_application['exchangerate']	 = $exchangerate;
		$post_application['convertedamount'] = $convertedamount;
		$post_application['forexamount']	 = 0;
		$post_application['stat']			 = $post_header['stat'];

		/**INSERT PV App**/
		$isDetailExist	= $this->getValue($applicationTable, array("COUNT(*) as count"),"voucherno = '$voucherno'");

		if($isDetailExist[0]->count == 0)
		{
			$insertResult = $this->db->setTable($applicationTable)
								->setValues($post_application)
								// ->buildInsert();
								->runInsert();
			
			// var_dump($insertResult);

			if(!$insertResult)
				$errmsg[] 		= "<li>Saving in Disbursement Voucher Application.</li>";
			
		}
		else
		{
			// Delete data if existing
			$insertResult = $this->db->setTable($applicationTable)
								->setWhere("voucherno = '$voucherno'")
								// ->buildDelete();
								->runDelete();

			if(!$insertResult)
				$errmsg[] 		= "<li>Saving in Disbursement Voucher Application.</li>";
			else
			{
				// Then insert data
				$insertResult = $this->db->setTable($applicationTable)
									->setValues($post_application)
									// ->buildInsert();
									->runInsert();
				
				// var_dump($insertResult);
				
				if(!$insertResult)
					$errmsg[] 		= "<li>Saving in Disbursement Voucher Application.</li>";
			}
			
		}


		foreach($data as $postIndex => $postValue)
		{
			if($postIndex == 'costcenter' || $postIndex == 'accountcode' ||  $postIndex=='detailparticulars' || $postIndex=='debit' || $postIndex=='credit' )
			{
				$a		= '';
				
				foreach($postValue as $postValueIndex => $postValueIndexValue)
				{
					if($postIndex == 'debit' || $postIndex == 'credit')
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

			if(($postIndex == 'chequeaccount' || $postIndex == 'chequenumber' || $postIndex == 'chequedate' || $postIndex == 'chequeamount') && !empty($postValue) )
			{
				$b		= '';

				foreach($postValue as $postValueIndex => $postValueIndexValue)
				{
					if($postIndex == 'chequeamount')
					{
						// echo "\n1";
						$b = str_replace(',', '', $postValueIndexValue);
					}
					else if($postIndex == 'chequedate')
					{
						// echo "\n2";
						$b = ($postValueIndexValue != '') ? $this->date->dateDbFormat($postValueIndexValue) : "0000-00-00";
					}
					else
					{
						// echo "\n3";
						$b = htmlentities(addslashes(trim($postValueIndexValue)));
					}
					
					$chequeData[$postIndex][$postValueIndex] = $b;
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

		/**CHEQUE DETAILS**/
		if(!empty($chequeData))
		{
			foreach($chequeData as $chequeDataIndex => $chequeDataValue)
			{
				foreach($chequeDataValue as $chequeValueIndex => $chequeValueIndexValue)
				{
					$newArray[$chequeValueIndex][$chequeDataIndex] = $chequeValueIndexValue;
				}
			}
		}

		$cheque_info		= array();
		$tempCheque 		= array();

		if(!empty($newArray))
		{
			$linecount		= 1;

			foreach($newArray as $newArrayIndex => $newArrayValue)
			{
				$chequeaccount			= (!empty($newArrayValue['chequeaccount'])) ? $newArrayValue['chequeaccount'] : "";
				$chequenumber			= (!empty($newArrayValue['chequenumber'])) ? $newArrayValue['chequenumber'] : "";
				$chequedate				= (!empty($newArrayValue['chequedate'])) ? $newArrayValue['chequedate'] : "";
				$chequeamount			= (!empty($newArrayValue['chequeamount'])) ? $newArrayValue['chequeamount'] : "";
				$chequeconvertedamount	= (!empty($newArrayValue['chequeconvertedamount'])) ? $newArrayValue['chequeconvertedamount'] : $chequeamount;
				$chequedate				= $this->date->dateDbFormat($chequedate);
				
				if(!empty($chequedate) && !empty($chequeaccount) && !empty($chequenumber) && !empty($chequeamount))
				{
					$cheque_header['voucherno']				= $voucherno;
					$cheque_header['transtype']				= "DV";
					$cheque_header['linenum']				= $linecount;
					$cheque_header['chequeaccount']			= $chequeaccount;
					$cheque_header['chequenumber']			= $chequenumber;
					$cheque_header['chequedate']			= $chequedate;
					$cheque_header['chequeamount']			= $chequeamount;
					$cheque_header['chequeconvertedamount']	= $chequeconvertedamount;
					$cheque_header['stat']					= 'uncleared';
				
					$linecount++;
					
					$tempCheque[] = $cheque_header;
				}
			}
		}

		/**INSERT TO CHEQUES TABLE**/
		if(strtolower($paymenttype) == 'cheque')
		{
			$isDetailExist	= $this->getValue($chequeTable, array("COUNT(*) as count"),"voucherno = '$voucherno'");

			if($isDetailExist[0]->count == 0)
			{
				$insertResult =  $this->db->setTable($chequeTable)
									->setValues($tempCheque)
									// ->buildInsert();		
									->runInsert();
				
				if(!$insertResult)
				{
					$errmsg[] = "<li>Saving in Cheque Details.</li>";
				}
			}
			else
			{
				// Delete data if existing
				$this->db->setTable($chequeTable)
					->setWhere("voucherno = '$voucherno'");
					
				$insertResult = $this->db->runDelete();

				// Then insert data

				if($insertResult && !empty($tempCheque))
				{
					$insertResult =  $this->db->setTable($chequeTable)
										->setValues($tempCheque)
										// ->buildInsert();		
										->runInsert();
					
					if(!$insertResult)
					{
						$errmsg[] = "<li>Saving in Cheque Details.</li>";
					}
				}	
			}

			// Delete
			// $this->db->setTable($chequeTable)
			// 		->setWhere("voucherno = '$voucherno'");
					
			// $insertResult = $this->db->runDelete();
			
			// // Insert
			// if($insertResult && !empty($tempCheque))
			// {
			// 	$insertResult =  $this->db->setTable($chequeTable)
			// 						->setValues($tempCheque)
			// 						// ->buildInsert();		
			// 						->runInsert();
				
			// 	if(!$insertResult)
			// 	{
			// 		$errmsg[] = "<li>Saving in Cheque Details.</li>";
			// 	}
			// }	
		}

		/**INSERT DETAILS**/
		$linenum			= 1;
		$tempArr 			= array();
	
		foreach($tempArray as $tempArrayIndex => $tempArrayValue)
		{
			$data_insert["voucherno"]         = $voucherno;
			$data_insert['slcode']			  = '-';
			$data_insert['linenum']	          = $linenum;
			$data_insert['transtype']         = 'DV';
			$data_insert['accountcode']		  = $tempArrayValue['accountcode'];
			$data_insert['debit']			  = $tempArrayValue['debit'];
			$data_insert['credit']			  = $tempArrayValue['credit'];
			$data_insert['currencycode']	  = 'PHP';
			$data_insert['exchangerate']	  = $exchangerate;
			$data_insert['converteddebit']	  = $tempArrayValue['debit'];
			$data_insert['convertedcredit']	  = $tempArrayValue['credit'];
			$data_insert['taxcode']			  = '';
			$data_insert['taxacctflg']		  = '';
			$data_insert['taxline']			  = '';
			$data_insert['vatflg']			  = '';
			$data_insert['detailparticulars'] = $tempArrayValue['detailparticulars'];
			$data_insert['stat']		      = $status;
			$data_insert['checkstat']		  = 'uncleared';
			$data_insert['postingdate']		  = $this->datetime;
			$data_insert['postedby']		  = USERNAME;

			
			$linenum++;

			if($tempArrayValue['debit'] == 0 && $tempArrayValue['credit'] == 0)
			{
				
			}
			else
			{
				// For PV Details
				$tempArr[] 						= $data_insert;
			}
		}

		/**INSERT IR DETAILS**/
		$isDetailExist	= $this->getValue($detailInvTable, array("COUNT(*) as count"),"voucherno = '$voucherno'");

		if($isDetailExist[0]->count == 0)
		{
			$this->db->setTable($detailInvTable)
				->setValues($tempArr);

			$insertResult = $this->db->runInsert();

			if(!$insertResult)
				$errmsg[] 		= "<li>Saving in Disbursement Voucher Details.</li>";
		}
		else
		{
			// Delete data if existing
			$this->db->setTable($detailInvTable)
				->setWhere("voucherno = '$voucherno' ");
			$this->db->runDelete();

			// Then insert data
			$this->db->setTable($detailInvTable)
				->setValues($tempArr);
			$insertResult = $this->db->runInsert();

			if(!$insertResult)
				$errmsg[] 		= "<li>Saving in Disbursement Voucher Details.</li>";
		}

		return $errmsg;
	}

	public function updateData($data, $table, $cond)
	{
		$data_insert["voucherno"] = $data["voucherno"];
		$data_insert["stat"]      = $data["stat"];

		$result = $this->db->setTable($table)
				->setValues($data_insert)
				->setWhere($cond)
				->runUpdate();
		
		return $result;
	}

	public function saveDetails($table, $data, $cmp, $form = "")
	{
		$datetime                  = date("Y-m-d H:i:s");
		$result 				   = "";

		if($form == "vendordetail")
		{
			$data_insert["stat"]       = "active";
			$data_insert["terms"]      = $data["h_terms"];
			$data_insert["tinno"]      = $data["h_tinno"];
			$data_insert["address1"]   = $data["h_address1"];
		}

		if($data["h_querytype"] == "insert")
		{

			$this->db->setTable($table)
				->setValues($data_insert);

			$result = $this->db->runInsert();
		
		}
		else if($data["h_querytype"] == 'update')
		{
			$partnercode = $data["h_condition"];
			$cond 		 = "partnercode = '$partnercode'";
			
			$this->db->setTable($table)
					->setValues($data_insert)
					->setWhere($cond);
					
			$result = $this->db->runUpdate();
		}

		return $result;
	}

	public function retrieveListing_($data)
	{
		$daterangefilter = isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
		$vendfilter      = isset($data['vendfilter']) ? htmlentities($data['vendfilter']) : ""; 
		$addCond         = isset($data['addCond']) ? htmlentities($data['addCond']) : "";
		$searchkey 		 = isset($data['search']) ? htmlentities($data['search']) : "";
		$sort 		 	 = isset($data['sort']) ? htmlentities($data['sort']) : "pv.transactiondate";

		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? $this->date->dateDbFormat($datefilterArr[0]) : ""; //date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? $this->date->dateDbFormat($datefilterArr[1]) : ""; //date("Y-m-d",strtotime($datefilterArr[1])) : "";

		// OR p.first_name LIKE '%$searchkey%' OR p.last_name LIKE '%$searchkey%')

		$add_query 	= (!empty($searchkey)) ? "AND (pv.voucherno LIKE '%$searchkey%' OR p.partnername LIKE '%$searchkey%') " : "";
		$add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND pv.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
		$add_query .= (!empty($vendfilter) && $vendfilter != '') ? "AND p.partnercode = '$vendfilter' " : "";

		$main_fields = array("pv.voucherno", "pv.transactiondate", "pv.netamount as amount", "pv.source", "pv.stat", "CONCAT( first_name, ' ', last_name )", "pv.checkstat", "p.partnername AS vendor");
		$main_join   = "partners p ON p.partnercode = pv.vendor";
		$main_table  = "paymentvoucher pv";
		$main_cond   = "pv.stat = 'posted' $add_query AND pv.source = 'DV'";

		/*
			SELECT pv.voucherno, pv.transactiondate, pv.netamount as amount, pv.source, pv.stat, CONCAT( first_name, ' ', last_name ), pv.checkstat, p.partnername AS vendor 
			FROM paymentvoucher pv 
			LEFT JOIN partners p ON p.partnercode = pv.vendor 
			WHERE pv.stat = 'posted' AND (pv.voucherno LIKE '%fold%' OR p.partnername LIKE '%fold%') AND pv.transactiondate BETWEEN '2017-06-01' AND '2017-06-30'  AND  pv.companycode = 'CID' AND pv.source = 'DV'
		*/

		$query 		 = $this->db->setTable($main_table)
								->setFields($main_fields)
								->leftJoin($main_join)
								->setWhere($main_cond)
								->setOrderBy($sort)
								// ->buildSelect();
								->runPagination();
		// var_dump($query);
		return $query;
		
	}
	
	public function retrieveExistingDV($voucherno)
	{	
		$retrieved_data =	array();
		
		$header_fields = "voucherno, transactiondate, vendor, referenceno, amount, exchangerate, convertedamount, proformacode, particulars, paymenttype";

		$condition 		=	" voucherno = '$voucherno' ";
		
		$retrieved_data['header'] = 	$this->db->setTable('paymentvoucher')
														->setFields($header_fields)
														->setWhere($condition)
														->setLimit('1')
														->runSelect()
														->getRow();
									
		// Retrieve Vendor Details
		$vendor_code 			  = 	$retrieved_data['header']->vendor;
		$retrieved_data['vendor'] =		$this->retrieveVendorDetails($vendor_code);

		// Retrieve Details
		$detail_fields 		= "pv.costcentercode, pv.accountcode, pv.detailparticulars, pv.debit, pv.credit, CONCAT(segment5, ' - ', accountname) accountname";
		$condition 			= " voucherno = '$voucherno' ";
		$detailJoin   		= "chartaccount as chart ON chart.id = pv.accountcode AND chart.companycode = pv.companycode";
		
		$retrieved_data['details'] = 	$this->db->setTable('pv_details AS pv')
											->setFields($detail_fields)
											->leftJoin($detailJoin)
											->setWhere($condition)
											->runSelect()
											->getResult();
		
		// Retrieve Payments
		$applicationFields = "app.voucherno,main.transactiondate,detail.accountcode,CONCAT(segment5, ' - ', accountname) accountname,main.wtaxcode,ftax.shortname,main.paymenttype,main.referenceno,app.amount,app.stat,main.checkdate,main.atcCode,main.particulars,detail.checkstat,app.discount,app.exchangerate,app.convertedamount";

		$appJoin_pv  = "paymentvoucher as main ON main.voucherno = app.voucherno AND main.companycode = app.companycode";
		$appJoin_pvd = "pv_details as detail ON detail.voucherno = app.voucherno AND detail.companycode = app.companycode";
		$appJoin_ca  = "chartaccount as chart ON chart.id = detail.accountcode AND chart.companycode = detail.companycode";
		$appJoin_fin = "fintaxcode as ftax ON ftax.fstaxcode = main.wtaxcode AND ftax.companycode = main.companycode";

		$app_cond 	 = "app.voucherno = '$voucherno' AND detail.linenum = '1' AND app.stat != 'cancelled'";

		$applicationArray = $this->db->setTable('pv_application as app')
									->setFields($applicationFields)
									->leftJoin($appJoin_pv)
									->leftJoin($appJoin_pvd)
									->leftJoin($appJoin_ca)
									->leftJoin($appJoin_fin)
									->setWhere($app_cond)
									// ->buildSelect();
									->runSelect()
									->getResult();
		
		// var_dump($applicationArray);
		
		$retrieved_data["payments"] = $applicationArray;

		// Received Cheques
		$chequeFields = 'voucherno, chequeaccount, chequenumber, chequedate, chequeamount, chequeconvertedamount';
		$cheque_cond  = "voucherno = '$voucherno'";

		$chequeArray  = $this->db->setTable('pv_cheques')
									->setFields($chequeFields)
									->setWhere($cheque_cond)
									->runSelect()
									->getResult();

		$rollArray	  = array();
	
		if(!empty($chequeArray))
		{
			$checkArray	= array();
			
			$chequeListArray	= array();

			for($c = 0; $c < count($chequeArray); $c++)
			{
				$pvno					= $chequeArray[$c]->voucherno;
				$chequeaccount			= $chequeArray[$c]->chequeaccount;
				$chequenumber			= $chequeArray[$c]->chequenumber; 
				$chequedate				= $chequeArray[$c]->chequedate; 
				$chequedate				= $this->date->dateFormat($chequedate);
				$chequeamount			= $chequeArray[$c]->chequeamount;
				$chequeconvertedamount	= $chequeArray[$c]->chequeconvertedamount;

				$rollArray1['chequeaccount']		= $chequeaccount;
				$rollArray1['chequenumber']			= $chequenumber;
				$rollArray1['chequedate']			= $chequedate;
				$rollArray1['chequeamount']			= $chequeamount;
				$rollArray1['chequeconvertedamount'] = $chequeconvertedamount;
				
				$rollArray[$pvno][]				= $rollArray1;
			}
		}
		
		$retrieved_data["rollArray"] = $rollArray;


		// Received Cheques for View
		$chequeFieldsv = 'pvc.voucherno, pvc.chequeaccount, CONCAT(segment5, ' - ', accountname) accountname, pvc.chequenumber, pvc.chequedate, pvc.chequeamount, pvc.chequeconvertedamount';
		$cheque_condv  = "pvc.voucherno = '$voucherno'";
		$cheque_joinv  = "chartaccount chart ON chart.id = pvc.chequeaccount AND chart.companycode = pvc.companycode";
		
		$chequeArrayv  = $this->db->setTable('pv_cheques AS pvc')
									->setFields($chequeFieldsv)
									->leftJoin($cheque_joinv)
									->setWhere($cheque_condv)
									->runSelect()
									->getResult();
		
		$rollArrayv	  = array();
	
		if(!empty($chequeArrayv))
		{
			for($c = 0; $c < count($chequeArrayv); $c++)
			{
				$pvno					= $chequeArrayv[$c]->voucherno;
				$accountname			= $chequeArrayv[$c]->accountname;
				$chequeaccount			= $chequeArrayv[$c]->chequeaccount;
				$chequenumber			= $chequeArrayv[$c]->chequenumber; 
				$chequedate				= $chequeArrayv[$c]->chequedate; 
				$chequedate				= $this->date->dateFormat($chequedate);
				$chequeamount			= $chequeArrayv[$c]->chequeamount;
				$chequeconvertedamount	= $chequeArrayv[$c]->chequeconvertedamount;

				$rollArray2['accountname']			= $accountname;
				$rollArray2['chequeaccount']		= $chequeaccount;
				$rollArray2['chequenumber']			= $chequenumber;
				$rollArray2['chequedate']			= $chequedate;
				$rollArray2['chequeamount']			= $chequeamount;
				$rollArray2['chequeconvertedamount'] = $chequeconvertedamount;
				
				$rollArrayv[$pvno][]				= $rollArray2;
			}
		}
		
		$retrieved_data["rollArrayv"] = $rollArrayv;
		

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
		
		// var_dump($this->db->buildSelect());

		return $result;
	}

	public function delete_temp_transactions($voucherno, $table, $detailTable, $pvapp, $pvcheques)
	{
		$result = $this->db->setTable($table)
				->setWhere(" voucherno = '$voucherno' AND stat = 'temporary' ")
				// ->buildDelete();
				->runDelete();
			
		if( $result )
		{
			// Delete PV Details
			$result = $this->db->setTable($detailTable)
							->setWhere(" voucherno = '$voucherno' AND stat = 'temporary' ")
							->runDelete();

			// Delete PV App
			$result = $this->db->setTable($pvapp)
						->setWhere(" voucherno = '$voucherno' AND stat = 'temporary' ")
						->runDelete();

			// Delete PV Cheques
			$result = $this->db->setTable($pvcheques)
						->setWhere(" voucherno = '$voucherno' AND stat = 'temporary' ")
						->runDelete();

		}

		return $result;
	}

	public function retrievePaymentDetails($voucherno)
	{
		$paymentFields		= array("'' sourceno", "main.checknumber","main.referenceno","main.particulars as remarks","app.amount as amount","main.paymenttype");
		$paymentJoin_ 		= "paymentvoucher as main ON main.voucherno = app.voucherno AND main.companycode = app.companycode"; 
		$paymentCondition 	= "main.voucherno = '$voucherno'";
		$paymentOrderBy 	= "app.linenum";

		$result = $this->db->setTable("pv_application as app")
					->setFields($paymentFields)
					->leftJoin($paymentJoin_)
					->setWhere($paymentCondition)
					->setOrderBy($paymentOrderBy)
					->runSelect()
					->getResult();

		// var_dump($this->db->buildSelect());
		
		return $result;
	}

	public function deletePayments($voucher)
	{
		$update_info 	= array();
		$errmsg 	 	= array();

		$appTable		= "pv_application";
		$detailTable	= "pv_details";
		$mainTable		= "paymentvoucher";
		
		$update_info			= array();
		$update_info['stat']	= 'cancelled';
		
		// Update pv_application
		$result = $this->db->setTable($appTable)
				->setValues($update_info)
				->setWhere("voucherno = '$voucher'")
				->runUpdate();
		
		if(!$result)
			$errmsg[] = "The system has encountered an error in updating PV Application [$voucher]. Please contact admin to fix this issue.";
		else
			$this->logs->saveActivity("Update PV Application [$voucher]");

		// Update pv_details
		$result = $this->db->setTable($detailTable)
				->setValues($update_info)
				->setWhere("voucherno = '$voucher'")
				->runUpdate();
		
		if(!$result)
			$errmsg[] = "The system has encountered an error in updating Payment Voucher Details [$voucher]. Please contact admin to fix this issue.";
		else
			$this->logs->saveActivity("Update Payment Voucher Details [$voucher]");
		
		// Update paymentvoucher
		$result = $this->db->setTable($mainTable)
				->setValues($update_info)
				->setWhere("voucherno = '$voucher'")
				->runUpdate();	
		
		if(!$result)
			$errmsg[] = "The system has encountered an error in updating Payment Voucher [$voucher]. Please contact admin to fix this issue.";
		else
			$this->logs->saveActivity("Update Payment Voucher [$voucher]");

		return $errmsg;
	}

	public function deleteData($data)
	{
		$table = $data["table"];
		$cond  = stripslashes($data["condition"]);
		
		$this->db->setTable($table)
				 ->setWhere($cond);
		
		// echo $this->db->buildDelete();

		$result = $this->db->runDelete();

		return $result;
	}

	public function runUpdate($data, $table, $cond)
	{
		$result = $this->db->setTable($table)
				->setValues($data)
				->setWhere($cond)
				// ->buildUpdate();
				->runUpdate();
		
		// var_dump($result);

		return $result;
	}

}
?>