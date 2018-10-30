<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ledger   	    = new general_ledger();
		$this->input            = new input();
		$this->ui 				= new ui();
		$this->view->header_active = 'report/';
	}

	public function view() {
		$this->view->title = 'General Ledger';
		$this->report_model = new report_model;
   		$this->report_model->generateBalanceTable();
		//$data['accountcodes'] 		= 	array('sample account 1', 'sample account 2');

		$data['datefilter'] 		= 	$this->date->datefilterMonth();
		$data['accountcodes'] 		=	$this->ledger->retrieveAccounts();

		$data['ui'] = $this->ui;
		$this->view->load('general_ledger', $data);
	}

	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data 		= $this->input->post(array('daterangefilter','accountcodefilter', 'search','sort'));

		$acctcode 	= $data['accountcodefilter'];
		$search		= $data['search'];
		$sort 		= $data['sort'];
		$datefilter	= $data['daterangefilter'];
		$datefilter = explode('-', $datefilter);

		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		
		$table 		= 	'';
		$footer 	=	"";
		$grandDebit 		= 0;
		$grandCredit 		= 0;

		$grandAmount 		= 0;
		$totalDebit 		= 0;
		$totalCredit 		= 0;
		
		$pagination = $this->ledger->retrieveGLReport($acctcode,$dates[0],$dates[1],$search, $sort);
				
		if (empty($pagination->result)) {
			$table .= '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		else
		{
			$prevacct 			= '';
			$nextacct			= '';
			$grandTotal 		= array();
			$totalPerAcct 		= array();

			$retrieved = $this->ledger->retrieveAllAccounts($acctcode, $dates[0], $dates[1], $search, $sort);

			if (!empty($retrieved)){
				$non_pagi_begd 		=	$non_pagi_begc 	=	0;
				$non_pagi_totald 	=	$non_pagi_totalc 	=	0;
				$next 		= 	$prev	=	'';
				foreach ($retrieved as $key => $row){
					$accountcode  		=	$row->accountcode;
					$debit 				= 	$row->debit;
					$credit 			=	$row->credit;
					$prev			= 	$accountcode;
					
					$beginning  		=	$this->ledger->retrieveBeginningBalance($accountcode,$dates[0],$dates[1]);

					$non_pagi_begd 	=	($beginning[0]->balance > 0) 	?	$beginning[0]->balance 	: 	0;
					$non_pagi_begc 	=	($beginning[0]->balance < 0) 	?	($beginning[0]->balance) * -1 	: 	0;

					if($prev != $next) {
						$non_pagi_totald 	+= 	$non_pagi_begd;
						$non_pagi_totalc 	+=	$non_pagi_begc;
					}
					$next 		= 	$prev;

					$non_pagi_totald 	+= 	$debit;
					$non_pagi_totalc 	+=	$credit;
				}
				$grandDebit 	+=	$non_pagi_totald;
				$grandCredit 	+=  $non_pagi_totalc;
			}
	
			foreach ($pagination->result as $key => $row) 
			{
				$accountcode  		=	$row->accountcode;
				$segment5  			=	$row->segment5;
				$accountname 		=	$row->accountname;
				$date 				=	$row->transactiondate;
				$voucherno 			=	$row->voucherno;
				$partner 			=	$row->partner;
				$debit 				= 	$row->debit;
				$credit 			=	$row->credit;
				$transtype 			=	$row->transtype;
				$description  		=	"";
				$status 			=	"";
				$bdebit 			=	0;
				$bcredit 			=	0;

				$prevacct			= 	$accountcode;
				
				$beginning  		=	$this->ledger->retrieveBeginningBalance($accountcode,$dates[0],$dates[1]);

				$beginning_debit 	=	($beginning[0]->balance > 0) 	?	$beginning[0]->balance 	: 	0;
				$beginning_credit 	=	($beginning[0]->balance < 0) 	?	($beginning[0]->balance) * -1 	: 	0;

				$link 			=	"";
				$voucherno2	 	=	"";
				if( $transtype == 'DM' ){
					$link 	.=	BASE_URL."financials/debit_memo/view/";
				} else if( $transtype == 'CM' ) {
					$link 	.=	BASE_URL."financials/credit_memo/view/";
				} else if( $transtype == 'AP' ) {
					$link 	.=	BASE_URL."financials/accounts_payable/view/";
				} else if( $transtype == 'AR' ) {
					$link 	.=	BASE_URL."financials/accounts_receivable/view/";
				} else if( $transtype == 'RV' ) {
					$result 	=	$this->ledger->findSourceAR($voucherno);
					$voucherno2 	=	isset($result->arvoucherno) ? $result->arvoucherno : "";
					// $link 		.=	BASE_URL."financials/accounts_receivable/view/";
					$link 	.=	BASE_URL."financials/receipt_voucher/view/";
				} else if( $transtype == 'PV' ) {
					$result 	=	$this->ledger->findSourceAP($voucherno);
					$voucherno2 	=	isset($result->apvoucherno) ? $result->apvoucherno : "";
					// $link 	.=	BASE_URL."financials/accounts_payable/view/";
					$link 	.=	BASE_URL."financials/payment_voucher/view/";
				} else if( $transtype == 'JV' ) {
					$link 	.=	BASE_URL."financials/journal_voucher/view/";
				} else if( $transtype == 'DV' ) {
					$link 	.=	BASE_URL."financials/payment/view/";
				}

				// $link 	.=	($voucherno2 != "") 	? 	$voucherno2 	: 	$voucherno;
				$link 	.=	$voucherno;

				if($prevacct != $nextacct)
				{	
					$bdebit 		=	$beginning_debit;
					$bcredit 		= 	$beginning_credit;

					if ($nextacct != "") {

						$table		.= '<tr>';
						$table		.= '<td style="text-align:left">';
						$table		.= '<strong>Sub Total</strong>';
						$table		.= '</td>';
						$table		.= '<td colspan="5">';
						$table		.= '<strong>'.number_format($totalDebit, 2, '.', ',').'</strong>';
						$table		.= '</td>';
						$table		.= '<td>';
						$table		.= '<strong>'.number_format($totalCredit, 2, '.', ',').'</strong>';
						$table		.= '</tr>';

						$totalDebit 		=	0;
						$totalCredit 		=	0;
					}	
					
					$table		.= '<tr>';
					$table		.= '<td style="text-align:left">';
					$table		.= '<strong>'.$segment5.'</strong>';
					$table		.= '</td>';
					$table		.= '<td style="text-align:left">';
					$table		.= '<strong>'.$accountname.'</strong>';
					$table		.= '</td>';
					$table		.= '<td colspan="9"></td>';
					$table		.= '</tr>';

					$table		.= '<tr>';
					$table		.= '<td style="text-align:left">';
					$table		.= '<strong>Beginning</strong>';
					$table		.= '</td>';
					$table		.= '<td colspan="5">';
					$table		.= '<strong>'.number_format($bdebit, 2, '.', ',').'</strong>';
					$table		.= '</td>';
					$table		.= '<td>';
					$table		.= '<strong>'.number_format($bcredit, 2, '.', ',').'</strong>';
					$table		.= '</tr>';

					$totalDebit 	+= 	$beginning_debit;
					$totalCredit 	+=	$beginning_credit;
				}

				$table .= '<tr>';	
				$table .= '<td style="text-align:left">' . $this->date->dateFormat($date) . '</td>';
				$table .= '<td style="text-align:left"><a href="'.$link.'" target="_blank">' . $voucherno . '</a></td>' ;
				$table .= '<td style="text-align:left">' . $partner . '</td>';
				$table .= '<td>' . $description . '</td>';
				$table .= '<td>' . $status . '</td>';
				$table .= '<td>' . number_format($debit, 2, '.', ',') . '</td>';
				$table .= '<td>' . number_format($credit, 2, '.', ',') . '</td>';
				$table .= '</tr>';
				
				$nextacct 		= $prevacct;

				$totalDebit 	+= 	$debit;
				$totalCredit 	+=	$credit;
			}

			$table		.= '<tr>';
			$table		.= '<td style="text-align:left">';
			$table		.= '<strong>Sub Total</strong>';
			$table		.= '</td>';
			$table		.= '<td colspan="5">';
			$table		.= '<strong>'.number_format($totalDebit, 2, '.', ',').'</strong>';
			$table		.= '</td>';
			$table		.= '<td>';
			$table		.= '<strong>'.number_format($totalCredit, 2, '.', ',').'</strong>';
			$table		.= '</tr>';
		}

		$table		.= '<tr class="warning">';
		$table		.= '<td style="text-align:left">';
		$table		.= '<strong>Grand Total</strong>';
		$table		.= '</td>';
		$table		.= '<td colspan="5">';
		$table		.= '<strong>'.number_format($grandDebit, 2, '.', ',').'</strong>';
		$table		.= '</td>';
		$table		.= '<td>';
		$table		.= '<strong>'.number_format($grandCredit, 2, '.', ',').'</strong>';
		$table		.= '</tr>';

		$pagination->table = $table;
		$pagination->csv   = $this->export();

		return $pagination;
	}

	private function export()
	{
		$data 		= $this->input->post(array('daterangefilter','accountcodefilter', 'search','sort'));

		$acctcode 	= $data['accountcodefilter'];
		$sort 		= $data['sort'];
		$search		= $data['search'];

		$data['daterangefilter'] = str_replace(array('%2C', '+'), array(',', ' '), $data['daterangefilter']);
		
		$strdate	= $data['daterangefilter'];	
		$datefilter = explode('-', $data['daterangefilter']);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}

		$prevacct 			= '';
		$nextacct			= '';

		$grandAmount 		= 0;
		$totalDebit 		= 0;
		$totalCredit 		= 0;
		$grandDebit 		= 0;
		$grandCredit		= 0;

		$a 					= array();
		$grandTotal 		= array();
		$accounts 			= array();
		$content 			= array();

		$retrieved = $this->ledger->retrieveAllAccounts($acctcode, $dates[0], $dates[1], $search, $sort);
		
		$main 	= array("Account Code","Account Name"); 
		$header = array("Transaction Date","Voucher No.","Partner","Description","Status","Total Debit","Total Credit"); 
		
		$csv 	= '';
		$csv 	.= 'General Ledger';
		$csv 	.= "\n\n";
		$csv 	.= '"Date:","'.$strdate.'"';
		$csv 	.= "\n\n";
		$csv 	.= '"' . implode('","', $main) . '"';
		$csv  	.= "\n";
		$csv 	.= '"' . implode('","', $header) . '"';
		$csv 	.= "\n";

		if (!empty($retrieved)){
			foreach ($retrieved as $key => $row){
				$accountcode  		=	$row->accountcode;
				$segment5  			=	$row->segment5;
				$accountname 		=	$row->accountname;
				$date 				=	$row->transactiondate;
				$voucherno 			=	$row->voucherno;
				$partner 			=	$row->partner;
				$debit 				= 	$row->debit;
				$credit 			=	$row->credit;
				$transtype 			=	$row->transtype;
				$description  		=	"";
				$status 			=	"";
				$bdebit 			=	0;
				$bcredit 			=	0;

				$prevacct			= 	$accountcode;
				
				$beginning  		=	$this->ledger->retrieveBeginningBalance($accountcode,$dates[0],$dates[1]);

				$beginning_debit 	=	($beginning[0]->balance > 0) 	?	$beginning[0]->balance 	: 	0;
				$beginning_credit 	=	($beginning[0]->balance < 0) 	?	($beginning[0]->balance) * -1 	: 	0;

				if($prevacct != $nextacct)
				{	
					$bdebit 		=	$beginning_debit;
					$bcredit 		= 	$beginning_credit;
					
					if ($nextacct != "") {
						$csv .= '"","","","","Sub Total","'.$this->amount($totalDebit).'","' . $this->amount($totalCredit) . '"';
						$csv .= "\n";
						
						$grandDebit 	+=	$totalDebit;
						$grandCredit 	+=  $totalCredit;
						$totalDebit 		=	0;
						$totalCredit 		=	0;
					}

					$csv .= '"'.$segment5.'","' . $accountname . '"';
					$csv .= "\n";
					$csv .= '"Beginning","","","","","'.$this->amount($bdebit).'","' . $this->amount($bcredit) . '"';
					$csv .= "\n";

					$totalDebit 	+= 	$beginning_debit;
					$totalCredit 	+=	$beginning_credit;
				}

				$csv .= '"' . $this->date->dateFormat($date) . '",';
				$csv .= '"' . $voucherno . '",';
				$csv .= '"' . $partner . '",';
				$csv .= '"' . $description . '",';
				$csv .= '"' . $status . '",';
				$csv .= '"' . $this->amount($debit) . '",';
				$csv .= '"' . $this->amount($credit) . '"';
				$csv .= "\n";

				$nextacct 		= $prevacct;

				$totalDebit 	+= 	$debit;
				$totalCredit 	+=	$credit;
			}

			$grandDebit 	+=	$totalDebit;
			$grandCredit 	+=  $totalCredit;
			$csv .= '"","","","","Sub Total","'.$this->amount($totalDebit).'","' . $this->amount($totalCredit) . '"';
			$csv .= "\n";
		}
		
		$csv .= '"","","","","Grand Total","'.$this->amount($grandDebit).'","' . $this->amount($grandCredit) . '"';
		$csv .= "\n";

		return $csv;
	}

	private function amount($amount)
	{
		return number_format($amount,2);
	}
}