<?php
class controller extends wc_controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->url 			    = new url();
		$this->stock_transfer 	= new stock_transfer();
		$this->restrict 		= new inventory_restriction_model();
		$this->seq 				= new seqcontrol();
		$this->input            = new input();
		$this->ui 			    = new ui();
		$this->logs  			= new log;
		$this->view->title      = 'Stock Transfer';
		$this->show_input 	    = true;
		$this->inventory_model  = $this->checkoutModel('inventory_module/inventory_model');
		$session                = new session();
		$this->companycode      = $session->get('companycode');
		
		$this->user 		    = USERNAME;

		$this->fields1 = array(
			"stocktransferno",
			"reference",
			"source",
			"transactiondate",
			"destination",
			"transferdate",
			"prepared_by",
			"remarks",
			"total_amount"  
		);  
	  
		$this->fields2 = array(
			"itemcode",
			"detailparticular",
			"source",
			"destination",
			"ohqty",
			"qtytoapply",
			"uom",
			"price",
			"amount",
		);

		$this->approval_header = array(
			"stocktransferno",
			"reference",
			"source",
			"transactiondate",
			"destination",
			"transferdate",
			"approved_by",
			"remarks",
			"total_amount",
			"source_no" 
		);  
		
		$this->approval_fields = array(
			"itemcode",
			"linenum",
			"detailparticular",
			"ohqty",
			"qtytransferred",
			"qtytoapply",
			"uom",
			"price",
			"amount",
		);
	}

	public function  create() {
		$data = array();
		$data["ui"]                   = $this->ui;
		$data['show_input']           = $this->show_input;

		$data['button_name']          = "Save";
		$data["task"] 		          = "create";
		
		$data["cmp"]  		          = COMPANYCODE;
			
		$this->view->title 			= 'Stock Transfer Request';
		$data["item_list"] 			= $this->stock_transfer->getItemList();
		$data['ajax_task'] 			= 'create';
		$data['ajax_post']          = '';
		$data['transactionno']      = '';
		$data['reference']        	= '';
		$data['transactiondate'] 	= date('M j, Y');
		$data['transferdate']    	= '';
		$data['remarks'] 			= '';
		$data['prepared_by'] 		= '';
		$data['destination'] 		= "";
		$data['source']      		= "";
		$data['stat'] 				= "";
		$data['row_details'] 		= json_encode(array($this->fields2));
		$data['warehouse_list']		= $this->stock_transfer->getWarehouseList();		

		// Retrieve Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;

		$data['restrict_str'] 		= true;

		// Item Limit
		$item_limit 			= $this->stock_transfer->getReference("st_limit");
		$data['item_limit']		= ($item_limit[0]->value) 	? 	$item_limit[0]->value 	: 	50; 
		
		$this->view->load('stock_transfer/stocktransfer', $data);
	}

	public function edit($sid = ""){
		$this->view->title = 'Edit Stock Transfer Request';

		$stocktransferno = $sid;
		$data = (array) $this->stock_transfer->getStockTransferRequest($this->fields1, $stocktransferno);
		$data['transactionno'] = $stocktransferno;
		$data['transactiondate'] = date('M j, Y', strtotime($data['transactiondate']));
		$data['transferdate']    = date('M j, Y', strtotime($data['transferdate']));
		$data['ui'] = $this->ui;
		$data["item_list"] 			= $this->stock_transfer->getItemList();
		$data['row_details'] = json_encode($this->stock_transfer->getStockTransferDetailsRequest($stocktransferno));
		$data['ajax_task'] = 'edit';
		$data['ajax_post'] = "&stocktransferno=$stocktransferno";
		$data['show_input'] = true;
		$data["task"] 		= "edit";
		$data['h_site_source'] 	=	$data['source'];
		$data['warehouse_list']		= $this->stock_transfer->getWarehouseList();
		
		$current_stat 		=	$this->stock_transfer->getStat($sid,'stock_transfer');
		$data['stat'] 		=	$current_stat->stat;

		// Item Limit
		$item_limit 			= $this->stock_transfer->getReference("st_limit");
		$data['item_limit']		= ($item_limit[0]->value) 	? 	$item_limit[0]->value 	: 	50; 
		
		// Retrieve Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;

		$data['restrict_str'] 		= true;

		$this->view->load('stock_transfer/stocktransfer', $data);
	}

	public function view($sid = ""){
		$stocktransferno = $sid;
		
		$this->view->title 		= 'View Stock Transfer Request';
		$data = (array) $this->stock_transfer->getStockTransferRequest($this->fields1, $stocktransferno);
		$data['transactionno'] = $stocktransferno;
		$transactiondate 		= $data['transactiondate'];
		$restrict_str 			= $this->restrict->setButtonRestriction($transactiondate);
		$data['transactiondate'] = date('M j, Y', strtotime($transactiondate));
		$data['transferdate']    = date('M j, Y', strtotime($data['transferdate']));
		$data['ui'] = $this->ui;
		$data['warehouse_list']		= $this->stock_transfer->getWarehouseList();
		$data["item_list"] 			= $this->stock_transfer->getItemList();
		$data['row_details'] = json_encode($this->stock_transfer->getStockTransferDetailsRequest($stocktransferno));
		$data['show_input'] = false;
		$stocktransferno = $sid;
		$current_stat 		=	$this->stock_transfer->getStat($sid,'stock_transfer');
		$data['stat'] 		=	$current_stat->stat;
		$data["task"] 		= 	"view";
		// Retrieve Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;

		$data['restrict_str'] 		= $restrict_str;
		$this->view->load('stock_transfer/stocktransfer', $data);
	}

	public function edit_approval($sid = ""){
		$this->view->title = 'Edit Stock Transfer';

		$stocktransferno = $sid;
		$data = (array) $this->stock_transfer->getStockTransferApproval($this->approval_header, $stocktransferno);
		
		$source 	=	$data['source_no'];
		$data['transactionno'] = $stocktransferno;
		$data['transactiondate'] = date('M j, Y', strtotime($data['transactiondate']));
		$data['transferdate']    = date('M j, Y', strtotime($data['transferdate']));
		$data['ui'] = $this->ui;
		$data['warehouse_list']		= $this->stock_transfer->getWarehouseList();
		$data["item_list"] 			= $this->stock_transfer->getItemList();
		$data['row_details'] = json_encode($this->stock_transfer->getStockTransferDetailsApproval($this->approval_fields, $stocktransferno, false));
	
		$data['ajax_task'] = 'update_approval';
		$data['ajax_post'] = "&stocktransferno=$stocktransferno";
		$data['show_input'] = true;
		$data["task"] 		= "edit_approval";
		$data['h_site_source'] 	=	$data['source'];
		
		$current_stat 		=	$this->stock_transfer->getStat($sid,'stock_approval');
		$data['stat'] 		=	$current_stat->stat;

		// Retrieve Closed Date
		$close_date 			= $this->restrict->getClosedDate();
		$data['close_date']		= $close_date;

		$data['restrict_sta'] 	= true;

		$this->view->load('stock_transfer/stocktransfer_approval', $data);
	}
	
	public function view_approval($sid = ""){
		$stocktransferno = $sid;
		
		$this->view->title = 'View Stock Transfer';
		$data = (array) $this->stock_transfer->getStockTransferApproval($this->approval_header, $stocktransferno);
	
		$data['transactionno'] 	 = $stocktransferno;
		$source 				 = $data['source_no'];
		$data['transactiondate'] = date('M j, Y', strtotime($data['transactiondate']));
		$data['transferdate']    = date('M j, Y', strtotime($data['transferdate']));
		$data['ui'] 			 = $this->ui;
		$data['warehouse_list']	 = $this->stock_transfer->getWarehouseList();
		$data["item_list"] 		 = $this->stock_transfer->getItemList();
		$data['row_details'] 	 = json_encode($this->stock_transfer->getStockTransferDetailsApproval($this->approval_fields, $stocktransferno));
		$data['show_input'] 	 = false;
		$data['task'] 			 = "view_approval";
		$data['ajax_task'] 	     = "";
		$data['ajax_post'] 	     = "";
		$current_stat 			 = $this->stock_transfer->getStat($sid,'stock_approval');
		$data['stat'] 			 = $current_stat->stat;
		// Retrieve Closed Date
		$close_date 			= $this->restrict->getClosedDate();
		$data['close_date']		= $close_date;

		$data['restrict_sta'] 	= $this->restrict->setButtonRestriction($data['transactiondate']);
		$this->view->load('stock_transfer/stocktransfer_approval', $data);
	}

	public function release($sid = ""){
		$stocktransferno = $sid;

		$this->view->title = 'Release Stock Transfer Request';
		$data = (array) $this->stock_transfer->getStockTransferRequest($this->fields1, $stocktransferno);
		$data['approved_by'] 	 = "";
		$data['remarks'] 	 	 = "";
		$data['reference'] 	     = "";
		$data['transactionno'] 	 = $stocktransferno;
		$data['transactiondate'] = date('M j, Y', strtotime($data['transactiondate']));
		$data['transferdate']    = date('M j, Y', strtotime($data['transferdate']));
		$data['ui'] 			 = $this->ui;
		$data['warehouse_list']	 = $this->stock_transfer->getWarehouseList();
		$data["item_list"] 		 = $this->stock_transfer->getItemList();
		$data['row_details'] 	 = json_encode($this->stock_transfer->getStockTransferDetailsRequest($sid));
		$data['show_input'] 	 = true;
		$data["task"]  			 = "release";
		$data["ajax_task"] 		 = "set_release";
		$data['ajax_post']		 = '';
		$data['source_no'] 		 = $stocktransferno;
		// Retrieve Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;
		$this->view->load('stock_transfer/stocktransfer_approval', $data);
	}

	public function received($sid = ""){
		$stocktransferno = $sid;
	
		$this->view->title = 'View Stock Transfer';
		$data = (array) $this->stock_transfer->getStockTransferRequest($this->fields1, $stocktransferno);
		$data['transactionno'] = $stocktransferno;
		$data['transactiondate'] = date('M j, Y', strtotime($data['transactiondate']));
		$data['transferdate']    = date('M j, Y', strtotime($data['transferdate']));
		$data['ui'] = $this->ui;
		$data['warehouse_list']		= $this->stock_transfer->getWarehouseList();
		$data["item_list"] 			= $this->stock_transfer->getItemList();
		$data['row_details'] = json_encode($this->stock_transfer->getStockTransferDetails($this->fields2,$stocktransferno));
		$data['show_input'] = false;
		$data["task"] = "received";
		// Retrieve Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;
		$this->view->load('stock_transfer/stocktransfer_approval', $data);
	}

	public function listing()
	{
		$data['ui'] 				= $this->ui;
	
		$data['show_input'] 		= true;
		
		$data['date_today'] 		= date("M d, Y");
		$data["cmp"]                = COMPANYCODE;
		$data["mod"]                = "";
		$data["type"]               = "";
		$data["task"]               = "";
		$data["sid"]                = "";
		$data["prefix"]             = "";
		$data["site_id"]            = "";
		$data["custfilter"]         = "";
		$data['datefilter'] 		= $this->date->datefilterMonth();
		$data["types"] 				= $this->stock_transfer->getValue("wc_option", array("code ind","value val")," type = 'transfer_type' ","value");	
		$data["warehouses"] 		= $this->stock_transfer->getValue("warehouse", array("warehousecode ind","description val"),"stat = 'active'","warehousecode");

		$this->view->load('stock_transfer/stocktransfer_list', $data);
	}

	public function print_preview($voucherno) {
		$documentinfo		= $this->stock_transfer->getDocumentRequestInfo($voucherno);
		$documentdetails	= array(
			'Date'			=> $this->date->dateFormat($documentinfo->documentdate),
			'Transfer Date'	=> $this->date->dateFormat($documentinfo->transferdate),
			'Request #'		=> $voucherno,
			'Reference' 	=> $documentinfo->reference
		);

		$print = new print_inventory_model();
		$print->setDocumentType('Transfer - Request')
			  ->setFooterDetails(array('Approved By', 'Checked By'))
			  ->setCustomerDetails($documentinfo)
			  ->setDocumentDetails($documentdetails)
			  // ->addTermsAndCondition()
			  ->addReceived();

		$print->setHeaderWidth(array(40, 100, 30, 30))
				->setHeaderAlign(array('C', 'C', 'C', 'C'))
				->setHeader(array('Item Code', 'Description', 'Qty', 'UOM'))
				->setRowAlign(array('L', 'L', 'R', 'L'))
				->setSummaryWidth(array('170', '30'));
		
		$documentcontent	= $this->stock_transfer->getDocumentRequestContent($voucherno);

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
				$print->drawSummary(array('Total Qty' => $total_quantity));
				$total_quantity = 0;
			}
		}
		$print->drawSummary(array('Total Qty' => $total_quantity));

		$print->drawPDF('Approved Stock Transfer - ' . $voucherno);
	}

	public function print_approval($voucherno) {
		$documentinfo		= $this->stock_transfer->getDocumentApprovalInfo($voucherno);
		$documentdetails	= array(
			'Date'			=> $this->date->dateFormat($documentinfo->documentdate),
			'Transfer Date'	=> $this->date->dateFormat($documentinfo->transferdate),
			'Transfer #'	=> $voucherno,
			'Request #'		=> $documentinfo->referenceno,
			'Reference' 	=> $documentinfo->reference
		);

		$print = new print_inventory_model();
		$print->setDocumentType('Transfer - Approved')
				->setFooterDetails(array('Approved By', 'Checked By'))
				->setCustomerDetails($documentinfo)
				->setDocumentDetails($documentdetails)
				// ->addTermsAndCondition()
				->addReceived();

		

		$print->setHeaderWidth(array(30, 50, 10, 20, 30, 30, 30))
				->setHeaderAlign(array('C', 'C', 'C', 'C', 'C', 'C', 'C'))
				->setHeader(array('Item Code', 'Description', 'Qty', 'UOM', 'S/N', 'E/N', 'C/N'))
				->setRowAlign(array('L', 'L', 'R', 'L', 'L', 'L', 'L'))
				->setSummaryWidth(array('170', '30'));
		
		$documentcontent	= $this->stock_transfer->getDocumentApprovalContent($voucherno);
		$detail_height = 37;

		$total_quantity = 0;
		foreach ($documentcontent as $key => $row) {
			if ($key % $detail_height == 0) {
				$print->drawHeader();
			}

			$total_quantity	+= $row->Quantity;
			$row->Quantity	= number_format($row->Quantity, 2);
			
			if($row->serialno != "" || $row->engineno != "" || $row->chassisno != ""){
				$qty = number_format(1, 2);
				$sn = ($row->serialno == '')? 'N/A':$row->serialno;
				$en = ($row->engineno == '')? 'N/A':$row->engineno;
				$cn = ($row->chassisno == '')? 'N/A':$row->chassisno;
			} 
			else{
				$qty = $row->Quantity;
				$sn = 'N/A';
				$en = 'N/A';
				$cn = 'N/A';
			}

			$row->Quantity = $qty;
			$row->serialno = $sn;
			$row->engineno = $en;
			$row->chassisno = $cn;

			$print->addRow($row);

			if (($key + 1) % $detail_height == 0) {
				$print->drawSummary(array('Total Qty' => $total_quantity));
				$total_quantity = 0;
			}
		}
		$print->drawSummary(array('Total Qty' => $total_quantity));
		$print->drawPDF('Approved Transfer - ' . $voucherno);
	}

	public function ajax($task){
		header('Content-type: application/json');
		$result 	=	"";

		if ($task == 'save_temp_data' || $task == 'create') 
		{
			$result = $this->add();
		}
		else if ($task == 'edit') 
		{
			$result = $this->update();
		}
		else if ($task == 'delete')
		{
			$result = $this->delete();
		}
		else if($task == 'cancel')
		{
			$result = $this->cancel();
		}
		else if($task == 'list'){
			$result = $this->ajax_list();
		}
		else if($task == 'set_release'){
			$result = $this->set_release();
		}
		else if($task == 'set_received'){
			$result = $this->set_received();
		}
		else if($task == 'get_value') 
		{
			$result = $this->get_value();
		}
		else if( $task == 'get_item_details' )
		{
			$result = $this->get_details();
		}
		else if( $task == 'get_warehouse_list' )
		{
			$result = $this->get_warehouse_list();
		}
		else if( $task == 'update_approval' )
		{
			$result = $this->update_approval();
		}
		else if( $task == 'update_request_status' )
		{
			$result = $this->update_request_status();
		} 
		else if( $task == 'delete_approval' )
		{
			$result = $this->delete_approval();
		}
		else if( $task == 'ajax_load_serial' )
		{
			$result = $this->ajax_load_serial();
		}
		else if( $task == 'save_serialized' )
		{
			$result = $this->save_serialized();
		}
		echo json_encode($result); 
	}

	private function get_warehouse_list(){
		
		$code 	=	$this->input->post('warehouse');

		$warehouse_list 	=	$this->stock_transfer->getWarehouseList($code);

		$list 		= "<option></option>";
		foreach($warehouse_list as $row)
		{
			$list 	.=	"<option value='".$row->ind."'>".$row->val."</option>";
		}
		
		$dataArray = array( "list" => $list );

		return $dataArray;
	}

	private function ajax_list()
	{
		$data = $this->input->post(array('search','filter', 'daterangefilter','warehouse','type'));
		$search = $data['search'];
		$filter = $data['filter'];
		$datefilter	= $data['daterangefilter'];
		$dates[0] 	=	"";
		$dates[1]	=	"";
		if($datefilter!=""){
			$datefilter = explode('-', $datefilter);
			foreach ($datefilter as $date) {
				$dates[] = date('Y-m-d', strtotime($date));
			}
		}
		$warehouse 	=	$data['warehouse'];
		$type 		=	$data['type'];

		$pagination_req = $this->stock_transfer->getStockTransferRequestList($search, $filter, $dates[0], $dates[1], $warehouse, $type);
		$pagination_app = $this->stock_transfer->getStockTransferApprovalList($search, $filter, $dates[0], $dates[1], $warehouse, $type);
		
		$table = '';

		$transfer_in 	=	"";
		$transfer_out 	=	"";
		
		$not_open 		= 0;
		$not_released  	= 0;

		for ($i = 0; $i < count($pagination_req->result); $i++) {

			$transactiondate	= $pagination_req->result[$i]->transactiondate;
			$date 				= $this->date->dateDbFormat($transactiondate);
			$restrict_str 		= $this->restrict->setButtonRestriction($date);
			$transactiondate	= date("M d, Y",strtotime($transactiondate));
			$transferdate	    = $pagination_req->result[$i]->transferdate;
			$transferdate		= date("M d, Y",strtotime($transferdate));
			$stocktransferno	= $pagination_req->result[$i]->stocktransferno;
			$source 		    = $pagination_req->result[$i]->source;
			$destination	    = $pagination_req->result[$i]->destination;
			$prepared_by        = $pagination_req->result[$i]->prepared_by;
			$stat				= $pagination_req->result[$i]->stat;
			$remarks			= $pagination_req->result[$i]->remarks;
			$total_amount		= $pagination_req->result[$i]->total_amount;
			$enteredby			= $pagination_req->result[$i]->enteredby;
			
			$dropdown = $this->ui->loadElement('check_task')
								 ->addView()
								 ->addEdit($stat == 'open' && ($enteredby == $this->user) && $restrict_str)
								 ->addOtherTask('Transfer Stocks', 'open',($stat == 'approved' || $stat == 'partial') && $restrict_str)
								 ->addOtherTask('Approve', 'thumbs-up', $stat == 'open' && $restrict_str)
								 ->addOtherTask('Reject', 'thumbs-down', $stat == 'open' && $restrict_str)
								 ->addOtherTask('Close Request', 'bookmark', $stat == 'posted' && $restrict_str)
								 ->addDelete(($stat == 'open') &&  ($enteredby == $this->user) && $restrict_str)
								 ->addPrint()
								 ->setValue($stocktransferno)
								 ->setLabels(array('delete'=>'Cancel'))
								 ->draw();
			
			$status = '';

			if( $stat == 'posted' ) {
				$status = '<span class="label label-default">READY FOR CLOSING</span>';
			} else if( $stat == 'open' || $stat == 'request' ) {
				$status = '<span class="label bg-purple">PENDING</span>';
			} else if( $stat == 'approved' ) {
				$status = '<span class="label label-warning">APPROVED</span>';
			} else if( $stat == 'partial') {
				$status = '<span class="label label-info">PARTIAL</span>';
			} else if( $stat == 'rejected') {
				$status = '<span class="label label-danger">REJECTED</span>';
			} else if( $stat == 'cancelled' ){
				$status = '<span class="label label-danger">CANCELLED</span>';
			}

			$transfer_out .= '<tr>';
			$transfer_out .= '<td>' . $dropdown . '</td>';
			$transfer_out .= '<td>' . $stocktransferno. '</td>';
			$transfer_out .= '<td>' . $destination. '</td>';
			$transfer_out .= '<td>' . $source. '</td>';
			$transfer_out .= '<td>' . $transactiondate . '</td>';
			$transfer_out .= '<td>' . $status. '</td>';
			$transfer_out .= '</tr>';
			
		} 

		for ($i = 0; $i < count($pagination_app->result); $i++) {

			$transactiondate	= $pagination_app->result[$i]->transactiondate;
			$date 				= $this->date->dateDbFormat($transactiondate);
			$restrict_sta 		= $this->restrict->setButtonRestriction($date);
			$transactiondate	= date("M d, Y",strtotime($transactiondate));
			$transferdate	    = $pagination_app->result[$i]->transferdate;
			$transferdate		= date("M d, Y",strtotime($transferdate));
			$stocktransferno	= $pagination_app->result[$i]->stocktransferno;
			$source_no			= $pagination_app->result[$i]->source_no;		
			$source 		    = $pagination_app->result[$i]->source;
			$destination	    = $pagination_app->result[$i]->destination;
			$approved_by        = $pagination_app->result[$i]->approved_by;
			$stat				= $pagination_app->result[$i]->stat;
			$remarks			= $pagination_app->result[$i]->remarks;
			$total_amount		= $pagination_app->result[$i]->total_amount;
			$enteredby			= $pagination_app->result[$i]->enteredby;
			$request_stat		= $pagination_app->result[$i]->request_stat;

			$dropdown = $this->ui->loadElement('check_task')
								 ->addOtherTask('View','eye-open',true,'view_approval')
								 ->addOtherTask('Edit','pencil',($request_stat != 'closed' &&  ($enteredby == $this->user)) && $restrict_sta,'edit_approval')
								 ->addOtherTask('Print','print',true,'print_approval')
								//  ->addDelete(($stat != 'posted') &&  ($enteredby == $this->user))
								 ->addOtherTask('Cancel','trash',($request_stat != 'closed') &&  ($enteredby == $this->user) && $restrict_sta,'delete_approval') 
								 ->setValue($stocktransferno)
								 ->draw();
			$status = '';

			if( $stat == 'open' || $stat == 'released'){
				$status = '<span class="label label-success">TRANSFERRED</span>';
			} else if( $stat == 'cancelled' ){
				$status = '<span class="label label-danger">CANCELLED</span>';
			}

			$transfer_in .= '<tr>';
			$transfer_in .= '<td>' . $dropdown . '</td>';
			$transfer_in .= '<td>' . $stocktransferno. '</td>';
			$transfer_in .= '<td>' . $source_no. '</td>';
			$transfer_in .= '<td>' . $destination. '</td>';
			$transfer_in .= '<td>' . $source. '</td>';
			$transfer_in .= '<td>' . $transactiondate . '</td>';
			$transfer_in .= '<td>' . $status. '</td>';
			$transfer_in .= '</tr>';
		} 
		
		if ( $transfer_out == "" ){
			$transfer_out 	.=	'<tr><td colspan="6" class="text-center">No Records Found</td></tr>';
		}
		if( $transfer_in == "" ){
			$transfer_in 	.=	'<tr><td colspan="6" class="text-center">No Records Found</td></tr>';
		}

		$pagination_req->out = $transfer_out;
		$pagination_req->in  = $transfer_in;
		return $pagination_req;

	}

	private function get_details()
	{
		$itemcode 	= $this->input->post('itemcode');
		$warehouse 	= $this->input->post('warehouse');

		$result 	= $this->stock_transfer->getItemDetails($itemcode, $warehouse);
		if(!$result)
			$var = (object) array("notfound"=>true); 
		
		return $result;
	}

	private function set_release(){
		// Merge header and details data
		$data						= $this->input->post($this->approval_header);
		$data2						= $this->getItemDetails();
		$data2						= $this->cleanData($data2);
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		$data['transferdate']		= $this->date->dateDbFormat($data['transferdate']);
		$seq						= new seqcontrol();
		$sta_no 					= $seq->getValue('STA');
		$data['stocktransferno']	= $sta_no;

		$itemcode 	= explode(',',$this->input->post('serialitemcode'));
		$linenum 	= explode(',',$this->input->post('seriallinenum'));
		$serialno 	= explode(',',$this->input->post('serialno'));
		$chassisno 	= explode(',',$this->input->post('chassisno'));
		$engineno 	= explode(',',$this->input->post('engineno'));
		foreach ($itemcode as $key => $value) {
			$voucherno[] = $sta_no;
		}
		$values = array(
					'stocktransferno' => $voucherno,
					'itemcode' 	=> $itemcode,
					'linenum' 	=> $linenum,
					'serialno' 	=> $serialno,
					'chassisno' => $chassisno,
					'engineno' 	=> $engineno,
				);

		$result = $this->stock_transfer->saveSerializedItems($values);
		
		$result	= $this->stock_transfer->saveStockTransferApproval($data, $data2);

		if ($result && $this->inventory_model) {
			$this->inventory_model->prepareInventoryLog('Stock Transfer', $sta_no)
									->setDetails($data['approved_by'])
									->computeValues()
									->logChanges();

			$this->inventory_model->setReference($sta_no)
								->setDetails($data['approved_by'])
								->generateBalanceTable();
		}
		$redirect_url = MODULE_URL;

		return array(
			'redirect'	=> $redirect_url,
			'success'	=> $result
		);
	}

	private function update_approval(){
		$this->fields1[]   			= "source_no";	
		$data						= $this->input->post($this->approval_header);
		unset($data['voucherno']);

		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		$data['transferdate']		= $this->date->dateDbFormat($data['transferdate']);
		$voucherno					= $data['stocktransferno'];
		$data2						= $this->getItemDetails();
		$data2						= $this->cleanData($data2);

		$this->inventory_model->prepareInventoryLog('Stock Transfer', $voucherno)
								->preparePreviousValues();

		$result						= $this->stock_transfer->updateStockApproval($data, $data2, $voucherno);
	
		if ($result && $this->inventory_model) {
			$this->inventory_model->computeValues()
									->setDetails($data['approved_by'])
									->logChanges();

			$this->inventory_model->generateBalanceTable();
		}
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function update_request_status(){
		$transferno 				= $this->input->post('transferno');
		$status 					= $this->input->post('status');

		$data['stat'] 				= $status;
		$result						= $this->stock_transfer->updateStockTransferStatus($data,$transferno);
	
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function getItemDetails() {
		$data = array();
		$temp = $this->input->post($this->approval_fields);
		// var_dump($temp);
		foreach ($temp['qtytransferred'] as $key => $quantity) {
			if ($quantity < 1) {
				foreach ($this->approval_fields as $field) {
					if (is_array($temp[$field])) {
						unset($temp[$field][$key]);
					}
				}
			}
		}
		foreach ($this->approval_fields as $field) {
			if (is_array($temp[$field])) {
				$data[$field] = array_values($temp[$field]);
			} else {
				$data[$field] = $temp[$field];
			}
		}
		// var_dump($data);
		return $data;
	}

	private function set_received(){
		$stocktransferno 	= $this->input->post('transaction_no');
		$approved_by 		= $this->input->post('approved_by');

		$data['stat'] 	=	'received';
		$result 	= $this->stock_transfer->updateStatus($data, 'stock_transfer', $stocktransferno);
		// var_dump($result);
		if( $result )
		{
			$msg = "success";
			$this->logs->saveActivity("Received Stock Transfer [$stocktransferno] ");
			
			if ( $this->inventory_model ) {
				$this->inventory_model->prepareInventoryLog('Stock Transfer', $stocktransferno)
									->setDetails($approved_by)
									->computeValues()
									->logChanges();

				$this->inventory_model->setReference($stocktransferno)
									->setDetails($approved_by)
									->generateBalanceTable();
			}
		}
		else
		{
			$msg = $result;
		}

		return $dataArray = array("msg" => $msg);
	}

	private function get_value()
	{
		$data_cond = $this->input->post("event");
		
		if($data_cond == "getPartnerInfo")
		{
			$data_var = array('address1', "tinno", "terms");
			$code 	  = $this->input->post("code");

			$cond 	  = "partnercode = '$code'";

			$result = $this->stock_transfer->getValue("partners", $data_var, $cond);

			$dataArray = array( "address" => $result[0]->address1, "tinno" => $result[0]->tinno, "terms" => $result[0]->terms );
		}
		else if($data_cond == "exchange_rate")
		{
			$data_var = array("accountnature");
			$account  = $this->input->post("account");

			$cond 	  = "accountname = '$account'";

			$result = $this->stock_transfer->getValue("chartaccount", $data_var, $cond);

			$dataArray = array("accountnature" => $result[0]->accountnature);

		}
		
		return $dataArray;
	}
	
	private function add()
	{
		$fields1 = array(
				"referenceno"=>"reference",
 				"site_source"=>"source",
  				"transactiondate",
  				"site_destination"=>"destination",
				"transferdate",
				"prepared_by",
  				"remarks",
				"total_amount"  
				);  
  
		$data = $this->input->post($fields1);
		$data2 = $this->input->post($this->fields2);

		$data['transactiondate'] = date('Y-m-d', strtotime($data['transactiondate']));
		$data['transferdate'] = date('Y-m-d', strtotime($data['transferdate']));
		$fetch_seq	= $this->seq->getValue('ST');
		$data['stocktransferno'] = $fetch_seq;
		$data['stat'] = 'open';
		$data2['stat'] = 'open'; 
		$data['total_amount'] = str_replace(",","",$data["total_amount"]);
		$result = $this->stock_transfer->saveStockTransferRequest($data,$data2);

		$this->logs->saveActivity("Create New Stock Transfer Request [".$data["stocktransferno"]."] ");
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}
	
	private function update()
	{
		$fields1 = array(
				"referenceno"=>"reference",
 				"site_source"=>"source",
  				"transactiondate",
  				"site_destination"=>"destination",
				"transferdate",
				"prepared_by",
  				"remarks",
				"total_amount"  
				);  

		//$data = $this->input->post();
		$data = $this->input->post($fields1);
		$data2 = $this->input->post($this->fields2);

		$data['transactiondate'] = date('Y-m-d', strtotime($data['transactiondate']));
		$data['transferdate'] = date('Y-m-d', strtotime($data['transferdate']));
		$data['stocktransferno'] = $this->input->post('stocktransferno');
		$data['stat'] = 'open';
		$data2['stat'] = 'open'; 
		$data['total_amount'] = str_replace(",","",$data["total_amount"]);
		$result = $this->stock_transfer->updateStockTransferRequest($data, $data2, $data['stocktransferno']);
		$this->logs->saveActivity("Update Stock Transfer Request [".$data['stocktransferno']."] ");
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}
	private function delete() 
	{
		
		$delete_id = $this->input->post('voucherno');
		// var_dump($delete_id);
		if ($delete_id) {
			$this->stock_transfer->deleteStockTransfer($delete_id);
			$this->logs->saveActivity("Delete Stock Transfer Request [".$delete_id."] ");
		}

		return array(
			'msg'	=> ''
		);
	}

	private function delete_approval() 
	{
		$delete_id = $this->input->post('voucherno');

		$retrieve_details =	$this->stock_transfer->getStockTransferApproval(array('approved_by'),$delete_id);
		$approved_by 	  = isset($retrieve_details->approved_by)	?	$retrieve_details->approved_by 	:	"";
		$result = 0;
		if ($delete_id) {
			$result = $this->stock_transfer->deleteStockTransferApproval($delete_id);
		}
		if ($result && $this->inventory_model) {
			$this->inventory_model->prepareInventoryLog('Stock Transfer', $delete_id)
										->computeValues()
										->logChanges('Cancelled');

			$this->inventory_model->generateBalanceTable();
		}
		return array(
			'msg'	=> ''
		);
	}

	private function ajax_load_serial(){
		$task 		= $this->input->post('task');
		$sourceno 	= $this->input->post('sourceno');
		$itemcode 	= $this->input->post('itemcode');
		$itemname 	= $this->input->post('itemname');
		$linenum 	= $this->input->post('linenum');
		$max 		= $this->input->post('max');
		$warehouse 	= $this->input->post('warehouse');

		if ($task=='view_approval') {
			$result 	= $this->stock_transfer->retrieveApprovedSerial($sourceno, $linenum, $itemcode);
		}
		else{
			$result 	= $this->stock_transfer->retrieveSerial($itemcode, $warehouse);
		}

		$table 		= '';
		foreach ($result as $key => $row) {
			$table 	.= '<tr>';

			if ($task != 'view_approval') {	
			$table 	.= '<td><input type="checkbox" name="chkitem" class="chkitem" data-itemcode="'.$itemcode.'" data-linenum="'.$linenum.'" data-serial="'.$row->serialno.'" data-chassis="'.$row->chassisno.'" data-engine="'.$row->engineno.'" data-maxval="'.$max.'"></td>';
			}
			else{
			$table 	.= '<td></td>';
			}

			$table 	.= '<td class="text-center">'.$itemcode.'</td>';
			$table 	.= '<td class="text-center">'.$itemname.'</td>';
			$table 	.= '<td class="text-center">'.$row->serialno.'</td>';
			$table 	.= '<td class="text-center">'.$row->chassisno.'</td>';
			$table 	.= '<td class="text-center">'.$row->engineno.'</td>';
			$table 	.= '</tr>';
		}
		if ($table == '') {
			$table = '<tr><td colspan="6" class="text-center"><b>No Records Found</b></td></tr>';
		}
		$table .= '<script>checkSelected($(".chkitem"));</script>';
		
		return $table;
	}
	
}
?>