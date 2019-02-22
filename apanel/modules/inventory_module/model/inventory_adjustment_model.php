<?php
class inventory_adjustment_model extends wc_model {

	public function getInventoryAdjustmentList($itemcode, $brandcode, $warehouse, $sort) {
		$warehouse_cond = '';
		$condition 		= 'items.itemgroup="goods"';

		if ($warehouse && $warehouse != 'none') {
			$warehouse_cond .= (empty($condition) ? '' : ' AND ') . " inv.warehouse = '$warehouse'";
			
			if ($itemcode && $itemcode != 'none') {
				$condition .= (empty($condition) ? '' : ' AND ') . " items.itemcode = '$itemcode'";
			}
			if ($brandcode && $brandcode != 'none') {
				$condition .= (empty($condition) ? '' : ' AND ') . " b.brandcode = '$brandcode'";
			}
		}

		$result = $this->db->setTable("items as items")
							->leftJoin('brands as b ON b.brandcode = items.brandcode AND b.companycode = items.companycode')
							->leftJoin('invfile as inv ON inv.itemcode = items.itemcode '.$warehouse_cond)  
                            ->leftJoin('invdtlfile as invdtlfile ON invdtlfile.itemcode = inv.itemcode AND invdtlfile.warehouse = inv.warehouse') 
							->setFields("items.itemcode as itemcode, b.brandname, inv.onhandQty as OHQty, inv.warehouse as warehouse , inv.allocatedQty as AllocQty, inv.availableQty as AvailQty, inv.orderedQty as OrderQty,items.itemname as itemname, items.item_ident_flag")
							->setWhere($condition)
							->setOrderBy($sort)
							->runPagination();
		// echo $this->db->getQuery();
		return $result;
	}	

	public function getValue($table, $cols = array(), $cond, $orderby = "",$limit = "" , $bool = "")
	{
		$result = $this->db->setTable($table)
					->setFields($cols)
					->setWhere($cond)
					->setOrderBy($orderby)
					->setLimit($limit)
					->runSelect($bool)
					->getResult();
		//echo $this->db->getQuery();
		return $result;
	}

	public function getImportList()
	{
		return	$this->db->setTable('items')
						->setFields('items.itemcode as itemcode, items.itemname as name, w.description as warehouse')
						->leftJoin("warehouse w ON w.companycode = items.companycode")
						->leftJoin("invfile inv ON inv.itemcode = items.itemcode AND w.warehousecode = inv.warehouse AND w.companycode = inv.companycode")
						//->setWhere("inv.onhandQty IS NULL")
						->setWhere("items.itemgroup = 'goods'")
						->setOrderBy('items.itemcode')
						->runSelect()
						->getResult();
	}

	public function getImportSerialList($itemcode, $warehouse) {
		$result	=	$this->db->setTable('items')
							->setFields('items.itemcode as itemcode, items.itemname as name, w.description as warehouse, inv.onhandQty qty, items.item_ident_flag')
							->leftJoin("warehouse w ON w.companycode = items.companycode")
							->leftJoin("invfile inv ON inv.itemcode = items.itemcode AND w.warehousecode = inv.warehouse AND w.companycode = inv.companycode")
							->setWhere("items.itemgroup = 'goods' AND items.itemcode = '$itemcode' AND inv.warehouse = '$warehouse'")
							->setOrderBy('items.itemcode')
							->runSelect()
							->getResult();
		// echo $this->db->getQuery();
		return $result;
	}

	public function getLoggedInUsers($curr_user)
	{
		return $this->db->setTable('wc_users')
						->setFields("CONCAT(firstname,' ',lastname) as name")
						->setWhere("checktime > NOW() AND username != '$curr_user'")
						->setOrderBy('firstname, lastname')
						->runSelect()
						->getResult();		
	}

	public function check_if_exists($column, $table, $condition)
	{
		$result= $this->db->setTable($table)
						->setFields("COUNT(".$column.") count")
						->setWhere($condition)
						->runSelect()
						->getResult();

		return $result;
	}

