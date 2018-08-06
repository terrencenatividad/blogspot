<?php
class controller extends wc_controller {

	public function __construct() 
	{
		parent::__construct();
		$this->ui = new ui();
		$this->view->header_active = 'report/';
		$this->ap_transaction	= new ap_transaction();
		$this->input            = new input();
		$this->show_input 	    = true;
		$session                = new session();
	}

	public function view() 
	{
		$this->view->title = 'AP Transactions Report';
		$data['ui'] = $this->ui;
		$data['show_input'] 	= true;
		$data['datefilter'] 	= $this->date->datefilterMonth();
		$data['supplier_list']	= $this->ap_transaction->getSuppliers();
		$this->view->load('ap_transaction', $data);
	}

	public function ajax($task)
	{
		header('Content-type: application/json');
		$result 	=	"";

		if($task == 'list')
		{
			$result = $this->ajax_list();
		}
		else if($task == 'load_supplier_list')
		{
			$result = $this->load_supplier_list();
		}
		else if($task == 'load_voucher_list')
		{
			$result = $this->load_voucher_list();
		}
		else if($task == 'export')
		{
			$result = $this->export();
		}

		echo json_encode($result); 
	}

	private function load_supplier_list(){
		$data_post = $this->input->post(array('daterangefilter','supplier'));
	
		$pagination = $this->ap_transaction->getsupplierList($data_post);
		$tablerow = "";
		
		if(!empty($pagination->result))
		{
			for($i=0;$i < count($pagination->result);$i++)
			{	
				
				$suppliercode		= $pagination->result[$i]->suppliercode;
			    $suppliername		= $pagination->result[$i]->suppliername;
			    $supplieraddress	= $pagination->result[$i]->supplieraddress;
			   
				$tablerow	.= '<tr data-id = "'.$suppliercode.'">';
				$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$suppliercode.'</td>';
				$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$suppliername.'</td>';
				$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$supplieraddress.'</td>';
				$tablerow	.= '</tr>';
				
				
			}	
		}else{
			$tablerow	.= '<tr>';
			$tablerow	.= '<td class="center" colspan="3">- No Records Found -</td>';
			$tablerow	.= '</tr>';
		}

		$pagination->table = $tablerow;
		return $pagination;					
	}

	private function load_voucher_list()
	{
		$data_post = $this->input->post(array('daterangefilter','supplier','voucher','status','limit', "search"));
	
		$pagination = $this->ap_transaction->getVoucherList($data_post);
		$tablerow = "";
	
		if(!empty($pagination->result))
		{
			for($i=0;$i < count($pagination->result);$i++)
			{	
				$voucherno		    = $pagination->result[$i]->voucherno;
			    $suppliercode		= $pagination->result[$i]->suppliercode;
			    $suppliername		= $pagination->result[$i]->suppliername;
				$referenceno        = $pagination->result[$i]->referenceno;
			    $transactiondate	= $this->date->dateFormat($pagination->result[$i]->transactiondate); 
				$invoiceno          = $pagination->result[$i]->invoiceno;
				$invoicedate        = $this->date->dateFormat($pagination->result[$i]->invoicedate);
			    $duedate            = $this->date->dateFormat($pagination->result[$i]->duedate); 

				$tablerow	.= '<tr data-id = "'.$voucherno.'">';
				$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$voucherno.'</td>';
				$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$suppliername.'</td>';
				$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$referenceno.'</td>';
				$tablerow	.= '<td class="text-center" class="left" style="vertical-align:middle;">&nbsp;'.$transactiondate.'</td>';
				$tablerow	.= '<td class="text-center" style="vertical-align:middle;">&nbsp;'.$invoiceno.'</td>';
				$tablerow	.= '<td class="text-center" style="vertical-align:middle;">&nbsp;'.$invoicedate.'</td>';
				$tablerow	.= '<td class="text-center" style="vertical-align:middle;">&nbsp;'.$duedate.'</td>';
				$tablerow	.= '</tr>';	
			}	
		}
		else
		{
			$tablerow	.= '<tr>';
			$tablerow	.= '<td class="text-center" colspan="7">- No Records Found -</td>';
			$tablerow	.= '</tr>';
		}

		$pagination->table = $tablerow;
		return $pagination;	
	}

