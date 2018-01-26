<?php
class financial_model extends wc_model {

	private $header_table			= '';
	private $detail_table			= '';
	private $source_header_table	= '';
	private $source_detail_table	= '';
	private $voucherno				= '';
	private $vouchertype			= '';
	private $primaryaccount			= '';
	private $secondaryaccount		= '';
	private $taxaccount				= '';
	private $accounttype			= '';
	private $accounttype2			= '';
	private $partnertype			= '';
	private $reverse				= false;
	private $add_field				= array();

	public function setSourceHeaderTable($table) {
		$this->source_header_table = $table;
		return $this;
	}

	public function setSourceDetailTable($table) {
		$this->source_detail_table = $table;
		return $this;
	}

	public function generateAP($voucherno) {
		$this->voucherno			= $voucherno;
		$this->header_table			= 'accountspayable';
		$this->detail_table			= 'ap_details';
		$this->vouchertype			= 'AP';

		$this->add_field			= array('invoiceno');

		$this->source_header_table	= ($this->source_header_table) ? $this->source_header_table : 'purchasereceipt';
		$this->source_detail_table	= ($this->source_detail_table) ? $this->source_detail_table : 'purchasereceipt_details';
		
		$this->initPurchase();

		$result = ( ! empty($this->source_header_table) && ! empty($this->source_detail_table));
		if ($result) {
			$result = $this->generateAccount();
		}

		return $result;
	}

	public function generateAR($voucherno) {
		$this->voucherno			= $voucherno;
		$this->header_table			= 'accountsreceivable';
		$this->detail_table			= 'ar_details';
		$this->vouchertype			= 'AR';

		$this->source_header_table	= ($this->source_header_table) ? $this->source_header_table : 'salesinvoice';
		$this->source_detail_table	= ($this->source_detail_table) ? $this->source_detail_table : 'salesinvoice_details';

		$this->initSales();

		$result = ( ! empty($this->source_header_table) && ! empty($this->source_detail_table));
		if ($result) {
			$result = $this->generateAccount();
		}

		return $result;
	}

	public function generateDM($voucherno) {
		$this->voucherno			= $voucherno;
		$this->header_table			= 'journalvoucher';
		$this->detail_table			= 'journaldetails';
		$this->vouchertype			= 'DM';

		$this->source_header_table	= ($this->source_header_table) ? $this->source_header_table : 'salesreturn';
		$this->source_detail_table	= ($this->source_detail_table) ? $this->source_detail_table : 'salesreturn_details';

		$this->setReverse();
		$this->initSales();

		$result = ( ! empty($this->source_header_table) && ! empty($this->source_detail_table));
		if ($result) {
			$result = $this->generateAccount();
		}

		return $result;
	}

	public function generateCM($voucherno) {
		$this->voucherno			= $voucherno;
		$this->header_table			= 'journalvoucher';
		$this->detail_table			= 'journaldetails';
		$this->vouchertype			= 'CM';

		$this->source_header_table	= ($this->source_header_table) ? $this->source_header_table : 'purchasereturn';
		$this->source_detail_table	= ($this->source_detail_table) ? $this->source_detail_table : 'purchasereturn_details';

		$this->setReverse();
		$this->initPurchase();

		$result = ( ! empty($this->source_header_table) && ! empty($this->source_detail_table));
		if ($result) {
			$result = $this->generateAccount();
		}

		return $result;
	}

	public function cancelAP($voucherno) {
		$this->voucherno			= $voucherno;
		$this->header_table			= 'accountspayable';
		$this->detail_table			= 'ap_details';

		$result = $this->cancelAccount();

		return $result;
	}

	public function cancelAR($voucherno) {
		$this->voucherno			= $voucherno;
		$this->header_table			= 'accountsreceivable';
		$this->detail_table			= 'ar_details';
		
		$result = $this->cancelAccount();

		return $result;
	}

	public function cancelDM($voucherno) {
		$this->voucherno			= $voucherno;
		$this->header_table			= 'journalvoucher';
		$this->detail_table			= 'journaldetails';
		
		$result = $this->cancelAccount();

		return $result;
	}

	public function cancelCM($voucherno) {
		$this->voucherno			= $voucherno;
		$this->header_table			= 'journalvoucher';
		$this->detail_table			= 'journaldetails';
		
		$result = $this->cancelAccount();

		return $result;
	}

	private function initSales() {
		$this->primaryaccount			= 'receivable_account';
		$this->secondaryaccount			= 'revenue_account';
		$this->taxaccount				= 'salesAccount';

		$this->accounttype				= 'debit';
		$this->accounttype2				= 'credit';
		$this->partnertype				= 'customer';
	}

