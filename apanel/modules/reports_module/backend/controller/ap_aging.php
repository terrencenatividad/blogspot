<?php
class controller extends wc_controller {

	public function __construct() 
	{
		parent::__construct();
		$this->ui = new ui();
		$this->view->header_active 	= 'report/';
		$this->ap_aging 			= new ap_aging();
		$this->input            	= new input();
		$this->show_input 	    	= true;
		$session                	= new session();
	}

	public function view() 
	{
		$this->view->title = 'Accounts Payable Aging';
		$data['ui'] = $this->ui;
		$data['show_input'] = true;
		$data['datefilter'] = date("M d, Y");
		$data['customer_list'] = $this->ap_aging->getCustomerList();
		$this->view->load('ap_aging', $data);
	}

	public function ajax($task)
	{
		header('Content-type: application/json');
		$result 	=	"";

		if($task == 'list')
		{
			$result = $this->ajax_list();
		}else if($task == 'export')
		{
			$result = $this->export();
		}
		echo json_encode($result); 
	}

	private function ajax_list()
	{
		$data = $this->input->post(array('daterangefilter','customer'));

		$daterangefilter	= isset($data['daterangefilter'])? $data['daterangefilter'] : "";
		$partnerfilter		= isset($data['customer'])? $data['customer'] : "";
		$sort				= isset($data['sort'])  ?  $data['sort']  : "";
		$sortBy				= isset($data['sortBy'])  ? $data['sortBy']  : "";
		//$default_datefilter = date("M d, Y",strtotime('first day of this month')).' - '.date("M d, Y",strtotime('last day of this month'));

		// $date_filter = explode('-', $daterangefilter);
		// foreach ($date_filter as $date) {
		//  	$dates[] = date('Y-m-d', strtotime($date));
		// }

		// $datefilterFrom = (!empty($dates[0]))? $dates[0] : "";
		// $datefilterTo   = (!empty($dates[1]))? $dates[1] : "";
		$datefilter     = date("Y-m-d",strtotime($daterangefilter));

		$pagination = $this->ap_aging->getApAging($datefilter,$partnerfilter);

		$tablerow = "";
		$totalcurrent = 0;
		$total30 	  = 0;
		$total60 	  = 0;
		$totalover	  = 0;
		$totalbalance = 0;
		$norecords 	  = 0;

		if(!empty($pagination->result))
		{
			for($i=0;$i < count($pagination->result);$i++)
			{	
				$dateDelayed   	= 0;
				$partner		= $pagination->result[$i]->partner;
			    $invoiceno		= $pagination->result[$i]->invoiceno;
			    $terms			= $pagination->result[$i]->terms;
			    $duedate		= $pagination->result[$i]->duedate;
			    $sourceno		= $pagination->result[$i]->sourceno;
			    $amount			= $pagination->result[$i]->amount;
				$voucher		= $pagination->result[$i]->voucher;
				$source			= $pagination->result[$i]->source;
			    $invoicedate	= $pagination->result[$i]->invoicedate;
			    $reference		= $pagination->result[$i]->reference;
			
				$paymentamount_fetch	= $this->ap_aging->getValue("pv_application","SUM(amount) as amount","apvoucherno = '$voucher' AND stat = 'posted' AND entereddate <= '$datefilter 11:59:59' "); 
			
				$paymentamount 	= $paymentamount_fetch[0]->amount;
				$paymentamount	= (!empty($paymentamount)) ? $paymentamount : 0;
			
				$balance		= $amount - $paymentamount;
				$diff 			= $this->ap_aging->dateDiff($datefilter,$duedate);
				$dateDelayed   	= $diff;
			
				//$viewlink		= BASE_URL."purchase/purchase_receipt/view/$reference";
				$viewlink		= BASE_URL."financials/accounts_payable/view/$voucher";

				// if($balance > 0)
				// {
					$tablerow	.= '<tr>';
					$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$partner.'</td>';
					$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;<a href="'.$viewlink.'" target="_blank" >'.$voucher.'</a></td>';
					$tablerow	.= '<td class="center" style="vertical-align:middle;">'.$terms.'</td>';
					$tablerow	.= '<td class="center" style="vertical-align:middle;">'.$this->date->dateFormat($duedate).'</td>';
					$tablerow	.= ($dateDelayed < 1) ? '<td class="right" style="vertical-align:middle;">'.number_format($balance,2).'</td>' : '<td class="right" style="vertical-align:middle;"></td>';
					$tablerow	.= ($dateDelayed > 0 && $dateDelayed < 31) ? '<td class="right" style="vertical-align:middle;">'.number_format($balance,2).'</td>' : '<td class="right" style="vertical-align:middle;"></td>';
					$tablerow	.= ($dateDelayed > 30 && $dateDelayed < 61) ? '<td class="right" style="vertical-align:middle;">'.number_format($balance,2).'</td>' : '<td class="right" style="vertical-align:middle;"></td>';
					$tablerow	.= ($dateDelayed > 60) ? '<td class="right" style="vertical-align:middle;">'.number_format($balance,2).'</td>' : '<td class="right" style="vertical-align:middle;"></td>';
					$tablerow	.= '<td class="right" style="vertical-align:middle;" >'.number_format($balance,2).'</td>';
					$tablerow	.= '</tr>';
					
					$totalcurrent 	+= ($dateDelayed < 1) ? $balance : 0;
					$total30 		+= ($dateDelayed > 0 && $dateDelayed < 31) ? $balance : 0;
					$total60 		+= ($dateDelayed > 30 && $dateDelayed < 61) ? $balance : 0;
					$totalover		+= ($dateDelayed > 60) ? $balance : 0;
					$totalbalance 	+= $balance;
					$norecords 	= 0;
				// } else {
				// 	$norecords 	+= 1;
				// }
			}	

			if( $norecords > 0 ){
				$tablerow	.= '<tr>';
				$tablerow	.= '<td colspan = "9" class="text-center" >No Records Found</td>';
				$tablerow	.= '</tr>';
			}

			/**TOTAL AMOUNTS**/
			$tablerow	.= '<tr style="background:#DDD">';
			$tablerow	.= '<td class="right" style="vertical-align:middle;" colspan="4"><strong>Total</strong>&nbsp;</td>';
			$tablerow	.= '<td class="right" style="vertical-align:middle;"><strong>'.number_format($totalcurrent,2).'</strong></td>';
			$tablerow	.= '<td class="right" style="vertical-align:middle;"><strong>'.number_format($total30,2).'</strong></td>';
			$tablerow	.= '<td class="right" style="vertical-align:middle;"><strong>'.number_format($total60,2).'</strong></td>';
			$tablerow	.= '<td class="right" style="vertical-align:middle;"><strong>'.number_format($totalover,2).'</strong></td>';
			$tablerow	.= '<td class="right" style="vertical-align:middle;"><strong>'.number_format($totalbalance,2).'</strong></td>';
			$tablerow	.= '</tr>';
		}
		else
		{
			$tablerow	.= '<tr>';
			$tablerow	.= '<td colspan = "9" class="text-center" >No Records Found</td>';
			$tablerow	.= '</tr>';
		}

		$pagination->table = $tablerow;
		$pagination->csv   = $this->export();
		return $pagination;
	}

