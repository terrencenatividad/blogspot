<?php
class controller extends wc_controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->url 			    = new url();
		$this->po 				= new purchase_order();
		$this->seq 				= new seqcontrol();
		$this->input            = new input();
		$this->ui 			    = new ui();
		$this->log 				= new log();
		$this->restrict 		= new purchase_restriction_model();
		$this->view->title      = 'Purchase Order';
		$this->show_input 	    = true;
		$this->inventory_model	= $this->checkoutModel('inventory_module/inventory_model');
		$this->report_model		= $this->checkoutModel('reports_module/report_model');

		$this->user 		    = USERNAME;

		$session                = new session();
		$this->companycode      = $session->get('companycode');
		$this->view->header_active = 'purchase/purchase_order/';

		$this->fields = array(
			'voucherno',
			'vendor',
			'referenceno',
			'tinno',
			'address1',
			'terms',
			'transactiondate',
			'department'
		);
		
		$this->inventory_model  = $this->checkoutModel('inventory_module/inventory_model');
	}

	public function listing()
	{
		$data['ui'] 				= $this->ui;
		
		$data['show_input'] 		= true;

		$data['date_today'] 		= date("M d, Y");
		
		$data['vendor_list'] 		= $this->po->retrieveVendorList();
		
		$data["payment_mode_list"] 	= $this->po->getOption("payment_type");
		$data["cash_account_codes"] = $this->po->retrieveCashAccountClassList();

		$this->view->load('purchase_order/purchase_orderlist', $data);
	}

	public function create($request_no="")
	{
		$this->view->title      = 'Create Purchase Order';
		$data 					= $this->input->post($this->fields);

		// Item Limit
		$item_limit 			= $this->po->getReference("po_limit");
		$data['item_limit']		= ($item_limit[0]->value) 	? 	$item_limit[0]->value 	: 	50; 

		// Closed Date
		$close_date 			= $this->restrict->getClosedDate();
		$data['close_date']		= $close_date;
		$data["taxrate_list"]		= $this->po->getTaxRateList();
		$data["wtaxcode_list"]		= $this->po->getWTaxCodeList();

		$data['vendor_list'] 	= $this->po->retrieveVendorList();
		$data['proforma_list'] 	= $this->po->retrieveProformaList();
		$data['budget_list'] = $this->po->getBudgetCodes();

		$data["business_type"] 	= $this->po->getOption("businesstype");
		$data["vat_type"] 		= $this->po->getOption("vat_type");
		$data["tax_codes"] 		= $this->po->getTaxCode('VAT',"fstaxcode ind, shortname val");
		$data["wtax_codes"] 	= $this->po->getTaxCode('WTX',"fstaxcode ind, shortname val");
		$data['percentage'] 	= "";
		$data['remarks'] 		= "";
		$data['h_disctype'] 	= "perc";

		$data["h_request_no"] 	= "";

		$curr_type_data         = array("currencycode ind", "currency val");
		$data["currency_codes"] = $this->po->getValue("currency", $curr_type_data,'','currencycode');

		$w_entry_data          = array("warehousecode ind","description val");
		$data["warehouses"] 	= $this->po->getValue("warehouse", $w_entry_data,"stat = 'active'","warehousecode");

		$cc_entry_data          = array("itemcode ind","CONCAT(itemcode,' - ',itemname) val");
		$data["itemcodes"] 		= $this->po->getValue("items", $cc_entry_data,"stat = 'active'","itemcode");

		$acc_entry_data         = array("accountname ind","CONCAT(segment5,' - ', accountname )  val");
		$acc_entry_cond         = "accounttype != 'P'";
		$data["account_entries"]= $this->po->getValue("chartaccount", $acc_entry_data,$acc_entry_cond, "segment5");

		$data['transactiondate'] = $this->date->dateFormat();

		/**ADD NEW VENDOR**/
		
		// Retrieve business type list
		$bus_type_data                = array("code ind", "value val");
		$bus_type_cond                = "type = 'businesstype'";
		$data["business_type_list"]   = $this->po->getValue("wc_option", $bus_type_data, $bus_type_cond, false);

		// Retrieve generated ID
		$gen_value               = $this->po->getValue("purchaseorder", "COUNT(*) as count", "voucherno != ''");
		$data["generated_id"]    = (!empty($gen_value[0]->count)) ? 'TMP_'.($gen_value[0]->count + 1) : 'TMP_1';

		/** RETRIEVAL FOR QUOTATION / PO **/

		if( $request_no != "" )
		{
			$retrieved_data 		= $this->po->retrieveExistingPQ($request_no);

			$data["request_no"]  	= $retrieved_data["header"]->voucherno;
			$data['department'] 	= $retrieved_data['header']->department;
			$data["referenceno"]  	= $retrieved_data["header"]->voucherno;
			/** RETRIEVE DATA **/
			//Header Data
			//$data["vendor"]      	 = $retrieved_data["header"]->requestor;
			//$data["due_date"]    	 = date('M d,Y', strtotime($retrieved_data["header"]->duedate));
			$data["transactiondate"] = date('M d,Y', strtotime($retrieved_data["header"]->transactiondate));

			//Footer Data
			$data['t_subtotal'] 	 = $retrieved_data['header']->amount;
			$data['t_discount'] 	 = $retrieved_data['header']->discountamount;
			$data['t_total'] 	 	 = $retrieved_data['header']->netamount;

			$discounttype 		 	 = $retrieved_data['header']->discounttype;
			$data['percentage'] 	 = "";
			$data['h_disctype'] 	 = $discounttype;
			$data['t_vat'] 			 = $retrieved_data['header']->taxamount;
			$data['t_wtax'] 		 = $retrieved_data['header']->wtaxamount;
			$data['t_wtaxcode'] 	 = $retrieved_data['header']->wtaxcode;
			$data['t_wtaxrate'] 	 = $retrieved_data['header']->wtaxrate;

			//Vendor Data
			$data["terms"] 		 	 = '';
			$data["tinno"] 		 	 = '';
			$data["address1"] 		 = '';
			
			//Details
			$data['details'] 		 = $retrieved_data['details'];
		}
		else
		{
			$data["request_no"] = "";
		}

		$data['ui'] 			= $this->ui;
		$data['show_input'] 	= true;		
		$data['ajax_post'] 		= "voucher=".$data["generated_id"];
		$data['task'] 			= "create";
		$data["row_ctr"] 		= 0;
		$data['cmp'] 			= $this->companycode;
		$data['restrict_po'] 	= false;
		//Finalize Saving	
		$save_status 			= $this->input->post('save');
		//echo $save_status;
		if( $save_status == "final" || $save_status == "final_preview" || $save_status == "final_new" )
		{
			$voucherno 				= $this->input->post('h_voucher_no');	
			$isExist 				= $this->po->getValue("purchaseorder", array("voucherno"), "voucherno = '$voucherno'");
			// var_dump($isExist);
			if($isExist[0]->voucherno)
			{
				/** RETRIEVE DATA **/
				$retrieved_data 		= $this->po->retrieveExistingPO($voucherno);
				
				$data["voucherno"]       = $retrieved_data["header"]->voucherno;
				$data["vendor"]      	 = $retrieved_data["header"]->vendor;
				$data['referenceno'] 	 = $retrieved_data['header']->referenceno;
				$data['department'] 	 = $retrieved_data['header']->department;
				$data['remarks'] 	 	 = $retrieved_data['header']->remarks;

				$data["transactiondate"] = date('M d,Y', strtotime($retrieved_data["header"]->transactiondate));
				
				//Footer Data
				$data['t_subtotal'] 	 = $retrieved_data['header']->amount;
				$data['t_discount'] 	 = $retrieved_data['header']->discountamount;
				$data['t_total'] 	 	 = $retrieved_data['header']->netamount;
				$data['t_vat'] 			 = $retrieved_data['header']->taxamount;
				$data['t_wtax'] 		 = $retrieved_data['header']->wtaxamount;
				$data['t_wtaxcode'] 	 = $retrieved_data['header']->wtaxcode;
				$data['t_wtaxrate'] 	 = $retrieved_data['header']->wtaxrate;
				$data['s_atc_code'] 	 = $retrieved_data['header']->atcCode;

				$discounttype 		 	 = $retrieved_data['header']->discounttype;
				$data['percentage'] 	 = "";
				$data['h_disctype'] 	 = $discounttype;

				//Vendor Data
				$data["terms"] 		 	 = $retrieved_data["vendor"]->terms;
				$data["tinno"] 		 	 = $retrieved_data["vendor"]->tinno;
				$data["address1"] 		 = $retrieved_data["vendor"]->address1;
				
				//Details
				$data['details'] 		 = $retrieved_data['details'];

				/**UPDATE TABLES FOR FINAL SAVING**/
				$generatedvoucher			= $this->seq->getValue('PO', $this->companycode); 
				
				$update_info				= array();
				$fields = array();
				$update_info['voucherno']	= $generatedvoucher;
				$update_info['stat']		= 'open';
				$update_condition			= "voucherno = '$voucherno'";
				$fields['voucherno'] = $update_info['voucherno'];
				$updateTempRecord			= $this->po->updateData($update_info,"purchaseorder",$update_condition);
				$updateTempRecord = $this->po->updateActual($fields, $voucherno);
				$updateTempRecord			= $this->po->updateData($update_info,"purchaseorder_details",$update_condition);

				$this->log->saveActivity("Created Purchase Order [$generatedvoucher] ");

				if( $request_no != "" )
				{
					$update_info				= array();
					$update_info['stat']		= 'locked';
					$update_condition			= "voucherno = '$request_no'";
					$updateTempRecord			= $this->po->updateData($update_info,"purchaserequest",$update_condition);
					$updateTempRecord			= $this->po->updateData($update_info,"purchaserequest_details",$update_condition);

					$this->log->saveActivity("Converted Purchase Request [$request_no] ");
				}

				if ( $this->inventory_model ) {
					$this->inventory_model->generateBalanceTable();
				}

				if( $updateTempRecord && $save_status == 'final' )
				{
					$this->url->redirect(BASE_URL . 'purchase/purchase_order');
				}
				else if( $updateTempRecord && $save_status == 'final_preview' )
				{
					$this->url->redirect(BASE_URL . 'purchase/purchase_order/view/'.$generatedvoucher);
				}
				else if( $updateTempRecord && $save_status == 'final_new' )
				{
					$this->url->redirect(BASE_URL . 'purchase/purchase_order/create');
				}
			}

		}

		$this->view->load('purchase_order/purchase_order', $data);
	}

	public function edit($voucherno)
	{
		$this->view->title      = 'Edit Purchase Order';
		$retrieved_data 		= $this->po->retrieveExistingPO($voucherno);

		// Item Limit
		$item_limit 			= $this->po->getReference("po_limit");
		$data['item_limit']		= ($item_limit[0]->value) 	? 	$item_limit[0]->value 	: 	50; 

		// Closed Date
		$close_date 			= $this->restrict->getClosedDate();
		$data['close_date']		= $close_date;

		$data['vendor_list'] 	= $this->po->retrieveVendorList();
		$data['proforma_list'] 	= $this->po->retrieveProformaList();
		$data["business_type"] 	= $this->po->getOption("businesstype");
		$data["vat_type"] 		= $this->po->getOption("vat_type");
		$data['budget_list'] = $this->po->getBudgetCodes();
		$data["tax_codes"] 		= $this->po->getTaxCode('VAT',"fstaxcode ind, shortname val");
		$data["wtax_codes"] 	= $this->po->getTaxCode('WTX',"fstaxcode ind, shortname val");

		$curr_type_data         = array("currencycode ind", "currency val");
		$data["currency_codes"] = $this->po->getValue("currency", $curr_type_data,'','currencycode');

		$cc_entry_data          = array("itemcode ind","CONCAT(itemcode,' - ',itemname) val");
		$data["itemcodes"] 		= $this->po->getValue("items", $cc_entry_data,"stat = 'active'","itemcode");

		$acc_entry_data          = array("accountname ind","CONCAT(segment5,' - ', accountname )  val");
		$acc_entry_cond          = "accounttype != 'P'";
		$data["account_entries"] = $this->po->getValue("chartaccount", $acc_entry_data,$acc_entry_cond, "segment5");

		/**ADD NEW VENDOR**/
		
		// Retrieve business type list
		$bus_type_data                = array("code ind", "value val");
		$bus_type_cond                = "type = 'businesstype'";
		$data["business_type_list"]   = $this->po->getValue("wc_option", $bus_type_data, $bus_type_cond, false);

		$data["generated_id"] 	= $voucherno;
		$data["sid"] 		    = $voucherno;

		$data["request_no"]  	= ""; 

		$data['ui'] 			= $this->ui;
		$data['show_input'] 	= true;
		$data['ajax_post'] 		= "&voucher=$voucherno";
		$data['task'] 			= "edit";
		$data["row_ctr"] 		= 0;
		$data['cmp'] 			= $this->companycode;

		// Header Data
		$transactiondate 		 = $retrieved_data["header"]->transactiondate;
		$data["voucherno"]       = $retrieved_data["header"]->voucherno;
		$data["vendor"]      	 = $retrieved_data["header"]->vendor;
		$data['department'] 	 = $retrieved_data['header']->department;
		$data['referenceno'] 	 = $retrieved_data['header']->referenceno;
		$data['remarks'] 	 	 = $retrieved_data['header']->remarks;
		$data["transactiondate"] = date('M d,Y', strtotime($transactiondate));
		
		//Footer Data
		$data['t_subtotal'] 	 = $retrieved_data['header']->amount;
		$data['t_discount'] 	 = $retrieved_data['header']->discountamount;
		$data['t_total'] 	 	 = $retrieved_data['header']->netamount;
		$data['t_vat'] 			 = $retrieved_data['header']->taxamount;
		$data['t_wtax'] 		 = $retrieved_data['header']->wtaxamount;
		$data['t_wtaxcode'] 	 = $retrieved_data['header']->wtaxcode;
		$data['t_wtaxrate'] 	 = $retrieved_data['header']->wtaxrate;
		$data['s_atc_code'] 	 = $retrieved_data['header']->atcCode;

		$discounttype 		 	 = $retrieved_data['header']->discounttype;
		$data['percentage'] 	 = "";
		$data['h_disctype'] 	 = $discounttype;

		//Vendor Data
		$data["terms"] 		 	 = $retrieved_data["vendor"]->terms;
		$data["tinno"] 		 	 = $retrieved_data["vendor"]->tinno;
		$data["address1"] 		 = $retrieved_data["vendor"]->address1;
		
		//Details
		$data['details'] 		 = $retrieved_data['details'];

		$wr_array	= array();
		foreach ($data['details'] as $index => $dtl){
			$wh			= $dtl->warehouse;
			$wr_array[]	= $wh;
		}
		$wr_cond = ($wr_array) ? " OR warehousecode IN ('".implode("','",$wr_array)."')" : "";
		
		$w_entry_data          = array("warehousecode ind","description val, w.stat stat");
		$data["warehouses"] 	= $this->po->getValue("warehouse w", $w_entry_data,"stat = 'active' $wr_cond","warehousecode");

		$restrict_po 			 =	$this->restrict->setButtonRestriction($transactiondate);
		$data['restrict_po'] 	 = $restrict_po;
		$this->view->load('purchase_order/purchase_order', $data);
	}

	public function view($voucherno)
	{
		$this->view->title      = 'View Purchase Order';
		$retrieved_data 		= $this->po->retrieveExistingPO($voucherno);
		$close_date 			= $this->restrict->getClosedDate();
		$data['close_date']		= $close_date;
		$item_limit 			= $this->po->getReference("po_limit");
		$data['item_limit']		= ($item_limit[0]->value) 	? 	$item_limit[0]->value 	: 	50; 
		
		$data['vendor_list'] 	= $this->po->retrieveVendorList();
		$data['proforma_list'] 	= $this->po->retrieveProformaList();
		$data['budget_list'] = $this->po->getBudgetCodes();
		$data["business_type"] 	= $this->po->getOption("businesstype");
		$data["vat_type"] 		= $this->po->getOption("vat_type");
		$data["tax_codes"] 		= $this->po->getTaxCode('VAT',"fstaxcode ind, shortname val");
		$data["wtax_codes"] 	= $this->po->getTaxCode('WTX',"fstaxcode ind, shortname val");

		$curr_type_data         = array("currencycode ind", "currency val");
		$data["currency_codes"] = $this->po->getValue("currency", $curr_type_data,'','currencycode');

		$w_entry_data          = array("warehousecode ind","description val");
		$data["warehouses"] 	= $this->po->getValue("warehouse", $w_entry_data,"stat = 'active'","warehousecode");

		$cc_entry_data          = array("itemcode ind","CONCAT(itemcode,' - ',itemname) val");
		$data["itemcodes"] 		= $this->po->getValue("items", $cc_entry_data,'',"itemcode");

		$acc_entry_data          = array("accountname ind","CONCAT(segment5,' - ', accountname )  val");
		$acc_entry_cond          = "accounttype != 'P'";
		$data["account_entries"] = $this->po->getValue("chartaccount", $acc_entry_data,$acc_entry_cond, "segment5");

		// Retrieve business type list
		$bus_type_data                = array("code ind", "value val");
		$bus_type_cond                = "type = 'businesstype'";
		$data["business_type_list"]   = $this->po->getValue("wc_option", $bus_type_data, $bus_type_cond, false);

		$data["generated_id"] 	= $voucherno;
		$data["sid"] 		    = $voucherno;

		$data["request_no"]  	= ""; 
		
		$data['ui'] 			= $this->ui;
		$data['show_input'] 	= false;
		$data['ajax_post'] 		= "";
		$data['task'] 			= "view";
		$data["row_ctr"] 		= 0;
		$data['cmp'] 			= $this->companycode;

		$transactiondate 		= $retrieved_data["header"]->transactiondate;

		// Header Data
		$data["voucherno"]       = $retrieved_data["header"]->voucherno;
		$data["vendor"]      	 = $retrieved_data["header"]->companyname;
		$data['department'] 	 = $retrieved_data['header']->department;
		$data['referenceno'] 	 = $retrieved_data['header']->referenceno;
		$data["transactiondate"] = $this->date->dateFormat($transactiondate);
		$data['stat'] 			 = $retrieved_data["header"]->stat;
		$data['remarks'] 	 	 = $retrieved_data['header']->remarks;

		//Footer Data
		$data['t_subtotal'] 	 = $retrieved_data['header']->amount;
		$data['t_discount'] 	 = $retrieved_data['header']->discountamount;
		$data['t_total'] 	 	 = $retrieved_data['header']->netamount;
		$data['t_vat'] 			 = $retrieved_data['header']->taxamount;
		$data['t_wtax'] 		 = $retrieved_data['header']->wtaxamount;
		$data['t_wtaxcode'] 	 = $retrieved_data['header']->wtaxcode;
		$data['t_wtaxrate'] 	 = $retrieved_data['header']->wtaxrate;
		$data['s_atc_code'] 	 = $retrieved_data['header']->atcCode;

		$discountamount 		 = $retrieved_data['header']->discountamount;
		$discounttype 		 	 = $retrieved_data['header']->discounttype;
		$data['percentage'] 	 = ($discounttype == 'perc' && $discountamount > 0 ) 	? 	"%" 	: 	"";
		$data['h_disctype'] 	 = $discounttype;

		//Vendor Data
		$data["terms"] 		 	 = $retrieved_data["vendor"]->terms;
		$data["tinno"] 		 	 = $retrieved_data["vendor"]->tinno;
		$data["address1"] 		 = $retrieved_data["vendor"]->address1;
		
		//Details
		$data['details'] 		 = $retrieved_data['details'];

		$restrict_po 			 =	$this->restrict->setButtonRestriction($transactiondate);
		$data['restrict_po'] 	 = $restrict_po;

		if ($data['stat'] == 'posted') {
			$cancelled_items			= $this->po->getUnReceivedItems($data["voucherno"]);
			$data['cancelled_items']	= $cancelled_items;
			if ($cancelled_items) {
				$data['received_items'] = $this->po->getReceivedItems($data["voucherno"]);
			}
		}

		$this->view->load('purchase_order/purchase_order', $data);
	}

	public function tag_as_complete($voucherno)
	{
		$data['stat'] 			=	'posted';
		$data['postingdate'] 	=	$this->date->datetimeDbFormat();
		$data['postedby'] 		=	$this->user;

		$result 	=	$this->po->updateData($data, "purchaseorder", " voucherno = '$voucherno' AND stat != 'posted' ");

		if( $result )
		{
			$result 	=	$this->po->updateData($data, "purchaseorder_details", " voucherno = '$voucherno' AND stat != 'posted' ");

			if( $result )
			{
				$this->log->saveActivity("Tagged as Complete - Purchase Order [$voucherno] ");

				if ( $this->inventory_model ) {
					$this->inventory_model->generateBalanceTable();
				}

				$this->url->redirect(BASE_URL . 'purchase/purchase_order');
			}
		}

		//$this->view->load('purchase_order/purchase_order', $data);

	}

	public function print_preview($voucherno) 
	{
		$companycode = $this->companycode;
		
		/** HEADER INFO **/

		$docinfo_table  = "purchaseorder as po";
		$docinfo_fields = array('po.transactiondate AS documentdate','po.voucherno AS voucherno',"p.partnername AS company","CONCAT( p.first_name, ' ', p.last_name ) AS vendor","referenceno AS referenceno",'po.amount AS amount','po.remarks as remarks','po.discounttype as disctype','po.discountamount as discount', 'po.netamount as net','po.amount as amount','po.taxamount as vat', 'po.wtaxamount as wtax','po.wtaxcode as wtaxcode','po.wtaxrate as wtaxrate');
		$docinfo_join   = "partners as p ON p.partnercode = po.vendor AND p.partnertype = 'supplier' AND p.companycode = po.companycode";
		$docinfo_cond 	= "po.voucherno = '$voucherno'";

		$documentinfo  	= $this->po->retrieveData($docinfo_table, $docinfo_fields, $docinfo_cond, $docinfo_join);
		$documentinfo	= $documentinfo[0]; 
		$vendor 	    = $documentinfo->vendor;

		/** HEADER INFO - END**/

		/** DETAILS INFO **/

		$docdet_table   = "purchaseorder_details as dtl";
		$docdet_fields  = array("dtl.itemcode, detailparticular as description", "dtl.receiptqty as quantity","dtl.receiptuom as uom","unitprice as price","amount as amount");
		$docdet_cond    = "dtl.voucherno = '$voucherno'";
		$docdet_join 	= "items i ON i.itemcode = dtl.itemcode AND i.companycode = dtl.companycode";
		$docdet_groupby = "";
		$docdet_orderby = "dtl.linenum";
		
		$documentcontent = $this->po->retrieveData($docdet_table, $docdet_fields, $docdet_cond, $docdet_join, $docdet_orderby, $docdet_groupby);

		/** DETAILS INFO --END**/
		
		/** VENDOR DETAILS **/

		$vendorcode 		=	$this->po->getValue("purchaseorder", array('vendor')," voucherno = '$voucherno'");

		$custField			= array('partnername vendor', 'address1 address', 'tinno', 'terms', 'mobile contactno');
		$vendordetails		= $this->po->retrieveData("partners",$custField," partnertype = 'supplier' AND partnercode = '".$vendorcode[0]->vendor."'");
		$vendordetails	= $vendordetails[0];

		/** VENDOR DETAILS --END**/

		$documentdetails	= array(
			'Date'	=> $this->date->dateFormat($documentinfo->documentdate),
			'PO #'	=> $voucherno,
			'Ref #'	=> $documentinfo->referenceno
		);

		$amount = $documentinfo->amount;
		$vat = $documentinfo->vat;
		$wtaxamount = $documentinfo->wtax;
		$taxcode = $documentinfo->wtaxcode;
		$taxrate = $documentinfo->wtaxrate;
		$netamount = $documentinfo->net;

		$print = new purchase_print_model();
		$print->setDocumentType('Purchase Order')
		->setFooterDetails(array('Prepared By', 'Recommending Approval', 'Approved By'))
		->setVendorDetails($vendordetails)
		->setDocumentDetails($documentdetails)
		->addReceived();

		$print->setHeaderWidth(array(40, 60, 20, 20, 30, 30))
		->setHeaderAlign(array('C', 'C', 'C', 'C', 'C', 'C'))
		->setHeader(array('Item Code', 'Description', 'Qty', 'UOM', 'Price', 'Amount'))
		->setRowAlign(array('L', 'L', 'R', 'L', 'R', 'R'))
		->setSummaryAlign(array('J','R','R', 'R'))	
		->setSummaryWidth(array('120', '50', '30'));

		$detail_height = 37;
		$notes = preg_replace('!\s+!', ' ', $documentinfo->remarks);
		$total_amount = 0;
		foreach ($documentcontent as $key => $row) {
			if ($key % $detail_height == 0) {
				$print->drawHeader();
			}

			$total_amount	+= $row->amount;
			$row->quantity		= number_format($row->quantity);
			$row->price			= number_format($row->price, 2);
			$row->amount		= number_format($row->amount, 2);
			$row->description 	= html_entity_decode(stripslashes($row->description));
			$print->addRow($row);
			if (($key + 1) % $detail_height == 0) {
				$print->drawSummary(array(array('Notes:', 'Total Purchase', number_format($amount, 2)),
											array($notes, 'Total Purchase Tax', number_format($vat, 2)),
											array('', 'Withholding Tax', number_format($wtaxamount, 2)),
											array('', 'Total Amount Due', number_format($netamount, 2)),
											array('', '', '')
				));
				$total_amount = 0;
			}
		}
		$print->drawSummary(array(array('Notes:', 'Total Purchase', number_format($amount, 2)),
											array($notes, 'Total Purchase Tax', number_format($vat, 2)),
											array('', 'Withholding Tax', number_format($wtaxamount, 2)),
											array('', 'Total Amount Due', number_format($netamount, 2)),
											array('', '', '')
		));
		$print->drawPDF('Purchase Order - ' . $voucherno);
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
		else if($task == 'po_listing') 
		{
			$result = $this->po_listing();
		}
		else if($task == 'get_value') 
		{
			$result = $this->get_value();
		}
		else if($task == 'save_vendor')
		{
			$result = $this->save_vendor_data();
		}
		else if( $task == 'get_item_details' )
		{
			$result = $this->get_details('item');
		}
		else if( $task == 'delete_po' )
		{
			$result = $this->delete_po();
		}
		else if( $task == 'delete_row' )
		{
			$result = $this->delete_row();
		}
		else if( $task == 'get_ATC' )
		{
			$result = $this->get_ATC();
		}
		else if( $task == 'get_duplicate' )
		{
			$result = $this->get_duplicate();
		} 
		else if( $task == "checkifitemisinbudget") 
		{
			$result = $this->checkifitemisinbudget();
		}
		else if( $task == "checkifpairexistsinbudget" )
		{
			$result = $this->checkifpairexistsinbudget();
		}


		echo json_encode($result); 
	}

	private function po_listing()
	{
		$posted_data 	= $this->input->post(array("daterangefilter", "vendor", "search", "filter", "sort","limit"));

		// var_dump($this->input->post());

		$pagination 	= $this->po->retrieveListing($posted_data);

		$table 	= '';

		if( !empty($pagination->result) ) :
			foreach ($pagination->result as $key => $row) {

				$vendor 	= $this->po->getValue('partners',array('first_name','last_name')," partnercode = '$row->vendor' ");
				$has_rcpt 	= $this->po->getValue('purchasereceipt',array('COUNT(voucherno) as receipt')," source_no = '$row->voucherno' ");
				
				//$vendor_name 	=	$vendor[0]->first_name . " " . $vendor[0]->last_name;
				$transactiondate 	=	$row->transactiondate;
				$balance 			= 	$row->balance;

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
					$balance	 	= 0;
				}
				else if($row->stat == 'cancelled')
				{
					$voucher_status = '<span class="label label-danger">CANCELLED</span>';
				}

				$restrict_po =	$this->restrict->setButtonRestriction($transactiondate);

				$element = $this->ui->loadElement('check_task')
				->addView()
				->addEdit(($row->stat == 'open' && $restrict_po))
				->addOtherTask('Tag as Complete', 'bookmark',($row->stat != 'closed' && $row->stat != 'posted' && $row->stat != 'open' && $row->stat != 'cancelled'))
				->addPrint()
				->addDelete(($row->stat == 'open' && $restrict_po))
				->setValue($row->voucherno)
				->addCheckbox($row->stat == 'open'&& $restrict_po)
				->setLabels(array('delete'=>'Cancel'));

				$dropdown = $element->draw();

				$table .= '<tr>';
				$table .= '<td class="text-center">' . $dropdown . '</td>';
				$table .= '<td>' . date("M d, Y",strtotime($row->transactiondate)) . '</td>';
				$table .= '<td>' . $row->voucherno. '</td>';
				// $table .= '<td>' . $row->request_no. '</td>';
				$table .= '<td>' . $row->referenceno. '</td>';
				$table .= '<td>' . $row->vendor . '</td>';
				$table .= '<td class = "text-right" >' . number_format($row->netamount,2) . '</td>';
				$table .= '<td class = "text-right" >' . number_format($balance,2) . '</td>';
				$table .= '<td>' . $voucher_status . '</td>';
				$table .= '</tr>';
			}
		else:
			$table .= "<tr>
			<td colspan = '8' class = 'text-center'>No Records Found</td>
			</tr>";
		endif;
		
		$pagination->table 	= $table;
		$pagination->csv 	= $this->export_main();
		return $pagination;
	}

	private function get_details($type)
	{
		$itemcode 	= $this->input->post('itemcode');

		$result 	= $this->po->retrieveItemDetails($itemcode);
		
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

			$result = $this->po->getValue("partners", $data_var, $cond);

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

		$result = $this->po->check_duplicate($input);

		$count = $result[0]->count;
		
		$msg   = "";

		if( $count > 0 )
		{	
			$msg = "exists";
		}

		return $dataArray = array("msg" => $msg);
	}
	
	private function get_ATC()
	{
		$wtaxcode 	=	$this->input->post('code');
		// echo $wtaxcode;
		
		$fields 	= 	array('atc_code','short_desc','tax_rate');

		$result 	=	$this->po->getValue('atccode', $fields, " wtaxcode = '$wtaxcode' ", "atc_code");

		$selection 	= 	"";
		
		$selection 	.= 	"<option value = 'none'>None</option>";

		foreach($result as $row)
		{
			$selection 	.=	"<option value = '".$row->atc_code."'>". $row->atc_code . " - ".$row->short_desc."</option>"; 
		}
		// var_dump($result);
		
		return $dataArray = array("atc_codes" => $selection,"wtaxrate" => $result[0]->tax_rate);
	}	

	private function add($final="")
	{
		$data_post 	= $this->input->post();
		$voucher 	= $this->input->post("h_voucher_no");


		if( $final == "save" )
			$result    = $this->po->processTransaction($data_post, "create" , $voucher);
		else
			$result    = $this->po->processTransaction($data_post, "create");


		$saveArr = array();
		$actualArr = array();

		if(!empty($result['errmsg']) && !empty($result['error']) && !empty($result['warning']) && !empty($result['date_checker']))
		{
			$msg = $result;
		}
		else
		{
			// $msg = "success";
			$msg = $result;
			if ( $this->inventory_model ) {
				$this->inventory_model->generateBalanceTable();
			}
			if($this->report_model){
				$this->report_model->generateAssetActivity();
			}
			if(isset($data_post['budgetcode'])){
				for($i=1;$i<=count($data_post['budgetcode']);$i++){
					$saveArr['voucherno'] 	= $data_post['h_voucher_no'];
					$saveArr['accountcode'] = $result['accountcode'];
					$saveArr['budget_code'] = $data_post['budgetcode'][$i];
					$saveArr['actual'] 		= str_replace(',','', $data_post['itemprice'][$i]);
					$actualArr[]      		= $saveArr;
				}
				$save = $this->po->saveActual($actualArr, $data_post['h_voucher_no']);
			}
		}
		return $dataArray = array("msg" => $msg);
	}
	
	private function update()
	{
		$data_post 	= $this->input->post();

		$voucher 	= $this->input->post("voucher");
		
		$result = $this->po->processTransaction($data_post, "edit", $voucher);
		
		if(!empty($result['errmsg']) && !empty($result['error']) && !empty($result['warning']))
		{
			$msg = $result;
		}
		else
		{
			$this->log->saveActivity("Edited Purchase Order [$voucher] ");
			$msg = $result;

			if ( $this->inventory_model ) {
				$this->inventory_model->generateBalanceTable();
			}
			if($this->report_model){
				$this->report_model->generateAssetActivity();
			}
			if(isset($data_post['budgetcode'])){
				for($i=1;$i<=count($data_post['budgetcode']);$i++){
					$saveArr['voucherno'] 	= $data_post['h_voucher_no'];
					$saveArr['accountcode'] = $result['accountcode'];
					$saveArr['budget_code'] = $data_post['budgetcode'][$i];
					$saveArr['actual'] 		= str_replace(',','', $data_post['amount'][$i]);
					$actualArr[]      		= $saveArr;
				}
			}
			$save = $this->po->updateBudget($actualArr, $voucher);
		}

		return $dataArray = array( "msg" => $msg, "voucher" => $voucher );
	}

	private function cancel()
	{
		$voucher 	= 	$this->input->post("voucher");

		$result 	=	$this->po->delete_temp_transactions($voucher, "purchaseorder", "purchaseorder_details");

		if($result == '1')
			$msg = "success";
		else
			$msg = "Failed to Cancel.";

		return $dataArray = array( "msg" => $msg );
	}

	private function delete_po()
	{
		$vouchers 		=	$this->input->post('delete_id');
		$po_vouchers 	= 	"'" . implode("','", $vouchers) . "'";
		
		$data['stat'] 	=	"cancelled";
		
		$cond 			=	" voucherno IN ($po_vouchers) ";

		$result 	=	$this->po->updateData($data, "purchaseorder", $cond);

		if( $result )
		{
			$result 	=	$this->po->updateData($data, "purchaseorder_details", $cond);
		}

		if($result == '1')
		{
			$this->log->saveActivity("Cancelled Purchase Order [ ". implode(',',$vouchers) ." ] ");
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

		$result = $this->po->deleteData($table, $cond);

		if($result)
			$msg = "success";
		else
			$msg = "The system has encountered an error in cancelling. Please contact admin to fix this issue.";

		return	$dataArray = array( "msg" => $msg );
	}

	private function save_vendor_data()
	{
		$result    	= "";

		$data_var  	= array("partnercode", "first_name", "last_name", "email", "address1", "businesstype", "tinno", "terms");
		
		$data_post 		= $this->input->post($data_var);
		
		$partnercode 	= $this->input->post('partnercode');

		$result 		= $this->po->insertVendor($data_post);

		if($result)
		{
			$this->log->saveActivity("Purchase Order - Added New Customer [$partnercode] ");
			$msg = "success";
		}
		else
		{
			$msg = "The system has encountered an error in saving. Please contact admin to fix this issue.";
		}

		return $dataArray = array( "msg" => $msg );
		
	}

	private function export_main(){
		$data 		= $this->input->post(array("daterangefilter", "vendor", "search", "filter","sort","limit"));
		
		$header = array("Date","PO No.","Reference No.","Supplier","Amount");

		$csv = '';
		$csv .= '"' . implode('","', $header) . '"';
		$csv .= "\n";
		
		$result = $this->po->export_main($data);

		$totalamount 	=	0;

		if (!empty($result)){
			foreach ($result as $key => $row){

				$totalamount += $row->netamount;

				$csv .= '"' . $row->transactiondate . '",';
				$csv .= '"' . $row->voucherno . '",';
				$csv .= '"' . $row->referenceno . '",';
				$csv .= '"' . $row->partnername . '",';
				$csv .= '"' . number_format($row->netamount,2) . '"';
				$csv .= "\n";
			}
		}
		
		$csv .= '"","","","Total ","'. number_format($totalamount,2) .'"';
		return $csv;
	}

	private function checkifitemisinbudget(){
		$itemcode = $this->input->post('itemcode');

		// Get item's purchase account 
		$ret_acct 	= 	$this->po->get_purchaseaccount($itemcode);
		$item_acct 	=	isset($ret_acct->expense_account) ? $ret_acct->expense_account : 0;

		// Check if Account is used in a Budget
		$ret_result = $this->po->checkifaccountisinbudget($item_acct);
		$result 	=	!empty($ret_result) ? 1 : 0;

		return $dataArray = array("result"=>$result);
	}

	private function checkifpairexistsinbudget() {
		$itemcode 	= $this->input->post('itemcode');
		$budget 	= $this->input->post('budgetcode');

		// Get item's purchase account 
		$ret_acct 	= 	$this->po->get_purchaseaccount($itemcode);
		$item_acct 	=	isset($ret_acct->expense_account) ? $ret_acct->expense_account : 0;

		// Check if Budget_Accountcode pair exists
		$ret_result = $this->po->checkifpairexistsinbudget($item_acct, $budget);
		$result 	=	!empty($ret_result) ? 1 : 0;

		return $dataArray = array("result"=>$result);
	}
}
?>