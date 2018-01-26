<?php
class controller extends wc_controller 
{

	public function __construct()
	{
		parent::__construct();
		$this->url 			    = new url();
		$this->payment 	= new payment();
		$this->seq 				= new seqcontrol();
		$this->input            = new input();
		$this->ui 			    = new ui();
		$this->logs  			= new log;
		$this->show_input 	    = true;

		$session                = new session();
		$this->companycode      = COMPANYCODE;
		
		$this->fields = array(
				'voucherno',
				'vendor',
				'referenceno',
				'tinno',
				'address1',
				'terms',
				'apv',
				'proformacode',
				"particulars",
				"paymenttype"
			);
	}

	public function listing()
	{
		$this->view->title      = 'Disbursement Voucher';

		$data['ui'] 				= $this->ui;
	
		$data['show_input'] 		= true;

		$data['date_today'] 		= date("M d, Y");
		
		$data['vendor_list'] 		= $this->payment->retrieveVendorList();
		
		$this->view->load('payment/payment_list', $data);
	}

	public function create()
	{
		$this->view->title      = 'Disbursement Voucher';

		$data 					= $this->input->post($this->fields);
 
		$data["transactiondate"] = $this->date->dateFormat();

		$data['vendor_list'] 	= $this->payment->retrieveVendorList();
		$data['proforma_list'] 	= $this->payment->retrieveDisursementProformaList();
		$data["business_type"] 	= $this->payment->getOption("businesstype");
		$data["vat_type"] 		= $this->payment->getOption("vat_type");

		$curr_type_data         = array("currencycode ind", "currency val");
		$data["currency_codes"] = $this->payment->getValue("currency", $curr_type_data,'','currencycode');

		$cc_entry_data          = array("costcentercode ind","costcenter val");
		$data["cost_centers"] 	= $this->payment->getValue("costcenter", $cc_entry_data,'',"costcentercode");

		$data["account_entries"] 	= $this->payment->retrieveCashAccountList();

		// Retrieve generated ID
		$gen_value               = $this->payment->getValue("paymentvoucher", "COUNT(*) as count", "voucherno != ''");
		$data["generated_id"]    = (!empty($gen_value->count)) ? 'TMP_'.($gen_value->count + 1) : 'TMP_1';
		
		$data['ui'] 			= $this->ui;
		$data['show_input'] 	= true;
		$data['ajax_post'] 		= "";
		$data['task'] 			= "create";
		$data["row_ctr"] 		= 0;
		$data['cmp'] 			= $this->companycode;

		// Cash Account Options
		$cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		$cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		$cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		$cash_order_by 		 	  = "class.accountclass";
		$data["cash_account_list"] = $this->payment->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);

		//Finalize Saving
		$save_status 			= $this->input->post('save');

		if( $save_status == "final" )
		{
			$voucherno 			= $this->input->post('h_voucher_no');
			$isExist 			= $this->payment->getValue("paymentvoucher", array("voucherno"), "voucherno = '$voucherno'");

			if($isExist[0]->voucherno)
			{
				/**UPDATE MAIN TABLES**/
				$generatedvoucher			= $this->seq->getValue('DV'); 
			
				$update_info				= array();
				$update_info['voucherno']	= $generatedvoucher;
				$update_info['stat']		= 'posted';
				$update_condition			= "voucherno = '$voucherno'";

				$cheque_info 				= array();
				$cheque_info['voucherno']	= $generatedvoucher;
		
				$updateTempRecord			= $this->payment->updateData($update_info,"paymentvoucher",$update_condition);
				$updateTempRecord			= $this->payment->updateData($update_info,"pv_details",$update_condition);
				$updateTempRecord			= $this->payment->runUpdate($cheque_info, "pv_cheques", $update_condition);

				$updateTempRecord			= $this->payment->updateData($update_info,"pv_application",$update_condition);
			}

			// For Admin Logs
			$this->logs->saveActivity("Add New Disbursement Voucher [$generatedvoucher]");

			if( $updateTempRecord && $save_status == 'final' )
			{
				$this->url->redirect(BASE_URL . 'financials/payment');
			}
			else if( $updateTempRecord && $save_status == 'final_preview' )
			{
				$this->url->redirect(BASE_URL . 'financials/payment/view/'.$generatedvoucher);
			}
			else if( $updateTempRecord && $save_status == 'final_new' )
			{
				$this->url->redirect(BASE_URL . 'financials/payment/create');
			}
		}