	private function export()
	{
		$data 		= $this->input->post(array('daterangefilter','customer'));
		
		$data['daterangefilter'] = str_replace(array('%2C', '+'), array(',', ' '), $data['daterangefilter']);
		
		$datefilter	= $data['daterangefilter'];	
		// $datefilter = explode('-', $datefilter);
		// $dates		= array();
		// foreach ($datefilter as $date) {
		// 	$dates[] = date('Y-m-d', strtotime($date));
		// }
		
		// $default_datefilter = date("M d, Y",strtotime('first day of this month')).' - '.date("M d, Y",strtotime('last day of this month'));		
		// $datefilterFrom = (!empty($dates[0]))? $dates[0] : "";
		// $datefilterTo   = (!empty($dates[1]))? $dates[1] : "";

		$datefilter     = date("Y-m-d",strtotime($datefilter));
		$customer 	 	= isset($data['customer'])	? $data['customer'] : "";

		$totalcurrent = 0;
		$total30 	  = 0;
		$total60 	  = 0;
		$totalover	  = 0;
		$totalbalance = 0;

		$retrieved = $this->ap_aging->fileExport($datefilter,$customer);
		
		$header = array("Customer","Reference","Terms","Due Date","Current","1 - 30 Days","31 - 60 Days","Over 60 Days","Balance");
		
		$csv 	= '';
		$csv 	.= '"' . implode('","',$header).'"';
		$csv 	.= "\n";

		$filtered 	=	array_filter($retrieved);

		if (!empty($filtered)){
			foreach ($filtered as $key => $row){
				$dateDelayed   	= 0;
				$partner		= $row->partner;
			    $invoiceno		= $row->invoiceno;
			    $terms			= $row->terms;
			    $duedate		= $row->duedate;
			    $sourceno		= $row->sourceno;
			    $amount			= $row->amount;
				$voucher		= $row->voucher;
				$source			= $row->source;
			    $invoicedate	= $row->invoicedate;
				
				$paymentamount_fetch= $this->ap_aging->getValue("pv_application","SUM(amount) as amount","apvoucherno = '$voucher' AND stat = 'posted' AND entereddate <= '$datefilter 11:59:59' "); 
				
				$paymentamount 		= $paymentamount_fetch[0]->amount;
				$paymentamount		= (!empty($paymentamount)) ? $paymentamount : 0;
			
				$balance			= $amount - $paymentamount;

				$diff 			= $this->ap_aging->dateDiff($datefilter,$duedate);
				$dateDelayed   		= $diff;

				// if($balance > 0){
					
					$curr_	= ($dateDelayed < 1) ? number_format($balance,2) : 0;
					$curr_1	= ($dateDelayed > 0 && $dateDelayed < 31) ? number_format($balance,2) : 0;
					$curr_2	= ($dateDelayed > 30 && $dateDelayed < 61) ? number_format($balance,2) : 0;
					$curr_3	= ($dateDelayed > 60) ? number_format($balance,2) : 0;
					
					$duedate =	date("M d, Y",strtotime($duedate));

					$csv .= '"' . $partner 		. '",';
					$csv .= '"' . $voucher 	. '",';
					$csv .= '"' . $terms 		. '",';
					$csv .= '"' . $duedate 		. '",';
					$csv .= '"' . $curr_ 		. '",';
					$csv .= '"' . $curr_1 		. '",';
					$csv .= '"' . $curr_2 		. '",';
					$csv .= '"' . $curr_3 		. '",';
					$csv .= '"' . number_format($balance,2) . '"';
					$csv .= "\n";

					$totalcurrent 	+= ($dateDelayed < 1) ? $balance : 0;
					$total30 		+= ($dateDelayed > 0 && $dateDelayed < 31) ? $balance : 0;
					$total60 		+= ($dateDelayed > 30 && $dateDelayed < 61) ? $balance : 0;
					$totalover		+= ($dateDelayed > 60) ? $balance : 0;
					$totalbalance 	+= $balance;
				// }
			}

			$csv .= '"",';
			$csv .= '"",';
			$csv .= '"",';
			$csv .= '"Total",';
			$csv .= '"' . number_format($totalcurrent,2) 	. '",';
			$csv .= '"' . number_format($total30,2) 		. '",';
			$csv .= '"' . number_format($total60,2) 		. '",';
			$csv .= '"' . number_format($totalover,2) 		. '",';
			$csv .= '"' . number_format($totalbalance,2) 		. '"';
			$csv .= "\n";
		}

		return $csv;
	}

}
?>