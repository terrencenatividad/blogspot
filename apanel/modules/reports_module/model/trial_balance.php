<?php
class trial_balance extends wc_model {

	public function getPrevCarry($account,$date){
		$debit 		= 0;
		$credit 	= 0;
		$prevyear 	= date("Y",strtotime($date." -1 year"));
		
		$fetch_debit  = $this->getValue("balance_table",array("SUM(debit) as debit")," accountcode = '$account' AND YEAR(transactiondate) = $prevyear ");
		$debit        = $fetch_debit[0]->debit;
		$fetch_credit = $this->getValue("balance_table",array("SUM(credit) as credit")," accountcode = '$account' AND YEAR(transactiondate) = $prevyear ");
		$credit 	  = $fetch_credit[0]->credit;

		return ($debit > $credit) ? $debit - $credit : -($credit - $debit);
	}

	public function getBalanceCarry($account,$fromdate,$todate){
		$debit 		= 0;
		$credit 	= 0;
		$currentyear= date("Y",strtotime($fromdate));

		$fetch_debit  = $this->getValue("balance_table","SUM(debit) as debit"," accountcode = '$account' AND YEAR(transactiondate) = $currentyear AND transactiondate < '$fromdate'");
		$debit		  = $fetch_debit[0]->debit;
		$fetch_credit = $this->getValue("balance_table","SUM(credit) as credit"," accountcode = '$account' AND YEAR(transactiondate) = $currentyear AND transactiondate < '$fromdate'");
		$credit       = $fetch_credit[0]->credit;
		return ($debit > $credit) ? $debit - $credit : -($credit - $debit);
	}

	public function getCurrent($account,$fromdate,$todate){
		$debit 		= 0;
		$credit 	= 0;

		$fetch_debit  = $this->getValue("balance_table","SUM(debit) as debit"," accountcode = '$account' AND (transactiondate >= '$fromdate' AND transactiondate <= '$todate')");
		$debit        = $fetch_debit[0]->debit;
		$fetch_credit = $this->getValue("balance_table","SUM(credit) as credit"," accountcode = '$account' AND (transactiondate >= '$fromdate' AND transactiondate <= '$todate')");
		$credit       = $fetch_credit[0]->credit; 
		return ($debit > $credit) ? $debit - $credit : -($credit - $debit);
	}

	public function getAccountCodeBeg($beg_balance_query_table,$beg_balance_query_fields,$beg_balance_query_condition,$account,$field){
	
		$result = $this->db->setTable($beg_balance_query_table)
					->setFields($beg_balance_query_fields)
					->setWhere($beg_balance_query_condition." AND accountcode='".$account."'")
					->setGroupBy("gldetails.account ASC")
					->runSelect()
					->getResult();

		if(!empty($result)){
			for($i = 0; $i < count($result); $i++)
			{
				$transactiondate	= $result[0]->transactiondate;
			 	$debit				= $result[0]->debit;
			 	$credit				= $result[0]->credit;
				$throw =($field == 'debit') ? $debit : $credit;
		 		return array('transactiondate'=>$transactiondate,'throw'=>$throw);
			}
		}else{
		 	return 0;
		}
	}

	public function getValue($table, $cols = array(), $cond, $orderby = "", $addon = true,$limit = false)
	{
		 $this->db->setTable($table)
					->setFields($cols)
					->setWhere($cond)
					->setOrderBy($orderby);
					if($limit){
						$this->db->setLimit('1');
					}
		$result =   $this->db->runSelect($addon)
					->getResult();
					// echo $this->db->getQuery();
		return $result;
	}

