<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->logs				= new log();
		$this->service_quotation= new service_quotation_model();
		$this->parts_and_service= new parts_and_service_model();
		
		$this->session			= new session();
		$this->fields 			= array(
			'voucherno',
			'reference',
			'customer',
			'notes',
			'job_type',
			'discount_type',
			'transactiondate',
			'targetdate'
		);
		$this->fields_header	= array(
			'header_fiscalyear'		=> 'fiscalyear',
			'header_period'			=> 'period',
			'header_taxcode' 		=> 'taxcode', 
			'header_taxamount' 		=> 'taxamount',
			'header_discounttype'   => 'discounttype',
			'header_discountrate'   => 'discountrate',
			'header_discountamount' => 'discountamount'
		);
		$this->fields_details			= array(
			'itemcode',
			'detailparticular',
			'linenum',
			'warranty',
			'warehouse',
			'quantity',
			'uom',
			'price',
			'discount',
			'amount',
			'taxcode',
			'taxamount',
			'taxrate'
		);
		$this->clean_number		= array(
			'issueqty'
		);
	}
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}
	public function listing() {
		$this->view->title		= 'Service Quotation';
		$data['customer_list']	= $this->service_quotation->getCustomerList();
		$data['ui']				= $this->ui;
		$this->view->load('service_quotation/service_quotation_list', $data);
	}
	public function create() {
		$this->view->title			= 'Add Service Quotation';
		$this->fields[]				= 'stat';
		$data						= $this->input->post($this->fields);
		$data['ui']					= $this->ui;
		$data['transactiondate']	= $this->date->dateFormat();
		$data['targetdate']			= $this->date->dateFormat();
		$data['job_list']			= $this->service_quotation->getOption('job_type','code');
		$data['customer_list']		= $this->service_quotation->getCustomerList();
		
		$data['discount_type_list']	= $this->service_quotation->getOption('discount_type','value');
		$data['discount_type'] 		= "none";
		$data['item_list']			= $this->service_quotation->getItemList();
		$data['warehouse_list']		= $this->service_quotation->getWarehouseList();
		$data["taxrate_list"]		= $this->service_quotation->getTaxRateList();
		$data["taxrates"]			= $this->service_quotation->getTaxRates();
		$data['header_values']		= json_encode(array());
		$data['voucher_details']	= json_encode(array());
		$data['t_vatable_sales']	= 0;
		$data['t_vat_exempt_sales']	= 0;
		$data['t_vatsales']			= 0;
		$data['t_vat']				= 0;
		$data['t_amount']			= 0;
		$data['t_discount']			= 0;
		$data['ajax_task']			= 'ajax_create';
		$data['ajax_post']			= '';
		$data['show_input']			= true;
		// Closed Date
		$close_date 				= $this->parts_and_service->getClosedDate();
		$data['close_date']			= $close_date;
		$data['restrict_dr'] 		= false;

		$this->view->load('service_quotation/service_quotation', $data);
	}
	public function edit($id) {
		$this->view->title			= 'Edit Service Quotation';
		$this->fields[]				= 'stat';
		$data						= $this->input->post($this->fields);
		$data['ui']					= $this->ui;
		$data['transactiondate']	= $this->date->dateFormat();
		$data['targetdate']			= $this->date->dateFormat();
		$data['job_list']			= $this->service_quotation->getOption('job_type','code');
		$data['customer_list']		= $this->service_quotation->getCustomerList();
		$data['discount_type_list']	= $this->service_quotation->getOption('discount_type','value');
		$data['item_list']			= $this->service_quotation->getItemList();
		$data['warehouse_list']		= $this->service_quotation->getWarehouseList();
		$data["taxrate_list"]		= $this->service_quotation->getTaxRateList();
		$data["taxrates"]			= $this->service_quotation->getTaxRates();
		$data['header_values']		= json_encode(array(
			''
		));
		$data['voucher_details']	= json_encode(
			array(
				'0'	=> array(
					'itemcode' => 'AAA0000001',
					'detailparticular' => 'This is a description',
					'warranty' => 'yes',
					'warehouse' => 'BAW',
					'quantity' => 1,
					'uom' => 'pcs',
					'price' => 0,
					'taxcode' => '',
					'amount' => '0.00'
				),
				'1'	=> array(
					'itemcode' => 'AAA0000002',
					'detailparticular' => '',
					'warranty' => 'no',
					'warehouse' => 'BAW',
					'quantity' => 2,
					'uom' => 'pcs',
					'price' => 100,
					'taxcode' => 'VATG',
					'amount' => '180.00'
				)
			)
		);
		$data['t_vatable_sales']	= 180;
		$data['t_vat_exempt_sales']	= 0;
		$data['t_vatsales']			= 180;
		$data['t_vat']				= 21.60;
		$data['t_amount']			= 201.60;
		$data['t_discount']			= 20.00;

		$data['ajax_task']			= 'ajax_edit';
		$data['ajax_post']			= '';
		$data['show_input']			= true;
		// Closed Date
		$close_date 				= $this->parts_and_service->getClosedDate();
		$data['close_date']			= $close_date;
		$data['restrict_dr'] 		= false;

		$data['voucherno']					= $id;
		$this->view->load('service_quotation/service_quotation', $data);
	}
	private function ajax_list() {
		$data		= $this->input->post(array('search', 'sort', 'customer', 'filter', 'daterangefilter'));
		$sort		= $data['sort'];
		$search		= $data['search'];
		$customer	= $data['customer'];
		$filter		= $data['filter'];
		$datefilter	= $data['daterangefilter'];

		//$pagination	= $this->service_quotation->getServiceQuotationPagination($search, $sort, $customer, $filter, $datefilter);
		$table		= '';
		$pagination = new stdClass;
		// if (empty($pagination->result)) {
		// 	$table = '<tr><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>';
		// }
		// foreach ($pagination->result as $key => $row) {
		// 	$transactiondate 	=	$row->transactiondate;
		// 	$table .= '<tr>';
		// 	$dropdown = $this->ui->loadElement('check_task')
		// 							->addView()
		// 							->addEdit($row->stat == 'Pending')
		// 							->addDelete($row->stat == 'Pending')
		// 							->addPrint()
		// 							->addOtherTask('Add Payment', 'bookmark')
		// 							->addCheckbox($row->stat == 'Pending')
		// 							->setLabels(array('delete' => 'Cancel'))
		// 							->setValue($row->voucherno)
		// 							->draw();
		// 	$table .= '<td align = "center">' . $dropdown . '</td>';
		// 	$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
		// 	$table .= '<td>' . $row->voucherno . '</td>';
		// 	$table .= '<td>' . $row->customer . '</td>';
		// 	$table .= '<td>' . $row->source_no . '</td>';
		// 	$table .= '<td>' . $this->date->dateFormat($row->deliverydate) . '</td>';
		// 	$table .= '<td>' . $this->colorStat($row->stat) . '</td>';
		// 	$table .= '</tr>';
		// }
		$len = 10;
		for($x=1;$x<=$len;$x++){
			$transactiondate = date("M d, Y");
			$job_types 		= array('Inspection','Repair','Preventive Maintenance','Refurbish');
			$job_type 		= $job_types[(array_rand($job_types))];
			$statuses 		= array('Pending','Partial','With JO','Cancelled');
			$status 		= ($filter == 'all') ? $statuses[(array_rand($statuses))] : $filter;
			$dropdown = $this->ui->loadElement('check_task')
						->addView()
						->addEdit()
						->addDelete()
						->addPrint()
						->addOtherTask('Add Payment', 'bookmark')
						->addCheckbox()
						->setLabels(array('delete' => 'Cancel'))
						->setValue($x)
						->draw();
			$table .= '<tr>';
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $this->date->dateFormat($transactiondate) . '</td>';
			$table .= '<td>SQ00000'.$x.'</td>';
			$table .= '<td>Company C</td>';
			$table .= '<td>'.$job_type.'</td>';
			$table .= '<td>'.$this->generateRandomString().'</td>';
			$table .= '<td>'.$this->colorStat($status).'</td>';
		}
		$pagination->table = $table;
		$pagination->pagination = '<div class="text-center">
									<ul class="pagination">
										<li class="disabled">
											<a href="#" data-page="1">
												<span aria-hidden="true">«</span>
											</a>
										</li>
										<li class="active"><a href="#" data-page="1">1</a></li><li><a href="#" data-page="2">2</a></li><li><a href="#" data-page="3">3</a></li><li><a href="#" data-page="4">4</a></li><li><a href="#" data-page="5">5</a></li><li><a href="#" data-page="6">6</a></li><li><a href="#" data-page="7">7</a></li><li><a href="#" data-page="8">8</a></li>
										<li><a href="#" data-page="9">9</a></li>
										<li>
											<a href="#" data-page="2">
												<span aria-hidden="true">»</span>
											</a>
										</li>
									</ul>
									</div>';
		return $pagination;
	}

	private function colorStat($stat) {
		$color = 'default';
		switch ($stat) {
			case 'Pending':
				$color = 'default';
				break;
			case 'Partial':
				$color = 'warning';
				break;
			case 'Cancelled':
				$color = 'danger';
				break;
			case 'With JO':
				$color = 'info';
				break;
		}
		return '<span class="label label-' . $color . '">' . strtoupper($stat) . '</span>';
	}

	private function ajax_create(){
		$seq 		= new seqcontrol();
		$voucherno 	= $seq->getValue("SEQ");
		$service 			= $this->input->post($this->fields);
		$service_details = $this->input->post($this->fields_details);
		var_dump($service);
		var_dump($service_details);
	}

	public function generateRandomString($length = 10){
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$charactersLength = strlen($characters);
		$randomString = '';
		for($i = 0;$i < $length; $i++){
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}