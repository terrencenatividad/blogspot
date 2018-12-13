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
				'a.companycode',
				'a.source',
				'a.stat'
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
				'source',
				'stat',
				'partnercode',
			);
			$ap = $this->db->setTable('accountspayable a')
							->innerJoin('ap_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
							->setFields(array_merge($fields, array('vendor')))
							->setWhere("a.stat IN('open','posted','cancelled')")
							->buildSelect();
							
			$ar = $this->db->setTable('accountsreceivable a')
							->innerJoin('ar_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
							->setFields(array_merge($fields, array('customer')))
							->setWhere("a.stat IN('open','posted','cancelled')")
							->buildSelect();
							
			$pv = $this->db->setTable('paymentvoucher a')
							->innerJoin('pv_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
							->setFields(array_merge($fields, array('vendor')))
							->setWhere("a.stat IN('open','posted','cancelled')")
							->buildSelect();
							
			$rv = $this->db->setTable('receiptvoucher a')
							->innerJoin('rv_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
							->setFields(array_merge($fields, array('customer')))
							->setWhere("a.stat IN('open','posted','cancelled')")
							->buildSelect();
							
			$jv = $this->db->setTable('journalvoucher a')
							->innerJoin('journaldetails b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
							->setFields(array_merge($fields, array('partner')))
							->setWhere("a.stat IN('open','posted','cancelled')")
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

	// public function saveAssetHistoryActivity($activity,$data) {
	// 	// $data1['asset_number']	  = $data['asset_number'];
	// 	// $data1['serial_number']	  = $data['serial_number'];
	// 	$data1['transactiondate'] = date('Y-m-d H:i:s');
	// 	$data1['transactiontype'] = $activity;
	// 	// $data1['transferto'] 	  = $data['accountable_person'].' - '.$data['asset_location'];
	// 	return $this->db->setTable('asset_history')
	// 					->setValues($data1)
	// 					->runInsert();
	// }

	// public function saveAssetTransactionActivity($activity,$data) {
	// 	// $data1['asset_number']	  = $data['asset_number'];
	// 	// $data1['serial_number']	  = $data['serial_number'];
	// 	$data1['transactiondate'] = date('Y-m-d H:i:s');
	// 	$data1['transactiontype'] = $activity;
	// 	// $data1['transferto'] 	  = $data['accountable_person'].' - '.$data['asset_location'];
	// 	return $this->db->setTable('asset_transaction')
	// 					->setValues($data1)
	// 					->runInsert();
	// }
	public function generateAssetActivity(){
	$result = $this->db->setTable('asset_transaction')
		->setWhere('companycode IS NOT NULL')
		->runDelete();

		$pofields 	=	array(	'po.companycode',
								'pod.voucherno',
								'"Create Purchase Order"',
								'am.asset_class',
								'am.asset_number',
								'am.sub_number',
								'po.entereddate',
								'pod.amount',
								'CONCAT(am.asset_location ," - ", am.accountable_person)'
							);
		$prfields 	=	array(	'pr.companycode',
								'prd.voucherno',
								'"Received Asset"',
								'am.asset_class',
								'am.asset_number',
								'am.sub_number',
								'pr.entereddate',
								'prd.amount',
								'CONCAT(am.asset_location ," - ", am.accountable_person)'
							);
		$apfields 	=	array(	'ap.companycode',
								'apd.voucherno',
								'"Repairs"',
								'am.asset_class',
								'am.asset_number',
								'am.sub_number',
								'ap.entereddate',
								'apd.debit',
								'CONCAT(am.asset_location ," - ", am.accountable_person)'
							);

		$columns 	=	array(	'companycode',
								'voucherno',
								'transactiontype',
								'asset_class',
								'asset_number',
								'sub_number',
								'transactiondate',
								'amount',
								'transferto'
							);

		$po = $this->db->setTable('purchaseorder po')
						->leftJoin('purchaseorder_details pod ON po.companycode = pod.companycode AND po.voucherno = pod.voucherno')
						->leftJoin('items i ON i.itemcode = pod.itemcode')
						->leftJoin('itemtype it ON it.id = i.typeid')
						->leftJoin('asset_master am ON am.itemcode = i.itemcode')
						->setFields($pofields)
						->setWhere("po.stat IN('open','posted','cancelled') AND it.label = 'Fixed Asset' AND EXISTS (SELECT *  FROM asset_master am WHERE am.itemcode = i.itemcode)")
						->buildSelect();

		$pr = $this->db->setTable('purchasereceipt pr')
						->leftJoin('purchasereceipt_details prd ON pr.companycode = prd.companycode AND pr.voucherno = prd.voucherno')
						->leftJoin('items i ON i.itemcode = prd.itemcode')
						->leftJoin('itemtype it ON it.id = i.typeid')
						->leftJoin('asset_master am ON am.itemcode = i.itemcode')
						->setFields($prfields)
						->setWhere("pr.stat IN('received','cancelled') AND it.label = 'Fixed Asset' AND EXISTS (SELECT *  FROM asset_master am WHERE am.itemcode = i.itemcode)")
						->buildSelect();

		$ap = $this->db->setTable('accountspayable ap')
						->leftJoin('ap_details apd ON ap.companycode = apd.companycode AND ap.voucherno = apd.voucherno')
						->leftJoin('chartaccount coa ON coa.id = apd.accountcode')
						->leftJoin('asset_master am ON am.asset_number = ap.assetid')
						->setFields($apfields)
						->setWhere("ap.stat IN('open','posted','cancelled') AND accountname = 'Fixed Asset'")
						->buildSelect();
						
						
		$union = $po . ' UNION ALL ' . $pr. ' UNION ALL ' . $ap;
		$result = $this->db->setTable('asset_transaction')
						->setFields($columns)
						->setInsertSelect($union)
						->runInsert(false);
	}
}