	private function initPurchase() {
		$this->primaryaccount			= 'payable_account';
		$this->secondaryaccount			= 'expense_account';
		$this->taxaccount				= 'purchaseAccount';

		$this->accounttype				= 'credit';
		$this->accounttype2				= 'debit';
		$this->partnertype				= 'vendor';
	}

	private function setReverse() {
		$this->reverse = true;
	}

	private function generateAccount() {
		$header = $this->getHeaderSource();
		$details = $this->getDetailSource();
		if ($header && $details) {
			$check_exist	= $this->checkExist();
			if ( ! $check_exist) {
				$seq		= new seqcontrol();
				$voucherno	= $seq->getValue($this->vouchertype);
			} else {
				$voucherno	= $check_exist->voucherno;
			}
			$result = $this->saveHeader($header, $voucherno, $check_exist);
			if ($result) {
				$result = $this->saveDetail($details, $voucherno, $check_exist);
			}
		} else {
			$result = false;
		}

		return $result;
	}

	private function cancelAccount() {
		$check_exist	= $this->checkExist();
		$result			= false;
		if ($check_exist) {
			$values = array('stat' => 'cancelled');
			$result = $this->db->setTable($this->header_table)
								->setValues($values)
								->setWhere("voucherno = '{$check_exist->voucherno}'")
								->setLimit(1)
								->runUpdate();

			if ($result) {
				$result = $this->db->setTable($this->detail_table)
									->setValues($values)
									->setWhere("voucherno = '{$check_exist->voucherno}'")
									->runUpdate();
			}
		}

		return $result;
	}

	private function checkExist() {
		$result = $this->db->setTable($this->header_table)
							->setFields('voucherno')
							->setWhere("sourceno = '{$this->voucherno}'")
							->setLimit(1)
							->runSelect()
							->getRow();

		return $result;
	}

	private function saveHeader($header, $voucherno, $check_exist) {
		$data				= array(
			'voucherno'			=> $voucherno,
			'currencycode'		=> 'PHP',
			'referenceno'		=> $this->voucherno,
			'invoiceno'			=> (isset($header->invoiceno)) ? $header->invoiceno : $this->voucherno,
			'exchangerate'		=> '1',
			'stat'				=> 'posted',
			'transtype'			=> $this->vouchertype,
			'transactiondate'	=> $header->transactiondate,
			'duedate'			=> $header->transactiondate,
			$this->partnertype	=> $header->{$this->partnertype},
			'amount'			=> $header->netamount,
			'convertedamount'	=> $header->netamount,
			'period'			=> date('m'),
			'fiscalyear'		=> date('Y'),
			'balance'			=> $header->netamount,
			'source'			=> '',
			'sourceno'			=> $this->voucherno
		);

		if ($this->header_table != 'journalvoucher') {
			$data['invoicedate'] = $header->transactiondate;
		}

		$this->db->setTable($this->header_table)
					->setWhere("sourceno = '{$this->voucherno}'")
					->setValues($data);
		
		if ($check_exist) {
			$result = $this->db->runUpdate();
		} else {
			$result = $this->db->runInsert();
		}
		
		return $result;
	}

	private function saveDetail($detail, $voucherno, $check_exist) {
		$data = array(
			'voucherno'			=> $voucherno,
			'source'			=> $this->voucherno,
			'transtype'			=> $this->vouchertype,
			'currencycode'		=> 'PHP',
			'exchangerate'		=> '1',
			'stat'				=> 'posted'
		);
		$linenum = 0;
		foreach ($detail['accountcode'] as $row) {
			$linenum++;
			$data['linenum'][] = $linenum;
		}
		$data = array_merge($detail, $data);

		
		if ($check_exist) {
			$result = $this->db->setTable($this->detail_table)
								->setWhere("source = '{$this->voucherno}'")
								->runDelete();
		}

		$result = $this->db->setTable($this->detail_table)
							->setValuesFromPost($data)
							->runInsert();

		return $result;
	}

	private function getHeaderSource() {
		$fields = array(
			$this->partnertype,
			'transactiondate',
			'voucherno',
			'netamount',
			'period',
			'fiscalyear',
			'wtaxcode',
			'wtaxamount',
			'purchaseAccount',
			'salesAccount'
		);
		$fields = array_merge($fields, $this->add_field);
		$result = $this->db->setTable($this->source_header_table . ' dr')
							->leftJoin('fintaxcode f ON f.fstaxcode = dr.wtaxcode AND f.companycode = dr.companycode')
							->setFields($fields)
							->setWhere("voucherno = '{$this->voucherno}'")
							->setLimit(1)
							->runSelect()
							->getRow();

		return $result;
	}

