<?php
	class sales_order extends wc_model
	{
		public function retrieveCustomerList()
		{
			$result = $this->db->setTable('partners')
						->setFields("partnercode ind, CONCAT(partnercode,' - ',partnername) val")
						->setWhere("partnercode != '' AND partnertype = 'customer'  AND stat = 'active'")
						->setOrderBy("val")
						->runSelect()
						->getResult();
			
			return $result;
		}

		public function retrieveCustomerDetails($vendor_code)
		{
			$fields = "address1, tinno, terms, email, partnername as companyname, CONCAT( first_name, ' ', last_name ) AS name";
			$cond 	= "partnercode = '$vendor_code' AND partnertype = 'customer' ";

			$result = $this->db->setTable('partners')
								->setFields($fields)
								->setWhere($cond)
								->setLimit('1')
								->runSelect()
								->getRow();

			return $result;
		}

		public function retrieveItemDetails($itemcode, $customer)
		{
			$fields = "i.itemname as itemname, i.itemdesc as itemdesc, i.uom_base, p.itemprice as price, template.adjusted_price as c_price, 
						i.receivable_account item_receivable, i.revenue_account item_revenue, i.expense_account item_expense, i.payable_account item_payable, i.bundle bundle,
						i.inventory_account item_inventory, class.receivable_account class_receivable, class.revenue_account class_revenue, class.expense_account class_expense, 
						class.payable_account class_payable, class.inventory_account class_inventory, u.uomcode, template.stat as stat";
			$cond 	= "i.itemcode = '$itemcode'";

			$subquery 		=	"SELECT  pld.sellPrice as adjusted_price,
											pld.itemDtlCode as itemcode,
											cp.companycode as companycode,
											cp.itemPriceCode as template_code,
											cp.customerCode as customer,
											cp.stat as stat
									FROM customer_price_list cp 
									LEFT JOIN price_list_details pld ON pld.itemPriceCode = cp.itemPriceCode 
															AND cp.companycode = pld.companycode
									WHERE cp.customerCode = '$customer'";

			$result = $this->db->setTable('items i')
								->leftJoin('uom u ON u.uomcode = i.uom_selling AND u.companycode = i.companycode')
								->leftJoin('items_price p ON p.itemcode = i.itemcode AND p.companycode = i.companycode')
								->leftJoin('itemclass class ON class.id = i.classid AND class.companycode = i.companycode')
								->leftJoin('( '. $subquery .' ) template ON template.companycode = i.companycode AND template.itemcode = i.itemcode ')
								->setFields($fields)
								->setWhere($cond)
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
			$fields 			= array('s.voucherno','s.quotation_no', 'p.partnername', 's.transactiondate','s.stat','s.netamount');

			$daterangefilter 	= isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
			$custfilter      	= isset($data['customer']) ? htmlentities($data['customer']) : ""; 
			$searchkey 		 	= isset($data['search']) ? htmlentities($data['search']) : "";
			$filter 		 	= isset($data['filter']) ? htmlentities($data['filter']) : "";
			$sort 		 		= isset($data['sort']) ? htmlentities($data['sort']) : "s.voucherno DESC";	
			$limit 		 		= isset($data['limit']) ? htmlentities($data['limit']) : "10";
		
			$datefilterArr		= explode(' - ',$daterangefilter);
			$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
			$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";

			$add_query 	= (!empty($searchkey)) ? "AND (s.voucherno LIKE '%$searchkey%' OR p.partnername LIKE '%$searchkey%') " : "";
			$add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND s.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
			$add_query .= (!empty($custfilter) && $custfilter != 'none') ? "AND p.partnercode = '$custfilter' " : "";
			
			if( !empty($filter) && $filter == 'open')
			{
				$add_query 	.=	" AND s.stat = 'open' ";
			}
			else if( !empty($filter) && $filter == 'partial' )
			{
				$add_query 	.= 	" AND s.stat = 'partial' ";
			}
			else if( !empty($filter) && $filter == 'posted' )
			{
				$add_query 	.= 	" AND s.stat = 'posted' ";
			}
			else if( !empty($filter) && $filter == 'cancelled' )
			{
				$add_query 	.= 	" AND s.stat = 'cancelled' ";
			}
			else if( $filter == 'all' )
			{
				$add_query 	.= 	" ";
			}
			
			$result 	=	 $this->db->setTable('salesorder s')
							->setFields($fields)
							->leftJoin('partners p ON p.partnercode = s.customer AND p.partnertype = "customer"')
							->setWhere(" s.stat != 'temporary'  $add_query")
							->setOrderBy($sort)
							->setLimit($limit)
							->runPagination();
						// echo $this->db->getQuery();
			return $result;
		}
		
		public function retrieveExistingSO($voucherno)
		{	 
			$retrieved_data =	array();
			
			$header_fields 	= 	"s.voucherno, s.transactiondate, s.duedate, s.customer, p.partnername, CONCAT(p.first_name,' ',p.last_name) as  customer_name, s.s_address, s.amount, s.discounttype, s.discountamount, s.netamount, s.vat_sales, s.vat_exempt, s.vat_zerorated, s.taxamount, s.remarks, s.stat";

			$condition 		=	" s.voucherno = '$voucherno' ";
			
			$retrieved_data['header'] 	= 	$this->db->setTable('salesorder s')
													->leftJoin(' partners p ON p.partnercode = s.customer AND p.partnertype = \'customer\' ')
													->setFields($header_fields)
													->setWhere($condition)
													->setLimit('1')
													->runSelect()
													->getRow();
										
			// Retrieve Customer Details
			$customer_code 			 	 = 	$retrieved_data['header']->customer;
			$retrieved_data['customer']  =	$this->retrieveCustomerDetails($customer_code);

			// Retrieve Details
			$detail_fields 			= "sd.itemcode, sd.detailparticular, sd.warehouse, w.description, sd.unitprice, sd.issueqty, u.uomcode issueuom, sd.taxcode, sd.taxrate, sd.amount, sd.discountrate, sd.discountamount, sd.discountedamount, sd.discounttype, sd.parentcode, sd.isbundle, sd.parentline";
			$condition 				= " sd.voucherno = '$voucherno' ";
			
			$retrieved_data['details'] = 	$this->db->setTable('salesorder_details sd')
											->leftJoin('uom u ON u.uomcode = sd.issueuom')
											->leftJoin('warehouse w ON w.warehousecode = sd.warehouse ')
											->setFields($detail_fields)
											->setWhere($condition)
											->setOrderBy('sd.linenum')
											->runSelect()
											->getResult();
			return $retrieved_data;
		}

		public function retrieveExistingSQ($quotation_no)
		{	 
			$retrieved_data =	array();
			
			$header_fields 	= 	"voucherno, transactiondate, duedate, customer, amount, discounttype, discountamount, netamount, vat_sales, vat_exempt, taxamount";

			$condition 		=	" voucherno = '$quotation_no' ";
			
			$retrieved_data['header'] 	= 	$this->db->setTable('salesquotation')
															->setFields($header_fields)
															->setWhere($condition)
															->setLimit('1')
															->runSelect()
															->getRow();
										
			// Retrieve Vendor Details
			$customer_code 			 	 = 	$retrieved_data['header']->customer;
			$retrieved_data['customer']  =	$this->retrieveCustomerDetails($customer_code);

			// Retrieve Details
			$detail_fields 			= "itemcode, detailparticular, unitprice, issueqty, taxcode, taxrate, amount, issueuom";
			$condition 				= " voucherno = '$quotation_no' ";
			
			$retrieved_data['details'] = 	$this->db->setTable('salesquotation_details')
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
						->runSelect($bool)
						->getResult();

			return $result;
		}

		public function getJVDates()
		{
			$result = $this->db->setTable("journalvoucher")
						->setFields("period, fiscalyear, transactiondate")
						->setWhere("source = 'closing' AND stat != 'temporary'")
						->setGroupBy("period, fiscalyear")
						->setOrderBy("period DESC")
						->setLimit(1)
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

		public function getTaxCode($taxtype,  $orderby = "")
		{
			$result = $this->db->setTable('fintaxcode')
						->setFields("fstaxcode ind, shortname val")
						->setWhere("taxtype = '$taxtype'")
						->setOrderBy($orderby)
						->runSelect(false)
						->getResult();

			return $result;
		}

		public function processTransaction($data, $task, $voucher = "")
		{
			$mainInvTable		= "salesorder";
			$detailInvTable		= "salesorder_details";
		
			$insertResult		= 0;
			$errmsg				= array();

			// Journal Data
			$voucherno			= (isset($data['h_voucher_no']) && (!empty($data['h_voucher_no']))) ? htmlentities(addslashes(trim($data['h_voucher_no']))) : "";

			$quotation_no		= (isset($data['h_quotation_no']) && (!empty($data['h_quotation_no']))) ? htmlentities(addslashes(trim($data['h_quotation_no']))) : "";
			
			$customer			= (isset($data['customer']) && (!empty($data['customer']))) ? htmlentities(addslashes(trim($data['customer']))) : "";

			$s_address			= (isset($data['s_address']) && (!empty($data['s_address']))) ? htmlentities(addslashes(trim($data['s_address']))) : "";
			
			$transactiondate	= (isset($data['transaction_date']) && (!empty($data['transaction_date']))) ? htmlentities(addslashes(trim($data['transaction_date']))) : "";
			
			$duedate 			= (isset($data['due_date']) && (!empty($data['due_date']))) ? htmlentities(addslashes(trim($data['due_date']))) : "";

			$remarks			= (isset($data['remarks']) && (!empty($data['remarks']))) ? htmlentities(addslashes(trim($data['remarks']))) : "";
			
			$terms				= (isset($data['customer_terms']) && (!empty($data['customer_terms']))) ? htmlentities(addslashes(trim($data['customer_terms']))) : 0;
			
			$vat_sales 			= (isset($data['t_vatsales']) && (!empty($data['t_vatsales']))) ? htmlentities(addslashes(trim($data['t_vatsales']))) : "";

			$vat_exempt 		= (isset($data['t_vatexempt']) && (!empty($data['t_vatexempt']))) ? htmlentities(addslashes(trim($data['t_vatexempt']))) : "";

			$vat_zerorated 		= (isset($data['t_vatzerorated']) && (!empty($data['t_vatzerorated']))) ? htmlentities(addslashes(trim($data['t_vatzerorated']))) : "";

			$vat 	 			= (isset($data['t_vat']) && (!empty($data['t_vat']))) ? htmlentities(addslashes(trim($data['t_vat']))) : "";

			$subtotal 			= (isset($data['t_subtotal']) && (!empty($data['t_subtotal']))) ? htmlentities(addslashes(trim($data['t_subtotal']))) : "";
			
			$total_discount 	= (isset($data['t_discount']) && (!empty($data['t_discount']))) ? htmlentities(addslashes(trim($data['t_discount']))) : "";

			$totalamount 		= (isset($data['t_total']) && (!empty($data['t_total']))) ? htmlentities(addslashes(trim($data['t_total']))) : "";

			$discounttype 		= (isset($data['discounttype']) && (!empty($data['discounttype']))) ? htmlentities(addslashes(trim($data['discounttype']))) : "";
		
			$discount_amount 	= (isset($data['discountamount']) && (!empty($data['discountamount']))) ? htmlentities(addslashes(trim($data['discountamount']))) : "";
			
			$discount_rate 		= (isset($data['discountrate']) && (!empty($data['discountrate']))) ? htmlentities(addslashes(trim($data['discountrate']))) : "";

			$_final 			= (isset($data['save']) && (!empty($data['save']))) ? htmlentities(addslashes(trim($data['save']))) : "";	
			
			$status				= ( empty($_final) && $voucher == "" ) ? "temporary" : "open";
			
			/**TRIM COMMAS FROM AMOUNTS**/
			$subtotal			= str_replace(',','',$subtotal);
			$totalamount		= str_replace(',','',$totalamount);
			$discount_amount	= str_replace(',','',$discount_amount);
			$total_discount		= str_replace(',','',$total_discount);

			$vat				= str_replace(',','',$vat);
			$vat_sales			= str_replace(',','',$vat_sales);
			$vat_exempt			= str_replace(',','',$vat_exempt);
			$vat_zerorated		= str_replace(',','',$vat_zerorated);

			/**FORMAT DATES**/
			$transactiondate	= date("Y-m-d",strtotime($transactiondate));
			$duedate 			= date("Y-m-d",strtotime($duedate));
			$period				= date("n",strtotime($transactiondate));
			$fiscalyear			= date("Y",strtotime($transactiondate));

			$post_header['voucherno'] 			=	$voucherno;
			$post_header['transactiondate'] 	=	$transactiondate;
			$post_header['duedate'] 			= 	$duedate;
			$post_header['customer'] 			=	$customer;
			$post_header['s_address'] 			=	$s_address;
			$post_header['fiscalyear'] 			=	$fiscalyear;
			$post_header['period'] 				=	$period;
			$post_header['transtype'] 			=	"SO";
			$post_header['stat'] 		 		= 	$status;
			$post_header['term'] 				= 	$terms;
			$post_header['remarks'] 			=	$remarks;
			$post_header['quotation_no'] 		=	$quotation_no;
			$post_header['referenceno'] 		= 	'';
			$post_header['amount'] 				= 	$subtotal;
			$post_header['discounttype'] 		=	$discounttype;
			$post_header['discountamount'] 		=	$total_discount;
			$post_header['netamount'] 			=	$totalamount;
			$post_header['taxcode'] 			=	'VAT';
			$post_header['taxamount'] 			=	$vat;
			$post_header['wtaxcode'] 			=	'';
			$post_header['wtaxamount'] 			=	'';
			$post_header['wtaxrate'] 			=	'';
			$post_header['vat_sales'] 			= 	$vat_sales;
			$post_header['vat_exempt'] 			=	$vat_exempt;
			$post_header['vat_zerorated'] 		=	$vat_zerorated;

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
				if($postIndex == 'h_itemcode' || $postIndex == 'h_parentcode' || $postIndex == 'h_isbundle' || $postIndex == 'h_parentline' || $postIndex=='detailparticulars' || $postIndex == 'h_warehouse' || 
					$postIndex == 'quantity' ||  $postIndex == 'itemprice' || $postIndex == 'discount'|| $postIndex == 'amount' || 
					$postIndex == 'h_amount'|| $postIndex == 'uom' || $postIndex == 'taxamount' || $postIndex == 'taxcode' || 
					$postIndex == 'taxrate' || $postIndex == 'itemdiscount' || $postIndex == 'discountedamount' ) 
				{
					$a		= '';
					
					foreach($postValue as $postValueIndex => $postValueIndexValue)
					{
						if($postIndex == 'quantity' || $postIndex == 'amount' || $postIndex == 'h_amount' || $postIndex == 'taxamount' || $postIndex == 'taxrate' || 
							$postIndex == 'itemprice' || $postIndex == 'discount'|| $postIndex == 'itemdiscount' || $postIndex == 'discountedamount')
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

			//||  $postIndex=='taxcode' || 
					//$postIndex=='taxrate' || $postIndex == 'taxamount' 
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
				$itemcode 			=	$tempArrayValue['h_itemcode'];
				$parentcode 		=	$tempArrayValue['h_parentcode'];
				$isbundle 			=	$tempArrayValue['h_isbundle'];
				$h_parentline 		=	$tempArrayValue['h_parentline'];
				$detailparticular 	= 	$tempArrayValue['detailparticulars'];
				$quantity 			=	$tempArrayValue['quantity'];
				$warehouse 			=  	$tempArrayValue['h_warehouse'];
				$price 				= 	$tempArrayValue['itemprice'];
				$discount 			= 	$tempArrayValue['discount'];
				$amount 			=	$tempArrayValue['h_amount'];
				$taxcode 			=	isset($tempArrayValue['taxcode']) ? $tempArrayValue['taxcode'] : '';
				$taxrate 			=	$tempArrayValue['taxrate'];
				$taxamount 			=	$tempArrayValue['taxamount'];
				$discountamt  		=	$tempArrayValue['itemdiscount'];
				$discountedamt 		=	$tempArrayValue['discountedamount'];

				$ret_conversion 	=	$this->getValue("items",array('uom_selling','selling_conv')," itemcode = '$itemcode' ");

				//var_dump($ret_conversion);

				$uom 				=	(!empty($ret_conversion[0]->uom_selling)) ? $ret_conversion[0]->uom_selling 	: 	'';
				$conversion 		=	(!empty($ret_conversion[0]->selling_conv)) ? $ret_conversion[0]->selling_conv 	: 	0;
				$convissueqty 		=	$quantity * $conversion;

				$convuom 			= 	$tempArrayValue['uom'];

				if( $tempArrayValue['h_itemcode'] != "" )
				{
					$data_insert["voucherno"]         	= $voucherno;
					$data_insert['transtype']         	= 'SO';
					$data_insert['linenum']	        	= $linenum;
					$data_insert['itemcode']	  		= $itemcode;
					$data_insert['parentcode']	  		= $parentcode;
					$data_insert['isbundle']	  		= $isbundle;
					$data_insert['parentline']	  		= $h_parentline;
					$data_insert['detailparticular']	= $detailparticular;
					$data_insert['warehouse']			= $warehouse;
					$data_insert['issueuom']			= $uom;
					$data_insert['issueqty']			= $quantity;
					$data_insert['unitprice']	  		= $price;
					$data_insert['amount']	  			= $amount;
					$data_insert['stat']	  			= $status;
					$data_insert['taxcode']			  	= $taxcode;
					$data_insert['taxrate']		  		= $taxrate;
					$data_insert['taxamount']			= $taxamount;
					$data_insert['convissueqty']	  	= $convissueqty;
					$data_insert['convuom']		  		= $convuom;
				 	$data_insert['conversion']			= $conversion;
					$data_insert['discounttype'] 		= $discounttype;
					$data_insert['discountrate'] 		= ($discounttype == 'perc') ? $discount : 0;
					$data_insert['discountamount'] 		= $discountamt;
					$data_insert['discountedamount'] 	= $discountedamt;

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
			else if( $isDetailExist[0]->count > 0 && ( $task == 'create' || $task == 'edit' ) )
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

			return $result;
		}

		public function deleteData($table, $cond)
		{	
			$this->db->setTable($table)
					->setWhere($cond);

			$result = $this->db->runDelete();

			return $result;
		}

		public function insertCustomer($data)
		{
			$data["stat"]     	   = "active";
			$data['partnertype']    = "customer";

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

		public function export_main($data) {
			
			$fields 			= array('s.voucherno','s.quotation_no', 'p.partnername', 's.transactiondate','s.stat','s.netamount');

			$daterangefilter 	= isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
			$custfilter      	= isset($data['customer']) ? htmlentities($data['customer']) : ""; 
			$searchkey 		 	= isset($data['search']) ? htmlentities($data['search']) : "";
			$filter 		 	= isset($data['filter']) ? htmlentities($data['filter']) : "";
			$sort 		 		= isset($data['sort']) ? htmlentities($data['sort']) : "s.voucherno DESC";	
			$limit 		 		= isset($data['limit']) ? htmlentities($data['limit']) : "10";
		
			$datefilterArr		= explode(' - ',$daterangefilter);
			$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
			$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";

			$add_query 	= (!empty($searchkey)) ? "AND (s.voucherno LIKE '%$searchkey%' OR p.first_name LIKE '%$searchkey%' OR p.last_name LIKE '%$searchkey%') " : "";
			$add_query .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND s.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";
			$add_query .= (!empty($custfilter) && $custfilter != 'none') ? "AND p.partnercode = '$custfilter' " : "";

			if( !empty($filter) && $filter == 'open')
			{
				$add_query 	.=	" AND s.stat = 'open' ";
			}
			else if( !empty($filter) && $filter == 'posted' )
			{
				$add_query 	.= 	" AND s.stat = 'posted' ";
			}
			else if( !empty($filter) && $filter == 'cancelled' )
			{
				$add_query 	.= 	" AND s.stat = 'cancelled' ";
			}
			else
			{
				$add_query 	.= 	" ";
			}
			
			$fields 		= array('s.voucherno','s.quotation_no', 's.customer', 's.transactiondate','s.stat','s.netamount','p.partnername');

			return $this->db->setTable('salesorder s')
							->setFields($fields)
							->leftJoin('partners p ON p.partnercode = s.customer AND p.partnertype = "customer"')
							->setWhere(" s.stat != 'temporary'  $add_query")
							->setOrderBy($sort)
							->setLimit($limit)
							->runSelect()
							->getResult();
		}

		public function retrieve_credit_limit($code){
			$result =  $this->db->setTable('partners')
							 ->setFields('credit_limit')
							 ->setWhere(" partnercode = '$code' AND partnertype = 'customer' AND stat = 'active'")
							 ->runSelect()
							 ->getResult();
							//  echo $this->db->getQuery();
			return $result;
		}

		public function retrieve_incurred_receivables($code){
			$result =  $this->db->setTable('accountsreceivable')
							 ->setFields('SUM(amountreceived) receivables')
							 ->setWhere(" stat NOT IN ('cancelled','temporary') AND customer = '$code'")
							 ->setGroupBy('customer')
							 ->runSelect()
							 ->getResult();
							 
			return $result;
		}

		public function retrieve_outstanding_receivables($code){
			$result =  $this->db->setTable('accountsreceivable')
							 ->setFields('SUM(balance) receivables')
							 ->setWhere(" stat NOT IN ('cancelled','temporary') AND customer = '$code'")
							 ->setGroupBy('customer')
							 ->runSelect()
							 ->getResult();
							 
			return $result;
		}

		public function retrieve_item_quantity($itemcode, $wh){
			$result =  $this->db->setTable('invfile')
							 ->setFields('onhandQty')
							 ->setWhere("itemcode = '$itemcode' AND warehouse = '$wh'")
							 ->runSelect()
							 ->getResult();
			return $result;
		}

		public function getUnReceivedItems($voucherno) {
			$dr_inner = $this->db->setTable('deliveryreceipt a')
								->innerJoin('deliveryreceipt_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
								->setFields('a.companycode, linenum, b.discountamount, b.discountrate, source_no, SUM(issueqty) issueqty')
								->setWhere("a.stat = 'Delivered' OR a.stat = 'With Invoice'")
								->setGroupBy('source_no, itemcode, linenum')
								->buildSelect();
	
			$result	= $this->db->setTable('salesorder a')
								->innerJoin('salesorder_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
								->leftJoin("($dr_inner) dr ON dr.source_no = a.voucherno AND dr.companycode = a.companycode AND dr.linenum = b.linenum")
								->setFields('b.itemcode, detailparticular, b.warehouse, (b.issueqty - dr.issueqty) balance_qty, dr.discountamount as discountamount, dr.discountrate as discountrate, b.amount as amount, b.taxcode, b.taxrate, b.issueuom, unitprice')
								->setWhere("a.stat IN('open', 'partial', 'posted') AND a.voucherno = '$voucherno'")
								->setHaving('balance_qty > 0')
								->runSelect()
								->getResult();
	
			return $result;
		}

		public function getReceivedItems($voucherno) {
			$dr_inner = $this->db->setTable('deliveryreceipt a')
								->innerJoin('deliveryreceipt_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
								->setFields('a.companycode, linenum, b.discountamount, b.discountrate, b.amount as amount, b.taxamount, source_no, SUM(issueqty) issueqty')
								->setWhere("a.stat = 'Delivered' OR a.stat = 'With Invoice'")
								->setGroupBy('source_no, itemcode, linenum')
								->buildSelect();
	
			$result	= $this->db->setTable('salesorder a')
								->innerJoin('salesorder_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
								->leftJoin("($dr_inner) dr ON dr.source_no = a.voucherno AND dr.companycode = a.companycode AND dr.linenum = b.linenum")
								->setFields('b.itemcode, detailparticular, b.warehouse, dr.issueqty, b.taxcode, b.taxrate, dr.taxamount as taxamount, b.issueuom, dr.discountamount as discountamount, dr.discountrate as discountrate, unitprice, dr.amount as amount, vat_sales, vat_exempt, vat_zerorated')
								->setWhere("a.stat IN('open', 'partial', 'posted') AND a.voucherno = '$voucherno'")
								->runSelect()
								->getResult();
	
			return $result;
		}
	}
?>