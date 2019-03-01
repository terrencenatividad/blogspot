<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->logs				= new log();
		$this->job_order        = new job_order_model();
		$this->parts_and_service= new parts_and_service_model();
		$this->inventory_model	= $this->checkoutModel('inventory_module/inventory_model');		
		$this->session			= new session();
		$this->seq 					= new seqcontrol();
		$this->fields 			= array(
			'job_order_no',
			'transactiondate',
            'customer',
			'reference',
            'service_quotation',
			'po_number',
			'notes',
			'stat'
		);
		$this->fields_header	= array(
			'header_fiscalyear'		=> 'fiscalyear',
			'header_period'			=> 'period',
			'header_discounttype'   => 'discounttype'
		);
		$this->fields2			= array(
			'job_order_no',
			'h_itemcode'		=> 'itemcode',
			'detailparticular',
			'linenum',
			'h_warehouse'		=> 'warehouse',
			'qty' 				=> 'quantity',
			'h_uom'				=> 'uom',
			'isbundle',
			'parentline',
			'parentcode'
		);
		$this->fields3			= array(
			'job_order_no',
			'jod.itemcode',
			'i.itemname',
			'detailparticular',
			'jod.linenum',
			'jod.warehouse',
			'jod.qty' => 'jod.quantity',
			'jod.uom',
			'isbundle',
			'parentcode',
			'parentline',
			'item_ident_flag',
			'bom.quantity bomqty',
			'w.description'
		);
		$this->fields33			= array(
			'jr.job_order_no',
			'jod.itemcode',
			'i.itemname',
			'detailparticular',
			'jr.linenum',
			'jod.warehouse',
			'jod.qty' => 'jod.quantity',
			'jod.uom',
			'isbundle',
			'parentcode',
			'parentline',
			'item_ident_flag',
			'bom.quantity bomqty',
			'w.description',
			'SUM(jr.quantity) as issuedqty'
		);
		$this->fields4			= array(
			'job_release_no',
			'job_order_no',
			'h_transactiondate' =>'transactiondate',
			'issuancedate',
			'h_itemcode' 		=> 'itemcode',
			'linenum',
			'h_detailparticular'=> 'detailparticulars',
			'h_warehouse'		=> 'warehouse',
			'quantity',
			'h_uom'  => 'unit',
			'serialnumbers',
			'stat'
		);
		$this->fieldsattachment	= array(
			//'attachment_id',
			'attachment_url',
			'attachment_name',
			'attachment_type',
			//'reference',
			//'stat'
		);
		$this->clean_number		= array(
			'issueqty'
		);
		$this->fieldsMainListing = array(
			'job_order_no',
			'transactiondate',
			'customer',
			'reference',
			'service_quotation',
			'po_number',
			'notes',
			'j.stat',
			'partnername',
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
		$this->view->addCSS(array(
				'jquery.fileupload.css'
			)
		);  
		$this->view->addJS(
			array(
				'jquery.dirrty.js',
				'jquery.ui.widget.js',
				'jquery.iframe-transport.js',
				'jquery.fileupload.js',
				'jquery.fileupload-process.js',
				'jquery.fileupload-validate.js',
				'jquery.fileupload-ui.js'
			)
		);
		$this->view->load('job_order/job_order_list', $data);
	}
	public function create() {
		$this->view->title			= 'Add Job Order';
		$this->fields[]				= 'stat';
		$data						= $this->input->post($this->fields);
		$data['ui']					= $this->ui;
		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
		$data['targetdate']			= $this->date->dateFormat($data['transactiondate']);
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

		$data						= (array) $this->job_order->getJOByID($this->fields, $id);

		$data['ui']					= $this->ui;
		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
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

		$data['voucher_details']	= json_encode($this->job_order->getJobOrderDetails($this->fields2, $id));
		//var_dump($data['voucher_details']);

		$data['ajax_task']			= 'ajax_edit';
		$data['ajax_post']			= '';
		$data['show_input']			= true;
		// Closed Date
		$close_date 				= $this->parts_and_service->getClosedDate();
		$data['close_date']			= $close_date;
		$data['restrict_dr'] 		= true;
		
		$this->view->load('job_order/job_order', $data);
	}
	public function view($id) {
		$this->view->title			= 'View Job Order';
		$this->view->addCSS(array(
				'jquery.fileupload.css'
			)
		);  
		$this->view->addJS(
			array(
				'jquery.dirrty.js',
				'jquery.ui.widget.js',
				'jquery.iframe-transport.js',
				'jquery.fileupload.js',
				'jquery.fileupload-process.js',
				'jquery.fileupload-validate.js',
				'jquery.fileupload-ui.js'
			)
		);
		$this->fields[]				= 'stat';
		$data						= $this->input->post($this->fields);
		$data						= (array) $this->job_order->getJOByID($this->fields, $id);
		$data['ui']					= $this->ui;
		$data['transactiondate']	= $this->date->dateFormat($data['transactiondate']);
		$data['targetdate']			= $this->date->dateFormat();
		$data['job_list']			= $this->job_order->getOption('job_type','code');
		$data['customer_list']		= $this->job_order->getCustomerList();
		$data['discount_type_list']	= $this->job_order->getOption('discount_type','value');
		$data['item_list']			= $this->job_order->getItemList();
		$data['warehouse_list']		= $this->job_order->getWarehouseList();
		$data["taxrate_list"]		= $this->job_order->getTaxRateList();
		$data["taxrates"]			= $this->job_order->getTaxRates();

		$this->fields2['isbundle'] = " IF(isbundle = 'yes','1','0') isbundle";
		$voucherdetails 			= json_encode($this->job_order->getJobOrderDetails($this->fields2, $id));
		$data['voucher_details']	= $voucherdetails;
		// var_dump($voucherdetails); 	
		$data['header_values']		= json_encode(array(
			''
		));		
		$data['ajax_task']			= 'ajax_view';
		$data['ajax_post']			= '';
		$data['show_input']			= false;
		// Closed Date
		$close_date 				= $this->parts_and_service->getClosedDate();
		$data['close_date']			= $close_date;
		$data['restrict_dr'] 		= true;

		$data['job_order_no']			= $id;
		if ($data['stat'] == 'completed') {
			$getData 				= $this->job_order->selectAttachment($id,$this->fieldsattachment);
			$data['filename'] 		= $getData->attachment_name;
			$data['filetype'] 		= $getData->attachment_type;
			$data['fileurl'] 		= $getData->attachment_url;
		}
		$this->view->load('job_order/job_order', $data);
	}
	public function print_preview($voucherno){

		$header  = $this->job_order->getJOheader($voucherno);
		$details = $this->job_order->getJOcontent($voucherno);
		$jr_details = $this->job_order->getJRcontent($voucherno);
		$customer = $this->parts_and_service->getCustomerDetails($header->customer);
		/** VENDOR DETAILS --END**/

		$docheader	= array(
			'Date' 	=> $this->date->dateFormat($header->transactiondate),
			'JO #'				=> $header->voucherno,
			'REF #'				=> $header->reference
		);
		$print = new jo_print_model();
		$print->setDocumentType('Delivery Receipt')
				->setFooterDetails(array('Approved By', 'Checked By'))
				->setCustomerDetails($customer)
				->setRemarksDetail($header->notes)
				->setDocumentDetails($docheader)
				->setStatDetail($header->stat)
				->setDocumentInfo($header)
				// ->addTermsAndCondition()
				->addReceived();

		$print->setHeaderWidth(array(40, 100, 30, 30))
				->setHeaderAlign(array('C', 'C', 'C', 'C'))
				->setHeader(array('Item Code', 'Description', 'Qty', 'UOM'))
				->setRowAlign(array('L', 'L', 'R', 'L'))
				->setSummaryWidth(array('120', '50', '30'))
				->setSummaryAlign(array('J','R','R'));	

		$detail_height = 37;
		$total_quantity = 0;

		/**
		 * Custom : Tag as printed
		 * Also store user and timestamp
		 */
		$print_data['print'] = 1;
		$print_data['printby'] = USERNAME;
		$print_data['printdate'] = date("Y-m-d H:i:s");
		$this->job_order->updateData($print_data, "job_order", " job_order_no = '$voucherno' AND print = '0' ");
		
		//$notes = preg_replace('!\s+!', ' ', $header->notes);
		$notes = htmlentities($header->notes);
		foreach ($details as $key => $row) {
			if ($key % $detail_height == 0) {
				$print->drawHeader();
			}
			
			$total_quantity += $row->quantity;
			$print->addRow($row);

			if (($key + 1) % $detail_height == 0) {
				
				$print->drawSummary(array(array('Notes:', 'Total Qty', $total_quantity),
											array($notes, '', ''),
											array('', '', ''),
											array('', '', ''),
											array('', '', '')
				));
				$total_amount = 0;
			}
		}
		$print->drawSummary(array(array('Notes:', 'Total Qty', $total_quantity),
											array($notes, '', ''),
											array('', '', ''),
											array('', '', ''),
											array('', '', '')
		));
		$print->drawPDF('Job Order - ' . $voucherno);
	}
	public function payment($id) {
		$this->view->title			= 'Job Order - Issue Parts';
		$this->fields[]				= 'stat';
		$data						= $this->input->post($this->fields);
		$data						= (array) $this->job_order->getJOByID($this->fields, $id);
		$data['ui']					= $this->ui;
		$data['transactiondate']	= $this->date->dateFormat();
		$data['issuancedate']		= $this->date->dateFormat();
		$data['targetdate']			= $this->date->dateFormat();
		$data['job_list']			= $this->job_order->getOption('job_type','code');
		$data['customer_list']		= $this->job_order->getCustomerList();
		$data['discount_type_list']	= $this->job_order->getOption('discount_type','value');
		$data['item_list']			= $this->job_order->getItemList('goods');
		$data['warehouse_list']		= $this->job_order->getWarehouseList();
		$data["taxrate_list"]		= $this->job_order->getTaxRateList();
		$data["taxrates"]			= $this->job_order->getTaxRates();
		$data['header_values']		= json_encode(array(
			''
		));
		$data['voucher_details']	= json_encode($this->job_order->getJobOrder($this->fields3, $this->fields33, $id));

		$data['ajax_task']			= '';
		$data['ajax_post']			= '';
		$data['show_input']			= false;
		// Closed Date
		$close_date 				= $this->parts_and_service->getClosedDate();
		$data['close_date']			= $close_date;
		$data['restrict_dr'] 		= false;

		$this->view->load('job_order/job_order_payment', $data);
	}
	private function ajax_list() {
		$data		= $this->input->post(array('search', 'sort', 'customer', 'filter', 'daterangefilter', 'limit'));
		$sort		= $data['sort'];
		$search		= $data['search'];
		$customer	= $data['customer'];
		$filter		= $data['filter'];
		$datefilter	= $data['daterangefilter'];
		$limit 		= $data['limit'];

		$pagination	= $this->job_order->getJOPagination($search, $sort, $customer, $filter, $datefilter, $limit, $this->fieldsMainListing);
		$table		= '';

		if (empty($pagination->result)) {
			$table = '<tr><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $row) {
			$withparts = $this->job_order->checkForParts($row->job_order_no);
			$withparts = ($withparts=='released')? true : false;
			$showactions = ($row->stat=='completed' || $row->stat=='cancelled')? false : true;
			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit($row->stat == 'prepared')
									->addDelete($row->stat == 'prepared')
									->addPrint()
									->addOtherTask('Issue Parts', 'bookmark', $showactions)
									->addOtherTask('Tag as Complete', 'bookmark', $showactions)
									->addCheckbox($row->stat == 'prepared')
									->setLabels(array('delete' => 'Cancel'))
									->setValue($row->job_order_no)
									->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $row->job_order_no . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->partnername . '</td>';
			$table .= '<td>' . $row->service_quotation . '</td>';
			$table .= '<td>' . $row->reference . '</td>';
			$table .= '<td>' . $this->colorStat($row->stat) . '</td>';
			$table .= '</tr>';
		}
		// $len = 1;
		// for($x=1;$x<=$len;$x++){
		// 	$transactiondate = date("M d, Y");
		// 	$job_types 		= array('Inspection','Repair','Preventive Maintenance','Refurbish');
		// 	$job_type 		= $job_types[(array_rand($job_types))];
		// 	$statuses 		= array('Pending','Partial','With JO','Cancelled');
		// 	// $status 		= ($filter == 'all') ? $statuses[(array_rand($statuses))] : $filter;
		// 	$status 		= "Prepared";
		// 	$dropdown = $this->ui->loadElement('check_task')
		// 				->addView()
		// 				->addEdit()
		// 				->addDelete()
		// 				// ->addPrint()
		// 				->addOtherTask('Issue Parts', 'bookmark')
		// 				->addOtherTask('Tag as Complete', 'bookmark')
		// 				->addCheckbox()
		// 				->setLabels(array('delete' => 'Cancel'))
		// 				->setValue($x)
		// 				->draw();
		// 	// $table .= '<tr>';
		// 	// $table .= '<td align = "center">' . $dropdown . '</td>';
		// 	// $table .= '<td>' . $this->date->dateFormat($transactiondate) . '</td>';
		// 	// $table .= '<td>SQ00000'.$x.'</td>';
		// 	// $table .= '<td>Company C</td>';
		// 	// $table .= '<td>'.$job_type.'</td>';
		// 	// $table .= '<td>'.$this->generateRandomString().'</td>';
		// 	// $table .= '<td>'.$this->colorStat($status).'</td>';

		// 	$table .= '<tr>';
		// 	$table .= '<td align = "center">' . $dropdown . '</td>';
		// 	$table .= '<td>Dec 03, 2018</td>';
		// 	$table .= '<td>JO0000000001</td>';
		// 	$table .= '<td>Company C</td>';
		// 	$table .= '<td>SQ0000000001</td>';
		// 	$table .= '<td>001020123</td>';
		// 	$table .= '<td>'.$this->colorStat($status).'</td>';
		// }
		// $pagination->table = $table;
		// $pagination->pagination = '<div class="text-center">
		// 							<ul class="pagination">
		// 								<li class="disabled">
		// 									<a href="#" data-page="1">
		// 										<span aria-hidden="true">«</span>
		// 									</a>
		// 								</li>
		// 								<li class="active"><a href="#" data-page="1">1</a></li><li><a href="#" data-page="2">2</a></li><li><a href="#" data-page="3">3</a></li><li><a href="#" data-page="4">4</a></li><li><a href="#" data-page="5">5</a></li><li><a href="#" data-page="6">6</a></li><li><a href="#" data-page="7">7</a></li><li><a href="#" data-page="8">8</a></li>
		// 								<li><a href="#" data-page="9">9</a></li>
		// 								<li>
		// 									<a href="#" data-page="2">
		// 										<span aria-hidden="true">»</span>
		// 									</a>
		// 								</li>
		// 							</ul>
		// 							</div>';
		$pagination->table = $table;
		return $pagination;
	}

	private function colorStat($stat) {
		$color = 'default';
		switch ($stat) {
			case 'prepared':
				$color = 'default';
				break;
			case 'partial':
				$color = 'warning';
				break;
			case 'cancelled':
				$color = 'danger';
				break;
			case 'completed':
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

	private function ajax_serial_list() {
		$search	= $this->input->post('search');
		$itemcode = $this->input->post('itemcode');
		$allserials = $this->input->post('allserials');
		$checked_serials = $this->input->post('checked_serials');
		$itemselected = $this->input->post('itemselected');
		$linenum = $this->input->post('linenumber');
		$id = $this->input->post('id');
		$task = $this->input->post('task');
		$item_ident = $this->input->post('item_ident');
		$checked_serials = $this->input->post('checked_serials');
		$voucherno = '';
		if ($task=='ajax_edit') {
			$voucherno = $this->input->post('voucherno');
		}
		$curr = $this->job_order->getJOSerials($itemcode, $voucherno, $linenum);
		if ($curr) {
			$current_id = explode(",", $curr->serialnumbers);
			$curr_serialnumbers = $curr->serialnumbers;
		}
		else {
			$current_id = [];
			$curr_serialnumbers = '';
		}
		$array_id = explode(',', $id);
		$all_id = explode(',', $allserials);
		$checked_id = explode(',', $checked_serials);
		
		$pagination	= $this->job_order->getSerialList($itemcode, $search, $voucherno, $linenum,$id,$task);
		$table		= '';
		$counter = 0;
		foreach ($pagination->result as $key => $row) {
			if ($curr_serialnumbers == $id) {
				$checker = (in_array($row->id, $array_id) || in_array($row->id, $checked_id) || in_array($row->id, $current_id)) ? 'checked' : '';
			}
			else {
				$checker = (in_array($row->id, $array_id) || in_array($row->id, $checked_id)) ? 'checked' : '';
			}
			$hide_tr = ((in_array($row->id, $all_id) && !in_array($row->id, $array_id))) ? 'hidden' : '';
			$table .= '<tr class = "'.$hide_tr.'">';
			$table .= '<td class = "text-center"><input type = "checkbox" name = "check_id[]" id = "check_id" class = "check_id" value = "'.$row->id.'" '.$checker.'></td>';
			
			$has_serial 	=	substr($item_ident,0,1);
			$has_engine 	=	substr($item_ident,1,1);
			$has_chassis 	=	substr($item_ident,2,1);

			$hide_serial 	=	($has_serial == 0) 	? "hidden" 	:	"";
			$hide_engine 	=	($has_engine == 0) ? "hidden" 	:	"";
			$hide_chassis 	=	($has_chassis == 0)? "hidden" 	:	"";
			
			$table .= '<td class = "'.$hide_serial.'">' . $row->serialno . '</td>';
			$table .= '<td class = "'.$hide_engine.'">' . $row->engineno . '</td>';
			$table .= '<td class = "'.$hide_chassis.'">' . $row->chassisno . '</td>';
			$table .= '</tr>';
			$counter++;
		}
		if ($counter == 0) {
			$table.= '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}
	
	private function ajax_load_sq_list() {
        $customer   = $this->input->post('customer');
		$search   = $this->input->post('search');
		$pagination = $this->job_order->getSQPagination($customer,$search);
		$table      = '';

		if (empty($pagination->result)) {
			$table = '<tr><td colspan="5" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr data-id = "' . $row->voucherno . '">';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->notes . '</td>';
			$table .= '</tr>';
		}
		// $table .= '<script>checkExistingPR();</script>';
		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_load_sq_details() {
		$voucherno	= $this->input->post('voucherno');
		$warehouse	= $this->input->post('warehouse');
		$details	= $this->job_order->getServiceQuotationDetails($voucherno, $warehouse);
		$header		= $this->job_order->getServiceQuotationHeader($this->fields_header, $voucherno);
		//var_dump($details,$header);
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

	private function ajax_create() {
		$job_order_no 				= $this->seq->getValue("JO");
		$submit						= $this->input->post('submit');
		$data 						= $this->input->post($this->fields);
		$data['stat'] 				= 'prepared';
		$data['job_order_no'] 		= $job_order_no;
		$data['transactiondate'] 	= date('Y-m-d', strtotime($data['transactiondate']));
		$data2 						= $this->input->post($this->fields2);
		foreach($data2 as $key => $content){
			if(is_array($content)){
				foreach($content as $ind => $val) {
					if($key == 'isbundle') {
						if($val == 1 || $val == 'Yes'){
							$data2[$key][$ind] = 'yes';
						} else {
							$data2[$key][$ind]  = 'no';
						}
					}
				}
			}
		}
		
		$result		= $this->job_order->saveJobOrder($data, $data2);
		
		$this->inventory_model->setReference($data['job_order_no'])
									->setDetails($data['customer'])
									->generateBalanceTable();

		$redirect_url = MODULE_URL;
		if ($submit == 'save_new') {
			$redirect_url = MODULE_URL . 'create';
		} else if ($submit == 'save_preview') {
			$redirect_url = MODULE_URL . 'view/' . $data['job_order_no'];
		}
		return array(
			'redirect'	=> $redirect_url,
			'success'	=> $result
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		if ($delete_id) {
			$result		= $this->job_order->deleteJobOrder($delete_id);
			if(empty($result)) {
				$msg = "success";
			}else {
				$msg = $result;
			}
		}
		return array('success' => $msg);
	}

	private function ajax_load_bundle_details() {
		$itemcode	= $this->input->post('itemcode');
		
		$header		= $this->job_order->retrieveItemDetails($itemcode);
		$details 	= $this->job_order->retrieveBundleDetails($itemcode);
		$mainheader = $this->job_order->getItemListforBundle($itemcode);
		$table		= '';
		$success	= true;
		if (empty($header)) {
			$table		= '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
			$success	= false;
		}
		//var_dump($header, $details);
		return array(
			'table'		=> $header,
			'details'	=> $details,
			'header'	=> $header,
			'mainheader'=> $mainheader,
			'success'	=> $success
		);
	}

	private function ajax_create_issue() {
		$job_release_no 			= $this->seq->getValue("JR");
		$data						= $this->input->post($this->fields4);
		$customer					= $this->input->post('h_customer');
		$data['job_release_no'] 	= $job_release_no;
		$data['transactiondate'] 	= date('Y-m-d', strtotime($data['transactiondate']));
		$data['issuancedate'] 		= date('Y-m-d', strtotime($data['issuancedate']));
		// foreach ($data['quantity'] as $key => $value) {
		// 	if ($value < 1) {
		// 		unset($data['itemcode'][$key]);
		// 		unset($data['linenum'][$key]);
		// 		unset($data['detailparticulars'][$key]);
		// 		unset($data['warehouse'][$key]);
		// 		unset($data['quantity'][$key]);
		// 		unset($data['unit'][$key]);
		// 		unset($data['serialnumbers'][$key]);
		// 	}
		// }

		$result						= $this->job_order->saveJobRelease($data);
		
		$this->job_order->createClearingEntries($data['job_release_no']);

		if ($result && $this->inventory_model) {
			$this->inventory_model->prepareInventoryLog('Job Release', $data['job_release_no'])
									->setDetails($customer)
									->computeValues()
									->logChanges();

			// $this->inventory_model->prepareInventoryLog('Job Release Parts', $data['job_release_no'])
			// 						->setDetails($customer)
			// 						->computeValues()
			// 						->logChanges();

			$this->inventory_model->setReference($data['job_release_no'])
									->setDetails($customer)
									->generateBalanceTable();
		}
		
		return array(	
			'result'=> $result
		);
	}

	private function ajax_edit_issue() {
		$job_release_no = $this->input->post('jobreleaseno');
		$data			= $this->input->post($this->fields4);
		$result = $this->job_order->getQty($job_release_no,$data);
		foreach ($result as $row) {
			$qty = $row->quantity;
		}
		return array(	
			'result' => $result
		);
	}

	private function ajax_update_issue() {
		$job_release_no = $this->input->post('jobreleaseno');
		$data			= $this->input->post($this->fields4);
		$data['issuancedate'] 		= date('Y-m-d', strtotime($data['issuancedate']));

		$customer		= $this->input->post('h_customer');
		
		$this->inventory_model->prepareInventoryLog('Job Release', $job_release_no)
								->preparePreviousValues();
														
		$result 		= $this->job_order->updateIssueParts($job_release_no,$data);

		$this->job_order->createClearingEntries($job_release_no);

		if ($result && $this->inventory_model) {
			$this->inventory_model->computeValues()
									->setDetails($customer)
									->logChanges();
		
			$this->inventory_model->generateBalanceTable();
		}
		return array(	
			'result' => $result
		);
	}

	private function ajax_delete_issue() {
		$delete_id = $this->input->post('id');
		
		if ($delete_id) {
			$result = $this->job_order->deleteJobRelease($delete_id);
		}
		if ($result && $this->inventory_model) {
			
				$this->inventory_model->prepareInventoryLog('Job Release', $delete_id)
										->computeValues()
										->logChanges('Cancelled');

				// $this->inventory_model->prepareInventoryLog('Job Release Parts', $delete_id)
				// 						->computeValues()
				// 						->logChanges('Cancelled');

				$this->job_order->createClearingEntries($delete_id);
			}
			$this->inventory_model->generateBalanceTable();
		
		return array(
			'success' => $result
		);
	}

	private function ajax_load_issue() {
		$jobno						= $this->input->post('jobno');
		$task						= $this->input->post('ajax_task');

		$result	= $this->job_order->getIssuedPartsNo($jobno);
		$table = '';
		
		if (empty($result)) {
			$table = '<tr><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($result as $row) {
		if($task == ''){
				$table .= '<tr data-id = "' . $row->jrno . '"">';
				$table .= '<td colspan="5">' . 'Part Issuance No.: '.$row->jrno . '</td>';
				$table .= '<td>' . '<a class="btn-sm" pointer id="editip" title="Edit"><span class="glyphicon glyphicon-pencil editip pointer" style="border: 1px solid gainsboro;
				padding: 3px 4px 3px 4px;
				background-color: lavender;"></span></a>' . '</td>';
				$table .= '<td>' . '<a class="btn-sm" pointer title="Delete"><span class="glyphicon glyphicon-trash deleteip pointer" style="border: 1px solid gainsboro;
				padding: 3px 4px 3px 4px;
				background-color: lavender;"></span></a>' . '</td>';
				$table .= '</tr>';
			}
			
			$list	= $this->job_order->getIssuedParts($row->jrno);
			foreach ($list as $key => $row) {
				if($row->quantity > 0){
					$table .= '<tr>';
					$table .= '<td>' . $row->itemcode . '</td>';
					$table .= '<td>' . $row->detailparticulars . '</td>';
					$table .= '<td>' . $row->description . '</td>';
					$table .= '<td>' . $row->quantity . '</td>';
					$table .= '<td>' . $row->unit . '</td>';
					if($task == ''){
						$table .= '<td>' . '' . '</td>';
						$table .= '<td>' . '' . '</td>';
					}
				}
			$table .= '</tr>';
		}
	}		
		
		return array(	
			'issuedparts'=> $table
		);
	}

	private function ajax_checkbundle() {
		$itemcode	= $this->input->post('itemcode');
		$result		= $this->job_order->retrieveItemDetails($itemcode);
		$success	= true;
		if (empty($result)) {
			$success	= false;
		}
		//var_dump($result);
		return array(
			'result'  => $result,
			'success' => $success
		);
	 }
	 
	private function ajax_edit() {
		$job_order_no 			= $this->input->post('job_order_no');
		$data = $this->input->post($this->fields);
		$data['stat'] = 'prepared';
		$data['job_order_no'] = $job_order_no[0];
		$data['transactiondate'] 	= date('Y-m-d', strtotime($data['transactiondate']));
		$data['print']				= '0';
		$data2 = $this->input->post($this->fields2);
		
		// $itemcodewosq					= $this->input->post('detail_itemcode');
		// if($data['service_quotation'] == '') {
		// 	$data2['itemcode']	= $itemcodewosq;
		// }
		//var_dump($data, $data2, $data['job_order_no']);
		$result		= $this->job_order->updateJO($data, $data2);

		$this->inventory_model->setReference($data['job_order_no'])
									->setDetails($data['customer'])
									->generateBalanceTable();

		return array(
			'redirect' => MODULE_URL,
			'success' => $result
		);
	}

	private function ajax_upload_file()
	{
		$post_data 		= $this->input->post();
		$upload_handler	= new UploadHandler();
		$reference 		= $post_data['reference'];
		$upload_result 	= false;
		$task 			= $post_data['task'];
		unset($post_data['task']);

		if (isset($upload_handler->response) && isset($upload_handler->response['files'])) {
			if(!isset($upload_handler->response['files'][0]->error)){
				/**
				 * Generate Attachment Id
				 * @param table
				 * @param group fields
				 * @param custom condition
				 */

				if ($task=='view') 
				$attachment_id = $this->job_order->getCurrentId("job_order_attachments", $reference);
			
				else
				$attachment_id = $this->job_order->getNextId("job_order_attachments","attachment_id");
				foreach($upload_handler->response['files'] as $key => $row) {
					$post_data['attachment_id'] 	= $attachment_id;
					$post_data['attachment_name'] 	= $row->name;
					$post_data['attachment_type'] 	= $row->type;
					$post_data['attachment_url']	= $row->url;
				}
				if ($task == 'view')
					$upload_result 	= $this->job_order->replaceAttachment($post_data);
				
				else
					$upload_result 	= $this->job_order->uploadAttachment($post_data);
			}else{
				$upload_result 	= false;
			}
		}
		if($upload_result && $task == 'listing'){
			$this->job_order->updateData(array('stat' => 'completed'), 'job_order', " job_order_no = '$reference' ");
		}
	}
	
	private function ajax_retrieve_bom_qty(){
		$bundle_item = $this->input->post('bundle');

		$result 	 = $this->job_order->retrieve_bom_qty($bundle_item);

		// var_dump($result);
		return $dataArray = array('result' => $result);
	}

	private function ajax_check_issuedqty() {
		$job_order_no	= $this->input->post('job_order_no');
		$result		= $this->job_order->getIssuedQty($job_order_no);
		
		foreach ($result as $row) {
			$qty = $row->issuedqty;
		}
		
		if(empty($result)){
			return array('result' => 'hmm');
		}
		
		return array('result' => $result);
	}
}