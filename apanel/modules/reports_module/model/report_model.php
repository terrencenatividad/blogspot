<?php
class report_model extends wc_model {

	public function generateBalanceTable() {
		$result = $this->db->setTable('balance_table')
							->setWhere('companycode IS NOT NULL')
							->runDelete();

		if ($result) {
			$fields = array(
				'a.voucherno',
				'a.transtype',
				'linenum',
				'accountcode',
				'period',
				'fiscalyear',
				'debit',
				'credit',
				'transactiondate',
				'a.companycode'
			);
			$fields2 = array(
				'voucherno',
				'transtype',
				'linenum',
				'accountcode',
				'period',
				'fiscalyear',
				'debit',
				'credit',
				'transactiondate',
				'companycode',
				'partnercode'
			);
			$ap = $this->db->setTable('accountspayable a')
							->innerJoin('ap_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
							->setFields(array_merge($fields, array('vendor')))
							->setWhere("a.stat = 'posted'")
							->buildSelect();
							
			$ar = $this->db->setTable('accountsreceivable a')
							->innerJoin('ar_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
							->setFields(array_merge($fields, array('customer')))
							->setWhere("a.stat = 'posted'")
							->buildSelect();
							
			$pv = $this->db->setTable('paymentvoucher a')
							->innerJoin('pv_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
							->setFields(array_merge($fields, array('vendor')))
							->setWhere("a.stat = 'posted'")
							->buildSelect();
							
			$rv = $this->db->setTable('receiptvoucher a')
							->innerJoin('rv_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
							->setFields(array_merge($fields, array('customer')))
							->setWhere("a.stat = 'posted'")
							->buildSelect();
							
			$jv = $this->db->setTable('journalvoucher a')
							->innerJoin('journaldetails b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
							->setFields(array_merge($fields, array('partner')))
							->setWhere("a.stat = 'posted'")
							->buildSelect();

			$inner_query = $ap . ' UNION ALL ' . $ar . ' UNION ALL ' . $pv . ' UNION ALL ' .$rv . ' UNION ALL ' . $jv;
			$result = $this->db->setTable('balance_table')
								->setFields($fields2)
								->setInsertSelect($inner_query)
								->runInsert(false);
		}
	}

	public function generateSalesReportsTable(){

		$result = $this->db->setTable('balance_table_sales')
		->setWhere('companycode IS NOT NULL')
		->runDelete();

		$fields 	=	array(	'si.companycode',
								'si.transactiondate',
								'si.voucherno',
								'ar.voucherno',
								'MONTH(si.transactiondate)',
								'YEAR(si.transactiondate)',
								'sid.linenum',
								'sid.itemcode',
								'itm.itemname',
								'class.label',
								'sid.issueqty',
								'base.uomdesc',
								'sid.convissueqty',
								'conv.uomdesc',
								'sid.conversion',
								'sid.unitprice',
								'sid.warehouse',
								'si.customer',
								'sect.partnername',
								'sid.discountedamount + sid.taxamount' );

		$columns 	=	array(	'companycode',
								'transactiondate',
								'invoiceno',
								'arno',
								'month',
								'year',
								'linenum',
								'itemcode',
								'itemname',
								'itemclass',
								'quantity',
								'uom',
								'convertedqty',
								'converteduom',
								'conversion',
								'unitprice',
								'warehouse',
								'customercode',
								'customer',
								'amount' );

		$si = $this->db->setTable('salesinvoice si')
						->leftJoin('salesinvoice_details sid ON si.companycode = sid.companycode AND si.voucherno = sid.voucherno')
						->leftJoin('accountsreceivable ar ON ar.invoiceno = si.voucherno AND ar.companycode = si.companycode AND ar.stat != "cancelled"')
						->leftJoin('partners sect ON sect.partnercode = si.customer AND sect.partnertype = "customer"')
						->leftJoin('items itm ON itm.itemcode = sid.itemcode AND itm.companycode = sid.companycode')
						->leftJoin('itemclass class ON class.id = itm.classid AND itm.companycode = class.companycode')
						->leftJoin('uom base ON base.uomcode = sid.issueuom AND base.companycode = sid.companycode')						
						->leftJoin('uom conv ON conv.uomcode = sid.convuom AND conv.companycode = sid.companycode')												
						->setFields($fields)
						->setWhere("(si.voucherno != '' AND si.voucherno != '-') AND si.stat NOT IN ('temporary', 'cancelled') ")
						->buildSelect();
					
		$result = $this->db->setTable('balance_table_sales')
						->setFields($columns)
						->setInsertSelect($si)
						->runInsert(false);
	
	}

	public function generatePurchaseReportsTable(){
		$result = $this->db->setTable('balance_table_purchase')
		->setWhere('companycode IS NOT NULL')
		->runDelete();

		$fields 	=	array(	'pr.companycode',
								'pr.transactiondate',
								'pr.voucherno',
								'ap.voucherno',
								'MONTH(pr.transactiondate)',
								'YEAR(pr.transactiondate)',
								'prd.linenum',
								'prd.itemcode',
								'itm.itemname',
								'class.label',
								'prd.receiptqty',
								'base.uomdesc',
								'prd.convreceiptqty',
								'conv.uomdesc',
								'prd.convuom',
								'prd.unitprice',
								'prd.warehouse',
								'sect.partnername',
								'pr.vendor',
								'prd.amount + prd.taxamount - prd.discountamount - prd.withholdingamount ' );

		$columns 	=	array(	'companycode',
								'transactiondate',
								'invoiceno',
								'arno',
								'month',
								'year',
								'linenum',
								'itemcode',
								'itemname',
								'itemclass',
								'quantity',
								'uom',
								'convertedqty',
								'converteduom',
								'conversion',
								'unitprice',
								'warehouse',
								'supplier',
								'suppliercode',
								'amount' );

		$pr = $this->db->setTable('purchasereceipt pr')
						->leftJoin('purchasereceipt_details prd ON pr.companycode = prd.companycode AND pr.voucherno = prd.voucherno')
						->leftJoin('accountspayable ap ON ap.invoiceno = pr.voucherno AND ap.companycode = pr.companycode AND ap.stat != "cancelled"')
						->leftJoin('partners sect ON sect.partnercode = pr.vendor AND sect.partnertype = "customer"')
						->leftJoin('items itm ON itm.itemcode = prd.itemcode AND itm.companycode = prd.companycode')
						->leftJoin('itemclass class ON class.id = itm.classid AND itm.companycode = class.companycode')
						->leftJoin('uom base ON base.uomcode = prd.receiptuom AND base.companycode = prd.companycode')						
						->leftJoin('uom conv ON conv.uomcode = prd.convuom AND conv.companycode = prd.companycode')												
						->setFields($fields)
						->setWhere("(pr.voucherno != '' AND pr.voucherno != '-') AND pr.stat NOT IN ('temporary', 'cancelled') ")
						->buildSelect();
			
		$result = $this->db->setTable('balance_table_purchase')
						->setFields($columns)
						->setInsertSelect($pr)
						->runInsert(false);
	}

}