	private function generateSearch($search, $array) 
	{
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

	public function update_inventory($data, $generatedvoucher)
	{
		//print_r($data);
		$mainInvTable 		= "inventoryadjustments_header";
		$dtlInvTable 		= "inventoryadjustments";
		$serializedtable 	= "items_serialized";

		//$this->seq 			= new seqcontrol();
		//$generatedvoucher	= $this->seq->getValue('ADJ'); 

		$action 			= (isset($data['action']) && (!empty($data['action']))) ? htmlentities(addslashes(trim($data['action']))) : "";

		$adjustdate 		= (isset($data['adjustdate']) && (!empty($data['adjustdate']))) ? htmlentities(addslashes(trim($data['adjustdate']))) : "";
		
		$remarks 			= (isset($data['remarks']) && (!empty($data['remarks']))) ? htmlentities(addslashes(trim($data['remarks']))) : "";

		$itemcode 			= (isset($data['itemcode']) && (!empty($data['itemcode']))) ? htmlentities(addslashes(trim($data['itemcode']))) : "";

		$itemname 	 		= (isset($data['itemname']) && (!empty($data['itemname']))) ? htmlentities(addslashes(trim($data['itemname']))) : "";

		$issueqty 			= (isset($data['issueqty']) && (!empty($data['issueqty']))) ? htmlentities(addslashes(trim($data['issueqty']))) : 0;
		$issueqty_serial 	= (isset($data['issueqty_serial']) && (!empty($data['issueqty_serial']))) ? htmlentities(addslashes(trim($data['issueqty_serial']))) : 0;
		$issueqty 			= str_replace(',','',$issueqty);
		$issueqty_serial 	= str_replace(',','',$issueqty_serial);

		$warehouse 			= (isset($data['h_warehouse']) && (!empty($data['h_warehouse']))) ? htmlentities(addslashes(trim($data['h_warehouse']))) : "";

		$serials 			= (isset($data['serials']) && !empty($data['serials']) && $data['serials'] != "[]") ? $data['serials'] : "";
		$serials_manual		= (isset($data['serials_manual']) && (!empty($data['serials_manual']))) ? stripslashes($data['serials_manual']) : "";
		$serials_manual 	= json_decode($serials_manual);
	
		/**FORMAT DATES**/
		$transactiondate	= date("Y-m-d",strtotime($adjustdate));
		$period				= date("n",strtotime($transactiondate));
		$fiscalyear			= date("Y",strtotime($transactiondate));
	
		$increase 			= $decrease 		= 0;
		
		$retrieval 			=  	$this->db->setTable("invfile")
													->setFields("onhandQty")
													->setWhere(" itemcode = '$itemcode' ")
													->runSelect()
													->getRow();
												// echo $action;
		if( $action == 'plus' ){
			$increase 		= (empty($serials) && empty($serials_manual)) ? $issueqty : $issueqty_serial;
			$decrease 		= 0;
		}
		else if( $action == 'minus' )
		{
			$qty 			= (empty($serials) && empty($serials_manual)) ? $issueqty : $issueqty_serial;
			$decrease 		= -$qty;
			$increase 		= 0;
		}

		$ret_onhand 		= isset($retrieval->onhandQty) 	?  $retrieval->onhandQty 	: 	0;
		$amt_value 			= ( $increase != 0 ) 	?	$ret_onhand +	$increase	:	$ret_onhand + $decrease;

		$header['voucherno']  			= $generatedvoucher;
		$header['transactiondate'] 		= $transactiondate;
		$header['itemcode'] 			= $itemcode;
		$header['onhand'] 			 	= $amt_value;
		$header['warehouse'] 			= $warehouse;
		$header['remarks'] 				= $remarks;
		$header['transtype'] 			= "ADJ";
		$header['fiscalyear'] 			= $fiscalyear;
		$header['period'] 				= $period;
		$header['stat'] 				= 'open';

		$this->db->setTable($mainInvTable)
				->setValues($header);
		
		$insertResult = $this->db->runInsert();
		
		if( $insertResult )
		{
			$ret_aveprice 	=	$this->getValue('price_average', array('price_average'), " itemcode = '$itemcode' ", "linenum DESC", 1);
			
			$details['voucherno'] 		= $generatedvoucher;
			$details['itemcode'] 		= $itemcode;
			$details['onhand'] 			= $ret_onhand;
			$details['increase'] 		= $increase;
			$details['decrease'] 		= $decrease;
			$details['unitprice'] 		= isset($ret_aveprice[0]->price_average) 	?	$ret_aveprice[0]->price_average 	: 	0;
			$details['warehouse'] 		= $warehouse;
			$details['serialids'] 		= $serials;

			$this->db->setTable($dtlInvTable)
				->setValues($details);
		
			$insertResult = $this->db->runInsert();

			if($insertResult && $serials!=""){
				// var_dump();
				$exploded_serials 		= json_decode(stripslashes($serials));
				// var_dump($exploded_serials);
				$update_srl_inv['stat'] = 'Not Available';

				$insertResult = $this->db->setTable('items_serialized')
										 ->setValues($update_srl_inv)
										 ->setWhere("id IN ('".implode('\',\'',$exploded_serials)."')")
										 ->runUpdate();

										//  echo $this->db->getQuery();
			}
			// var_dump($decoded_serials);
			$listid 	=	[];
			if($insertResult && !empty($serials_manual)){
				foreach($serials_manual as $key=>$row){
					$serial 	=	$row->serialno;
					$engine 	=	$row->engineno;
					$chassis 	=	$row->chassisno;

					$serial_data['warehousecode'] 	=	$warehouse;
					$serial_data['voucherno'] 		=	$generatedvoucher;
					$serial_data['source_no'] 		=	$generatedvoucher;
					$serial_data['itemcode'] 		=	$itemcode;
					$serial_data['linenum'] 		=	$key + 1;
					$serial_data['rowno'] 			=	$key + 1;
					$serial_data['serialno'] 		=	$serial;
					$serial_data['engineno'] 		=	$engine;
					$serial_data['chassisno'] 		=	$chassis;
					$serial_data['stat'] 			=	"Available";

					$item_serialized[] 	=	$serial_data;
				}

				$this->db->setTable($serializedtable)
						->setValues($item_serialized);
				
				$insertResult = $this->db->runInsert();
			}
					
			return $insertResult;
		}
	}

	public function update_locktime($username)
	{
		$curr_time 			=	$this->date->datetimeDbFormat();
		$data['locktime'] 	=	date('Y-m-d H:i:s', strtotime('+5 minutes', strtotime($curr_time)) );

		return $this->db->setTable('wc_users')
						->setValues($data)
						->setWhere("username != '$username' ")
						->runUpdate();
	}

	public function getChartAccountList() {
		return $this->db->setTable('chartaccount c')
						->innerJoin('accountclass ac ON c.companycode = ac.companycode AND c.accountclasscode = ac.accountclasscode')
						->setFields('id ind, accountname val, accountclass parent')
						->setOrderBy('accountclass, accountname')
						->runSelect()
						->getResult();
	}

	public function get_code_list($itemcode) 
	{
		$item_inv 	=	$this->getValue('items',array('inventory_account', 'classid')," itemcode = '$itemcode' ","");
		$class_id 	=	isset($item_inv[0]->classid) 	? 	$item_inv[0]->classid 	:	"";
		$item_acct 	=	isset($item_inv[0]->inventory_account) 	? 	$item_inv[0]->inventory_account 	: 	0;
		$class_inv 	=	$this->getValue('itemclass',array('inventory_account')," id = '$class_id' ","");
		$account_id = 	isset($class_inv[0]->inventory_account) 	?	$class_inv[0]->inventory_account 	: 	$item_acct;
		
		return $this->db->setTable('chartaccount c')
						->innerJoin('accountclass ac ON c.companycode = ac.companycode AND c.accountclasscode = ac.accountclasscode')
						->setFields('id ind, accountname val, accountclass parent')
						->setWhere(" id != '$account_id' ")
						->setOrderBy('accountclass, accountname')
						->runSelect()
						->getResult();
	}

	public function save_journal_voucher($data)
	{	
		$generatedvoucher 	=	isset($data['voucher']) 			?	$data['voucher'] 			: 	"";
		$reference 			=	isset($data['adjustment_voucher']) 	?	$data['adjustment_voucher'] : 	"";
		$warehouse 			=	isset($data['warehouse']) 			?	$data['warehouse'] 			: 	"";
		$adjustdate 		=	isset($data['adjustdate']) 			?	$data['adjustdate'] 		: 	"";
		$remarks 			=	isset($data['remarks']) 			? 	$data['remarks'] 			: 	"";
		$itemcode 			= 	isset($data['itemcode']) 			? 	$data['itemcode'] 			: 	"";
		$itemname 			=	isset($data['itemname']) 			? 	$data['itemname']		 	: 	"";
		$quantity 			=	isset($data['issueqty']) 			?	$data['issueqty'] 			: 	0;
		$quantity_serial 	=	isset($data['issueqty_serial']) 	?	$data['issueqty_serial'] 	:	0;
		$selectedaccount  	=	isset($data['inventory_account']) 	? 	$data['inventory_account'] 	: 	"";
		$actualaccount  	=	isset($data['inv_acct']) 			? 	$data['inv_acct'] 			: 	"";
		
		$action  			=	isset($data['action']) 				? 	$data['action'] 			: 	"";

		$retrieved_price 	=	$this->getValue('items_price',array('itemprice')," itemcode = '$itemcode' ");
		$retrieved_aveprice =	$this->getValue('price_average', array('price_average'), " itemcode = '$itemcode' ", "linenum DESC", 1);
		
		$itemprice 			=	0.00;

		if( isset($retrieved_aveprice[0]->price_average) )
		{
			$itemprice 		=	$retrieved_aveprice[0]->price_average;
		}
		else if( !isset($retrieved_aveprice[0]->price_average) && isset( $retrieved_price[0]->itemprice ) )
		{
			$itemprice 		=	$retrieved_price[0]->itemprice;
		}

		$actual_quantity 	=	0;
		if($quantity!=0 && $quantity_serial==0) {
			$actual_quantity= 	$quantity;
		} else {
			$actual_quantity=	$quantity_serial;
		}

		$amount 			=	$actual_quantity * $itemprice;

		/**FORMAT DATES**/
		$transactiondate	= 	$this->date->datetimeDbFormat($adjustdate);
		$documentdate 		=	$this->date->datetimeDbFormat($adjustdate);
		$period				= 	date("n",strtotime($documentdate));
		$fiscalyear			= 	date("Y",strtotime($documentdate));

		$result 			=	0;

		//Header Details
		$header['voucherno'] 		=	$generatedvoucher;
		$header['transtype'] 		=	"JV";
		$header['stat'] 			=	"posted";
		$header['transactiondate'] 	=	$transactiondate;
		$header['fiscalyear'] 		=	$fiscalyear;
		$header['period'] 			= 	$period;
		// $header['documentdate'] 	=	$documentdate;
		$header['currencycode'] 	= 	"PHP";
		$header['exchangerate'] 	=	1;
		$header['amount'] 	 		=	$amount;
		$header['convertedamount'] 	=	$amount;
		$header['referenceno'] 		=	$reference;
		$header['sitecode'] 		= 	$warehouse;
		$header['remarks'] 			= 	$remarks;
		
		//Insert Header
		$result 	=	 $this->insertdata('journalvoucher',$header);

		if( $action == 'plus' )
		{
			if( $result )
			{
				$result 	=	$this->create_jvdetails_plus($data);
			}
		}
		else if ( $action == 'minus')
		{
			if( $result )
			{
				$result 	=	$this->create_jvdetails_minus($data);
			}
		}

		return $result;
	}

	public function create_jvdetails_plus($data)
	{
		$generatedvoucher 	=	isset($data['voucher']) 			?	$data['voucher'] 			: 	"";
		$reference 			=	isset($data['adjustment_voucher']) 	?	$data['adjustment_voucher'] : 	"";
		$warehouse 			=	isset($data['warehouse']) 			?	$data['warehouse'] 			: 	"";
		$adjustdate 		=	isset($data['adjustdate']) 			?	$data['adjustdate'] 		: 	"";
		$remarks 			=	isset($data['remarks']) 			? 	$data['remarks'] 			: 	"";
		$itemcode 			= 	isset($data['itemcode']) 			? 	$data['itemcode'] 			: 	"";
		$itemname 			=	isset($data['itemname']) 			? 	$data['itemname']		 	: 	"";
		$quantity 			=	isset($data['issueqty']) 			?	$data['issueqty'] 			: 	0;
		$quantity_serial 	=	isset($data['issueqty_serial']) 	?	$data['issueqty_serial'] 	:	0;
		$selectedaccount  	=	isset($data['inventory_account']) 	? 	$data['inventory_account'] 	: 	"";
		$actualaccount  	=	isset($data['inv_acct']) 			? 	$data['inv_acct'] 			: 	"";

		$retrieved_price 	=	$this->getValue('items_price',array('itemprice')," itemcode = '$itemcode' ");
		$retrieved_aveprice =	$this->getValue('price_average', array('price_average'), " itemcode = '$itemcode' ", "linenum DESC", 1);
		
		$itemprice 			=	0.00;

		if( isset($retrieved_aveprice[0]->price_average) )
		{
			$itemprice 		=	$retrieved_aveprice[0]->price_average;
		}
		else if( !isset($retrieved_aveprice[0]->price_average) && isset( $retrieved_price[0]->itemprice ) )
		{
			$itemprice 		=	$retrieved_price[0]->itemprice;
		}

		$actual_quantity 	=	0;
		if($quantity!=0 && $quantity_serial==0) {
			$actual_quantity= 	$quantity;
		} else {
			$actual_quantity=	$quantity_serial;
		}

		$amount 			=	$actual_quantity * $itemprice;


		/**FORMAT DATES**/
		$transactiondate	= 	$this->date->datetimeDbFormat($adjustdate);
		$documentdate 		=	$this->date->datetimeDbFormat($adjustdate);
		$period				= 	date("n",strtotime($documentdate));
		$fiscalyear			= 	date("Y",strtotime($documentdate));

		$result 			=	0;

		$details['voucherno'] 			=	$generatedvoucher;
		$details['transtype'] 			=	'JV';
		$details['linenum'] 			=	1;
		$details['accountcode'] 		= 	$actualaccount;
		$details['debit'] 				=  	$amount;
		$details['credit'] 				=	0;
		$details['exchangerate'] 		= 	1;
		$details['converteddebit'] 		= 	$amount;
		$details['convertedcredit'] 	= 	0;
		$details['detailparticulars'] 	= 	$itemname;
		$details['stat'] 				= 	"posted";

		$result 	=	 $this->insertdata('journaldetails',$details);

		if( $result )
		{
			$details['voucherno'] 			=	$generatedvoucher;
			$details['transtype'] 			=	'JV';
			$details['linenum'] 			=	1;
			$details['accountcode'] 		= 	$selectedaccount;
			$details['debit'] 				=  	0;
			$details['credit'] 				=	$amount;
			$details['exchangerate'] 		= 	1;
			$details['converteddebit'] 		= 	0;
			$details['convertedcredit'] 	= 	$amount;
			$details['detailparticulars'] 	= 	$itemname;
			$details['stat'] 				= 	"posted";

			$result 	=	 $this->insertdata('journaldetails',$details);
		}

		return $result;
	}

	public function create_jvdetails_minus($data)
	{
		$generatedvoucher 	=	isset($data['voucher']) 			?	$data['voucher'] 			: 	"";
		$reference 			=	isset($data['adjustment_voucher']) 	?	$data['adjustment_voucher'] : 	"";
		$warehouse 			=	isset($data['warehouse']) 			?	$data['warehouse'] 			: 	"";
		$adjustdate 		=	isset($data['adjustdate']) 			?	$data['adjustdate'] 		: 	"";
		$remarks 			=	isset($data['remarks']) 			? 	$data['remarks'] 			: 	"";
		$itemcode 			= 	isset($data['itemcode']) 			? 	$data['itemcode'] 			: 	"";
		$itemname 			=	isset($data['itemname']) 			? 	$data['itemname']		 	: 	"";
		$quantity 			=	isset($data['issueqty']) 			?	$data['issueqty'] 			: 	0;
		$quantity_serial 	=	isset($data['issueqty_serial']) 	?	$data['issueqty_serial'] 	:	0;
		$selectedaccount  	=	isset($data['inventory_account']) 	? 	$data['inventory_account'] 	: 	"";
		$actualaccount  	=	isset($data['inv_acct']) 			? 	$data['inv_acct'] 			: 	"";

		$retrieved_price 	=	$this->getValue('items_price',array('itemprice')," itemcode = '$itemcode' ");
		$retrieved_aveprice =	$this->getValue('price_average', array('price_average'), " itemcode = '$itemcode' ", "linenum DESC", 1);
		
		$itemprice 			=	0.00;

		if( isset($retrieved_aveprice[0]->price_average) )
		{
			$itemprice 		=	$retrieved_aveprice[0]->price_average;
		}
		else if( !isset($retrieved_aveprice[0]->price_average) && isset( $retrieved_price[0]->itemprice ) )
		{
			$itemprice 		=	$retrieved_price[0]->itemprice;
		}

		$actual_quantity 	=	0;
		if($quantity!=0 && $quantity_serial==0) {
			$actual_quantity= 	$quantity;
		} else {
			$actual_quantity=	$quantity_serial;
		}

		$amount 			=	$actual_quantity * $itemprice;

		/**FORMAT DATES**/
		$transactiondate	= 	$this->date->datetimeDbFormat($adjustdate);
		$documentdate 		=	$this->date->datetimeDbFormat($adjustdate);
		$period				= 	date("n",strtotime($documentdate));
		$fiscalyear			= 	date("Y",strtotime($documentdate));

		$result 			=	0;

		$details['voucherno'] 			=	$generatedvoucher;
		$details['transtype'] 			=	'JV';
		$details['linenum'] 			=	1;
		$details['accountcode'] 		= 	$actualaccount;
		$details['debit'] 				=  	0;
		$details['credit'] 				=	$amount;
		$details['exchangerate'] 		= 	1;
		$details['converteddebit'] 		= 	0;
		$details['convertedcredit'] 	= 	$amount;
		$details['detailparticulars'] 	= 	$itemname;
		$details['stat'] 				= 	"posted";

		$result 	=	 $this->insertdata('journaldetails',$details);

		if( $result )
		{
			$details['voucherno'] 			=	$generatedvoucher;
			$details['transtype'] 			=	'JV';
			$details['linenum'] 			=	2;
			$details['accountcode'] 		= 	$selectedaccount;
			$details['debit'] 				=  	$amount;
			$details['credit'] 				=	0;
			$details['exchangerate'] 		= 	1;
			$details['converteddebit'] 		= 	$amount;
			$details['convertedcredit'] 	= 	0;
			$details['detailparticulars'] 	= 	$itemname;
			$details['stat'] 				= 	"posted";
			
			$result 	=	 $this->insertdata('journaldetails',$details);
		}

		return $result;
	}

	public function insertData($table, $data)
	{
		$result 	=	$this->db->setTable($table)
				 		->setValues($data)
				 		->runInsert();
		// echo $result;

		return $result;
	}

	public function deleteBeginningJV($table){
		$result 	=	$this->db->setTable($table)
				 		->setWhere("source = 'beginning'")
						 ->runDelete();
		// echo $result;
		// echo $this->db->getQuery();

		return $result;
	}

	public function importbeginningbalance($data){
		$result = $this->db->setTable('inv_beg_balance')
				->setValuesFromPost($data)
				->runInsert();

		return $result;
	}

	public function truncatebeginningbalance(){
		$result = $this->db->setTable('inv_beg_balance')
							->setWhere("companycode = 'CID'")
							->runDelete(false);
		return $result;
	}

	public function importinitialserials($data){
		$result = $this->db->setTable('items_serialized')
							->setValuesFromPost($data)
							->runInsert();
		// echo $this->db->getQuery();
		return $result;
	}

	public function deleteimportedserials($warehouse, $itemcode) {
		$result = $this->db->setTable('items_serialized')
							->setWhere("warehousecode = '$warehouse' AND itemcode = '$itemcode'")
							->runDelete();
		// echo $this->db->getQuery();
		return $result;
	}

	public function generate_beg_jv($voucher, $jvvoucher)
	{
		$detail_info		= array();
		$result 			= 0;

		$header_fields 		= " inv.voucherno as voucherno, inv.importdate as transactiondate, SUM(inv.amount) as total";
		$condition 			= " inv.voucherno = '$voucher' ";
		
		$headerData 		= $this->db->setTable('inv_beg_balance as inv')
										->setFields($header_fields)
										->leftJoin('items item ON item.itemcode = inv.itemcode AND item.companycode = inv.companycode')
										->leftJoin('itemclass class ON class.id = item.classid AND item.companycode = class.companycode')
										->setGroupBy('inv.voucherno')
										->setWhere($condition)
										->runSelect()
										->getResult();
										//var_dump($headerData);

		$detail_fields 		= " inv.voucherno as voucherno, inv.importdate as transactiondate, inv.itemcode as itemcode, inv.amount as totalperitem, item.payable_account as itemapaccount,
								item.inventory_account as iteminvacct, class.payable_account as classpaaccount, class.inventory_account as classinvacct";
		$condition 			= " inv.voucherno = '$voucher' ";
		
		$detailData 		= $this->db->setTable('inv_beg_balance as inv')
										->setFields($detail_fields)
										->leftJoin('items item ON item.itemcode = inv.itemcode AND item.companycode = inv.companycode')
										->leftJoin('itemclass class ON class.id = item.classid AND item.companycode = class.companycode')
										->setWhere($condition)
										->runSelect()
										->getResult();

		if(!empty($detailData)){
			$result 	=	$this->deleteBeginningJV("journaldetails");
			if($result && !empty($headerData)) {
				$result 		=	$this->deleteBeginningJV("journalvoucher");
			}
		}

		if(!empty($headerData)){
			foreach($headerData as $headerIndex => $headerValue)
			{
				$voucherno 					= 	$headerValue->voucherno;
				$transactiondate 			= 	$headerValue->transactiondate;
				$amount 					= 	$headerValue->total;
			
				$transactiondate 			=	$this->date->datetimeDbFormat($transactiondate);
				$period						= 	date("n",strtotime($transactiondate));
				$fiscalyear					= 	date("Y",strtotime($transactiondate));

				$header['voucherno'] 		=	$jvvoucher;
				$header['transtype'] 		=	"JV";
				$header['stat'] 			=	"posted";
				$header['transactiondate'] 	=	$transactiondate;
				$header['fiscalyear'] 		=	$fiscalyear;
				$header['period'] 			= 	$period;
				$header['currencycode'] 	= 	"PHP";
				$header['exchangerate'] 	=	1;
				$header['amount'] 	 		=	$amount;
				$header['convertedamount'] 	=	$amount;
				$header['referenceno'] 		=	"Beginning Balance";
				$header['source'] 			=	'beginning';
				$header['remarks'] 			= 	'Imported Beginning Balance';
				
				if($result && !empty($header)){
					$result 	=	 $this->insertdata('journalvoucher',$header);
				}
			}
		}
		$header_total 	= 	0;

		if(!empty($detailData) && $result){
			foreach($detailData as $detailIndex => $detailValue)
			{
				$voucherno 		= $detailValue->voucherno;
				$itemcode 		= $detailValue->itemcode;
				$amount 		= $detailValue->totalperitem;

				$itemaccount 	= $detailValue->itemapaccount;
				$classaccount 	= $detailValue->classpaaccount;
				
				$iteminvacct 	= $detailValue->iteminvacct;
				$classinvacct 	= $detailValue->classinvacct;

				$account 		= ( $itemaccount > 0 )	? $itemaccount 	: 	$classaccount;
				$inventoryacct 	= ( $iteminvacct > 0 ) 	? $iteminvacct 	: 	$classinvacct;
		
				/**GROUP BALANCE ACCOUNTS**/
				if(!empty($account)){
					$account_info[$account]['amount'][] 		= $amount;
				}
				
				/**GROUP SALES ACCOUNTS**/
				if(!empty($inventoryacct)){
					$inv_info[$inventoryacct]['amount'][] 		= $amount;
				}

				$header_total 	+=	$amount;
			}

			/**
			* Accounts Payable Account
			*/
			$linenum			= 1;
			$generatedvoucher 	= $jvvoucher;

			if(!empty($account_info)){
				foreach($account_info as $account_index => $account_value)
				{
					$totalamount	= array_sum($account_value['amount']);

					$details['voucherno'] 			=	$generatedvoucher;
					$details['transtype'] 			=	'JV';
					$details['linenum'] 			=	$linenum;
					$details['accountcode'] 		= 	$account_index;
					$details['debit'] 				=  	0;
					$details['credit'] 				=	$totalamount;
					$details['exchangerate'] 		= 	1;
					$details['converteddebit'] 		= 	0;
					$details['convertedcredit'] 	= 	$totalamount;
					$details['detailparticulars'] 	= 	"";
					$details['stat'] 				= 	"posted";
					$details['source']				=	"beginning";

					$detailArray[]						= $details;			
					$linenum++;
				}
			}

			if(!empty($inv_info)){
				foreach($inv_info as $inv_index => $inv_value)
				{
					$totalamount	= array_sum($inv_value['amount']);

					$details['voucherno'] 			=	$generatedvoucher;
					$details['transtype'] 			=	'JV';
					$details['linenum'] 			=	$linenum;
					$details['accountcode'] 		= 	$inv_index;
					$details['debit'] 				=  	$totalamount;
					$details['credit'] 				=	0;
					$details['exchangerate'] 		= 	1;
					$details['converteddebit'] 		= 	$totalamount;
					$details['convertedcredit'] 	= 	0;
					$details['detailparticulars'] 	= 	"";
					$details['stat'] 				= 	"posted";
					$details['source']				=	"beginning";

					$detailArray[]						= $details;			
					$linenum++;
				}
			}

			if($result && !empty($detailArray)){
				$result	 = $this->db->setTable("journaldetails")
								->setValues($detailArray)
								->runInsert();		
			}
		}
		return $result;
	}

	public function getDisplayPermission(){
		// $result 	=	$this->db->setTable("invdtlfile dtl")
		// 						->leftJoin("inventorylogs logs ON logs.itemcode = dtl.itemcode AND logs.warehouse = dtl.warehouse AND logs.quantity = dtl.beginningQty")
		// 						->setFields("COUNT(*) count")
		// 						->setWhere("logs.activity != 'Beginning Balance'")
		// 						->runSelect()
		// 						->getRow();

		$ret_seq_cnt 	=	$this->db->setTable("wc_sequence_control")
								  ->setFields("COUNT(*) count")
								  ->setWhere("current > start AND prefix NOT IN ('BAL','JV','BUD','JOB','JOBIPO') ")
								  ->runSelect()
								  ->getRow();
								  // > 

		$ret_jrl_cnt 	=	$this->db->setTable('journalvoucher')
									->setFields("COUNT(*) count")
									->setWhere("source != 'beginning'")
									->runSelect()
									->getRow();

		
		$result =	1;
		if($ret_seq_cnt->count > 0 || $ret_jrl_cnt->count > 0){
			$result = 0;
		}

		return $result;
	}

	public function deleteBeginningLogs(){
		$result 	=	$this->db->setTable('inventorylogs')
								  ->setWhere("activity = 'Beginning Balance'")
								  ->runDelete();
		return $result;
	}

	public function getSerialList($itemcode, $warehouse, $search) {
		$condition = '';
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('serialno', 'engineno', 'chassisno'));
		}
		$result	= $this->db->setTable('items_serialized')
								->setFields(array('id', 'itemcode', 'serialno', 'engineno', 'chassisno', 'stat'))
								->setWhere("stat = 'Available' AND itemcode = '$itemcode' AND warehousecode = '$warehouse'" .$condition)
								->setOrderBy('voucherno, linenum, rowno')
								->runPagination();

