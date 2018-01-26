<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->logs				= new log();
		$this->packing_model	= new packing_list_model();
		$this->inventory_model	= $this->checkoutModel('inventory_module/inventory_model');
		$this->session			= new session();
		$this->fields 			= array(
			'voucherno',
			'transactiondate',
			'customer',
			'source_no',
			'remarks',
			'warehouse',
			'amount',
			'netamount'
		);
		$this->fields_header	= array(
			'header_fiscalyear'		=> 'fiscalyear',
			'header_period'			=> 'period',
		);
		$this->fields2			= array(
			'itemcode',
			'detailparticular',
			'linenum',
			'issueqty',
			'issueuom',
			'unitprice',
			'detail_amount'			=> 'amount',
			'convissueqty',
			'convuom',
			'conversion',
			'detail_warehouse'		=> 'warehouse'
		);
		$this->clean_number		= array(
			'issueqty'
		);
	}

	public function listing() {
		$this->view->title		= 'Packing List';
		$data['customer_list']	= $this->packing_model->getCustomerList();
		$data['ui']				= $this->ui;
		$this->view->load('packing_list/packinglist_list', $data);
	}

	public function create() {
		$this->view->title			= 'Create Packing List';
		$this->fields[]				= 'stat';
		$data						= $this->input->post($this->fields);
		$data['ui']					= $this->ui;
		$data['transactiondate']	= $this->date->dateFormat();
		$data['customer_list']		= $this->packing_model->getCustomerList();
		$data['warehouse_list']		= $this->packing_model->getWarehouseList();
		$data["item_list"]			= $this->packing_model->getItemList();
		$data["taxrate_list"]		= $this->packing_model->getTaxRateList();
		$data["wtaxcode_list"]		= $this->packing_model->getWTaxCodeList();
		$data["taxrates"]			= $this->packing_model->getTaxRates();
		$data['header_values']		= json_encode(array());
		$data['voucher_details']	= json_encode(array());
		$data['ajax_task']			= 'ajax_create';
		$data['ajax_post']			= '';
		$data['show_input']			= true;
		$this->view->load('packing_list/packinglist', $data);
	}

	public function edit($voucherno) {
		$this->view->title			= 'Edit Packing List';
		$this->fields[]				= 'stat';
		$data						= (array) $this->packing_model->getPackingListById($this->fields, $voucherno);
		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
		$data['ui']					= $this->ui;
		$data['customer_list']		= $this->packing_model->getCustomerList();
		$data['warehouse_list']		= $this->packing_model->getWarehouseList();
		$data["item_list"]			= $this->packing_model->getItemList();
		$data['header_values']		= json_encode($this->packing_model->getPackingListById($this->fields_header, $voucherno));
		$data['voucher_details']	= json_encode($this->packing_model->getPackingListDetails($this->fields2, $voucherno, false));
		$data["taxrate_list"]		= $this->packing_model->getTaxRateList();
		$data["wtaxcode_list"]		= $this->packing_model->getWTaxCodeList();
		$data["taxrates"]			= $this->packing_model->getTaxRates();
		$data['ajax_task']			= 'ajax_edit';
		$data['ajax_post']			= "&voucherno_ref=$voucherno";
		$data['show_input']			= true;
		$this->view->load('packing_list/packinglist', $data);
	}

	public function view($voucherno) {
		$this->view->title			= 'View Packing List';
		$this->fields[]				= 'stat';
		$data						= (array) $this->packing_model->getPackingListById($this->fields, $voucherno);
		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
		$data['ajax_task']			= '';
		$data['ui']					= $this->ui;
		$data['customer_list']		= $this->packing_model->getCustomerList();
		$data['warehouse_list']		= $this->packing_model->getWarehouseList();
		$data["item_list"]			= $this->packing_model->getItemList();
		$data['header_values']		= json_encode($this->packing_model->getPackingListById($this->fields_header, $voucherno));
		$data['voucher_details']	= json_encode($this->packing_model->getPackingListDetails($this->fields2, $voucherno));
		$data["taxrate_list"]		= $this->packing_model->getTaxRateList();
		$data["wtaxcode_list"]		= $this->packing_model->getWTaxCodeList();
		$data["taxrates"]			= $this->packing_model->getTaxRates();
		$data['show_input']			= false;
		$this->view->load('packing_list/packinglist', $data);
	}

	public function print_preview($voucherno) {
		$documentinfo		= $this->packing_model->getDocumentInfo($voucherno);
		$customerdetails	= $this->packing_model->getCustomerDetails($documentinfo->partnercode);
		$documentdetails	= array(
			'Date'	=> $this->date->dateFormat($documentinfo->documentdate),
			'PL #'	=> $voucherno,
			'SO #'	=> $documentinfo->referenceno
		);

		$print = new sales_print_model();
		$print->setDocumentType('Packing List')
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
		
		$documentcontent	= $this->packing_model->getDocumentContent($voucherno);
		$detail_height = 37;

		$total_quantity = 0;
		foreach ($documentcontent as $key => $row) {
			if ($key % $detail_height == 0) {
				$print->drawHeader();
			}

			$total_quantity	+= $row->Quantity;
			$row->Quantity	= number_format($row->Quantity, 2);
			$print->addRow($row);
			if (($key + 1) % $detail_height == 0) {
				$print->drawSummary(array('Total Quantity' => $total_quantity));
				$total_quantity = 0;
			}
		}
		$print->drawSummary(array('Total Quantity' => $total_quantity));

		$print->drawPDF('Packing List - ' . $voucherno);
	}

	public function print_view($voucherno) {
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

		$pagination	= $this->packing_model->getPackingListPagination($search, $sort, $customer, $filter, $datefilter);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit($row->stat == 'Packed')
									->addDelete($row->stat == 'Packed')
									->addPrint()
									->addCheckbox($row->stat == 'Packed')
									->setValue($row->voucherno)
									->setLabels(array('delete' => 'Cancel'))
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
			case 'Packed':
				$color = 'success';
				break;
			case 'Delivered':
				$color	= 'primary';
				$stat	= 'Closed';
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
		$data['voucherno']			= $seq->getValue('PL');
		$result						= $this->packing_model->savePackingList($data, $data2);
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
		$result						= $this->packing_model->updatePackingList($data, $data2, $voucherno);
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
			$result = $this->packing_model->deletePackingList($delete_id);
		}
		if ($result && $this->inventory_model) {
			foreach ($delete_id as $voucherno) {
				$this->inventory_model->generateBalanceTable();
			}
		}
		return array(
			'success' => $result
		);
	}

	private function ajax_load_ordered_list() {
		$customer		= $this->input->post('customer');
		$search			= $this->input->post('search');
		$pagination		= $this->packing_model->getSalesOrderPagination($customer, $search);
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

	private function ajax_load_ordered_details() {
		$voucherno	= $this->input->post('voucherno');
		$warehouse	= $this->input->post('warehouse');
		$details	= $this->packing_model->getSalesOrderDetails($voucherno, $warehouse);
		$header		= $this->packing_model->getSalesOrderHeader($this->fields_header, $voucherno);
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