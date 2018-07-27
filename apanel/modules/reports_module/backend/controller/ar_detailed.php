<?php
class controller extends wc_controller {

	public function __construct(){
		parent::__construct();
		$this->ui = new ui();
		$this->view->header_active = 'report/';
		$this->ar_detailed		= new ar_detailed();
		$this->input            = new input();
		$this->show_input 	    = true;
		$session                = new session();
	}

	public function view(){
		$this->view->title = 'AR Detailed Report';
		$data['ui'] = $this->ui;
		$data['show_input'] 	= true;
		$data['datefilter'] 	= date("M d, Y");
		$data['customer_list']	= $this->ar_detailed->getCustomers();
		$this->view->load('ar_detailed', $data);
	}

	public function ajax($task)
	{
		header('Content-type: application/json');
		$result 	=	"";

		if($task == 'list'){
			$result = $this->ajax_list();
		}else if($task == 'export'){
			$result = $this->export();
		}

		echo json_encode($result); 
	}

	private function ajax_list()
	{
		$data_post = $this->input->post(array('datefilter','customer'));
	
		$pagination = $this->ar_detailed->getInvoiceList($data_post);
		
		$total 			= 0;
		$total_applied 	= 0;
		$total_balance	= 0;
		$subtotal 		= 0;
		$sub_applied	= 0;
		$sub_balance	= 0;
		$tablerow 		= "";
		$prevcust 		= "";
		$nextcust 		= "";
		$sub 			= array();
		$subapplied 	= array();
		$subbalance 	= array();

		if(!empty($pagination->result))
		{
			foreach ($pagination->result as $key => $row) 
			{
				$customercode  		= $row->customercode;
				$amount       		= $row->amount;
				$voucherno    		= $row->voucherno;
				$payment       		= $this->ar_detailed->getPayments($voucherno,$data_post['datefilter']);
				$amountapplied 		= ($payment) ? $payment[0]->paymentamount : 0;
				$discountapplied 	= ($payment) ? $payment[0]->paymentdiscount : 0;
				$applied 			= ($amountapplied + $discountapplied);
				$balance       		= $amount - $applied;
				
				$total 				+= 	$amount;
				$total_applied 		+= 	$applied;
				$total_balance 		+= 	$balance;

				if( !isset($sub[$customercode]['amount']) ){
					$sub[$customercode]['amount'] 			=	$amount;
					$subapplied[$customercode]['applied'] 	=	$applied;
					$subbalance[$customercode]['balance'] 	=	$balance;
				} else {
					$sub[$customercode]['amount'] 			+=	$amount;
					$subapplied[$customercode]['applied'] 	+=	$applied;
					$subbalance[$customercode]['balance'] 	+=	$balance;
				}
			}

			foreach ($pagination->result as $key => $row) 
			{	
				$customercode 		= $row->customercode;
				$customername 		= $row->customername;
				$voucherno    		= $row->voucherno;
				$transactiondate 	= date('M j, Y', strtotime($row->transactiondate));
				$invoiceno    		= $row->invoiceno;
				$amount       		= $row->amount;
				$payment       		= $this->ar_detailed->getPayments($voucherno,$data_post['datefilter']);
				$amountapplied 		= ($payment) ? $payment[0]->paymentamount : 0;
				$discountapplied 	= ($payment) ? $payment[0]->paymentdiscount : 0;
				$amountpaid 		= ($amountapplied + $discountapplied);
				$balance 			= $amount - $amountpaid;
				$particulars        = $row->particulars;
				$aplink      	 	= BASE_URL."financials/accounts_receivable/view/".$voucherno;
				$pvlink 			= "";
				$prevcust 			= $customercode;

				if( $prevcust != $nextcust ) {
					
					if ($nextcust != "") {
						$tablerow 	.= '<tr style="background:#fff;">';
						$tablerow	.= '<td class="text-right" colspan="4" >&nbsp;<b>Subtotal:</b></td>';
						$tablerow	.= '<td class="text-right" >&nbsp;<b>'.number_format($subtotal,2)  .'</b></td>';
						$tablerow	.= '<td class="text-right" >&nbsp;<b>'.number_format($sub_applied,2)  .'</b></td>';
						$tablerow	.= '<td class="text-right" >&nbsp;<b>'.number_format($sub_balance,2)  .'</b></td>';
						$tablerow 	.= '</tr>';
					}
					$tablerow 	.= '<tr style="background:#DDD;">';
					$tablerow	.= '<td class="text-left" style="vertical-align:middle;">&nbsp;'.$customercode.'</td>';
					$tablerow	.= '<td class="text-left" colspan="6" style="vertical-align:middle;">&nbsp;<b>'.$customername.'</b></td>';
					$tablerow 	.= '</tr>';
				}
				
				$tablerow 	.= '<tr>';
				$tablerow	.= '<td class="left">&nbsp;<a href="'.$aplink.'" target="_blank" >'.$voucherno.'</a></td>';
				$tablerow	.= '<td class="left">&nbsp;'.$transactiondate.'</td>';
				$tablerow	.= '<td class="left">&nbsp;'.$invoiceno .'</td>';
				$tablerow	.= '<td class="text-left">&nbsp;'.$particulars  .'</td>';
				$tablerow	.= '<td class="text-right" >&nbsp;'.number_format($amount,2)  .'</td>';
				$tablerow	.= '<td class="text-right" >&nbsp;'.number_format($amountpaid,2)  .'</td>';
				$tablerow	.= '<td class="text-right" >&nbsp;'.number_format($balance,2)  .'</td>';
				$tablerow 	.= '</tr>';

				$nextcust 	= $prevcust;

				$subtotal 		= $sub[$customercode]['amount'];
				$sub_applied 	= $subapplied[$customercode]['applied'];
				$sub_balance	= $subbalance[$customercode]['balance'];
			}	

			$tablerow 	.= '<tr style="background:#fff;">';
			$tablerow	.= '<td class="text-right" colspan="4" >&nbsp;<b>Subtotal:</b></td>';
			$tablerow	.= '<td class="text-right">&nbsp;<b>'.number_format($subtotal,2)  .'</b></td>';
			$tablerow	.= '<td class="text-right">&nbsp;<b>'.number_format($sub_applied,2)  .'</b></td>';
			$tablerow	.= '<td class="text-right">&nbsp;<b>'.number_format($sub_balance,2)  .'</b></td>';
			$tablerow 	.= '</tr>';

			/**TOTAL AMOUNTS**/
			$tablerow 	.= '<tr style="background:#DDD;">';
			$tablerow	.= '<td class="text-right" colspan="4" >&nbsp;<b>Total:</b></td>';
			$tablerow	.= '<td class="text-right" >&nbsp;<b>'.number_format($total,2)  .'</b></td>';
			$tablerow	.= '<td class="text-right" >&nbsp;<b>'.number_format($total_applied,2)  .'</b></td>';
			$tablerow	.= '<td class="text-right" >&nbsp;<b>'.number_format($total_balance,2)  .'</b></td>';
			$tablerow 	.= '</tr>';
		
		} else {
			$tablerow	.= '<tr>';
			$tablerow	.= '<td class="text-center" colspan="7">- No Records Found -</td>';
			$tablerow	.= '</tr>';
		}

		$pagination->table 	= $tablerow;
		$pagination->csv	= $this->export();
		return $pagination;
	}

