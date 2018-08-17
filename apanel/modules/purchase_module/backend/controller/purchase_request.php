<?php
class controller extends wc_controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->url 			    = new url();
		$this->pr 				= new purchase_request();
		$this->seq 				= new seqcontrol();
		$this->input            = new input();
		$this->ui 			    = new ui();
		$this->restrict 		= new purchase_restriction_model();
		$this->view->title      = 'Purchase Request';
		$this->show_input 	    = true;
		$this->logs  			= new log;
		$this->inventory_model	= $this->checkoutModel('inventory_module/inventory_model');

		$session                = new session();
		$this->companycode      = $session->get('companycode');
		
		$this->fields = array(
				'voucherno',
				'requestor',
				'documentno',
				'remarks',
				'tinno',
				'address1',
				'terms',
				'transactiondate',
				'due_date',
				'department'
			);
	}

	public function listing()
	{
		$data['ui'] 				= $this->ui;
	
		$data['show_input'] 		= true;

		$data['date_today'] 		= date("M d, Y");
		
		$data['requestor_list'] 		= $this->pr->retrieverequestorList();
		
		$data["payment_mode_list"] 	= $this->pr->getOption("payment_type");
		$data["cash_account_codes"] = $this->pr->retrieveCashAccountClassList();

		$this->view->load('purchase_request/purchase_requestlist', $data);
	}

	public function create()
	{
		$data 					= $this->input->post($this->fields);

		// Closed Date
		$close_date 			= $this->restrict->getClosedDate();
		$data['close_date']		= $close_date;
		
		$data['requestor_list'] = $this->pr->retrieverequestorList();
		$data['proforma_list'] 	= $this->pr->retrieveProformaList();
		$data["business_type"] 	= $this->pr->getOption("businesstype");
		$data["vat_type"] 		= $this->pr->getOption("vat_type");
		$data["tax_codes"] 		= $this->pr->getTaxCode('VAT');
		$data['percentage'] 	= "";
		$data['h_disctype'] 	= "perc";

		$curr_type_data         = array("currencycode ind", "currency val");
		$data["currency_codes"] = $this->pr->getValue("currency", $curr_type_data,'','currencycode');

		$cc_entry_data          = array("itemcode ind","CONCAT(itemcode, ' - ', itemname) val");
		$data["itemcodes"] 		= $this->pr->getValue("items", $cc_entry_data,'',"itemcode");
		$data["itemnames"] 		= $this->pr->getValue("items", $cc_entry_data,'',"itemname");

		$acc_entry_data          = array("accountname ind","CONCAT(segment5,' - ', accountname )  val");
		$acc_entry_cond          = "accounttype != 'P'";
		$data["account_ entries"] = $this->pr->getValue("chartaccount", $acc_entry_data,$acc_entry_cond, "segment5");

		$data['transactiondate'] = $this->date->dateFormat();

		/**ADD NEW requestor**/
		
		// Retrieve business type list
		$bus_type_data                = array("code ind", "value val");
		$bus_type_cond                = "type = 'businesstype'";
		$data["business_type_list"]   = $this->pr->getValue("wc_option", $bus_type_data, $bus_type_cond, false);

		// Retrieve generated ID
		$gen_value               = $this->pr->getValue("purchaserequest", "COUNT(*) as count", "voucherno != ''");
		$data["generated_id"]    = (!empty($gen_value->count)) ? 'TMP_'.($gen_value->count + 1) : 'TMP_1';

		$data['ui'] 			= $this->ui;
		$data['show_input'] 	= true;		
		$data['ajax_post'] 		= "voucher=".$data["generated_id"];
		$data['task'] 			= "create";
		$data["row_ctr"] 		= 0;
		$data['cmp'] 			= $this->companycode;
		$data['restrict_req'] 	= false;
		//Finalize Saving	
		$save_status 			= $this->input->post('save');
		//echo $save_status;
		if( $save_status == "final" || $save_status == "final_preview" || $save_status == "final_new" )
		{
			$voucherno 				= $this->input->post('h_voucher_no');	
			$isExist 				= $this->pr->getValue("purchaserequest", array("voucherno"), "voucherno = '$voucherno'");
			
			if($isExist[0]->voucherno)
			{
				/** RETRIEVE DATA **/
				$retrieved_data 		= $this->pr->retrieveExistingREQ($voucherno);
				$data["voucherno"]       = $retrieved_data["header"]->voucherno;
				$data["requestor"]      = $retrieved_data["header"]->requestor;
				$data["department"]    	 = $retrieved_data["header"]->department;
				$data["transactiondate"] = date('M d,Y', strtotime($retrieved_data["header"]
				->transactiondate));
				//Footer Data
				$data['t_subtotal'] 	 = $retrieved_data['header']->amount;
				$data['t_discount'] 	 = $retrieved_data['header']->discountamount;
				$data['t_total'] 	 	 = $retrieved_data['header']->netamount;

				$discounttype 		 	 = $retrieved_data['header']->discounttype;
				$data['percentage'] 	 = "";
				$data['h_disctype'] 	 = $discounttype;

				//Vendor Data
				// $data["terms"] 		 	 = $retrieved_data["requestor"]->terms;
				// $data["tinno"] 		 	 = $retrieved_data["requestor"]->tinno;
				// $data["address1"] 		 = $retrieved_data["requestor"]->address1;
				
				//Details
				$data['details'] 		 = $retrieved_data['details'];

				/**UPDATE TABLES FOR FINAL SAVING**/
				$generatedvoucher			= $this->seq->getValue('REQ', $this->companycode); 
			
				$update_info				= array();
				$update_info['voucherno']	= $generatedvoucher;
				$update_info['stat']		= 'open';
				$update_condition			= "voucherno = '$voucherno'";
				$updateTempRecord			= $this->pr->updateData($update_info,"purchaserequest",$update_condition);

				$this->logs->saveActivity("Create Purchase Request [$generatedvoucher] ");

				$updateTempRecord			= $this->pr->updateData($update_info,"purchaserequest_details",$update_condition);

				if( $updateTempRecord && $save_status == 'final' )
				{
					// $this->url->redirect(BASE_URL . 'purchase/purchase_request');
					$data['modal_script'] =  
					"<script type='text/javascript'>
						$(function() {
							$('#delay_modal').modal('show');
							setTimeout(function() {
								window.location = '" . BASE_URL . 'purchase/purchase_request' . "';
							}, 1000)	
						});
					</script>";
						// window.location = '" . BASE_URL . 'purchase/purchase_request' . "';
				}
				else if( $updateTempRecord && $save_status == 'final_preview' )
				{
					$this->url->redirect(BASE_URL . 'purchase/purchase_request/view/'.$generatedvoucher);
				}
				else if( $updateTempRecord && $save_status == 'final_new' )
				{
					$this->url->redirect(BASE_URL . 'purchase/purchase_request/create');
				}
			}

		}

		$this->view->load('purchase_request/purchase_request', $data);
	}

	public function edit($voucherno)
	{
		$retrieved_data 		= $this->pr->retrieveExistingREQ($voucherno);
		
		// Closed Date
		$close_date 			= $this->restrict->getClosedDate();
		$data['close_date']		= $close_date;
		
		$data['requestor_list'] = $this->pr->retrieverequestorList();
		$data['proforma_list'] 	= $this->pr->retrieveProformaList();
		$data["business_type"] 	= $this->pr->getOption("businesstype");
		$data["vat_type"] 		= $this->pr->getOption("vat_type");
		$data["tax_codes"] 		= $this->pr->getTaxCode('VAT');

		$curr_type_data         = array("currencycode ind", "currency val");
		$data["currency_codes"] = $this->pr->getValue("currency", $curr_type_data,'','currencycode');

		$cc_entry_data          = array("itemcode ind","CONCAT(itemcode, ' - ', itemname) val");
		$data["itemcodes"] 		= $this->pr->getValue("items", $cc_entry_data,'',"itemcode");

		$acc_entry_data          = array("accountname ind","CONCAT(segment5,' - ', accountname )  val");
		$acc_entry_cond          = "accounttype != 'P'";
		$data["account_entries"] = $this->pr->getValue("chartaccount", $acc_entry_data,$acc_entry_cond, "segment5");

		/**ADD NEW requestor**/
		
		// Retrieve business type list
		$bus_type_data                = array("code ind", "value val");
		$bus_type_cond                = "type = 'businesstype'";
		$data["business_type_list"]   = $this->pr->getValue("wc_option", $bus_type_data, $bus_type_cond, false);

		$data["generated_id"] 	= $voucherno;
		$data["sid"] 		    = $voucherno;

		$data['ui'] 			= $this->ui;
		$data['show_input'] 	= true;
		$data['ajax_post'] 		= "&voucher=$voucherno";
		$data['task'] 			= "edit";
		$data["row_ctr"] 		= 0;
		$data['cmp'] 			= $this->companycode;

		// Header Data
		$transactiondate 		 = $retrieved_data["header"]->transactiondate;
		$data["voucherno"]       = $retrieved_data["header"]->voucherno;
		$data["requestor"]       = $retrieved_data["header"]->requestor;
		$data["remarks"]       	 = $retrieved_data["header"]->remarks;
		$data["department"]      = $retrieved_data["header"]->department;
		$data["due_date"]    	 = date('M d,Y', strtotime($retrieved_data["header"]->duedate));
		$data["transactiondate"] = date('M d,Y', strtotime($transactiondate));

		$this->logs->saveActivity("Update Purchase Request [$voucherno] ");
		
		//Footer Data
		$data['t_subtotal'] 	 = $retrieved_data['header']->amount;
		$data['t_discount'] 	 = $retrieved_data['header']->discountamount;
		$data['t_total'] 	 	 = $retrieved_data['header']->netamount;
		$data['t_vat'] 			 = $retrieved_data['header']->taxamount;

		$discounttype 		 	 = $retrieved_data['header']->discounttype;
		$data['percentage'] 	 = "";
		$data['h_disctype'] 	 = $discounttype;

		//Vendor Data
		// $data["terms"] 		 	 = $retrieved_data["requestor"]->terms;
		// $data["tinno"] 		 	 = $retrieved_data["requestor"]->tinno;
		// $data["address1"] 		 = $retrieved_data["requestor"]->address1;
		
		//Details
		$data['details'] 		 = $retrieved_data['details'];
		$restrict_req 			 = $this->restrict->setButtonRestriction($transactiondate);
		$data['restrict_req'] 	 = $restrict_req;
		$this->view->load('purchase_request/purchase_request', $data);
	}

	public function view($voucherno)
	{
		$retrieved_data 		= $this->pr->retrieveExistingREQ($voucherno);

		$close_date 			= $this->restrict->getClosedDate();
		$data['close_date']		= $close_date;
		
		$data['requestor_list'] 	= $this->pr->retrieverequestorList();
		$data['proforma_list'] 	= $this->pr->retrieveProformaList();
		$data["business_type"] 	= $this->pr->getOption("businesstype");
		$data["vat_type"] 		= $this->pr->getOption("vat_type");
		$data["tax_codes"] 		= $this->pr->getTaxCode('VAT');

		$curr_type_data         = array("currencycode ind", "currency val");
		$data["currency_codes"] = $this->pr->getValue("currency", $curr_type_data,'','currencycode');

		$cc_entry_data          = array("itemcode ind","CONCAT(itemcode, ' - ', itemname) val");
		$data["itemcodes"] 		= $this->pr->getValue("items", $cc_entry_data,'',"itemcode");

		$acc_entry_data          = array("accountname ind","CONCAT(segment5,' - ', accountname )  val");
		$acc_entry_cond          = "accounttype != 'P'";
		$data["account_entries"] = $this->pr->getValue("chartaccount", $acc_entry_data,$acc_entry_cond, "segment5");

		$data["generated_id"] 	= $voucherno;
		$data["sid"] 		    = $voucherno;

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
		$data["requestor"]       = $retrieved_data["header"]->requestor;
		$data["remarks"]      	 = $retrieved_data["header"]->remarks;
		$data["department"]      = $retrieved_data["header"]->department;
		$data["due_date"]    	 = $this->date->dateFormat($duedate);
		$data["transactiondate"] = $this->date->dateFormat($transactiondate);
		
		//Footer Data
		$data['t_subtotal'] 	 = $retrieved_data['header']->amount;
		$data['t_discount'] 	 = $retrieved_data['header']->discountamount;
		$data['t_total'] 	 	 = $retrieved_data['header']->netamount;
		$data['t_vat'] 			 = $retrieved_data['header']->taxamount;
		// $data['t_vatsales'] 	 = $retrieved_data['header']->vat_sales;
		// $data['t_vatexempt'] 	 = $retrieved_data['header']->vat_exempt;

		$discountamount 		 = $retrieved_data['header']->discountamount;
		$discounttype 		 	 = $retrieved_data['header']->discounttype;
		$data['percentage'] 	 = ($discounttype == 'perc' && $discountamount > 0 ) 	? 	"%" 	: 	"";
		$data['h_disctype'] 	 = $discounttype;

		//Vendor Data
		// $data["terms"] 		 	 = $retrieved_data["requestor"]->terms;
		// $data["tinno"] 		 	 = $retrieved_data["requestor"]->tinno;
		// $data["address1"] 		 = $retrieved_data["requestor"]->address1;
		
		//Details
		$data['details'] 		 = $retrieved_data['details'];
		
		$restrict_req 			 =	$this->restrict->setButtonRestriction($transactiondate);
		$data['restrict_req'] 	 = $restrict_req;

		$this->view->load('purchase_request/purchase_request', $data);
	}

	public function print_preview($voucherno) 
	{
		$companycode = $this->companycode;
		
		/** HEADER INFO **/

			$docinfo_table  = "purchaserequest as so";
			$docinfo_fields = array('so.transactiondate AS documentdate','so.voucherno AS voucherno',"partnername AS requestor","'' AS referenceno",'so.amount AS amount','so.remarks as remarks','so.discounttype as disctype','so.discountamount as discount', 'so.netamount as net','so.amount as amount','so.taxamount as vat');
			$docinfo_join   = "partners as p ON p.partnercode = so.requestor AND p.companycode = so.companycode";
			$docinfo_cond 	= "so.voucherno = '$voucherno'";

			$documentinfo  	= $this->pr->retrieveData($docinfo_table, $docinfo_fields, $docinfo_cond, $docinfo_join);
			$documentinfo	= $documentinfo[0]; 
			$requestor 	    = $documentinfo->requestor;

		/** HEADER INFO - END**/

		/** DETAILS INFO **/

			$docdet_table   = "purchaserequest_details as dtl";
			$docdet_fields  = array("dtl.itemcode as itemcode", "dtl.detailparticular as description", "dtl.receiptqty as quantity","UPPER(dtl.receiptuom) as uom");
			$docdet_cond    = "dtl.voucherno = '$voucherno'";
			$docdet_join 	= "";
			$docdet_groupby = "";
			$docdet_orderby = "dtl.linenum";
			
			$documentcontent = $this->pr->retrieveData($docdet_table, $docdet_fields, $docdet_cond, $docdet_join, $docdet_orderby, $docdet_groupby);
			// var_dump($documentdetails);

		/** DETAILS INFO --END**/
		
		/** requestor DETAILS **/

			$requestorcode 		=	$this->pr->getValue("purchaserequest", array('requestor')," voucherno = '$voucherno'");

			$custField			= array('mobile contactno', "CONCAT(firstname, ' ',lastname) requestor,email");
			$requestordetails	= $this->pr->retrieveData("wc_users",$custField," username = '".$requestorcode[0]->requestor."'");
			$requestordetails	= $requestordetails[0];

		/** requestor DETAILS --END**/

		$documentdetails	= array(
			'Date'		=> $this->date->dateFormat($documentinfo->documentdate),
			'REQ #'	=> $voucherno
		);

		$print = new purchase_print_model();
		$print->setDocumentType('Purchase Request')
				->setFooterDetails(array('Approved By', 'Checked By'))
				->setRequestorDetails($requestordetails)
				->setDocumentDetails($documentdetails)
				// ->addTermsAndCondition()
				->addReceived();

		$print->setHeaderWidth(array(40, 100, 30, 30))
				->setHeaderAlign(array('C', 'C', 'C', 'C'))
				->setHeader(array('Item Code', 'Description', 'Quantity', 'UOM'))
				->setRowAlign(array('L', 'L', 'R', 'L'))
				->setSummaryWidth(array('170', '30'));

		$detail_height = 37;

		$total_quantity = 0;
		foreach ($documentcontent as $key => $row) {
			if ($key % $detail_height == 0) {
				$print->drawHeader();
			}

			$total_quantity	+= $row->quantity;
			$row->quantity	= number_format($row->quantity);
			$print->addRow($row);
			if (($key + 1) % $detail_height == 0) {
				$print->drawSummary(array('Total Quantity' => number_format($total_quantity)));
				$total_quantity = 0;
			}
		}
		$print->drawSummary(array('Total Quantity' => number_format($total_quantity)));

		$print->drawPDF('Purchase Request - ' . $voucherno);
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
		else if($task == 'pr_listing') 
		{
			$result = $this->pr_listing();
		}
		else if($task == 'get_value') 
		{
			$result = $this->get_value();
		}
		else if($task == 'save_requestor')
		{
			$result = $this->save_requestor_data();
		}
		else if( $task == 'get_item_details' )
		{
			$result = $this->get_details('item');
		}
		else if( $task == 'delete_pr' )
		{
			$result = $this->delete_pr();
		}
		else if( $task == 'update_pr_accept' )
		{
			$result = $this->update_pr_accept();
		}
		else if( $task == 'update_req_decline' )
		{
			$result = $this->update_req_decline();
		}
		else if( $task == 'delete_row' )
		{
			$result = $this->delete_row();
		}
		else if( $task == 'update_statusClosed' )
		{
			$result = $this->update_statusClosed();
		}
		else if( $task == 'ajax_delete' )
		{
			$result = $this->ajax_delete();
		}
		else if( $task == 'delete_so' )
		{
			$result = $this->delete_so();
		}

		echo json_encode($result); 
	}

	private function update_statusClosed() {
		$voucherno = $this->input->post('voucherno');
		if ($voucherno) {
			$this->pr->completeREQ($voucherno);
		}
	}

	private function ajax_delete() {
		$voucherno = $this->input->post('voucherno');
		if ($voucherno) {
			$this->pr->deleteREQ($voucherno);
		}
	}

	private function pr_listing()
	{
		$posted_data 	= $this->input->post(array("daterangefilter", "requestor", "search", "filter","sort","limit"));

		$search = $this->input->post("search");

		$filter = $this->input->post("filter");
		$sort = $this->input->post("sort");
		$limit = $this->input->post("limit");

		$pagination 	= $this->pr->retrieveListing($posted_data);

		$table 	= '';

		if( !empty($pagination->result) ) :
			foreach ($pagination->result as $key => $row) {

				$requestor 	= $this->pr->getValue('wc_users',array('username','firstname','lastname')," username = '$row->requestor' ");

				$requestor_name  =	$requestor[0]->firstname . " " . $requestor[0]->lastname;

				$transactiondate 	=	$row->transactiondate;

				if($row->stat == 'locked')
				{
					$voucher_status = '<span class="label label-success">CONVERTED</span>';
				}
				else if($row->stat == 'open')
				{
					$voucher_status = '<span class="label label-info">DRAFT</span>';
				}
				else if($row->stat == 'cancelled')
				{
					$voucher_status = '<span class="label label-warning">CANCELLED</span>';
				}
				else if( $row->stat == 'expired' )
				{	
					$voucher_status = '<span class="label label-danger">EXPIRED</span>';
				}
				
				$table .= '<tr>';
				
				$restrict_req =	$this->restrict->setButtonRestriction($transactiondate);

				$element = $this->ui->loadElement('check_task')
									->addView()
									->addEdit($row->stat == 'open' && $restrict_req )
									->addDelete( ($row->stat == 'open' || $row->stat == 'expired') && $restrict_req) 
									->addPrint()
									->setLabels(array('delete'=>'Cancel'))
									->addOtherTask('Convert to PO', 'share', ($row->stat == 'open' && $restrict_req))
									->addCheckbox($row->stat == 'open' && $restrict_req)
									->setValue($row->voucherno);
									
				$dropdown = $element->draw();
				$table .= '<td align = "center">' . $dropdown . '</td>';
				$table .= '<td>' . date("M d, Y",strtotime($row->transactiondate)) . '</td>';
				$table .= '<td>' . $row->voucherno. '</td>';
				$table .= '<td>' . $requestor_name . '</td>';
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

	private function get_details($type)
	{
		$itemcode 	= $this->input->post('itemcode');

		$itemname 	= $this->input->post('itemname');

		$result 	= $this->pr->retrieveItemDetails($itemcode,$itemname);
		
		return $result;
	}

	private function get_value()
	{
		$data_cond = $this->input->post("event");
		
		// if($data_cond == "getPartnerInfo")
		// {
		// 	$data_var = array('address1', "tinno", "terms");
		// 	$code 	  = $this->input->post("code");

		// 	$cond 	  = "partnercode = '$code'";

		// 	$result = $this->pr->getValue("partners", $data_var, $cond);

		// 	$dataArray = array( "address" => $result[0]->address1, "tinno" => $result[0]->tinno, "terms" => $result[0]->terms );
		// }
		if($data_cond == "getPartnerInfo")
		{
			$data_var = array('email', "mobile");
			$code 	  = $this->input->post("code");

			$cond 	  = "username = '$code'";

			$result = $this->pr->getValue("wc_users", $data_var, $cond);

			$dataArray = array( "email" => $result[0]->email, "mobile" => $result[0]->mobile);
		}
		else if($data_cond == "exchange_rate")
		{
			$data_var = array("accountnature");
			$account  = $this->input->post("account");

			$cond 	  = "accountname = '$account'";

			$result = $this->pr->getValue("chartaccount", $data_var, $cond);

			$dataArray = array("accountnature" => $result[0]->accountnature);

		}
		
		return $dataArray;
	}
	
	private function add($final="")
	{
		$data_post 	= $this->input->post();
		$voucher 	= $this->input->post("h_voucher_no");

		if( $final == "save" )
			$result    = $this->pr->processTransaction($data_post, "create" , $voucher);
		else
			$result    = $this->pr->processTransaction($data_post, "create");


		//Finalize Saving	
		$save_status 			= $this->input->post('save');
		//echo $save_status;
		if( $save_status == "final" || $save_status == "final_preview" || $save_status == "final_new" )
		{
			$voucherno 				= $this->input->post('h_voucher_no');	
			$isExist 				= $this->pr->getValue("purchaserequest", array("voucherno"), "voucherno = '$voucherno'");
			
			if($isExist[0]->voucherno)
			{
				/** RETRIEVE DATA **/
				$retrieved_data 		= $this->pr->retrieveExistingREQ($voucherno);
				$data["voucherno"]       = $retrieved_data["header"]->voucherno;
				$data["requestor"]      = $retrieved_data["header"]->requestor;
				$data["department"]    	 = $retrieved_data["header"]->department;
				$data["transactiondate"] = date('M d,Y', strtotime($retrieved_data["header"]
				->transactiondate));
				//Footer Data
				$data['t_subtotal'] 	 = $retrieved_data['header']->amount;
				$data['t_discount'] 	 = $retrieved_data['header']->discountamount;
				$data['t_total'] 	 	 = $retrieved_data['header']->netamount;

				$discounttype 		 	 = $retrieved_data['header']->discounttype;
				$data['percentage'] 	 = "";
				$data['h_disctype'] 	 = $discounttype;

				//Vendor Data
				// $data["terms"] 		 	 = $retrieved_data["requestor"]->terms;
				// $data["tinno"] 		 	 = $retrieved_data["requestor"]->tinno;
				// $data["address1"] 		 = $retrieved_data["requestor"]->address1;
				
				//Details
				$data['details'] 		 = $retrieved_data['details'];

				/**UPDATE TABLES FOR FINAL SAVING**/
				$generatedvoucher			= $this->seq->getValue('REQ', $this->companycode); 
			
				$update_info				= array();
				$update_info['voucherno']	= $generatedvoucher;
				$update_info['stat']		= 'open';
				$update_condition			= "voucherno = '$voucherno'";
				$this->logs->saveActivity("Create Purchase Request [$generatedvoucher] ");
				$updateTempRecord			= $this->pr->updateData($update_info,"purchaserequest",$update_condition);
				$updateTempRecord			= $this->pr->updateData($update_info,"purchaserequest_details",$update_condition);
				$data = array("msg" => 'success', "voucher" => $generatedvoucher);
			}

		} else {
			$data = false;
		}
		return $data;
	}
	
	private function update()
	{
		$data_post 	= $this->input->post();

		$voucher 	= $this->input->post("voucher");

		/**
		* Update Database
		*/
		$result = $this->pr->processTransaction($data_post, "edit", $voucher);

		if(!empty($result))
			$msg = $result;
		else
			$msg = "success";

		return $dataArray = array( "msg" => $msg, "voucher" => $voucher );
	}

	private function cancel()
	{
		$voucher 	= 	$this->input->post("voucher");

		$result 	=	$this->pr->delete_temp_transactions($voucher, "purchaserequest", "purchaserequest_details");

		if($result == '1')
			$msg = "success";
		else
			$msg = "Failed to Cancel.";

		return $dataArray = array( "msg" => $msg );
	}

	private function delete_pr()
	{
		$voucher 		=	$this->input->post('voucherno');
		$data['stat'] 	=	"cancelled";
		
		$cond 			=	" voucherno = '$voucher' ";

		$result 	=	$this->pr->updateData($data, "purchaserequest", $cond);

		$this->logs->saveActivity("Cancelled Purchase Request [$voucher] ");

		if( $result )
		{
			$result 	=	$this->pr->updateData($data, "purchaserequest_details", $cond);
		}

		if($result == '1')
			$msg = "success";
		else
			$msg = "Failed to Cancel.";

		return $dataArray = array( "msg" => $msg );
	}

	private function update_req_decline()
	{
		$voucher 		=	$this->input->post('voucherno');
		$data['stat'] 	=	"posted";
		
		$cond 			=	" voucherno = '$voucher' ";

		$result 	=	$this->pr->updateData($data, "purchaserequest", $cond);

		if( $result )
		{
			$result 	=	$this->pr->updateData($data, "purchaserequest_details", $cond);
		}

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

		$result = $this->pr->deleteData($table, $cond);

		if($result)
			$msg = "success";
		else
			$msg = "The system has encountered an error in deleting. Please contact admin to fix this issue.";

		return	$dataArray = array( "msg" => $msg );
	}

	private function save_requestor_data()
	{
		$result    	= "";

		$data_var  	= array("partnercode", "first_name", "last_name", "email", "address1", "businesstype", "tinno", "terms");
		
		$data_post 	= $this->input->post($data_var);
			
		$result 	= $this->pr->insertrequestor($data_post);
		// var_dump($result);

		$requestorcode 	= $this->input->post('partnercode');

		// 

		if($result)
		{
			$this->logs->saveActivity("Purchase Order - Added New Customer [$requestorcode] ");
			$msg = "success";
		} else {
			$msg = "The system has encountered an error in saving. Please contact admin to fix this issue.";
		}

		return $dataArray = array( "msg" => $msg );

	}

	private function export_main($posted_data){
		$header = array("Date","Req No.","Vendor","Department");

		$csv = '';
		$csv .= '"' . implode('", "', $header) . '"';
		$csv .= "\n";
		
		$result = $this->pr->export_main($posted_data);

		// $totalamount 	=	0;
		

		if (!empty($result)){
			foreach ($result as $key => $row){
				
				$requestor 	= $this->pr->getValue('wc_users','username'," username = '$row->requestor' ");
				$customer_name 	=	$requestor[0]->username;


				$csv .= '"' . $row->transactiondate . '",';
				$csv .= '"' . $row->voucherno . '",';
				$csv .= '"' . $customer_name . '",';
				$csv .= '"' . $row->department . '"';
				$csv .= "\n";
			}
		}
		
		// $csv .= '"","",""," ","'. number_format($totalamount,2) .'"';
		return $csv;
	}

	private function delete_so()
	{
		$vouchers 		=	$this->input->post('delete_id');
		$po_vouchers 	= 	"'" . implode("','", $vouchers) . "'";
		
		$data['stat'] 	=	"cancelled";
		
		$cond 			=	" voucherno IN ($po_vouchers) ";

		$result 	=	$this->pr->updateData($data, "purchaserequest", $cond);

		if( $result )
		{
			$result 	=	$this->pr->updateData($data, "purchaserequest_details", $cond);
		}

		if($result == '1')
		{
			$this->logs->saveActivity("Cancelled Purchase Request [ ". implode(',',$vouchers) ." ] ");
			$msg = "success";

			if ( $result && $this->inventory_model ) {
				$this->inventory_model->generateBalanceTable();
			}
		}
		else
		{
			$msg = "Failed to Cancel.";
		}

		return $dataArray = array( "msg" => $msg );
	}


}
?>