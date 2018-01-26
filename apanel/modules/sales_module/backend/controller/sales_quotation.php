<?php
class controller extends wc_controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->url 			    = new url();
		$this->sq 				= new sales_quotation();
		$this->seq 				= new seqcontrol();
		$this->input            = new input();
		$this->ui 			    = new ui();
		$this->view->title      = 'Quotation';
		$this->show_input 	    = true;
		$this->logs  			= new log;
		// $this->log 				= new log();

		$session                = new session();
		$this->companycode      = $session->get('companycode');
		
		$this->fields = array(
				'voucherno',
				'customer',
				'documentno',
				'tinno',
				'address1',
				'terms',
				'transactiondate',
				'due_date',
				'remarks',
				'amount'
			);
	}

	public function listing()
	{
		$data['ui'] 				= $this->ui;
	
		$data['show_input'] 		= true;

		$data['date_today'] 		= date("M d, Y");
		
		$data['customer_list'] 		= $this->sq->retrieveCustomerList();
		
		$data["payment_mode_list"] 	= $this->sq->getOption("payment_type");
		$data["cash_account_codes"] = $this->sq->retrieveCashAccountClassList();

		$this->view->load('sales_quotation/sales_quotationlist', $data);
	}

	public function create()
	{
		$data 					= $this->input->post($this->fields);

		$data['customer_list'] 	= $this->sq->retrieveCustomerList();
		$data['proforma_list'] 	= $this->sq->retrieveProformaList();
		$data["business_type"] 	= $this->sq->getOption("businesstype");
		$data["vat_type"] 		= $this->sq->getOption("vat_type");
		$data["tax_codes"] 		= $this->sq->getTaxCode('VAT');
		$data['percentage'] 	= "";
		$data['h_disctype'] 	= "perc";

		$curr_type_data         = array("currencycode ind", "currency val");
		$data["currency_codes"] = $this->sq->getValue("currency", $curr_type_data,'','currencycode');

		$cc_entry_data          = array("itemcode ind","itemname val");
		$data["itemcodes"] 		= $this->sq->getValue("items", $cc_entry_data,'',"itemcode");

		$acc_entry_data          = array("accountname ind","CONCAT(segment5,' - ', accountname )  val");
		$acc_entry_cond          = "accounttype != 'P'";
		$data["account_ entries"] = $this->sq->getValue("chartaccount", $acc_entry_data,$acc_entry_cond, "segment5");

		$data['transactiondate'] = $this->date->dateFormat();

		/**ADD NEW CUSTOMER**/
		
		// Retrieve business type list
		$bus_type_data                = array("code ind", "value val");
		$bus_type_cond                = "type = 'businesstype'";
		$data["business_type_list"]   = $this->sq->getValue("wc_option", $bus_type_data, $bus_type_cond, false);

		// Retrieve generated ID
		$gen_value               = $this->sq->getValue("salesquotation", "COUNT(*) as count", "voucherno != ''");
		$data["generated_id"]    = (!empty($gen_value->count)) ? 'TMP_'.($gen_value->count + 1) : 'TMP_1';

		$data['ui'] 			= $this->ui;
		$data['show_input'] 	= true;		
		$data['ajax_post'] 		= "voucher=".$data["generated_id"];
		$data['task'] 			= "create";
		$data["row_ctr"] 		= 0;
		$data['cmp'] 			= $this->companycode;

		//Finalize Saving	
		$save_status 			= $this->input->post('save');
		//echo $save_status;
		if( $save_status == "final" || $save_status == "final_preview" || $save_status == "final_new" )
		{
			$voucherno 				= $this->input->post('h_voucher_no');	
			$isExist 				= $this->sq->getValue("salesquotation", array("voucherno"), "voucherno = '$voucherno'");
			
			if($isExist[0]->voucherno)
			{
				/** RETRIEVE DATA **/
				$retrieved_data 		= $this->sq->retrieveExistingSQ($voucherno);
			
					
				$data["voucherno"]       = $retrieved_data["header"]->voucherno;
				$data["customer"]      	 = $retrieved_data["header"]->customer;
				$data["due_date"]    	 = date('M d,Y', strtotime($retrieved_data["header"]->duedate));
				$data["transactiondate"] = date('M d,Y', strtotime($retrieved_data["header"]->transactiondate));
				
				//Footer Data
				$data['amount'] 	 = $retrieved_data['header']->amount;
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

				/**UPDATE TABLES FOR FINAL SAVING**/
				$generatedvoucher			= $this->seq->getValue('SQ', $this->companycode); 
			
				$update_info				= array();
				$update_info['voucherno']	= $generatedvoucher;
				$update_info['stat']		= 'open';
				$update_condition			= "voucherno = '$voucherno'";
				$updateTempRecord			= $this->sq->updateData($update_info,"salesquotation",$update_condition);
				$updateTempRecord			= $this->sq->updateData($update_info,"salesquotation_details",$update_condition);

				$this->logs->saveActivity("Create Quotation [$generatedvoucher] ");

				if( $updateTempRecord && $save_status == 'final' )
				{
					$this->url->redirect(BASE_URL . 'sales/sales_quotation');
				}
				else if( $updateTempRecord && $save_status == 'final_preview' )
				{
					$this->url->redirect(BASE_URL . 'sales/sales_quotation/view/'.$generatedvoucher);
				}
				else if( $updateTempRecord && $save_status == 'final_new' )
				{
					$this->url->redirect(BASE_URL . 'sales/sales_quotation/create');
				}
			}

		}

		$this->view->load('sales_quotation/sales_quotation', $data);
	}

	public function edit($voucherno)
	{
		$retrieved_data 		= $this->sq->retrieveExistingSQ($voucherno);
		$data['customer_list'] 	= $this->sq->retrieveCustomerList();
		$data['proforma_list'] 	= $this->sq->retrieveProformaList();
		$data["business_type"] 	= $this->sq->getOption("businesstype");
		$data["vat_type"] 		= $this->sq->getOption("vat_type");
		$data["tax_codes"] 		= $this->sq->getTaxCode('VAT');

		$curr_type_data         = array("currencycode ind", "currency val");
		$data["currency_codes"] = $this->sq->getValue("currency", $curr_type_data,'','currencycode');

		$cc_entry_data          = array("itemcode ind","itemname val");
		$data["itemcodes"] 		= $this->sq->getValue("items", $cc_entry_data,'',"itemcode");

		$acc_entry_data          = array("accountname ind","CONCAT(segment5,' - ', accountname )  val");
		$acc_entry_cond          = "accounttype != 'P'";
		$data["account_entries"] = $this->sq->getValue("chartaccount", $acc_entry_data,$acc_entry_cond, "segment5");

		/**ADD NEW CUSTOMER**/
		
		// Retrieve business type list
		$bus_type_data                = array("code ind", "value val");
		$bus_type_cond                = "type = 'businesstype'";
		$data["business_type_list"]   = $this->sq->getValue("wc_option", $bus_type_data, $bus_type_cond, false);

		$data["generated_id"] 	= $voucherno;
		$data["sid"] 		    = $voucherno;

		$data['ui'] 			= $this->ui;
		$data['show_input'] 	= true;
		$data['ajax_post'] 		= "&voucher=$voucherno";
		$data['task'] 			= "edit";
		$data["row_ctr"] 		= 0;
		$data['cmp'] 			= $this->companycode;

		// Header Data
		$data["remarks"]         = $retrieved_data["header"]->remarks;
		$data["voucherno"]       = $retrieved_data["header"]->voucherno;
		$data["customer"]      	 = $retrieved_data["header"]->customer;
		$data["due_date"]    	 = date('M d,Y', strtotime($retrieved_data["header"]->duedate));
		$data["transactiondate"] = date('M d,Y', strtotime($retrieved_data["header"]->transactiondate));

		$this->logs->saveActivity("Update Quotation [$voucherno] ");
		
		//Footer Data
		$data['amount'] 	 = $retrieved_data['header']->amount;
		$data['t_discount'] 	 = $retrieved_data['header']->discountamount;
		$data['t_total'] 	 	 = $retrieved_data['header']->netamount;
		$data['t_vat'] 			 = $retrieved_data['header']->taxamount;
		$data['t_vatsales'] 	 = $retrieved_data['header']->vat_sales;
		$data['t_vatexempt'] 	 = $retrieved_data['header']->vat_exempt;

		$discounttype 		 	 = $retrieved_data['header']->discounttype;
		$data['percentage'] 	 = "";
		$data['h_disctype'] 	 = $discounttype;

		//Vendor Data
		// $data["terms"] 		 	 = $retrieved_data["customer"]->terms;
		// $data["tinno"] 		 	 = $retrieved_data["customer"]->tinno;
		// $data["address1"] 		 = $retrieved_data["customer"]->address1;
		
		//Details
		$data['details'] 		 = $retrieved_data['details'];
		
		$this->view->load('sales_quotation/sales_quotation', $data); 
	}

	public function view($voucherno)
	{
		$retrieved_data 		= $this->sq->retrieveExistingSQ($voucherno);

		$data['customer_list'] 	= $this->sq->retrieveCustomerList();
		$data['proforma_list'] 	= $this->sq->retrieveProformaList();
		$data["business_type"] 	= $this->sq->getOption("businesstype");
		$data["vat_type"] 		= $this->sq->getOption("vat_type");
		$data["tax_codes"] 		= $this->sq->getTaxCode('VAT');

		$curr_type_data         = array("currencycode ind", "currency val");
		$data["currency_codes"] = $this->sq->getValue("currency", $curr_type_data,'','currencycode');

		$cc_entry_data          = array("itemcode ind","itemname val");
		$data["itemcodes"] 		= $this->sq->getValue("items", $cc_entry_data,'',"itemcode");

		$acc_entry_data          = array("accountname ind","CONCAT(segment5,' - ', accountname )  val");
		$acc_entry_cond          = "accounttype != 'P'";
		$data["account_entries"] = $this->sq->getValue("chartaccount", $acc_entry_data,$acc_entry_cond, "segment5");

		$data["generated_id"] 	= $voucherno;
		$data["sid"] 		    = $voucherno;

		$data['ui'] 			= $this->ui;
		$data['show_input'] 	= false;
		$data['ajax_post'] 		= "";
		$data['task'] 			= "view";
		$data["row_ctr"] 		= 0;
		$data['cmp'] 			= $this->companycode;

		// Header Data
		$data["voucherno"]       = $retrieved_data["header"]->voucherno;
		$data["remarks"]       = $retrieved_data["header"]->remarks;
		$data["customer"]      	 = $retrieved_data["header"]->customer;
		$data["due_date"]    	 = date('M d,Y', strtotime($retrieved_data["header"]->duedate));
		$data["transactiondate"] = date('M d,Y', strtotime($retrieved_data["header"]->transactiondate));
		
		//Footer Data
		$data['amount'] 	 	 = $retrieved_data['header']->amount;
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
		// $data["terms"] 		 	 = $retrieved_data["customer"]->terms;
		// $data["tinno"] 		 	 = $retrieved_data["customer"]->tinno;
		// $data["address1"] 		 = $retrieved_data["customer"]->address1;
		
		//Details
		$data['details'] 		 = $retrieved_data['details'];
		
		$this->view->load('sales_quotation/sales_quotation', $data);
	}

	public function print_preview($voucherno) 
	{
		$companycode = $this->companycode;
		
		/** HEADER INFO **/

			$docinfo_table  = "salesquotation as so";
			$docinfo_fields = array('so.transactiondate AS documentdate','so.voucherno AS voucherno',"partnername AS customer","'' AS referenceno",'so.amount AS amount','so.remarks as remarks','so.discounttype as disctype','so.discountamount as discount', 'so.netamount as net','so.amount as amount','so.vat_sales as vat_sales','so.vat_exempt as vat_exempt','so.taxamount as vat');
			$docinfo_join   = "partners as p ON p.partnercode = so.customer AND p.companycode = so.companycode";
			$docinfo_cond 	= "so.voucherno = '$voucherno'";

			$documentinfo  	= $this->sq->retrieveData($docinfo_table, $docinfo_fields, $docinfo_cond, $docinfo_join);
			$documentinfo	= $documentinfo[0]; 
			$customer 	    = $documentinfo->customer;
			// var_dump($customer);

		/** HEADER INFO - END**/

		/** DETAILS INFO **/

			$docdet_table   = "salesquotation_details as dtl";
			$docdet_fields  = array("dtl.itemcode as itemcode", "dtl.detailparticular as description","UPPER(dtl.issueuom)","unitprice as price","amount as amount");
			$docdet_cond    = "dtl.voucherno = '$voucherno'";
			$docdet_join 	= "";
			$docdet_groupby = "";
			$docdet_orderby = "dtl.linenum";
			
			$documentcontent = $this->sq->retrieveData($docdet_table, $docdet_fields, $docdet_cond, $docdet_join, $docdet_orderby, $docdet_groupby);

		/** DETAILS INFO --END**/
		
		/** CUSTOMER DETAILS **/

			$customercode 		=	$this->sq->getValue("salesquotation", array('customer')," voucherno = '$voucherno'");

			$custField			= array('partnername customer', 'address1 address', 'tinno', 'terms', 'mobile contactno');
			$customerdetails	= $this->sq->retrieveData("partners",$custField," partnertype= 'customer' AND partnercode = '".$customercode[0]->customer."'");
			$customerdetails	= $customerdetails[0];

		/** CUSTOMER DETAILS --END**/

		$documentdetails	= array(
			'Date'	=> $this->date->dateFormat($documentinfo->documentdate),
			'SQ #'	=> $voucherno
		);

		$print = new sales_print_model();
		$print->setDocumentType('Sales Quotation')
				->setFooterDetails(array('Approved By', 'Checked By'))
				->setCustomerDetails($customerdetails)
				->setDocumentDetails($documentdetails)
				// ->addTermsAndCondition()
				->addReceived();

		$print->setHeaderWidth(array(40, 100, 30, 30))
				->setHeaderAlign(array('C', 'C', 'C', 'C'))
				->setHeader(array('Item Code', 'Description', 'UOM', 'Price'))
				->setRowAlign(array('L', 'L', 'L', 'R'))
				->setSummaryWidth(array('170', '30'));

		$detail_height = 37;

		$total_amount = 0;
		foreach ($documentcontent as $key => $row) {
			if ($key % $detail_height == 0) {
				$print->drawHeader();
			}

			$total_amount	+= $row->price;
			$row->price	= number_format($row->price, 2);
			$print->addRow($row);
			if (($key + 1) % $detail_height == 0) {
				$print->drawSummary(array('Total Amount' => number_format($total_amount, 2)));
				$total_amount = 0;
			}
		}
		$print->drawSummary(array('Total Amount' => number_format($total_amount, 2)));

		$print->drawPDF('Sales Quotation - ' . $voucherno);
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
		else if($task == 'sq_listing') 
		{
			$result = $this->sq_listing();
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
			$result = $this->get_details('item');
		}
		else if( $task == 'ajax_delete' )
		{
			$result = $this->ajax_delete();
		}
		else if( $task == 'update_sq_accept' )
		{
			$result = $this->update_sq_accept();
		}
		else if( $task == 'update_sq_decline' )
		{
			$result = $this->update_sq_decline();
		}
		else if( $task == 'delete_row' )
		{
			$result = $this->delete_row();
		}
		else if( $task == 'update_statusClosed' )
		{
			$result = $this->update_statusClosed();
		}
		else if( $task == 'delete_so' )
		{
			$result = $this->delete_so();
		}

		echo json_encode($result); 
	}

	private function sq_listing()
	{
		$voucher_status = '';
		$posted_data 	= $this->input->post(array("daterangefilter", "customer", "search", "filter","sort","limit"));

		$search = $this->input->post("search");
		$filter = $this->input->post("filter");
		$custfilter =  $this->input->post("customer");
		$daterangefilter  = $this->input->post("daterangefilter");
		$sort  = $this->input->post("sort");

		$pagination 	= $this->sq->retrieveListing($posted_data);
		$table 	= '';

		if( !empty($pagination->result) ) :
			foreach ($pagination->result as $key => $row) {

				$customer 	= $this->sq->getValue('partners','partnername'," partnercode = '$row->customer' ");

				$customer_name 	=	$customer[0]->partnername;

				if($row->stat == 'locked')
				{
					$voucher_status = '<span class="label label-success">CONVERTED</span>';
				}
				else if($row->stat == 'open')
				{
					$voucher_status = '<span class="label label-info">DRAFT</span>';
				}
				else if( $row->stat == 'cancelled' )
				{	
					$voucher_status = '<span class="label label-warning">CANCELLED</span>';
				}
				else if( $row->stat == 'expired' )
				{	
					$voucher_status = '<span class="label label-danger">EXPIRED</span>';
				}

				$table .= '<tr>';
				$element = $this->ui->loadElement('check_task')
									->addView()
									->addEdit($row->stat == 'open')
									->addDelete($row->stat == 'open' || $row->stat == 'expired' )
									// ->addOtherTask('Print Preview', 'print',($row->stat == 'open'  || $row->stat == 'cancelled' || $row->stat == 'locked' ))
									->addPrint()
									->setLabels(array('delete'=>'Cancel'))
									->addOtherTask('Convert to SO', 'share', ($row->stat == 'open'))
									->setValue($row->voucherno);
				if ($row->stat == 'open') {
					$element->addCheckbox();
				}
				$dropdown = $element->draw();
				$table .= '<td align = "center">' . $dropdown . '</td>';
				$table .= '<td>' . date("M d, Y",strtotime($row->transactiondate)) . '</td>';
				$table .= '<td>' . $row->voucherno. '</td>';
				$table .= '<td>' . $customer_name . '</td>';
				$table .= '<td>' . $voucher_status . '</td>';
				$table .= '</tr>';
			}
		else:
			$table .= "<tr>
							<td colspan = '5' class = 'text-center'>No Records Found</td>
					</tr>";
		endif;

		$pagination->table = $table;
		$pagination->csv 	= $this->export_main($posted_data);
		return $pagination;
	}

	private function ajax_delete() {
		$voucherno = $this->input->post('voucherno');
		if ($voucherno) {
			$this->sq->deleteSQ($voucherno);
		}
	}

	private function update_statusClosed() {
		$voucherno = $this->input->post('voucherno');
		if ($voucherno) {
			$this->sq->completeSQ($voucherno);
		}
	}
	
	private function get_details()
	{
		$itemcode 	= $this->input->post('itemcode');
		$customer 	= $this->input->post('customer');

		$result 	= $this->sq->retrieveItemDetails($itemcode,$customer);
		
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

			$result = $this->sq->getValue("partners", $data_var, $cond);

			$dataArray = array( "address" => $result[0]->address1, "tinno" => $result[0]->tinno, "terms" => $result[0]->terms );
		}
		if($data_cond == "exchange_rate")
		{
			$data_var = array("accountnature");
			$account  = $this->input->post("account");

			$cond 	  = "accountname = '$account'";

			$result = $this->sq->getValue("chartaccount", $data_var, $cond);

			$dataArray = array("accountnature" => $result[0]->accountnature);

		}
		
		return $dataArray;
	}
	
	private function add($final="")
	{
		$data_post 	= $this->input->post();
		$voucher 	= $this->input->post("h_voucher_no");

		if( $final == "save" )
			$result    = $this->sq->processTransaction($data_post, "create" , $voucher);
		else
			$result    = $this->sq->processTransaction($data_post, "create");

		if(!empty($result))
			$msg = $result;
		else
			$msg = "success";

		return $dataArray = array("msg" => $msg);
	}
	
	private function update()
	{
		$data_post 	= $this->input->post();

		$voucher 	= $this->input->post("voucher");

		/**
		* Update Database
		*/
		$result = $this->sq->processTransaction($data_post, "edit", $voucher);

		if(!empty($result))
			$msg = $result;
		else
			$msg = "success";

		return $dataArray = array( "msg" => $msg, "voucher" => $voucher );
	}

	private function cancel()
	{
		$voucher 	= 	$this->input->post("voucher");

		$result 	=	$this->sq->delete_temp_transactions($voucher, "salesquotation", "salesquotation_details");

		if($result == '1')
			$msg = "success";
		else
			$msg = "Failed to Cancel.";

		return $dataArray = array( "msg" => $msg );
	}

	private function delete_row()
	{
		$table 		= $this->input->post('table');
		$linenum	= $this->input->post('linenum'); 
		$voucher 	= $this->input->post('voucherno');

		$cond 		= " voucherno = '$voucher' AND linenum = '$linenum' ";

		$result = $this->sq->deleteData($table, $cond);

		if($result)
			$msg = "success";
		else
			$msg = "The system has encountered an error in deleting. Please contact admin to fix this issue.";

		return	$dataArray = array( "msg" => $msg );
	}

	private function save_customer_data()
	{
		$result    	= "";

		$data_var  	= array("partnercode", "first_name", "last_name", "email", "address1", "businesstype", "tinno", "terms");
		
		$data_post 	= $this->input->post($data_var);

		$requestorcode 	= $this->input->post('partnercode');

		$this->log->saveActivity("Quotation - Added New Customer [$requestorcode] ");
			
		$result 	= $this->sq->insertCustomer($data_post);

		if($result)
			$msg = "success";
		else
			$msg = "The system has encountered an error in saving. Please contact admin to fix this issue.";

		return $dataArray = array( "msg" => $msg );
	
	}

	private function export_main($posted_data){
	$header = array("Date","Quotation No.","Customer");

	$csv = '';
	$csv .= '"' . implode('", "', $header) . '"';
	$csv .= "\n";
	
	$result = $this->sq->export_main($posted_data);

	// $totalamount 	=	0;
	

	if (!empty($result)){
		foreach ($result as $key => $row){
			
			$customer 	= $this->sq->getValue('partners','partnername'," partnercode = '$row->customer' ");
			$customer_name 	=	$customer[0]->partnername;

			// $totalamount += $row->netamount;

			$csv .= '"' . $row->transactiondate . '",';
			$csv .= '"' . $row->voucherno . '",';
			//$csv .= '"' . $row->quotation_no . '",';
			$csv .= '"' . $customer_name . '",';
			// $csv .= '"' . $row->netamount . '"';
			$csv .= "\n";
		}
	}
	
	// $csv .= '"","",""," ","'. number_format($totalamount,2) .'"';
	return $csv;
	}

	private function delete_so()
	{
		$vouchers 		=	$this->input->post('delete_id');
		$so_vouchers 	= 	"'" . implode("','", $vouchers) . "'";
		
		$data['stat'] 	=	"cancelled";
		
		$cond 			=	" voucherno IN ($so_vouchers) ";

		$result 	=	$this->sq->updateData($data, "salesquotation", $cond);

		if( $result )
		{
			$result 	=	$this->sq->updateData($data, "salesquotation_details", $cond);
		}

		if($result == '1')
		{
			$this->logs->saveActivity("Deleted Sales Quotation [ ". implode(',',$vouchers) ." ] ");
			$msg = "success";
		
			// if ( $this->inventory_model ) {
			// 	$this->inventory_model->generateBalanceTable();
			// }
		}
		else
		{
			$msg = "Failed to Cancel.";
		}

		return $dataArray = array( "msg" => $msg );
	}

}
?>