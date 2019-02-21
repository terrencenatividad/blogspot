<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->seq 				= new seqcontrol();
		$this->jv_model			= new journal_voucher_model();
		$this->restrict 		= new financials_restriction_model();
		$this->session			= new session();
		$this->log 				= new log();
		$this->fields 			= array(
			'voucherno',
			'transactiondate',
			'referenceno',
			'remarks',
			'proformacode',
			'amount',
			'source',
			'stat'
		);
		$this->fields2			= array(
			'voucherno',
			'accountcode',
			'detailparticulars',
			'debit',
			'credit',
			'linenum'
		);
	}

	public function listing() {
		$this->view->title	= 'Journal Voucher';
		$data['ui']				= $this->ui;
		$data['show_input']     = true;
		$data['source_list']	= array("import"=>"Imported JV","manual"=>"Manual JV","closed"=>"Closed Books");
		$this->view->load('journal_voucher/journal_voucher_list', $data);
	}

	public function create() {
		$this->view->title			= 'Create Journal Voucher';
		$data						= $this->input->post($this->fields);
		// Retrieve Closed Date
		$data['close_date']			= $this->restrict->getClosedDate();
		$data['checker']			= '';
		$data['display_edit']		= 1;
		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
		$data['ui'] = $this->ui;
		$data['chartofaccounts']	= $this->jv_model->getChartOfAccountList();
		$data['voucher_details']	= json_encode(array());
		$data['ajax_task']			= 'ajax_create';
		$data['proforma_list']		= $this->jv_model->getProformaList($data);
		$data['ajax_post']			= '';
		$data['show_input']			= true;
		$data['restrict_jv']		= true;
		$this->view->load('journal_voucher/journal_voucher', $data);
	}

	public function edit($voucherno) {
		$this->view->title			= 'Edit Journal Voucher';
		$data						= (array) $this->jv_model->getJournalVoucherById($this->fields, $voucherno);
		// Retrieve Closed Date
		$data['close_date']			= $this->restrict->getClosedDate();
		$checker 					= isset($data['source']) && !empty($data['source']) ? $data['source'] : "";
		$data['checker']			= $checker;
		$status						= $data['stat'];
		$data['display_edit']		= ($checker!="import" && $checker!="beginning" && $checker!="closing" && $status != 'cancelled') ? 1 : 0;
		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
		$data['ui'] = $this->ui;
		$data['voucher_details']	= json_encode($this->jv_model->getJournalVoucherDetails($this->fields2, $voucherno, $status));
		$coa_array	= array();
		$hey = json_decode($data['voucher_details']);
			foreach ($hey as $index => $dtl){
				$coa			= $dtl->accountcode;
				$coa_array[]	= $coa;
			}
		$data['chartofaccounts']	= $this->jv_model->getEditChartOfAccountList($coa_array);			
		$data['ajax_task']			= 'ajax_edit';
		$data['proforma_list']		= $this->jv_model->getProformaList($data);
		$data['ajax_post']			= "&voucherno_ref=$voucherno";
		$data['show_input']			= true;
		$data['restrict_jv'] 		= true;
		$this->view->load('journal_voucher/journal_voucher', $data);
	}

	public function view($voucherno) {
		$this->view->title			= 'View Journal Voucher';
		$this->fields[]				= 'stat';
		$data						= (array) $this->jv_model->getJournalVoucherById($this->fields, $voucherno);
		// Retrieve Closed Date
		$data['close_date']			= $this->restrict->getClosedDate();
		$checker 					= isset($data['source']) && !empty($data['source']) ? $data['source'] : "";
		$data['checker']			= $checker;
		$status						= $data['stat'];
		$data['display_edit']		= ($checker!="import" && $checker!="beginning" && $checker!="closing" && $checker!="yrend_closing" && $status != 'cancelled') ? 1 : 0;
		$transactiondate 			= $data['transactiondate'];
		$data['transactiondate']	= $this->date->dateFormat($transactiondate);
		$data['ui'] = $this->ui;
		$data['ajax_task']			= 'ajax_view';
		$data['proforma_list']		= $this->jv_model->getProformaList($data);
		$data['voucher_details']	= json_encode($this->jv_model->getJournalVoucherDetails($this->fields2, $voucherno,$status));
		$coa_array	= array();
		$hey = json_decode($data['voucher_details']);
			foreach ($hey as $index => $dtl){
				$coa			= $dtl->accountcode;
				$coa_array[]	= $coa;
			}
		$data['chartofaccounts']	= $this->jv_model->getEditChartOfAccountList($coa_array);		
		$data['show_input']			= false;
		$data['restrict_jv']		= $this->restrict->setButtonRestriction($transactiondate);
		$this->view->load('journal_voucher/journal_voucher', $data);
	}

	public function print_preview($voucherno) {
		$documentinfo		= $this->jv_model->getDocumentInfo($voucherno);
		$documentdetails	= $this->jv_model->getDocumentDetails($voucherno);
		$print = new print_voucher_model('P', 'mm', 'Letter');
		$print->setDocumentType('Journal Voucher')
				->setDocumentInfo($documentinfo)
				->setVoucherStatus(strtoupper($documentinfo->stat))
				->setDocumentDetails($documentdetails)
				->drawPDF('jv_voucher_' . $voucherno);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data		= $this->input->post(array('search', 'typeid', 'classid', 'daterangefilter', 'sort','source','filter'));
		$sort		= $data['sort'];
		$search		= $data['search'];
		$typeid		= $data['typeid'];
		$classid	= $data['classid'];
		$datefilter	= $data['daterangefilter'];
		$source 	= $data['source'];
		$filter 	= $data['filter'];

		$pagination	= $this->jv_model->getJournalVoucherPagination($this->fields, $search, $sort, $datefilter, $source, $filter);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {;
			$voucherno			=	isset($row->voucherno) 			? 	$row->voucherno 									:	"";
			$transactiondate 	=	isset($row->transactiondate) 	?	$this->date->DateDbFormat($row->transactiondate)	:	"";
			$status				=   $row->stat;

			//Checker for Imported files or Closing
			$checker 			=	isset($row->checker) && !empty($row->checker) 		? 	$row->checker 	:	"";
			$uneditable_box 	= 	array("import","beginning","closing","yrend_closing","accrual_jv","reversed_ajv","jo_release","depreciation");
			$display_edit_delete=  	in_array($checker, $uneditable_box) 	?	0	:	1;
			// $display_edit_delete=  	($checker!="import" || $checker!="beginning" || $checker!="closing"  || $checker!="yrend_closing" || $checker!="accrual_jv" || $checker!="reverse_ajv" || $checker!="jo_release") 	?	1	:	0;

			//Transaction Dates equivalent to the closing date / period should be deleted first
			$latest_closed_date = 	$this->restrict->getClosedDate();
			$date_compare 		= 	($transactiondate == $latest_closed_date) 	?	1	:	0;
			$latest_voucherno 	=	$this->restrict->getLatestVoucher();
			$voucher_compare 	=	($voucherno == $latest_voucherno) 			?	1	:	0;

			//Checker for restricting for Closing on Edit / Delete [ 0 = within closing period ]
			$restrict_jv 		= 	$this->restrict->setButtonRestriction($transactiondate);

			// echo $checker." ".$display_edit_delete." ".$restrict_jv."\n";
			$import 			=	($checker!="" && ($row->checker == 'import'||$row->checker  == 'beginning'))	?	"Yes" 	:	"No";
			$voucher_status = '<span class="label label-danger">'.strtoupper($status).'</span>';
			if($status == 'open'){
				$voucher_status = '<span class="label label-info">'.strtoupper($status).'</span>';
			}else if($status == 'posted'){
				$voucher_status = '<span class="label label-success">'.strtoupper($status).'</span>';
			}
			$table .= '<tr>';
			// echo "1 ".$display_edit_delete."\n\n";
			// echo "2 ".$restrict_jv."\n\n";
			// echo "3 ".$checker."\n\n";

			$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit($status != "cancelled" && $display_edit_delete && $restrict_jv )
									->addDelete($status != "cancelled" && $display_edit_delete  && ($restrict_jv || $date_compare && $voucher_compare))
									->addPrint()
									->addCheckbox($status != "cancelled" && $display_edit_delete  && ($restrict_jv || $date_compare && $voucher_compare))
									->setLabels(array('delete' => 'Cancel'))
									->setValue($voucherno)
									->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $this->date->dateFormat($transactiondate) . '</td>';
			$table .= '<td>' . $import . '</td>';
			$table .= '<td>' . $voucherno . '</td>';
			$table .= '<td>' . $row->referenceno . '</td>';
			$table .= '<td class="text-right">' . number_format($row->amount, 2) . '</td>';
			$table .= '<td>' . $voucher_status . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_get_proforma() {
		$proformacode	= $this->input->post('proformacode');
		$proforma		= $this->jv_model->getProforma($proformacode);
		return array(
			'proforma' => $proforma
		);
	}

	private function ajax_create() {
		$submit						= $this->input->post('submit');
		$data						= $this->input->post($this->fields);
		$data2						= $this->input->post($this->fields2);
		$seq						= new seqcontrol();
		$data['voucherno']			= $seq->getValue('JV');
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		$result						= $this->jv_model->saveJournalVoucher($data, $data2);
		$redirect_url = MODULE_URL;
		if ($submit == 'save_new') {
			$redirect_url = MODULE_URL . 'create';
		} else if ($submit == 'save') {
			$redirect_url = MODULE_URL . 'view/' . $data['voucherno'];
		} else if ($submit == 'save_exit') {
			$redirect_url = MODULE_URL;
		}
		return array(
			'redirect'	=> $redirect_url,
			'success'	=> $result
		);
	}

	private function ajax_edit() {
		$data						= $this->input->post($this->fields);
		unset($data['voucherno']);
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		$voucherno					= $this->input->post('voucherno_ref');
		$data2						= $this->input->post($this->fields2);
		$data2['stat']				= 'posted';
		$result						= $this->jv_model->updateJournalVoucher($data, $data2, $voucherno, 'Update');
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		if ($delete_id) {
			$this->jv_model->deleteJournalVouchers($delete_id);
		}
		if ($delete_id) {
			$this->jv_model->reverseEntries($delete_id);
		}
	}

	private function delete_related_jobjv(){
		$delete_id 	= 	$this->input->post('delete_id');
		$vouchlist 	=	$this->jv_model->getJournalVoucherBySourceNo("voucherno",$delete_id);
		// var_dump($vouchlist);
		foreach($vouchlist as $row){
			$voucherno[] =	$row->voucherno;
			
			if ($voucherno) {
				$result = $this->jv_model->deleteJournalVouchers($voucherno);
			}
			if ($result && $voucherno) {
				$result = $this->jv_model->reverseEntries($voucherno);
			}
		}
	}
	
	public function get_import(){
		header('Content-type: application/csv');
		$header = array('Document Set','Transaction Date','Reference','Notes','Account Name','Description','Debit','Credit');
		$return = "";
		
		$return .= '"' . implode('","',$header) . '"';
		$return .= "\n";

		echo $return;
	}
	
	private function save_import(){
		$file		= fopen($_FILES['file']['tmp_name'],'r') or exit ("File Unable to upload") ;

		$filedir	= $_FILES["file"]["tmp_name"];

		$file_types = array( "text/x-csv","text/tsv","text/comma-separated-values", "text/csv", "application/csv", "application/excel", "application/vnd.ms-excel", "application/vnd.msexcel", "text/anytext", "application/octet-stream");

		$errmsg 	=	array();
		$proceed 	=	false;

		/**VALIDATE FILE IF CORRUPT**/
		if(!empty($_FILES['file']['error'])){
			$errmsg[] = "File being uploaded is corrupted.<br/>";
		}

		/**VALIDATE FILE TYPE**/
		if(!in_array($_FILES['file']['type'],$file_types)){
			$errmsg[]= "Invalid file type, file must be .csv.<br/>";
		}
		
		$headerArr = array('Document Set','Transaction Date','Reference','Notes','Account Name','Description','Debit','Credit');
		
		$warning 			=	array();
		if( empty($errmsg) ) {
			$x = array_map('str_getcsv', file($_FILES['file']['tmp_name']));
			$error 	=	array();
			for ($n = 0; $n < count($x); $n++) {
				if($n==0 && empty($errmsg)) {
					$layout = count($headerArr);
					$template = count($x);
					$header = $x[$n];

					for ($m=0; $m< $layout; $m++){
						$template_header = $header[$m];
						
						$error = (empty($template_header) || !in_array($template_header,$headerArr)) ? "error" : "";
					}	
					
					$errmsg[]	= (!empty($error) || $error != "" ) ? "Invalid template. Please download the template from the system first.<br/>" : "";
					
					$errmsg		= array_filter($errmsg);

				}
				if ( $n >= 1 ) {
					$z[] = $x[$n];
				}
			}
			
			$line 				=	2;
			$post 				=	array();
			$vouchlist 			= 	array();
			$h_vouchlist 		=	array();
			$datelist 			= 	array();
			$referencelist 		=	array();
			$noteslist 			=	array();
			$accountlist 		=	array();
			$descriptions 		= 	array();
			$debitlist 			= 	array();
			$creditlist 		= 	array();
			$totaldebit 		=	array();
			$totalcredit 		=	array();

			if( empty($errmsg)  && !empty($z) ){
				$total_debit 	=	0;
				$total_credit 	=	0;
				$prev_no 		=	$prev_date 		=	$prev_ref 	=	$prev_notes 	=	$voucherno 		= "";
				foreach ($z as $key => $b) {
					if ( ! empty($b)) {	
						$jvno 			=	isset($b[0]) 					? 	$b[0] 	:	"";
						$transdate 		=	isset($b[1]) 					? 	$b[1] 	:	"";
						$transdate 		=	$this->date->dateDbFormat($transdate);
						$reference 		=	isset($b[2]) 					?	htmlentities(trim($b[2]))	:	"";
						$notes 			=	isset($b[3]) 					?	htmlentities(trim($b[3]))	:	"";
						$account 		=	isset($b[4]) 					?	htmlentities(trim($b[4]))	:	"";
						$account 		= 	str_replace('&ndash;', '-', $account);
						$description 	=	isset($b[5]) 					?	htmlentities(trim($b[5]))	:	"";
						$debit 			=	isset($b[6]) && !empty($b[6]) 	?	$b[6]	:	0;
						$credit 		=	isset($b[7]) && !empty($b[7])	?	$b[7]	:	0;

						//Check if account Name exists
						$acct_exists 	=	$this->jv_model->check_if_exists('id','chartaccount'," accountname = '$account' ");
						$acct_count 	=	$acct_exists[0]->count;	

						//Get latest closing date
						$close_date 	=	$this->restrict->getClosedDate();

						if(!empty($account)){
							if( $acct_count <= 0 ) {
								$errmsg[]	= "Account Name [<strong>$account</strong>] on <strong>row $line</strong> does not exist.<br/>";
								$errmsg		= array_filter($errmsg);
							}	
						}else{
							$errmsg[]	= "Account Name on <strong>row $line</strong> should not be empty.<br/>";
							$errmsg		= array_filter($errmsg);
						}
						
						if( $key == 0 ){
							// Check if Document Set is not empty. 
							if($jvno == ""){
								$errmsg[]	= "Document Set on <strong>row $line</strong> should not be empty.<br/>";
								$errmsg		= array_filter($errmsg);
							} else {
								$voucherno		= $this->seq->getValue('JV');
							}
							// Check if Transaction Date is not Empty 
							if($transdate == ''){
								$errmsg[]	= "Transaction Date on <strong>row $line</strong> should not be empty.<br/>";
								$errmsg		= array_filter($errmsg);
							}
							//Check if Transaction Date is not within Closed Date Period
							if($transdate <= $close_date){
								$errmsg[]	= "Transaction Date [<strong>$transdate</strong>] on <strong>row $line</strong> must not be within the Closed Period.<br/>";
								$errmsg		= array_filter($errmsg);
							}
							//Check if Account is not empty
							if($account == ''){
								$errmsg[]	= "Account on <strong>row $line</strong> should not be empty.<br/>";
								$errmsg		= array_filter($errmsg);
							}
							//Check if Debit / Credit has an amount
							if($debit == '' && $credit == ''){
								$errmsg[]	= "Debit or Credit on <strong>$line</strong> should have a value.<br/>";
								$errmsg		= array_filter($errmsg);
							}
						} else {
							if ($jvno == '') {
								$jvno = $prev_no;
							} else if ($jvno != $prev_no) {
								$total_credit 	= 0;
								$total_debit 	= 0;
								$voucherno		= $this->seq->getValue('JV');
							} 
							if ($jvno == $prev_no) {
								// Check Transaction Date is the same
								if($transdate == ''){
									$transdate 	= $prev_date;
								} else if ($transdate != $prev_date) {
									$errmsg[]	= "Transaction Date [<strong>$transdate</strong>] on <strong>row $line</strong> should be the same for vouchers # <strong>$jvno</strong>.<br/>";
									$errmsg		= array_filter($errmsg);
								}
								//Check if Transaction Date is not within Closed Date Period
								if($transdate <= $close_date){
									$errmsg[]	= "Transaction Date [<strong>$transdate</strong>] on <strong>row $line</strong> must not be within the Closed Period.<br/>";
									$errmsg		= array_filter($errmsg);
								}
								// Check the Reference #
								if($reference == ''){
									$reference 	=	$prev_ref;
								} else if ($reference != $prev_ref) {
									$errmsg[]	= "Reference No [<strong>$reference</strong>] on <strong>row $line</strong> should be the same for vouchers # <strong>$jvno</strong>.<br/>";
									$errmsg		= array_filter($errmsg);
								}
								// Check the Notes
								if($notes == ''){
									$notes 	=	$prev_notes;
								} else if ($notes != $prev_notes) {
									$errmsg[]	= "Notes [<strong>$notes</strong>] on <strong>row $line</strong> should be the same for vouchers # <strong>$jvno</strong>.<br/>";
									$errmsg		= array_filter($errmsg);
								}
								// Check if Credit != 0 && Debit != 0
								if( $total_credit == 0 && $total_debit == 0 ){
									$errmsg[]	= "The Total Debit and Total Credit on <strong>row $line</strong> must have a value.<br/>";
									$errmsg		= array_filter($errmsg);
								}
							}
						}

						$total_credit 	+=	$credit;
						$total_debit 	+=	$debit;

						// Check if Debit Total == Credit Total
						if ( ! isset($z[$key + 1]) || ($jvno != $z[$key + 1][0] && $z[$key + 1][0] != '')) {
							$totaldebit[] 	= $total_debit;
							$totalcredit[]	= $total_credit;
							if ($total_credit != $total_debit){
								$errmsg[]	= "The Total Debit and Total Credit on <strong>row $line</strong> must be equal.<br/>";
								$errmsg		= array_filter($errmsg);
							}
						}

						if(empty($errmsg)){
							$vouchlist[] 		= $voucherno;
							$accountlist[] 		= $this->jv_model->getAccountId($account);
							$descriptions[] 	= $description;
							$debitlist[] 		= $debit; 
							$creditlist[] 		= $credit;

							if( !isset($h_vouchlist) || !in_array($voucherno, $h_vouchlist) ){
								$h_vouchlist[] 		= $voucherno;
								$datelist[] 		= $transdate;
								$referencelist[] 	= $reference;
								$noteslist[] 		= $notes;
							}
 						}

						$prev_no 		= $jvno;
						$prev_date		= $transdate;
						$prev_ref 		= $reference;
						$prev_notes 	= $notes;
						
						$line++;
					}
				}

				if( empty($errmsg) ) {

					$header 	=	array(
						'voucherno' 		=> $h_vouchlist,
						'transactiondate' 	=> $datelist,
						'referenceno'		=> $referencelist,
						'remarks'			=> $noteslist,
						'amount' 			=> $totaldebit,
						'convertedamount' 	=> $totaldebit,
					); 
					
					foreach ($header['voucherno'] as $key => $row) {
						$header['currencycode'][] 	= "PHP";
						$header['exchangerate'][] 	= 1;
						$header['stat'][] 			= "posted";
						$header['transtype'][] 		= "JV";
						$header['source'][] 		= "import";
					}

					foreach ($header['transactiondate'] as $key => $row) {
						$period			= date("n",strtotime($row));
						$fiscalyear		= date("Y",strtotime($row));
						$header['period'][] 	= $period;
						$header['fiscalyear'][] = $fiscalyear;
					}

					$proceed  			= $this->jv_model->save_import("journalvoucher",$header);

					$details = array(
						'voucherno' 		=> $vouchlist,
						'accountcode'		=> $accountlist,
						'detailparticulars'	=> $descriptions,
						'debit' 			=> $debitlist,
						'credit' 			=> $creditlist
					);

					$linenum = 1;

					foreach ($details['voucherno'] as $key => $row) {
						$details['linenum'][] 	= $linenum;
						$details['stat'][] 		= "posted";
						$details['transtype'][] = "JV";
						$linenum++;
						if (isset($details['voucherno'][$key + 1]) && $details['voucherno'][$key + 1] != $row) {
							$linenum = 1;
						}
					}

					if($proceed){
						$proceed 	=	$this->jv_model->save_import("journaldetails",$details);

						if($proceed){
							$this->log->saveActivity("Imported Journal Vouchers.");
						}
					} 
				}
			}
		}

		$error_messages		= implode(' ', $errmsg);
		$warning_messages	= implode(' ', $warning);

		return array("proceed" => $proceed,"errmsg"=>$error_messages, "warning"=>$warning_messages);
	}

}