	private function ajax_list()
	{
		$data_post = $this->input->post(array('daterangefilter','supplier','voucher','status'));
	
		$pagination = $this->ap_transaction->getInvoiceList($data_post);
		
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
				
				$suppliercode  		=	$row->suppliercode;
				$amount       		= 	$row->amount;
				$applied       		= 	$row->amountpaid;
				$balance       		= 	$row->balance;
				$total 				+= 	$amount;
				$total_applied 		+= 	$applied;
				$total_balance 		+= 	$balance;

				if( !isset($sub[$suppliercode]['amount']) ){
					$sub[$suppliercode]['amount'] 			=	$amount;
					$subapplied[$suppliercode]['applied'] 	=	$applied;
					$subbalance[$suppliercode]['balance'] 	=	$balance;
				} else {
					$sub[$suppliercode]['amount'] 			+=	$amount;
					$subapplied[$suppliercode]['applied'] 	+=	$applied;
					$subbalance[$suppliercode]['balance'] 	+=	$balance;
				}
			}

			foreach ($pagination->result as $key => $row) 
			{	
				$suppliercode 		= $row->suppliercode;
				$suppliername 		= $row->suppliername;
				$voucherno    		= $row->voucherno;
				$pvoucherno   		= $row->pvoucherno;
				$transactiondate 	= date('M j, Y', strtotime($row->transactiondate));
				$invoiceno    		= $row->invoiceno;
				$amount       		= number_format($row->amount,2);
				$amountpaid 		= number_format($row->amountpaid,2);
				$balance      		= number_format($row->balance,2);
				$particulars        = $row->particulars;
				$terms              = $row->terms;
				$status             = $row->stat;
				$aplink      	 	= BASE_URL."financials/accounts_payable/view/".$voucherno;
				// $rvlink				= (isset($rvoucherno)) 	? 	BASE_URL."financials/accounts_receivable/manage/view/".$voucherno 	: 	"";
				$pvlink 			= "";
				$prevcust 			= $suppliercode;

				if( $prevcust != $nextcust ) {
					
					if ($nextcust != "") {
						$tablerow 	.= '<tr style="background:#fff;">';
						$tablerow	.= '<td class="text-right" colspan="4" style="vertical-align:middle;">&nbsp;<b>Subtotal:</b></td>';
						$tablerow	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;<b>'.number_format($subtotal,2)  .'</b></td>';
						$tablerow	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;<b>'.number_format($sub_applied,2)  .'</b></td>';
						$tablerow	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;<b>'.number_format($sub_balance,2)  .'</b></td>';
						$tablerow	.= '<td colspan="3" style="vertical-align:middle;">&nbsp;</td>';
						$tablerow 	.= '</tr>';
					}
					$tablerow 	.= '<tr style="background:#DDD;">';
					$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$suppliercode.'</td>';
					$tablerow	.= '<td class="left" colspan="9" style="vertical-align:middle;">&nbsp;<b>'.$suppliername.'</b></td>';
					$tablerow 	.= '</tr>';
				}
				
				$tablerow 	.= '<tr>';
				$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;<a href="'.$aplink.'" target="_blank" >'.$voucherno.'</a></td>';
				$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$pvoucherno.'</td>';
				$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$transactiondate.'</td>';
				$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$invoiceno .'</td>';
				$tablerow	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;'.$amount  .'</td>';
				$tablerow	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;'.$amountpaid  .'</td>';
				$tablerow	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;'.$balance  .'</td>';
				$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$particulars  .'</td>';
				$tablerow	.= '<td class="text-center" style="vertical-align:middle;">&nbsp;'.$terms  .'</td>';
				$tablerow	.= '<td class="text-center" style="vertical-align:middle;">&nbsp;'.$status  .'</td>';
				$tablerow 	.= '</tr>';

				$nextcust 	= $prevcust;

				$subtotal 		= $sub[$suppliercode]['amount'];
				$sub_applied 	= $subapplied[$suppliercode]['applied'];
				$sub_balance	= $subbalance[$suppliercode]['balance'];
			}	

			$tablerow 	.= '<tr style="background:#fff;">';
			$tablerow	.= '<td class="text-right" colspan="4" style="vertical-align:middle;">&nbsp;<b>Subtotal:</b></td>';
			$tablerow	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;<b>'.number_format($subtotal,2)  .'</b></td>';
			$tablerow	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;<b>'.number_format($sub_applied,2)  .'</b></td>';
			$tablerow	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;<b>'.number_format($sub_balance,2)  .'</b></td>';
			$tablerow	.= '<td colspan="3" style="vertical-align:middle;">&nbsp;</td>';
			$tablerow 	.= '</tr>';