		$this->view->load('payment/payment', $data);
	}
	
	public function edit($voucherno)
	{
		$this->view->title      = 'Disbursement Voucher';

		// echo "\nEDIT";
		$retrieved_data 		= $this->payment->retrieveExistingDV($voucherno);

		$data['vendor_list'] 	= $this->payment->retrieveVendorList();
		$data['proforma_list'] 	= $this->payment->retrieveDisursementProformaList();
		$data["business_type"] 	= $this->payment->getOption("businesstype");
		
		$data["generated_id"] 	= $voucherno;
		$data["sid"] 		    = $voucherno;

		$data['ui'] 			= $this->ui;
		$data['show_input'] 	= true;
		$data['ajax_post'] 		= "";
		$data['task'] 			= "edit";
		$data["row_ctr"] 		= 0;
		$data['cmp'] 			= $this->companycode;
	
		$data["account_entries"] 	= $this->payment->retrieveCashAccountList();

		$cc_entry_data          = array("costcentercode ind","costcenter val");
		$data["cost_centers"] 	= $this->payment->getValue("costcenter", $cc_entry_data,'',"costcentercode");

		// Header Data
		$data["voucherno"]       = $retrieved_data["header"]->voucherno;
		$data["referenceno"]     = $retrieved_data["header"]->referenceno;
		$data["vendor"]      	 = $retrieved_data["header"]->vendor;
		$data["exchangerate"]    = $retrieved_data["header"]->exchangerate;
		$data["proformacode"]    = $retrieved_data["header"]->proformacode;
		$data["transactiondate"] = $this->date->dateFormat($retrieved_data["header"]->transactiondate);
		$data["particulars"]     = $retrieved_data["header"]->particulars;
		$data["paymenttype"]     = $retrieved_data["header"]->paymenttype;

		//Vendor Data
		$data["terms"] 		 	 = $retrieved_data["vendor"]->terms;
		$data["tinno"] 		 	 = $retrieved_data["vendor"]->tinno;
		$data["address1"] 		 = $retrieved_data["vendor"]->address1;
		
		//Details
		$data['details'] 		 = $retrieved_data['details'];

		// For Cheques
		$data['rollArray'] 		 = $retrieved_data['rollArray'];

		
		// Cash Account Options
		$cash_account_fields 	  = 'chart.id ind, chart.accountname val, class.accountclass';
		$cash_account_join 	 	  = "accountclass as class USING(accountclasscode)";
		$cash_account_cond 	 	  = "(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'";
		$cash_order_by 		 	  = "class.accountclass";
		$data["cash_account_list"] = $this->payment->retrieveData("chartaccount as chart", $cash_account_fields, $cash_account_cond, $cash_account_join, $cash_order_by);

		$data_post 			= $this->input->post(array('save', "vendor", "transaction_date", "h_save_preview", "h_save_new"));
	
		if(!empty($data_post["vendor"]) && !empty($data_post["transaction_date"]))
		{
			$update_info				= array();
			
			$update_info['voucherno']	= $voucherno;
			$update_info['stat']		= 'posted';
			$update_condition			= "voucherno = '$voucherno'";

			$updateTempRecord			= $this->payment->updateData($update_info,"paymentvoucher",$update_condition);
			$updateTempRecord			= $this->payment->updateData($update_info,"pv_details",$update_condition);
			$updateTempRecord			= $this->payment->updateData($update_info,"pv_cheques",$update_condition);
			$updateTempRecord			= $this->payment->updateData($update_info,"pv_application",$update_condition);

			// For Admin Logs
			$this->logs->saveActivity("Update Disbursement Voucher [$voucherno]");

			if( !empty($data_post["save"]) )
			{
				$this->url->redirect(BASE_URL . 'financials/payment');
			}
			else if(!empty($data_post['h_save_preview']))
			{
				$this->url->redirect(BASE_URL . 'financials/payment/view/'.$voucherno);
			}
			else
			{
				$this->url->redirect(BASE_URL . 'financials/payment/create');
			}
		}

		$this->view->load('payment/payment', $data);
	}

	public function view($voucherno)
	{
		$this->view->title      = 'Disbursement Voucher';

		// echo "\nVIEW";
		$retrieved_data 	 	= $this->payment->retrieveExistingDV($voucherno);

		$data['vendor_list'] 	= $this->payment->retrieveVendorList();
		$data['proforma_list'] 	= $this->payment->retrieveDisursementProformaList();
		$data["business_type"] 	= $this->payment->getOption("businesstype");

		$data['ui'] 			= $this->ui;
		$data['show_input'] 	= false;
		$data['ajax_post'] 		= "";
		$data['task'] 			= "view";
		$data["row_ctr"] 		= 0;
		$data['cmp'] 			= $this->companycode;

		$data["generated_id"] 	= $voucherno;
		$data["sid"] 		    = $voucherno;

		// Header Data
		$data["voucherno"]       = $retrieved_data["header"]->voucherno;
		$data["referenceno"]     = $retrieved_data["header"]->referenceno;
		$data["vendor"]      	 = $retrieved_data["header"]->vendor;
		$data["exchangerate"]    = $retrieved_data["header"]->exchangerate;
		$data["exchangerate"]    = $retrieved_data["header"]->exchangerate;
		$data["proformacode"]    = $retrieved_data["header"]->proformacode;
		$data["transactiondate"] = $date = $this->date->dateFormat($retrieved_data["header"]->transactiondate);
		$data["particulars"]    = $retrieved_data["header"]->particulars;
		$data["paymenttype"]    = $retrieved_data["header"]->paymenttype;

		//Vendor Data
		$data["terms"] 		 	 = (!empty($retrieved_data["vendor"]->terms)) ? $retrieved_data["vendor"]->terms : "";
		$data["tinno"] 		 	 = (!empty($retrieved_data["vendor"]->tinno)) ? $retrieved_data["vendor"]->tinno : "";
		$data["address1"] 		 = (!empty($retrieved_data["vendor"]->address1)) ? $retrieved_data["vendor"]->address1 : "";
		$data["email"] 		 	 = (!empty($retrieved_data["vendor"]->email)) ? $retrieved_data["vendor"]->email : "";
		$data["vendor"] 		 = (!empty($retrieved_data["vendor"]->name)) ? $retrieved_data["vendor"]->name : "";

		$data["account_entries"] = $this->payment->retrieveCashAccountList();

		//Details
		$data['details'] 		 = $retrieved_data['details'];

		// Payments
		$data['payments'] 		 = $retrieved_data['payments'];

		// For Cheques
		$data['rollArray'] 		 = $retrieved_data['rollArray'];
		
		// Use in view for display cheque details
		$data['rollArrayv'] 	 = $retrieved_data['rollArrayv'];

		// var_dump($data['rollArrayv']);


		$this->view->load('payment/payment', $data);
	}

	public function ajax($task)
	{
		header('Content-type: application/json');

		if ($task == 'save_temp_data') 
		{
			$result = $this->add();
		}
		else if ($task == 'edit') 
		{
			$result = $this->update();
		}
		else if ($task == 'delete_row') 
		{
			$result = $this->delete_row();
		}
		else if ($task == 'payment_listing') 
		{
			$result = $this->payment_listing();
		}
		else if ($task == 'get_value') 
		{
			$result = $this->get_value();
		}
		else if ($task == 'save_data') 
		{
			$result = $this->save_data();
		}
		else if( $task == 'delete_dv' )
		{
			$result = $this->delete_dv();
		}
		else if( $task == 'apply_proforma' )
		{
			$result = $this->apply_proforma();
		}
		else if($task == 'cancel')
		{
			$result = $this->cancel();
		}
		else if ($task == 'delete_payments') 
		{
			$result = $this->delete_payments();
		}
		else if($task == "check")
		{
			$result = $this->check();
		}
		
		echo json_encode($result); 
	}

	private function add($final="")
	{
		$data_post 	= $this->input->post();
		$voucher 	= $this->input->post("voucherno");
		
		// var_dump($data_post);

		if( $final == "save" )
			$result    = $this->payment->processTransaction($data_post, "create" , $voucher);
		else
			$result    = $this->payment->processTransaction($data_post, "create");

		if(!empty($result))
			$msg = $result;
		else
			$msg = "success";

		// , 'redirect'=> MODULE_URL
		$dataArray = array("msg" => $msg);

		return $dataArray;
	}

	private function update()
	{
		$data_post 	= $this->input->post();

		$voucher 	= $this->input->post("voucher");

		/**
		* Update Database
		*/
		$result = $this->payment->processTransaction($data_post, "edit", $voucher);

		if(!empty($result))
			$msg = $result;
		else
		{
			$msg = "success";
		}
			

		return $dataArray = array( "msg" => $msg, "voucher" => $voucher );
	}

	private function save_data()
	{
		$result    = "";

		$data_cond = $this->input->post("h_form");
		
		if($data_cond == "vendordetail")
		{
			$data_var  = array("h_terms", "h_tinno", "h_address1", "h_querytype", "h_condition");
			$data_post = $this->input->post($data_var);
			
			/**
			* Save Vendor Detials
			*/
			$result = $this->payment->saveDetails("partners", $data_post, $this->companycode, "vendordetail");
		}

		if($result)
			$msg = "success";
		else
			$msg = "The system has encountered an error in saving. Please contact admin to fix this issue.";

		$dataArray = array( "msg" => $msg );
		
		return $dataArray;
	
	}

	private function get_value()
	{
		$data_cond = $this->input->post("event");
		
		if($data_cond == "getPartnerInfo")
		{
			$data_var = array('address1', "tinno", "terms");
			$code 	  = $this->input->post("code");

			$cond 	  = "partnercode = '$code'";
		 
			$result = $this->payment->getValue("partners", $data_var, $cond);

			$dataArray = array( "address" => $result[0]->address1, "tinno" => $result[0]->tinno, "terms" => $result[0]->terms );
		}
		else if($data_cond == "exchange_rate")
		{
			$data_var = array("accountnature");
			$account  = $this->input->post("account");

			$cond 	  = "id = '$account'";
		
			$result = $this->payment->getValue("chartaccount", $data_var, $cond);
		
			$dataArray = array("accountnature" => $result[0]->accountnature);
		}
		
		return $dataArray;
		// echo json_encode($dataArray);
	}
	
	private function payment_listing()
	{

		$posted_data 	= $this->input->post(array("daterangefilter", "vendfilter", "search", "addCond", "sort"));

		$list 	= $this->payment->retrieveListing_($posted_data);

		$table 	= '';
		$view_link 	= "";
		$edit_link  = "";
		$ap_balance = 0;

		if( !empty($list->result) ) :
			foreach ($list->result as $key => $row) 
			{
				$date        = $row->transactiondate;
				$date        = $this->date->dateFormat($date);
				$voucher     = $row->voucherno; 
				$amount	  	 = $row->amount; 
				$vendor		 = $row->vendor; 
				// $checkstat   = $row->checkstat; 
				$source   	 = $row->source; 

				// if($source == "PV")
				// {
				// 	$view_link = BASE_URL . 'financials/payment_voucher/view/'.$voucher.'';
				// 	$edit_link = BASE_URL . 'financials/payment_voucher/edit/'.$voucher.'';
				// 	$voucherlink = BASE_URL . 'financials/payment_voucher/print_preview/'.$voucher.'';
				// }
				// else if($source == "DV")
				// {
					$view_link = BASE_URL . 'financials/payment/view/'.$voucher.'';
					$edit_link = BASE_URL . 'financials/payment/edit/'.$voucher.'';
					$voucherlink = BASE_URL . 'financials/payment/print_preview/'.$voucher.'';
				// }
			
			
				$task		= '<div class="btn-group task_buttons" name="task_buttons">
								<a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
								<ul class="dropdown-menu left">
									<li>
										<a class="btn-sm" href="'.$view_link.'">
											<span class="glyphicon glyphicon-eye-open"></span> View
										</a>
									</li>';
			
				$task		.= '<li><a class="btn-sm" href="'.$edit_link.'"><span class="glyphicon glyphicon-pencil"></span> Edit</a></li>';
				
				// $task		.= '<li><a class="btn-sm" href="'.$voucherlink.'" title="Print Accounts Payable Voucher" target="_blank"><span class="glyphicon glyphicon-print"></span> Print Voucher</a></li>';

				$task		.= '<li class="divider"></li><li><a class="btn-sm record-delete" href="#deleteModal" data-toggle="modal" onClick="$(\'.modal-body #recordId\').val(\''.$voucher.'\');" data-id="'.$voucher.'"><span class="glyphicon glyphicon-trash"></span> Delete</a></li>';
				$task		.= '</ul>
							</div>';

				$table	.= '<tr class="list_row" title="View Details of AP '.$voucher.'">';
				$table	.= '<td class="text-center" style="vertical-align:middle;">'.$task.'</td>';
				$table	.= '<td class="text-center" style="vertical-align:middle;">'.$date.'</td>';
				$table	.= '<td class="text-left" style="vertical-align:middle;">&nbsp;'.$voucher.'</td>';
				$table	.= '<td class="text-left" style="vertical-align:middle;">&nbsp;'.$vendor.'</td>';
				$table	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;'.number_format($amount,2).'</td>';
				// $table	.= '<td class="text-center" style="vertical-align:middle;">&nbsp;'.$checkstat.'</td>';
				$table	.= '</tr>';
			}
		else:
			$table .= "<tr>
							<td colspan = '5' class = 'text-center'>No Records Found</td>
					</tr>";
		endif;

		return array('table' => $table, "pagination" => $list->pagination);
	}

	private function delete_dv()
	{
		$voucher 			=	$this->input->post('voucherno');

		$data['voucherno'] 	=	$voucher;
		$data['stat'] 		=	"cancelled";
		
		$cond 				=	" voucherno = '$voucher' ";
		
		$result 			=	$this->payment->updateData($data, "paymentvoucher", $cond);

		if( $result )
		{
			$result 		=	$this->payment->updateData($data, "pv_details", $cond);
			
			if($result)
				$result 	=	$this->payment->updateData($data, "pv_application", $cond);
		}

		if($result == '1')
			$msg = "success";
		else
			$msg = "Failed to Delete.";

		return $dataArray = array( "msg" => $msg );
	}

	private function cancel()
	{
		$voucher 	= 	$this->input->post("voucher");

		$result 	=	$this->payment->delete_temp_transactions($voucher, "paymentvoucher", "pv_details", "pv_application", "pv_cheques");

		if($result == '1')
			$msg = "success";
		else
			$msg = "Failed to Delete.";

		return $dataArray = array( "msg" => $msg );
	}

	private function apply_proforma()
	{
		$code       = $this->input->post("code");
		$ui         = $this->ui;
		$show_input = $this->show_input;
		
		// RETRIEVE ACCOUNT CODE
		$account_entry_list = $this->payment->retrieveCashAccountList();

		$dataArray		= $this->payment->retrieveData("proforma_details",array('accountcodeid'),"proformacode = '$code'");

		$row			= 1;
		$table 			= "";

		if(!empty($dataArray))
		{
			for($i = 0; $i < count($dataArray); $i++)
			{
				$accountcode = $dataArray[$i]->accountcodeid;
			
				$table	.= '<tr class="clone">';
				
				$table	.= '<td class = "remove-margin">';
				$table 	.= $ui->formField('dropdown')
							->setPlaceholder('Select One')
							->setSplit('', 'col-md-12')
							->setName("accountcode[".$row."]")
							->setId("accountcode[".$row."]")
							->setList($account_entry_list)
							->setValue($accountcode)
							->draw($show_input);
				$table	.= '</td>';
				
				$table	.= '<td class = "remove-margin">';
				$table  .= $ui->formField('text')
							->setSplit('', 'col-md-12')
							->setName('detailparticulars['.$row.']')
							->setId('detailparticulars['.$row.']')
							->setAttribute(array("maxlength" => "100"))
							->setValue("")
							->draw($show_input);
				$table	.= '</td>';
				
				$table	.= '<td class = "remove-margin">';
				$table  .= $ui->formField('text')
							->setSplit('', 'col-md-12')
							->setName('debit['.$row.']')
							->setId('debit['.$row.']')
							->setAttribute(array("maxlength" => "20", "onBlur" => "addAmountAll('debit'); formatNumber(this.id);", "onClick" => "SelectAll(this.id);", "onKeyPress" => "return isNumberKey2(e);"))
							->setClass("format_values_db format_values")
							->setValue("0.00")
							->draw($show_input);
				$table	.= '</td>';
				
				$table	.= '<td class = "remove-margin">';
				$table 	.= $ui->formField('text')
							->setSplit('', 'col-md-12')
							->setName('credit['.$row.']')
							->setId('credit['.$row.']')
							->setAttribute(array("maxlength" => "20", "onBlur" => "addAmountAll('credit'); formatNumber(this.id);", "onClick" => "SelectAll(this.id);", "onKeyPress" => "return isNumberKey2(e);"))
							->setClass("format_values_cr format_values")
							->setValue("0.00")
							->draw($show_input);
				$table	.= '</td>';
				
				$table	.= '<td class="center">';
				$table	.= '<button type="button" class="btn btn-danger btn-flat confirm-delete" data-id="<?=$row?>" name="chk[]" style="outline:none;" onClick="confirmDelete('.$row.');"><span class="glyphicon glyphicon-trash"></span></button>';
				$table	.= '</td>';
				
				$table	.= '</tr>';
				
				$row++;
			}
		}

		return $returnArray = array( "table" => $table );

	}

	public function print_preview($voucherno) 
	{
		// Retrieve Document Info
		$sub_select = $this->payment->retrieveData("pv_application", array("SUM(amount) AS amount"), "voucherno = '$voucherno'");

		$sub_select[0]->amount;

		$docinfo_table  = "paymentvoucher as pv";
		$docinfo_fields = array('pv.transactiondate AS documentdate','pv.voucherno AS voucherno',"CONCAT( first_name, ' ', last_name )","'{$sub_select[0]->amount}' AS amount",'pv.amount AS amt', "'' AS referenceno", "particulars AS remarks", "p.partnername AS vendor");
		$docinfo_join   = "partners as p ON p.partnercode = pv.vendor AND p.companycode = pv.companycode";
		$docinfo_cond 	= "pv.voucherno = '$voucherno'";

		$documentinfo  	= $this->payment->retrieveData($docinfo_table, $docinfo_fields, $docinfo_cond, $docinfo_join);

		$vendor 	    = $documentinfo[0]->vendor;
		
		// Retrieve Document Details
		$docdet_table   = "pv_details as dtl";
		$docdet_fields  = array("chart.segment5 as accountcode", "chart.accountname as accountname", "SUM(dtl.debit) as debit","SUM(dtl.credit) as credit");
		$docdet_join    = "chartaccount as chart ON chart.id = dtl.accountcode AND chart.companycode = dtl.companycode";
		$docdet_cond    = "dtl.voucherno = '$voucherno'";
		$docdet_groupby = "dtl.accountcode";
		$docdet_orderby = "CASE WHEN dtl.debit > 0 THEN 1 ELSE 2 END, dtl.linenum";
		
		$documentdetails = $this->payment->retrieveData($docdet_table, $docdet_fields, $docdet_cond, $docdet_join, $docdet_orderby, $docdet_groupby);

		// Retrieve Payment Details
		$paymentArray	 = $this->payment->retrievePaymentDetails($voucherno);

		// Retrieve Cheque Details
		$pv_v 		  = "";
		$pv_voucherno = $this->payment->getValue("pv_application", array("voucherno"), "voucherno = '$voucherno'");
		
		$chequeArray = "";
		if(!empty($pv_voucherno))
		{
			for($p = 0; $p < count($pv_voucherno); $p++)
			{
				$pv_v .= "'".$pv_voucherno[$p]->voucherno."',";
			}
		
			$pv_v = rtrim($pv_v, ", ");
			
			$cheque_table = "pv_cheques pvc";
			$cheque_fields = array("pvc.chequeaccount", "chart.accountname AS accountname", "pvc.chequenumber AS chequenumber", "pvc.chequedate AS chequedate", "pvc.chequeamount AS chequeamount");
			$cheque_cond = "pvc.voucherno IN($pv_v)";
			$cheque_join = "chartaccount chart ON pvc.chequeaccount = chart.id";
			$chequeArray = $this->payment->retrieveData($cheque_table, $cheque_fields, $cheque_cond, $cheque_join);
		}
		
		// Setting for PDFs
		$print = new print_voucher_model('P', 'mm', 'Letter');
		$print->setDocumentType('Disbursement Voucher')
				->setDocumentInfo($documentinfo[0])
				->setVendor($vendor)
				->setPayments($paymentArray)
				->setDocumentDetails($documentdetails)
				->setCheque($chequeArray)
				->drawPDF('dv_voucher_' . $voucherno);
	}

	private function delete_payments()
	{
		$data_post = $this->input->post("voucher");

		/**
		* Delete Database
		*/
		$result = $this->payment->deletePayments($data_post);

		if(empty($result))
			$msg = "success";
		else
			$msg = $result;
			
		return $dataArray = array( "msg" => $msg );
	}

	private function delete_row()
	{
		$data_var  = array('table', "condition");
		$data_post = $this->input->post($data_var);

		/**
		* Delete Database
		*/
		$result = $this->payment->deleteData($data_post);

		if($result)
			$msg = "success";
		else
			$msg = "The system has encountered an error in deleting. Please contact admin to fix this issue.";

		return $dataArray = array( "msg" => $msg );
	}

	private function check()
	{
		$chequevalue = $this->input->post("chequevalue");

		/**
		* Validate cheque number
		*/
		$result  = $this->payment->getValue("pv_cheques", "chequenumber", "chequenumber = '$chequevalue'" );
		$success = false;
		$msg 	 = "";

		if(!empty($result))
		{
			$success = true;
		}

		return $dataArray = array( "success" => $success );
	}

}