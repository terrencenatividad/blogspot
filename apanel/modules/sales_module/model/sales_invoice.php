<?php
class sales_invoice extends wc_model
{
	private $invoice = '';

	public function retrieveCustomerList($drlink=false)
	{
		if($drlink){
			$result = $this->db->setTable('partners as part')
					->setFields("part.partnercode ind, CONCAT(part.partnercode,' - ',part.partnername) val")
					->leftJoin("deliveryreceipt as dr ON dr.customer = part.partnercode and dr.companycode = part.companycode")
					->setWhere("part.partnercode != '' AND part.partnertype = 'customer' AND dr.stat = 'Delivered' ")
					->setGroupBy("part.partnercode")
					->setOrderBy("val")
					->runSelect()
					->getResult();
		}else{
			$result = $this->db->setTable('partners')
					->setFields("partnercode ind, CONCAT(partnercode,' - ',partnername) val")
					->setWhere("partnercode != '' AND partnertype = 'customer' AND stat = 'active'")
					->setGroupBy("partnercode")
					->setOrderBy("val")
					->runSelect()
					->getResult();
		}
		
		return $result;
	}

	public function retrieveCustomerDetails($customer_code)
	{
		$fields = "address1, tinno, terms, email, partnername AS name";
		$cond 	= " partnercode = '$customer_code' AND partnertype = 'customer'  ";

		$result = $this->db->setTable('partners')
							->setFields($fields)
							->setWhere($cond)
							->setLimit('1')
							->runSelect()
							->getRow();

		return $result;
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

	public function retrieveItemDetails($itemcode)
	{
		$fields = "itemname, itemdesc, weight";
		$cond 	= "itemcode = '$itemcode'";

		$result = $this->db->setTable('items')
							->setFields($fields)
							->setWhere($cond)
							->setLimit('1')
							->runSelect()
							->getRow();

		return $result;
	}
	
	public function getValue($table, $cols = array(), $cond, $orderby = "",$default = true)
	{
		$result = $this->db->setTable($table)
					->setFields($cols)
					->setWhere($cond)
					->setOrderBy($orderby)
					->runSelect($default)
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

	public function getTaxCode($taxtype,  $orderby = "")
	{
		$result = $this->db->setTable('fintaxcode')
					->setFields("fstaxcode ind, shortname val")
					->setWhere(" taxtype = '$taxtype' ")
					->setOrderBy($orderby)
					->runSelect(false)
					->getResult();

		return $result;
	}

	public function getDeliveryList($customer,$search) {
		$condition 		= " dr.stat = 'Delivered' AND dr.customer = '$customer' ";
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('dr.voucherno','dr.remarks'));
		}

		$result		= $this->db->setTable('deliveryreceipt dr')
								->setFields('dr.voucherno voucherno, dr.transactiondate transactiondate, dr.remarks notes')
								->setWhere($condition)
								->setGroupBy('dr.voucherno')
								->runPagination();

		return $result;
	}

	public function getDeliveries()
	{
		$result = $this->db->setTable('deliveryreceipt')
					->setFields("voucherno as ind, voucherno as val")
					->setWhere(" stat != 'Cancelled' ")
					->setOrderBy("voucherno")
					->runSelect()
					->getResult();

		return $result;
	}