			/**TOTAL AMOUNTS**/
			$tablerow 	.= '<tr style="background:#DDD;">';
			$tablerow	.= '<td class="text-right" colspan="4" style="vertical-align:middle;">&nbsp;<b>Total:</b></td>';
			$tablerow	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;<b>'.number_format($total,2)  .'</b></td>';
			$tablerow	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;<b>'.number_format($total_applied,2)  .'</b></td>';
			$tablerow	.= '<td class="text-right" style="vertical-align:middle;">&nbsp;<b>'.number_format($total_balance,2)  .'</b></td>';
			$tablerow	.= '<td colspan="3" style="vertical-align:middle;">&nbsp;</td>';
			$tablerow 	.= '</tr>';
		
		} else {
			$tablerow	.= '<tr>';
			$tablerow	.= '<td class="text-center" colspan="10">- No Records Found -</td>';
			$tablerow	.= '</tr>';
		}

		$pagination->table 	= $tablerow;
		$pagination->csv	= $this->export();
		return $pagination;

	}

	private function export()
	{
		$data 		= $this->input->post(array('daterangefilter','supplier','voucher','status'));
		$strdate	= $data['daterangefilter'];
		$datefilter = explode('-', $data['daterangefilter']);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}

		$retrieved = $this->ap_transaction->fileExport($data);
		
		$main 	= array("supplier Code","supplier Name"); 
		$header = array("Voucher Number","Payment Voucher","Transaction Date","Invoice No","Amount","Amount Applied","Balance","Remarks","Terms","Status");
		
		$csv 	= '';
		$csv 	.= 'Accounts Payable Transactions';
		$csv 	.= "\n\n";
		$csv 	.= '"Date:","'.$strdate.'"';
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
				
				$suppliercode  		=	$row->suppliercode;
				$amount       		= 	$row->amount;
				$applied       		= 	$row->amountpaid;
				$balance       		= 	$row->balance;
				$total 				+= 	$amount;
				$total_applied 		+= 	$applied;
				$total_balance 		+= 	$balance;

				if( !isset($sub[$suppliercode]['amount']) ){
					$sub[$suppliercode]['amount'] 			=	$amount;
					$subapplied[$suppliercode]['applied'] 	=	$applied;
					$subbalance[$suppliercode]['balance'] 	=	$balance;
				} else {
					$sub[$suppliercode]['amount'] 			+=	$amount;
					$subapplied[$suppliercode]['applied'] 	+=	$applied;
					$subbalance[$suppliercode]['balance'] 	+=	$balance;
				}
			}
			
			foreach ($filtered as $key => $row){
				$suppliercode 		= $row->suppliercode;
				$suppliername 		= $row->suppliername;
				$voucherno    		= $row->voucherno;
				$pvoucherno   		= $row->pvoucherno;
				$transactiondate 	= date('M j, Y', strtotime($row->transactiondate));
				$invoiceno    		= $row->invoiceno;
				$amount       		= number_format($row->amount,2);
				$amountpaid 		= number_format($row->amountpaid,2);
				$balance      		= number_format($row->balance,2);
				$particulars        = $row->particulars;
				$terms              = $row->terms;
				$status             = $row->stat;
				$prevcust 			= $suppliercode;

				if( $prevcust != $nextcust ) {
					
					if ($nextcust != "") {
						$csv .= '"","","","Subtotal","' . number_format($subtotal,2) . '","' . number_format($sub_applied,2) . '","' . number_format($sub_balance,2) . '","","",""';
						$csv .= "\n";
	
					}
					$tablerow 	.= '<tr style="background:#DDD;">';
					$tablerow	.= '<td class="left" style="vertical-align:middle;">&nbsp;'.$suppliercode.'</td>';
					$tablerow	.= '<td class="left" colspan="9" style="vertical-align:middle;">&nbsp;<b>'.$suppliername.'</b></td>';
					$tablerow 	.= '</tr>';

					$csv .= '"'.$suppliercode.'",';
					$csv .= '"'.$suppliername.'"';
					$csv .= "\n";
				}
				
				$csv .= '"'	.	$voucherno			.	'",';
				$csv .= '"'	. 	$pvoucherno 		. 	'",';
				$csv .= '"'	. 	$transactiondate 	. 	'",';
				$csv .= '"' . 	$invoiceno 			. 	'",';
				$csv .= '"' .	$amount 	 		. 	'",';
				$csv .= '"'	. 	$amountpaid 		. 	'",';
				$csv .= '"'	. 	$balance 			. 	'",';
				$csv .= '"'	. 	$particulars 		. 	'",';
				$csv .= '"' . 	$terms 	 			. 	'",';
				$csv .= '"' . 	$status 			. 	'"';
				$csv .= "\n";

				$nextcust 	= $prevcust;

				$subtotal 		= $sub[$suppliercode]['amount'];
				$sub_applied	= $subapplied[$suppliercode]['applied'];
				$sub_balance 	= $subbalance[$suppliercode]['balance'];
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