<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->stock_based   	= new stock_based();
		$this->session			= new session();
		$this->data = array();
		$this->view->header_active = 'stock_based/';
	}

	public function listing() {
		$this->view->title = 'Stock Based Sales';
		$data['ui'] = $this->ui;
        $data['item_list']          = $this->stock_based->getInvoiceData('items');
		$data['customer_list'] 		= $this->stock_based->getInvoiceData('customers');
		$data['warehouse_list'] 	= $this->stock_based->getInvoiceData('warehouse');
		$data['datefilter'] 		= $this->date->datefilterMonth();

		$this->view->load('stock_based', $data);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data 		= $this->input->post(array('itemcode','warehouse','customer','daterangefilter'));
		$itemcode 	= $data['itemcode'];
		$warehouse 	= $data['warehouse'];
		$customer 	= $data['customer'];
		$datefilter	= $data['daterangefilter'];
		$datefilter = explode('-', $datefilter);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}

		$list = $this->stock_based->retrieveSalesReport($itemcode, $warehouse, $customer, $dates[0], $dates[1]);
		$table = '';

		if (empty($list->result)) {
			$table = '<tr><td colspan="6" class="text-center"><b>No Records Found</b></td></tr>';
		}
		else
		{
			$grandqty 			= 0;
			$grandtotal 		= 0;
			$grandprice 		= 0;
			$grandamt   		= 0; 
			$base__uom 			= '';
			$sales__uom 		= '';

			foreach ($list->result as $key => $row) 
			{
				$date 			= $row->date;
				$invoice 		= $row->invoice;
				$customer 		= $row->customer;
				$sales_qty 		= $row->sales_qty;
				$sales_uom 		= $row->sales_uom;
				$base_qty 		= $row->base_qty;
				$base_uom 		= $row->base_uom;
				$unitprice 		= $row->unitprice;
				$tax 			= $row->tax;
				$discount 		= $row->discount;
				$totalamount 	= $row->amount + $row->tax + $row->discount;
				
				$table .= '<tr>';
				$table .= '<td>' . $this->date->dateFormat($date) . '</td>';
				$table .= '<td>' . $invoice . '</td>';
				$table .= '<td>' . $customer . '</td>';
				$table .= '<td class = "text-right">' . number_format($sales_qty,2) .' '. $sales_uom .'</td>';
				$table .= '<td class = "text-right">' . number_format($base_qty,2) .' '. $base_uom .'</td>';
				$table .= '<td class = "text-right">' . number_format($unitprice,2) . '</td>';
				$table .= '<td class = "text-right">' . number_format($totalamount,2) . '</td>';
				$table .= '</tr>';

				$grandqty	 	+= $sales_qty;
				$grandtotal  	+= $base_qty;
				$grandprice	 	+= $unitprice;
				$grandamt   	+= $totalamount;
				$sales__uom 	 = $sales_uom;
				$base__uom 		 = $base_uom;
			}

			$table .='<tr>';
			$table .='<td align="right" colspan="3"><b>Total</b></td>';
			$table .='<td align="right">'.number_format($grandqty,2).' '. $sales__uom .'</td>';
			$table .='<td align="right">'.number_format($grandtotal,2). ' '. $base__uom . '</td>';
			$table .='<td align="right">'.number_format($grandprice,2). '</td>';
			$table .='<td align="right">'.number_format($grandamt,2).'</td>';
			$table .='</tr>'; 
		}

		$list->table = $table;
		$list->csv   = $this->export();
		return $list;
	}

	private function export(){
		$data = $this->input->post(array('itemcode','warehouse','customer','daterangefilter'));
		$data['daterangefilter'] = str_replace(array('%2C', '+'), array(',', ' '), $data['daterangefilter']);
		$result = $this->stock_based->fileExport($data);
		$header = array("Date","Invoice","Customer","Qty","Total","Unit Price","Amount");

		$csv = 'Stock Based Sales';
		$csv .= "\n\n";
		$csv .= '"Date:","'.$data['daterangefilter'].'"';
		$csv .= "\n\n";
		$csv .= '"' . implode('","', $header) . '"';
		$csv .= "\n";
		
		$grandqty 			= 0;
		$grandtotal 		= 0;
		$grandprice 		= 0;
		$grandamt   		= 0; 
		$base__uom 			= '';
		$sales__uom 		= '';

		if (!empty($result)){
			foreach ($result as $key => $row){
				$date 			= $row->date;
				$invoice 		= $row->invoice;
				$customer 		= $row->customer;
				$sales_qty 		= $row->sales_qty;
				$sales_uom 		= $row->sales_uom;
				$base_qty 		= $row->base_qty;
				$base_uom 		= $row->base_uom;
				$unitprice 		= $row->unitprice;
				$tax 			= $row->tax;
				$discount 		= $row->discount;
				$totalamount 	= $row->amount + $row->tax + $row->discount;

				$csv .= '"' . $this->date->dateFormat($date) . '",';
				$csv .= '"' . $invoice . '",';
				$csv .= '"' . $customer . '",';
				$csv .= '"' . $this->amount($sales_qty) . ' '.$sales_uom.'",';
				$csv .= '"' . $this->amount($base_qty) . ' '.$base_uom.'",';
				$csv .= '"' . $this->amount($unitprice) . '",';
				$csv .= '"' . $this->amount($totalamount) . '"';
				$csv .= "\n";

				$grandqty	 	+= $sales_qty;
				$grandtotal  	+= $base_qty;
				$grandprice	 	+= $unitprice;
				$grandamt   	+= $totalamount;
				$sales__uom 	 = $sales_uom;
				$base__uom 		 = $base_uom;
			}
		}
		$csv .= ',,"Total:","' . $this->amount($grandqty). ' '.$sales__uom. '","' . $this->amount($grandtotal) . ' '.$base__uom. '","' . $this->amount($grandprice) . '","' . $this->amount($grandamt) . '"';
		return $csv;
	}

	private function amount($amount)
	{
		return number_format($amount,2);
	}

}