<?php
class controller extends wc_controller 
{
	public function __construct() 
	{
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->returns_customer	= new returns_customer();
		$this->item_model		= new item_model();
		$this->session			= new session();
		$this->show_input 	    = true;
		$this->data = array();
		$this->view->header_active  = 'report/';
	}

	public function listing() 
	{
		$this->view->title = 'Sales Return per Customer';
		$data['ui'] = $this->ui;
		$data['customer_list'] = $this->returns_customer->retrieveCustomerList();
		$data['warehouse_list'] = $this->returns_customer->getWarehouseList();
		$data['datefilter'] 	= $this->date->datefilterMonth();
		$data['show_input']  = $this->show_input;
		$this->view->load('returns_customer/returns_customer_list', $data);
	}

	public function view($warehouse, $cust_code, $datefilter, $data_type = 'return')
	{
		$cust_code =  base64_decode($cust_code);
		$datefilter = base64_decode($datefilter);
		$warehouse = $warehouse;

		$this->view->title = 'Detailed Report per Customer';
		$data = (array) $this->returns_customer->customerDetails($cust_code);
		$data['ui'] = $this->ui;
		$data['cust_code'] = $cust_code;
		$data['datefilter'] = $datefilter;
		$data['warehouse'] = $warehouse;
		$data['data_type'] = $data_type;
		$this->view->load('returns_customer/returns_customer', $data);
	}
	
	public function ajax($task) 
	{
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function get_invoice()
	{
		$data       = $this->input->post(array('customer','datefilter', 'warehouse', 'data_type'));
		$cust_code  = $data['customer'];
		$datefilter = $data['datefilter'];
		$warehouse  = $data['warehouse'];
		$data_type  = $data['data_type'];
		$pagination = $this->returns_customer->customerInvoices($cust_code,$datefilter, $warehouse, $data_type);
		$table      = '';
		
		if (empty($pagination->result)) 
		{
			$table = '<tr><td colspan="6" class="text-center"><b>No Records Found</b></td></tr>';
		}
		
		$total = 0;

		foreach ($pagination->result as $key => $row) 
		{
			$table .= '<tr>';
			$table .= '<td>' . $this->date->dateFormat($row->date) . '</td>';
			$table .= '<td><a href="' . BASE_URL . 'sales/sales_return/view/'.$row->voucherno.'">'.$row->voucherno.'</a></td>';
			$table .= '<td>' . $row->itemcode . '-' . $row->detailparticular .'</td>';
			$table .= '<td class="text-right">' . $row->issueqty . '</td>';
			$table .= '<td class="text-right">' . $row->uom . '</td>';
			$table .= '<td class="text-right">' . $this->amount($row->unitprice) . '</td>';
			$table .= '<td class="text-right">' . $this->amount($row->amount) . '</td>';
			$table .= '</tr>';

			$row->amount = str_replace(',','',$row->amount);

			$total += $row->amount;
		}

		$table .= '<tr>
					<td colspan="6" class = "text-right" style = "font-weight: bold;">Total Sales Return:</td>
				  	<td  class = "text-right">'.$this->amount($total).'</td>
				  </tr>';

		
		$pagination->table = $table;
		$pagination->csv   = $this->export_details();
		return $pagination;
	}

	private function ajax_list() 
	{
		$data       = $this->input->post(array('daterangefilter','customer','warehouse'));
		$datefilter = $data['daterangefilter'];
		$customer	= $data['customer'];
		$warehouse  = $data['warehouse'];
		$search  	=$this->input->post('search');
		$datefilter = explode('-', $datefilter);
		$dates		= array();
		
		foreach ($datefilter as $date) 
		{
			$dates[] = date('Y-m-d', strtotime($date));
		}

		$pagination = $this->returns_customer->customer_list($dates[0], $dates[1], $customer, $warehouse, $search);
		$table = '';
		
		if (empty($pagination->result)) 
		{
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		$totalAmount = 0.00;

		foreach ($pagination->result as $key => $row) 
		{
			$totalAmount += $row->amount;

			$table .= '<tr class="clickable" data-id="'. $row->warehouse.'/'. base64_encode($row->partnercode) . '/' . base64_encode($data['daterangefilter']).'">';
			$table .= '<td></td>';
			$table .= '<td><a>' . $row->name . '</a></td>';
			$table .= '<td class="text-left">' . $this->amount($row->amount) . '</td>';
			$table .= '</tr>';
		}
			$table .= '<tr>	
			
			<td></td>
			<td style="font-weight:bold">Total Amount</td>
			<td style="font-weight:bold">' .$this->amount($totalAmount). '</td></tr>';
		$pagination->table  = $table;
		$pagination->csv 	= $this->export();
		
		return $pagination;
	}

	private function export_details()
	{
		$data = $this->input->post(array('customer','warehouse','datefilter','data_type'));
		
		$customer = isset($data['customer'])  	?  	$data['customer'] 	: 	"";
		
		if( $customer != "--" ){
			$cust = $this->returns_customer->customerDetails($customer);
		} else {
			$cust = (object)array('partnercode' => '--','name'=>'--');

		}

		$title = "Detailed Sales Return Per Customer Report";
		$header = array("Transaction Date","SR No.","Item","Qty","UOM","Unit Price","Amount");

		$csv = '';
		$csv = '"' . $title . '"';
		$csv .= "\n\n";
		$csv .= '"Customer Code","'.$cust->partnercode.'"';
		$csv .= "\n";
		$csv .= '"Customer","'.$cust->name.'"';
		$csv .= "\n\n";
		$csv .= '"' . implode('","', $header) . '"';
		$csv .= "\n";
		

		$result = $this->returns_customer->export_details($data);

		$totalAmount = 0.00;
		
		if (!empty($result)){
			foreach ($result as $key => $row){
				$totalAmount += $row->amount;
				$csv .= '"' . $this->date->dateFormat($row->date) . '",';
				$csv .= '"' . $row->voucherno . '",';
				$csv .= '"' . $row->itemcode . '-' . $row->detailparticular . '",';
				$csv .= '"' . $row->issueqty . '",';
				$csv .= '"' . $row->uom . '",';
				$csv .= '"' . $this->amount($row->unitprice) . '",';
				$csv .= '"' . $this->amount($row->amount) . '"';
				$csv .= "\n";
			}
		}

		$csv .= '"","","","","","Total Amount:","' . $this->amount($totalAmount) . '"';
		return $csv;
	}

	private function export()
	{
		$data = $this->input->post(array('customer','warehouse','daterangefilter'));
		$data['daterangefilter'] = str_replace(array('%2C', '+'), array(',', ' '), $data['daterangefilter']);
		$result = $this->returns_customer->fileExport($data);

		$header = array("Customer", "Amount");

		$csv = 'Sales Return per Customer';
		$csv .= "\n\n";
		$csv .= '"Date:","'.$data['daterangefilter'].'"';
		$csv .= "\n\n";
		$csv .= '"' . implode('", "', $header) . '"';
		$csv .= "\n";
		
		$totalAmount = 0.00;
		if (!empty($result))
		{
			foreach ($result as $key => $row)
			{
				$totalAmount += $row->amount;
				$csv .= '"' . $row->name . '",';
				$csv .= '"' . $this->amount($row->amount) . '"';
				$csv .= "\n";
			}
		}
		$csv .= '"Total Amount:","' . $this->amount($totalAmount) . '"';
		return $csv;
	}

	private function amount($amount)
	{
		return number_format($amount,2);
	}

}