	public function insertData($data)
	{
		$mainInvTable		= "salesinvoice";
		$detailInvTable		= "salesinvoice_details";
		
		$insertResult		= 0;
		$errmsg				= array();
		$tempvoucher 		= $this->getValue("salesinvoice", array("COUNT(*) as count"), " voucherno != '' ");
	
		/**TEMPORARY ID**/
		$generatedvoucher	= ($tempvoucher[0]->count > 0) ? 'SI_TMP_'.($tempvoucher[0]->count+1) : 'SI_TMP_1';
		$voucherno			= (isset($data['voucherno']) && (!empty($data['voucherno']))) ? htmlentities(addslashes(trim($data['voucherno']))) : $generatedvoucher;

		$isExist			= $this->getValue($mainInvTable, array("stat"), "voucherno = '$voucherno' AND stat = 'posted' ");
		
		$this->setInvoice($voucherno);
		
		$status				= (!empty($isExist)) ? "posted" : "temporary";
		
		$invoicedate		= (isset($data['transactiondate']) && (!empty($data['transactiondate']))) ? htmlentities(addslashes(trim($data['transactiondate']))) : "";
		$drno				= (isset($data['drno']) && (!empty($data['drno']))) ? htmlentities(addslashes(trim($data['drno']))) : "";
		$referenceno		= (isset($data['referenceno']) && (!empty($data['referenceno']))) ? htmlentities(addslashes(trim($data['referenceno']))) : "";
		$customer			= (isset($data['customer']) && (!empty($data['customer']))) ? htmlentities(addslashes(trim($data['customer']))) : "";
		$duedate			= (isset($data['duedate']) && (!empty($data['duedate']))) ? htmlentities(addslashes(trim($data['duedate']))) : "";
		$remarks			= (isset($data['remarks']) && (!empty($data['remarks']))) ? htmlentities(addslashes(trim($data['remarks']))) : "";

		$totalamount		= (isset($data['total']) && (!empty($data['total']))) ? htmlentities(addslashes(trim($data['total']))) : 0;
		$vatable_sales		= (isset($data['vatable_sales']) && (!empty($data['vatable_sales']))) ? htmlentities(addslashes(trim($data['vatable_sales']))) : 0;
		$vatexempt_sales	= (isset($data['vatexempt_sales']) && (!empty($data['vatexempt_sales']))) ? htmlentities(addslashes(trim($data['vatexempt_sales']))) : 0;
		$total_sales		= (isset($data['total_sales']) && (!empty($data['total_sales']))) ? htmlentities(addslashes(trim($data['total_sales']))) : 0;
		$discounttype		= (isset($data['discounttype']) && (!empty($data['discounttype']))) ? htmlentities(addslashes(trim($data['discounttype']))) : "amt";
		$discountamount		= (isset($data['discountamount']) && (!empty($data['discountamount']))) ? htmlentities(addslashes(trim($data['discountamount']))) : 0;
		$total_tax			= (isset($data['total_tax']) && (!empty($data['total_tax']))) ? htmlentities(addslashes(trim($data['total_tax']))) : 0;
		$exchangerate		= (isset($data['exchangerate']) && (!empty($data['exchangerate']))) ? htmlentities(addslashes(trim($data['exchangerate']))) : "1.00";
		$warehouse 			= '';

		if(!empty($drno))
		{
			$warehouseObj 	= $this->getValue("deliveryreceipt", array("warehouse"), " voucherno = '$drno' ");
			$warehouse 		= $warehouseObj[0]->warehouse;
		}
		
		/**TRIM COMMAS FROM AMOUNTS**/
		$totalamount		= str_replace(',','',$totalamount);
		$vatable_sales		= str_replace(',','',$vatable_sales);
		$vatexempt_sales	= str_replace(',','',$vatexempt_sales);
		$total_sales		= str_replace(',','',$total_sales);
		$discountamount		= str_replace(',','',$discountamount);
		$total_tax			= str_replace(',','',$total_tax);
		$exchangerate		= str_replace(',','',$exchangerate);

		/**FORMAT DATES**/
		$invoicedate		= date("Y-m-d",strtotime($invoicedate));
		$duedate			= date("Y-m-d",strtotime($duedate));
		$period				= date("n",strtotime($invoicedate));
		$fiscalyear			= date("Y",strtotime($invoicedate));

		$post_header['voucherno'] 			= $voucherno;
		$post_header['warehouse'] 			= $warehouse;
		$post_header['transactiondate'] 	= $invoicedate;
		$post_header['drno'] 				= $drno;
		$post_header['referenceno'] 		= $referenceno;
		$post_header['duedate'] 			= $duedate;
		$post_header['transtype'] 			= "SI";
		$post_header['customer'] 			= $customer;
		$post_header['fiscalyear'] 			= $fiscalyear;
		$post_header['period'] 				= $period;
		$post_header['sitecode'] 			= '';
		$post_header['remarks'] 			= $remarks;
		$post_header['stat'] 				= $status;
		$post_header['amount'] 				= $totalamount;
		$post_header['discounttype'] 		= $discounttype;
		$post_header['discountamount'] 		= $discountamount;
		$post_header['netamount'] 			= $total_sales;
		$post_header['taxamount'] 			= $total_tax;
		$post_header['vat_sales'] 			= $vatable_sales;
		$post_header['vat_exempt'] 			= $vatexempt_sales;
		$post_header['vat_zerorated'] 		= 0;
		$post_header['exchangerate'] 		= $exchangerate;
		
		/**INSERT HEADER**/
		if($status=='temporary')
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
		else
		{
			$this->db->setTable($mainInvTable)
				->setValues($post_header)
				->setWhere(" voucherno = '$voucherno' ");
			
			$insertResult = $this->db->runUpdate();
			
		}

		/**INSERT DETAILS**/
		foreach($data as $postIndex => $postValue)
		{
			if($postIndex == 'itemcode' ||  $postIndex == 'h_itemcode' ||  $postIndex=='detailparticulars' || $postIndex=='quantity' || $postIndex=='itemprice' || $postIndex=='discount'|| $postIndex=='h_discount'|| $postIndex=='h_discountrate'|| $postIndex=='taxrate' || $postIndex=='taxamount' || $postIndex=='amount' || $postIndex=='taxcode' || $postIndex=='h_taxcode' || $postIndex=='itemdiscount' || $postIndex=='discountedamount')
			{
				$a		= '';
				
				foreach($postValue as $postValueIndex => $postValueIndexValue)
				{
					if($postIndex == 'quantity' || $postIndex == 'itemprice' || $postIndex=='discount' || $postIndex=='h_discount'|| $postIndex=='h_discountrate' || $postIndex == 'taxrate' || $postIndex == 'amount' || $postIndex == 'taxamount' || $postIndex=='itemdiscount' || $postIndex=='discountedamount')
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
			
			$detail_row = $this->db->setTable('deliveryreceipt_details')
							->setFields("issueuom, convissueqty, convuom, warehouse, conversion")
							->setWhere(" voucherno = '$drno' AND linenum = '$linenum' ")
							->runSelect()
							->getRow();
			
			$dr_issueuom 		= (!empty($detail_row->issueuom)) ? $detail_row->issueuom : '';
			$dr_convissueqty	= (!empty($detail_row->convissueqty)) ? $detail_row->convissueqty : 0;
			$dr_convuom 		= (!empty($detail_row->convuom)) ? $detail_row->convuom : '';
			$dr_warehouse 		= (!empty($detail_row->warehouse)) ? $detail_row->warehouse : '';
			$dr_conversion 		= (!empty($detail_row->conversion)) ? $detail_row->conversion : '1.00';

			$data_insert["voucherno"]       = $voucherno;
			$data_insert['transtype']       = 'SI';
			$data_insert['linenum']	        = $linenum;
			$data_insert['itemcode']        = $tempArrayValue['h_itemcode'];
			$data_insert['detailparticular']= $tempArrayValue['detailparticulars'];
			
			$data_insert['issueqty']  		= $tempArrayValue['quantity'];
			$data_insert['issueuom']  		= $dr_issueuom;
			$data_insert['convissueqty']  	= $dr_convissueqty;
			$data_insert['convuom']  		= $dr_convuom;
			$data_insert['conversion']  	= $dr_conversion;
			$data_insert['unitprice']  		= $tempArrayValue['itemprice'];
			$data_insert['amount']  		= $tempArrayValue['amount'];
			$data_insert['warehouse']  		= $dr_warehouse;
			//$data_insert['discount']    	= $tempArrayValue['discount'];
			$data_insert['itemdiscount']    = $tempArrayValue['h_discount'];
			$data_insert['discounttype']    = $discounttype;
			$data_insert['discountedamount']= $tempArrayValue['discountedamount'];
			$data_insert['discountrate'] 	= ($discounttype == 'perc') ? $tempArrayValue['h_discountrate'] : 0;

			// if(isset($tempArrayValue['h_taxcode']) && $tempArrayValue['h_taxcode'] != 'none'){
			// 	$taxcode 					= $tempArrayValue['h_taxcode'];
			// 	$data_insert['taxcode']    	= $taxcode;
			// 	$tax 						= $this->getValue("fintaxcode", array("taxrate"), " fstaxcode = '$taxcode' ");
			// 	$taxrate 					= ($tax) ? $tax[0]->taxrate : 0;
			// 	//$taxamount 					= $data_insert['amount'] * $taxrate;
			// 	// $taxamount 					= $data_insert['discountedamount'] * $taxrate;
			// 	$data_insert['taxrate']    	= $taxrate;
			// 	$data_insert['taxamount']   = $tempArrayValue['taxamount'];
			// }else{
			// 	$data_insert['taxcode'] 	= '';
			// 	$data_insert['taxrate'] 	= '0.00';
			// 	$data_insert['taxamount'] 	= '0.00';
			// }
			$data_insert['taxcode'] 		= $tempArrayValue['h_taxcode'];
			$data_insert['taxrate'] 		= $tempArrayValue['taxrate'];
			$data_insert['taxamount'] 		= $tempArrayValue['taxamount'];
			$data_insert['stat']		    = $status;

			$linenum++;
			
			$tempArr[] 						= $data_insert;
		}

		/**INSERT IR DETAILS**/
		$isDetailExist	= $this->getValue($detailInvTable, array("COUNT(*) as count"),"voucherno = '$voucherno'");

		if($isDetailExist[0]->count == 0)
		{
			$this->db->setTable($detailInvTable)
				->setValues($tempArr);

			$insertResult = $this->db->runInsert();
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
		}

		return $insertResult;
	}

	public function updateData($data, $table, $cond)
	{
		$result = $this->db->setTable($table)
				->setValues($data)
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
		else if($form == "newVendor")
		{
			$data_insert["stat"]         = "active";

			$data_insert["partnercode"]   = $data["partnercode"];
			$data_insert["first_name"]    = $data["vendor_name"];
			$data_insert["email"] 		  = $data["email"];
			$data_insert["address1"]      = $data["address"];
			$data_insert["businesstype"]  = $data["businesstype"];
			$data_insert["tinno"]         = $data["tinno"];
			$data_insert["terms"]  		  = $data["terms"];
			$data_insert["issupplier"]    = "yes";
			$data_insert["iscustomer"]    = "no";
			$data_insert["autoap"]   	  = "Y";
			$data_insert["currencycode"]  = "PHP";
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

	public function getInvoiceList($search, $customer, $filter, $startdate, $enddate, $sort) {
		$fields = array(
			'inv.transactiondate date',
			'inv.voucherno voucherno',
			'cust.partnername customer',
			'inv.amount amount',
			'app.balance balance',
			'inv.stat stat'
		);
		$sort = ($sort) ? $sort : 'inv.voucherno desc';
		$condition = " AND inv.stat != 'temporary' ";
		
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('inv.voucherno','cust.partnername'));
		}
		if( $filter == 'unpaid' )
		{
			$condition 	.=	" AND inv.stat = 'posted' AND (inv.amount = app.balance) AND inv.amount > 0 AND inv.stat != 'cancelled' ";
		}
		else if( $filter == 'partial' )
		{
			$condition 	.= 	" AND inv.stat = 'posted' AND (app.balance > 0) AND (inv.amount != app.balance) AND inv.stat != 'cancelled' ";
		}
		else if( $filter == 'paid' )
		{
			$condition 	.= 	" AND inv.stat = 'posted' AND (app.balance = 0) AND (inv.amount != app.balance) AND inv.stat != 'cancelled' ";
		}
		else if( $filter == 'approval' )
		{
			$condition 	.= 	" AND inv.stat = 'open' ";
		}
		else if( $filter == 'cancelled' )
		{
			$condition 	.= 	" AND inv.stat = 'cancelled' ";
		}
		else if( $filter == 'all' )
		{
			$condition 	.= 	" ";
		}
		
		if ($customer && $customer != 'none') {
			$condition .= " AND cust.partnercode = '$customer' ";
		}
		if ($startdate && $enddate) {
			$condition .= " AND inv.transactiondate BETWEEN '$startdate' AND '$enddate' ";
		}
		
		$result = $this->db->setTable('salesinvoice as inv')
						->setFields($fields)
						->leftJoin('partners cust ON cust.partnercode = inv.customer AND cust.companycode = inv.companycode')
						->leftJoin("accountsreceivable app ON app.sourceno = inv.voucherno AND app.companycode = inv.companycode AND app.stat != 'cancelled' ")
						->setWhere(" inv.stat NOT IN ('temporary') ".$condition)
						->setGroupBy(" inv.voucherno ")
						->setOrderBy($sort)
						->runPagination();

		return $result;
	}

	public function retrieveListing($data)
	{
		$fields = array(
			'inv.transactiondate date',
			'inv.voucherno voucherno',
			'cust.partnername customer',
			'inv.amount amount',
			'app.balance balance',
			'inv.stat stat'
		);

		$daterangefilter 	= isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
		$custfilter      	= isset($data['custfilter']) ? htmlentities($data['custfilter']) : ""; 
		$searchkey 		 	= isset($data['search']) ? htmlentities($data['search']) : "";
		$condition 		 	= isset($data['condition']) ? htmlentities($data['condition']) : "";
		$items 		 		= isset($data['items']) ? htmlentities($data['items']) : 20;

		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";

		$add_query 	= (!empty($searchkey)) ? " AND (inv.voucherno LIKE '%$searchkey%' OR cust.partnername LIKE '%$searchkey%') " : "";
		$add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND inv.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
		$add_query .= (!empty($custfilter) && $custfilter != 'all') ? "AND cust.partnercode = '$custfilter' " : "";

		if( $condition == 'unpaid' )
		{
			$add_query 	.=	" AND inv.stat = 'posted' AND (inv.amount = app.balance) AND inv.amount > 0 AND inv.stat != 'cancelled' ";
		}
		else if( $condition == 'partial' )
		{
			$add_query 	.= 	" AND inv.stat = 'posted' AND (app.balance >= 0) AND (inv.amount != app.balance) AND inv.stat != 'cancelled' ";
		}
		else if( $condition == 'paid' )
		{
			$add_query 	.= 	" AND inv.stat = 'posted' AND (app.balance = 0) AND (inv.amount != app.balance) AND inv.stat != 'cancelled' ";
		}
		else if( $condition == 'pending' )
		{
			$add_query 	.= 	" AND inv.stat = 'open' ";
		}
		else if( $condition == 'cancelled' )
		{
			$add_query 	.= 	" AND inv.stat = 'cancelled' ";
		}
		else if( $condition == 'all' )
		{
			$add_query 	.= 	" ";
		}

		return $this->db->setTable('salesinvoice as inv')
						->setFields($fields)
						->leftJoin('partners cust ON cust.partnercode = inv.customer AND cust.companycode = inv.companycode')
						->leftJoin("accountsreceivable app ON app.sourceno = inv.voucherno AND app.companycode = inv.companycode AND app.stat != 'cancelled' ")
						->setWhere(" inv.stat NOT IN ('temporary') ".$add_query)
						->setGroupBy(" inv.voucherno ")
						->setOrderBy(" inv.voucherno DESC ")
						->runSelect()
						->getResult();
	}
	
	public function retrieveSalesInvoice($voucherno)
	{	 
		$retrieved_data =	array();
		
		$header_fields 	= 	"inv.voucherno as voucherno, inv.transactiondate as transactiondate, inv.drno as drno, inv.referenceno as referenceno,
		 					inv.duedate as duedate, cust.partnercode as customercode, cust.partnername as customername, inv.discounttype as discounttype, 
							inv.discountamount as discountamount, inv.amount as amount, inv.netamount as netamount, inv.vat_sales as vat_sales, 
							inv.vat_exempt as vat_exempt, inv.taxamount as taxamount, inv.remarks as remarks, inv.stat as status";

		$condition 		=	" inv.voucherno = '$voucherno' ";
		
		$retrieved_data['header'] 	= 	$this->db->setTable('salesinvoice as inv')
												->setFields($header_fields)
												->leftJoin('partners cust ON cust.partnercode = inv.customer AND cust.companycode = inv.companycode')
												->setWhere($condition)
												->setLimit('1')
												->runSelect()
												->getRow();
									
		// Retrieve Vendor Details
		$customer_code 			 	 = 	$retrieved_data['header']->customercode;
		$retrieved_data['customer']  =	$this->retrieveCustomerDetails($customer_code);

		// Retrieve Details
		$detail_fields 			= "itemcode, detailparticular, unitprice, issueqty, taxcode, taxrate, taxamount, amount, itemdiscount, discountedamount, issueuom, discounttype, discountrate";
		$condition 				= " voucherno = '$voucherno' ";
		
		$retrieved_data['details'] = 	$this->db->setTable('salesinvoice_details')
										->setFields($detail_fields)
										->setWhere($condition)
										->runSelect()
										->getResult();
		return $retrieved_data;
	}

	public function retrieveDeliveries($code)
	{
		$header_fields 	= 	"dr.customer, dr.remarks, dr.taxamount, dr.taxcode, (dr.discountamount/so.amount) discount, dr.discounttype, dr.discountamount";
		$condition 		=	" dr.voucherno = '$code' ";
		$retrieved_data['header'] 	= 	$this->db->setTable('deliveryreceipt dr')
												->leftJoin('salesorder so on so.voucherno = dr.source_no AND so.companycode = dr.companycode')
												->setFields($header_fields)
												->setWhere($condition)
												->setLimit('1')
												->runSelect()
												->getRow();

		$detail_fields 		= "itemcode, detailparticular, unitprice, issueqty, taxcode, taxrate, taxamount, amount, issueuom, discountrate, discounttype, discountamount";
		$condition 			= " voucherno = '$code' ";
		
		$retrieved_data['details'] 	= $this->db->setTable('deliveryreceipt_details')
										->setFields($detail_fields)
										->setWhere($condition)
										->runSelect()
										->getResult();
		return $retrieved_data;
	}

	public function saveData($table, $data, $cond="")
	{
		$result 	= "";
		$querytype 	= $data["querytype"];
		unset($data["querytype"]);

		if($querytype == 'insert')
		{
			$result	 = $this->db->setTable($table)
								->setValues($data)
								->runInsert();
		}
		else if($querytype == 'update')
		{
			$result  = $this->db->setTable($table)
								->setValues($data)
								->setWhere($cond)
								->runUpdate();

		}

		return $result;
	}

	public function deleteData($table, $cond)
	{	
		$this->db->setTable($table)
				->setWhere($cond);

		$result = $this->db->runDelete();

		return $result;
	}

	/**
	 * Set Invoice Number
	 */
	public function setInvoice($invoice)
	{
		$this->invoice = $invoice;
	}

	/**
	 * Get Invoice Number
	 */
	public function getInvoice()
	{
		return $this->invoice;
	}

	/**
	 * Generate Accounts Receivable
	 */
	public function generateReceivable($invoice, $auto_ar, $trigger)
	{
		$seq 				= new seqcontrol();
		$detail_info		= array();
		$financial_header 	= array();
		$result 			= 1;
		$existing_ar		= $this->getValue("accountsreceivable", array('voucherno')," sourceno = '$invoice' AND stat = 'posted' ");
		
		/**
		 * Get Total Invoice Amount
		 **/
		$invoice_total 		= $this->getValue("salesinvoice", array('amount')," voucherno = '$invoice' AND (stat = 'open' OR stat = 'posted') ");
		if($invoice_total[0]->amount > 0){

			if($trigger == 'no')
			{
				$ar_data 			= array();
				$ar_data['stat'] 	= 'cancelled';
				$result 			= $this->db->setTable("accountsreceivable")
										->setValues($ar_data)
										->setWhere(" sourceno = '$invoice' ")
										->runUpdate();

				if($result){
					$voucher = $this->getValue("accountsreceivable", array("voucherno"), " sourceno = '$invoice' AND stat = 'posted' ");
					if(!empty($voucher))
					{
						$vno 	 = $voucher[0]->voucherno;
						$result  = $this->db->setTable("ar_details")
											->setValues($ar_data)
											->setWhere(" voucherno = '$vno' ")
											->runUpdate();
					}
				}
				
			}
			
			if($trigger == 'yes' && $auto_ar)
			{
				$header_fields 		= " transactiondate, period, fiscalyear, duedate, customer, remarks, amount, discounttype, discountamount, netamount, referenceno ";
				$condition 			= " voucherno = '$invoice' ";
				$retrieved_data['header'] 	= 	$this->db->setTable('salesinvoice')
														->setFields($header_fields)
														->setWhere($condition)
														->setLimit('1')
														->runSelect()
														->getRow();

				$detail_fields 		= " inv.itemcode as itemcode, inv.detailparticular as remarks, inv.amount as amount,
										inv.taxamount as taxamount, inv.taxcode as taxcode, item.receivable_account as araccount,
										item.revenue_account as slsaccount, tax.salesAccount as vataccount, inv.taxrate as taxrate,
										itmclass.receivable_account as class_araccount, itmclass.revenue_account as class_slsaccount,
										inv.itemdiscount as itemdiscount";
				$condition 			= " voucherno = '$invoice' ";
				
				$retrieved_data['details'] 	= $this->db->setTable('salesinvoice_details as inv')
														->setFields($detail_fields)
														->leftJoin('items item ON item.itemcode = inv.itemcode AND item.companycode = inv.companycode')
														->leftJoin('itemclass itmclass ON itmclass.id = item.classid AND itmclass.companycode = inv.companycode')
														->leftJoin('fintaxcode tax ON tax.fstaxcode = inv.taxcode AND tax.companycode = inv.companycode')
														->setWhere($condition)
														->runSelect()
														->getResult();
												
				$financial_voucher  					= ($existing_ar && !empty($existing_ar[0]->voucherno)) ? $existing_ar[0]->voucherno : $seq->getValue('AR');
				$financial_header['voucherno']			= $financial_voucher;
				$financial_header['transactiondate']	= $retrieved_data['header']->transactiondate;
				$financial_header['period']				= $retrieved_data['header']->period;
				$financial_header['customer']			= $retrieved_data['header']->customer;
				$financial_header['fiscalyear']			= $retrieved_data['header']->fiscalyear;
				$financial_header['currencycode']		= 'PHP';
				$financial_header['exchangerate']		= '1.00';
				$financial_header['stat']				= 'posted';
				$financial_header['transtype']			= 'AR';
				$financial_header['invoicedate']		= $retrieved_data['header']->transactiondate;
				$financial_header['duedate']			= $retrieved_data['header']->duedate;
				$financial_header['invoiceno']			= $invoice;
				$financial_header['referenceno']		= $retrieved_data['header']->referenceno;
				$financial_header['amount']				= $retrieved_data['header']->amount;
				$financial_header['convertedamount']	= $retrieved_data['header']->amount;
				$financial_header['terms']				= '0';
				$financial_header['amountreceived']		= '0.00';
				$financial_header['amountforreceipt']	= $retrieved_data['header']->amount;
				$financial_header['particulars']		= $retrieved_data['header']->remarks;
				$financial_header['balance']			= $retrieved_data['header']->amount;
				$financial_header['source']				= 'SI';
				$financial_header['sourceno']			= $invoice;

				/**
				* Save Accounts Receivable 
				*/
				if(!empty($retrieved_data['header']))
				{
					$this->db->setTable("accountsreceivable")
							->setWhere("voucherno = '$financial_voucher'")
							->runDelete();

					$result	 = $this->db->setTable("accountsreceivable")
									->setValues($financial_header)
									->runInsert();
					
					if($result){
						$discounttype	= $retrieved_data['header']->discounttype;
						$discountamount	= $retrieved_data['header']->discountamount;
						$invoiceamount	= $retrieved_data['header']->amount;
						$discountperc	= ($discountamount > 0) ? $discountamount / $invoiceamount : 0;
						$detailData 	= $retrieved_data['details'];

						// if($discounttype == 'perc' && $discountamount > 0)
						// {
						// 	$invoicenet		= $retrieved_data['header']->netamount;
						// 	$discountamount	= $invoicenet * ($discountamount / 100);
						// 	$discountperc	= ($discountamount > 0) ? round(($discountamount / $invoiceamount),2) : 0;
						// }

						if(!empty($detailData)){
							foreach($detailData as $detailIndex => $detailValue)
							{
								$itemcode 		= $detailValue->itemcode;
								$remarks 		= $detailValue->remarks;
								$amount 		= $detailValue->amount;
								$taxamount 		= $detailValue->taxamount;
								$taxcode 		= $detailValue->taxcode;
								$taxrate 		= $detailValue->taxrate;
								$itemdiscount 	= $detailValue->itemdiscount;
								$discounttax 	= $taxamount * $discountperc;
								//$amount 		= $amount - (($amount - $taxamount) * $discountperc);

								$account 		= $detailValue->araccount;
								$vataccount 	= $detailValue->vataccount;
								$salesaccount 	= $detailValue->slsaccount;

								$account 		= (!empty($account) && $account != 0) ? $account : $detailValue->class_araccount;
								$salesaccount 	= (!empty($salesaccount) && $salesaccount != 0) ? $salesaccount : $detailValue->class_slsaccount;
								$discountaccount= $this->getValue("fintaxcode",array('salesAccount as account'), " fstaxcode = 'DC' ");
								
								/**GROUP BALANCE ACCOUNTS**/
								if(!empty($account)){
									$account_info[$account]['remarks'][] 		= $remarks;
									$account_info[$account]['amount'][] 		= $amount + $taxamount;
									//$account_info[$account]['amount'][] 		= (($amount - $itemdiscount) + (($amount - $itemdiscount) * $taxrate));
									//$account_info[$account]['amount'][] 		= $amount + ($taxamount - $discounttax);
								}
								
								/**GROUP SALES ACCOUNTS**/
								if(!empty($salesaccount)){
									$sales_info[$salesaccount]['remarks'][] 	= $remarks;
									if($discountperc > 0){
										//$sales_info[$salesaccount]['amount'][] 	= $amount;
										$sales_info[$salesaccount]['amount'][] 	= $amount + $itemdiscount;
									}else{
										//$sales_info[$salesaccount]['amount'][] 	= $amount;
										$sales_info[$salesaccount]['amount'][] 	= $amount;
									}
								}
								
								/**GROUP TAX ACCOUNTS**/
								if(!empty($vataccount)){
									$vat_info[$vataccount]['remarks'][] 		= $remarks;
									//$vat_info[$vataccount]['amount'][] 			= ($amount - $itemdiscount) * $taxrate;
									$vat_info[$vataccount]['amount'][] 			= $amount * $taxrate;
								}
							}

							/**
							* Discount
							*/
							$linenum		= 1;

							if($discountamount > 0)
							{
								$detail_info['voucherno']			= $financial_header['voucherno'];
								$detail_info['transtype']			= 'AR';
								$detail_info['linenum']				= $linenum;
								$detail_info['accountcode']			= $discountaccount[0]->account;
								$detail_info['currencycode']		= "PHP";
							
								$detail_info['debit']				= $discountamount;
								$detail_info['credit']				= 0;
								$detail_info['converteddebit']		= $discountamount;
								$detail_info['convertedcredit']		= 0;
						
								$detail_info['sourcecode']			= 'SI';
								//$detail_info['detailparticulars']	= '';
								$detail_info['stat']				= 'posted';
							
								$detailArray[]						= $detail_info;
							
								$linenum++;
							}

							/**
							* Accounts Receivable Account
							*/
						
							if(!empty($account_info)){
								foreach($account_info as $account_index => $account_value)
								{
									$previndex		= $account_index;
									//$totalamount	= array_sum($account_value['amount']) - $discountamount;
									$totalamount	= array_sum($account_value['amount']);

									$detail_info['voucherno']			= $financial_header['voucherno'];
									$detail_info['transtype']			= 'AR';
									$detail_info['linenum']				= $linenum;
									$detail_info['accountcode']			= $account_index;
									$detail_info['currencycode']		= "PHP";
								
									$detail_info['debit']				= $totalamount;
									$detail_info['credit']				= 0;
									$detail_info['converteddebit']		= $totalamount;
									$detail_info['convertedcredit']		= 0;

									$detail_info['sourcecode']			= 'SI';

									//$detail_info['detailparticulars']	= (isset($account_value['remarks'][0])) ? $account_value['remarks'][0] : '';
									$detail_info['stat']				= 'posted';
									
									$detailArray[]						= $detail_info;
									
									$linenum++;
								}
							}

							/**
							* Sales Account
							*/
							if(!empty($sales_info)){
								foreach($sales_info as $sales_index => $sales_value)
								{
									$totalsales	= array_sum($sales_value['amount']);
									
									$detail_info['voucherno']			= $financial_header['voucherno'];
									$detail_info['transtype']			= 'AR';
									$detail_info['linenum']				= $linenum;
									$detail_info['accountcode']			= $sales_index;
									$detail_info['currencycode']		= "PHP";
									
									$detail_info['debit']				= 0;
									$detail_info['credit']				= $totalsales;
									$detail_info['converteddebit']		= 0;
									$detail_info['convertedcredit']		= $totalsales;
									
									
									$detail_info['sourcecode']			= 'SI';
									//$detail_info['detailparticulars']	= (isset($sales_value['remarks'][0])) ? $sales_value['remarks'][0] : '';
									$detail_info['stat']				= 'posted';
									
									$detailArray[]						= $detail_info;
									
									$linenum++;	
								}
							}

							/**
							* VAT Account
							*/
							if(!empty($vat_info)){
								foreach($vat_info as $vat_index => $vat_value)
								{
									$totalvat			= array_sum($vat_value['amount']);
									
									$detail_info['voucherno']			= $financial_header['voucherno'];
									$detail_info['transtype']			= 'AR';
									$detail_info['linenum']				= $linenum;
									$detail_info['accountcode']			= $vat_index;
									$detail_info['currencycode']		= "PHP";
									
									$detail_info['debit']				= 0;
									$detail_info['credit']				= $totalvat;
									$detail_info['converteddebit']		= 0;
									$detail_info['convertedcredit']		= $totalvat;
									
									$detail_info['sourcecode']			= 'SI';

									//$detail_info['detailparticulars']	= (isset($vat_value['remarks'][0])) ? $vat_value['remarks'][0] : '';
									$detail_info['stat']				= 'posted';
									
									$detailArray[]						= $detail_info;
									
									$linenum++;	
								}
							}
							
							if(!empty($detailArray)){
								$this->db->setTable("ar_details")
									->setWhere("voucherno = '$financial_voucher'")
									->runDelete();

								$result	 = $this->db->setTable("ar_details")
													->setValues($detailArray)
													->runInsert();				
							}
						}
					}
				}
			}
			
		}
		if($result)
		{
			// Update Invoice Status
			$invoice_data 			= array();
			$invoice_data['stat'] 	= ($trigger == 'yes') ? 'posted' : 'open';
			$result  = $this->db->setTable("salesinvoice")
									->setValues($invoice_data)
									->setWhere(" voucherno = '$invoice' ")
									->runUpdate();
		}
		
		return $result;
	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

	public function retrieveVatExValue(){
		$result 			= 	$this->db->setTable('wc_reference')
										  ->setFields(array('code','value'))
										  ->setWhere("code = 'sale_vatex'")
										  ->runSelect(false)
										  ->getRow();
		return $result;
	}
}
?>