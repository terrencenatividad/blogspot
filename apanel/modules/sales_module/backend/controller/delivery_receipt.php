<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->logs				= new log();
		$this->delivery_model	= new delivery_receipt_model();
		$this->inventory_model	= $this->checkoutModel('inventory_module/inventory_model');
		$this->session			= new session();
		$this->fields 			= array(
			'voucherno',
			'transactiondate',
			'customer',
			'deliverydate',
			'packing_no',
			'remarks',
			'warehouse'
		);
		$this->fields_header	= array(
			'header_warehouse'		=> 'warehouse',
			'header_term'			=> 'term',
			'header_amount'			=> 'amount',
			'header_discounttype'	=> 'discounttype',
			'header_discountamount'	=> 'discountamount',
			'header_netamount'		=> 'netamount',
			'header_taxcode'		=> 'taxcode',
			'header_taxamount'		=> 'taxamount',
			'header_wtaxcode'		=> 'wtaxcode',
			'header_wtaxamount'		=> 'wtaxamount',
			'header_wtaxrate'		=> 'wtaxrate',
			'header_vat_sales'		=> 'vat_sales',
			'header_vat_exempt'		=> 'vat_exempt',
			'header_vat_zerorated'	=> 'vat_zerorated'
		);
		$this->fields2			= array(
			'itemcode',
			'detailparticular',
			'issueqty',
			'issueuom',
			'convuom',
			'convissueqty',
			'conversion',
			'unitprice',
			'taxcode',
			'taxrate',
			'taxamount',
			'amount',
			'discounttype',
			'discountamount',
			'detail_warehouse' => 'warehouse'
		);
	}

	public function listing() {
		$this->view->title		= 'Delivery Receipt List';
		$data['customer_list']	= $this->delivery_model->getCustomerList();
		$data['ui']				= $this->ui;
		$this->view->load('delivery_receipt/delivery_receipt_list', $data);
	}

	public function create() {
		$this->view->title			= 'Create Delivery Receipt';
		$this->fields[]				= 'stat';
		$data						= $this->input->post($this->fields);
		$data['ui']					= $this->ui;
		$data['deliverydate']		= $this->date->dateFormat();
		$data['transactiondate']	= $this->date->dateFormat();
		$data['customer_list']		= $this->delivery_model->getCustomerList();
		$data['warehouse_list']		= $this->delivery_model->getWarehouseList();
		$data["item_list"]			= $this->delivery_model->getItemList();
		$data['header_values']		= json_encode(array());
		$data['voucher_details']	= json_encode(array());
		$data['ajax_task']			= 'ajax_create';
		$data['ajax_post']			= '';
		$data['show_input']			= true;
		$this->view->load('delivery_receipt/delivery_receipt', $data);
	}

	public function edit($voucherno) {
		$this->view->title			= 'Edit Delivery Receipt';
		$this->fields[]				= 'stat';
		$data						= (array) $this->delivery_model->getDeliveryReceiptById($this->fields, $voucherno);
		$data['deliverydate']		= $this->date->dateFormat($data['deliverydate']);
		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
		$data['ui']					= $this->ui;
		$data['customer_list']		= $this->delivery_model->getCustomerList();
		$data['warehouse_list']		= $this->delivery_model->getWarehouseList();
		$data["item_list"]			= $this->delivery_model->getItemList();
		$data['header_values']		= json_encode($this->delivery_model->getDeliveryReceiptById($this->fields_header, $voucherno));
		$data['voucher_details']	= json_encode($this->delivery_model->getDeliveryReceiptDetails($this->fields2, $voucherno));
		$data['ajax_task']			= 'ajax_edit';
		$data['ajax_post']			= "&voucherno_ref=$voucherno";
		$data['show_input']			= true;
		$this->view->load('delivery_receipt/delivery_receipt', $data);
	}

	public function view($voucherno) {
		$this->view->title			= 'View Delivery Receipt';
		$this->fields[]				= 'stat';
		$data						= (array) $this->delivery_model->getDeliveryReceiptById($this->fields, $voucherno);
		$data['deliverydate']		= $this->date->dateFormat($data['deliverydate']);
		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
		$data['ajax_task']			= '';
		$data['ui']					= $this->ui;
		$data['customer_list']		= $this->delivery_model->getCustomerList();
		$data['warehouse_list']		= $this->delivery_model->getWarehouseList();
		$data["item_list"]			= $this->delivery_model->getItemList();
		$data['header_values']		= json_encode($this->delivery_model->getDeliveryReceiptById($this->fields_header, $voucherno));
		$data['voucher_details']	= json_encode($this->delivery_model->getDeliveryReceiptDetails($this->fields2, $voucherno));
		$data['show_input']			= false;
		$this->view->load('delivery_receipt/delivery_receipt', $data);
	}

	public function print_preview($voucherno) {
		$documentinfo		= $this->delivery_model->getDocumentInfo($voucherno);
		$customerdetails	= $this->delivery_model->getCustomerDetails($documentinfo->partnercode);
		$documentdetails	= array(
			'Date'	=> $this->date->dateFormat($documentinfo->documentdate),
			'DR #'	=> $voucherno,
			'PL #'	=> $documentinfo->packing_no,
			'SO #'	=> $documentinfo->referenceno,
			'TERMS'	=> $customerdetails->terms
		);

		$print = new sales_print_model();
		$print->setDocumentType('Delivery Receipt')
				->setFooterDetails(array('Approved By', 'Checked By'))
				->setCustomerDetails($customerdetails)
				->setDocumentDetails($documentdetails)
				// ->addTermsAndCondition()
				->addReceived();

		$print->setHeaderWidth(array(40, 100, 30, 30))
				->setHeaderAlign(array('C', 'C', 'C', 'C'))
				->setHeader(array('Item Code', 'Description', 'Quantity', 'UOM'))
				->setRowAlign(array('L', 'L', 'R', 'L'))
				->setSummaryWidth(array('170', '30'));
		
		$documentcontent	= $this->delivery_model->getDocumentContent($voucherno);
		$detail_height = 37;

		$total_quantity = 0;
		foreach ($documentcontent as $key => $row) {
			if ($key % $detail_height == 0) {
				$print->drawHeader();
			}

			$total_quantity += $row->Quantity;
			$row->Quantity	= number_format($row->Quantity, 2);
			$print->addRow($row);
			if (($key + 1) % $detail_height == 0) {
				$print->drawSummary(array('Total Quantity' => $total_quantity));
				$total_quantity = 0;
			}
		}
		$print->drawSummary(array('Total Quantity' => $total_quantity));

		$print->drawPDF('Delivery Receipt - ' . $voucherno);
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
		$customer	= $data['customer'];
		$filter		= $data['filter'];
		$datefilter	= $data['daterangefilter'];

		$pagination	= $this->delivery_model->getDeliveryReceiptPagination($search, $sort, $customer, $filter, $datefilter);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit($row->stat == 'Delivered' || $row->stat == 'Prepared')
									->addDelete($row->stat == 'Delivered' || $row->stat == 'Prepared')
									->addPrint()
									->addOtherTask('Tag as Delivered', 'bookmark',($row->stat == 'Prepared'))
									->addCheckbox($row->stat == 'Delivered' || $row->stat == 'Prepared')
									->setLabels(array('delete' => 'Cancel'))
									->setValue($row->voucherno)
									->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $row->customer . '</td>';
			$table .= '<td>' . $row->packing_no . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->deliverydate) . '</td>';
			$table .= '<td>' . $this->colorStat($row->stat) . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function colorStat($stat) {
		$color = 'default';
		switch ($stat) {
			case 'Delivered':
				$color = 'success';
				break;
			case 'Cancelled':
				$color = 'warning';
				break;
			case 'With Invoice':
				$color = 'info';
				break;
		}
		return '<span class="label label-' . $color . '">' . strtoupper($stat) . '</span>';
	}

	private function ajax_create() {
		$data						= array_merge($this->input->post($this->fields), $this->input->post($this->fields_header));
		$submit						= $this->input->post('submit');
		$data2						= $this->input->post($this->fields2);
		$data['deliverydate']		= $this->date->dateDbFormat($data['deliverydate']);
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		$seq						= new seqcontrol();
		$data['voucherno']			= $seq->getValue('DR');
		$result						= $this->delivery_model->saveDeliveryReceipt($data, $data2);

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
		$data['deliverydate']		= $this->date->dateDbFormat($data['deliverydate']);
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		$voucherno					= $this->input->post('voucherno_ref');
		$data2						= $this->input->post($this->fields2);
		$result						= $this->delivery_model->updateDeliveryReceipt($data, $data2, $voucherno);

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
			$result = $this->delivery_model->deleteDeliveryReceipt($delete_id);
		}

		if ($result && $this->inventory_model) {
			$this->inventory_model->generateBalanceTable();
		}

		return array(
			'success' => $result
		);
	}

	private function ajax_update_tagdelivered() {
		$id = $this->input->post('id');
		if ($id) {
			$result = $this->delivery_model->tagAsDelivered($id);
			if ($result && $this->inventory_model) {
				$this->inventory_model->generateBalanceTable();
			}
		}
	}

	private function ajax_update_untagdelivered() {
		$id = $this->input->post('id');
		if ($id) {
			$result = $this->delivery_model->untagAsDelivered($id);
			if ($result && $this->inventory_model) {
				$this->inventory_model->generateBalanceTable();
			}
			
		}
	}

	private function ajax_load_packing_list() {
		$customer	= $this->input->post('customer');
		$search		= $this->input->post('search');
		$pagination	= $this->delivery_model->getPackingPagination($customer, $search);
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

	private function ajax_load_packing_details() {
		$voucherno	= $this->input->post('voucherno');
		$details	= $this->delivery_model->getPackingDetails($voucherno);
		$header		= $this->delivery_model->getPackingHeader($this->fields_header, $voucherno);
		$table		= '';
		$success	= true;
		if (empty($details)) {
			$table		= '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
			$success	= false;
		}
		return array(
			'table'		=> $table,
			'details'	=> $details,
			'header'	=> $header,
			'success'	=> $success
		);
	}

}