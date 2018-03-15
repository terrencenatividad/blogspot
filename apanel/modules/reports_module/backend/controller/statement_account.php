<?php
class controller extends wc_controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->url 			    = new url();
		$this->soa 				= new statement_account();
		$this->seq 				= new seqcontrol();
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
		$this->view->load('statement_account/statement_accountlist', $data);
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
		$posted_data 	= $this->input->post(array("daterangefilter", "custfilter", "search", "filter","sort"));
		$search = $this->input->post("search");
		$filter = $this->input->post("filter");
		$cust = $this->input->post("custfilter");
		$sort = $this->input->post("sort");
		$pagination = $this->soa->retrieveListing($posted_data);
		$cDetails = $this->soa->retrieveCustomerDetails($cust);
		if (empty($cDetails)) {
			$cDetails = (object) array(
							'name' => '',
							'address1' => ''
						);
		}
		
		$date 	= $this->input->post('daterangefilter');
		$datefilterArr		= explode(' - ',$date);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
		$table 	= '';
		
		if( !empty($pagination->result) ) :

			$amount = 0.00;
			$table .= '<tr>';						
			$table .= '<td> As of '. date("M d, Y",strtotime($datefilterFrom)) .  '</td>
			<td></td>
			<td colspan="4" class ="text-center">Previous Balance (Forwarded)</td>
			<td>' .number_format($amount,2,".","."). '</td></tr>';
			foreach ($pagination->result as $key => $row) {

				$table .= '<tr>';				
				$table .= '	
										</ul>
									</div>
								</td>';
			
				$table .= '<td>' . date("M d, Y",strtotime($row->invoicedate)) .  '</td>';
				$table .= '<td>' . $row->invoiceno . '</td>';
				if($row->transtype == 'RV'){
					$table .= '<td>Payment</td>';
				} else {
					$table .= '<td>Invoice</td>';
				}
				$table .= '<td>' . $row->referenceno . '</td>';
				$table .= '<td>' . $row->particulars . '</td>';
				if($row->transtype == 'RV'){
					 $row->invoiceamount *= -1;
				}
				$table .= '<td>' .   $this->amount($row->invoiceamount) . '</td>';
				$amount += $row->invoiceamount;
				$table .= '<td>' .   $this->amount($amount) . '</td>';
				$table .= '</tr>';
			}
		$table .= '<tr>	
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td>Current Balance</td>
			<td>' .number_format($amount,2,".",","). '</td></tr>';
		else:
			$table .= "<tr>
							<td colspan = '7' class = 'text-center'>No Records Found</td>
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
		$posted_data 	= $this->input->post(array("daterangefilter", "custfilter", "search", "filter"));
		$search 		= $this->input->post("search");
		$filter 		= $this->input->post("filter");
		$cust 			= $this->input->post("custfilter");
		$result 		= $this->soa->fileExport($posted_data);

		$cDetails = $this->soa->retrieveCustomerDetails($cust);
	
		
		$date 	= $this->input->post('daterangefilter');
		$datefilterArr		= explode(' - ',$date);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
		$header = array("Invoice Date","Invoice Number", "Document Type","Ref No.","Description","Amount","Balance");
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

		if (!empty($result)){
			
			foreach ($result as $key => $row){
				$csv .=  '"'.$this->date($row->invoicedate).'",';
				$csv .=  '"'.$row->invoiceno.'",';
				if($row->transtype == 'RV'){
					$csv .= '"Payment",';
				} else {
					$csv .= '"Invoice",';
				}
				$csv .=	 '"'.$row->referenceno.'",';
				$csv .=  '"'.$row->particulars.'",';
				if($row->transtype == 'RV'){
					 $row->invoiceamount *= -1;
				}
				$csv .=  '"'.$this->amount($row->invoiceamount).'",';
				$amount += $row->invoiceamount;
				$csv .=  '"'.$this->amount($amount).'",';
				$csv .= "\n";
			}
			
		}
		$csv .= '"","","","","","Current Balance","'.$this->amount($amount).'"';
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