<?php
class trial_balance extends wc_model {

	public function getPrevCarry($account,$date){
		$debit 		= 0;
		$credit 	= 0;
		$prevyear 	= date("Y",strtotime($date." -1 year"));

		$fetch_debit  = $this->getValue("balance_table",array("SUM(debit) as debit")," accountcode = '$account' AND 
		YEAR(transactiondate) = $prevyear ");
		$debit        = $fetch_debit[0]->debit;
		$fetch_credit = $this->getValue("balance_table",array("SUM(credit) as credit")," accountcode = '$account' AND YEAR(transactiondate) = $prevyear ");
		$credit 	  = $fetch_credit[0]->credit;
		return ($debit > $credit) ? $debit - $credit : -($credit - $debit);
	}

	public function getBalanceCarry($account,$fromdate,$todate){
		$debit 		= 0;
		$credit 	= 0;
		$currentyear= date("Y",strtotime($fromdate));

		$fetch_debit  = $this->getValue("balance_table","SUM(debit) as debit"," accountcode = '$account' AND YEAR(transactiondate) = $currentyear AND transactiondate <= '$fromdate'");
		$debit		  = $fetch_debit[0]->debit;
		$fetch_credit = $this->getValue("balance_table","SUM(credit) as credit"," accountcode = '$account' AND YEAR(transactiondate) = $currentyear AND transactiondate <= '$fromdate'");
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
		// echo "CREDIT = ".$credit."      "; 
		// echo "DEBIT = ".$debit."\n";

		return ($debit > $credit) ? $debit - $credit : -($credit - $debit);

		// $fetch_result 	=	$this->getValue("balance_table","SUM(debit)-SUM(credit)", "accountcode = '$account' AND (transactiondate >= '$fromdate' AND transactiondate <= '$todate')");

	}

	public function getAccountCodeBeg($beg_balance_query_table,$beg_balance_query_fields,$beg_balance_query_condition,$account,$field){
	
		$result = $this->db->setTable($beg_balance_query_table)
					->setFields($beg_balance_query_fields)
					->setWhere($beg_balance_query_condition." AND accountcode='".$account."'")
					->setGroupBy("gldetails.account ASC")
					//->buildSelect();
		
					->runSelect()
					->getResult();
		//var_dump($result);
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
					//->buildSelect();
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

	public function fileExport($currentyear,$prevyear,$fstype="")
	{
		$fs_cond 	=	(!empty($fstype)) 	?	" AND chart.fspresentation = '$fstype'" 	:	"";
		
		$result = $this->db->setTable("chartaccount as chart")
						->setFields(array("chart.id as accountid","chart.segment5 as accountcode","chart.accountname as accountname"))
						->leftJoin("balance_table as bal ON bal.accountcode = chart.id")
						->setWhere("YEAR(bal.transactiondate) >= $prevyear 
		AND YEAR(bal.transactiondate) <= $currentyear $fs_cond")
						->setGroupBy("chart.segment5")
						->setOrderBy("chart.segment5 ASC")
						->runSelect()
						->getResult();
						// echo $this->db->getQuery();
		return $result;
	}

	public function load_account_transactions($data){
		$daterangefilter			= isset($data['daterangefilter'])  ?  $data['daterangefilter']  : "";
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
			//$accounts 	= implode( "','" ,$acctfilter );
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
						->runSelect(false)
						->getResult();
						
		 if(!empty($fetch_result))
		{
		 	for($i=0;$i < count($fetch_result);$i++)
			{	
				$accountcode        = $fetch_result[$i]->accountcode;
				$segment5           = $fetch_result[$i]->segment5;
				$accountname        = $fetch_result[$i]->accountname;
	
		 		$voucher			= $fetch_result[$i]->voucherno;
		 		$transactiondate	= $fetch_result[$i]->transactiondate;
		 		$transactiondate	= date("M d, Y",strtotime($transactiondate));
				$period             = $fetch_result[$i]->period;
				$fiscalyear         = $fetch_result[$i]->fiscalyear;
				$partnername        = $fetch_result[$i]->partner;
				$transtype          = $fetch_result[$i]->transtype;
		 		$debit				= $fetch_result[$i]->debit;
		 		$credit				= $fetch_result[$i]->credit;
		 	
				
		 		$documentno = '';
		 		$source     = '';

				if($transtype == 'AP'){
			
					$documentno	= $this->getValue("accountspayable",array("sourceno"),"voucherno = '$voucher'");
					$documentno = $documentno[0]->sourceno;
					$source		= $this->getValue("accountspayable",array("source"),"voucherno = '$voucher'");
					$source     = $source[0]->source;

					$voucherno_ = (empty($documentno)) 	?  	$voucher 	: 	$documentno;

					//$modtype	= ($source == 'EXP') ? "bills" : "purchase_voucher";
					$link		= '<a href="'.BASE_URL.'financials/accounts_payable/print_preview/'.$voucher.'" target="_blank">'.$voucherno_.'</a>';

				}else if($transtype == 'PV'){
					// $link		= '<a href="' .BASE_URL.'modules/purchase/csv/payment_voucher.php?sid='.$voucher) .'" target="_blank">'.$voucher.'</a>';
					$link		= '<a href="' . BASE_URL. 'financials/payment_voucher/print_preview/'.$voucher .'" target="_blank">'.$voucher.'</a>';
				}else if($transtype == 'AR'){
					$documentno = $this->getValue("accountsreceivable",array("sourceno")," voucherno = '$voucher'");
					$documentno = $documentno[0]->sourceno;
					$source 	= $this->getValue("accountsreceivable",array("source")," voucherno = '$voucher'");
					$source     = $source[0]->source;

					$voucherno_ = (empty($documentno)) 	?  	$voucher 	: 	$documentno;
					// if( $transtype  )
					$link		= '<a href="' .BASE_URL.'financials/accounts_receivable/print_preview/'.$voucher .'" target="_blank">'.$voucherno_.'</a>';
				}else if($transtype == 'RV'){
					$link		= '<a href="' . BASE_URL. 'financials/receipt_voucher/print_preview/'.$voucher .'" target="_blank">'.$voucher.'</a>';
				}else if($transtype == 'DV'){
					$link		= '<a href="' . BASE_URL. 'financials/payment/print_preview/'.$voucher .'" target="_blank">'.$voucher.'</a>';
				}else if($transtype == 'JV'){
					$link		= '<a href="' . BASE_URL .'financials/journal_voucher/print_preview/'.$voucher .'" target="_blank">'.$voucher.'</a>';
				}else if($transtype == 'DM'){
					$documentno = $this->getValue("journalvoucher",array("sourceno")," voucherno = '$voucher'");
					$documentno = $documentno[0]->sourceno;
					$source 	= $this->getValue("journalvoucher",array("source")," voucherno = '$voucher'");
					$source     = $source[0]->source;

					$voucherno_ = (empty($documentno)) 	?  	$voucher 	: 	$documentno;
					$link		= '<a href="' . BASE_URL. 'financials/debit_memo/print_preview/'.$voucher .'" target="_blank">'.$voucherno_.'</a>';
				}else if($transtype == 'CM'){
					$documentno = $this->getValue("journalvoucher",array("sourceno")," voucherno = '$voucher'");
					$documentno = $documentno[0]->sourceno;
					$source 	= $this->getValue("journalvoucher",array("source")," voucherno = '$voucher'");
					$source     = $source[0]->source;
					$voucherno_ = (empty($documentno)) 	?  	$voucher 	: 	$documentno;
					$link		= '<a href="' . BASE_URL. 'financials/credit_memo/print_preview/'.$voucher .'" target="_blank">'.$voucherno_.'</a>';
				}else if($transtype == 'BEG'){
					$link		= $documentno;
				}else if($transtype == 'BEG'){
					$link		= $voucher;
				}

				$tablerow	.= '<tr>';
				$tablerow	.= '<td style="vertical-align:middle;" >&nbsp;'.$link.'</td>';
				$tablerow	.= '<td class=" center" style="vertical-align:middle;" >&nbsp;'.$transactiondate.'</td>';
				$tablerow	.= '<td class=" right" style="vertical-align:middle;" >'.number_format($debit,2).'</td>';
				$tablerow	.= '<td class=" right" style="vertical-align:middle;" >'.number_format($credit,2).'</td>';
				$tablerow	.= '</tr>';
			
			}
		}else{
			$tablerow	.= '<tr>';
			$tablerow	.= '<td class="center" colspan="4">- No Records Found -</td>';
			$tablerow	.= '</tr>';
		}
		

		//echo $this->db->getQuery();
		
		$accountname		= $this->getValue("chartaccount",array("accountname")," id = '$acctfilter'","",true,true);

		$result	= array(
				'title'=>$accountname[0]->accountname,
				'qtr'=>$qtr,
				'table'=>$tablerow,
				'accountfilter'=>$acctfilter,
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

	public function save_journal_voucher($data)
	{	
		$generatedvoucher 	=	isset($data['voucher']) 			?	$data['voucher'] 			: 	"";
		$reference 			=	isset($data['reference']) 			?	$data['reference'] 			: 	"";
		$warehouse 			=	isset($data['warehouse']) 			?	$data['warehouse'] 			: 	"";
		$date 				=	isset($data['daterangefilter']) 	?	$data['daterangefilter'] 	: 	"";
		$remarks 			=	isset($data['notes']) 				? 	$data['notes'] 				: 	"";
		$actualaccount  	=	isset($data['retained_acct']) 		? 	$data['retained_acct'] 		: 	"";
		$detailparticular 	=	isset($data['detailparticular']) 	? 	$data['detailparticular'] 	:	"";

		/**FORMAT DATES**/
		$dateArr 			=	explode(' - ',$date);
		$transactiondate	= 	$this->date->datetimeDbFormat($dateArr[1]);
		$period				= 	date("n",strtotime($transactiondate));
		$fiscalyear			= 	date("Y",strtotime($transactiondate));

		$result 			=	0;

		$default_datefilter = date("M d, Y",strtotime('first day of this month')).' - '.date("M d, Y",strtotime('last day of this month'));

		$datefilterFrom 	= (!empty($dateArr[0]))? $this->date->datetimeDbFormat($dateArr[0]) : "";
		$datefilterTo   	= (!empty($dateArr[1]))? $this->date->datetimeDbFormat($dateArr[1]) : "";
		$datefilter     	= (!empty($daterangefilter))? $daterangefilter : $default_datefilter;
		$currentyear 		= date("Y",strtotime($datefilterTo));
		$prevyear 			= date("Y",strtotime($datefilterFrom." -1 year"));

		$accounts_arr 		= $this->fileExport($currentyear,$prevyear,'IS');

		foreach($accounts_arr as $row){
			$accountid 		= $row->accountid;
			$amount			= $this->getCurrent($accountid,$datefilterFrom,$datefilterTo);
			if( $amount > 0 ){
				$credit 			= 	$amount;
			} else {
				$debit 				= -($amount);
			}
		} 

		//Header Details
		$header['voucherno'] 		=	$generatedvoucher;
		$header['transtype'] 		=	"JV";
		$header['stat'] 			=	"temporary";
		$header['transactiondate'] 	=	$transactiondate;
		$header['fiscalyear'] 		=	$fiscalyear;
		$header['period'] 			= 	$period;
		$header['currencycode'] 	= 	"PHP";
		$header['exchangerate'] 	=	1;
		$header['amount'] 	 		=	$debit;
		$header['convertedamount'] 	=	$debit;
		$header['referenceno'] 		=	$reference;
		$header['source'] 			=	"closing";
		$header['sitecode'] 		= 	$warehouse;
		$header['remarks'] 			= 	$remarks;
		
		//Insert Header
		$result 	=	 $this->insertdata('journalvoucher',$header);

		$debit 				= 0;
		$credit 			= 0;
		$retained 			= 0;
		$linenum 			= 1;

		if( $result ){
			foreach($accounts_arr as $row){
				$accountid 		= $row->accountid;
				$amount			= $this->getCurrent($accountid,$datefilterFrom,$datefilterTo);
	
				$data['account'] 	=	$accountid;
				$data['linenum'] 	=	$linenum;
	
				if( $amount > 0 ){
					$credit 			= 	$amount;
					$data['amount'] 	=	$credit;
					$result 			=	$this->create_jvdetails_credit($data);
				} else {
					$debit 				= -($amount);
					$data['amount'] 	=	$debit;
					$result 			=	$this->create_jvdetails_debit($data);
				}
	
				$retained 		= ($debit > $credit) ? $debit - $credit : $credit - $debit;
				$linenum 		+=	1;
			} 
	
			if( $result ) {
				// Retained
				$details['voucherno'] 			=	$generatedvoucher;
				$details['transtype'] 			=	'JV';
				$details['linenum'] 			=	$linenum;
				$details['accountcode'] 		= 	$actualaccount;
				$details['debit'] 				=  	0;
				$details['credit'] 				=	$retained;
				$details['exchangerate'] 		= 	1;
				$details['converteddebit'] 		= 	0;
				$details['convertedcredit'] 	= 	$retained;
				$details['source'] 				= 	"closing";
				$details['detailparticulars'] 	= 	$detailparticular;
				$details['stat'] 				= 	"temporary";
	
				$result 						=	 $this->insertdata('journaldetails',$details);
			}	
		}

		return array(
			 		'result'=>$result,
					'voucherno'=>$generatedvoucher
				);

	}

	public function create_jvdetails_debit($data) {
		$generatedvoucher 	=	isset($data['voucher']) 			?	$data['voucher'] 			: 	"";
		$reference 			=	isset($data['reference']) 			?	$data['reference'] 			: 	"";
		$warehouse 			=	isset($data['warehouse']) 			?	$data['warehouse'] 			: 	"";
		$date 				=	isset($data['daterangefilter']) 	?	$data['daterangefilter'] 	: 	"";
		$remarks 			=	isset($data['notes']) 				? 	$data['notes'] 				: 	"";
		$account  			=	isset($data['account']) 			? 	$data['account'] 			: 	"";
		$amount  			=	isset($data['amount']) 				? 	$data['amount'] 			: 	0;
		$detailparticular 	=	isset($data['detailparticular']) 	? 	$data['detailparticular'] 	:	"";
		$linenum 			=	isset($data['linenum']) 			? 	$data['linenum'] 			:	"";

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

		$result 	=	 $this->insertdata('journaldetails',$details);

		return $result;
	}

	public function create_jvdetails_credit($data) {
		$generatedvoucher 	=	isset($data['voucher']) 			?	$data['voucher'] 			: 	"";
		$reference 			=	isset($data['reference']) 			?	$data['reference'] 			: 	"";
		$warehouse 			=	isset($data['warehouse']) 			?	$data['warehouse'] 			: 	"";
		$date 				=	isset($data['daterangefilter']) 	?	$data['daterangefilter'] 	: 	"";
		$remarks 			=	isset($data['notes']) 				? 	$data['notes'] 				: 	"";
		$account  			=	isset($data['account']) 			? 	$data['account'] 			: 	"";
		$amount  			=	isset($data['amount']) 				? 	$data['amount'] 			: 	0;
		$detailparticular 	=	isset($data['detailparticular']) 	? 	$data['detailparticular'] 	:	"";
		$linenum 			=	isset($data['linenum']) 			? 	$data['linenum'] 			:	"";

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

		return $result;
	}

	public function getChartAccountList($cond_value="") {
		$cond 	=	(!empty($cond_value)) 	?	" accountname LIKE '%$cond_value%' " 	:	"";
		return $this->db->setTable('chartaccount c')
						->innerJoin('accountclass ac ON c.companycode = ac.companycode AND c.accountclasscode = ac.accountclasscode')
						->setFields('id ind, accountname val, accountclass parent')
						->setOrderBy('accountclass, accountname')
						->setWhere($cond)
						->runSelect()
						->getResult();
	}

	public function getJVHeader($voucherno){
		return $this->db->setTable('journalvoucher')
						->setFields('referenceno, remarks, proformacode, transactiondate')
						->setWhere("voucherno = '$voucherno' AND stat = 'temporary' AND source = 'closing' ")
						->runSelect()
						->getRow();
	}

	public function getJVDetails($voucherno){
		return $this->db->setTable('journaldetails')
						->setFields('linenum, accountcode, detailparticulars, debit, credit')
						->setWhere("voucherno = '$voucherno' AND stat = 'temporary' AND source = 'closing' ")
						->runPagination();
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
}	