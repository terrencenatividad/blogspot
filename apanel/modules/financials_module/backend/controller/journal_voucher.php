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
			'source'
		);
		$this->fields2			= array(
			'voucherno',
			'accountcode',
			'detailparticulars',
			'debit',
			'credit'
		);
	}

	public function listing() {
		$this->view->title	= 'Journal Voucher List';
		$data['ui']			= $this->ui;
		$this->view->load('journal_voucher/journal_voucher_list', $data);
	}

	public function create() {
		$this->view->title	= 'Journal Voucher Create';
		$data						= $this->input->post($this->fields);
		// Retrieve Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;
		$data['checker'] 			= "";
		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
		$data['ui'] = $this->ui;
		$data['proforma_list']		= $this->jv_model->getProformaList();
		$data['chartofaccounts']	= $this->jv_model->getChartOfAccountList();
		$data['voucher_details']	= json_encode(array());
		$data['ajax_task']			= 'ajax_create';
		$data['ajax_post']			= '';
		$data['show_input']			= true;
		$this->view->load('journal_voucher/journal_voucher', $data);
	}

	public function edit($voucherno) {
		$this->view->title			= 'Journal Voucher  Edit';
		$data						= (array) $this->jv_model->getJournalVoucherById($this->fields, $voucherno);
		// Retrieve Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;
		$checker 					= isset($data['source']) && !empty($data['source']) 	? 	$data['source'] 	:	"";
		$display_edit				= ($checker!="import" && $checker!="beginning" && $checker!="closing") 	?	1	:	0;
		$data['checker'] 			= $display_edit;
		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
		$data['ui'] = $this->ui;
		$data['proforma_list'] 		= $this->jv_model->getProformaList();
		$data['chartofaccounts']	= $this->jv_model->getChartOfAccountList();
		$data['voucher_details']	= json_encode($this->jv_model->getJournalVoucherDetails($this->fields2, $voucherno));
		$data['ajax_task']			= 'ajax_edit';
		$data['ajax_post']			= "&voucherno_ref=$voucherno";
		$data['show_input']			= true;
		$this->view->load('journal_voucher/journal_voucher', $data);
	}

	public function view($voucherno) {
		$this->view->title			= 'Journal Voucher View';
		$data						= (array) $this->jv_model->getJournalVoucherById($this->fields, $voucherno);
		// Retrieve Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;
		$checker 					= isset($data['source']) && !empty($data['source']) 	? 	$data['source'] 	:	"";
		$display_edit				= ($checker!="import" && $checker!="beginning" && $checker!="closing") 	?	1	:	0;
		$data['checker'] 			= $display_edit;
		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
		$data['ui'] = $this->ui;
		$data['proforma_list']		= $this->jv_model->getProformaList();
		$data['chartofaccounts']	= $this->jv_model->getChartOfAccountList();
		$data['voucher_details']	= json_encode($this->jv_model->getJournalVoucherDetails($this->fields2, $voucherno));
		$data['show_input']			= false;
		
		$this->view->load('journal_voucher/journal_voucher', $data);
	}

	public function print_preview($voucherno) {
		$documentinfo		= $this->jv_model->getDocumentInfo($voucherno);
		$documentdetails	= $this->jv_model->getDocumentDetails($voucherno);
		$print = new print_voucher_model('P', 'mm', 'Letter');
		$print->setDocumentType('Journal Voucher')
				->setDocumentInfo($documentinfo)
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
		$data		= $this->input->post(array('search', 'typeid', 'classid', 'daterangefilter', 'sort'));
		$sort		= $data['sort'];
		$search		= $data['search'];
		$typeid		= $data['typeid'];
		$classid	= $data['classid'];
		$datefilter	= $data['daterangefilter'];

		$pagination	= $this->jv_model->getJournalVoucherPagination($this->fields, $search, $sort, $datefilter);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			//Checker for Imported files or Closing
			$checker 	=	isset($row->checker) && !empty($row->checker) 	? 	$row->checker 	:	"";

			$voucherno			=	$row->voucherno;
			$transactiondate 	=	isset($row->transactiondate) 						?	$this->date->DateDbFormat($row->transactiondate) 	:	0;
			$import 			=	(isset($row->checker) && ($row->checker == 'import'||$row->checker  == 'beginning'))	?	"Yes" 	:	"No";
			$closing_checker 	=	!empty($this->jv_model->checkIfClosing($voucherno)) 	?	$this->jv_model->checkIfClosing($voucherno)	:	0;
			$latest 			= 	$this->jv_model->getLatestClosedDate();
			$closed_date 		= 	isset($latest->closed_date) 	?	$this->date->DateDbFormat($latest->closed_date) 	:	0;

			$date_compare 		= 	($transactiondate == $closed_date) 	?	1	:	0;
	
			$display_edit_delete=  	($checker!="import" && $checker!="beginning" && $checker!="closing") 	?	1	:	0;

			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit($display_edit_delete)
									->addDelete(($date_compare && $closing_checker) && $display_edit_delete)
									->addPrint()
									->addCheckbox(($date_compare && $closing_checker) && $display_edit_delete)
									->setLabels(array('delete' => 'Cancel'))
									->setValue($voucherno)
									->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $import . '</td>';
			$table .= '<td>' . $voucherno . '</td>';
			$table .= '<td>' . $row->referenceno . '</td>';
			$table .= '<td class="text-right">' . number_format($row->amount, 2) . '</td>';
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
		} else if ($submit == 'save_preview') {
			$redirect_url = MODULE_URL . 'view/' . $data['voucherno'];
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

		$file_types = array( "text/x-csv","text/tsv","text/comma-separated-values", "text/csv", "application/csv", "application/excel", "application/vnd.ms-excel", "application/vnd.msexcel", "text/anytext");

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
			$warning 			=	array();
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