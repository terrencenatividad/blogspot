<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ledger   	    = new stock_ledger();
		$this->input            = new input();
		$this->ui 				= new ui();
		$this->view->header_active = 'report/';
	}

	public function view() {
		$this->view->title = 'Stock Transaction Ledger';
		$this->report_model = new report_model;
   		$this->report_model->generateBalanceTable();
		//$data['accountcodes'] 		= 	array('sample account 1', 'sample account 2');

		$data['accountcodes'] 		=	$this->ledger->retrieveAccounts();

		$data['ui'] = $this->ui;
		$this->view->load('stock_ledger', $data);
	}

	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data 		= $this->input->post(array('daterangefilter','accountcodefilter', 'search'));

		$acctcode 	= $data['accountcodefilter'];
		$search		= $data['search'];
		$datefilter	= $data['daterangefilter'];
		$datefilter = explode('-', $datefilter);

		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		
		$table 		= 	'';
		$footer 	=	"";
		
		$pagination = $this->ledger->retrieveGLReport($acctcode,$dates[0],$dates[1],$search);
		//var_dump($pagination);
		if (empty($pagination->result)) {
			$table .= '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		else
		{

			$prevacct 			= '';
			$nextacct			= '';

			$grandAmount 		= 0;
			$totalDebit 		= 0;
			$totalCredit 		= 0;
			$grandTotal 		= array();
			$totalPerAcct 		= array();

			foreach ($pagination->result as $key => $row) 
			{
				
				$accountcode  		=	$row->accountcode;
				
				$beginning  		=	$this->ledger->retrieveBeginningBalance($accountcode,$dates[0],$dates[1]);

				$totalPerAcct 		= 	$this->ledger->retrieveAccountTotal($accountcode, $dates[0],$dates[1]);
			
				$grandTotal[$accountcode]['debit'] 	=	$beginning[0]->tdebit 	+	$totalPerAcct[0]->tdebit;
				$grandTotal[$accountcode]['credit'] =	$beginning[0]->tcredit 	+	$totalPerAcct[0]->tcredit;
			}

			//var_dump($grandTotal);
			
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

				$prevacct			= 	$accountcode;
				
				$beginning  		=	$this->ledger->retrieveBeginningBalance($accountcode,$dates[0],$dates[1]);

				if($prevacct != $nextacct)
				{	
					$totalPerAcct 		= 	$this->ledger->retrieveAccountTotal($accountcode, $dates[0],$dates[1]);
				
					if ($nextacct != "") {

						$table		.= '<tr>';
						$table		.= '<td>';
						$table		.= '<strong>Sub Total</strong>';
						$table		.= '</td>';
						$table		.= '<td colspan="5">';
						$table		.= '<strong>'.number_format($totalDebit, 2, '.', ',').'</strong>';
						$table		.= '</td>';
						$table		.= '<td>';
						$table		.= '<strong>'.number_format($totalCredit, 2, '.', ',').'</strong>';
						$table		.= '</tr>';
					}
					
					$table		.= '<tr>';
					$table		.= '<td>';
					$table		.= '<strong>'.$segment5.'</strong>';
					$table		.= '</td>';
					$table		.= '<td>';
					$table		.= '<strong>'.$accountname.'</strong>';
					$table		.= '</td>';
					$table		.= '<td colspan="9"></td>';
					$table		.= '</tr>';

					$table		.= '<tr>';
					$table		.= '<td>';
					$table		.= '<strong>Beginning</strong>';
					$table		.= '</td>';
					$table		.= '<td colspan="5">';
					$table		.= '<strong>'.number_format($beginning[0]->tdebit, 2, '.', ',').'</strong>';
					$table		.= '</td>';
					$table		.= '<td>';
					$table		.= '<strong>'.number_format($beginning[0]->tcredit, 2, '.', ',').'</strong>';
					$table		.= '</tr>';
				}

				$table .= '<tr>';	
				$table .= '<td>' . $date . '</td>';
				$table .= '<td>' . $voucherno . '</td>' ;
				$table .= '<td>' . $partner . '</td>';
				$table .= '<td>' . $description . '</td>';
				$table .= '<td>' . $status . '</td>';
				$table .= '<td>' . number_format($debit, 2, '.', ',') . '</td>';
				$table .= '<td>' . number_format($credit, 2, '.', ',') . '</td>';
				$table .= '</tr>';
				
				$nextacct 		= $prevacct;

				// $totalDebit 	+= 	$debit;
				// $totalCredit 	+=	$credit;

				$totalDebit 	=	$grandTotal[$accountcode]['debit'];
				$totalCredit 	=	$grandTotal[$accountcode]['credit'];
			}
		}

		$pagination->table = $table;

		return $pagination;
	}

	private function export()
	{
		$data 		= $this->input->get(array('daterangefilter','accountcodefilter', 'search'));

		$acctcode 	= $data['accountcodefilter'];
		$search		= $data['search'];

		$data['daterangefilter'] = str_replace(array('%2C', '+'), array(',', ' '), $data['daterangefilter']);
		
		$datefilter	= $data['daterangefilter'];	
		$datefilter = explode('-', $datefilter);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		//var_dump($dates);
		$retrieved = $this->ledger->fileExport($acctcode, $dates[0], $dates[1], $search);
		
		$main 	= array("Account Code", "Account Name"); 
		$header = array("Transaction Date", "Voucher No.", "Partner", "Description", "Status", "Total Debit", "Total Credit"); 
		$csv 	= '';

		$filename = "stock_ledger_Report.csv";
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename='.$filename);
		$fp = fopen('php://output', 'w');
		fputcsv($fp, $main);
		fputcsv($fp, $header);

		$a 	= array();
		
		$prevacct 			= '';
		$nextacct			= '';

		$grandAmount 		= 0;
		$totalDebit 		= 0;
		$totalCredit 		= 0;
		$grandTotal 		= array();
		$accounts 			= array();
		$content 			= array();

		foreach ($retrieved as $key => $row) 
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

			if($prevacct != $nextacct)
			{	
				$bdebit 		=	number_format($beginning[0]->tdebit, 2, '.', ',');
				$bcredit 		= 	number_format($beginning[0]->tcredit, 2, '.', ',');
				
				if ($nextacct != "") {
					fputcsv($fp, array("Sub Total",'','','','',$totalDebit,$totalCredit));
					$totalDebit 		=	0;
					$totalCredit 		=	0;
				}

				fputcsv($fp, array($segment5,$accountname));
				fputcsv($fp, array("Beginning",'','','','',$bdebit,$bcredit));

				$totalDebit 	+= 	$beginning[0]->tdebit;
				$totalCredit 	+=	$beginning[0]->tcredit;
			}

			$ddebit 	=	number_format($debit, 2, '.', ',');
			$dcredit 	=	number_format($credit, 2, '.', ',');
			
			fputcsv($fp, array($date, $voucherno, $partner, $description, $status, $ddebit, $dcredit));

			$nextacct 		= $prevacct;

			$totalDebit 	+= 	$debit;
			$totalCredit 	+=	$credit;
		}

		exit;
	}

}