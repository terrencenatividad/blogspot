<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->adjustment		= new inventory_adjustment_model();
		$this->item_model		= new item_model();
		$this->session			= new session();
		$this->log 				= new log();
		$this->seq 				= new seqcontrol();
		$this->data 			= array();
		$this->name 			= NAME;
		$this->view->header_active = 'inventory/adjustment/';

		$this->inventory_model  = $this->checkOutModel('inventory_module/inventory_model');
	}

	public function listing() {
		$this->view->title 	= 'Inventory Adjustment List';
		$datetime 			= date("Y-m-d H:i:s");
		$adjustmentdate 	= date('M d, Y');
		$transactiondate 	= date('M d, Y');
		
		$data['voucherno'] 			= "- auto generated -";
 		$data['adjustmentdate'] 	= $adjustmentdate;
		$data['importdate'] 		= $transactiondate;
		
		$display 					= $this->adjustment->getDisplayPermission();
		// $transactions_count 		= isset($transactions->count) 	? 	$transactions->count 	:	0;
		$data['display_import_btn'] = $display;

		$data['ui'] 				= $this->ui;
		$data['chart_account_list'] = $this->adjustment->getChartAccountList();
		$data['item_list'] 			= $this->item_model->getItemDropdownList();
		$w_entry_data          		= array("warehousecode ind","description val");
		$data["warehouses"] 		= $this->adjustment->getValue("warehouse", $w_entry_data,"stat = 'active'","warehousecode");

		$this->view->load('inventory_adjustment/inventory_adjustment_list', $data);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function create_jv()
	{
		$data 		= 	$this->input->post();
		$itemcode 	=	$this->input->post('itemcode');
		$action 	=	$this->input->post('action');

		$item_acct 	= 	$this->adjustment->getValue("items", array("inventory_account","classid")," itemcode = '$itemcode'");
		$classid 	= 	isset($item_acct[0]->classid) 	?	$item_acct[0]->classid 	: 	0;
	
		$class_acct = 	$this->adjustment->getValue("itemclass", array("inventory_account")," id = '$classid'");
		$account 	= 	isset($class_acct[0]->inventory_account) 	? 	$class_acct[0]->inventory_account 	: 	$item_acct[0]->inventory_account;

		$result 	= 	0;
		$voucher	= 	$this->seq->getValue('JV');

		$data['voucher'] 	=	$voucher;
		$data['inv_acct'] 	=	$account;

		$adj_voucher= 	$this->input->post('adjustment_voucher'); 	

		$result 	=	$this->adjustment->save_journal_voucher($data);

		if( $result )
		{
			$msg = "success";
			$this->log->saveActivity("Created Journal Voucher [$voucher] for Adjustment[$adj_voucher] ");
		}
		else
		{
			$msg = $result;
		}

		return $dataArray = array("msg" => $msg);	
	}

	private function update_inventory()
	{
		$data 		= 	$this->input->post();

		$itemcode 	=	$this->input->post('itemcode');
		$action 	=	$this->input->post('action');
		$qty 		=	$this->input->post('issueqty');
		$value 		=	"";

		$voucher 	= 	$this->seq->getValue('ADJ'); 

		if ( $action == 'plus' )
		{
			$value 	=	'+ '.$qty;
		}
		else if( $action == 'minus' )
		{
			$value 	=	'- '.$qty;
		}

		$result 	=	$this->adjustment->update_inventory($data, $voucher);
		
		if( $result )
		{
			$msg = "success";
			$this->log->saveActivity("Adjusted [$itemcode] with $value ");
			if ( $this->inventory_model ) {
				$this->inventory_model->setReference($voucher)
										->setDetails($this->name)
										->generateBalanceTable();
			}
		}
		else
		{
			$msg = $result;
		}

		return $dataArray = array("msg" => $msg,"voucher" => $voucher);
	}

	private function update_locktime(){
		$curr_user 	=	USERNAME;
		$result 	=	$this->adjustment->update_locktime($curr_user);

		if( $result )
		{
			$msg = "success";
			$this->log->saveActivity("Current User [$curr_user]. Locked Other Users for Adjustment.");
		}
		else
		{
			$msg = $result;
		}

		return $dataArray = array("msg" => $msg);
	}

	private function retrieve_users(){
		$curr_user 	=	USERNAME;
		$temp 		=	array();

		$result 	=	$this->adjustment->getLoggedInUsers($curr_user);
		
		foreach ($result as $key => $row) {
			$temp[] 	=	$row->name."<br>";
		}

		$lists		= implode(' ', $temp);

		return $dataArray = array("user_lists" => $lists);
	}
	
	private function ajax_list() {
		$data = $this->input->post(array('itemcode','warehouse', 'daterangefilter','sort'));
		$itemcode 	= $data['itemcode'];
		$warehouse 	= $data['warehouse'];
		$sort 		= $data['sort'];

		$list = $this->adjustment->getInventoryAdjustmentList($itemcode,$warehouse, $sort);
		$table = '';

		if (empty($list->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		if ($list->result) 
		{
			foreach ($list->result as $key => $row) {

				$itemcode 		=	trim($row->itemcode);
				$itemname 		=	trim($row->itemname);
				$warehouse 		=	trim($row->warehouse);
				$quantity 		= 	isset($row->OHQty) 		?	$row->OHQty 	: 	number_format(0,2);
				$allocated 		=	isset($row->AllocQty) 	?	$row->AllocQty 	: 	number_format(0,2);
				$ordered 		=  	isset($row->OrderQty) 	? 	$row->OrderQty 	: 	number_format(0,2);
				$available 		=  	isset($row->AvailQty) 	? 	$row->AvailQty 	: 	number_format(0,2);

				$table .= '<tr>';
				$table .= '<td>' . $itemcode . '</td>';
				$table .= '<td>' . $itemname . '</td>' ;
				$table .= '<td>' . $quantity . '</td>';
				$table .= '<td>' . $allocated . '</td>';
				$table .= '<td>' . $ordered . '</td>';
				$table .= '<td>' . $available . '</td>';
				$table .= '<td>
								<button type = "button" id="plus" class = "btn btn-danger" onClick = "adjustment(\''.$itemcode.'\',\''.$itemname.'\', \''.$quantity.'\', \'plus\');" ><i class="fa fa-plus"></i></button>
								<button type = "button" id="plus" class = "btn btn-danger" onClick = "adjustment(\''.$itemcode.'\',\''.$itemname.'\', \''.$quantity.'\', \'minus\');"><i class="fa fa-minus"></i></button>
							</td>'; 
				$table .= '</tr>';
			}
		}

		$list->table = $table;
		return $list;
	}

	public function get_import($date){
		
		header('Content-type: application/csv');
		$header = array('Item Code','Item Name','Warehouse','Quantity','Unit Price');

		$return = '';
		$return .= '"Date","'.$date.'"';
		$return .= "\n\n";
		$return .= '"' . implode('","',$header) . '"';
		$return .= "\n";
		//$return .= '"PEN_006","WH_01","3","Accounts Payable - Non-Trade"';
		
		$lists 	=	$this->adjustment->getImportList();	

		foreach($lists as $key){
			$return .= '"'.$key->itemcode.'","'.$key->name.'","'.$key->warehouse.'","0","0.00"';
			$return .= "\n";
		}

		echo $return;
	}

	private function save_import(){
		$file		= fopen($_FILES['file']['tmp_name'],'r') or exit ("File Unable to upload") ;

		$filedir	= $_FILES["file"]["tmp_name"];

		$file_types = array( "text/x-csv","text/tsv","text/comma-separated-values", "text/csv", "application/csv", "application/excel", "application/vnd.ms-excel", "application/vnd.msexcel", "text/anytext");

		$errmsg 	=	array();
		$proceed 	=	false;

		/**VALIDATE FILE IF CORRUPT**/
		if(!empty($_FILES['file']['error'])){
			$errmsg[] = "File being uploaded is corrupted.<br/>";
		}

		/**VALIDATE FILE TYPE**/
		if(!in_array($_FILES['file']['type'],$file_types)){
			$errmsg[]= "Invalid file type, file must be .csv.<br/>";
		}
		
		$headerArr = array('Item Code','Item Name','Warehouse','Quantity','Unit Price');
		$first_line 	=	"Date";

		$importdate =	"";

		if( empty($errmsg) )  {
			$x = array_map('str_getcsv', file($_FILES['file']['tmp_name']));

			for ($n = 0; $n < count($x); $n++) {
				if($n==0){
					$header = $x[$n];

					for($y=0; $y < count($header); $y++){
						if( $header[$y] != ""){
							$importdate = $header[1];

							$error 		= ($header[0] != "Date") ? "error" : "";
							$errmsg[]	= (!is_null($error) && $error != "" ) ? "Invalid template. Please download the template from the system first.<br/>" : "";
			
							$error 		= (empty($header[1])) ? "error" : "";
							$errmsg[]	= (!is_null($error) && $error != "" ) ? "Please Input a date.<br/>" : "";
							
							$errmsg		= array_filter($errmsg);
						}
					}
				}
				
				// echo var_dump($errmsg);
				if($n==2 && empty($errmsg)){
					$layout = count($headerArr);
					$template = count($x);
					$header = $x[$n];

					for ($m=0; $m< $layout; $m++){
						$template_header = $header[$m];

						$error 	= (empty($template_header) && !in_array($template_header,$headerArr)) ? "error" : "";
					}	
					$errmsg[]	= (!empty($error) ) ? "Invalid template. Please download the template from the system first.<br/>" : "";
					
					$errmsg		= array_filter($errmsg);

				}

				if ( $n > 2 ) {
					$z[] = $x[$n];
				}
			}
			
			$line 				=	4;
			$warehousecode 		= 	"";
			$accountcode 		=	"";
			$pair_list 			= 	array();
			$post 				=	array();
			$warning 			=	array();

			$itemcode_ 			= 	array();
			$warehouse_ 		=	array();
			$quantity_ 			=	array();
			$price_ 			=	array();
			$amount_ 			=	array();

			$total_amount 		=	0;

			if( empty($errmsg)  && !empty($z) ){
				foreach ($z as $b)  {
					if ( ! empty($b)) {	
						$itemcode 	   		= $b[0];
						$warehouse 	        = $b[2];
						$quantity        	= $b[3];
						$unitprice 			= $b[4];
						//$account 	        = $b[5];
	
						// $item_account 		= $this->adjustment->getValue('items',array('classid','inventory_account')," itemcode = '$itemcode'");
						// $classid 			= $item_account[0]->classid;
						// $itemclass_account 	= $this->adjustment->getValue('itemclass',array('inventory_account')," id = '$classid'");
	
						// $account 			= ($item_account[0]->inventory_account != 0) 	? 	$item_account[0]->inventory_account 	: 	$itemclass_account[0]->inventory_account;
						
						// Check if item Code exists
						$exists 			= $this->adjustment->check_if_exists('itemcode','items'," itemcode = '$itemcode' ");
						$itemcode_count 	= $exists[0]->count;
			
						if( $itemcode_count <= 0 ){
							$errmsg[]	= "Item Code [<strong>$itemcode</strong>] on row $line does not exists.<br/>";
							$errmsg		= array_filter($errmsg);
						}
	
						// Check if Warehouse Exists
						$exists = $this->adjustment->check_if_exists('warehousecode','warehouse'," description = '$warehouse' ");
						$warehouse_count 	= $exists[0]->count;
	
						if( $warehouse_count <= 0 )
						{
							$errmsg[]	= "Warehouse [<strong>$warehouse</strong>] on row $line does not exists.<br/>";
							$errmsg		= array_filter($errmsg);
						} else {
							$retrieve 		=	$this->adjustment->getValue('warehouse', array('warehousecode'), " description = '$warehouse' ");
							$warehousecode 	=	$retrieve[0]->warehousecode;
						}
	
						// Check if Quantity is a number
						if(!is_numeric($quantity)) {
							$errmsg[] 	= "Quantity [ <strong>$quantity</strong> ] on row $line is not a valid amount.<br/>";
							$errmsg		= array_filter($errmsg);
						}
	
						// if($quantity <= 0)
						// {
						// 	$errmsg[] 	= "Quantity on row $line is [ <strong>$quantity</strong> ]. Please input a quantity greater than 0.<br/>";
						// 	$errmsg		= array_filter($errmsg);
						// } 

						if($quantity > 0) {
							if($unitprice <= 0){
								$errmsg[] 	= "Price on row $line is [ <strong>$unitprice</strong> ]. Please input a Price greater than 0.<br/>";
								$errmsg		= array_filter($errmsg);
							}
						}
	
						// Check if Price is a number
						if(!is_numeric($unitprice)) {
							$errmsg[] 	= "Price [ <strong>$unitprice</strong> ] on row $line is not a valid amount.<br/>";
							$errmsg		= array_filter($errmsg);
						}

						if($unitprice > 0) {
							if($quantity <= 0){
								$errmsg[] 	= "Quantity on row $line is [ <strong>$quantity</strong> ]. Please input a Quantity greater than 0.<br/>";
								$errmsg		= array_filter($errmsg);
							}
						}
	
						$amount 		=	$unitprice * $quantity;
						$total_amount 	+=	$amount;
						// Get Total per Account
						// if( !isset($account_list[$account]) ){
						// 	$account_list[$account] 	=	0;
						// } else {
						// 	$account_list[$account] 	+=	$amount;
						// }
	
						// Check if Item Code Exists in the said warehouse
						// $exists 				= $this->adjustment->getValue('invfile', array('onhandQty', "COUNT('itemcode') as count"), " itemcode = '$itemcode' AND warehouse = '$warehousecode' ");
						// $item_warehouse_count 	= $exists[0]->count;
						// $inv_onhand 			= $exists[0]->onhandQty;
	
						// Check if Itemcode & Warehouse Pair already have onhand quantity
						// if( $item_warehouse_count > 0 && $inv_onhand > 0 )
						// {
						// 	$warning[] 	= "The Item Code [<strong>$itemcode</strong>] already have existing transactions. Current On Hand Quantity is retained.";
						// 	$warning	= array_filter($warning);
						// }
	
						// Insert the itemcode - warehouse pair into an array ( for validation )
						$concat_itemnloc 	 		= $itemcode.'-'.$warehouse;
					
						if( !in_array($concat_itemnloc, $pair_list) ){
							$pair_list[] 	=	$concat_itemnloc;
						} else {
							$errmsg[]	= "Itemcode [<strong>$itemcode</strong>] and Warehouse [<strong>$warehouse</strong>] on row $line has a duplicate within the document.<br/>";
							$errmsg		= array_filter($errmsg);
						}
	
						if( $quantity > 0 )
						{
							$itemcode_[] 			= $itemcode;
							$warehouse_[] 			= $warehousecode;
							$quantity_[] 			= $quantity;
							$price_[] 				= $unitprice;
							$amount_[] 				= $amount;
							  //$account_[] 			= $accountid;
						}
						
						$line++;
					}
				}
			
				if( empty($errmsg) ){
					$voucherno 		=	$this->seq->getValue('BAL');
					$importdate 	=	$this->date->dateDBFormat($importdate);	
					$post = array(
						'voucherno' 	=> $voucherno,
						'importdate' 	=> $importdate,
						'itemcode'		=> $itemcode_,
						'warehouse'		=> $warehouse_,
						'quantity'		=> $quantity_,
						'unitprice' 	=> $price_,
						'amount' 		=> $amount_
					);

					$clear_table 		= $this->adjustment->truncatebeginningbalance();
					if($clear_table){
						$proceed  			= $this->adjustment->importbeginningbalance($post);
					}
					
					if( $proceed ){
						$this->log->saveActivity("Imported Beginning Balance[$voucherno].");
						
						if( $total_amount > 0 ){
							$jv 			= 	$this->seq->getValue('JV');
							$result 		=	$this->adjustment->generate_beg_jv($voucherno,$jv);

							if( $result ){
								$this->log->saveActivity("Created Journal Voucher [$jv] for Beginning Balance[$voucherno].");
							}
						}

						if ( $this->inventory_model ) {
							$this->inventory_model->setReference($voucherno)
													->setDetails($this->name)
													->generateBalanceTable();
						}
					}
				}
			}
		}
		$error_messages		= implode(' ', $errmsg);
		$warning_messages	= (!empty($warning_messages)) ? implode(' ', $warning) 	:	"";
		
		return array("proceed" => $proceed,"errmsg"=>$error_messages, "warning"=>$warning_messages);
	}

	private function get_code(){

		$itemcode 	=	$this->input->post('code');

		$coa_list 	=	$this->adjustment->get_code_list($itemcode);

		$list 		= "<option></option>";
		foreach($coa_list as $row)
		{
			$list 	.=	"<option value='".$row->ind."'>".$row->val."</option>";
		}
		
		$dataArray = array( "list" => $list );

		return $dataArray;
	}
}