	private function export()
	{
		$data 		= $this->input->post(array('datefilter','customer'));
		$datefilter	= $data['datefilter'];
		$retrieved = $this->ar_detailed->fileExport($data);
		
		$main 	= array("Customer Code","Customer Name"); 
		$header = array("Voucher Number","Transaction Date","Invoice No","Remarks","Amount","Amount Paid","Balance");
		
		$csv 	= '';
		$csv 	.= 'Accounts Receivable Detailed Report';
		$csv 	.= "\n";
		$csv 	.= '"Date:","'.$datefilter.'"';
		$csv 	.= "\n\n";
		$csv 	.= '"' . implode('","',$main).'"';
		$csv 	.= "\n";
		$csv 	.= '"' . implode('","',$header).'"';
		$csv 	.= "\n";

		$filtered 	=	array_filter($retrieved);

		$total 			= 0;
		$total_applied 	= 0;
		$total_balance	= 0;
		$subtotal 		= 0;
		$sub_applied 	= 0;
		$sub_balance 	= 0;
		$tablerow 		= "";
		$prevcust 		= "";
		$nextcust 		= "";
		$sub 			= array();
		$subapplied 	= array();
		$subbalance 	= array();

		if (!empty($filtered)){
			foreach ($filtered as $key => $row) 
			{
				$customercode  		= $row->customercode;
				$amount       		= $row->amount;
				$voucherno    		= $row->voucherno;
				$payment       		= $this->ar_detailed->getPayments($voucherno,$datefilter);
				$amountapplied 		= ($payment) ? $payment[0]->paymentamount : 0;
				$discountapplied 	= ($payment) ? $payment[0]->paymentdiscount : 0;
				$amountpaid 		= ($amountapplied + $discountapplied);
				$applied       		= $amountpaid;
				$balance 			= $amount - $amountpaid;
				$total 				+= 	$amount;
				$total_applied 		+= 	$applied;
				$total_balance 		+= 	$balance;

				if( !isset($sub[$customercode]['amount']) ){
					$sub[$customercode]['amount'] 			=	$amount;
					$subapplied[$customercode]['applied'] 	=	$applied;
					$subbalance[$customercode]['balance'] 	=	$balance;
				} else {
					$sub[$customercode]['amount'] 			+=	$amount;
					$subapplied[$customercode]['applied'] 	+=	$applied;
					$subbalance[$customercode]['balance'] 	+=	$balance;
				}
			}
			
			foreach ($filtered as $key => $row){
				$customercode 		= $row->customercode;
				$customername 		= $row->customername;
				$voucherno    		= $row->voucherno;
				$transactiondate 	= date('M j, Y', strtotime($row->transactiondate));
				$invoiceno    		= $row->invoiceno;
				$amount       		= $row->amount;
				$payment       		= $this->ar_detailed->getPayments($voucherno,$datefilter);
				$amountapplied 		= ($payment) ? $payment[0]->paymentamount : 0;
				$discountapplied 	= ($payment) ? $payment[0]->paymentdiscount : 0;
				$amountpaid 		= ($amountapplied + $discountapplied);
				$balance      		= $amount - $amountpaid;
				$particulars        = $row->particulars;
				$prevcust 			= $customercode;

				if( $prevcust != $nextcust ) {
					
					if ($nextcust != "") {
						$csv .= '"","","","Subtotal","' . number_format($subtotal,2) . '","' . number_format($sub_applied,2) . '","' . number_format($sub_balance,2) . '","","",""';
						$csv .= "\n";
	
					}
					$csv .= '"'.$customercode.'",';
					$csv .= '"'.$customername.'"';
					$csv .= "\n";
				}
				
				$csv .= '"'	.	$voucherno			.	'",';
				$csv .= '"'	. 	$transactiondate 	. 	'",';
				$csv .= '"' . 	$invoiceno 			. 	'",';
				$csv .= '"'	. 	$particulars 		. 	'",';
				$csv .= '"' .	number_format($amount,2) 	 		. 	'",';
				$csv .= '"'	. 	number_format($amountpaid,2) 		. 	'",';
				$csv .= '"'	. 	number_format($balance,2)			. 	'"';
				
				$csv .= "\n";

				$nextcust 	= $prevcust;

				$subtotal 		= $sub[$customercode]['amount'];
				$sub_applied	= $subapplied[$customercode]['applied'];
				$sub_balance 	= $subbalance[$customercode]['balance'];
			}

			$csv .= '"","","","Subtotal","' . number_format($subtotal,2) . '","' . number_format($sub_applied,2) . '","' . number_format($sub_balance,2) . '","","",""';
			$csv .= "\n";

			/**TOTAL AMOUNTS**/
			$csv .= '"","","","Total","' . number_format($total,2) . '","' . number_format($total_applied,2) . '","' . number_format($total_balance,2) . '","","",""';
		}
		return $csv;
	}
}
?>