	private function getDetailSource() {
		$fields = array(
			'IF(i.receivable_account > 0, i.receivable_account, ic.receivable_account) receivable_account',
			'IF(i.revenue_account > 0, i.revenue_account, ic.revenue_account) revenue_account',
			'IF(i.expense_account > 0, i.expense_account, ic.expense_account) expense_account',
			'IF(i.payable_account > 0, i.payable_account, ic.payable_account) payable_account',
			'drd.amount',
			'drd.taxamount',
			'drd.taxcode',
			'wtaxamount',
			'wtaxrate',
			'dr.discountamount',
			'IF(f.purchaseAccount > 0, f.purchaseAccount, f2.purchaseAccount) purchaseAccount',
			'IF(f.salesAccount > 0, f.salesAccount, f2.salesAccount) salesAccount',
			'f3.purchaseAccount purchaseAccount2',
			'f3.salesAccount salesAccount2',
			'f4.purchaseAccount discountAccount'
		);
		$result = $this->db->setTable($this->source_detail_table . ' drd')
							->innerJoin($this->source_header_table . ' dr ON dr.voucherno = drd.voucherno AND dr.companycode = drd.companycode')
							->innerJoin('items i ON i.itemcode = drd.itemcode AND i.companycode = drd.companycode')
							->innerJoin('itemclass ic ON ic.id = i.classid AND ic.companycode = i.companycode')
							->leftJoin('fintaxcode f ON f.fstaxcode = drd.taxcode AND f.companycode = drd.companycode')
							->leftJoin('fintaxcode f2 ON f2.fstaxcode = drd.taxcode AND f2.companycode = drd.companycode')
							->leftJoin('fintaxcode f3 ON f3.fstaxcode = dr.wtaxcode AND f3.companycode = drd.companycode')
							->leftJoin("fintaxcode f4 ON f4.companycode = drd.companycode AND f4.fstaxcode = 'DC'")
							->setFields($fields)
							->setWhere("drd.voucherno = '{$this->voucherno}'")
							->runSelect()
							->getResult();

		$total_amount = 0;

		foreach ($result as $row) {
			$total_amount += $row->amount;
		}

		$data		= array();
		$dc_add		= true;
		if ($result && $total_amount > 0) {
			foreach ($result as $row) {
				$primary_amount = $row->amount - (($row->amount / $total_amount) * $row->discountamount);
				if ($this->header_table != 'accountspayable') {
					$this->addAccount($data, 'primaryaccount', $row->{$this->primaryaccount}, $primary_amount - ($row->amount * $row->wtaxrate) + $row->taxamount);
				}
				$this->addAccount($data, 'secondaryaccount', $row->{$this->secondaryaccount}, $row->amount);
				if ($row->discountamount && $dc_add) {
					$this->addAccount($data, 'discount', $row->discountAccount, $row->discountamount);
					$dc_add = false;
				}
				if ($row->{$this->taxaccount} && $row->taxamount) {
					$this->addAccount($data, 'taxaccount', $row->{$this->taxaccount}, $row->taxamount);
				}
				if ($row->{$this->taxaccount . '2'} && $row->wtaxamount) {
					$this->addAccount($data, 'wtaxaccount', $row->{$this->taxaccount . '2'}, $row->amount * $row->wtaxrate);
				}
				if ($this->header_table == 'accountspayable') {
					$this->addAccount($data, 'primaryaccount', $row->{$this->primaryaccount}, $primary_amount - ($row->amount * $row->wtaxrate) + $row->taxamount);
				}
			}
		}
		$data2 = array();
		foreach ($data as $type => $row) {
			foreach ($row as $key => $value) {
				$data2['accountcode'][]		= $key;
				$data2['debit'][]			= $this->getDebit($type, $value);
				$data2['credit'][]			= $this->getCredit($type, $value);
				$data2['converteddebit'][]	= $this->getDebit($type, $value);
				$data2['convertedcredit'][]	= $this->getCredit($type, $value);
			}
		}

		return $data2;
	}

	private function getDebit($type, $value) {
		$accounttype = ($type == 'primaryaccount' || $type == 'wtaxaccount' || $type == 'discount') ? 'accounttype' : 'accounttype2';
		if ($this->reverse) {
			$accounttype = ($accounttype == 'accounttype2') ? 'accounttype' : 'accounttype2';
		}
		return ($this->$accounttype == 'debit') ? $value : 0;
	}

	private function getCredit($type, $value) {
		$accounttype = ($type == 'primaryaccount' || $type == 'wtaxaccount' || $type == 'discount') ? 'accounttype' : 'accounttype2';
		if ($this->reverse) {
			$accounttype = ($accounttype == 'accounttype2') ? 'accounttype' : 'accounttype2';
		}
		return ($this->$accounttype == 'credit') ? $value : 0;
	}

	private function addAccount(&$data, $type, $account, $value) {
		if ($value > 0) {
			if (isset($data[$type]) && isset($data[$type][$account])) {
				$data[$type][$account] += $value;
			} else {
				$data[$type][$account] = $value;
			}
		}
	}

}
