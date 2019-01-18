<?php
class purchase_order extends wc_model
{
	public function retrieveVendorList()
	{
		$result = $this->db->setTable('partners')
		->setFields("partnercode ind, CONCAT(partnercode,' - ',partnername) val")
		->setWhere("partnercode != '' AND partnertype = 'supplier' AND stat = 'active'")
		->setOrderBy("val")
		->runSelect()
		->getResult();

		return $result;
	}

	public function getReference($code)
	{
		$result = $this->db->setTable('wc_reference')
		->setFields("value")
		->setWhere("code = '$code'")
		->setLimit('1')
		->runSelect(false)
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

	public function getWTaxCodeList() {
		$result = $this->db->setTable('fintaxcode')
		->setFields('fstaxcode ind, shortname val')
		->setWhere("taxtype = 'WTX'")
		->setOrderBy('taxrate')
		->runSelect()
		->getResult();

		return $result;
	}

	public function retrieveVendorDetails($vendor_code)
	{
		$fields = "address1, tinno, terms, email, partnername as companyname, CONCAT( first_name, ' ', last_name ) AS name";
		$cond 	= "partnercode = '$vendor_code' AND partnertype = 'supplier'";

		$result = $this->db->setTable('partners')
		->setFields($fields)
		->setWhere($cond)
		->setLimit('1')
		->runSelect()
		->getRow();

					//echo $this->db->getQuery();
		return $result;
	}
	public function retrieveItemDetails($itemcode)
	{
		$fields = "i.itemname as itemname, i.itemdesc as itemdesc, i.weight as weight , i.uom_base, COALESCE(p.price_average,0) as price, u.uomcode as uomcode, u.uomdesc as uomdesc";
		$cond 	= "i.itemcode = '$itemcode'";
			// $order 	= "p.linenum DESC";

		$result = $this->db->setTable('items i')
		->leftJoin('uom u ON u.uomcode = i.uom_purchasing AND u.companycode = i.companycode')
		->leftJoin('price_average p ON p.itemcode = i.itemcode AND p.companycode = i.companycode')
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

	public function updateActual($fields, $voucherno) {
		$result = $this->db->setTable('actual_budget')
		->setValues($fields)
		->setWhere("voucherno = '$voucherno'")
		->runUpdate(false);
		
		return $result;
	}

	public function retrieveListing($data)
	{
		$fields 			= array('po.voucherno', 'p.partnername as vendor', 'po.referenceno', 'po.request_no', 'po.transactiondate','po.stat','po.netamount','(po.netamount - IFNULL(pr.received_amount,0)) as balance');

		$daterangefilter 	= isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
		$vendfilter      	= isset($data['vendor']) ? htmlentities($data['vendor']) : ""; 
		$searchkey 		 	= isset($data['search']) ? htmlentities($data['search']) : "";
		$filter 		 	= isset($data['filter']) ? htmlentities($data['filter']) : "";
		$sort 		 	 	= isset($data['sort']) ? htmlentities($data['sort']) : " po.voucherno DESC ";

		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";

		$add_query   = (!empty($searchkey)) ? "AND (po.voucherno LIKE '%$searchkey%' OR p.first_name LIKE '%$searchkey%' OR p.last_name LIKE '%$searchkey%'  OR po.referenceno LIKE '%$searchkey%'  OR p.partnername LIKE '%$searchkey%' OR po.transactiondate LIKE '%$searchkey%') " : "";
		$add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND po.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
		$add_query .= (!empty($vendfilter) && $vendfilter != 'none') ? "AND p.partnercode = '$vendfilter' " : "";

		if( !empty($filter) && $filter == 'open')
		{
			$add_query 	.=	" AND po.stat = 'open' ";
		}
		else if( !empty($filter) && $filter == 'posted' )
		{
			$add_query 	.= 	" AND po.stat = 'posted' ";
		}
		else if( !empty($filter) && $filter == 'partial' )
		{
			$add_query 	.= 	" AND po.stat = 'partial' ";
		}
		else if( !empty($filter) && $filter == 'cancelled' )
		{
			$add_query 	.= 	" AND po.stat = 'cancelled' ";
		}
		else if( $filter == 'all' )
		{
			$add_query 	.= 	" ";
		}

		$receipt 	=	$this->db->setTable('purchasereceipt pr')
		->setFields(array('pr.source_no source_no','pr.vendor vendor','pr.stat stat','SUM((pr.netamount) + (pr.discountamount) + (pr.wtaxamount)) received_amount'))
		->setWhere("(pr.stat NOT IN ('temporary', 'Cancelled' ) OR pr.stat IS NULL) ")
		->setGroupBy('pr.source_no')
		->buildSelect();

		return $this->db->setTable('purchaseorder po')
		->setFields($fields)
		->leftJoin('partners p ON p.partnercode = po.vendor AND p.partnertype = "supplier" AND p.companycode = po.companycode')
		->leftJoin("($receipt) pr ON pr.source_no = po.voucherno AND pr.vendor = p.partnercode")
		->setWhere(" po.stat NOT IN ( 'temporary' )   $add_query")
		->setOrderBy($sort)
		->setGroupBy('po.voucherno')
		->runPagination();		
	}

	public function retrieveExistingPO($voucherno)
	{	 
		$retrieved_data =	array();
		
		$header_fields 	= 	"po.voucherno, po.transactiondate, po.referenceno, po.vendor, p.partnername as companyname,CONCAT(p.first_name, ' ',p.last_name) as vendor_name, po.amount, po.discounttype, po.discountamount, po.netamount, po.taxamount, po.wtaxamount, po.wtaxrate, po.wtaxcode, po.atcCode, po.department, po.stat";

		$condition 		=	" po.voucherno = '$voucherno' ";
		
		$retrieved_data['header'] 	= 	$this->db->setTable('purchaseorder po')
		->leftJoin(' partners p ON p.partnercode = po.vendor AND p.partnertype = "supplier" AND p.companycode = po.companycode')
		->setFields($header_fields)
		->setWhere($condition)
		->setLimit('1')
		->runSelect()
		->getRow();

		// Retrieve Vendor Details
		$vendor_code 			 	 = 	$retrieved_data['header']->vendor;
		$retrieved_data['vendor']    =	$this->retrievevendorDetails($vendor_code);

			// Retrieve Details
		$detail_fields 			= "pd.budgetcode, pd.itemcode, pd.detailparticular, pd.warehouse, w.description, pd.unitprice, pd.receiptqty,receiptuom, pd.taxcode, pd.taxrate, pd.amount";
		$condition 				= " pd.voucherno = '$voucherno' ";

		$retrieved_data['details'] = 	$this->db->setTable('purchaseorder_details pd')
		->leftJoin('warehouse w ON w.warehousecode = pd.warehouse ')
		->leftJoin("uom u ON u.uomcode = pd.receiptuom")
		->setFields($detail_fields)
		->setWhere($condition)
		->runSelect()
		->getResult();

			//echo $this->db->getQuery();
		return $retrieved_data;
	}

	public function retrieveExistingPQ($request_no)
	{	 
		$retrieved_data =	array();

		$header_fields 	= 	"voucherno, transactiondate, requestor, amount, discounttype, discountamount, netamount, taxamount, wtaxamount, wtaxrate, wtaxcode, department";

		$condition 		=	" voucherno = '$request_no' ";

		$retrieved_data['header'] 	= 	$this->db->setTable('purchaserequest')
		->setFields($header_fields)
		->setWhere($condition)
		->setLimit('1')
		->runSelect()
		->getRow();

			// Retrieve Vendor Details
			// $vendor_code 			 	 = 	$retrieved_data['header']->requestor;
			// $retrieved_data['vendor']  	=	$this->retrieveVendorDetails($vendor_code);

			// Retrieve Details
		$detail_fields 				= 	"itemcode, detailparticular, unitprice, receiptqty, receiptuom, taxcode, taxrate, amount";
		$condition 					= 	" voucherno = '$request_no' ";

		$retrieved_data['details'] 	= 	$this->db->setTable('purchaserequest_details')
		->setFields($detail_fields)
		->setWhere($condition)
		->runSelect()
		->getResult();
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

	public function getValue($table, $cols = array(), $cond, $orderby = "", $bool = "")
	{
		$result = $this->db->setTable($table)
		->setFields($cols)
		->setWhere($cond)
		->setOrderBy($orderby)
		->runSelect(false)
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

	public function getTaxCode($taxtype, $fields, $orderby = "")
	{
		$result = $this->db->setTable('fintaxcode')
		->setFields($fields)
		->setWhere("taxtype = '$taxtype'")
		->setOrderBy($orderby)
		->runSelect(false)
		->getResult();

		return $result;
	}

	public function processTransaction($data, $task, $voucher = "")
	{
			//var_dump($data);	
			//var_dump($data);
		$mainInvTable		= "purchaseorder";
		$detailInvTable		= "purchaseorder_details";
		
		$insertResult		= 0;
		$errmsg				= array();

			// Journal Data
		$voucherno			= (isset($data['h_voucher_no']) && (!empty($data['h_voucher_no']))) ? htmlentities(addslashes(trim($data['h_voucher_no']))) : "";
		
		$request_no			= (isset($data['h_request_no']) && (!empty($data['h_request_no']))) ? htmlentities(addslashes(trim($data['h_request_no']))) : "";

		$vendor				= (isset($data['vendor']) && (!empty($data['vendor']))) ? htmlentities(addslashes(trim($data['vendor']))) : "";

		$transactiondate	= (isset($data['transaction_date']) && (!empty($data['transaction_date']))) ? htmlentities(addslashes(trim($data['transaction_date']))) : "";

		$referenceno 		= (isset($data['referenceno']) && (!empty($data['referenceno']))) ? htmlentities(addslashes(trim($data['referenceno']))) : "";

		$department 		= (isset($data['department']) && (!empty($data['department']))) ? htmlentities(addslashes(trim($data['department']))) : "";

		$remarks			= (isset($data['remarks']) && (!empty($data['remarks']))) ? htmlentities(addslashes(trim($data['remarks']))) : "";

		$terms				= (isset($data['vendor_terms']) && (!empty($data['vendor_terms']))) ? htmlentities(addslashes(trim($data['vendor_terms']))) : 0;

		$vat 	 			= (isset($data['t_vat']) && (!empty($data['t_vat']))) ? htmlentities(addslashes(trim($data['t_vat']))) : "";

		$subtotal 			= (isset($data['t_subtotal']) && (!empty($data['t_subtotal']))) ? htmlentities(addslashes(trim($data['t_subtotal']))) : "";

		$totalamount 		= (isset($data['t_total']) && (!empty($data['t_total']))) ? htmlentities(addslashes(trim($data['t_total']))) : "";

		$discounttype 		= (isset($data['discounttype']) && (!empty($data['discounttype']))) ? htmlentities(addslashes(trim($data['discounttype']))) : "";

		$discount_amount 	= (isset($data['t_discount']) && (!empty($data['t_discount']))) ? htmlentities(addslashes(trim($data['t_discount']))) : "";

		$atc_code 			= (isset($data['s_atc_code']) && (!empty($data['s_atc_code']))) ? htmlentities(addslashes(trim($data['s_atc_code']))) : "";

		$wtax 			 	= (isset($data['t_wtax']) && (!empty($data['t_wtax']))) ? htmlentities(addslashes(trim($data['t_wtax']))) : "";

		$wtaxcode 			= (isset($data['t_wtaxcode']) && (!empty($data['t_wtaxcode']))) ? htmlentities(addslashes(trim($data['t_wtaxcode']))) : "";

		$wtaxrate 			= (isset($data['wtaxrate']) && (!empty($data['wtaxrate']))) ? htmlentities(addslashes(trim($data['wtaxrate']))) : "";

		$_final 			= (isset($data['save']) && (!empty($data['save']))) ? htmlentities(addslashes(trim($data['save']))) : "";	

		$status				= ( empty($_final) && $voucher == "" ) ? "temporary" : "open";

		/**TRIM COMMAS FROM AMOUNTS**/
		$subtotal			= str_replace(',','',$subtotal);
		$totalamount		= str_replace(',','',$totalamount);

		/**FORMAT DATES**/
		$transactiondate	= date("Y-m-d",strtotime($transactiondate));
		$period				= date("n",strtotime($transactiondate));
		$fiscalyear			= date("Y",strtotime($transactiondate));

		$post_header['voucherno'] 			=	$voucherno;
		$post_header['transactiondate'] 	=	$transactiondate;
		$post_header['referenceno'] 		= 	$referenceno;
		$post_header['request_no'] 			= 	$request_no;
		$post_header['vendor'] 				=	$vendor;
		$post_header['fiscalyear'] 			=	$fiscalyear;
		$post_header['period'] 				=	$period;
		$post_header['transtype'] 			=	"PO";
		$post_header['stat'] 		 		= 	$status;
		$post_header['remarks'] 			=	$remarks;
		$post_header['department'] 			=	$department;
		$post_header['atcCode'] 			=	$atc_code;
		$post_header['amount'] 				= 	$subtotal;
		$post_header['discounttype'] 		=	$discounttype;
		$post_header['discountamount'] 		=	$discount_amount;
		$post_header['netamount'] 			=	$totalamount;
		$post_header['taxamount'] 			=	$vat;
		$post_header['wtaxcode'] 			=	$wtaxcode;
		$post_header['wtaxamount'] 			=	$wtax;
		$post_header['wtaxrate'] 			=	$wtaxrate;

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
				$this->db->setTable($mainInvTable)
				->setValues($post_header);

				$insertResult = $this->db->runInsert();
			}
		}
		else if( $task == 'create' )
		{
			$this->db->setTable($mainInvTable)
			->setValues($post_header)
			->setWhere("voucherno = '$voucherno' ");
				// echo $this->db->getQuery();	
			$insertResult = $this->db->runUpdate();
		}
		else if( $task == 'edit' )
		{
			$cond 	=	"voucherno = '$voucher' ";

			$insertResult = $this->db->setTable($mainInvTable)
			->setValues($post_header)
			->setWhere($cond)
			->runUpdate();
		}

		/**INSERT DETAILS**/
		foreach($data as $postIndex => $postValue)
		{
			if($postIndex == 'budgetcode' || $postIndex == 'itemcode' || $postIndex=='detailparticulars' || $postIndex == 'warehouse' ||  
				$postIndex == 'quantity' || $postIndex == 'uom' || $postIndex == 'itemprice' || $postIndex=='taxcode' ||
				$postIndex=='taxrate' || $postIndex == 'taxamount' || $postIndex == 'amount' || 
				$postIndex == 'h_amount')
			{
				$a		= '';

				foreach($postValue as $postValueIndex => $postValueIndexValue)
				{
					if($postIndex == 'quantity' || $postIndex == 'amount' || $postIndex == 'h_amount' || $postIndex == 'taxamount' || $postIndex == 'itemprice')
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

			//var_dump($data);

		foreach($tempArray as $tempArrayIndex => $tempArrayValue)
		{
			$budgetcode 		=	$tempArrayValue['budgetcode'];
			$itemcode 			=	$tempArrayValue['itemcode'];
			$detailparticular 	=	$tempArrayValue['detailparticulars'];
			$warehouse 			=	$tempArrayValue['warehouse'];
			$quantity 			=	$tempArrayValue['quantity'];
			$uom 				=	$tempArrayValue['uom'];
			$price 				=	$tempArrayValue['itemprice'];
			$amount 			=	$tempArrayValue['amount'];
			$taxcode  			=	$tempArrayValue['taxcode'];
			$taxrate 			=	$tempArrayValue['taxrate'];
			$taxamount 	 		=	$tempArrayValue['taxamount'];

			$ret_conversion 	=	$this->getValue("items",array('uom_purchasing','purchasing_conv')," itemcode = '$itemcode' ");

			$convuom 		=	(!empty($ret_conversion[0]->uom_purchasing)) ? $ret_conversion[0]->uom_purchasing 	: 	'';
			$conversion 	=	(!empty($ret_conversion[0]->purchasing_conv)) ? $ret_conversion[0]->purchasing_conv : 	0;
			$convissueqty 	=	$quantity * $conversion;

			if( $tempArrayValue['itemcode'] != "" )
			{
				$data_insert["budgetcode"]         	= $budgetcode;
				$data_insert["voucherno"]         	= $voucherno;
				$data_insert['transtype']         	= 'PO';
				$data_insert['linenum']	        	= $linenum;
				$data_insert['itemcode']	  		= $itemcode;
				$data_insert['detailparticular']	= $detailparticular;
				$data_insert['warehouse']			= $warehouse;
				$data_insert['receiptuom']			= $uom;
				$data_insert['receiptqty']			= $quantity;
				$data_insert['unitprice']	  		= $price;
				$data_insert['amount']	  			= $amount;
				$data_insert['stat']	  			= $status;
				$data_insert['taxcode']			  	= $taxcode;
				$data_insert['taxrate']		  		= $taxrate;
				$data_insert['taxamount']			= $taxamount;
				$data_insert['convreceiptqty']	  	= $convissueqty;
				$data_insert['convuom']		  		= $convuom;
				$data_insert['conversion']			= $conversion;

				$linenum++;

				$tempArr[] 						= $data_insert;
			}
		}
		$warning = array();
		$checkamount = array();
		$error = array();
		$date_checker = array();
		$accountcode = '';
		/**INSERT IR DETAILS**/
		$isDetailExist	= $this->getValue($detailInvTable, array("COUNT(*) as count"),"voucherno = '$voucherno'");
		foreach($tempArr as $row) {
			if(!empty($row['budgetcode'])) {
				$budgetcode = $row['budgetcode'];
				$type = $this->db->setTable('budget')
				->setFields('budget_check')
				->setWhere("budget_code = '$budgetcode'")
				->runSelect()
				->getRow();

				$result = $this->db->setTable('budget')
				->setFields("TRUE")
				->setWhere("budget_code = '$budgetcode' AND effectivity_date <= '$transactiondate'")
				->runSelect()
				->getRow();

				$get_date = $this->db->setTable('budget')
				->setFields("effectivity_date")
				->setWhere("budget_code = '$budgetcode'")
				->runSelect()
				->getRow();

				if(!$result) {
					foreach($get_date as $key) {
						$date_checker[] = "The budget code " .$budgetcode . " is available on " . date('M d, Y', strtotime($key)). "<br>";
					}
				} else if(empty($date_checker)) {
					if($type->budget_check == 'Monitored') {

						$itemcode = $row['itemcode'];
						$check = $this->db->setTable('items i')
						->leftJoin('chartaccount as ca ON i.expense_account = ca.id')
						->setFields('i.expense_account, CONCAT(ca.segment5, " - ", ca.accountname) as accountname')
						->setWhere("itemcode = '$itemcode'")
						->runSelect()
						->getRow();

						if($check->expense_account == 0) {
							$getaccount = $this->db->setTable('itemclass ic')
							->setFields('ic.expense_account as expense_account, CONCAT(ca.segment5, " - ", ca.accountname) as accountname')
							->leftJoin('items as i ON i.classid = ic.id')
							->leftJoin('chartaccount as ca ON ca.id = ic.expense_account')
							->runSelect()
							->getRow();

							$expenseaccount = $getaccount->expense_account;
							$accountcode = $expenseaccount;
							$getbudgetaccount = $this->db->setTable('budget_details as bd')
							->setFields("IF(IFNULL(bs.amount, 0) = 0, 0, SUM(bs.amount)) + bd.amount - IF(IFNULL(ac.actual, 0) = 0, 0, SUM(ac.actual)) as amount, b.budget_check as budget_check, CONCAT(ca.segment5, ' - ', ca.accountname) as accountname")
							->leftJoin('budget as b ON bd.budget_code = b.budget_code')
							->leftJoin("budget_supplement as bs ON bs.budget_id = b.id AND bs.accountcode = '$accountcode'")
							->leftJoin('chartaccount as ca ON ca.id = bd.accountcode')
							->leftJoin("actual_budget as ac ON ac.accountcode = bd.accountcode AND ac.budget_code = '$budgetcode' AND ac.voucherno NOT LIKE '%TMP%'")
							->setWhere("bd.budget_code = '$budgetcode' AND bd.accountcode = '$accountcode'")
							->setGroupBy('bs.accountcode')
							->runSelect()
							->getRow();
							if(!$getbudgetaccount) {
								$warning[] = 'The account ' . $getaccount->accountname . ' is not in your budget code ' .$budgetcode. '.';
							} else {
								if($subtotal > $getbudgetaccount->amount) {
									$checkamount[] = "You were about to exceed your budget from " . $row['budgetcode']. " account " . $getaccount->accountname. ".</br>";
								}
							}
						} else {
							$expenseaccount = $check->expense_account;
							$accountcode = $expenseaccount;
							$getbudgetaccount = $this->db->setTable('budget_details as bd')
							->setFields("IF(IFNULL(bs.amount, 0) = 0, 0, SUM(bs.amount)) + bd.amount - IF(IFNULL(ac.actual, 0) = 0, 0, SUM(ac.actual)) as amount, b.budget_check as budget_check, CONCAT(ca.segment5, ' - ', ca.accountname) as accountname")
							->leftJoin('budget as b ON bd.budget_code = b.budget_code')
							->leftJoin("budget_supplement as bs ON bs.budget_id = b.id AND bs.accountcode = '$accountcode'")
							->leftJoin('chartaccount as ca ON ca.id = bd.accountcode')
							->leftJoin("actual_budget as ac ON ac.accountcode = bd.accountcode AND ac.budget_code = '$budgetcode' AND ac.voucherno NOT LIKE '%TMP%'")
							->setWhere("bd.budget_code = '$budgetcode' AND bd.accountcode = '$accountcode'")
							->setGroupBy('bs.accountcode')
							->runSelect()
							->getRow();
							if(!$getbudgetaccount) {
								$warning[] = 'The account ' . $check->accountname . ' is not in your budget code ' .$budgetcode. '.';
							} else {
								if($subtotal > $getbudgetaccount->amount) {
									$checkamount[] = "You were about to exceed your budget from " . $row['budgetcode']. " account " . $check->accountname. ".</br>";
								}
							}
						}
					} else {
						$itemcode = $row['itemcode'];
						$check = $this->db->setTable('items i')
						->leftJoin('chartaccount as ca ON i.expense_account = ca.id')
						->setFields('i.expense_account, CONCAT(ca.segment5, " - ", ca.accountname) as accountname')
						->setWhere("itemcode = '$itemcode'")
						->runSelect()
						->getRow();

						if($check->expense_account == 0) {
							$getaccount = $this->db->setTable('itemclass ic')
							->setFields('ic.expense_account as expense_account, CONCAT(ca.segment5, " - ", ca.accountname) as accountname')
							->leftJoin('items as i ON i.classid = ic.id')
							->leftJoin('chartaccount as ca ON ca.id = ic.expense_account')
							->runSelect()
							->getRow();

							$expenseaccount = $getaccount->expense_account;
							$accountcode = $expenseaccount;
							$getbudgetaccount = $this->db->setTable('budget_details as bd')
							->setFields("IF(IFNULL(bs.amount, 0) = 0, 0, SUM(bs.amount)) + bd.amount - IF(IFNULL(ac.actual, 0) = 0, 0, SUM(ac.actual)) as amount, b.budget_check as budget_check, CONCAT(ca.segment5, ' - ', ca.accountname) as accountname")
							->leftJoin('budget as b ON bd.budget_code = b.budget_code')
							->leftJoin("budget_supplement as bs ON bs.budget_id = b.id AND bs.accountcode = '$accountcode'")
							->leftJoin('chartaccount as ca ON ca.id = bd.accountcode')
							->leftJoin("actual_budget as ac ON ac.accountcode = bd.accountcode AND ac.budget_code = '$budgetcode' AND ac.voucherno NOT LIKE '%TMP%'")
							->setWhere("bd.budget_code = '$budgetcode' AND bd.accountcode = '$accountcode'")
							->setGroupBy('bs.accountcode')
							->runSelect()
							->getRow();
							if(!$getbudgetaccount) {
								$warning[] = 'The account ' . $getaccount->accountname . ' is not in your budget code ' .$budgetcode. '.';
							} else {
								if($subtotal > $getbudgetaccount->amount) {
									$error[] = "You are not allowed to exceed budget from " . $row['budgetcode']. " account " . $getaccount->accountname. ".</br>";
								}
							}
						} else {
							$expenseaccount = $check->expense_account;
							$accountcode = $expenseaccount;
							$getbudgetaccount = $this->db->setTable('budget_details as bd')
							->setFields("IF(IFNULL(bs.amount, 0) = 0, 0, SUM(bs.amount)) + bd.amount - IF(IFNULL(ac.actual, 0) = 0, 0, SUM(ac.actual)) as amount, b.budget_check as budget_check, CONCAT(ca.segment5, ' - ', ca.accountname) as accountname")
							->leftJoin('budget as b ON bd.budget_code = b.budget_code')
							->leftJoin("budget_supplement as bs ON bs.budget_id = b.id AND bs.accountcode = '$accountcode'")
							->leftJoin('chartaccount as ca ON ca.id = bd.accountcode')
							->leftJoin("actual_budget as ac ON ac.accountcode = bd.accountcode AND ac.budget_code = '$budgetcode' AND ac.voucherno NOT LIKE '%TMP%'")
							->setWhere("bd.budget_code = '$budgetcode' AND bd.accountcode = '$accountcode'")
							->setGroupBy('bs.accountcode')
							->runSelect()
							->getRow();
							if(!$getbudgetaccount) {
								$warning[] = 'The account ' . $check->accountname . ' is not in your budget code ' .$budgetcode. '.';
							} else {
								if($subtotal > $getbudgetaccount->amount) {
									$error[] = "You are not allowed to exceed budget from " . $row['budgetcode']. " account " . $check->accountname. ".</br>";
								}
							}
						}
					}
				}
			}	
		}

		if(empty($error) && empty($warning)) {
			if($isDetailExist[0]->count == 0 && $task == 'create' )
			{
				$this->db->setTable($detailInvTable)
				->setValues($tempArr);

				$insertResult = $this->db->runInsert();
			}
			else if( $isDetailExist[0]->count > 0 && ( $task == 'create' || $task == 'edit' ) )
			{
				$vn 	=	"";

				// Delete data if existing
				$this->db->setTable($detailInvTable)
				->setWhere("voucherno = '$voucherno' ");
				$this->db->runDelete();

				// Then insert data
				$this->db->setTable($detailInvTable)
				->setValues($tempArr);
				$insertResult = $this->db->runInsert();
			}
		}

		if(!$insertResult)
		{
			$errmsg[] 		= "The system has encountered an error in saving. Our team is currently checking on this.<br/>";
		}

		return array('errmsg' =>$errmsg, 'warning' => $warning, 'checkamount' => $checkamount, 'error' => $error, 'accountcode' => $accountcode, 'date_checker' => $date_checker);
	}

	public function saveActual($fields, $voucherno) {
		$this->db->setTable('actual_budget')
		->setWhere("voucherno = '$voucherno' ")
		->runDelete(false);

		$result = $this->db->setTable('actual_budget')
		->setValues($fields)
		->runInsert(false);
		return $result;
	}

	public function updateBudget($fields, $voucherno) {
		return $this->db->setTable('actual_budget')
		->setValues($fields)
		->setWhere("voucherno = '$voucherno' ")
		->runUpdate(false);
	}

	public function getBudgetCodes()
	{
		$result = $this->db->setTable('budget')
		->setFields("budget_code ind, budget_code val")
		->setWhere("status = 'approved'")
		->runSelect()
		->getResult();

		return $result;
	}

	public function updateData($data, $table, $cond)
	{
		$result = $this->db->setTable($table)
		->setValues($data)
		->setWhere($cond)
		->runUpdate();
					//echo $this->db->getQuery();
		return $result;
	}

	public function deleteData($table, $cond)
	{	
		$this->db->setTable($table)
		->setWhere($cond);

		$result = $this->db->runDelete();

		return $result;
	}

	public function insertVendor($data)
	{
		$data["stat"]     	   = "active";
		$data['partnertype']   = "supplier";

		$result = $this->db->setTable('partners')
		->setValues($data)
		->runInsert();
					//echo $this->db->getQuery();
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

	public function check_duplicate($code)
	{
		return $this->db->setTable('partners')
		->setFields('COUNT(partnercode) count')
		->setWhere(" partnercode = '$code' ")
		->runSelect()
		->getResult();
	}

	public function export_main($data) {

		$fields 			= array('po.voucherno', 'p.partnername as vendor', 'po.referenceno', 'po.request_no', 'po.transactiondate','po.stat','po.netamount');

		$daterangefilter 	= isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
		$vendfilter      	= isset($data['vendor']) ? htmlentities($data['vendor']) : ""; 
		$searchkey 		 	= isset($data['search']) ? htmlentities($data['search']) : "";
		$filter 		 	= isset($data['filter']) ? htmlentities($data['filter']) : "";
		$limit 		 	 	= isset($data['limit']) ? htmlentities($data['limit']) : "10";
		$sort 		 	 	= isset($data['sort']) ? htmlentities($data['sort']) : " po.voucherno DESC ";

		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";

		$add_query 	= (!empty($searchkey)) ? "AND (po.voucherno LIKE '%$searchkey%' OR p.first_name LIKE '%$searchkey%' OR p.last_name LIKE '%$searchkey%') " : "";
		$add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND po.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
		$add_query .= (!empty($vendfilter) && $vendfilter != 'none') ? "AND p.partnercode = '$vendfilter' " : "";

		if( !empty($filter) && $filter == 'open')
		{
			$add_query 	.=	" AND po.stat = 'open' ";
		}
		else if( !empty($filter) && $filter == 'posted' )
		{
			$add_query 	.= 	" AND po.stat = 'posted' ";
		}
		else if( !empty($filter) && $filter == 'cancelled' )
		{
			$add_query 	.= 	" AND po.stat = 'cancelled' ";
		}
		else
		{
			$add_query 	.= 	" ";
		}

		$fields 		= array('po.voucherno', 'po.vendor','p.partnername', 'po.referenceno', 'po.request_no', 'po.transactiondate','po.stat','po.netamount','pr.received_amount','(po.netamount - IFNULL(pr.received_amount,0)) as balance');

		$receipt 	=	$this->db->setTable('purchasereceipt pr')
		->setFields(array('pr.source_no source_no','pr.vendor vendor','pr.stat stat','SUM((pr.netamount) + (pr.discountamount) + (pr.wtaxamount)) received_amount'))
		->setWhere("(pr.stat NOT IN ('temporary', 'Cancelled' ) OR pr.stat IS NULL) ")
		->setGroupBy('pr.source_no')
		->buildSelect();

		return $this->db->setTable('purchaseorder po')
		->setFields($fields)
		->leftJoin('partners p ON p.partnercode = po.vendor AND p.companycode = po.companycode AND p.partnertype = "supplier" ')
		->leftJoin("($receipt) pr ON pr.source_no = po.voucherno AND pr.vendor = p.partnercode")
		->setWhere(" po.stat != 'temporary'  $add_query")
		->setOrderBy($sort)
		->setLimit($limit)
		->runSelect()
		->getResult();

		return $result;
	}

	public function getUnReceivedItems($voucherno) {
		$pr_inner = $this->db->setTable('purchasereceipt a')
		->innerJoin('purchasereceipt_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
		->setFields('a.companycode, linenum, source_no, SUM(receiptqty) received_qty')
		->setWhere("a.stat = 'Received'")
		->setGroupBy('source_no, itemcode, linenum')
		->buildSelect();

		$result	= $this->db->setTable('purchaseorder a')
		->innerJoin('purchaseorder_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
		->leftJoin("($pr_inner) pr ON pr.source_no = a.voucherno AND pr.companycode = a.companycode AND pr.linenum = b.linenum")
		->setFields('b.itemcode, detailparticular, b.warehouse, (b.receiptqty - pr.received_qty) balance_qty, taxcode, taxrate, b.receiptuom, unitprice')
		->setWhere("a.stat IN('open', 'partial', 'posted') AND a.voucherno = '$voucherno'")
		->setHaving('balance_qty > 0')
		->runSelect()
		->getResult();

		return $result;
	}

	public function getReceivedItems($voucherno) {
		$pr_inner = $this->db->setTable('purchasereceipt a')
		->innerJoin('purchasereceipt_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
		->setFields('a.companycode, linenum, source_no, SUM(receiptqty) received_qty')
		->setWhere("a.stat = 'Received'")
		->setGroupBy('source_no, itemcode, linenum')
		->buildSelect();

		$result	= $this->db->setTable('purchaseorder a')
		->innerJoin('purchaseorder_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
		->leftJoin("($pr_inner) pr ON pr.source_no = a.voucherno AND pr.companycode = a.companycode AND pr.linenum = b.linenum")
		->setFields('b.itemcode, detailparticular, b.warehouse, pr.received_qty, taxcode, taxrate, b.receiptuom, unitprice, a.wtaxrate')
		->setWhere("a.stat IN('open', 'partial', 'posted') AND a.voucherno = '$voucherno'")
		->runSelect()
		->getResult();

		return $result;
	}
}
?>