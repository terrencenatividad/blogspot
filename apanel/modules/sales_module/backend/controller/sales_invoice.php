<?php
class controller extends wc_controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->url 			    = new url();
		$this->invoice 			= new sales_invoice();
		$this->seq 				= new seqcontrol();
		$this->input            = new input();
		$this->ui 			    = new ui();
		$this->restrict 		= new sales_restriction_model();
		$this->show_input 	    = true;

		$session                = new session();
		$this->companycode      = $session->get('companycode');
		
		$this->fields = array(
				'voucherno',
				'transactiondate',
				'referenceno',
				'customercode',
				'duedate',
				'remarks',
				'tinno',
				'address1',
				'terms'
			);

		$this->view->header_active = 'sales/sales_invoice/';
	}

	public function listing()
	{
		$this->view->title     		= 'Sales Invoice';
		
		$data['ui'] 				= $this->ui;
		$data['show_input'] 		= true;
		$data['date_today'] 		= '';
		$data['customer_list'] 		= $this->invoice->retrieveCustomerList();

		$data["display_list"]  		= array(10,15,20,50,100);

		$this->view->load('sales_invoice/sales_invoicelist', $data);
	}

	public function create()
	{
		$this->view->title     	= 'Create Sales Invoice';
		$data 					= $this->input->post($this->fields);

		$data['voucherno'] 		= '';
		$data["duedate"]    	= date('M d, Y');
		$data["transactiondate"]= date('M d, Y');
		
		// Item Limit
		$item_limit 			= $this->invoice->getReference("si_limit");
		$data['item_limit']		= $item_limit[0]->value; 

		// Closed Date
		$close_date 			= $this->restrict->getClosedDate();
		$data['close_date']		= $close_date;
		
		// Get Invoice Reference
		$invoice_reference 		= $this->invoice->getReference("invoice_dr");
		$invoice_dr 			= $invoice_reference[0]->value;

		$data['dr_linked'] 		= ($invoice_dr == 'yes') ? true : false;
		if($invoice_dr == 'yes')
		{
			$data["drno"] 		= '';
			$data["deliveries"] = $this->invoice->getDeliveries();
		}

		$data['customer_list'] 	= $this->invoice->retrieveCustomerList();
		$data["vat_type"] 		= $this->invoice->getOption("vat_type");
		$data["tax_codes"] 		= $this->invoice->getTaxCode('VAT');
		$data['percentage'] 	= "";
		$data['h_disctype'] 	= "perc";

		$item_entry_data        = array("itemcode ind","itemname val");
		$item_condition     	= " stat = 'active' ";
		$data["itemcodes"] 		= $this->invoice->getValue("items", $item_entry_data,$item_condition,"itemcode");

		$uom_data          		= array("uomcode ind","uomdesc val");
		$uom_condition     		= " uomcode != 'P' AND stat = 'active' ";
		$data["uomcodes"] 		= $this->invoice->getValue("uom", $uom_data,$uom_condition, "uomcode");

		/**
		* Get List of Business types (for new customer)
		*/
		$data["business_type_list"]   = $this->invoice->getOption("businesstype");

		$data['ui'] 			= $this->ui;
		$data['show_input'] 	= true;
		$data['ro_input'] 		= true;
		$data['task'] 			= "create";
		$data["row_ctr"] 		= 0;
		$data['cmp'] 			= $this->companycode;

		$data['percentage'] 	 = "";
		$data['disctype'] 	 	 = "amt";
		$data['disc_amt'] 	 	 = 'checked';
		$data['disc_perc'] 	 	 = '';
		$data['disc_radio_amt']  = 'active';
		$data['disc_radio_perc'] = '';
		$data['restrict_si'] 	 = false;

		//Finalize Saving	
		$save_status 			= $this->input->post('save');

		if( $save_status == "final" || $save_status == "final_preview" || $save_status == "final_new" )
		{
			$voucherno 				= $this->input->post('voucherno');	
			$isExist 				= $this->invoice->getValue("salesinvoice", array("voucherno"), " voucherno = '$voucherno' ");
			
			if($isExist[0]->voucherno)
			{
				/**UPDATE TABLES FOR FINAL SAVING**/
				$generatedvoucher			= $this->seq->getValue('SI'); 
			
				$update_info				= array();
				$update_info['voucherno']	= $generatedvoucher;
				$update_info['stat']		= 'posted';
				$update_condition			= " voucherno = '$voucherno' AND stat = 'temporary' ";
				$updateTempRecord			= $this->invoice->updateData($update_info,"salesinvoice",$update_condition);
				$updateTempRecord			= $this->invoice->updateData($update_info,"salesinvoice_details",$update_condition);

				/**
				 * Update DR status
				 */
				$drno 						= $this->input->post('drno');
				if(!empty($drno))
				{
					$dr_info 				 	= array();
					$dr_info['stat']			= 'With Invoice';
					$dr_condition				= " voucherno = '$drno' AND stat = 'Delivered' ";
					$updateDrRecord				= $this->invoice->updateData($dr_info,"deliveryreceipt",$dr_condition);
					$updateDrRecord				= $this->invoice->updateData($dr_info,"deliveryreceipt_details",$dr_condition);
				}

				/**
				 * Auto Post / Generate AR
				 */
				$this->generate_receivable('yes',$generatedvoucher);

				if( $updateTempRecord && $save_status == 'final' )
				{
					$this->url->redirect(BASE_URL . 'sales/sales_invoice');
				}
				else if( $updateTempRecord && $save_status == 'final_preview' )
				{
					$this->url->redirect(BASE_URL . 'sales/sales_invoice/view/'.$generatedvoucher);
				}
				else if( $updateTempRecord && $save_status == 'final_new' )
				{
					$this->url->redirect(BASE_URL . 'sales/sales_invoice/create');
				}
			}

		}

		$this->view->load('sales_invoice/sales_invoice', $data);
	}

	public function edit($voucherno)
	{
		$this->view->title     	= 'Edit Sales Invoice';
		$retrieved_data 		= $this->invoice->retrieveSalesInvoice($voucherno);

		// Item Limit
		$item_limit 			= $this->invoice->getReference("si_limit");
		$data['item_limit']		= $item_limit[0]->value;
		
		// Closed Date
		$close_date 			= $this->restrict->getClosedDate();
		$data['close_date']		= $close_date;
		
		$data['customer_list'] 	= $this->invoice->retrieveCustomerList();
		$data["business_type"] 	= $this->invoice->getOption("businesstype");
		$data["vat_type"] 		= $this->invoice->getOption("vat_type");
		$data["tax_codes"] 		= $this->invoice->getTaxCode('VAT');

		$item_entry_data        = array("itemcode ind","CONCAT(itemcode,' - ',itemname) val");
		$data["itemcodes"] 		= $this->invoice->getValue("items", $item_entry_data,'',"itemcode");

		$uom_data          		= array("uomcode ind","uomdesc val");
		$uom_condition     		= " uomcode != 'P' AND stat = 'active' ";
		$data["uomcodes"] 		= $this->invoice->getValue("uom", $uom_data,$uom_condition, "uomcode");

		$data["business_type_list"]   = $this->invoice->getOption("businesstype");

		// Get Invoice Reference
		$invoice_reference 		= $this->invoice->getReference("invoice_dr");
		$invoice_dr 			= $invoice_reference[0]->value;
		
		$data['dr_linked'] 		= ($invoice_dr == 'yes') ? true : false;
		if($invoice_dr == 'yes')
		{
			$data["drno"] 		= $retrieved_data["header"]->drno;
			$data["deliveries"] = $this->invoice->getDeliveries();
		}

		$data["generated_id"] 	= $voucherno;
		$data["sid"] 		    = $voucherno;

		$data['ui'] 			= $this->ui;
		$data['show_input'] 	= true;
		$data['ro_input'] 		= (!empty($data["drno"])) ? false : true;
		$data['ajax_post'] 		= "&voucher=$voucherno";
		$data['task'] 			= "edit";
		$data["row_ctr"] 		= 0;
		$data['cmp'] 			= $this->companycode;

		// Header Data
		$transactiondate 		 = $retrieved_data["header"]->transactiondate;
		$data["voucherno"]       = $retrieved_data["header"]->voucherno;
		$data["referenceno"]     = $retrieved_data["header"]->referenceno;
		$data["customercode"]    = $retrieved_data["header"]->customercode;
		$data["customername"]    = $retrieved_data["header"]->customername;
		$data["duedate"]    	 = date('M d, Y', strtotime($retrieved_data["header"]->duedate));
		$data["transactiondate"] = date('M d, Y', strtotime($transactiondate));
		$data["remarks"]      	 = $retrieved_data["header"]->remarks;
		$data["status"]       	 = $retrieved_data["header"]->status;

		//Footer Data
		$data['total'] 	 		 = $retrieved_data['header']->amount;
		$data['discountamount']  = $retrieved_data['header']->discountamount;
		$data['total_sales'] 	 = $retrieved_data['header']->netamount;
		$data['total_tax'] 		 = $retrieved_data['header']->taxamount;
		$data['vatable_sales'] 	 = $retrieved_data['header']->vat_sales;
		$data['vatexempt_sales'] = $retrieved_data['header']->vat_exempt;

		$discounttype 		 	 = $retrieved_data['header']->discounttype;
		$data['percentage'] 	 = "";
		$data['disctype'] 	 	 = $discounttype;
		$data['disc_amt'] 	 	 = ($discounttype == 'amt') ? 'checked' : '';
		$data['disc_perc'] 	 	 = ($discounttype == 'perc') ? 'checked' : '';
		$data['disc_radio_amt']  = ($discounttype == 'amt') ? 'active' : '';
		$data['disc_radio_perc'] = ($discounttype == 'perc') ? 'active' : '';

		//Vendor Data
		$data["terms"] 		 	 = $retrieved_data["customer"]->terms;
		$data["tinno"] 		 	 = $retrieved_data["customer"]->tinno;
		$data["address1"] 		 = $retrieved_data["customer"]->address1;
		
		//Details
		$data['details'] 		 = $retrieved_data['details'];
		$restrict_si 			 = $this->restrict->setButtonRestriction($transactiondate);
		$data['restrict_si'] 	 = $restrict_si;

		$this->view->load('sales_invoice/sales_invoice', $data);
	}

	public function view($voucherno)
	{
		$this->view->title     	= 'View Sales Invoice';
		$retrieved_data 		= $this->invoice->retrieveSalesInvoice($voucherno);
		
		$data['customer_list'] 	= $this->invoice->retrieveCustomerList();
		$data["business_type"] 	= $this->invoice->getOption("businesstype");
		$data["vat_type"] 		= $this->invoice->getOption("vat_type");
		$data["tax_codes"] 		= $this->invoice->getTaxCode('VAT');

		$item_entry_data        = array("itemcode ind","CONCAT(itemcode,' - ',itemname) val");
		$data["itemcodes"] 		= $this->invoice->getValue("items", $item_entry_data,'',"itemcode");

		// Item Limit
		$item_limit 			= $this->invoice->getReference("si_limit");
		$data['item_limit']		= $item_limit[0]->value;

		// Closed Date
		$close_date 			= $this->restrict->getClosedDate();
		$data['close_date']		= $close_date;
		
		$uom_data          		= array("uomcode ind","uomdesc val");
		$uom_condition     		= " uomcode != 'P' AND stat = 'active' ";
		$data["uomcodes"] 		= $this->invoice->getValue("uom", $uom_data,$uom_condition, "uomcode");

		$data["generated_id"] 	= $voucherno;
		$data["sid"] 		    = $voucherno;

		$data['ui'] 			= $this->ui;
		$data['show_input'] 	= false;
		$data['ro_input'] 		= false;
		$data['ajax_post'] 		= "";
		$data['task'] 			= "view";
		$data["row_ctr"] 		= 0;
		$data['cmp'] 			= $this->companycode;

		// Header Data
		$transactiondate 		= $retrieved_data["header"]->transactiondate;
		$duedate 				= $retrieved_data["header"]->duedate;

		$data["voucherno"]       = $retrieved_data["header"]->voucherno;
		$data["referenceno"]     = $retrieved_data["header"]->referenceno;
		$data["customercode"]    = $retrieved_data["header"]->customercode;
		$data["customername"]    = $retrieved_data["header"]->customername;
		$data["duedate"]    	 = $this->date->dateFormat($duedate);
		$data["transactiondate"] = $this->date->dateFormat($transactiondate);
		$data["remarks"]      	 = $retrieved_data["header"]->remarks;
		$data["status"]       	 = $retrieved_data["header"]->status;
		
		//Footer Data
		$data['total'] 	 		 = $retrieved_data['header']->amount;
		$data['discountamount']  = $retrieved_data['header']->discountamount;
		$data['total_sales'] 	 = $retrieved_data['header']->netamount;
		$data['total_tax'] 		 = $retrieved_data['header']->taxamount;
		$data['vatable_sales'] 	 = $retrieved_data['header']->vat_sales;
		$data['vatexempt_sales'] = $retrieved_data['header']->vat_exempt;

		$discountamount 		 = $retrieved_data['header']->discountamount;
		$discounttype 		 	 = $retrieved_data['header']->discounttype;
		$data['percentage'] 	 = ($discounttype == 'perc' && $discountamount > 0 ) 	? 	"%" 	: 	"";
		$data['h_disctype'] 	 = $discounttype;

		// Get Invoice Reference
		$invoice_reference 		= $this->invoice->getReference("invoice_dr");
		$invoice_dr 			= $invoice_reference[0]->value;

		$data['dr_linked'] 		= ($invoice_dr == 'yes') ? true : false;
		if($invoice_dr == 'yes')
		{
			$data["drno"] 		= $retrieved_data["header"]->drno;
			$data["deliveries"] = $this->invoice->getDeliveries();
		}

		//Vendor Data
		$data["terms"] 		 	 = $retrieved_data["customer"]->terms;
		$data["tinno"] 		 	 = $retrieved_data["customer"]->tinno;
		$data["address1"] 		 = $retrieved_data["customer"]->address1;
		
		//Details
		$data['details'] 		 = $retrieved_data['details'];
		$restrict_si 			 = $this->restrict->setButtonRestriction($transactiondate);
		$data['restrict_si'] 	 = $restrict_si;

		$this->view->load('sales_invoice/sales_invoice', $data);
	}

	private function invoice_listing() {
		$data 		= $this->input->post(array('search', 'customer', 'filter', 'daterangefilter', 'sort'));
		$search 	= $data['search'];
		$customer 	= $data['customer'];
		$tab 		= $data['filter'];
		$datefilter	= $data['daterangefilter'];
		$sort 		= $data['sort'];
		
		if(!empty($datefilter))
		{
			$datefilter = explode('-', $datefilter);
			foreach ($datefilter as $date) {
				$dates[] = date('Y-m-d', strtotime($date));
			}
		}else{
			$dates[0] = '';
			$dates[1] = '';
		}
		
		$pagination = $this->invoice->getInvoiceList($search, $customer, $tab, $dates[0], $dates[1], $sort);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$customer  			= 	$row->customer;
			$transactiondate 	=	$row->date;

			if($row->stat == 'posted'){
				if($row->amount == $row->balance){
					$status = '<span class="label label-info">UNPAID</span>';
				}else if($row->balance <= 0){
					$status = '<span class="label label-success">PAID</span>';
				}else if($row->balance > 0 && $row->amount != $row->balance){
					$status = '<span class="label label-primary">PARTIAL</span>';
				}
			}else if($row->stat == 'cancelled'){
				$status = '<span class="label label-danger">CANCELLED</span>';
			}else if($row->stat == 'open'){
				$status = '<span class="label label-warning">FOR APPROVAL</span>';
				$row->balance = $row->amount;
			}

			$restrict_si 	=	$this->restrict->setButtonRestriction($transactiondate);
			$show_edit 		= ($row->balance == $row->amount  && $row->stat != 'cancelled');
			$show_delete 	= ($row->balance == $row->amount && $row->stat != 'cancelled');

			$dropdown = $this->ui->loadElement('check_task')
							->addView()
							->addEdit($show_edit && $restrict_si)
							->addOtherTask(
								'Print Invoice',
								'print',
								true
							)
							->addDelete($show_delete && $restrict_si)
							->addCheckbox($show_delete && $restrict_si)
							->setValue($row->voucherno)
							->setLabels(array('delete' => 'Cancel'))
							->draw();

			$table .= '<tr>';
			$table 	.= '<td class="text-center" >'.$dropdown.'</td>';
			$table 	.= '<td>' . $this->date($row->date) . '</td>';
			$table 	.= '<td>' . $row->voucherno . '</td>';
			$table 	.= '<td>' . $customer . '</td>';
			$table 	.= '<td class="text-right">' . $this->amount($row->amount) . '</td>';
			$table 	.= '<td class="text-right">' . $this->amount($row->balance) . '</td>';
			$table 	.= '<td>' . $status . '</td>';
			$table 	.= '</tr>';
		}
		$pagination->table = $table;

		return $pagination;
	}

	public function generate_receivable($trigger,$invoiceno='')
	{
		// Get Invoice Reference
		$invoice_reference 	= $this->invoice->getReference("invoice_ar");
		$invoice_dr 		= $invoice_reference[0]->value;
		
		$auto_ar			= ($invoice_dr == 'yes') ? true : false;

		$invoice 			= (!empty($invoiceno)) ? $invoiceno : $this->input->post('voucherno');
		$result 			= $this->invoice->generateReceivable($invoice,$auto_ar,$trigger);

		if($result){
			$code = 1;
			$msg = "success";
		}else{
			$code = 0;
			$msg = "error";
		}

		return array(
			'code' => $code,
			'result' => $msg
		);
	}

	public function print_invoice($voucherno) 
	{
		$companycode = $this->companycode;
		
		/** HEADER INFO **/

		$docinfo_table  = "salesinvoice as si";
		$docinfo_fields = array('si.transactiondate AS documentdate', 'si.voucherno AS voucherno',
								"CONCAT( first_name, ' ', last_name) AS customer",
								"'' AS referenceno",'si.netamount AS amount','si.remarks as remarks',
								'si.discounttype as disctype','si.discountamount as discount', 
								'si.amount as net','si.vat_sales as vat_sales','si.vat_exempt as vat_exempt',
								'si.taxamount as vat','si.vat_zerorated as zerorated',
								'drno', 'pl.voucherno plno', 'pl.source_no sono');
		$docinfo_join   = "partners as p ON p.partnercode = si.customer AND p.companycode = si.companycode LEFT JOIN deliveryreceipt dr ON dr.voucherno = si.drno AND dr.companycode = si.companycode LEFT JOIN packinglist pl ON pl.voucherno = dr.source_no AND pl.companycode = dr.companycode";
		$docinfo_cond 	= "si.voucherno = '$voucherno'"; 

		$documentinfo  	= $this->invoice->retrieveData($docinfo_table, $docinfo_fields, $docinfo_cond, $docinfo_join);
		$documentinfo	= $documentinfo[0]; 

		$customer 	    = $documentinfo->customer;

		/** HEADER INFO - END**/

		/** DETAILS INFO **/

		$docdet_table   = "salesinvoice_details as dtl";
		//Jasmine - rearranged positions
		$docdet_fields  = array("dtl.itemcode as itemcode", "dtl.detailparticular as description","dtl.issueqty as quantity" ,"UPPER(dtl.issueuom) as uom", "unitprice as price", "taxamount","amount as amount", "taxrate", "itemdiscount");
		//$docdet_fields  = array("dtl.itemcode as itemcode", "dtl.detailparticular as description", "dtl.issueqty as quantity","unitprice as price","amount as amount");
		$docdet_cond    = " dtl.voucherno = '$voucherno' ";
		$docdet_join 	= "";
		$docdet_groupby = "";
		$docdet_orderby = " dtl.linenum ";
		
		$documentcontent = $this->invoice->retrieveData($docdet_table, $docdet_fields, $docdet_cond, $docdet_join, $docdet_orderby, $docdet_groupby);

		/** DETAILS INFO --END**/
		
		/** CUSTOMER DETAILS **/

		$customercode 		= $this->invoice->getValue("salesinvoice", array('customer')," voucherno = '$voucherno'");

		$custField			= array('partnername customer', 'address1 address', 'tinno', 'terms', 'mobile contactno');
		$customerdetails	= $this->invoice->retrieveData("partners",$custField," partnertype = 'customer' AND partnercode = '".$customercode[0]->customer."'");
		$customerdetails	= $customerdetails[0];

		/** CUSTOMER DETAILS --END**/

		$documentdetails	= array(
			'Date'	=> $this->date->dateFormat($documentinfo->documentdate),
			'SI #'	=> $voucherno,
			'SO #'	=> $documentinfo->sono,
			'DR #'	=> $documentinfo->drno,
			'TERMS'	=> $customerdetails->terms
		);

		$print = new sales_print_model();
		$print->setDocumentType('Sales Invoice')
				->setFooterDetails(array('Approved By', 'Checked By'))
				->setCustomerDetails($customerdetails)
				->setDocumentDetails($documentdetails)
				->addTermsAndCondition()
				->addReceived();

		$print->setHeaderWidth(array(30, 50, 20, 20, 30, 20, 30))
				->setHeaderAlign(array('C', 'C', 'C', 'C', 'C', 'C', 'C'))
				->setHeader(array('Item Code', 'Description', 'Quantity', 'UOM', 'Price', 'Tax', 'Amount'))
				->setRowAlign(array('L', 'L', 'R', 'L', 'R', 'R', 'R'))
				->setSummaryWidth(array('170', '30'));

		$detail_height = 28;


		$vatable_sales	= 0;
		$vat_exempt		= 0;
		$discount		= 0;
		$tax			= 0;
		$total_amount	= 0;
		foreach ($documentcontent as $key => $row) {
			if ($key % $detail_height == 0) {
				$print->drawHeader();
			}

			$vatable_sales	+= ($row->taxrate) ? $row->amount : 0;
			$vat_exempt		+= ($row->taxrate) ? 0 : $row->amount;
			$discount		+= $row->itemdiscount;
			$tax			+= $row->taxamount;
			$total_amount	+= 0;
			$row->quantity	= number_format($row->quantity);
			$row->price		= number_format($row->price, 2);
			$row->amount	= number_format($row->amount, 2);
			$row->taxamount	= number_format($row->taxamount, 2);
			$print->addRow($row);
			if (($key + 1) % $detail_height == 0) {
				$total_amount = $vatable_sales + $vat_exempt - $discount + $tax;
				$summary = array(
					'VATable Sales'		=> number_format($vatable_sales, 2),
					'VAT-Exempt Sales'	=> number_format($vat_exempt, 2),
					'Total Sales'		=> number_format($vatable_sales + $vat_exempt, 2),
					'Discount'			=> number_format($discount, 2),
					'Tax'				=> number_format($tax, 2),
					'Total Amount'		=> number_format($total_amount, 2)
				);
				$print->drawSummary($summary);
				$vatable_sales	= 0;
				$vat_exempt		= 0;
				$discount		= 0;
				$tax			= 0;
				$total_amount	= 0;
			}
		}
		$total_amount = $vatable_sales + $vat_exempt - $discount + $tax;
		$summary = array(
			'VATable Sales'		=> number_format($vatable_sales, 2),
			'VAT-Exempt Sales'	=> number_format($vat_exempt, 2),
			'Total Sales'		=> number_format($vatable_sales + $vat_exempt, 2),
			'Discount'			=> number_format($discount, 2),
			'Tax'				=> number_format($tax, 2),
			'Total Amount'		=> number_format($total_amount, 2)
		);
		$print->drawSummary($summary);

		$print->drawPDF('Sales Invoice - ' . $voucherno);
	}

	public function ajax($task,$type='')
	{
		header('Content-type: application/json');
		
		if ($task == 'save_temp_data') 
		{
			$result = $this->add();
		}
		else if ($task == 'edit') 
		{
			$result = $this->update_data();
		}
		else if ($task == 'delete_row') 
		{
			$result = $this->delete_row();
		}
		else if ($task == 'ajax_list') 
		{
			$result = $this->invoice_listing();
		}
		else if ($task == 'get_value') 
		{
			$result = $this->get_value();
		}
		else if ($task == 'save_data') 
		{
			$result = $this->save_data($type);
		}
		else if( $task == 'check_if_taxcode' )
		{
			$result = $this->check_if_taxcode();
		}
		else if( $task == 'get_item_details' )
		{
			$result = $this->get_details('item');
		}
		else if( $task == 'ajax_delete' )
		{
			$result = $this->delete_invoice();
		}
		else if( $task == 'get_deliveries' )
		{
			$result = $this->get_deliveries();
		}
		else if( $task == 'apply_approve' )
		{
			$result = $this->generate_receivable($type);
		}
		else if( $task == 'ajax_load_delivery_list')
		{
			$result = $this->ajax_load_delivery_list($type);
		}

		echo json_encode($result); 
	}

	private function get_deliveries()
	{
		$drno 		= $this->input->post('code');
		$result 	= '';
		$deliveries = $this->invoice->retrieveDeliveries($drno);

		$customer 	= $deliveries['header']->customer;
		$notes 		= $deliveries['header']->remarks;

		$item_entry_data    = array("itemcode ind","CONCAT(itemcode,' - ',itemname) val");
		$itemcodes 			= $this->invoice->getValue("items", $item_entry_data,"stat = 'active'","itemcode");

		$tax_codes 			= $this->invoice->getTaxCode('VAT');
		
		if(!is_null($deliveries['details'])){
			$row = 1;
			foreach($deliveries['details'] as $ind => $val)
			{
				$itemcode 			= $val->itemcode;
				$detailparticular 	= $val->detailparticular;
				$quantity 			= $val->issueqty;
				$unitprice 			= $val->unitprice;
				$taxcode 			= $val->taxcode;
				$taxrate 			= $val->taxrate;
				$taxamount 			= $val->taxamount;
				$amount 			= $val->amount;
				$uom 				= strtoupper($val->issueuom);
				$itemdiscount 		= 0;
				$discountedamount 	= 0;

				$result 	.= '<tr class="clone" valign="middle">';

				$result 	.= '<td>';
				$result 	.= $this->ui->formField('dropdown')
										->setPlaceholder('Select One')
										->setSplit('	', 'col-md-12')
										->setName("itemcode[".$row."]")
										->setId("itemcode[".$row."]")
										->setList($itemcodes)
										->setClass('itemcode')
										->setValue($itemcode)
										->draw(true);
				$result     .= '</td>';

				$result 	.= '<td>';
				$result 	.= $this->ui->formField('text')
									->setSplit('', 'col-md-12')
									->setName('detailparticulars['.$row.']')
									->setId('detailparticulars['.$row.']')
									->setAttribute(
										array(
											"maxlength" => "100"
										)
									)
									->setValue($detailparticular)
									->draw(true);
				$result     .= '</td>';

				$result 	.= '<td>';
				$result 	.= $this->ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('quantity['.$row.']')
										->setId('quantity['.$row.']')
										->setClass('quantity text-right')
										->setAttribute(
											array(
												"maxlength" => "20",
												"readOnly"  => "readOnly"
											)
										)
										->setValue(number_format($quantity,0))
										->draw(true);
				$result     .= '</td>';

				$result 	.= '<td>';
				$result 	.= $this->ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('unit['.$row.']')
										->setId('unit['.$row.']')
										->setValue($uom)
										->draw(false);
				$result     .= '</td>';

				$result 	.= '<td>';
				$result 	.= $this->ui->formField('text')
										->setSplit('', 'col-md-12')
										->setName('itemprice['.$row.']')
										->setId('itemprice['.$row.']')
										->setClass("text-right price")
										->setAttribute(
											array(
												"maxlength" => "20"
											)
										)
										->setValue(number_format($unitprice,2))
										->draw(true);
				$result     .= '</td>';

				$result 	.= '<td>';
				$result 	.= $this->ui->formField('dropdown')
										->setSplit('', 'col-md-12')
										->setName('taxcode['.$row.']')
										->setId('taxcode['.$row.']')
										->setClass("taxcode")
										->setAttribute(
											array(
												"maxlength" => "20",
												"disabled" => true
											)
										)
										->setList($tax_codes)
										// ->addHidden()
										->setValue($taxcode)
										->setNone("none")
										->draw(true);
				$result 	.= '<input id = "taxrate['.$row.']" name = "taxrate['.$row.']" type = "hidden" value="'.$taxrate.'">';
				$result 	.= '<input id = "taxamount['.$row.']" name = "taxamount['.$row.']" type = "hidden" value="'.$taxamount.'">';
				$result     .= '</td>';

				$result 	.= '<td>';
				$result 	.= $this->ui->formField('text')
									->setSplit('', 'col-md-12')
									->setName('amount['.$row.']')
									->setId('amount['.$row.']')
									->setClass("text-right amount")
									->setAttribute(
										array(
											"maxlength" => "20"
										)
									)
									->setValue(number_format($amount,2))
									->draw(true);
				$result 	.= '<input id = "h_amount['.$row.']" name = "h_amount['.$row.']" type = "hidden" >';
				$result 	.= '<input id = "itemdiscount['.$row.']" name = "itemdiscount['.$row.']" type = "hidden" value="'.$itemdiscount.'">';
				$result 	.= '<input id = "discountedamount['.$row.']" name = "discountedamount['.$row.']" type = "hidden" value="'.$discountedamount.'">';
				$result     .= '</td>';

				$result     .= '<td class="text-center">';
				//onClick="confirmDelete('.$row.');"
				$result     .= '<button type="button" class="btn btn-danger btn-flat confirm-delete disabled" data-id="'.$row.'" name="chk[]" style="outline:none;" ><span class="glyphicon glyphicon-trash"></span></button>';
				$result     .= '</td>';
				
				$result 	.= '</tr>';

				$row++;
			}
		}

		return array(
			'customer' 	=> $customer,
			'notes' 	=> $notes,
			'items'		=> $result
		);
	}

	private function get_details($type)
	{
		$itemcode 	= $this->input->post('itemcode');

		$result 	= $this->invoice->retrieveItemDetails($itemcode);
		
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

			$result = $this->invoice->getValue("partners", $data_var, $cond);

			$dataArray = array( "address" => $result[0]->address1, "tinno" => $result[0]->tinno, "terms" => $result[0]->terms );
		}
		else if($data_cond == "getTaxRate")
		{
			$data_var = array("taxrate");

			$taxcode  = $this->input->post("taxcode");

			$cond 	  = " fstaxcode = '$taxcode' ";

			$result = $this->invoice->getValue("fintaxcode", $data_var, $cond);

			$taxrate = ($result) ? $result[0]->taxrate : 0;

			$dataArray = array("taxrate" => $taxrate);

		}
		
		return $dataArray;
	}

	private function save_data($type)
	{
		$result    = "";

		if($type == "customerdetails")
		{
			$data_var  		= array("terms", "tinno", "address1", "querytype");
			$data_post 		= $this->input->post($data_var);
			
			$data_id 		= $this->input->post("id");
			$data_condition = " partnercode = '$data_id' ";

			/**
			* Save Customer Detials
			*/
			$result = $this->invoice->saveData("partners", $data_post, $data_condition);

			if($result){
				$code = 1;
				$msg = "success";
			}else{
				$code = 0;
				$msg = "error";
			}
		}
		else if ($type == 'new_customer')
		{
			$data_var  = array("partnercode", "partnercode", "email", "address1", "businesstype", "tinno", "terms", "partnertype","querytype");
			$data_post = $this->input->post($data_var);
			
			/**
			* Save New Customer
			*/
			$result = $this->invoice->saveData("partners", $data_post);

			if($result){
				$code = 1;
				$msg = "success";
			}else{
				$code = 0;
				$msg = "error";
			}
		}
		else if ($type == 'temp_data')
		{
			$data_post = $this->input->post();
			$msg 	   = "";
			
			$result    = $this->invoice->insertData($data_post);

			if($result){
				$code = 1;
				$msg = $this->invoice->getInvoice();
			}else{
				$code = 0;
				$msg = "error";
			}
				
		}
		
		$dataArray = array(
			"code" => $code,
			"msg" => $msg
		);

		return $dataArray;
	}

	private function update_data()
	{
		$save_status 	= $this->input->post('save');
		$data_post 		= $this->input->post();
		$msg 	   		= "";

		$result    		= $this->invoice->insertData($data_post);

		if($result){
			$code 		= 1;
			$msg 		= $save_status;
			/**
			 * Auto Post / Generate AR
			 */
			$this->generate_receivable('yes');
		}else{
			$code 		= 0;
			$msg 		= "error";
		}

		$dataArray = array(
			"code" => $code,
			"msg" => $msg
		);

		return $dataArray;
	}

	private function delete_row()
	{
		$table 		= $this->input->post('table');
		$linenum	= $this->input->post('linenum'); 
		$voucher 	= $this->input->post('voucherno');

		$cond 		= " voucherno = '$voucher' AND linenum = '$linenum' ";

		$result = $this->invoice->deleteData($table, $cond);

		if($result)
			$msg = "success";
		else
			$msg = "The system has encountered an error in deleting. Please contact admin to fix this issue.";

		return	$dataArray = array( "msg" => $msg );
	}

	private function delete_invoice()
	{
		//$voucher 		= $this->input->post('voucherno');
		$vouchers 		= $this->input->post('delete_id');
		$invoices 		= "'" . implode("','", $vouchers) . "'";
		$data['stat'] 	= "cancelled";
		
		$cond 			= " voucherno IN ($invoices) ";

		$result 		= $this->invoice->updateData($data, "salesinvoice", $cond);

		if( $result )
		{
			$result 	= $this->invoice->updateData($data, "salesinvoice_details", $cond);
		}

		if($result){
			$code 	= 1; 
			$msg 	= "success";

			$dr_info 				 	= array();
			$dr_info['stat']			= 'Delivered';
			$dr_condition				= " voucherno IN (select sls.drno from salesinvoice sls where sls.voucherno IN($invoices)) AND stat = 'With Invoice' ";
			$updateDrRecord				= $this->invoice->updateData($dr_info,"deliveryreceipt",$dr_condition);
			$updateDrRecord				= $this->invoice->updateData($dr_info,"deliveryreceipt_details",$dr_condition);

			$ar_info 				 	= array();
			$ar_info['stat']			= 'cancelled';
			$ar_condition				= " vsourceno IN ($invoices) AND stat = 'posted' ";
			$updateArRecord				= $this->invoice->updateData($ar_info,"accountsreceivable"," sourceno IN ($invoices) AND stat = 'posted' ");
			$updateArRecord				= $this->invoice->updateData($ar_info,"ar_details"," voucherno IN(select ar.voucherno from accountsreceivable ar where ar.sourceno IN($invoices)) AND stat = 'posted' ");

		}else{
			$code 	= 0; 
			$msg 	= "Sorry, the system was unable to cancel the invoice";
		}

		return array(
					"code" => $code,
				 	"msg" => $msg 
				);
	}

	private function ajax_load_delivery_list() {
		$customer	= $this->input->post('customer');
		$search		= $this->input->post('search');
		$list		= $this->invoice->getDeliveryList($customer,$search);
		$table		= '';
		if (empty($list->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($list->result as $key => $row) {
			$table .= '<tr data-id="' . $row->voucherno . '">';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->notes . '</td>';
			$table .= '</tr>';
		}
		$list->table = $table;
		// return array(
		// 	'table' => $table
		// );
		return $list;
	}

	private function date($date)
	{
		return date("M d, Y",strtotime($date));
	}

	private function amount($amount)
	{
		return number_format($amount,2);
	}
}
?>