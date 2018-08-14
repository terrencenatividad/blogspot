<?php
class bankrecon_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->log = new log();
	}

	public function cleanData($recon_id) {
		$config = $this->getConfigHeader($recon_id);
		$db = $this->getSystemListQuery($config, false);
		
		$query = $db->buildSelect();

		$query = $this->db->setTable('bankrecon_tagged bt')
							->leftJoin("($query) i ON bt.id = i.tagged_id AND bt.companycode = i.companycode")
							->setFields('bt.id')
							->setWhere("i.tagged_id IS NULL AND bt.recon_id = '$recon_id' AND bt.stat IN('matched', 'confirmed')")
							->buildSelect();

		$result = $this->db->setTable('bankrecon_tagged')
							->setWhere("id IN(SELECT * FROM ($query) i)")
							->runDelete();

		return $result;
	}

	public function cancelBankRecon($recon_id) {
		$result = $this->db->setTable('bankrecon')
							->setValues(array('stat' => 'cancelled'))
							->setWhere("id = '$recon_id'")
							->setLimit(1)
							->runUpdate();

		return $result;
	}

	public function getLastRecon() {
		$result = $this->db->setTable('bankrecon')
							->setFields('id, stat, periodfrom, periodto')
							->setOrderBy('id DESC')
							->setLimit(1)
							->runSelect()
							->getRow();

		return $result;
	}

	public function getLastReconClosed($accountcode) {
		$result = $this->db->setTable('bankrecon br')
							->innerJoin('chartaccount ca ON ca.id = br.accountcode AND ca.companycode = br.companycode')
							->setFields('periodfrom, periodto, accountname')
							->setWhere("stat = 'closed' AND accountcode = '$accountcode'")
							->setOrderBy('br.id DESC')
							->setLimit(1)
							->runSelect()
							->getRow();

		return $result;
	}

	public function finalizeBankRecon($recon_id) {
		$finalize	= false;
		$header		= $this->getHeaderValues($recon_id);
		$bank		= $header['balance_bank'] + $header['deposit_transit'] - $header['outstanding_cheques'];
		$book		= $header['balance_book'] + $header['unrecorded_deposit'] - $header['unrecorded_withdrawal'];
		$finalize	= $header['finalize'];

		$finalize = ($finalize) ? (round($bank, 2) == round($book, 2)) : $finalize;

		$result = false;

		if ($finalize) {
			$config = $this->getConfigHeader($recon_id);
			extract($config);

			$db = $this->getSystemListQuery($config, false);

			$result = $db->setFields('voucherno, transtype')
						->setWhere("stat = 'confirmed'")
						->runSelect()
						->getResult();

			$voucherno = array();

			foreach ($result as $row) {
				$voucherno[$row->transtype][] = $row->voucherno;
			}

			$result = ($result) ? false : true;

			foreach ($voucherno as $transtype => $id) {
				$table = 'journaldetails';
				$account_field = '';
				if ($transtype == 'JV') {
					$table = 'journaldetails';
					$account_field = 'accountcode';
					$stat_field = 'stat';
				} else if ($transtype == 'PV') {
					$table = 'pv_cheques';
					$account_field = 'chequeaccount';
					$stat_field = 'stat';
				} else if ($transtype == 'RV') {
					$table = 'rv_cheques';
					$account_field = 'chequeaccount';
					$stat_field = 'stat';
				} else if ($transtype == 'PVD') {
					$table = 'purchasereceipt';
					$account_field = '';
					$stat_field = 'checkstat';
				} else if ($transtype == 'RVD') {
					$table = 'receiptvoucher';
					$account_field = '';
					$stat_field = 'checkstat';
				}
				if ($table) {
					$ids = "'" . implode("', '", $id) . "'";
					$where = ($account_field) ? " AND $account_field = '$accountcode'" : '';
					$result = $this->db->setTable($table)
										->setValues(array($stat_field => 'cleared'))
										->setWhere("voucherno IN($ids)" . $where)
										->runUpdate();
				}
			}

			if ($result) {
				$result = $this->db->setTable('bankrecon')
									->setValues(array('stat' => 'closed', 'adjusted_balance' => $book))
									->setWhere("id = '$recon_id'")
									->setLimit(1)
									->runUpdate();
			}
		}

		return $result;
	}

	public function getHeaderValues($recon_id) {
		$finalize	= false;
		$bank		= $this->getBankListPagination($recon_id, true);
		$system		= $this->getSystemListPagination($recon_id, true);
		$matched	= $this->getMatchedListPagination($recon_id);

		if ($bank->result_count == 0 && $system->result_count == 0 && $matched->result_count == 0) {
			$finalize = true;
		}

		$ajax = array(
			'balance_bank'			=> $this->getBalancePerBank($recon_id),
			'balance_book'			=> $this->getBalancePerBook($recon_id),
			'deposit_transit'		=> $this->getTaggedTotal($recon_id, 'deposit_in_transit'),
			'unrecorded_deposit'	=> $this->getTaggedTotal($recon_id, 'unrecorded_deposit'),
			'outstanding_cheques'	=> $this->getTaggedTotal($recon_id, 'outstanding_cheque'),
			'unrecorded_withdrawal'	=> $this->getTaggedTotal($recon_id, 'unrecorded_withdrawal'),
			'finalize'				=> $finalize
		);

		return $ajax;
	}

	public function getTagged($tag, $recon_id) {
		$config = $this->getConfigHeader($recon_id);
		extract($config);
		
		// $fields		= 'id, r_transactiondate transactiondate, r_checkno checkno, r_amount amount';
		// if ($tag == 'unrecorded_withdrawal' || $tag == 'unrecorded_deposit') {
		// 	$fields = 'id, r_transactiondate transactiondate, r_checkno checkno, r_amount amount';
		// }

		if ($tag == 'unrecorded_withdrawal' || $tag == 'unrecorded_deposit') {
			$fields = 'id, r_transactiondate transactiondate, r_checkno checkno, r_amount amount, voucherno';
		}

		if ($tag == 'deposit_in_transit' || $tag == 'outstanding_cheque') {
			$fields = 'id, v_transactiondate transactiondate, r_checkno checkno, v_amount amount,voucherno';
		}


		$result = $this->db->setTable('bankrecon_tagged')
							->setFields("$fields")
							->setWhere("recon_id = '$recon_id' AND stat = '$tag'")
							->runSelect()
							->getResult();

		return $result;
	}
	
	public function getBankList() {
		$result = $this->db->setTable('chartaccount as chart')
							->setFields('chart.id ind, chart.accountname val, class.accountclass')
							->leftJoin('accountclass as class USING(accountclasscode)')
							->setWhere("(chart.id != '' AND chart.id != '-') AND class.accountclasscode = 'CASH' AND chart.accounttype != 'P'" )
							->setOrderBy('class.accountclass')
							->runSelect()
							->getResult();

		return $result;
	}

	public function saveBankReconCSV($details, $header) {
		$prev_recon_id = $this->getPreviousRecon($header['accountcode']);
		$result = $this->db->setTable('bankrecon_details bd')
							->innerJoin('bankrecon_tagged bt ON bd.id = bt.recdet_id AND bd.companycode = bt.companycode AND bt.recon_id = bd.recon_id')
							->setFields('transactiondate, checkno, description, debit, credit')
							->setWhere("bd.recon_id = '$prev_recon_id' AND bt.stat NOT IN('matched', 'confirmed', 'cancelled')")
							->runSelect()
							->getResult();
		
		if ($result) {
			foreach ($result as $row) {
				$details[] = array(
					'linenum'			=> '',
					'transactiondate'	=> $row->transactiondate,
					'checkno'			=> $row->checkno,
					'description'		=> $row->description,
					'debit'				=> $row->debit,
					'credit'			=> $row->credit,
				);
			}
		}

		$result = $this->db->setTable('bankrecon')
							->setValues($header)
							->runInsert();

		if ($result) {
			$insert_id = $this->db->getInsertId();
			foreach ($details as $key => $row) {
				$details[$key]['linenum']		= $key + 1;
				$details[$key]['recon_id']		= $insert_id;
				$details[$key]['accountcode']	= $header['accountcode'];
			}
			$result = $this->db->setTable('bankrecon_details')
								->setValues($details)
								->runInsert();
		}

		$this->autoMatch($insert_id);

		return $insert_id;
	}

	public function autoMatch($recon_id) {
		$bank_fields = array(
			'*',
			"IF(@amount != amount, @linenum:=0, '')",
			"IF(@checkno != checkno, @linenum:=0, '')",
			"IF(@nature != nature, @linenum:=0, '')",
			"@linenum := @linenum+1 linenum",
			"IF(@amount != amount, @amount:=amount, '')",
			"IF(@checkno != checkno, @checkno:=checkno, '')",
			"IF(@nature != nature, @nature:=nature, '')"
		);

		$system_fields = array(
			'*',
			"IF(@amount_x != amount, @linenum_x:=0, '')",
			"IF(@chequenumber_x != chequenumber, @linenum_x:=0, '')",
			"IF(@nature_x != nature, @linenum_x:=0, '')",
			"@linenum_x := @linenum_x+1 linenum",
			"IF(@amount_x != amount, @amount_x:=amount, '')",
			"IF(@chequenumber_x != chequenumber, @chequenumber_x:=chequenumber, '')",
			"IF(@nature_x != nature, @nature_x:=nature, '')"
		);

		$bank			= $this->getBankListQuery($recon_id);
		$bank_query 	= $bank->buildSelect();

		$bank_query		= $this->db->setTable("($bank_query) b, (select @linenum:=0, @amount:='', @checkno:='', @nature:='') conf")
									->setFields($bank_fields)
									->setWhere("b.recon_id = '$recon_id'")
									->setOrderBy('nature, amount DESC, checkno')
									->buildSelect(false);

		$config			= $this->getConfigHeader($recon_id);
		$system			= $this->getSystemListQuery($config);
		$system_query	= $system->buildSelect();

		$system_query		= $this->db->setTable("($system_query) b, (select @linenum_x:=0, @amount_x:='', @checkno_x:='', @nature_x:='') conf")
									->setFields($system_fields)
									->setOrderBy('nature, amount DESC, chequenumber')
									->buildSelect(false);

		$match_query	= $this->db->setTable("($bank_query) b")
									->innerJoin("($system_query) s ON b.checkno = s.chequenumber AND b.amount = s.amount AND b.nature = s.nature AND b.linenum = s.linenum")
									->setFields("'" . COMPANYCODE . "', b.recon_id, b.id recdet_id, b.transactiondate r_transactiondate, checkno r_checkno, b.amount r_amount, s.voucherno, s.transactiondate, s.chequenumber, s.amount, s.updatedate, 'matched' stat")
									->buildSelect(false);

		$result = $this->db->setTable('bankrecon_tagged')
							->setFields('companycode, recon_id, recdet_id, r_transactiondate, r_checkno, r_amount, voucherno, v_transactiondate, v_checkno, v_amount, v_updatedate, stat')
							->setInsertSelect($match_query)
							->runInsert(false);
	}

	public function getBankListPagination($recon_id, $no_previous = false) {
		$where = "b.id = '$recon_id' AND i.recdet_id IS NULL AND bt.id IS NULL";
		if ($no_previous) {
			$where .= " AND bd.transactiondate >= periodfrom AND bd.transactiondate <= periodto";
		}
		$db = $this->getBankListQuery($recon_id);

		$result = $db->setWhere($where)
						->runPagination();

		return $result;
	}

	public function getSystemListPagination($recon_id, $no_previous = false) {
		$config = $this->getConfigHeader($recon_id);
		extract($config);

		$where = "1 = 1";
		if ($no_previous) {
			$where = "transactiondate >= '$periodfrom' AND transactiondate <= '$periodto'";
		}

		$db = $this->getSystemListQuery($config);

		$result = $db->setWhere($where)
					->runPagination();

		return $result;
	}

	public function getMatchedListPagination($recon_id) {
		$fields = array(
			'id',
			'r_transactiondate',
			'r_checkno',
			'r_amount',
			'v_transactiondate',
			'v_checkno',
			'v_amount'
		);
		$config = $this->getConfigHeader($recon_id);
		$db = $this->getSystemListQuery($config, false);
		
		$query = $db->buildSelect();

		$result = $this->db->setTable('bankrecon_tagged bt')
							->innerJoin("($query) i ON bt.id = i.tagged_id AND bt.companycode = i.companycode")
							->setFields($fields)
							->setWhere("recon_id = '$recon_id' AND bt.stat = 'matched'")
							->runPagination();

		return $result;
	}

	public function getConfirmedListPagination($recon_id) {
		$fields = array(
			'id',
			'r_transactiondate',
			'r_checkno',
			'r_amount',
			'v_transactiondate',
			'v_checkno',
			'v_amount'
		);
		$config = $this->getConfigHeader($recon_id);
		$db = $this->getSystemListQuery($config, false);
		
		$query = $db->buildSelect();

		$result = $this->db->setTable('bankrecon_tagged bt')
							->innerJoin("($query) i ON bt.id = i.tagged_id AND bt.companycode = i.companycode")
							->setFields($fields)
							->setWhere("recon_id = '$recon_id' AND bt.stat = 'confirmed'")
							->runPagination();

		return $result;
	}

	public function confirmMatched($tagged_id) {
		$result = $this->db->setTable('bankrecon_tagged')
							->setValues(array('stat' => 'confirmed'))
							->setWhere("id = '$tagged_id'")
							->runUpdate();

		return $result;
	}

	public function removeMatched($tagged_id) {
		$result = $this->db->setTable('bankrecon_tagged')
							->setWhere("id = '$tagged_id'")
							->runDelete();

		return $result;
	}

	public function getForMatchingList($recdet_id) {
		$config = $this->getConfigDetails($recdet_id);
		extract($config);

		$where = '';
		if ($amount > 0) {
			$where = "amount = '$amount' AND nature = '$nature'";
		}
		if ($checkno) {
			$where .= (($where) ? " AND " : '') . "(chequenumber = '$checkno' OR chequenumber = '')";
		}

		$db = $this->getSystemListQuery($config);
		$result = $db->setWhere($where)
					->runSelect()
					->getResult();

		return (object) array(
			'result'			=> $result,
			'transaction_type'	=> $transaction_type
		);
	}

	public function getForMatchingList2($voucherno, $recon_id) {
		$config = $this->getConfigHeader($recon_id);

		$db = $this->getSystemListQuery($config);
		$result = $db->setWhere("voucherno = '$voucherno'")
					->setLimit(1)
					->runSelect()
					->getRow();
		extract($config);
		extract((array) $result);

		if (strtotime($transactiondate) >= strtotime($periodfrom) && strtotime($transactiondate) <= strtotime($periodto)) {
			$transaction_type = 'Current';
		} else {
			$transaction_type = 'Previous';
		}
			
		$db		= $this->getBankListQuery($recon_id);

		$where = '';
		if ($amount > 0) {
			$where .= " AND (debit = '$amount' OR credit = '$amount') AND IF(debit = 0, 'Expense', 'Income') = '$nature'";
		}
		if ($chequenumber) {
			$where .= " AND (checkno = '$chequenumber' OR checkno = '')";
		}
		$result = $db->setWhere("b.id = '$recon_id' AND i.recdet_id IS NULL AND bt.id IS NULL $where")
					->runSelect()
					->getResult();

		return (object) array(
			'result'			=> $result,
			'transaction_type'	=> $transaction_type
		);
	}

	public function setMatch($recdet_id, $voucherno) {
		$config = $this->getConfigDetails($recdet_id);

		$db = $this->getSystemListQuery($config);
		$voucher = $db->setWhere("voucherno = '$voucherno'")
					->setLimit(1)
					->runSelect()
					->getRow();

		$recon = $this->db->setTable('bankrecon_details')
						->setFields('transactiondate, checkno, IF(debit > 0, debit, credit) amount')
						->setWhere("id = '$recdet_id'")
						->setLimit(1)
						->runSelect()
						->getRow();

		$values = array(
			'recon_id'			=> $config['recon_id'],
			'recdet_id'			=> $recdet_id,
			'r_transactiondate' => $recon->transactiondate,
			'r_checkno'			=> $recon->checkno,
			'r_amount'			=> $recon->amount,
			'voucherno'			=> $voucher->voucherno,
			'v_transactiondate'	=> $voucher->transactiondate,
			'v_checkno'			=> $voucher->chequenumber,
			'v_amount'			=> $voucher->amount,
			'v_updatedate'		=> $voucher->updatedate,
			'stat'				=> 'matched'
		);

		$result = $this->db->setTable('bankrecon_tagged')
							->setValues($values)
							->runInsert();

		return $result;
	}

	public function tagUnrecordedDeposit($recdet_id) {
		$config = $this->getConfigDetails($recdet_id);

		$recon_id = (isset($config['recon_id'])) ? $config['recon_id'] : '';

		$db = $this->getBankListQuery($recon_id);

		$recon = $db->setWhere("b.id = '$recon_id' AND bd.id = '$recdet_id' AND i.recdet_id IS NULL")
						->runSelect()
						->getRow();

		$values = array(
			'recon_id'			=> $config['recon_id'],
			'recdet_id'			=> $recdet_id,
			'r_transactiondate' => $recon->transactiondate,
			'r_checkno'			=> $recon->checkno,
			'r_amount'			=> $recon->amount,
			'voucherno'			=> '',
			'v_transactiondate'	=> '',
			'v_checkno'			=> '',
			'v_amount'			=> '',
			'v_updatedate'		=> '',
			'stat'				=> 'unrecorded_deposit'
		);

		$result = $this->db->setTable('bankrecon_tagged')
							->setValues($values)
							->runInsert();

		return $result;
	}

	public function tagUnrecordedWithdrawal($recdet_id) {
		$config = $this->getConfigDetails($recdet_id);

		$recon_id = (isset($config['recon_id'])) ? $config['recon_id'] : '';

		$db = $this->getBankListQuery($recon_id);

		$recon = $db->setWhere("b.id = '$recon_id' AND bd.id = '$recdet_id' AND i.recdet_id IS NULL")
						->runSelect()
						->getRow();

		$values = array(
			'recon_id'			=> $config['recon_id'],
			'recdet_id'			=> $recdet_id,
			'r_transactiondate' => $recon->transactiondate,
			'r_checkno'			=> $recon->checkno,
			'r_amount'			=> $recon->amount,
			'voucherno'			=> '',
			'v_transactiondate'	=> '',
			'v_checkno'			=> '',
			'v_amount'			=> '',
			'v_updatedate'		=> '',
			'stat'				=> 'unrecorded_withdrawal'
		);

		$result = $this->db->setTable('bankrecon_tagged')
							->setValues($values)
							->runInsert();

		return $result;
	}

	public function tagDepositTransit($voucherno, $recon_id) {
		$config = $this->getConfigHeader($recon_id);

		$db = $this->getSystemListQuery($config);
		$voucher = $db->setWhere("voucherno = '$voucherno'")
					->setLimit(1)
					->runSelect()
					->getRow();

		$values = array(
			'recon_id'			=> $recon_id,
			'recdet_id'			=> '',
			'r_transactiondate' => '',
			'r_checkno'			=> '',
			'r_amount'			=> '',
			'voucherno'			=> $voucher->voucherno,
			'v_transactiondate'	=> $voucher->transactiondate,
			'v_checkno'			=> $voucher->chequenumber,
			'v_amount'			=> $voucher->amount,
			'v_updatedate'		=> $voucher->updatedate,
			'stat'				=> 'deposit_in_transit'
		);

		$result = $this->db->setTable('bankrecon_tagged')
							->setValues($values)
							->runInsert();

		return $result;
	}

	public function tagOutstandingCheque($voucherno, $recon_id) {
		$config = $this->getConfigHeader($recon_id);

		$db = $this->getSystemListQuery($config);
		$voucher = $db->setWhere("voucherno = '$voucherno'")
					->setLimit(1)
					->runSelect()
					->getRow();

		$values = array(
			'recon_id'			=> $recon_id,
			'recdet_id'			=> '',
			'r_transactiondate' => '',
			'r_checkno'			=> '',
			'r_amount'			=> '',
			'voucherno'			=> $voucher->voucherno,
			'v_transactiondate'	=> $voucher->transactiondate,
			'v_checkno'			=> $voucher->chequenumber,
			'v_amount'			=> $voucher->amount,
			'v_updatedate'		=> $voucher->updatedate,
			'stat'				=> 'outstanding_cheque'
		);

		$result = $this->db->setTable('bankrecon_tagged')
							->setValues($values)
							->runInsert();

		return $result;
	}

	private function getBalancePerBank($recon_id) {
		$result = $this->db->setTable('bankrecon')
							->setFields('endbalance')
							->setWhere("id = '$recon_id'")
							->setLimit(1)
							->runSelect()
							->getRow();
		
		
		$balance = ($result) ? $result->endbalance : 0;

		return $balance;
	}

	private function getBalancePerBook($recon_id) {
		$config = $this->getConfigHeader($recon_id);
		extract($config);

		$result = $this->db->setTable('balance_table')
							->setFields('(COALESCE(SUM(debit), 0, SUM(debit)) - COALESCE(SUM(credit), 0, SUM(credit))) as balance')
							->setWhere("accountcode = '$accountcode' AND transactiondate < '$periodfrom'")
							->runSelect()
							->getRow();

		$beg_balance = ($result) ? $result->balance : 0;

		$result = $this->db->setTable('balance_table')
							->setFields('(COALESCE(SUM(debit), 0, SUM(debit)) - COALESCE(SUM(credit), 0, SUM(credit))) as balance')
							->setWhere("accountcode = '$accountcode' AND transactiondate >= '$periodfrom' AND transactiondate <= '$periodto'")
							->runSelect()
							->getRow();

		$balance = ($result) ? $result->balance : 0;

		$balance += $beg_balance;

		return $balance;
	}

	private function getTaggedTotal($recon_id, $tag) {
		$config = $this->getConfigHeader($recon_id);
		extract($config);
		$date_field = 'v_transactiondate';
		$amount_field = 'v_amount';
		if ($tag == 'unrecorded_withdrawal' || $tag == 'unrecorded_deposit') {
			$date_field = 'r_transactiondate';
			$amount_field = 'r_amount';
		}

		$total = 0;

		$result = $this->db->setTable('bankrecon_tagged')
							->setFields("COALESCE(SUM($amount_field), 0, SUM($amount_field)) total")
							// ->setWhere("$date_field >= '$periodfrom' AND $date_field <= '$periodto' AND recon_id = '$recon_id' AND stat = '$tag'")
							->setWhere("recon_id = '$recon_id' AND stat = '$tag'")
							->runSelect()
							->getRow();

		if ($result) {
			$total = $result->total;
		}

		return $total;
	}

	private function getConfigHeader($id) {
		$result = $this->db->setTable('bankrecon')
							->setFields('accountcode, periodfrom, periodto')
							->setWhere("id = '$id'")
							->setLimit(1)
							->runSelect()
							->getRow();

		$config = array(
			'recon_id'		=> '',
			'accountcode'	=> '',
			'periodfrom'	=> '',
			'periodto'		=> '',
			'checkno'		=> '',
			'amount'		=> 0
		);
		if ($result) {
			$config = array(
				'recon_id'		=> $id,
				'accountcode'	=> $result->accountcode,
				'periodfrom'	=> $result->periodfrom,
				'periodto'		=> $result->periodto,
				'checkno'		=> '',
				'amount'		=> 0
			);
		}
		return $config;
	}

	private function getConfigDetails($id) {
		$result = $this->db->setTable('bankrecon_details bd')
							->innerJoin('bankrecon b ON b.id = bd.recon_id AND b.companycode = bd.companycode')
							->setFields("b.id, bd.accountcode, periodfrom, periodto, checkno, IF(debit > 0, debit, credit) amount, IF(debit = 0, 'Expense', 'Income') nature, IF(transactiondate >= periodfrom AND transactiondate <= periodto, 'Current', 'Previous') transaction_type")
							->setWhere("bd.id = '$id'")
							->setLimit(1)
							->runSelect()
							->getRow();

		$config = array(
			'recon_id'			=> '',
			'accountcode'		=> '',
			'periodfrom'		=> '',
			'periodto'			=> '',
			'checkno'			=> '',
			'nature'			=> '',
			'amount'			=> 0,
			'transaction_type'	=> $result->transaction_type
		);
		if ($result) {
			$config = array(
				'recon_id'			=> $result->id,
				'accountcode'		=> $result->accountcode,
				'periodfrom'		=> $result->periodfrom,
				'periodto'			=> $result->periodto,
				'checkno'			=> $result->checkno,
				'nature'			=> $result->nature,
				'amount'			=> $result->amount,
				'transaction_type'	=> $result->transaction_type
			);
		}
		return $config;
	}

	private function getBankListQuery($recon_id) {
		$fields = array(
			'bd.id id',
			'bd.recon_id',
			'bd.transactiondate',
			"IF(debit = 0, 'Expense', 'Income') nature",
			'checkno',
			"IF(debit = 0, credit, debit) amount"
		);
		$config = $this->getConfigHeader($recon_id);

		$db = $this->getSystemListQuery($config, false);
		$query = $db->buildSelect();

		$db = $this->db->setTable('bankrecon b')
							->innerJoin('bankrecon_details bd ON b.id = bd.recon_id AND b.companycode = bd.companycode')
							->leftJoin("($query) i ON bd.id = i.recdet_id AND bd.companycode = i.companycode")
							->leftJoin("bankrecon_tagged bt ON bt.recdet_id = bd.id AND bt.companycode = bd.companycode AND bt.stat NOT IN('matched', 'confirmed', 'cancelled') AND i.recdet_id IS NULL")
							->setFields($fields);

		return $db;
	}

	private function getSystemListQuery($config, $get_null = true) {
		extract($config);

		$prev_recon_id = $this->getPreviousRecon($accountcode);

		$result		= $this->db->setTable('bankrecon_tagged bt')
								->innerJoin('bankrecon b ON b.id = bt.recon_id AND b.companycode = bt.companycode')
								->setFields('voucherno')
								->setWhere("b.stat = 'closed' AND bt.stat NOT IN('outstanding_cheque', 'deposit_in_transit')")
								->runSelect()
								->getResult();

		$voucherno = array();

		foreach ($result as $row) {
			$voucherno[] = $row->voucherno;
		}

		$voucherno = "'" . implode("', '", $voucherno) . "'";

		$result1	= $this->db->setTable('journaldetails jd')
								->setFields("jv.companycode, jv.voucherno, '' chequenumber, IF(jd.debit > 0, jd.debit, jd.credit) amount, jv.transactiondate, jv.updatedate, IF(debit = 0, 'Expense', 'Income') nature, jd.transtype")
								->innerJoin('journalvoucher jv ON jd.voucherno = jv.voucherno AND jd.companycode = jv.companycode')
								->setWhere("jv.stat = 'posted' AND jv.transtype = 'JV' AND jd.accountcode = '$accountcode' AND jv.transactiondate <= '$periodto' AND jd.voucherno NOT IN($voucherno)")
								->buildSelect();

		$result2	= $this->db->setTable('rv_cheques rvc')
								->setFields("rvc.companycode, rvc.voucherno, rvc.chequenumber, rvc.chequeamount amount, rvc.chequedate transactiondate, rvc.updatedate, 'Income' nature, rvc.transtype")
								->innerJoin("receiptvoucher rv ON rvc.voucherno = rv.voucherno")
								->setWhere("chequeaccount = '$accountcode' AND rvc.stat = 'uncleared' AND rv.stat = 'posted' AND rvc.chequedate <= '$periodto' AND rvc.voucherno NOT IN($voucherno)")
								->buildSelect();

		$result3	= $this->db->setTable('pv_cheques pvc')
								->setFields("pvc.companycode, pvc.voucherno, pvc.chequenumber, pvc.chequeamount amount, pvc.chequedate transactiondate, pvc.updatedate, 'Expense' nature, pvc.transtype")
								->innerJoin('paymentvoucher pv ON pvc.voucherno = pv.voucherno AND pvc.companycode = pv.companycode')
								->setWhere("chequeaccount = '$accountcode'  AND pvc.stat = 'uncleared' AND pv.stat = 'posted' AND pvc.chequedate <= '$periodto' AND pvc.voucherno NOT IN($voucherno)")
								->buildSelect();

		$result4	= $this->db->setTable('rv_details rvd')
								->setFields("rvd.companycode, rvd.voucherno, '' chequenumber, IF(rvd.debit > 0, rvd.debit, rvd.credit) amount, rv.transactiondate transactiondate, rvd.updatedate, 'Income' nature, 'RVD' transtype")
								->innerJoin("receiptvoucher rv ON rvd.voucherno = rv.voucherno")
								->setWhere("accountcode = '$accountcode' AND rv.checkstat IN ('uncleared', 'unreleased') AND rv.stat = 'posted' AND rv.transactiondate <= '$periodto' AND rvd.voucherno NOT IN($voucherno) AND rv.paymenttype != 'cheque'")
								->buildSelect();

		$result5	= $this->db->setTable('pv_details pvd')
								->setFields("pvd.companycode, pvd.voucherno, '' chequenumber, IF(pvd.debit > 0, pvd.debit, pvd.credit) amount, pv.transactiondate transactiondate, pvd.updatedate, 'Expense' nature, 'PVD' transtype")
								->innerJoin('paymentvoucher pv ON pvd.voucherno = pv.voucherno AND pvd.companycode = pv.companycode')
								->setWhere("accountcode = '$accountcode'  AND pvd.stat = 'uncleared' AND pv.stat = 'posted' AND pv.transactiondate <= '$periodto' AND pvd.voucherno NOT IN($voucherno) AND pv.paymenttype != 'cheque'")
								->buildSelect();

		$query = $result1 . ' UNION ALL ' . $result2 . ' UNION ALL ' . $result3 . ' UNION ALL ' . $result4 . ' UNION ALL ' . $result5;

		$bankrecon = $this->db->setTable('bankrecon b')
								->innerJoin('bankrecon_tagged bt ON bt.recon_id = b.id AND bt.companycode = b.companycode')
								->setFields('bt.companycode, bt.recon_id, recdet_id, bt.id, bt.stat, bt.voucherno, bt.v_updatedate')
								->setWhere("accountcode = '$accountcode' AND (b.id = '$recon_id' OR b.id = 'closed')")
								->buildSelect();

		$where = ($get_null) ? 'b.id IS NULL' : 'b.id IS NOT NULL';

		$query2 = $this->db->setTable("($query) i")
							->leftJoin("($bankrecon) b ON i.voucherno = b.voucherno AND i.updatedate = b.v_updatedate AND i.companycode = b.companycode AND b.recon_id = '$recon_id'")
							->setFields('i.*, recdet_id, b.id tagged_id, b.stat')
							->setWhere($where)
							->buildSelect();

		$db = $this->db->setTable("($query2) i")
							->setFields('*')
							->setOrderBy('i.voucherno');

		return $db;
	}

	private function getPreviousRecon($accountcode) {
		$result = $this->db->setTable('bankrecon')
							->setFields('id')
							->setWhere("stat = 'closed' AND accountcode = '$accountcode'")
							->setOrderBy('id DESC')
							->setLimit(1)
							->runSelect()
							->getRow();

		$id = ($result) ? $result->id : '';

		return $id;
	}

}