	public function getTrialBalance($currentyear,$prevyear,$fstype="")
	{
		$fs_cond 	=	(!empty($fstype)) 	?	" AND chart.fspresentation = '$fstype'" 	:	"";
		$result = $this->db->setTable("chartaccount as chart")
						->setFields(array("chart.id as accountid","chart.segment5 as accountcode","chart.accountname as accountname"))
						->leftJoin("balance_table as bal ON bal.accountcode = chart.id")
						->setWhere("YEAR(bal.transactiondate) >= $prevyear 
		AND YEAR(bal.transactiondate) <= $currentyear $fs_cond")
						->setGroupBy("chart.segment5")
						->setOrderBy("chart.segment5 ASC")
						->runPagination();

		return $result;
	}

	public function retrieveCOAdetails($currentyear,$prevyear,$fstype="")
	{
		$fs_cond 	=	(!empty($fstype)) 	?	" AND chart.fspresentation = '$fstype'" 	:	"";
		
		$result = $this->db->setTable("chartaccount as chart")
						->setFields(array("chart.id as accountid","chart.segment5 as accountcode","chart.accountname as accountname"))
						->leftJoin("balance_table as bal ON bal.accountcode = chart.id")
						->setWhere("YEAR(bal.transactiondate) >= $prevyear AND YEAR(bal.transactiondate) <= $currentyear $fs_cond")
						->setGroupBy("chart.segment5")
						->setOrderBy("chart.segment5 ASC")
						->runSelect()
						->getResult();
						// echo $this->db->getQuery();
		return $result;
	}

	public function load_account_transactions($data){
		$daterangefilter	= isset($data['daterangefilter'])  ?  $data['daterangefilter']  : "";
		$acctfilter		    = isset($data['accountcode'])  ?  $data['accountcode']  : "";
		$items				= isset($data['items'])  ?  $data['items']  : "";
		$qtr				= isset($data['qtr'])  ? $data['qtr']  : 0;
		$sort				= isset($data['sort'])  ? $data['sort']  : "voucherno";
		$sortBy				= isset($data['sortBy'])  ?  $data['sortBy']  : "DESC";
		$dateArr 			= explode(" - ",$daterangefilter);
		
		$datefilter			= date("Y-m-d",strtotime($dateArr[1]));
		
		$year_1				= date("Y",strtotime($datefilter));
		$year_2				= date("Y",strtotime($datefilter." -1 year"));
		$year_3				= date("Y",strtotime($datefilter." -2 year"));

		$transactdateFrom 	 	= date("Y-m-d",strtotime($dateArr[0]));
		$transactdateTo 	 	= date("Y-m-d",strtotime($dateArr[1]));

		$link		= '';
		$tablerow   = '';
		$condition 	=	"";

		// For Account
		if ( $acctfilter && $acctfilter != "none" ) 
		{
			$condition .= " AND bal.accountcode IN ( '$acctfilter' ) ";
		}

		$fields 	=	 array('bal.accountcode as accountcode, ca.segment5 as segment5, 
							   ca.accountname, bal.transactiondate, bal.period, bal.fiscalyear, 
							   bal.voucherno, p.partnername as partner , bal.transtype, 
							   SUM(bal.debit) as debit, SUM(bal.credit) as credit');
		
		$fetch_result 	=	$this->db->setTable("balance_table bal")
						->leftJoin("chartaccount ca ON ca.id = bal.accountcode")
						->leftJoin("partners p ON p.partnercode = bal.partnercode")
						->setFields($fields)
						->setWhere(" bal.transactiondate >= '$transactdateFrom' 
								 AND bal.transactiondate <= '$transactdateTo' $condition ")
						->setGroupBy("bal.accountcode, bal.voucherno")
						->setOrderBy($sort)
						->runPagination();
						//->runSelect(false)
						//->getResult();
		if($fetch_result->result)
		{
		 	for($i=0;$i < count($fetch_result->result);$i++)
			{	
				$accountcode        = $fetch_result->result[$i]->accountcode;
				$segment5           = $fetch_result->result[$i]->segment5;
				$accountname        = $fetch_result->result[$i]->accountname;
	
		 		$voucher			= $fetch_result->result[$i]->voucherno;
		 		$transactiondate	= $fetch_result->result[$i]->transactiondate;
		 		$transactiondate	= date("M d, Y",strtotime($transactiondate));
				$period             = $fetch_result->result[$i]->period;
				$fiscalyear         = $fetch_result->result[$i]->fiscalyear;
				$partnername        = $fetch_result->result[$i]->partner;
				$transtype          = $fetch_result->result[$i]->transtype;
		 		$debit				= $fetch_result->result[$i]->debit;
		 		$credit				= $fetch_result->result[$i]->credit;
		 	
		 		$documentno 		= '';
		 		$source     		= '';

				if($transtype == 'AP'){
					$documentno	= $this->getValue("accountspayable",array("sourceno"),"voucherno = '$voucher'");
					$documentno = $documentno[0]->sourceno;
					$source		= $this->getValue("accountspayable",array("source"),"voucherno = '$voucher'");
					$source     = $source[0]->source;
					$voucherno_ = (empty($documentno)) 	?  	$voucher 	: 	$documentno;

					$link		= '<a href="'.BASE_URL.'financials/accounts_payable/view/'.$voucher.'" target="_blank">'.$voucher.'</a>';
				}else if($transtype == 'PV'){
					$link		= '<a href="' . BASE_URL. 'financials/payment_voucher/view/'.$voucher .'" target="_blank">'.$voucher.'</a>';
				}else if($transtype == 'AR'){
					$documentno = $this->getValue("accountsreceivable",array("sourceno")," voucherno = '$voucher'");
					$documentno = $documentno[0]->sourceno;
					$source 	= $this->getValue("accountsreceivable",array("source")," voucherno = '$voucher'");
					$source     = $source[0]->source;

					$voucherno_ = (empty($documentno)) 	?  	$voucher 	: 	$documentno;
					$link		= '<a href="' .BASE_URL.'financials/accounts_receivable/view/'.$voucher .'" target="_blank">'.$voucher.'</a>';
				}else if($transtype == 'RV'){
					$link		= '<a href="' . BASE_URL. 'financials/receipt_voucher/view/'.$voucher .'" target="_blank">'.$voucher.'</a>';
				}else if($transtype == 'DV'){
					$link		= '<a href="' . BASE_URL. 'financials/payment/view/'.$voucher .'" target="_blank">'.$voucher.'</a>';
				}else if($transtype == 'JV'){
					$link		= '<a href="' . BASE_URL .'financials/journal_voucher/view/'.$voucher .'" target="_blank">'.$voucher.'</a>';
				}else if($transtype == 'IT'){
					$source		= $this->getValue("journalvoucher",array("referenceno"),"voucherno = '$voucher'");
					$source     = ($source) ? $source[0]->referenceno : $voucher;
					$link		= '<a href="' . BASE_URL .'sales/delivery_receipt/view/'.$source .'" target="_blank">'.$source.'</a>';
				}else if($transtype == 'DM'){
					$documentno = $this->getValue("journalvoucher",array("sourceno")," voucherno = '$voucher'");
					$documentno = $documentno[0]->sourceno;
					$source 	= $this->getValue("journalvoucher",array("source")," voucherno = '$voucher'");
					$source     = $source[0]->source;
					$link		= '<a href="' . BASE_URL. 'financials/debit_memo/view/'.$voucher .'" target="_blank">'.$voucher.'</a>';
				}else if($transtype == 'CM'){
					$documentno = $this->getValue("journalvoucher",array("sourceno")," voucherno = '$voucher'");
					$documentno = $documentno[0]->sourceno;
					$source 	= $this->getValue("journalvoucher",array("source")," voucherno = '$voucher'");
					$source     = $source[0]->source;
					$link		= '<a href="' . BASE_URL. 'financials/credit_memo/view/'.$voucher .'" target="_blank">'.$voucher.'</a>';
				}else if($transtype == 'BEG'){
					$link		= $documentno;
				}else if($transtype == 'BEG'){
					$link		= $voucher;
				}

				$tablerow	.= '<tr>';
				$tablerow	.= '<td style="vertical-align:middle;" >&nbsp;'.$link.'</td>';
				$tablerow	.= '<td class=" center" style="vertical-align:middle;" >&nbsp;'.$transactiondate.'</td>';
				$tablerow	.= '<td class=" text-right" style="vertical-align:middle;" >'.number_format($debit,2).'</td>';
				$tablerow	.= '<td class=" text-right" style="vertical-align:middle;" >'.number_format($credit,2).'</td>';
				$tablerow	.= '</tr>';
			
			}
		}else{
			$tablerow	.= '<tr>';
			$tablerow	.= '<td class="center" colspan="4">- No Records Found -</td>';
			$tablerow	.= '</tr>';
		}
		
		$accountname		= $this->getValue("chartaccount",array("accountname")," id = '$acctfilter'","",true,true);
		
		$fetch_result->table = $tablerow;
		
		$result	= array(
				'title'=>$accountname[0]->accountname,
				'qtr'=>$qtr,
				'table'=>$fetch_result->table,
				'pagination'=>$fetch_result->pagination,
				'accountfilter'=>$acctfilter
			);
		return $result;

	}

	public function retrieveData($table, $fields = array(), $cond = "", $join = "", $orderby = "", $groupby = "")
	{
		$result = $this->db->setTable($table)
					->setFields($fields)
					->leftJoin($join)
					->setGroupBy($groupby)
					->setWhere($cond)
					->setOrderBy($orderby)
					->runSelect(false)
					->getResult();
		
		//var_dump($this->db->getQuery());	

		return $result;
	}

	private function removeComma($data) 
	{
		if (is_array($data)) {
			$temp = array();
			foreach ($data as $val) {
				$temp[] = $this->removeComma($val);
			}
			return $temp;
		} else {
			return str_replace(',', '', $data);
		}
	}

	public function getOption($type, $orderby = "")
	{
		$result = $this->db->setTable('wc_option')
					->setFields("code ind, value val")
					->setWhere("type = '$type'")
					->setOrderBy($orderby)
					->runSelect(false)
					->getResult();

		return $result;
	}

	public function buildQuery($table, $fields = array(), $cond = "")
	{	
		$sub_select = $this->db->setTable($table)
							   ->setFields($fields)
							   ->setWhere($cond)
							   ->buildSelect();
		// var_dump($this->db->buildSelect());
		return $sub_select;
	}


	private function getList($array, $id) {
		$list = array();
		foreach ($array as $key => $value) {
			if ($key != $id) {
				$list[] = (object) array('ind' => $key, 'val' => $value['label']);
				if (isset($value['children'])) {
					$list = array_merge($list, $this->getList($value['children'], $id));
				}
			}
		}
		return $list;
	}

	private function buildTree($list, $pid = 0) {
		$op = array();
		foreach($list as $item) {
			if ($item->parentid == $pid) {
				$op[$item->id] = array(
					'label' => $item->label
				);
				$children = $this->buildTree($list, $item->id);
				if ($children) {
					$op[$item->id]['children'] = $children;
				}
			}
		}
		return $op;
	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

	public function check_existing_jv($date){
		$datestring 		= date('Y-m-d', strtotime($date)).' last day of last month';
		$date_last_month 	= date_create($datestring);
		$date_last_month	= $date_last_month->format('Y-m-d'); 

		$result 	= $this->db->setTable("journalvoucher")
							   ->setFields(array('voucherno'))
							   ->setWhere("transactiondate = '$date_last_month' AND source='closing'")
								->runSelect(false)
								->getResult();

		return $result;
	}	

	public function check_existing_yrendjv($year=""){
		$cond		= ($year!="") ? " AND fiscalyear = '$year' " 	:	"";

		$result 	= $this->db->setTable("journalvoucher")
							   ->setFields(array('voucherno, fiscalyear'))
							   ->setWhere("stat NOT IN ('cancelled','temporary') AND source='yrend_closing' $cond")
								->runSelect(false)
								->getResult();

		return $result;
	}	

	public function check_latest_closedmonth($year=""){
		$cond		= ($year!="") ? " AND fiscalyear = '$year' " 	:	"";
		$result 	= $this->db->setTable("journalvoucher")
							   ->setFields(array('fiscalyear, period'))
							   ->setWhere("source='closing' AND stat NOT IN ('cancelled','temporary') $cond")
							   ->setOrderBy('period DESC')
							   ->setLimit(1)
								->runSelect(false)
								->getResult();

		return $result;
	}	

	public function get_last_day($month = '', $year = '')  {
	   if (empty($month))  {
		  $month = date('m');
	   }
		
	   if (empty($year))  {
		  $year = date('Y');
	   }
		
	   $result = strtotime("{$year}-{$month}-01");
	   $result = strtotime('-1 second', strtotime('+1 month', $result));
	 
	   return date('Y-m-d', $result);
	}

	public function save_journal_voucher($data){	
		$generatedvoucher 	=	isset($data['voucher']) 			?	$data['voucher'] 			: 	"";
		$warehouse 			=	isset($data['warehouse']) 			?	$data['warehouse'] 			: 	"";
		$lastdayofdate 		=	isset($data['datefrom']) 			?	$data['datefrom'] 			: 	"";
		$remarks 			=	isset($data['notes']) 				? 	$data['notes'] 				: 	"";
		$actualaccount  	=	isset($data['closing_account']) 	? 	$data['closing_account'] 	: 	"";
		$detailparticular 	=	isset($data['detailparticular']) 	? 	$data['detailparticular'] 	:	"";
		$source 			=	isset($data['source']) 				? 	$data['source'] 	:	"";

		$result 			=	0;
		$amount 			= 	0;
		$firstdayofdate 	=	"";

		/**FORMAT DATES**/
		if($source == "closing") {
			$exploded_date		=	explode(' ',$lastdayofdate);
			$lastdayofdate 		=	date("Y-m-d", strtotime($lastdayofdate));
			$month 				=	date('m', strtotime($lastdayofdate));
			$year 				=	date('Y', strtotime($lastdayofdate));

			$firstdayofdate 	=	date($year.'-'.$month.'-01');
		} else {
			$exploded_date		=	explode(' - ',$lastdayofdate);
			$firstdayofdate 	=	$exploded_date[0];
			$firstdayofdate 	=	date("Y-m-d", strtotime($firstdayofdate));
			$lastdayofdate 		=	$exploded_date[1];
			$lastdayofdate 		=	date("Y-m-d", strtotime($lastdayofdate));
			$month 				=	date('m', strtotime($lastdayofdate));
			$year 				=	date('Y', strtotime($lastdayofdate));
		}
 
		$currentyear 		= 	date("Y",strtotime($lastdayofdate));
		$prevyear 			= 	date("Y",strtotime($firstdayofdate." -1 year"));

		$current_year_id 	= $this->retrieveAccount("IS");
		$current_year_id 	= isset($current_year_id->salesAccount) ? $current_year_id->salesAccount 	:	"";

		$accounts_arr 		= $this->retrieveCOAdetails($currentyear,$prevyear,'IS');
			
		$h_amount 			= $h_total_debit 	= $h_total_credit = 0;
		foreach($accounts_arr as $row){
			$accountid 		= ($source == "closing") ? $row->accountid : $current_year_id;
			$prev_carry 	= $this->getPrevCarry($accountid,$firstdayofdate);
			$amount			= $this->getCurrent($accountid,$firstdayofdate,$lastdayofdate);
	
			if( $amount > 0 ){
				$credit 			= 	$prev_carry	+ $amount;
				$h_total_credit 	+=	$credit;
			} else {
				$debit 				= -($prev_carry + $amount);
				$h_total_debit 		+=	$debit;
			}
		} 

		$h_amount 	= 	($amount > 0) ? $h_total_credit :	$h_total_debit;

		$str_month 	=	date('F', strtotime($lastdayofdate));
		$reference	=	($source == "closing") ? "Closing for $str_month, $year" : "Year-end Closing for $year";

		$header['voucherno'] 		=	$generatedvoucher;
		$header['transtype'] 		=	"JV";
		$header['stat'] 			=	"temporary";
		$header['transactiondate'] 	=	$lastdayofdate;
		$header['fiscalyear'] 		=	$year;
		$header['period'] 			= 	$month;
		$header['currencycode'] 	= 	"PHP";
		$header['exchangerate'] 	=	1;
		$header['amount'] 	 		=	$h_amount;
		$header['convertedamount'] 	=	$h_amount;
		$header['referenceno'] 		=	$reference;
		$header['source'] 			=	$source;
		$header['sitecode'] 		= 	$warehouse;
		$header['remarks'] 			= 	$remarks;

		$result 					=	$this->insertdata('journalvoucher',$header);

		if($result){
			$debit 					= $total_debit 	= 0;
			$credit 				= $total_credit = 0;
			$retained 				= 0;
			$linenum 				= 1;

			foreach($accounts_arr as $row){
				$accountid 		= ($source == "closing") ? $row->accountid : $current_year_id;

				$prev_carry 	= $this->getPrevCarry($accountid,$firstdayofdate);
				$amount			= $this->getCurrent($accountid,$firstdayofdate,$lastdayofdate);

				$accounts['voucher'] 			=	$generatedvoucher;
				$accounts['account'] 			=	$accountid;
				$accounts['linenum'] 			=	$linenum;
				$accounts['detailparticulars'] 	= 	$detailparticular;
	
				if( $amount > 0 ){
					$credit 			= 	$prev_carry	+ $amount;
					$accounts['amount'] 	=	$credit;
					$result  			=	$this->create_jvdetails_credit($accounts);
					$total_credit 		+=	$credit;

					$linenum 		+=	1;	
				} else {
					$debit 				= -($prev_carry	+ $amount);
					$accounts['amount'] 	=	$debit;

					$total_debit 		+=	$debit;

					$result 			=	$this->create_jvdetails_debit($accounts);

					$linenum 			+=	1;	
				}
			} 
	
			$retained 		= ($total_debit > $total_credit) ? $total_debit - $total_credit : -($total_credit - $total_debit);

			if( $result ) {
				if( $retained < 0 ){
					$closing['voucher'] 			=	$generatedvoucher;
					$closing['linenum'] 			=	$linenum;
					$closing['account'] 			= 	$actualaccount;
					$closing['amount'] 				=  	-($retained);
					$closing['detailparticulars'] 	= 	$detailparticular;

					$result 			=	$this->create_jvdetails_debit($closing);
				} else {
					$closing['voucher'] 			=	$generatedvoucher;
					$closing['linenum'] 			=	$linenum;
					$closing['account'] 			= 	$actualaccount;
					$closing['amount'] 				=  	$retained;
					$closing['detailparticulars'] 	= 	$detailparticular;
	
					$result 			=	$this->create_jvdetails_credit($closing);
				}

				return array(
					'result'=>$result,
					'voucherno'=>$generatedvoucher
				);
			}
		}
	}

	public function create_jvdetails_debit($data) {

		// echo "Dedit ==== \n\n";

		$generatedvoucher 	=	isset($data['voucher']) 			?	$data['voucher'] 			: 	"";
		$reference 			=	isset($data['reference']) 			?	$data['reference'] 			: 	"";
		$warehouse 			=	isset($data['warehouse']) 			?	$data['warehouse'] 			: 	"";
		$date 				=	isset($data['daterangefilter']) 	?	$data['daterangefilter'] 	: 	"";
		$remarks 			=	isset($data['notes']) 				? 	$data['notes'] 				: 	"";
		$account  			=	isset($data['account']) 			? 	$data['account'] 			: 	"";
		$amount  			=	isset($data['amount']) 				? 	$data['amount'] 			: 	0;
		$detailparticular 	=	isset($data['detailparticular']) 	? 	$data['detailparticular'] 	:	"";
		$linenum 			=	isset($data['linenum']) 			? 	$data['linenum'] 			:	"";
		// echo "Account = ".$account."\n";

		$details['voucherno'] 			=	$generatedvoucher;
		$details['transtype'] 			=	'JV';
		$details['linenum'] 			=	$linenum;
		$details['accountcode'] 		= 	$account;
		$details['debit'] 				=  	$amount;
		$details['credit'] 				=	0;
		$details['exchangerate'] 		= 	1;
		$details['converteddebit'] 		= 	$amount;
		$details['convertedcredit'] 	= 	0;
		$details['source'] 				= 	"closing";
		$details['detailparticulars'] 	= 	$detailparticular;
		$details['stat'] 				= 	"temporary";
		// var_dump($details);
		$result 	=	 $this->insertdata('journaldetails',$details);

		return $result;
	}

	public function create_jvdetails_credit($data) {
		// echo "Credit ==== \n\n";
		
		// var_dump($data);

		$generatedvoucher 	=	isset($data['voucher']) 			?	$data['voucher'] 			: 	"";
		$reference 			=	isset($data['reference']) 			?	$data['reference'] 			: 	"";
		$warehouse 			=	isset($data['warehouse']) 			?	$data['warehouse'] 			: 	"";
		$date 				=	isset($data['daterangefilter']) 	?	$data['daterangefilter'] 	: 	"";
		$remarks 			=	isset($data['notes']) 				? 	$data['notes'] 				: 	"";
		$account  			=	isset($data['account']) 			? 	$data['account'] 			: 	"";
		$amount  			=	isset($data['amount']) 				? 	$data['amount'] 			: 	0;
		$detailparticular 	=	isset($data['detailparticular']) 	? 	$data['detailparticular'] 	:	"";
		$linenum 			=	isset($data['linenum']) 			? 	$data['linenum'] 			:	"";
		// echo "Account = ".$account."\n";

		$details['voucherno'] 			=	$generatedvoucher;
		$details['transtype'] 			=	'JV';
		$details['linenum'] 			=	$linenum;
		$details['accountcode'] 		= 	$account;
		$details['debit'] 				=  	0;
		$details['credit'] 				=	$amount;
		$details['exchangerate'] 		= 	1;
		$details['converteddebit'] 		= 	0;
		$details['convertedcredit'] 	= 	$amount;
		$details['source'] 				= 	"closing";
		$details['detailparticulars'] 	= 	$detailparticular;
		$details['stat'] 				= 	"temporary";

		$result 						=	$this->insertdata('journaldetails',$details);

		return $result;
	}

	public function insertData($table, $data) {
		$result 	=	$this->db->setTable($table)
				 		->setValues($data)
				 		->runInsert();
		// echo $this->db->getQuery();
		return $result;
	}

	public function getPeriodStart() {
		$result = $this->db->setTable('company')
							->setFields(array('taxyear', "MONTH(STR_TO_DATE(periodstart,'%b')) periodstart"))
							->setLimit(1)
							->runSelect()
							->getRow();

		// if ($result->taxyear == 'fiscal') {
		// 	$this->period = $result->periodstart;
		// 	if ($this->period > date('n')) {
		// 		$this->year = date('Y') - 1;
		// 	}
		// }

		return $result;
	}

	public function getChartAccountList() {
		// $cond 	=	(!empty($cond_value)) 	?	" accountname LIKE '%$cond_value%' " 	:	"";
		return $this->db->setTable('chartaccount c')
						->innerJoin('accountclass ac ON c.companycode = ac.companycode AND c.accountclasscode = ac.accountclasscode')
						->setFields('id ind, accountname val, accountclass parent')
						->setOrderBy('accountclass, accountname')
						// ->setWhere($cond)
						->runSelect()
						->getResult();
	}

	public function getCOAname($code) {
		return $this->db->setTable('chartaccount c')
						->setFields('id ind, accountname val')
						->setWhere("id = '$code'")
						->setLimit(1)
						->runSelect()
						->getRow();
	}

	public function retrieveAccount($code){
		return $this->db->setTable('fintaxcode')
						->setFields('salesAccount')
						->setWhere("fstaxcode = '$code'")
						->runSelect()
						->getRow();
	}

	public function getJVHeader($voucherno){
		$result =  $this->db->setTable('journalvoucher')
						->setFields('referenceno, remarks, proformacode, transactiondate')
						->setWhere("voucherno = '$voucherno' AND stat = 'temporary' AND source IN ('closing','yrend_closing') ")
						->runSelect()
						->getRow();
						
		return $result;
	}

	public function getJVDetails($voucherno, $search="", $limit=1){
		$search_cond 	=	($search!="") 	?	" AND (jv.detailparticulars LIKE '%$search%' OR ca.accountname LIKE '%$search%' OR ca.segment5 LIKE '%$search%' )" 	:	"";
		$result 		= 	$this->db->setTable('journaldetails jv')
									->leftJoin('chartaccount ca ON ca.id=jv.accountcode')
									->setFields('jv.linenum, jv.accountcode, CONCAT(ca.segment5," - ",ca.accountname) accountname, jv.detailparticulars, jv.debit, jv.credit')
									->setWhere("jv.voucherno = '$voucherno' AND jv.stat = 'temporary' AND jv.source IN ('closing','yrend_closing') AND ( jv.debit > 0 OR jv.credit > 0 ) $search_cond ")
									->setOrderBy("jv.linenum")
									->setLimit($limit)
									->runPagination();
		// echo $this->db->getQuery();
		return $result;
	}

	public function getProformaList() {
		$result = $this->db->setTable('proforma')
							->setFields("proformacode ind, proformadesc val")
							->setWhere("transactiontype = 'Journal Voucher'")
							->setOrderBy("proformadesc")
							->runSelect()
							->getResult();
		return $result;
	}

	public function update_jv_status($temp, $voucherno) {
		$data["stat"]   	= "posted";
		$data['voucherno'] 	= $voucherno;

		$condition 		= " voucherno = '$temp' ";

		$result 		= $this->db->setTable('journalvoucher')
									->setValues($data)
									->setWhere($condition)
									->setLimit(1)
									->runUpdate();

		if( $result ){
			$result 	=	$this->update_jvdetails_status($voucherno);
		}

		return $data 	=	array(
								"result" 	=>	$result
							);
	}

	public function update_jvdetails_status($voucherno) {
		$data["stat"]   = "posted";

		$condition 		= " voucherno = '$voucherno' ";

		$result 		= $this->db->setTable('journaldetails')
									->setValues($data)
									->setWhere($condition)
									->runUpdate();
									
		return $result;
	}

	public function getOpenList(){

		$leftJoin 		=	$this->db->setTable("journalvoucher jv")
									 ->setFields("MAX(jv.transactiondate) transactiondate, jv.period, jv.fiscalyear, jv.companycode")
									 ->setWhere("jv.source = 'closing' AND jv.stat = 'posted'")
									 ->setGroupBy("jv.period, jv.fiscalyear")
									 ->setOrderBy("jv.transactiondate DESC")
									 ->setLimit(1)
									 ->buildSelect();
		
		$sub_select2 	=	$this->db->setTable("journalvoucher jv")
									 ->setFields("MAX(jv.transactiondate) transactiondate, jv.period, jv.fiscalyear, jv.companycode")
									 ->setWhere("jv.source = 'closing' AND jv.stat = 'posted'")
									 ->setGroupBy("jv.period, jv.fiscalyear")
									 ->setOrderBy("jv.transactiondate DESC")
									 ->setLimit(1)
									 ->buildSelect();

		$sub_select 	=	$this->db->setTable("balance_table b1")
									 ->setFields("MIN(b1.fiscalyear) fiscalyear, b1.companycode")
									 ->leftJoin("($sub_select2) jv ON b1.companycode = jv.companycode")
									 ->setWhere("b1.companycode = 'CID' AND b1.transactiondate > IFNULL(jv.transactiondate, '0000-00-00')")
									 ->setGroupBy("b1.period, b1.fiscalyear")
									 ->setOrderBy("b1.fiscalyear ASC, b1.period ASC")
									 ->setLimit(1)
									 ->buildSelect();

		$result 		= 	$this->db->setTable('balance_table b1')
									->setFields("CONCAT(b1.period,'-', b1.fiscalyear) ind, CONCAT(MONTHNAME(STR_TO_DATE(b1.period, '%m')),' ',b1.fiscalyear) val")
									->leftJoin("($leftJoin) jv ON b1.companycode = jv.companycode")
									->innerJoin("($sub_select) b2 ON b2.companycode = b1.companycode")
									->setWhere("b1.companycode = 'CID' AND b1.transactiondate > IFNULL(jv.transactiondate, '0000-00-00') AND YEAR(b1.transactiondate) = b2.fiscalyear")
									->setGroupBy('b1.period, b1.fiscalyear')
									->setOrderBy('b1.fiscalyear ASC, b1.period ASC')
									->runSelect()
									->getResult();
		return $result;
	}

	public function getYearforClosing(){
		$ret_years 	=	$this->getSystemTransactionYears();
		$ret		= 	$this->getPeriodStart();
		$month_start= 	($ret->taxyear == 'fiscal') ? $ret->periodstart 	:	1;

		$select 	=	array(); 
		$y 	=	0;

		foreach($ret_years as $key => $result){
			$year 	=	$result->fiscalyear;
			for($x=1;$x<=12;$x++){
				$select[] 	=	"SELECT $year year, $x month";
			}
		}
		$select_query 	= implode(" UNION ",$select);
		
		// SELECT JV w/o closing
		$result 	=	$this->db->setTable("($select_query) period")
								->setFields("period.year")
								->leftJoin("journalvoucher jv ON jv.period = period.month AND jv.fiscalyear = period.year AND jv.source = 'closing' AND jv.stat = 'posted' ")
								->setWhere("jv.voucherno IS NULL ")
								->setGroupBy("period.year, period.month")
								->setOrderBy("period.year ASC, period.month ASC")
								->setLimit(1)
								->runSelect(false)
								->getRow();

		return $result;
	}

	public function getClosingMonth($year = false) {
		$ret_years 	=	$this->getSystemTransactionYears();

		$ret		= 	$this->getPeriodStart();
		// $month_start= 	($ret->taxyear == 'fiscal') ? $ret->periodstart 	:	1;
		$month_start= 	1;
		$month_end 	=   12;

		$select 	=	array(); 
		foreach($ret_years as $key => $result){
			$year 	=	$result->fiscalyear;
			for($x=$month_start;$x<=$month_end;$x++){
				$select[] 	=	"SELECT $year year, $x month";
				// if($x==12 && $ret->taxyear == 'fiscal'){
				// 	$month_end   = $month_start - 1;
				// 	$month_start = 1;
				// }
			}
		}
		$select_query 	= implode(" UNION ",$select);
		
		$result 	=	$this->db->setTable("($select_query) period")
								->setFields("period.month, period.year")
								->leftJoin("journalvoucher jv ON jv.period = period.month AND jv.fiscalyear = period.year AND jv.source = 'closing' AND jv.stat = 'posted' ")
								->setWhere("jv.voucherno IS NULL ")
								->setGroupBy("period.year, period.month")
								->setOrderBy("period.year ASC, period.month ASC")
								->setLimit(1)
								->runSelect(false)
								->getRow();
								
		return $result;
	}

	public function getMonthEnd($date) {
		return date("Y-m-t", strtotime($date));
	}

	public function getfirstOpen(){

		$leftJoin 		=	$this->db->setTable("journalvoucher jv")
									 ->setFields("MAX(jv.transactiondate) transactiondate, jv.period, jv.fiscalyear, jv.companycode")
									 ->setWhere("jv.source = 'closing' AND jv.stat = 'posted'")
									 ->setGroupBy("jv.period, jv.fiscalyear")
									 ->setOrderBy("jv.transactiondate DESC")
									 ->setLimit(1)
									 ->buildSelect();
		
		$sub_select2 	=	$this->db->setTable("journalvoucher jv")
									 ->setFields("MAX(jv.transactiondate) transactiondate, jv.period, jv.fiscalyear, jv.companycode")
									 ->setWhere("jv.source = 'closing' AND jv.stat = 'posted'")
									 ->setGroupBy("jv.period, jv.fiscalyear")
									 ->setOrderBy("jv.transactiondate DESC")
									 ->setLimit(1)
									 ->buildSelect();

		$sub_select 	=	$this->db->setTable("balance_table b1")
									 ->setFields("MIN(b1.fiscalyear) fiscalyear, b1.companycode")
									 ->leftJoin("($sub_select2) jv ON b1.companycode = jv.companycode")
									 ->setWhere("b1.companycode = 'CID' AND b1.transactiondate > IFNULL(jv.transactiondate, '0000-00-00')")
									 ->setGroupBy("b1.period, b1.fiscalyear")
									 ->setOrderBy("b1.fiscalyear ASC, b1.period ASC")
									 ->setLimit(1)
									 ->buildSelect();

		$result 		= 	$this->db->setTable('balance_table b1')
									->setFields("CONCAT(MONTHNAME(STR_TO_DATE(b1.period, '%m')), ' ', b1.fiscalyear) val")
									->leftJoin("($leftJoin) jv ON b1.companycode = jv.companycode")
									->leftJoin("($sub_select) b2 ON b2.companycode = b1.companycode")
									->setWhere("b1.companycode = 'CID' AND b1.transactiondate > IFNULL(jv.transactiondate, '0000-00-00') AND YEAR(b1.transactiondate) = b2.fiscalyear")
									->setGroupBy('b1.period, b1.fiscalyear')
									->setOrderBy('b1.fiscalyear ASC, b1.period ASC')
									->setLimit(1)
									->runSelect()
									->getRow();

		return $result;
	}

	public function getReference($voucherno){
		$result 		= 	$this->db->setTable('journalvoucher j')
									->setFields("j.referenceno")
									->setWhere("j.voucherno = '$voucherno' AND j.companycode = 'CID'")
									->setLimit(1)
									->runSelect()
									->getRow();
									// echo $this->db->getQuery();
		return $result;
	}

	public function delete_temporary_jv($voucherno){
		$result 	=	$this->db->setTable("journaldetails")
								 ->setWhere("voucherno = '$voucherno' AND stat = 'temporary' AND source IN ('closing','yrend_closing')")
								 ->runDelete();

		if( $result ){
			$result 	=	$this->db->setTable("journalvoucher")
								 ->setWhere("voucherno = '$voucherno' AND stat = 'temporary' AND source IN ('closing','yrend_closing')")
								 ->runDelete();
		}

		return $result;
		
	}

	public function getBalanceTableCount(){
		$result 		= 	$this->db->setTable('balance_table')
									->setFields("COUNT(*) count")
									->setWhere("companycode = 'CID'")
									->runSelect()
									->getRow();
		return $result;
	}

	public function retrieveAccess($groupname){
		$result = $this->db->setTable("wc_module_access")
					->setFields(array("mod_add","mod_view","mod_edit","mod_delete","mod_list","mod_print","mod_post","mod_unpost","mod_close"))
					->setWhere("groupname = '$groupname' AND companycode = 'CID' AND module_name = 'Trial Balance'")
					->setLimit(1)
					->runSelect()
					->getResult();
		return $result;
	}

	public function getSystemTransactionYears(){

		$result 	=	$this->db->setTable("balance_table")
								->setFields("fiscalyear")
								->setGroupBy('fiscalyear')
								->runSelect()
								->getResult();

		return $result;
	}

}	