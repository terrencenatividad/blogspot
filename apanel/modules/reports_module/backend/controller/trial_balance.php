<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui = new ui();
		$this->view->header_active = 'report/';
		$this->trial_balance = new trial_balance();
		// $this->financials	= new financialsClass();
		// $this->financials->updateBalanceTable();
		$this->input            = new input();
		$this->show_input 	    = true;
		$session                = new session();
	}

	public function view() {
		$this->view->title = 'Trial Balance';
		$this->report_model = new report_model;
   		$this->report_model->generateBalanceTable();
		$data['ui'] = $this->ui;
		$data['show_input'] = true;
		$data['datefilter'] = $this->date->datefilterMonth();
		$this->view->load('trial_balance', $data);
	}

	public function ajax($task)
	{
		header('Content-type: application/json');
		$result 	=	"";

		if($task == 'list'){
			$result = $this->ajax_list();
		}else if($task == 'load_account_transactions'){
			$result = $this->load_account_transactions();
		}else if($task == 'export'){
			$result = $this->export();
		}

		echo json_encode($result); 
	}

	private function ajax_list()
	{
		$data = $this->input->post(array('daterangefilter','limit'));
		$daterangefilter	= $data['daterangefilter'];
		$default_datefilter = date("M d, Y",strtotime('first day of this month')).' - '.date("M d, Y",strtotime('last day of this month'));

		$date_filter = explode('-', $daterangefilter);
		foreach ($date_filter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$datefilterFrom = (!empty($dates[0]))? $dates[0] : "";
		$datefilterTo   = (!empty($dates[1]))? $dates[1] : "";
		$datefilter     = (!empty($daterangefilter))? $daterangefilter : $default_datefilter;
			// echo $datefilterFrom;
			// echo " - ".$datefilterTo; 
		$currentyear 	= date("Y",strtotime($datefilterTo));
		$prevyear 		= date("Y",strtotime($datefilterFrom." -1 year"));

		$pagination = $this->trial_balance->getTrialBalance($currentyear,$prevyear);
		$table = '';
		$foot  = '';
		$totaldebit 	= 0;
		$totalcredit 	= 0;
		$totalprevcarry = 0;
		$totalbalcarry  = 0;
		$totalperiodbalance = 0;
		$totalaccumulatedbalance = 0;
		if(count($pagination->result)>0)
		{
			for($i=0;$i<count($pagination->result);$i++)
			{	
				$accountid          = $pagination->result[$i]->accountid;
				$accountcode		= $pagination->result[$i]->accountcode;
				$accountname		= $pagination->result[$i]->accountname;	

				$prevcarry 			= $this->trial_balance->getPrevCarry($accountid,$datefilterFrom);
				$balcarry			= $this->trial_balance->getBalanceCarry($accountid,$datefilterFrom,$datefilterTo);
				$amount				= $this->trial_balance->getCurrent($accountid,$datefilterFrom,$datefilterTo);

				$debit 				= ($amount > 0) ? $amount : 0;
				$credit 			= ($amount < 0) ? abs($amount) : 0;
				$periodbalance      = $amount;
				//$periodbalance      = ($amount > 0) ? $periodbalance : $periodbalance * -1;
				//$periodbalance      = ($amount > 0) ? $debit : -$amount;
				//$balcarry         = ($balcarry < 0) ? $balcarry * -1 : 0; 
				$accumulatedbalance = $prevcarry + $balcarry + $periodbalance;
				// echo "Prev Carry = ".$prevcarry." \n";
				// echo "Bal Carry = ".$balcarry."\n";
				// echo "Period = ".$periodbalance."\n";
				// echo "Accumulated = ".$accumulatedbalance."\n";

				$totalprevcarry 		+= $prevcarry;
				$totalbalcarry  		+= $balcarry;
				$totaldebit 			+= $debit;
				$totalcredit 			+= $credit;
				$totalperiodbalance 	+= $periodbalance;
				$totalaccumulatedbalance += $accumulatedbalance;

				$periodbalance 		= ($periodbalance < 0) ? '('.number_format(abs($periodbalance),2).')' : number_format(abs($periodbalance),2);
				$balcarry 			= ($balcarry < 0) ? '('.number_format(abs($balcarry),2).')' : number_format(abs($balcarry),2);
				 $credit 			= ($credit < 0) ? '('.number_format(abs($credit),2).')' : number_format(abs($credit),2);
				$debit 				= ($debit < 0) ? '('.number_format(abs($debit),2).')' : number_format(abs($debit),2);
				$prevcarry 			= ($prevcarry < 0) ? '('.number_format(abs($prevcarry),2).')' : number_format(abs($prevcarry),2);
				//$periodbalance  	= ($periodbalance < 0) ? '('.number_format(abs($periodbalance),2).')' : number_format(abs($periodbalance),2);
				$accumulatedbalance = ($accumulatedbalance < 0) ? '('.number_format(abs($accumulatedbalance),2).')' : number_format(abs($accumulatedbalance),2);

				$debitLink	= ($amount > 0) ? '<a href="javascript:void(0);" onClick="openList(\''.$accountid.'\');" >'.$debit.'</a>' : $debit;
				// $debitLink	= '<a href="javascript:void(0);" onClick="openList(\''.$accountcode.'\');" >'.$debit.'</a>';

				$creditLink	= ($amount < 0) ? '<a href="javascript:void(0);" onClick="openList(\''.$accountid.'\');" >'.$credit.'</a>' : $credit;
				// $creditLink	= '<a href="javascript:void(0);" onClick="openList(\''.$accountcode.'\');" >'.$credit.'</a>';

				$table .= "<tr>";																					
				$table .= '<td class="left">&nbsp;'.$accountcode.'</td>';
				$table .= '<td class="left">&nbsp;'.$accountname.'</td>';
				$table .= '<td class="text-right" >'.$prevcarry.'</td>';
				$table .= '<td class="text-right" >'.$balcarry.'</td>';
				$table .= '<td class="text-right" >'.$debitLink.'</td>';
				$table .= '<td class="text-right" >'.$creditLink.'</td>';
				$table .= '<td class="text-right" >'.$periodbalance.'</td>';
				$table .= '<td class="text-right" >'.$accumulatedbalance.'</td>';
				$table .= '</tr>';
			}	
			
			$totaldebit 				= ($totaldebit < 0) ? '('.number_format(abs($totaldebit),2).')' : number_format(abs($totaldebit),2);
			$totalcredit 				= ($totalcredit < 0) ? '('.number_format(abs($totalcredit),2).')' : number_format(abs($totalcredit),2);
			$totalperiodbalance 		= ($totalperiodbalance < 0) ? '('.number_format(abs($totalperiodbalance),2).')' : number_format(abs($totalperiodbalance),2);
			$totalaccumulatedbalance 	= ($totalaccumulatedbalance < 0) ? '('.number_format(abs($totalaccumulatedbalance),2).')' : number_format(abs($totalaccumulatedbalance),2);

			$table .= '<tr class="info">
						<td class="text-right" colspan="2"><strong>TOTAL</strong></td>
						<td class="text-right">'.$totalprevcarry.'</td>
						<td class="text-right">'.$totalbalcarry.'</td>
						<td class="text-right"><strong>'.$totaldebit.'</strong></td>
						<td class="text-right"><strong>'.$totalcredit.'</strong></td>
						<td class="text-right">'.$totalperiodbalance.'</td>
						<td class="text-right">'.$totalaccumulatedbalance.'</td>';

			$table.= '</tr>';

		}else{
			$table .= '<tr><td colspan="8" class="text-center"><b>No Records Found</b></td></tr>';
		}

		$pagination->table = $table;
		$pagination->csv   = $this->export();
		return $pagination;

	}

	private function export()
	{
		$data 		= $this->input->post(array('daterangefilter'));
		
		$data['daterangefilter'] = str_replace(array('%2C', '+'), array(',', ' '), $data['daterangefilter']);
		
		$datefilter	= $data['daterangefilter'];	
		$datefilter = explode('-', $datefilter);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		
		$default_datefilter = date("M d, Y",strtotime('first day of this month')).' - '.date("M d, Y",strtotime('last day of this month'));		
		$datefilterFrom = (!empty($dates[0]))? $dates[0] : "";
		$datefilterTo   = (!empty($dates[1]))? $dates[1] : "";
		$datefilter     = (!empty($daterangefilter))? $daterangefilter : $default_datefilter;

		$currentyear 	= date("Y",strtotime($datefilterTo));
		$prevyear 		= date("Y",strtotime($datefilterFrom." -1 year"));

		$totaldebit 	= 0;
		$totalcredit 	= 0;
		$totalperiodbalance = 0;
		$totalaccumulatedbalance = 0;
		$retrieved = $this->trial_balance->fileExport($currentyear,$prevyear);
		
		$header		= array('Account Code','Account Name','Prev Carryforward','Balance Carryforward','Total Debit','Total Credit','Balance for the Period','Accumulated Balance');

		$csv 	= '';
		$csv 	.= 'Trial Balance';
		$csv 	.= "\n\n";
		$csv 	.= '"' . implode('","',$header).'"';
		$csv 	.= "\n";

		$filtered 	=	array_filter($retrieved);

		if (!empty($filtered)){
			foreach ($filtered as $key => $row){
				$accountid 			= 	$row->accountid;
				$accountcode  		=	$row->accountcode;
				$accountname  		=	$row->accountname;
				
				$prevcarry 			= $this->trial_balance->getPrevCarry($accountid,$datefilterFrom);
				$balcarry			= $this->trial_balance->getBalanceCarry($accountid,$datefilterFrom,$datefilterTo);
				$amount				= $this->trial_balance->getCurrent($accountid,$datefilterFrom,$datefilterTo);

				$debit 				= ($amount > 0) ? $amount : 0;
				$credit 			= ($amount < 0) ? abs($amount) : 0;
				$periodbalance      = $amount;

				// $debit 				= ($amount > 0) ? $amount : 0;
				// $credit 			= ($amount < 0) ? $amount * -1 : 0;
				// $periodbalance      = ($amount > 0) ? $debit : $credit;
				// $periodbalance      = ($amount > 0) ? $periodbalance : $periodbalance * -1;
				$accumulatedbalance = $prevcarry + $balcarry + $periodbalance;
					
				$totaldebit 				+= $debit;
				$totalcredit 				+= $credit;
				$totalperiodbalance 		+= $periodbalance;
				$totalaccumulatedbalance 	+= $accumulatedbalance;

				$periodbalance 		= ($periodbalance < 0) ? '('.number_format(abs($periodbalance),2).')' : number_format(abs($periodbalance),2);
				$balcarry 			= ($balcarry < 0) ? '('.number_format(abs($balcarry),2).')' : number_format(abs($balcarry),2);
				$credit 			= ($credit < 0) ? '('.number_format(abs($credit),2).')' : number_format(abs($credit),2);
				$debit 				= ($debit < 0) ? '('.number_format(abs($debit),2).')' : number_format(abs($debit),2);
				$prevcarry 			= ($prevcarry < 0) ? '('.number_format(abs($prevcarry),2).')' : number_format(abs($prevcarry),2);
				$accumulatedbalance = ($accumulatedbalance < 0) ? '('.number_format(abs($accumulatedbalance),2).')' : number_format(abs($accumulatedbalance),2);

				$debitLink	= $debit;
				$creditLink	= $credit;

				$csv .= '"' . $accountcode . '",';
				$csv .= '"' . $accountname . '",';
				$csv .= '"' . $prevcarry . '",';
				$csv .= '"' . $balcarry . '",';
				$csv .= '"' . $debitLink . '",';
				$csv .= '"' . $creditLink . '",';
				$csv .= '"' . $periodbalance . '",';
				$csv .= '"' . $accumulatedbalance . '"';
				$csv .= "\n";
			}

			$totaldebit 				= ($totaldebit < 0) ? '('.number_format(abs($totaldebit),2).')' : number_format(abs($totaldebit),2);
			$totaldebit 				= ($totalcredit < 0) ? '('.number_format(abs($totalcredit),2).')' : number_format(abs($totalcredit),2);
			$totalperiodbalance 		= ($totalperiodbalance < 0) ? '('.number_format(abs($totalperiodbalance),2).')' : number_format(abs($totalperiodbalance),2);
			$totalaccumulatedbalance 	= ($totalaccumulatedbalance < 0) ? '('.number_format(abs($totalaccumulatedbalance),2).')' : number_format(abs($totalaccumulatedbalance),2);
			
			$csv .= '"","","","","' . $totaldebit . '","' . $totaldebit . '","' . $totalperiodbalance . '","' . $totalaccumulatedbalance . '"';
			$csv .= "\n";
		}

		return $csv;
	}

	private function load_account_transactions(){
		$data = $this->input->post(array('accountcode', 'daterangefilter','items'));
		$result = $this->trial_balance->load_account_transactions($data);
		// var_dump($result["title"]);
		return array(
			'title' => $result["title"],
			'qtr'   => $result["qtr"],
			'table' => $result["table"],
			'accountfilter' => $result["accountfilter"]
		);
	}

}
?>