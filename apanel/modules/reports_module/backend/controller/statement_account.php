<?php
class controller extends wc_controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->url 			    = new url();
		$this->soa 				= new statement_account();
		$this->input            = new input();
		$this->ui 			    = new ui();
		$this->view->title      = 'Statement of Account';
		$this->show_input 	    = true;

		$session                = new session();
		$this->companycode      = $session->get('companycode');
	}

	public function listing()
	{
		$data['ui'] 					= $this->ui;
		$data['show_input'] 			= true;
		$data['date_today'] 			= date("M d, Y");
		$data['requestor_list'] 		= $this->soa->retrieveCustomerList();
		$data['datefilter'] 			= $this->date->datefilterMonth();
		$this->view->load('statement_account', $data);
	}

	public function ajax($task)
	{
		header('Content-type: application/json');
		$result 	=	"";
		if($task == 'soa_listing') 
		{
			$result = $this->soa_listing();
		}
		echo json_encode($result); 
	}

	private function soa_listing()
	{
		$posted_data 	= $this->input->post(array("daterangefilter", "custfilter"));
		$cust 			= $this->input->post("custfilter");
		$pagination 	= $this->soa->retrieveListing($posted_data);
		$cDetails 		= $this->soa->retrieveCustomerDetails($cust);
		$prevBal 		= $this->soa->getPreviousBalance($posted_data);
		$grandBal 		= $this->soa->getGrandBalance($posted_data);

		$prevbalance 	= 0;
		if (!empty($prevBal)) {
			$prevbalance = $prevBal[0]->amount - $prevBal[0]->payment;
		}
		$grandbalance 	= 0;
		if (!empty($grandBal)) {
			$grandbalance = $grandBal[0]->amount - $grandBal[0]->payment;
			$grandbalance +=  $prevbalance;
		}
		if (empty($cDetails)) {
			$cDetails = (object) array(
							'name' => '',
							'address1' => ''
						);
		}
		$date 				= $this->input->post('daterangefilter');
		$datefilterArr		= explode(' - ',$date);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
		$table 				= '';
		
		if( !empty($pagination->result) ) :

			$balance = 0;
			if($pagination->page == 1){
				$balance= $prevbalance;
				$table .= '<tr>';						
				$table .= '<td class="warning" colspan="2"> As of '. date("M d, Y",strtotime($datefilterFrom)) .  '</td>
							<td colspan="4" class ="text-center warning">Previous Balance (Forwarded)</td>
							<td class="text-right warning">' .number_format($prevbalance,2,".","."). '</td></tr>';
			}
			foreach ($pagination->result as $key => $row) {

				$table .= '<tr>';
				$table .= '<td>' . date("M d, Y",strtotime($row->invoicedate)) .  '</td>';
				$table .= '<td>' . $row->invoiceno . '</td>';
				$table .= '<td>' . $row->documenttype . '</td>';
				$table .= '<td>' . $row->reference . '</td>';
				$table .= '<td>' . $row->particulars . '</td>';
				if($row->documenttype == 'Payment' || $row->documenttype == 'Credit Memo'){
					 $row->amount *= -1;
				}
				$table .= '<td class="text-right">' .   $this->amount($row->amount) . '</td>';
				$balance += $row->amount;
				$balance = ($balance >= 0) ? $balance : 0;
				$table .= '<td class="text-right">' .   $this->amount($balance). '</td>';
				$table .= '</tr>';
			}
		$table .= '<tr>	
			<td class="text-right warning" colspan="6"><strong>Sub Total Balance</strong></td>
			<td class="text-right warning"><strong>' .number_format($balance,2,".",","). '</strong></td></tr>';
		$table .= '<tr>	
			<td class="text-right warning" colspan="6"><strong>Grand Total Balance</strong></td>
			<td class="text-right warning"><strong>' .number_format($grandbalance,2,".",","). '</strong></td></tr>';

		else:
			$table .= '<tr>';						
			$table .= '<td class="warning" colspan="2"> As of '. date("M d, Y",strtotime($datefilterFrom)) .  '</td>
						<td colspan="4" class ="text-center warning">Previous Balance (Forwarded)</td>
						<td class="text-right warning">' .number_format($prevbalance,2,".","."). '</td></tr>';
			$table .= "<tr>
							<td colspan = '7' class = 'text-center'>
							Customer 
							<strong>".$cDetails->name."</strong> has no transactions for the date <strong>". date("M d, Y",strtotime($datefilterFrom)) ." - ".date("M d, Y",strtotime($datefilterTo))."</strong>
							</td>
					  </tr>";
		endif;
		$pagination->table = $table;
		$pagination->c_name = $cDetails->name;
		$pagination->c_add	= $cDetails->address1;
		$pagination->csv 	= $this->export();
		return $pagination;
	}

	private function export()
	{
		$posted_data 	= $this->input->post(array("daterangefilter", "custfilter"));
		$cust 			= $this->input->post("custfilter");
		$result 		= $this->soa->fileExport($posted_data);
		$prevBal 		= $this->soa->getPreviousBalance($posted_data);

		$balance 		= 0;
		$prevbalance 	= 0;
		if (!empty($prevBal)) {
			$prevbalance = $prevBal[0]->amount - $prevBal[0]->payment;
		}
		$cDetails 		= $this->soa->retrieveCustomerDetails($cust);
		
		$date 			= $this->input->post('daterangefilter');
		$datefilterArr	= explode(' - ',$date);
		$datefilterFrom	= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo	= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
		$header = array("Transaction Date","Invoice No.", "Type","Ref No.","Description","Amount","Balance");
		$csv = '';
		$csv = '"' . implode('", "', $header) . '"';
		$csv .= "\n";
		$amount = 0.00;

		$csv = '';
		$csv .= '"' . 'Statement of Account' . '",';
		$csv .= "\n";
		$csv .= "\n";
		$csv .= '"' . 'Customer:' . '",';
		$csv .= '"' . $cDetails->name . '",';
		$csv .= "\n";
		$csv .= '"' . 'Address:' . '",';
		$csv .= '"' . $cDetails->address1 . '",';
		$csv .= "\n";
		$csv .= '"' . 'Date' . '",';
		$csv .= '"' . $date . '",';
		
		$csv .= "\n";
		$csv .= "\n";
		$csv .= '"' . implode('","', $header) . '"';
		$csv .= "\n";
		$balance = $prevbalance;
		if (!empty($result)){
			$balance= $prevbalance;
			
			$csv .=  '"As of '.$this->date($datefilterFrom).'",';
			$csv .=  '"",';
			$csv .=  '"",';
			$csv .=  '"Previous Balance (Forwarded)",';
			$csv .=  '"",';
			$csv .=  '"",';
			$csv .=  '"'.$this->amount($prevbalance).'"';
			$csv .= "\n";
			foreach ($result as $key => $row){
				$csv .=  '"'.$this->date($row->invoicedate).'",';
				$csv .=  '"'.$row->invoiceno.'",';
				$csv .=  '"'.$row->documenttype.'",';
				$csv .=	 '"'.$row->reference.'",';
				$csv .=  '"'.$row->particulars.'",';
				if($row->documenttype == 'Payment' || $row->documenttype == 'Credit Memo'){
					 $row->amount *= -1;
				}
				$csv .=  '"'.$this->amount($row->amount).'",';
				$balance += $row->amount;
				$balance = ($balance >= 0) ? $balance : 0;
				$csv .=  '"'.$this->amount($balance).'"';
				$csv .= "\n";
			}
			
		}
		$csv .= '"","","","","","Grand Total Balance","'.$this->amount($balance).'"';
		return $csv;
	}

	private function amount($amount)
	{
		return number_format($amount,2);
	}

	private function date($date)
	{
		return date("M d, Y",strtotime($date));
	}

}
?>