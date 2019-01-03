<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->logs				= new log();
		$this->billing_model	= new billing_model();
		$this->financial_model	= $this->checkOutModel('financials_module/financial_model');
		$this->session			= new session();
		$this->fields			= array(
			'voucherno',
			'transactiondate',
			'job_orderno',
			'customer',
			'remarks',
			'amount',
			'discounttype',
			'total_discount'	=> 'discountamount',
			'netamount',
			'taxamount',
			'vat_sales',
			'vat_exempt',
			'vat_zerorated',
			'exchangerate'
		);
		$this->fields2			= array(
			'itemcode',
			'detailparticular',
			'linenum',
			'issueqty',
			'issueuom',
			'unitprice',
			'taxcode',
			'taxrate',
			'detail_taxamount'		=> 'taxamount',
			'detail_amount'			=> 'amount',
			'discount_amount'		=> 'discountamount',		
			'discountedamount',
			'discount'				=> 'discountrate'
		);
		$this->clean_number		= array(
			'issueqty'
		);
	}

	public function listing() {
		$this->view->title		= 'Billing List';
		$data['customer_list']	= $this->billing_model->getCustomerList();
		$data['ui']				= $this->ui;
		$this->view->load('billing/billing_list', $data);
	}

	public function create() {
		$this->view->title			= 'Create Billing';
		$this->fields[]				= 'stat';
		$data						= $this->input->post($this->fields);
		$data['ui']					= $this->ui;
		$data['transactiondate']	= $this->date->dateFormat();
		$data['customer_list']		= $this->billing_model->getCustomerList();
		$data['warehouse_list']		= $this->billing_model->getWarehouseList();
		$data["item_list"]			= $this->billing_model->getItemList();
		$data["itemdetail_list"]	= $this->billing_model->getItemDetailsList();
		$data["taxrate_list"]		= $this->billing_model->getTaxRateList();
		$data["taxrates"]			= $this->billing_model->getTaxRates();
		$disc_type_data        		= array("code ind","value val");
		$data["discounttypes"] 		= $this->billing_model->getValue("wc_option", $disc_type_data,"type = 'discount_type'");
		$data['voucher_details']	= json_encode(array(array('itemcode' => '')));
		$data['ajax_task']			= 'ajax_create';
		$data['ajax_post']			= '';
		$data['show_input']			= true;
		$this->view->load('billing/billing', $data);
	}

	public function edit($voucherno) {
		$this->view->title			= 'Edit Billing';
		$this->fields[]				= 'stat';
		$data						= (array) $this->billing_model->getBillingById($this->fields, $voucherno);
		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
		$data['ui']					= $this->ui;
		$data['customer_list']		= $this->billing_model->getCustomerList();
		$data['warehouse_list']		= $this->billing_model->getWarehouseList();
		$data["item_list"]			= $this->billing_model->getItemList();
		$data["itemdetail_list"]	= $this->billing_model->getItemDetailsList();
		$data['voucher_details']	= json_encode($this->billing_model->getBillingDetails($this->fields2, $voucherno, false));
		$data["taxrate_list"]		= $this->billing_model->getTaxRateList();
		$data["taxrates"]			= $this->billing_model->getTaxRates();
		$disc_type_data        		= array("code ind","value val");
		$data["discounttypes"] 		= $this->billing_model->getValue("wc_option", $disc_type_data,"type = 'discount_type'");
		$data['ajax_task']			= 'ajax_edit';
		$data['ajax_post']			= "&voucherno_ref=$voucherno";
		$data['show_input']			= true;
		$this->view->load('billing/billing', $data);
	}

	public function view($voucherno) {
		$this->view->title			= 'View Billing';
		$this->fields[]				= 'stat';
		$data						= (array) $this->billing_model->getBillingById($this->fields, $voucherno);
		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
		$data['ajax_task']			= '';
		$data['ui']					= $this->ui;
		$data['customer_list']		= $this->billing_model->getCustomerList();
		$data['warehouse_list']		= $this->billing_model->getWarehouseList();
		$data["item_list"]			= $this->billing_model->getItemList();
		$data['voucher_details']	= json_encode($this->billing_model->getBillingDetails($this->fields2, $voucherno));
		$data["taxrate_list"]		= $this->billing_model->getTaxRateList();
		$data["taxrates"]			= $this->billing_model->getTaxRates();
		$disc_type_data        		= array("code ind","value val");
		$data["discounttypes"] 		= $this->billing_model->getValue("wc_option", $disc_type_data,"type = 'discount_type'");
		$data['show_input']			= false;
		$this->view->load('billing/billing', $data);
	}

	public function print_preview($voucherno) {
		$documentinfo		= $this->billing_model->getDocumentInfo($voucherno);
		$vendordetails		= $this->billing_model->getCustomerDetails($documentinfo->partnercode);
		$documentdetails	= array(
			'Date'		=> $this->date->dateFormat($documentinfo->documentdate),
			'BILL #'	=> $voucherno,
			'TERMS'		=> $vendordetails->terms
		);

		$print = new billing_print_model();
		$print->setDocumentType('Billing')
				->setFooterDetails(array('Approved By', 'Checked By'))
				->setCustomerDetails($vendordetails)
				->setDocumentDetails($documentdetails)
				// ->addTermsAndCondition()
				->addReceived();

		$print->setHeaderWidth(array(30, 50, 30, 30, 30, 30))
				->setHeaderAlign(array('C', 'C', 'C', 'C', 'C', 'C', 'C'))
				->setHeader(array('Item Code', 'Description', 'Quantity', 'Price', 'Tax', 'Amount'))
				->setRowAlign(array('L', 'L', 'R', 'L', 'R', 'R', 'R'))
				->setSummaryWidth(array('170', '30'));
		
		$documentcontent	= $this->billing_model->getDocumentContent($voucherno);
		$detail_height = 37;

		$vatable_sales	= 0;
		$vat_exempt		= 0;
		$discount		= 0;
		$tax			= 0;
		$total_amount	= 0;
		foreach ($documentcontent as $key => $row) {
			if ($key % $detail_height == 0) {
				$print->drawHeader();
			}
			
			if ($documentinfo->discountrate == 0 && $documentinfo->discount) {
				$documentinfo->discountrate = $documentinfo->discount / ($documentinfo->amount + $documentinfo->vat) * 100;
			}

			$vatable_sales	+= ($row->taxrate) ? $row->Amount : 0;
			$vat_exempt		+= ($row->taxrate) ? 0 : $row->Amount;
			$discount		+= number_format(($row->Amount + $row->Tax) * $documentinfo->discountrate / 100, 2, '.', '');
			$tax			+= $row->Tax;
			$row->Quantity	= number_format($row->Quantity);
			$row->Price		= $row->basecurrency . ' ' . number_format($row->Price, 2);
			$row->Tax		= $row->basecurrency . ' ' . number_format($row->Tax, 2);
			$row->Amount	= $row->basecurrency . ' ' . number_format($row->Amount, 2);
			$print->addRow($row);
			if (($key + 1) % $detail_height == 0) {
				$total_amount = $vatable_sales + $vat_exempt - $discount + $tax;
				$summary = array(
					'VATable Sales'		=> $row->basecurrency . ' ' . number_format($vatable_sales, 2),
					'VAT-Exempt Sales'	=> $row->basecurrency . ' ' . number_format($vat_exempt, 2),
					'Total Sales'		=> $row->basecurrency . ' ' . number_format($vatable_sales + $vat_exempt, 2),
					'Tax'				=> $row->basecurrency . ' ' . number_format($tax, 2),
					'Discount'			=> $row->basecurrency . ' ' . number_format($discount, 2),
					'Total Amount'		=> $row->basecurrency . ' ' . number_format($total_amount, 2)
				);
				$print->drawSummary($summary);
				$vatable_sales	= 0;
				$vat_exempt		= 0;
				$discount		= 0;
				$tax			= 0;
				$total_amount	= 0;
			}
		}
		$total_amount = $vatable_sales + $vat_exempt - $discount + $tax;
		$summary = array(
			'VATable Sales'		=> $row->basecurrency . ' ' . number_format($vatable_sales, 2),
			'VAT-Exempt Sales'	=> $row->basecurrency . ' ' . number_format($vat_exempt, 2),
			'Total Sales'		=> $row->basecurrency . ' ' . number_format($vatable_sales + $vat_exempt, 2),
			'Tax'				=> $row->basecurrency . ' ' . number_format($tax, 2),
			'Discount'			=> $row->basecurrency . ' ' . number_format($discount, 2),
			'Total Amount'		=> $row->basecurrency . ' ' . number_format($total_amount, 2)
		);
		$print->drawSummary($summary);

		$print->drawPDF('Billing - ' . $voucherno);
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

		$pagination	= $this->billing_model->getBillingPagination($search, $sort, $customer, $filter, $datefilter);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit($row->stat == 'Unpaid')
									->addDelete($row->stat == 'Unpaid')
									->addPrint()
									->addCheckbox($row->stat == 'Unpaid')
									->setLabels(array('delete' => 'Cancel'))
									->setValue($row->voucherno)
									->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $row->customer . '</td>';
			$table .= '<td class="text-right">' . number_format($row->netamount, 2) . '</td>';
			$table .= '<td class="text-right">' . number_format($row->balance, 2) . '</td>';
			$table .= '<td>' . $this->colorStat($row->stat) . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function colorStat($stat) {
		$color = 'default';
		switch ($stat) {
			case 'Paid':
				$color = 'success';
				break;
			case 'With Partial Payment':
				$color = 'info';
				break;
			case 'Cancelled':
				$color = 'warning';
				break;
		}
		return '<span class="label label-' . $color . '">' . strtoupper($stat) . '</span>';
	}

	private function ajax_create() {
		$data						= $this->input->post($this->fields);
		$submit						= $this->input->post('submit');
		$data2						= $this->getItemDetails();
		$data2						= $this->cleanData($data2);
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		$data['period']				= $this->date->getMonthNumber($data['transactiondate']);
		$data['fiscalyear']			= $this->date->getYear($data['transactiondate']);
		$seq						= new seqcontrol();
		$data['voucherno']			= $seq->getValue('BILL');
		$result						= $this->billing_model->saveBilling($data, $data2);
		if ($result && $this->financial_model) {
			$this->financial_model->generateBillAR($data['voucherno']);
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
		$data						= $this->input->post($this->fields);
		unset($data['voucherno']);
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		$data['period']				= $this->date->getMonthNumber($data['transactiondate']);
		$data['fiscalyear']			= $this->date->getYear($data['transactiondate']);
		$voucherno					= $this->input->post('voucherno_ref');
		$data2						= $this->getItemDetails();
		$data2						= $this->cleanData($data2);
		$result						= $this->billing_model->updateBilling($data, $data2, $voucherno);
		if ($result && $this->financial_model) {
			$this->financial_model->generateBillAR($voucherno);
		}
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		if ($delete_id) {
			$result = $this->billing_model->deleteBilling($delete_id);
		}
		if ($result && $this->financial_model) {
			foreach ($delete_id as $voucherno) {
				$this->financial_model->cancelBillAR($voucherno);
			}
		}
		return array(
			'success' => $result
		);
	}

	private function ajax_load_jo_list() {
		$customer	= $this->input->post('customer');
		$search		= $this->input->post('search');
		$pagination	= $this->billing_model->getJOList($customer, $search);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr data-id="' . $row->job_order_no . '">';
			$table .= '<td>' . $row->job_order_no . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td class="text-right">' . $row->service_quotation . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_load_ordered_details() {
		$voucherno	= $this->input->post('voucherno');
		$details	= $this->billing_model->getJobOrderDetails($voucherno);
		$table		= '';
		$success	= true;
		if (empty($details)) {
			$table		= '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
			$success	= false;
		}
		return array(
			'table'		=> $table,
			'details'	=> $details,
			'success'	=> $success
		);
	}

	private function ajax_update_exchangerate() {
		$currency_code	= $this->input->post('currency_code');
		$data			= $this->input->post(array('effectivedate', 'exchangerate'));
		$result			= $this->billing_model->updateExchangeRate($data, $currency_code);

		return array(
			'success' => $result,
			'currency_list' => $currency_list
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