<?php
class controller extends wc_controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->url 			    = new url();
		$this->so 				= new sales_order();
		$this->seq 				= new seqcontrol();
		$this->input            = new input();
		$this->ui 			    = new ui();
		$this->log 				= new log();
		$this->restrict 		= new sales_restriction_model();
		$this->view->title      = 'Sales Order';
		$this->show_input 	    = true;

		$session                = new session();
		$this->companycode      = $session->get('companycode');
		
		$this->user 		    = USERNAME;

		$this->view->header_active = 'sales/sales_order/';

		$this->fields = array(
				'voucherno',
				'customer',
				'documentno',
				'tinno',
				'address1',
				'terms',
				'transactiondate',
				'remarks',
				'due_date'
			);

		$this->inventory_model  = $this->checkoutModel('inventory_module/inventory_model');
	}

	public function listing()
	{
		$data['ui'] 				= $this->ui;
	
		$data['show_input'] 		= true;

		$data['date_today'] 		= date("M d, Y");
		
		$data['customer_list'] 		= $this->so->retrieveCustomerList();
		
		$data["payment_mode_list"] 	= $this->so->getOption("payment_type");
		$data["cash_account_codes"] = $this->so->retrieveCashAccountClassList();

		$this->view->load('sales_order/sales_orderlist', $data);
	}

	public function create($quotation_no = '')
	{
		$data 					= $this->input->post($this->fields);
	
		// Item Limit
		$item_limit 			= $this->so->getReference("so_limit");
		$data['item_limit']		= ($item_limit[0]->value) 	? 	$item_limit[0]->value 	: 	50; 
		
		// Closed Date
		$close_date 			= $this->restrict->getClosedDate();
		$data['close_date']		= $close_date;
		
		/**LOAD DEFAULT VALUES**/

			$data['customer_list'] 	= $this->so->retrieveCustomerList();
			$data['proforma_list'] 	= $this->so->retrieveProformaList();
			$data["business_type"] 	= $this->so->getOption("businesstype");
			$data["vat_type"] 		= $this->so->getOption("vat_type");
			$data["tax_codes"] 		= $this->so->getTaxCode('VAT');
			$data['percentage'] 	= "";
			$data['h_disctype'] 	= "perc";

			$curr_type_data         = array("currencycode ind", "currency val");
			$data["currency_codes"] = $this->so->getValue("currency", $curr_type_data,'','currencycode');

			$cc_entry_data          = array("itemcode ind","CONCAT(itemcode,' - ',itemname) val");
			$data["itemcodes"] 		= $this->so->getValue("items", $cc_entry_data,'',"itemcode");

			$w_entry_data          = array("warehousecode ind","warehousecode val");
			$data["warehouses"] 	= $this->so->getValue("warehouse", $w_entry_data,'',"warehousecode");

			$acc_entry_data          = array("accountname ind","CONCAT(segment5,' - ', accountname )  val");
			$acc_entry_cond          = "accounttype != 'P'";
			$data["account_ entries"] = $this->so->getValue("chartaccount", $acc_entry_data,$acc_entry_cond, "segment5");

			$data['transactiondate'] = $this->date->dateFormat();

		/**ADD NEW CUSTOMER**/
		
			// Retrieve business type list
			$bus_type_data                = array("code ind", "value val");
			$bus_type_cond                = "type = 'businesstype'";
			$data["business_type_list"]   = $this->so->getValue("wc_option", $bus_type_data, $bus_type_cond, false);

			// Retrieve generated ID
			$gen_value               = $this->so->getValue("salesorder", "COUNT(*) as count", "voucherno != ''");
			$data["generated_id"]    = (!empty($gen_value[0]->count)) ? 'TMP_'.($gen_value[0]->count + 1) : 'TMP_1';
		
		//Finalize Saving	
			$save_status 			= $this->input->post('save');

		/** RETRIEVAL FOR QUOTATION / SO **/

		$data["quotation_no"]  = ""; 

		if( $quotation_no != "" )
		{
			$retrieved_data 		= $this->so->retrieveExistingSQ($quotation_no);
			$data["quotation_no"]  = $retrieved_data["header"]->voucherno;
			/** RETRIEVE DATA **/

			//Header Data
			$data["customer"]      	 = $retrieved_data["header"]->customer;
			$data["due_date"]    	 = $this->date->dateFormat($retrieved_data["header"]->duedate);
			$data["transactiondate"] = $this->date->dateFormat($retrieved_data["header"]->transactiondate);
			$data['remarks'] 		 = '';

			//Footer Data
			$data['t_subtotal'] 	 = $retrieved_data['header']->amount;
			$data['t_discount'] 	 = $retrieved_data['header']->discountamount;
			$data['t_total'] 	 	 = $retrieved_data['header']->netamount;

			$discounttype 		 	 = $retrieved_data['header']->discounttype;
			$data['percentage'] 	 = "";
			$data['h_disctype'] 	 = $discounttype;

			//Vendor Data
			$data["terms"] 		 	 = $retrieved_data["customer"]->terms;
			$data["tinno"] 		 	 = $retrieved_data["customer"]->tinno;
			$data["address1"] 		 = $retrieved_data["customer"]->address1;
			
			//Details
			$data['details'] 		 = $retrieved_data['details'];
		}
	
		$data['ui'] 			= $this->ui;
		$data['show_input'] 	= true;		
		$data['task'] 			= "create";
		$data['ajax_post'] 		= "voucher=".$data["generated_id"];
		$data["row_ctr"] 		= 0;
		$data['cmp'] 			= $this->companycode;
		
		if( $save_status == "final" || $save_status == "final_preview" || $save_status == "final_new" )
		{
			$voucherno 				= $this->input->post('h_voucher_no');	

			$isExist 				= $this->so->getValue("salesorder", array("voucherno"), "voucherno = '$voucherno'");
			
			$retrieved_data 		= $this->so->retrieveExistingSO($voucherno);
			
			/** RETRIEVE DATA **/

			//Header Data
			$data["voucherno"]       = $retrieved_data["header"]->voucherno;
			$data["customer"]      	 = $retrieved_data["header"]->customer;
			$data["due_date"]    	 = $this->date->dateFormat($retrieved_data["header"]->duedate);
			$data["transactiondate"] = $this->date->dateFormat($retrieved_data["header"]->transactiondate);

			//Footer Data
			$data['t_subtotal'] 	 = $retrieved_data['header']->amount;
			$data['t_discount'] 	 = $retrieved_data['header']->discountamount;
			$data['t_total'] 	 	 = $retrieved_data['header']->netamount;

			$discounttype 		 	 = $retrieved_data['header']->discounttype;
			$data['percentage'] 	 = "";
			$data['h_disctype'] 	 = $discounttype;

			//Vendor Data
			$data["terms"] 		 	 = $retrieved_data["customer"]->terms;
			$data["tinno"] 		 	 = $retrieved_data["customer"]->tinno;
			$data["address1"] 		 = $retrieved_data["customer"]->address1;
			
			//Details
			$data['details'] 		 = $retrieved_data['details'];

			if($isExist[0]->voucherno)
			{
				/**UPDATE TABLES FOR FINAL SAVING**/
				$generatedvoucher			= $this->seq->getValue('SO'); 
			
				$update_info				= array();
				$update_info['voucherno']	= $generatedvoucher;
				$update_info['stat']		= 'open';
				$update_condition			= "voucherno = '$voucherno'";
				$updateTempRecord			= $this->so->updateData($update_info,"salesorder",$update_condition);
				$updateTempRecord			= $this->so->updateData($update_info,"salesorder_details",$update_condition);

				if( $updateTempRecord )
				{
					$this->log->saveActivity("Created Sales Order [$generatedvoucher] ");

					if ( $this->inventory_model ) {
						$this->inventory_model->generateBalanceTable();
					}
				}

				if( $quotation_no != "" )
				{
					$update_info				= array();
					$update_info['stat']		= 'locked';
					$update_condition			= "voucherno = '$quotation_no'";
					$updateTempRecord			= $this->so->updateData($update_info,"salesquotation",$update_condition);
					$updateTempRecord			= $this->so->updateData($update_info,"salesquotation_details",$update_condition);

					if( $updateTempRecord )
					{
						$this->log->saveActivity("Converted Sales Quotation [$quotation_no] ");
						
						if ( $this->inventory_model ) {
							$this->inventory_model->generateBalanceTable();
						}
					}
				}

				if( $updateTempRecord && $save_status == 'final' )
				{
					$this->url->redirect(BASE_URL . 'sales/sales_order');
				}
				else if( $updateTempRecord && $save_status == 'final_preview' )
				{
					$this->url->redirect(BASE_URL . 'sales/sales_order/view/'.$generatedvoucher);
				}
				else if( $updateTempRecord && $save_status == 'final_new' )
				{
					$this->url->redirect(BASE_URL . 'sales/sales_order/create');
				}
			}

		}

		$this->view->load('sales_order/sales_order', $data);
	}

	public function edit($voucherno)
	{
		$retrieved_data 		= $this->so->retrieveExistingSO($voucherno);

		// Item Limit
		$item_limit 			= $this->so->getReference("so_limit");
		$data['item_limit']		= ($item_limit[0]->value) 	? 	$item_limit[0]->value 	: 	50; 
		
		// Closed Date
		$close_date 			= $this->restrict->getClosedDate();
		$data['close_date']		= $close_date;

		$data['customer_list'] 	= $this->so->retrieveCustomerList();
		$data['proforma_list'] 	= $this->so->retrieveProformaList();
		$data["business_type"] 	= $this->so->getOption("businesstype");
		$data["vat_type"] 		= $this->so->getOption("vat_type");
		$data["tax_codes"] 		= $this->so->getTaxCode('VAT');

		$curr_type_data         = array("currencycode ind", "currency val");
		$data["currency_codes"] = $this->so->getValue("currency", $curr_type_data,'','currencycode');

		$cc_entry_data          = array("itemcode ind","CONCAT(itemcode,' - ',itemname) val");
		$data["itemcodes"] 		= $this->so->getValue("items", $cc_entry_data,'',"itemcode");

		$w_entry_data          = array("warehousecode ind","warehousecode val");
		$data["warehouses"] 	= $this->so->getValue("warehouse", $w_entry_data,'',"warehousecode");

		$acc_entry_data          = array("accountname ind","CONCAT(segment5,' - ', accountname )  val");
		$acc_entry_cond          = "accounttype != 'P'";
		$data["account_entries"] = $this->so->getValue("chartaccount", $acc_entry_data,$acc_entry_cond, "segment5");

		/**ADD NEW CUSTOMER**/
		
		// Retrieve business type list
		$bus_type_data                = array("code ind", "value val");
		$bus_type_cond                = "type = 'businesstype'";
		$data["business_type_list"]   = $this->so->getValue("wc_option", $bus_type_data, $bus_type_cond, false);

		$data["generated_id"] 	= $voucherno;
		$data["sid"] 		    = $voucherno;

		$data["quotation_no"]  	= ""; 

		$data['ui'] 			= $this->ui;
		$data['show_input'] 	= true;
		$data['ajax_post'] 		= "&voucher=$voucherno";
		$data['task'] 			= "edit";
		$data["row_ctr"] 		= 0;
		$data['cmp'] 			= $this->companycode;

		// Header Data
		$data["voucherno"]       = $retrieved_data["header"]->voucherno;
		$data["customer"]      	 = $retrieved_data["header"]->customer;
		$data["due_date"]    	 = $this->date->dateFormat($retrieved_data["header"]->duedate);
		$data["transactiondate"] = $this->date->dateFormat($retrieved_data["header"]->transactiondate);
		$data['remarks'] 		 = $retrieved_data["header"]->remarks;
		
		//Footer Data
		$data['t_subtotal'] 	 = $retrieved_data['header']->amount;
		$data['t_discount'] 	 = $retrieved_data['header']->discountamount;
		$data['t_total'] 	 	 = $retrieved_data['header']->netamount;
		$data['t_vat'] 			 = $retrieved_data['header']->taxamount;
		$data['t_vatsales'] 	 = $retrieved_data['header']->vat_sales;
		$data['t_vatexempt'] 	 = $retrieved_data['header']->vat_exempt;

		$discounttype 		 	 = $retrieved_data['header']->discounttype;
		$data['percentage'] 	 = "";
		$data['h_disctype'] 	 = $discounttype;

		//Vendor Data
		$data["terms"] 		 	 = $retrieved_data["customer"]->terms;
		$data["tinno"] 		 	 = $retrieved_data["customer"]->tinno;
		$data["address1"] 		 = $retrieved_data["customer"]->address1;
		
		//Details
		$data['details'] 		 = $retrieved_data['details'];
		$data['restrict_so'] 	 = false;

		$this->view->load('sales_order/sales_order', $data);
	}

	public function view($voucherno)
	{
		$retrieved_data 		= $this->so->retrieveExistingSO($voucherno);

		// Closed Date
		$close_date 			= $this->restrict->getClosedDate();
		$data['close_date']		= $close_date;
		
		$data['customer_list'] 	= $this->so->retrieveCustomerList();
		$data['proforma_list'] 	= $this->so->retrieveProformaList();
		$data["business_type"] 	= $this->so->getOption("businesstype");
		$data["vat_type"] 		= $this->so->getOption("vat_type");
		$data["tax_codes"] 		= $this->so->getTaxCode('VAT');

		$curr_type_data         = array("currencycode ind", "currency val");
		$data["currency_codes"] = $this->so->getValue("currency", $curr_type_data,'','currencycode');

		$cc_entry_data          = array("itemcode ind","CONCAT(itemcode,' - ',itemname) val");
		$data["itemcodes"] 		= $this->so->getValue("items", $cc_entry_data,'',"itemcode");

		$w_entry_data          = array("warehousecode ind","warehousecode val");
		$data["warehouses"] 	= $this->so->getValue("warehouse", $w_entry_data,'',"warehousecode");

		$acc_entry_data          = array("accountname ind","CONCAT(segment5,' - ', accountname )  val");
		$acc_entry_cond          = "accounttype != 'P'";
		$data["account_entries"] = $this->so->getValue("chartaccount", $acc_entry_data,$acc_entry_cond, "segment5");

		$data["generated_id"] 	= $voucherno;
		$data["sid"] 		    = $voucherno;

		$data["quotation_no"]  	= ""; 

		$data['ui'] 			= $this->ui;
		$data['show_input'] 	= false;
		$data['ajax_post'] 		= "";
		$data['task'] 			= "view";
		$data["row_ctr"] 		= 0;
		$data['cmp'] 			= $this->companycode;

		$transactiondate 		= $retrieved_data["header"]->transactiondate;
		$duedate 				= $retrieved_data["header"]->duedate;
		
		// Header Data
		$data["voucherno"]       = $retrieved_data["header"]->voucherno;
		$data["customer"]      	 = $retrieved_data["header"]->partnername;
		$data["due_date"]    	 = $this->date->dateFormat($duedate);
		$data["transactiondate"] = $this->date->dateFormat($transactiondate);
		$data['remarks'] 		 = $retrieved_data["header"]->remarks;
		$data['stat'] 			 = $retrieved_data['header']->stat;

		//Footer Data
		$data['t_subtotal'] 	 = $retrieved_data['header']->amount;
		$data['t_discount'] 	 = $retrieved_data['header']->discountamount;
		$data['t_total'] 	 	 = $retrieved_data['header']->netamount;
		$data['t_vat'] 			 = $retrieved_data['header']->taxamount;
		$data['t_vatsales'] 	 = $retrieved_data['header']->vat_sales;
		$data['t_vatexempt'] 	 = $retrieved_data['header']->vat_exempt;
		
		$discountamount 		 = $retrieved_data['header']->discountamount;
		$discounttype 		 	 = $retrieved_data['header']->discounttype;
		$data['percentage'] 	 = ($discounttype == 'perc' && $discountamount > 0 ) 	? 	"%" 	: 	"";
		$data['h_disctype'] 	 = $discounttype;

		//Vendor Data
		$data["terms"] 		 	 = $retrieved_data["customer"]->terms;
		$data["tinno"] 		 	 = $retrieved_data["customer"]->tinno;
		$data["address1"] 		 = $retrieved_data["customer"]->address1;
		
		//Details
		$data['details'] 		 = $retrieved_data['details'];
		
		$restrict_so 			 =	$this->restrict->setButtonRestriction($transactiondate);
		$data['restrict_so'] 	= $restrict_so;

		// Retrieve business type list
		$bus_type_data                = array("code ind", "value val");
		$bus_type_cond                = "type = 'businesstype'";
		$data["business_type_list"]   = $this->so->getValue("wc_option", $bus_type_data, $bus_type_cond, false);

		$this->view->load('sales_order/sales_order', $data);
	}

	public function tag_as_complete($voucherno)
	{
		$data['stat'] 			=	'posted';
		$data['postingdate'] 	=	$this->date->datetimeDbFormat();
		$data['postedby'] 		=	$this->user;

		$result 	=	$this->so->updateData($data, "salesorder", " voucherno = '$voucherno' AND stat != 'posted' ");

		if( $result )
		{
			$result 	=	$this->so->updateData($data, "salesorder_details", " voucherno = '$voucherno' AND stat != 'posted' ");

			if( $result )
			{
				$this->log->saveActivity("Tagged as Complete - Sales Order [$voucherno] ");

				if ( $this->inventory_model ) {
					$this->inventory_model->generateBalanceTable();
				}

				$this->url->redirect(BASE_URL . 'sales/sales_order');
			}
		}

		//$this->view->load('sales_order/sales_order', $data);

	}

	public function print_preview($voucherno) 
	{
		$companycode = $this->companycode;
		
		/** OUTLINE CHECKER **/
			$outline 		=	$this->so->getValue("wc_reference", array('value')," code = 'so_outline'");
			$outline_ 		=	$outline[0]->value;

		/** HEADER INFO **/

			$docinfo_table  = "salesorder as so";
			$docinfo_fields = array('so.transactiondate AS documentdate','so.voucherno AS voucherno',"p.partnername AS company","CONCAT( p.first_name, ' ', p.last_name ) AS customer","'' AS referenceno",'so.amount AS amount','so.remarks as remarks','so.discounttype as disctype','so.discountamount as discount', 'so.netamount as net','so.amount as amount','so.vat_sales as vat_sales','so.vat_exempt as vat_exempt','so.taxamount as vat');
			$docinfo_join   = "partners as p ON p.partnercode = so.customer AND p.companycode = so.companycode";
			$docinfo_cond 	= "so.voucherno = '$voucherno'";

			$documentinfo  	= $this->so->retrieveData($docinfo_table, $docinfo_fields, $docinfo_cond, $docinfo_join);
			$documentinfo	= $documentinfo[0]; 
			$customer 	    = $documentinfo->customer;

		/** HEADER INFO - END**/

		/** DETAILS INFO **/

			$docdet_table   = "salesorder_details as dtl";
			$docdet_fields  = array("dtl.itemcode as itemcode", "dtl.detailparticular as description", "dtl.issueqty as quantity","UPPER(dtl.issueuom) uom","unitprice as price","amount as amount");
			//$docdet_fields  = array("dtl.itemcode as itemcode","dtl.issueqty as quantity", "dtl.detailparticular as description", "unitprice as price","amount as amount");
			$docdet_cond    = "dtl.voucherno = '$voucherno'";
			$docdet_join 	= "";
			$docdet_groupby = "";
			$docdet_orderby = "dtl.linenum";
			
			$documentcontent = $this->so->retrieveData($docdet_table, $docdet_fields, $docdet_cond, $docdet_join, $docdet_orderby, $docdet_groupby);

		/** DETAILS INFO --END**/
		
		/** CUSTOMER DETAILS **/

			$customercode 		=	$this->so->getValue("salesorder", array('customer')," voucherno = '$voucherno'");

			$custField			= array('partnername customer', 'address1 address', 'tinno', 'terms', 'mobile contactno');
			$customerdetails	= $this->so->retrieveData("partners",$custField," partnertype = 'customer' AND partnercode = '".$customercode[0]->customer."'");
			$customerdetails	= $customerdetails[0];

		/** CUSTOMER DETAILS --END**/

		$documentdetails	= array(
			'Date'	=> $this->date->dateFormat($documentinfo->documentdate),
			'SO #'	=> $voucherno
		);

		$print = new sales_print_model();
		$print->setDocumentType('Sales Order')
				->setFooterDetails(array('Approved By', 'Checked By'))
				->setCustomerDetails($customerdetails)
				->setDocumentDetails($documentdetails)
				// ->addTermsAndCondition()
				->addReceived();

		$print->setHeaderWidth(array(40, 60, 20, 20, 30, 30))
				->setHeaderAlign(array('C', 'C', 'C', 'C', 'C', 'C'))
				->setHeader(array('Item Code', 'Description', 'Quantity', 'UOM', 'Price', 'Amount'))
				->setRowAlign(array('L', 'L', 'R', 'L', 'R', 'R'))
				->setSummaryWidth(array('170', '30'));

		$detail_height = 37;

		$total_amount = 0;
		foreach ($documentcontent as $key => $row) {
			if ($key % $detail_height == 0) {
				$print->drawHeader();
			}

			$total_amount	+= $row->amount;
			$row->quantity	= number_format($row->quantity);
			$row->price		= number_format($row->price, 2);
			$row->amount	= number_format($row->amount, 2);
			$print->addRow($row);
			if (($key + 1) % $detail_height == 0) {
				$print->drawSummary(array('Total Amount' => number_format($total_amount, 2)));
				$total_amount = 0;
			}
		}
		$print->drawSummary(array('Total Amount' => number_format($total_amount, 2)));

		$print->drawPDF('Sales Order - ' . $voucherno);
	}

	public function ajax($task)
	{
		header('Content-type: application/json');
		$result 	=	"";

		if ($task == 'save_temp_data' || $task == 'create') 
		{
			$result = $this->add();
		}
		else if ($task == 'edit') 
		{
			$result = $this->update();
		}
		else if($task == 'cancel')
		{
			$result = $this->cancel();
		}
		else if($task == 'so_listing') 
		{
			$result = $this->so_listing();
		}
		else if($task == 'get_value') 
		{
			$result = $this->get_value();
		}
		else if($task == 'save_customer')
		{
			$result = $this->save_customer_data();
		}
		else if( $task == 'get_item_details' )
		{
			$result = $this->get_details();
		}
		else if( $task == 'delete_so' )
		{
			$result = $this->delete_so();
		}
		else if( $task == 'delete_row' )
		{
			$result = $this->delete_row();
		}
		else if( $task == 'get_duplicate' )
		{
			$result = $this->get_duplicate();
		}

		echo json_encode($result); 
	}

	private function get_details()
	{
		$itemcode 	= $this->input->post('itemcode');
		$customer 	= $this->input->post('customer');

		$result 	= $this->so->retrieveItemDetails($itemcode,$customer);
		
		return $result;
	}

	private function get_value()
	{
		$data_cond = $this->input->post("event");
		
		if($data_cond == "getPartnerInfo")
		{
			$data_var = array('address1', "tinno", "terms");
			$code 	  = $this->input->post("code");

			$cond 	  = "partnercode = '$code'";

			$result = $this->so->getValue("partners", $data_var, $cond);

			$dataArray = array( "address" => $result[0]->address1, "tinno" => $result[0]->tinno, "terms" => $result[0]->terms );
		}
		else if($data_cond == "getTaxRate")
		{
			$data_var = array("taxrate");

			$taxcode  = $this->input->post("taxcode");

			$cond 	  = " fstaxcode = '$taxcode' ";

			$result = $this->invoice->getValue("fintaxcode", $data_var, $cond);

			$dataArray = array("taxrate" => $result[0]->taxrate);

		}
		
		return $dataArray;
	}

	private function get_duplicate(){
		$input = $this->input->post('code');

		$result = $this->so->check_duplicate($input);

		$count = $result[0]->count;
		
		$msg   = "";

		if( $count > 0 )
		{	
			$msg = "exists";
		}

		return $dataArray = array("msg" => $msg);
	}
	
	private function add($final="")
	{
		$data_post 	= $this->input->post();
		$voucher 	= $this->input->post("h_voucher_no");

		if( $final == "save" )
			$result    = $this->so->processTransaction($data_post, "create" , $voucher);
		else
			$result    = $this->so->processTransaction($data_post, "create");

		if(!empty($result))
		{
			$msg = $result;
		}
		else
		{
			$msg = "success";

			if ( $this->inventory_model ) {
				$this->inventory_model->generateBalanceTable();
			}
		}

		return $dataArray = array("msg" => $msg);
	}
	
	private function update()
	{
		$data_post 	= $this->input->post();

		$voucher 	= $this->input->post("voucher");

		/**
		* Update Database
		*/
		$result = $this->so->processTransaction($data_post, "edit", $voucher);

		if(!empty($result))
		{
			$msg = $result;
		}
		else
		{
			$this->log->saveActivity("Edited Sales Order [$voucher] ");

			if ( $this->inventory_model ) {
				$this->inventory_model->generateBalanceTable();
			}

			$msg = "success";
		}

		return $dataArray = array( "msg" => $msg, "voucher" => $voucher );
	}

	private function cancel()
	{
		$voucher 	= 	$this->input->post("voucher");

		$result 	=	$this->so->delete_temp_transactions($voucher, "salesorder", "salesorder_details");

		if($result == '1')
			$msg = "success";
		else
			$msg = "Failed to Cancel.";

		return $dataArray = array( "msg" => $msg );
	}

	private function delete_so()
	{
		$vouchers 		=	$this->input->post('delete_id');
		$so_vouchers 	= 	"'" . implode("','", $vouchers) . "'";
		
		$data['stat'] 	=	"cancelled";
		
		$cond 			=	" voucherno IN ($so_vouchers) ";

		$result 	=	$this->so->updateData($data, "salesorder", $cond);

		if( $result )
		{
			$result 	=	$this->so->updateData($data, "salesorder_details", $cond);
		}

		if($result == '1')
		{
			$this->log->saveActivity("Cancelled Sales Order [ ". implode(',',$vouchers) ." ] ");
			$msg = "success";
		
			if ( $this->inventory_model ) {
				$this->inventory_model->generateBalanceTable();
			}
		}
		else
		{
			$msg = "Failed to Cancel.";
		}

		return $dataArray = array( "msg" => $msg );
	}

	private function delete_row()
	{
		$table 		= $this->input->post('table');
		$linenum	= $this->input->post('linenum'); 
		$voucher 	= $this->input->post('voucherno');

		$cond 		= " voucherno = '$voucher' AND linenum = '$linenum' ";

		$result = $this->so->deleteData($table, $cond);

		if($result)
			$msg = "success";
		else
			$msg = "The system has encountered an error in cancelling. Please contact admin to fix this issue.";

		return	$dataArray = array( "msg" => $msg );
	}

	private function save_customer_data()
	{
		$result    	= "";

		$data_var  	= array("partnercode","partnername", "first_name", "last_name", "email", "address1", "businesstype", "tinno", "terms");
		
		$data_post 	= $this->input->post($data_var);

		$partnercode 	=	$this->input->post('partnercode');
			
		$result 	= $this->so->insertCustomer($data_post);

		if($result)
		{
			$this->log->saveActivity("Sales Order - Added New Customer [$partnercode] ");
			$msg = "success";
		}
		else
		{
			$msg = "The system has encountered an error in saving. Please contact admin to fix this issue.";
		}

		return $dataArray = array( "msg" => $msg );
	
	}

	private function so_listing()
	{
		$posted_data 	= $this->input->post(array("daterangefilter", "customer", "search", "filter", "sort", "limit"));
		$pagination 	= $this->so->retrieveListing($posted_data);

		$table 	= '';
		
		if( !empty($pagination->result) ) :
			foreach ($pagination->result as $key => $row) {

				$transactiondate 	=	$row->transactiondate;

				$voucher_status = 	"";
				if($row->stat == 'open')
				{
					$voucher_status = '<span class="label label-warning">PENDING</span>';
				}
				else if($row->stat == 'partial')
				{
					$voucher_status = '<span class="label label-info">PARTIAL</span>';
				}
				else if($row->stat == 'posted')
				{
					$voucher_status = '<span class="label label-success">COMPLETE</span>';
				}
				else if($row->stat == 'closed')
				{
					$voucher_status = '<span class="label label-default">CLOSED</span>';
				}
				else if($row->stat == 'cancelled')
				{
					$voucher_status = '<span class="label label-danger">CANCELLED</span>';
				}
				
				$restrict_so =	$this->restrict->setButtonRestriction($transactiondate);

				$element 	= 	$this->ui->loadElement('check_task')
									->addView()
									->addEdit(($row->stat == 'open' && !$restrict_so))
									->addOtherTask('Tag as Complete', 'bookmark',($row->stat != 'closed' && $row->stat != 'posted' && $row->stat != 'open' && $row->stat != 'cancelled'))
									->addPrint()
									->addDelete(($row->stat == 'open' && !$restrict_so))
									// ->setClosed()
									->setValue($row->voucherno)
									->setLabels(array('delete'=>'Cancel'));

				if ($row->stat == 'open') {
					$element->addCheckbox();
				}

				$dropdown = $element->draw();
				
				$table .= '<tr>';
				$table .= '<td class="text-center">' . $dropdown . '</td>';
				$table .= '<td>' . $this->date->dateFormat($transactiondate) . '</td>';
				$table .= '<td>' . $row->voucherno. '</td>';
				// $table .= '<td>' . $row->quotation_no. '</td>';
				$table .= '<td>' . $row->partnername . '</td>';
				$table .= '<td class="text-right">' . number_format($row->netamount,2) . '</td>';
				$table .= '<td>' . $voucher_status . '</td>';
				$table .= '</tr>';
			}
		else:
			$table .= "<tr>
							<td colspan = '7' class = 'text-center'>No Records Found</td>
					</tr>";
		endif;
		$pagination->table 	= $table;
		$pagination->csv 	= $this->export_main();
		return $pagination;
	}

	private function export_main(){
		$data 	= $this->input->post(array("daterangefilter", "customer", "search", "filter", "sort", "limit"));
		
		$header = array("Date","SO No.","Quotation No.","Customer","Amount");

		$csv = '';
		$csv .= '"' . implode('", "', $header) . '"';
		$csv .= "\n";
		
		$result = $this->so->export_main($data);

		$totalamount 	=	0;

		if (!empty($result)){
			foreach ($result as $key => $row){

				$totalamount += $row->netamount;

				$csv .= '"' . $row->transactiondate . '",';
				$csv .= '"' . $row->voucherno . '",';
				$csv .= '"' . $row->quotation_no . '",';
				$csv .= '"' . $row->partnername . '",';
				$csv .= '"' . number_format($row->netamount,2) . '"';
				$csv .= "\n";
			}
		}
		
		$csv .= '"","","","Total ","'. number_format($totalamount,2) .'"';
		return $csv;
	}
}
?>