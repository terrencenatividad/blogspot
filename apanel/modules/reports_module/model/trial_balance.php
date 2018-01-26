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

	public function getTrialBalance($currentyear,$prevyear)
	{

		$result = $this->db->setTable("chartaccount as chart")
						->setFields(array("chart.id as accountid","chart.segment5 as accountcode","chart.accountname as accountname"))
						->leftJoin("balance_table as bal ON bal.accountcode = chart.id")
						->setWhere("YEAR(bal.transactiondate) >= $prevyear 
		AND YEAR(bal.transactiondate) <= $currentyear")
						->setGroupBy("chart.segment5")
						->setOrderBy("chart.segment5 ASC")
						->runPagination();
		return $result;
	}

	public function fileExport($currentyear,$prevyear)
	{

		$result = $this->db->setTable("chartaccount as chart")
						->setFields(array("chart.id as accountid","chart.segment5 as accountcode","chart.accountname as accountname"))
						->leftJoin("balance_table as bal ON bal.accountcode = chart.id")
						->setWhere("YEAR(bal.transactiondate) >= $prevyear 
		AND YEAR(bal.transactiondate) <= $currentyear")
						->setGroupBy("chart.segment5")
						->setOrderBy("chart.segment5 ASC")
						->runSelect()
						->getResult();
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

		// $tablerow		= "";
 
		// $begQuery       = "";
		// $mainQuery		= "";
		// $query_table    = "";
		// $query_fields   = "";
		// $query_cond     = "";
		// $query_groupby  = "";
		// $query_orderby  = "";
		/**DATE FILTER**/
		// if(!empty($datefilter) && $qtr == 0){
		// 	$transactdateFrom 	 	= date("Y-m-d",strtotime($dateArr[0]));
		// 	$transactdateTo 	 	= date("Y-m-d",strtotime($dateArr[1]));

		// 	$mainQuery 		.= " AND ((bal.period >= MONTH('$transactdateFrom') 
		// 						   and bal.period <= MONTH('$transactdateTo')) 
		// 						   AND (bal.fiscalyear >= YEAR('$transactdateFrom') 
		// 						   and bal.fiscalyear <= YEAR('$transactdateTo'))) 
		// 						   and (bal.transactiondate >= '$transactdateFrom' 
		// 						   and bal.transactiondate <= '$transactdateTo') ";
		// }else{
		// 	if($qtr == 1){
		// 		$mainQuery 		.= " AND ((bal.period >= '1' and bal.period <= '3') 
		// 							and bal.fiscalyear = YEAR('$datefilter')) ";
		// 	}else if($qtr == 2){
		// 		$mainQuery 		.= " AND ((bal.period >= '4' and bal.period <= '6') 
		// 							and bal.fiscalyear = YEAR('$datefilter')) ";
		// 	}else if($qtr == 3){
		// 		$mainQuery 		.= " AND ((bal.period >= '7' and bal.period <= '9') and bal.fiscalyear = YEAR('$datefilter')) ";
		// 	}else if($qtr == 4){
		// 		$mainQuery 		.= " AND ((bal.period >= '10' and bal.period <= '12') and bal.fiscalyear = YEAR('$datefilter')) ";
		// 	}else if($qtr == $year_1){
		// 		$mainQuery 		.= " AND (bal.fiscalyear = '$year_1') ";
		// 	}else if($qtr == $year_2){
		// 		$mainQuery 		.= " AND (bal.fiscalyear = '$year_2') ";
		// 	}else if($qtr == $year_3){
		// 		$mainQuery 		.= " AND (bal.fiscalyear = '$year_3') ";
		// 	}else if(!empty($dateArr)){
		// 		$transactdateFrom 	 	= date("Y-m-d",strtotime($dateArr[0]));
		// 		$transactdateTo 	 	= date("Y-m-d",strtotime($dateArr[1]));
				
		// 		$begQuery		.= " AND ((MONTH(chart.entereddate) >= MONTH('$transactdateFrom') and MONTH(chart.entereddate) <= MONTH('$transactdateTo')) and (YEAR(chart.entereddate) >= YEAR('$transactdateFrom') and YEAR(chart.entereddate) <= YEAR('$transactdateTo'))) and (DATE_FORMAT(chart.entereddate,'%Y-%m-%d') >= '$transactdateFrom' and DATE_FORMAT(chart.entereddate,'%Y-%m-%d') <= '$transactdateTo') ";

		// 		$mainQuery 		.= " AND ((bal.period >= MONTH('$transactdateFrom') and bal.period <= MONTH('$transactdateTo')) and (bal.fiscalyear >= YEAR('$transactdateFrom') and bal.fiscalyear <= YEAR('$transactdateTo'))) and (bal.transactiondate >= '$transactdateFrom' and bal.transactiondate <= '$transactdateTo') ";
		// 	}
		// }

		/**ITEM FILTER**/
	// if(!empty($acctfilter)){
	// 	$mainQuery 		.= " AND bal.accountcode = '$acctfilter' ";
	// }
	// $query_table         = "balance_table bal";
	// $query_fields        = array('bal.voucherno as voucherno',
	//                              'bal.transactiondate as transactiondate',
	// 							 'bal.debit as debit',
	// 							 'bal.credit as credit',
	// 							 'bal.transtype as transtype');


	// $query_cond				.= "bal.companycode='".COMPANYCODE."' $mainQuery";
	
	// $query_groupby			.= " bal.companycode, bal.voucherno";
	
	// $query_orderby			.= (!empty($sort) && !empty($sortBy)) ? " $sort $sortBy " : "";

	//$fetch_result			= $this->retrieveData($query_table, $query_fields, $query_cond,"", $query_orderby, $query_groupby);

	// var_dump($fetch_result);
	//$transactdateFrom_ =  date("Y-m-d",strtotime($dateArr[0]));

	//$balquery   = "";

	// $chart = "";
	// $chart_fields = array("DATE_FORMAT(chart.entereddate,'%Y-%m-%d') as transactiondate",
	// 				      "YEAR(chart.entereddate) as fiscalyear",
	// 					  "MONTH(chart.entereddate) as period",
	// 					  "chart.companycode as companycode",
	// 					  "chart.accountname as particulars",
	// 					  "chart.companycode referenceno",
	// 					  "chart.companycode voucherno",
	// 					  "chart.companycode partner",
	// 					  "'BEG' as transtype",
	// 					  "chart.segment5 linenum",
	// 					  "chart.generalledgercode costcentercode",
	// 					//   "chart.segment5 accountcode",
	// 					//   "chart.segment5 account",
	// 					  "chart.id accountcode",
	// 					  "chart.id account",
	// 					  "'DEBIT' as debit",
	// 					  "'CREDIT' as credit",
	// 					  "'PHP' as currencycode",
	// 					  "chart.companycode as taxcode",
	// 					  "chart.companycode vatflg",
	// 					  "chart.accountname detailparticulars",
	// 					  "chart.entereddate entereddate",
	// 					  "chart.enteredby enteredby",
	// 					  "chart.updatedate updatedate",
	// 					  "chart.updateby updateby",
	// 					  "chart.updateprogram updateprogram",
	// 					  "'STAT' as stat",
	// 					  "chart.companycode sitecode"
	// 					  );
						  
	//$chart = $this->buildQuery("chartaccount chart", $chart_fields,"");

	// $ap = "";
	// $ap =	$this->db->setTable("ap_details ad")
	// 				->setFields(array("am.invoicedate","am.fiscalyear fiscalyear","am.period period",
	// 								 "am.companycode companycode","am.particulars particulars",
	// 								 "am.referenceno referenceno","am.voucherno voucherno",
	// 								 "am.vendor partner","am.transtype transtype","ad.linenum linenum","ad.costcentercode costcentercode","ad.accountcode accountcode",
	// 								 "(select acct.segment5 from chartaccount acct where acct.id = ad.accountcode) as account",
	// 								 "ad.debit debit","ad.credit credit","ad.currencycode currencycode","ad.taxcode taxcode","ad.vatflg vatflg",
	// 								 "ad.detailparticulars detailparticulars","ad.entereddate entereddate","ad.enteredby enteredby","ad.updatedate updatedate",
	// 								 "ad.updateby updateby","ad.updateprogram updateprogram",
	// 								 "am.stat stat","am.sitecode sitecode"))
	// 				->leftJoin("accountspayable am ON am.voucherno = ad.voucherno and am.companycode = '".COMPANYCODE."'")
	// 				->setWhere("ad.companycode = '".COMPANYCODE."' AND am.companycode='".COMPANYCODE."' AND am.stat='posted'")
	// 				->buildSelect(false);

	//$ar = "";
	// $ar =	$this->db->setTable("ar_details rd")
	// 				->setFields(array("rm.invoicedate transactiondate","rm.fiscalyear fiscalyear","rm.period period","rm.companycode companycode","rm.particulars particulars","rm.referenceno referenceno", "rm.voucherno voucherno","rm.customer partner","rm.transtype transtype","rd.linenum linenum","rd.costcentercode costcentercode",
	// 				"rd.accountcode accountcode","(select acct.segment5 from chartaccount acct where acct.id = rd.accountcode) as account", "rd.debit debit", "rd.credit credit","rd.currencycode currencycode", "rd.taxcode taxcode","rd.vatflg vatflg", "rd.detailparticulars detailparticulars","rd.entereddate entereddate", "rd.enteredby enteredby","rd.updatedate updatedate","rd.updateby updateby", "rd.updateprogram updateprogram","rm.stat stat","rm.sitecode sitecode"))
	// 				->leftJoin("accountsreceivable rm ON rm.voucherno = rd.voucherno and rm.companycode = '".COMPANYCODE."'")
	// 				->setWhere("rd.companycode = '".COMPANYCODE."' AND rm.companycode='".COMPANYCODE."' AND rm.stat='posted'")
	// 				->buildSelect(false);

	// $pv = "";
	// $pv =	$this->db->setTable("pv_details pd")
	// 				->setFields(array("pm.transactiondate transactiondate","pm.fiscalyear fiscalyear","pm.period period","pm.companycode companycode","pm.particulars particulars","pm.referenceno referenceno", "pm.voucherno voucherno","pm.vendor partner","pm.transtype transtype","pd.linenum linenum","pd.costcentercode costcentercode",
	// 				"pd.accountcode accountcode","(select acct.segment5 from chartaccount acct where acct.id = pd.accountcode) as account","pd.debit debit","pd.credit credit","pd.currencycode currencycode","pd.taxcode taxcode","pd.vatflg vatflg", "pd.detailparticulars detailparticulars","pd.entereddate entereddate", "pd.enteredby enteredby","pd.updatedate updatedate","pd.updateby updateby", "pd.updateprogram updateprogram","pm.stat stat","'sitecode' as sitecode"))
	// 				->leftJoin("paymentvoucher pm ON pm.voucherno = pd.voucherno and pm.companycode = '".COMPANYCODE."'")
	// 				->setWhere("pd.companycode = '".COMPANYCODE."' AND 
	// 				pm.companycode='".COMPANYCODE."' AND pm.stat='posted'")
	// 				->buildSelect(false);

	// $rv = "";
	// $rv =	$this->db->setTable("rv_details vd")
	// 				->setFields(array("vm.transactiondate transactiondate","vm.fiscalyear fiscalyear","vm.period period","vm.companycode companycode","vm.particulars particulars","vm.referenceno referenceno", "vm.voucherno voucherno","vm.customer partner","vm.transtype transtype","vd.linenum linenum","vd.costcentercode costcentercode",
	// 				"vd.accountcode accountcode","(select acct.segment5 from chartaccount acct where acct.id = vd.accountcode) as account","vd.debit debit","vd.credit credit","vd.currencycode currencycode","vd.taxcode taxcode","vd.vatflg vatflg", "vd.detailparticulars detailparticulars","vd.entereddate entereddate", "vd.enteredby enteredby","vd.updatedate updatedate","vd.updateby updateby", "vd.updateprogram updateprogram","vm.stat stat","vm.sitecode sitecode"))
	// 				->leftJoin("receiptvoucher vm ON vm.voucherno = vd.voucherno and vm.companycode = '".COMPANYCODE."'")
	// 				->setWhere("vd.companycode = '".COMPANYCODE."' AND 
	// 				vm.companycode='".COMPANYCODE."' AND vm.stat='posted'")
	// 				->buildSelect(false);
                 
	// $jv = "";
	// $jv =	$this->db->setTable("journaldetails jd")
	// 				->setFields(array("jm.documentdate transactiondate","jm.fiscalyear fiscalyear","jm.period period","jm.companycode companycode","jm.particulars particulars","jm.referenceno referenceno", "jm.voucherno voucherno","jm.transtype partner","jm.transtype transtype","jd.linenum linenum","jd.costcentercode costcentercode",
	// 				"jd.accountcode accountcode","(select acct.segment5 from chartaccount acct where acct.id = jd.accountcode) as account","jd.debit debit","jd.credit credit","jd.currencycode currencycode","jd.taxcode taxcode","jd.vatflg vatflg", "jd.detailparticulars detailparticulars","jd.entereddate entereddate", "jd.enteredby enteredby","jd.updatedate updatedate","jd.updateby updateby", "jd.updateprogram updateprogram","jm.stat stat","jm.sitecode sitecode"))
	// 				->leftJoin("journalvoucher jm ON jm.voucherno = jd.voucherno and jm.companycode = '".COMPANYCODE."'")
	// 				->setWhere("jd.companycode = '".COMPANYCODE."' AND 
	// 				jm.companycode='".COMPANYCODE."' AND jm.stat='posted'")
	// 				->buildSelect(false);

	// $allQuery_fields = array("particulars","transactiondate","companycode","accountcode","SUM(debit) as debit","SUM(credit) as credit","detailparticulars","transtype","voucherno","stat","account");
	// $allQuery_table  = "($chart union $ap union $ar union $pv union $rv union $jv) gldetails";
	
	// if(!empty($transactdateFrom_)){	 		
	//  	$balquery .= " AND transactiondate < '$transactdateFrom_' ";
	// }

	// $beg_balance_query_table = $allQuery_table;
	// $beg_balance_query_fields = $allQuery_fields;
	// $beg_balance_query_condition  = "companycode='".COMPANYCODE."' AND (stat = 'posted' OR stat = 'active')".$balquery;

	$link		= '';
	$tablerow   = '';
	//retrieve beginning balance
	// $accntTotalDebitBeg 	= 0;
	// $accntTotalCreditBeg 	= 0;
	// $arrDebit 	= $this->getAccountCodeBeg($beg_balance_query_table,$beg_balance_query_fields,$beg_balance_query_condition,$acctfilter,"debit");
	// $arrCredit	= $this->getAccountCodeBeg($beg_balance_query_table,$beg_balance_query_fields,$beg_balance_query_condition,$acctfilter,"credit");
	// $accntTotalDebitBeg 	= $arrDebit["throw"];
	// $accntTotalCreditBeg 	= $arrCredit["throw"];
	// $debit_transactiondate  =date("M d, Y",strtotime($arrDebit["transactiondate"]));
	// $credit_transactiondate = date("M d, Y",strtotime($arrCredit["transactiondate"]));

	// if($accntTotalDebitBeg > $accntTotalCreditBeg)
	// 	{
	// 		$accntTotalDebitBeg = $accntTotalDebitBeg - $accntTotalCreditBeg;
	// 		$accntTotalCreditBeg = 0;
	// 	}
	// 	else{
	// 		$accntTotalCreditBeg = $accntTotalCreditBeg - $accntTotalDebitBeg;
	// 		$accntTotalDebitBeg = 0;
	// 	}

	// if($accntTotalDebitBeg > 0 || $accntTotalCreditBeg > 0)
	// {	
	// $tablerow	.= '<tr>';
	// $tablerow	.= '<td style="vertical-align:middle;" >&nbsp;BEG_BALANCE</td>';
	// if($accntTotalDebitBeg > $accntTotalCreditBeg)
	// {
	// $tablerow	.= '<td class=" center" style="vertical-align:middle;" >&nbsp;'.$debit_transactiondate .'</td>';
	// }else{
	// $tablerow	.= '<td class=" center" style="vertical-align:middle;" >&nbsp;'.$credit_transactiondate.'</td>';
	// }
	// $tablerow	.= '<td class=" right" style="vertical-align:middle;" >'.number_format($accntTotalDebitBeg,2).'</td>';
	// $tablerow	.= '<td class=" right" style="vertical-align:middle;" >'.number_format($accntTotalCreditBeg,2).'</td>';
	// $tablerow	.= '</tr>';
	// }
		// if(!empty($fetch_result))
		// {
		// 	for($i=0;$i < count($fetch_result);$i++)
		// 	{	
		// 		$voucher			= $fetch_result[$i]->voucherno;
		// 		$transactiondate	= $fetch_result[$i]->transactiondate;
		// 		$transactiondate	= date("M d, Y",strtotime($transactiondate));
		// 		$debit				= $fetch_result[$i]->debit;
		// 		$credit				= $fetch_result[$i]->credit;
		// 		$transtype			= $fetch_result[$i]->transtype;
				
		// 		$documentno = '';
		// 		$source     = '';

		// 		if($transtype == 'AP'){
			
		// 			$documentno	= $this->getValue("accountspayable",array("sourceno"),"voucherno = '$voucher'");
		// 			$documentno = $documentno[0]->sourceno;
		// 			$source		= $this->getValue("accountspayable",array("source"),"voucherno = '$voucher'");
		// 			$source     = $source[0]->source;
		// 			//$modtype	= ($source == 'EXP') ? "bills" : "purchase_voucher";
		// 			$link		= '<a href="'.BASE_URL.'financials/accounts_payable/print_preview/'.$documentno .'" target="_blank">'.$documentno.'</a>';
					
		// 		}else if($transtype == 'PV'){
		// 			// $link		= '<a href="' .BASE_URL.'modules/purchase/csv/payment_voucher.php?sid='.$voucher) .'" target="_blank">'.$voucher.'</a>';
		// 			$link  = '<a href="#" target="_blank">'.$voucher.'</a>';
		// 		}else if($transtype == 'AR'){
		// 			$documentno = $this->getValue("accountsreceivable",array("sourceno")," voucherno = '$voucher'");
		// 			$documentno = $documentno[0]->sourceno;
		// 			$source 	= $this->getValue("accountsreceivable",array("source")," voucherno = '$voucher'");
		// 			$source     = $source[0]->source;
		// 			$link		= '<a href="' .BASE_URL.'financials/accounts_receivable/print_preview/'.$documentno .'" target="_blank">'.$documentno.'</a>';
		// 		}else if($transtype == 'RV'){
		// 			$link		= '<a href="' . BASE_URL. 'financials/receipt_voucher/print_preview/'.$voucher .'" target="_blank">'.$voucher.'</a>';
		// 		}else if($transtype == 'JV'){
		// 			$link		= '<a href="' . BASE_URL .'financials/journal_voucher/print_preview/'.$voucher .'" target="_blank">'.$voucher.'</a>';
		// 		}else if($transtype == 'BEG'){
		// 			$link		= $documentno;
		// 		}else if($transtype == 'BEG'){
		// 			$link		= $voucher;
		// 		}

		// 		$tablerow	.= '<tr>';
		// 		$tablerow	.= '<td style="vertical-align:middle;" >&nbsp;'.$link.'</td>';
		// 		$tablerow	.= '<td class=" center" style="vertical-align:middle;" >&nbsp;'.$transactiondate.'</td>';
		// 		$tablerow	.= '<td class=" right" style="vertical-align:middle;" >'.number_format($debit,2).'</td>';
		// 		$tablerow	.= '<td class=" right" style="vertical-align:middle;" >'.number_format($credit,2).'</td>';
		// 		$tablerow	.= '</tr>';
			
		// 	}
		// }else{
		// 	$tablerow	.= '<tr>';
		// 	$tablerow	.= '<td class="center" colspan="4">- No Records Found -</td>';
		// 	$tablerow	.= '</tr>';
		// }
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
						// echo $this->db->getQuery();
		//var_dump($this->db->getQuery());
						
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

}