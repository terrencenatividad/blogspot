<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->logs				= new log();
		$this->sales_model		= new sales_return_model();
		// $this->financial_model  = $this->checkOutModel('financials_module/financial_model');
		$this->inventory_model	= $this->checkoutModel('inventory_module/inventory_model');
		$this->session			= new session();
		$this->fields 			= array(
			'voucherno',
			'transactiondate',
			'customer',
			'source_no',
			'remarks',
			'warehouse',
			'amount'
		);
		$this->fields_header	= array(
			'header_warehouse'		=> 'warehouse',
			'header_amount'			=> 'amount',
			'header_fiscalyear'		=> 'fiscalyear',
			'header_period'			=> 'period',
			'header_discounttype'	=> 'discounttype',
			'header_discountamount'	=> 'discountamount',
			'header_netamount'		=> 'netamount',
			'header_taxamount'		=> 'taxamount',
			'header_wtaxcode'		=> 'wtaxcode',
			'header_wtaxamount'		=> 'wtaxamount',
			'header_wtaxrate'		=> 'wtaxrate',
		);
		$this->fields2			= array(
			'itemcode',
			'detailparticular',
			'linenum',
			'issueqty',
			'issueuom',
			'convuom',
			'convissueqty',
			'conversion',
			'unitprice',
			'taxcode',
			'taxrate',
			'taxamount',
			'detail_amount' => 'amount',
			'convissueqty',
			'discounttype',
			'discountamount',
			'detail_warehouse' => 'warehouse',
		);
		$this->clean_number		= array(
			'issueqty'
		);
	}

	public function listing() {
		$this->view->title		= 'Sales Return List';
		$data['customer_list']	= $this->sales_model->getCustomerList();
		$data['ui']				= $this->ui;
		$this->view->load('sales_return/sales_return_list', $data);
	}

	public function create() {
		$this->view->title			= 'Create Sales Return';
		$this->fields[]				= 'stat';
		$data						= $this->input->post($this->fields);
		$data['ui']					= $this->ui;
		$data['transactiondate']	= $this->date->dateFormat();
		$data['customer_list']		= $this->sales_model->getCustomerList();
		$data['warehouse_list']		= $this->sales_model->getWarehouseList();
		$data["item_list"]			= $this->sales_model->getItemList();
		$data['header_values']		= json_encode(array());
		$data['voucher_details']	= json_encode(array());
		$data['ajax_task']			= 'ajax_create';
		$data['ajax_post']			= '';
		$data['show_input']			= true;
		$this->view->load('sales_return/sales_return', $data);
	}

	public function edit($voucherno) {
		$this->view->title			= 'Edit Sales Return';
		$this->fields[]				= 'stat';
		$data						= (array) $this->sales_model->getSalesReturnById($this->fields, $voucherno);
		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
		$data['ui']					= $this->ui;
		$data['customer_list']		= $this->sales_model->getCustomerList();
		$data['warehouse_list']		= $this->sales_model->getWarehouseList();
		$data["item_list"]			= $this->sales_model->getItemList();
		$data['header_values']		= json_encode($this->sales_model->getSalesReturnById($this->fields_header, $voucherno));
		$data['voucher_details']	= json_encode($this->sales_model->getSalesReturnDetails($this->fields2, $voucherno, false));
		$data['ajax_task']			= 'ajax_edit';
		$data['ajax_post']			= "&voucherno_ref=$voucherno";
		$data['show_input']			= true;
		$this->view->load('sales_return/sales_return', $data);
	}

	public function view($voucherno) {
		$this->view->title			= 'View Sales Return';
		$this->fields[]				= 'stat';
		$data						= (array) $this->sales_model->getSalesReturnById($this->fields, $voucherno);
		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
		$data['ajax_task']			= '';
		$data['ui']					= $this->ui;
		$data['customer_list']		= $this->sales_model->getCustomerList();
		$data['warehouse_list']		= $this->sales_model->getWarehouseList();
		$data["item_list"]			= $this->sales_model->getItemList();
		$data['header_values']		= json_encode($this->sales_model->getSalesReturnById($this->fields_header, $voucherno));
		$data['voucher_details']	= json_encode($this->sales_model->getSalesReturnDetails($this->fields2, $voucherno));
		$data['show_input']			= false;
		$this->view->load('sales_return/sales_return', $data);
	}

	public function print_preview($voucherno) {
		$documentinfo		= $this->sales_model->getDocumentInfo($voucherno);
		$customerdetails	= $this->sales_model->getCustomerDetails($documentinfo->partnercode);
		$documentdetails	= array(
			'Date'	=> $this->date->dateFormat($documentinfo->documentdate),
			'SR #'	=> $voucherno,
			'SI #'	=> $documentinfo->referenceno
		);

		$print = new sales_print_model();
		$print->setDocumentType('Sales Return')
				->setFooterDetails(array('Approved By', 'Checked By'))
				->setCustomerDetails($customerdetails)
				->setDocumentDetails($documentdetails)
				// ->addTermsAndCondition()
				->addReceived();

		$print->setHeaderWidth(array(40, 60, 20, 20, 30, 30))
				->setHeaderAlign(array('C', 'C', 'C', 'C', 'C', 'C'))
				->setHeader(array('Item Code', 'Description', 'Quantity', 'UOM', 'Price', 'Amount'))
				->setRowAlign(array('L', 'L', 'R', 'L', 'R', 'R'))
				->setSummaryWidth(array('170', '30'));
		
		$documentcontent	= $this->sales_model->getDocumentContent($voucherno);
		$detail_height = 37;

		$total_amount = 0;
		foreach ($documentcontent as $key => $row) {
			if ($key % $detail_height == 0) {
				$print->drawHeader();
			}

			$total_amount	+= $row->Amount;
			$row->Quantity	= number_format($row->Quantity);
			$row->Amount	= number_format($row->Amount, 2);
			$print->addRow($row);
			if (($key + 1) % $detail_height == 0) {
				$print->drawSummary(array('Total Amount' => number_format($total_amount, 2)));
				$total_amount = 0;
			}
		}
		$print->drawSummary(array('Total Amount' => number_format($total_amount, 2)));

		$print->drawPDF('Sales Return - ' . $voucherno);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data		= $this->input->post(array('search', 'sort', 'customer', 'filter', 'daterangefilter'));
		$sort		= $data['sort'];
		$search		= $data['search'];
		$customer		= $data['customer'];
		$filter		= $data['filter'];
		$datefilter	= $data['daterangefilter'];

		$pagination	= $this->sales_model->getSalesReturnPagination($search, $sort, $customer, $filter, $datefilter);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit($row->stat == 'Returned')
									->addDelete($row->stat == 'Returned')
									->addPrint($row->stat == 'Returned')
									->addCheckbox($row->stat == 'Returned')
									->setLabels(array('delete' => 'Cancel'))
									->setValue($row->voucherno)
									->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $row->customer . '</td>';
			$table .= '<td>' . $row->source_no . '</td>';
			$table .= '<td>' . $this->colorStat($row->stat) . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function colorStat($stat) {
		$color = 'default';
		switch ($stat) {
			case 'Returned':
				$color = 'success';
				break;
			case 'Cancelled':
				$color = 'warning';
				break;
		}
		return '<span class="label label-' . $color . '">' . strtoupper($stat) . '</span>';
	}

	private function ajax_create() {
		$data						= array_merge($this->input->post($this->fields), $this->input->post($this->fields_header));
		$submit						= $this->input->post('submit');
		$data2						= $this->getItemDetails();
		$data2						= $this->cleanData($data2);
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		$seq						= new seqcontrol();
		$data['voucherno']			= $seq->getValue('SR');
		$result						= $this->sales_model->saveSalesReturn($data, $data2);
		// if ($result && $this->financial_model) {
		// 	$this->financial_model->generateDM($data['voucherno']);
		// }
		if ($result && $this->inventory_model) {
			$this->inventory_model->setReference($data['voucherno'])
									->setDetails($data['customer'])
									->generateBalanceTable();
		}
		$redirect_url = MODULE_URL;
		if ($submit == 'save_new') {
			$redirect_url = MODULE_URL . 'create';
		} else if ($submit == 'save_preview') {
			$redirect_url = MODULE_URL . 'view/' . $data['voucherno'];
		}
		return array(
			'redirect'	=> $redirect_url,
			'success'	=> $result
		);
	}

	private function ajax_edit() {
		$data						= array_merge($this->input->post($this->fields), $this->input->post($this->fields_header));
		unset($data['voucherno']);
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		$voucherno					= $this->input->post('voucherno_ref');
		$data2						= $this->getItemDetails();
		$data2						= $this->cleanData($data2);
		$result						= $this->sales_model->updateSalesReturn($data, $data2, $voucherno);
		// if ($result && $this->financial_model) {
		// 	$this->financial_model->generateDM($voucherno);
		// }
		if ($result && $this->inventory_model) {
			$this->inventory_model->setReference($voucherno)
									->setDetails($data['customer'])
									->generateBalanceTable();
		}
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		if ($delete_id) {
			$result = $this->sales_model->deleteSalesReturn($delete_id);
		}
		// if ($result && $this->financial_model) {
		// 	foreach ($delete_id as $voucherno) {
		// 		$this->financial_model->cancelDM($voucherno);
		// 	}
		// }
		if ($result && $this->inventory_model) {
			$this->inventory_model->generateBalanceTable();
		}
		return array(
			'success' => $result
		);
	}

	private function ajax_load_invoice_list() {
		$customer	= $this->input->post('customer');
		$search		= $this->input->post('search');
		$pagination	= $this->sales_model->getSalesInvoicePagination($customer, $search);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr data-id="' . $row->voucherno . '">';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->remarks . '</td>';
			$table .= '<td class="text-right">' . number_format($row->netamount, 2) . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_load_invoice_details() {
		$voucherno	= $this->input->post('voucherno');
		$details	= $this->sales_model->getSalesInvoiceDetails($voucherno);
		$header		= $this->sales_model->getSalesInvoiceHeader($this->fields_header, $voucherno);
		$table		= '';
		$success	= true;
		if (empty($details)) {
			$table		= '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
			$success	= false;
		}
		$total_amount	= $header->amount;
		$total_discount	= 0;
		$discountrate	= 0;

		if ($header->discounttype == 'perc') {
			$total_discount	= $total_amount * $header->discountamount / 100;
			$discountrate	= $header->discountamount / 100;
		} else {
			$total_discount	= $header->discountamount;
			$discountrate	= $total_discount / $total_amount;
		}
		
		foreach ($details as $key => $row) {
			$taxamount = $row->unitprice - ($row->unitprice / (1 + $row->taxrate));
			$discount = ($row->unitprice - $taxamount) * $discountrate;
			$details[$key]->unitprice = $row->unitprice - $discount;
			$details[$key]->taxrate = 0;
			$details[$key]->taxamount = 0;
		}
		
		return array(
			'table'		=> $table,
			'details'	=> $details,
			'header'	=> $header,
			'success'	=> $success
		);
	}

	private function getItemDetails() {
		$data = array();
 		$temp = $this->input->post($this->fields2);
		foreach ($temp['issueqty'] as $key => $quantity) {
			if ($quantity < 1) {
				foreach ($this->fields2 as $field) {
					if (is_array($temp[$field])) {
						unset($temp[$field][$key]);
					}
				}
			}
		}
		foreach ($this->fields2 as $field) {
			if (is_array($temp[$field])) {
				$data[$field] = array_values($temp[$field]);
			} else {
				$data[$field] = $temp[$field];
			}
		}
		return $data;
	}

}