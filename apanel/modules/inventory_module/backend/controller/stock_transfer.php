<?php
class controller extends wc_controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->url 			    = new url();
		$this->stock_transfer 	= new stock_transfer();
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
	}

	public function  create() {
		$data = array();
		$data["ui"]                   = $this->ui;
		$data['show_input']           = $this->show_input;

		$data['button_name']          = "Save";
		$data["task"] 		          = "create";
		
		$data["cmp"]  		          = COMPANYCODE;
			
		$this->view->title 			= 'Stock Transfer Request';
		$data['warehouse_list']		= $this->stock_transfer->getWarehouseList();
		$data["item_list"] 			= $this->stock_transfer->getItemList();
		$data['ajax_task'] 			= 'create';
		$data['ajax_post']          = '';
		$data['transactionno']      = '';
		$data['reference']        = '';
		$data['transactiondate'] = date('M j, Y');
		$data['transferdate']    = '';
		$data['remarks'] = '';
		$data['prepared_by'] = '';
		$data['destination'] = "";
		$data['source']      = "";
		$data['stat'] 		= "";
		$row = array();
		$row["itemcode"] = "";
		$row["itemname"] = "";
		$row["source"] = "";
		$row["destination"] = "";
		$row["ohqty"] = "";
		$row["qtytoapply"] = "";
		$row["uom"] = "";
		$row["price"] = "";
		$row["amount"] = "";
		$data['row_details'] = json_encode(array($row));

		$this->view->load('stock_transfer/stocktransfer', $data);
	}

	public function edit($sid = ""){
		$this->view->title = 'Edit Stock Transfer Request';
	
		$fields1 = array(
		"reference",
		"source",
		"transactiondate",
		"destination",
		"transferdate",
		"prepared_by",
		"remarks",
		"total_amount"  
		);  
  
		$fields2 = array("dtl.itemcode",
				"i.itemname",
				"dtl.detailparticular",
				"dtl.source",
				"dtl.destination",
				"dtl.ohqty",
				"dtl.qtytoapply",
				"dtl.uom",
				"dtl.price",
				"dtl.amount",
		);
		$stocktransferno = $sid;
		$data = (array) $this->stock_transfer->getStockTransfer($fields1, $stocktransferno);
		$data['transactionno'] = $stocktransferno;
		$data['transactiondate'] = date('M j, Y', strtotime($data['transactiondate']));
		$data['transferdate']    = date('M j, Y', strtotime($data['transferdate']));
		$data['ui'] = $this->ui;
		$data['warehouse_list']		= $this->stock_transfer->getWarehouseList();
		$data["item_list"] 			= $this->stock_transfer->getItemList();
		$data['row_details'] = json_encode($this->stock_transfer->getStockTransferDetails($fields2,$stocktransferno));
		$data['ajax_task'] = 'edit';
		$data['ajax_post'] = "&stocktransferno=$stocktransferno";
		$data['show_input'] = true;
		$data["task"] 		= "edit";
		
		$current_stat 		=	$this->stock_transfer->getStat($sid);
		$data['stat'] 		=	$current_stat->stat;

		$this->view->load('stock_transfer/stocktransfer', $data);
	}

	public function view($sid = ""){
		$stocktransferno = $sid;
		$fields1 = array(
		"reference",
		"source",
		"transactiondate",
		"destination",
		"transferdate",
		"prepared_by",
		"remarks",
		"total_amount"  
		);  
  
		$fields2 = array("dtl.itemcode",
				"i.itemname",
				"dtl.detailparticular",
				"dtl.source",
				"dtl.destination",
				"dtl.ohqty",
				"dtl.qtytoapply",
				"dtl.uom",
				"dtl.price",
				"dtl.amount",
		);
		$this->view->title = 'View Stock Transfer';
		$data = (array) $this->stock_transfer->getStockTransfer($fields1, $stocktransferno);
		$data['transactionno'] = $stocktransferno;
		$data['transactiondate'] = date('M j, Y', strtotime($data['transactiondate']));
		$data['transferdate']    = date('M j, Y', strtotime($data['transferdate']));
		$data['ui'] = $this->ui;
		$data['warehouse_list']		= $this->stock_transfer->getWarehouseList();
		$data["item_list"] 			= $this->stock_transfer->getItemList();
		$data['row_details'] = json_encode($this->stock_transfer->getStockTransferDetails($fields2,$stocktransferno));
		$data['show_input'] = false;
		$stocktransferno = $sid;
		$fields1 = array(
		"reference",
		"source",
		"transactiondate",
		"destination",
		"transferdate",
		"prepared_by",
		"remarks",
		"total_amount"  
		);  
  
		$fields2 = array("dtl.itemcode",
				"i.itemname",
				"dtl.detailparticular",
				"dtl.source",
				"dtl.destination",
				"dtl.ohqty",
				"dtl.qtytoapply",
				"dtl.uom",
				"dtl.price",
				"dtl.amount",
		);
		$this->view->title = 'View Stock Transfer';
		$data = (array) $this->stock_transfer->getStockTransfer($fields1, $stocktransferno);
		$data['transactionno'] = $stocktransferno;
		$data['transactiondate'] = date('M j, Y', strtotime($data['transactiondate']));
		$data['transferdate']    = date('M j, Y', strtotime($data['transferdate']));
		$data['ui'] = $this->ui;
		$data['warehouse_list']		= $this->stock_transfer->getWarehouseList();
		$data["item_list"] 			= $this->stock_transfer->getItemList();
		$data['row_details'] = json_encode($this->stock_transfer->getStockTransferDetails($fields2,$stocktransferno));
		$data['show_input'] = false;
		$data["task"]  = "view";

		$current_stat 		=	$this->stock_transfer->getStat($sid);
		$data['stat'] 		=	$current_stat->stat;
		$this->view->load('stock_transfer/stocktransfer', $data);
	}

	public function release($sid = ""){
		$stocktransferno = $sid;
		$fields1 = array(
		"reference",
		"source",
		"transactiondate",
		"destination",
		"transferdate",
		"prepared_by",
		"remarks",
		"total_amount"  
		);  
  
		$fields2 = array("dtl.itemcode",
				"i.itemname",
				"dtl.detailparticular",
				"dtl.source",
				"dtl.destination",
				"dtl.ohqty",
				"dtl.qtytoapply",
				"dtl.uom",
				"dtl.price",
				"dtl.amount",
		);
		$this->view->title = 'View Stock Transfer';
		$data = (array) $this->stock_transfer->getStockTransfer($fields1, $stocktransferno);
		$data['transactionno'] = $stocktransferno;
		$data['transactiondate'] = date('M j, Y', strtotime($data['transactiondate']));
		$data['transferdate']    = date('M j, Y', strtotime($data['transferdate']));
		$data['ui'] = $this->ui;
		$data['warehouse_list']		= $this->stock_transfer->getWarehouseList();
		$data["item_list"] 			= $this->stock_transfer->getItemList();
		$data['row_details'] = json_encode($this->stock_transfer->getStockTransferDetails($fields2,$stocktransferno));
		$data['show_input'] = false;
		$data["task"]  = "release";
		$this->view->load('stock_transfer/stocktransfer_approval', $data);
	}

	public function received($sid = ""){
		$stocktransferno = $sid;
		$fields1 = array(
		"reference",
		"source",
		"transactiondate",
		"destination",
		"transferdate",
		"prepared_by",
		"remarks",
		"total_amount"  
		);  
  
		$fields2 = array("dtl.itemcode",
				"i.itemname",
				"dtl.detailparticular",
				"dtl.source",
				"dtl.destination",
				"dtl.ohqty",
				"dtl.qtytoapply",
				"dtl.uom",
				"dtl.price",
				"dtl.amount",
		);
		$this->view->title = 'View Stock Transfer';
		$data = (array) $this->stock_transfer->getStockTransfer($fields1, $stocktransferno);
		$data['transactionno'] = $stocktransferno;
		$data['transactiondate'] = date('M j, Y', strtotime($data['transactiondate']));
		$data['transferdate']    = date('M j, Y', strtotime($data['transferdate']));
		$data['ui'] = $this->ui;
		$data['warehouse_list']		= $this->stock_transfer->getWarehouseList();
		$data["item_list"] 			= $this->stock_transfer->getItemList();
		$data['row_details'] = json_encode($this->stock_transfer->getStockTransferDetails($fields2,$stocktransferno));
		$data['show_input'] = false;
		$data["task"] = "received";
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
		$data["warehouses"] 		= $this->stock_transfer->getValue("warehouse", array("warehousecode ind","description val"),'',"warehousecode");

		$this->view->load('stock_transfer/stocktransfer_list', $data);
	}

	public function print_preview($voucherno) 
	{
		$docinfo_table  = "stock_transfer st";
		$docinfo_fields = array(
			"st.stocktransferno",
			"st.reference",
			"w.description source",
			"st.transactiondate",
			"w2.description destination",
			"st.transferdate",
			"st.prepared_by",
			"st.remarks",
			"st.total_amount"  
		);
		$docinfo_join   = "warehouse w ON w.warehousecode = st.source AND w.companycode = st.companycode ";
		$docinfo_join   .= " LEFT JOIN warehouse w2 ON w2.warehousecode = st.destination AND w2.companycode = st.companycode ";
		$docinfo_cond 	= "st.stocktransferno = '$voucherno'";

		$documentinfo  	= $this->stock_transfer->retrieveData($docinfo_table, $docinfo_fields, $docinfo_cond, $docinfo_join);
		
		$docdet_table   = "stock_transfer_details dtl";
		$docdet_fields = $fields2 = array("dtl.itemcode",
			"dtl.detailparticular",
			"dtl.qtytoapply",
			"dtl.uom",
			"dtl.price",
			"dtl.amount"
		);

		$docdet_cond    = "dtl.stocktransferno = '$voucherno'";
		$docdet_join 	= "items i ON dtl.itemcode = i.itemcode";
		$docdet_groupby = "";
		$docdet_orderby = "dtl.linenum";
		$documentdetails = $this->stock_transfer->retrieveData($docdet_table, $docdet_fields, $docdet_cond, $docdet_join, $docdet_orderby, $docdet_groupby);
		
		$customercode 		=	$this->stock_transfer->getValue("packinglist", array('customer')," voucherno = '$voucherno'");
		
		$custField			= array('partnercode','first_name','last_name','address1','email','tinno','terms','mobile');
		// $customerDetails	= $this->stock_transfer->retrieveData("partners",$custField," partnertype = 'customer' AND partnercode = '".$customercode[0]->customer."'");

		$print = new print_inventory_model('P', 'mm', 'Letter');
		
		$print->setDocumentType('Stock Transfer')
				->setDocumentCode('ST')
				->setDocumentInfo($documentinfo[0])
				->setDocumentDetails($documentdetails)
				->drawPDF('st_voucher_' . $voucherno);
	}


	public function ajax($task)
	{
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
		$datefilter = explode('-', $datefilter);
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$warehouse 	=	$data['warehouse'];
		$type 		=	$data['type'];

		$pagination = $this->stock_transfer->getStockTransferList($search, $filter, $dates[0], $dates[1], $warehouse, $type);
		//var_dump($pagination);
		$table = '';

		$transfer_in 	=	"";
		$transfer_out 	=	"";
		
		$not_open 		= 0;
		$not_released  	= 0;

		if (empty($pagination->result)) {
			//$table = '<tr><td colspan="5" class="text-center"><b>No Records Found</td></tr>';
			$transfer_in 	.=	'<tr><td colspan="6" class="text-center">No Records Found</td></tr>';
			$transfer_out 	.=	'<tr><td colspan="6" class="text-center">No Records Found</td></tr>';
		}

		for ($i = 0; $i < count($pagination->result); $i++) {

			$transactiondate	= $pagination->result[$i]->transactiondate;
			$transactiondate	= date("M d, Y",strtotime($transactiondate));
			$transferdate	    = $pagination->result[$i]->transferdate;
			$transferdate		= date("M d, Y",strtotime($transferdate));
			$stocktransferno	= $pagination->result[$i]->stocktransferno;
			$source 		    = $pagination->result[$i]->source;
			$destination	    = $pagination->result[$i]->destination;
			$prepared_by        = $pagination->result[$i]->prepared_by;
			$stat				= $pagination->result[$i]->stat;
			$remarks			= $pagination->result[$i]->remarks;
			$total_amount		= $pagination->result[$i]->total_amount;

			if( $stat == 'open' )
			{
				$dropdown = $this->ui->loadElement('check_task')
								 ->addView()
								 ->addEdit(($stat != 'released'))
								 ->addOtherTask('Release', 'open',($stat == 'open'))
								 ->addDelete($stat != 'released')
								 ->setValue($stocktransferno)
								 ->setLabels(array('delete'=>'Cancel'))
								 ->draw();
			
				$status = '';

				if( $stat == 'posted' )
				{
					$status = '<span class="label label-success">TRANSFERRED</span>';
				}
				else if( $stat == 'open' || $stat == 'request')
				{
					$status = '<span class="label label-warning">PENDING</span>';
					$disabled_edit = 0;
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
			else
			{
				// $not_open	+= 1;
				// $transfer_out .= '<tr>';
				// $transfer_out .= '<td colspan = "6" class="text-center">No Records Found</td>';
				// $transfer_out .= '</tr>';
			}
			
			if( $stat == 'released' )//|| $stat == 'received'
			{
				$dropdown = $this->ui->loadElement('check_task')
								 ->addView()
								 ->addEdit(($stat != 'posted'))
								 ->addOtherTask('Receive', 'save',($stat == 'released'))
								 ->addDelete($stat != 'posted')
								 ->setValue($stocktransferno)
								 ->setLabels(array('delete'=>'Cancel'))
								 ->draw();
				$status = '';

				if( $stat == 'posted' )//|| $stat == 'received'
				{
					$status = '<span class="label label-success">TRANSFERRED</span>';
				}
				else if( $stat == 'open' || $stat == 'released')
				{
					$status = '<span class="label label-info">RELEASED</span>';
					$disabled_edit = 0;
				}

				$transfer_in .= '<tr>';
				$transfer_in .= '<td>' . $dropdown . '</td>';
				$transfer_in .= '<td>' . $stocktransferno. '</td>';
				$transfer_in .= '<td>' . $destination. '</td>';
				$transfer_in .= '<td>' . $source. '</td>';
				$transfer_in .= '<td>' . $transactiondate . '</td>';
				$transfer_in .= '<td>' . $status. '</td>';
				$transfer_in .= '</tr>';
			}
			else
			{
				// $not_released	+= 1;
				// $transfer_in .= '<tr>';
				// $transfer_in .= '<td colspan = "6" class="text-center">No Records Found</td>';
				// $transfer_in .= '</tr>';
			}
		} 
		
		if ( $transfer_out == "" ){
			$transfer_out 	.=	'<tr><td colspan="6" class="text-center">No Records Found</td></tr>';
		}
		if( $transfer_in == "" ){
			$transfer_in 	.=	'<tr><td colspan="6" class="text-center">No Records Found</td></tr>';
		}

		$pagination->out = $transfer_out;
		$pagination->in  = $transfer_in;
		return $pagination;

	}

	private function get_details()
	{
		$itemcode 	= $this->input->post('itemcode');
		$warehouse 	= $this->input->post('warehouse');

		$result 	= $this->stock_transfer->retrieveItemDetails($itemcode, $warehouse);
		if(!$result)
			$var = (object) array("notfound"=>true); 
		
		return $result;
	}

	private function set_release(){
		$stocktransferno 	= $this->input->post('transaction_no');

		$data['stat'] 	=	'released';
		$result 	= $this->stock_transfer->updateStatus($data, 'stock_transfer', $stocktransferno);
		// var_dump($result);
		if( $result )
		{
			$msg = "success";
			$this->logs->saveActivity("Released Stock Transfer [$stocktransferno] ");
			if ( $this->inventory_model ) {
				$this->inventory_model->generateBalanceTable();
			}
		}
		else
		{
			$msg = $result;
		}

		return $dataArray = array("msg" => $msg);
	}

	private function set_received(){
		$stocktransferno 	= $this->input->post('transaction_no');

		$data['stat'] 	=	'received';
		$result 	= $this->stock_transfer->updateStatus($data, 'stock_transfer', $stocktransferno);
		// var_dump($result);
		if( $result )
		{
			$msg = "success";
			$this->logs->saveActivity("Received Stock Transfer [$stocktransferno] ");
			if ( $this->inventory_model ) {
				$this->inventory_model->generateBalanceTable();
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
  
  		$fields2 = array("itemcode",
					    "itemname"=>"detailparticular",
						"sitesource"=>"source",
						"sitedestination"=>"destination",
  						"ohqty",
						"qtytoapply",
						"uom",
  						"price",
  						"amount",
		  		);

		$data = $this->input->post($fields1);
		$data2 = $this->input->post($fields2);

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
  
  		$fields2 = array("itemcode",
					    "itemname"=>"detailparticular",
						"sitesource"=>"source",
						"sitedestination"=>"destination",
  						"ohqty",
						"qtytoapply",
						"uom",
  						"price",
  						"amount",
		  		);

		//$data = $this->input->post();
		$data = $this->input->post($fields1);
		$data2 = $this->input->post($fields2);

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

}
?>