<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->logs				= new log();
		$this->job_order        = new job_order_model();
		$this->parts_and_service= new parts_and_service_model();
		
		$this->session			= new session();
		$this->fields 			= array(
			'voucherno',
			'transactiondate',
            'customer',
            'source_no',
			'reference',
			'customerpo',
			'notes'
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
		$this->fields2			= array(
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
		$this->view->title		= 'Job Order';
		$data['customer_list']	= $this->job_order->getCustomerList();
		$data['ui']				= $this->ui;
		$this->view->load('job_order/job_order_list', $data);
	}
	public function create() {
		$this->view->title			= 'Add Job Order';
		$this->fields[]				= 'stat';
		$data						= $this->input->post($this->fields);
		$data['ui']					= $this->ui;
		$data['transactiondate']	= $this->date->dateFormat();
		$data['targetdate']			= $this->date->dateFormat();
		$data['job_list']			= $this->job_order->getOption('job_type','code');
		$data['customer_list']		= $this->job_order->getCustomerList();
		$data['discount_type_list']	= $this->job_order->getOption('discount_type','value');
		$data['item_list']			= $this->job_order->getItemList();
		$data['warehouse_list']		= $this->job_order->getWarehouseList();
		$data["taxrate_list"]		= $this->job_order->getTaxRateList();
		$data["taxrates"]			= $this->job_order->getTaxRates();
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
		$this->view->load('job_order/job_order', $data);
	}
	public function edit($id) {
		$this->view->title			= 'Edit Job Order';
		$this->fields[]				= 'stat';
		$data						= $this->input->post($this->fields);
		$data['ui']					= $this->ui;
		$data['transactiondate']	= $this->date->dateFormat();
		$data['targetdate']			= $this->date->dateFormat();
		$data['job_list']			= $this->job_order->getOption('job_type','code');
		$data['customer_list']		= $this->job_order->getCustomerList();
		$data['discount_type_list']	= $this->job_order->getOption('discount_type','value');
		$data['item_list']			= $this->job_order->getItemList();
		$data['warehouse_list']		= $this->job_order->getWarehouseList();
		$data["taxrate_list"]		= $this->job_order->getTaxRateList();
		$data["taxrates"]			= $this->job_order->getTaxRates();
		$data['header_values']		= json_encode(array(
			''
		));
		$data['voucher_details']	= json_encode(
			array(
				'0'	=> array(
					'itemcode' => 'SERVICE001',
					'detailparticular' => 'L3608',
					'warehouse' => 'WH00003',
					'quantity' => 2,
					'uom' => 'PCS'
				),
				'1'	=> array(
					'itemcode' => '1-494443',
					'detailparticular' => 'Water Filter',
					'warehouse' => 'WH00003',
					'quantity' => 3,
					'uom' => 'PCS'
				)
			)
		);
		$data['reference']	= "0001";
		$data['customerpo']	= "000008001";
		$data['customer']	= "CUS_000008";

		$data['ajax_task']			= 'ajax_edit';
		$data['ajax_post']			= '';
		$data['show_input']			= true;
		// Closed Date
		$close_date 				= $this->parts_and_service->getClosedDate();
		$data['close_date']			= $close_date;
		$data['restrict_dr'] 		= false;

		$data['voucherno']			= "JO0000000001";
		$data['source_no']			= "SQ0000000001";
		$this->view->load('job_order/job_order', $data);
	}
	public function view($id) {
		$this->view->title			= 'View Job Order';
		$this->fields[]				= 'stat';
		$data						= $this->input->post($this->fields);
		$data['ui']					= $this->ui;
		$data['transactiondate']	= $this->date->dateFormat();
		$data['targetdate']			= $this->date->dateFormat();
		$data['job_list']			= $this->job_order->getOption('job_type','code');
		$data['customer_list']		= $this->job_order->getCustomerList();
		$data['discount_type_list']	= $this->job_order->getOption('discount_type','value');
		$data['item_list']			= $this->job_order->getItemList();
		$data['warehouse_list']		= $this->job_order->getWarehouseList();
		$data["taxrate_list"]		= $this->job_order->getTaxRateList();
		$data["taxrates"]			= $this->job_order->getTaxRates();
		$data['header_values']		= json_encode(array(
			''
		));
		$data['voucher_details']	= json_encode(
			array(
				'0'	=> array(
					'itemcode' => 'SERVICE001 - L3608',
					'detailparticular' => 'L3608',
					'warehouse' => 'WH00003',
					'quantity' => 2,
					'uom' => 'PCS'
				),
				'1'	=> array(
					'itemcode' => '1-494443 - Water Filter',
					'detailparticular' => 'Water Filter',
					'warehouse' => 'WH00003',
					'quantity' => 3,
					'uom' => 'PCS'
				)
			)
		);
		$data['reference']	= "0001";
		$data['customerpo']	= "000008001";
		$data['customer']	= "CUS_000008";

		$data['t_vatable_sales']	= 180;
		$data['t_vat_exempt_sales']	= 0;
		$data['t_vatsales']			= 180;
		$data['t_vat']				= 21.60;
		$data['t_amount']			= 201.60;
		$data['t_discount']			= 20.00;

		$data['ajax_task']			= 'ajax_view';
		$data['ajax_post']			= '';
		$data['show_input']			= false;
		// Closed Date
		$close_date 				= $this->parts_and_service->getClosedDate();
		$data['close_date']			= $close_date;
		$data['restrict_dr'] 		= false;

		$data['voucherno']			= "JO0000000001";
		$data['source_no']			= "SQ0000000001";
		$this->view->load('job_order/job_order', $data);
	}
	public function payment($id) {
		$this->view->title			= 'Job Order - Issue Parts';
		$this->fields[]				= 'stat';
		$data						= $this->input->post($this->fields);
		$data['ui']					= $this->ui;
		$data['transactiondate']	= $this->date->dateFormat();
		$data['targetdate']			= $this->date->dateFormat();
		$data['job_list']			= $this->job_order->getOption('job_type','code');
		$data['customer_list']		= $this->job_order->getCustomerList();
		$data['discount_type_list']	= $this->job_order->getOption('discount_type','value');
		$data['item_list']			= $this->job_order->getItemList();
		$data['warehouse_list']		= $this->job_order->getWarehouseList();
		$data["taxrate_list"]		= $this->job_order->getTaxRateList();
		$data["taxrates"]			= $this->job_order->getTaxRates();
		$data['header_values']		= json_encode(array(
			''
		));
		$data['voucher_details']	= json_encode(
			array(
				'0'	=> array(
					'itemcode' => 'SERVICE001 - L3608',
					'detailparticular' => 'L3608',
					'warehouse' => 'APOLLO',
					'orderqty' => 2,
					'quantity' => 2,
					'uom' => 'PCS'
				),
				'1'	=> array(
					'itemcode' => '1-494443 - Water Filter',
					'detailparticular' => 'Water Filter',
					'warehouse' => 'APOLLO',
					'orderqty' => 3,
					'quantity' => 3,
					'uom' => 'PCS'
				)
			)
		);
		$data['reference']	= "0001";
		$data['customerpo']	= "000008001";
		$data['customer']	= "CUS_000008";

		$data['t_vatable_sales']	= 180;
		$data['t_vat_exempt_sales']	= 0;
		$data['t_vatsales']			= 180;
		$data['t_vat']				= 21.60;
		$data['t_amount']			= 201.60;
		$data['t_discount']			= 20.00;

		$data['ajax_task']			= 'ajax_view';
		$data['ajax_post']			= '';
		$data['show_input']			= false;
		// Closed Date
		$close_date 				= $this->parts_and_service->getClosedDate();
		$data['close_date']			= $close_date;
		$data['restrict_dr'] 		= false;

		$data['voucherno']			= "JO0000000001";
		$data['source_no']			= "SQ0000000001";
		$this->view->load('job_order/job_order_payment', $data);
	}
	private function ajax_list() {
		$data		= $this->input->post(array('search', 'sort', 'customer', 'filter', 'daterangefilter'));
		$sort		= $data['sort'];
		$search		= $data['search'];
		$customer	= $data['customer'];
		$filter		= $data['filter'];
		$datefilter	= $data['daterangefilter'];

		//$pagination	= $this->job_order->getServiceQuotationPagination($search, $sort, $customer, $filter, $datefilter);
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
		$len = 1;
		for($x=1;$x<=$len;$x++){
			$transactiondate = date("M d, Y");
			$job_types 		= array('Inspection','Repair','Preventive Maintenance','Refurbish');
			$job_type 		= $job_types[(array_rand($job_types))];
			$statuses 		= array('Pending','Partial','With JO','Cancelled');
			// $status 		= ($filter == 'all') ? $statuses[(array_rand($statuses))] : $filter;
			$status 		= "Prepared";
			$dropdown = $this->ui->loadElement('check_task')
						->addView()
						->addEdit()
						->addDelete()
						// ->addPrint()
						->addOtherTask('Issue Parts', 'bookmark')
						->addOtherTask('Tag as Complete', 'bookmark')
						->addCheckbox()
						->setLabels(array('delete' => 'Cancel'))
						->setValue($x)
						->draw();
			// $table .= '<tr>';
			// $table .= '<td align = "center">' . $dropdown . '</td>';
			// $table .= '<td>' . $this->date->dateFormat($transactiondate) . '</td>';
			// $table .= '<td>SQ00000'.$x.'</td>';
			// $table .= '<td>Company C</td>';
			// $table .= '<td>'.$job_type.'</td>';
			// $table .= '<td>'.$this->generateRandomString().'</td>';
			// $table .= '<td>'.$this->colorStat($status).'</td>';

			$table .= '<tr>';
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>Dec 03, 2018</td>';
			$table .= '<td>JO0000000001</td>';
			$table .= '<td>Company C</td>';
			$table .= '<td>SQ0000000001</td>';
			$table .= '<td>001020123</td>';
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