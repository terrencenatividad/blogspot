<?
class financialsClass extends wc_model{	

	/**
	 * Update Balance Table
	 */
	public function updateBalanceTable()
	{ 
		
		$cmp				= COMPANYCODE;

		$balance_table		= 'balance_table';
		$financial_array	= array();

		$this->deleteRecord($balance_table," companycode = '$cmp' ");

		/**
		 * Data from Journal Voucher
		 */
	
		$journal_select_query = $this->db->setTable("journaldetails as dtl")
										->setFields(array("dtl.companycode","dtl.accountcode","dtl.voucherno","hdr.transtype",
											"dtl.linenum","dtl.detailparticulars","hdr.transactiondate","hdr.period","hdr.fiscalyear",
											"dtl.debit","dtl.credit"))
										->leftJoin("journalvoucher as hdr ON hdr.voucherno = dtl.voucherno AND hdr.companycode = '$cmp'")
										->setWhere("dtl.companycode = '$cmp' AND (hdr.stat = 'posted' OR hdr.stat = 'open')")
										->buildSelect(false);

		$journal_query_result = $this->db->setTable($balance_table)
		->setFields(array("companycode","accountcode","voucherno","transtype","linenum","detailparticulars","transactiondate","period","fiscalyear","debit","credit"))
		->setInsertSelect($journal_select_query)
		->runInsert(false);

		//companycode,accountcode,voucherno,partnercode,transtype,linenum,transactiondate,
		//period,fiscalyear,debit,credit
		/**
		 * Data from Accounts Payable
		 */

		$ap_select_query = $this->db->setTable("ap_details as dtl")
					->setFields(array("dtl.companycode","dtl.accountcode","dtl.voucherno","hdr.transtype",
						  "dtl.linenum","dtl.detailparticulars","hdr.transactiondate","hdr.period","hdr.fiscalyear",
						  "dtl.debit","dtl.credit"))
					->leftJoin("accountspayable as hdr ON hdr.voucherno = dtl.voucherno AND hdr.companycode = '$cmp'")
					->setWhere("dtl.companycode = '$cmp' AND (hdr.stat = 'posted' OR hdr.stat = 'open')")
					->buildSelect(false);

		$ap_query_result = $this->db->setTable($balance_table)
		->setFields(array("companycode","accountcode","voucherno","transtype","linenum","detailparticulars","transactiondate","period","fiscalyear","debit","credit"))
		->setInsertSelect($ap_select_query)
		->runInsert(false);

		/**
		 * Data from Accounts Receivable
		 */

		$ar_select_query = $this->db->setTable("ar_details as dtl")
					->setFields(array("dtl.companycode","dtl.accountcode","dtl.voucherno","hdr.transtype",
						  "dtl.linenum","dtl.detailparticulars","hdr.transactiondate","hdr.period","hdr.fiscalyear",
						  "dtl.debit","dtl.credit"))
					->leftJoin("accountsreceivable as hdr ON hdr.voucherno = dtl.voucherno AND hdr.companycode = '$cmp'")
					->setWhere("dtl.companycode = '$cmp' AND (hdr.stat = 'posted' OR hdr.stat = 'open')")
					->buildSelect(false);

		$ar_query_result = $this->db->setTable($balance_table)
		->setFields(array("companycode","accountcode","voucherno","transtype","linenum","detailparticulars","transactiondate","period","fiscalyear","debit","credit"))
		->setInsertSelect($ar_select_query)
		->runInsert(false);

		/**
		 * Data from Payment Voucher
		 */

		$pv_select_query = $this->db->setTable("pv_details as dtl")
					->setFields(array("dtl.companycode","dtl.accountcode","dtl.voucherno","hdr.transtype",
						  "dtl.linenum","dtl.detailparticulars","hdr.transactiondate","hdr.period","hdr.fiscalyear",
						  "dtl.debit","dtl.credit"))
					->leftJoin("paymentvoucher as hdr ON hdr.voucherno = dtl.voucherno AND hdr.companycode = '$cmp'")
					->setWhere("dtl.companycode = '$cmp' AND (hdr.stat = 'posted' OR hdr.stat = 'open')")
					->buildSelect(false);

		$pv_query_result = $this->db->setTable($balance_table)
		->setFields(array("companycode","accountcode","voucherno","transtype","linenum","detailparticulars","transactiondate","period","fiscalyear","debit","credit"))
		->setInsertSelect($pv_select_query)
		->runInsert(false);

		$rv_select_query = $this->db->setTable("rv_details as dtl")
					->setFields(array("dtl.companycode","dtl.accountcode","dtl.voucherno","hdr.transtype",
						  "dtl.linenum","dtl.detailparticulars","hdr.transactiondate","hdr.period","hdr.fiscalyear",
						  "dtl.debit","dtl.credit"))
					->leftJoin("receiptvoucher as hdr ON hdr.voucherno = dtl.voucherno AND hdr.companycode = '$cmp'")
					->setWhere("dtl.companycode = '$cmp' AND (hdr.stat = 'posted' OR hdr.stat = 'open')")
					->buildSelect(false);

		$rv_query_result = $this->db->setTable($balance_table)
		->setFields(array("companycode","accountcode","voucherno","transtype","linenum","detailparticulars","transactiondate","period","fiscalyear","debit","credit"))
		->setInsertSelect($rv_select_query)
		->runInsert(false);

		
    }

	private function deleteRecord($table,$condition){
		$this->db->setTable($table)
				 ->setWhere($condition)
				 ->runDelete();
	}
}