								// echo $this->db->getQuery();
								
		return $result;
	}

	public function checkifexisting($itemcode, $fieldvalue, $fieldtype){
		$array 		= 	array();
		$condition 	=	"";

		if($fieldtype == 'serial') {
			$array 	=	array('COUNT(serialno) count');
			$condition 	=	"itemcode = '$itemcode' AND serialno = '$fieldvalue'";
		} else if($fieldtype == 'engine') {
			$array 	=	array('COUNT(engineno) count');
			$condition 	=	"itemcode = '$itemcode' AND engineno = '$fieldvalue'";
		} else {
			$array 	=	array('COUNT(chassisno) count');
			$condition 	=	"itemcode = '$itemcode' AND chassisno = '$fieldvalue'";
		}

		$result = $this->db->setTable('items_serialized')
							->setFields($array)
							->setWhere($condition)
							->runSelect()
							->getResult();

		return $result;
	}

	public function check_existing_serials($warehouse, $itemcode){
		// $result = $this->db->setTable('items_serialized')
		// 					->setFields(array("COUNT(*) count"))
		// 					->setWhere("warehousecode = '$warehouse' AND itemcode = '$itemcode'")
		// 					->runSelect()
		// 					->getRow();
		// return $result;

		$inner_query 	=	$this->db->setTable('items_serialized')
									->setFields(array("COUNT(*) count", "itemcode", "warehousecode", "companycode"))
									->setGroupBy("itemcode, warehousecode")
									->buildSelect();

		$result 		=	$this->db->setTable("items i")
									 ->leftJoin("invfile inv ON inv.itemcode = i.itemcode AND inv.companycode = i.companycode")
									 ->leftJoin("($inner_query) isr ON isr.itemcode = inv.itemcode AND isr.warehousecode = inv.warehouse AND isr.companycode = inv.companycode")
									 ->setFields(array("IF(inv.onhandQty>IFNULL(isr.count,0), 'show', 'hide') display", "i.itemcode"))
									 ->setWhere("inv.warehouse = '$warehouse' AND inv.itemcode = '$itemcode'")
									 ->runSelect()
									 ->getRow();
									//  echo $this->db->getQuery();
		return $result;
	}

	public function check_duplicate($code, $table, $cond) {
		$result = $this->db->setTable($table)
						->setFields('COUNT(*) count')
						->setWhere($cond)
						->runSelect()
						->getResult();
		return $result;
	}
	
	public function getItemDropdownList($search="") {
		$condition = " stat = 'active' AND itemgroup = 'goods'";
		if ($search) {
			$condition .= " AND itemcode = '$search'";
		}
		return $this->db->setTable('items')
						->setFields('itemcode ind, CONCAT(itemcode," - ",itemname) val')
						->setWhere($condition)
						->runSelect()
						->getResult();
	}
}
