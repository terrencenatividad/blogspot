<?php
class landed_cost extends wc_model {

    public function getSupplierList() {
		$result = $this->db->setTable('partners p')
							->setFields("DISTINCT p.partnercode ind, p.partnername val")
							->leftJoin("import_purchaseorder ipo ON p.partnercode = ipo.vendor")
							->setWhere("p.partnercode != '' AND p.partnertype = 'supplier' AND p.stat = 'active' AND ipo.stat = 'posted'")
							->setGroupBy("val")
							->setOrderBy("val")
							->runSelect()
							->getResult();

		return $result;
    }
    
    public function getImportPurchaseOrderList() {
		$result = $this->db->setTable('import_purchaseorder ipo')
							->setFields("ipo.voucherno ind, ipo.voucherno val")
							// ->leftJoin("accountspayable ap ON p.partnercode = ap.vendor")
							->leftJoin("job_details jd ON jd.ipo_no = ipo.voucherno")
							->setWhere("ipo.voucherno != '' AND ipo.stat = 'posted' AND jd.job_no != ''")
							->setGroupBy("val")
							->setOrderBy("val")
							->runSelect()
							->getResult();

		return $result;
    }

	public function getUnitCostLanded($startdate, $enddate, $import_purchase_order, $supplier, $tab) {
		$fields = array (
			'ipo_d.companycode',
			'ipo_d.voucherno',
			'ipo_d.linenum',
			'jd.itemcode',
			'ipo_d.detailparticular',
			'ipo_d.receiptuom',
			'ipo_d.receiptqty',
			'ipo_d.unitprice',
			'ipo_d.amount',
			'ipo_d.basecurrency',
			'ipo_d.exchangecurrency',
			'ipo_d.exchangerate',
			'ipo_d.convertedamount',
			'ipo.transactiondate',
			'ipo.fiscalyear',
			'ipo.period',
			'ipo.stat',
			'ipo.netamount',
			'ipo.freight',
			'ipo.insurance',
			'ipo.packaging',
			'ipo.converted_freight',
			'ipo.converted_insurance',
			'ipo.converted_packaging',
            'p.partnername',
			'p.partnercode',
			'jd.job_no',
			'jd.qty',
			'pr.voucherno AS receiptno',
			'pr.transactiondate AS receiptdate',
			'i.itemname',
			'j.stat AS job_status'
		);
		$cond_dates = ($startdate && $enddate) ? " AND ipo.transactiondate >= '$startdate' AND ipo.transactiondate <= '$enddate'" : "";
		$cond_ipo = ($import_purchase_order != "none" && $import_purchase_order != "") ? "AND ipo_d.voucherno = '$import_purchase_order'" : "";
		$cond_supplier = ($supplier != "none" && $supplier != "") ? " AND ipo.vendor = '$supplier'" : "";
		
		if ($tab == "Completed"){
			$cond_tab = "AND j.stat = 'closed'";
		}elseif ($tab == "Partial"){
			$cond_tab = "AND j.stat = 'on-going'";
		}else{
			$cond_tab = "AND j.stat != 'cancelled'";
		}
		// echo $cond_tab;

		$result = $this->db->setTable('job_details jd')
							->setFields($fields)
							->leftJoin('import_purchaseorder_details ipo_d ON ipo_d.voucherno = jd.ipo_no AND ipo_d.itemcode = jd.itemcode')
							->leftJoin('import_purchaseorder ipo ON ipo.voucherno = jd.ipo_no')
							->leftJoin('partners p ON ipo.vendor = p.partnercode')
							->leftJoin('purchasereceipt pr ON pr.source_no = ipo.voucherno')
							->leftJoin("items i ON i.itemcode = ipo_d.itemcode")
							->leftJoin('job j ON jd.job_no = j.job_no')
							->setWhere("ipo_d.stat = 'open' $cond_ipo $cond_supplier $cond_dates AND jd.job_no != '' $cond_tab")
							->setOrderBy('jd.ipo_no ASC, ipo_d.linenum ASC')
							->runPagination();
							// echo $this->db->getQuery();

		return $result;
	}

	public function getJobsOfIpo($import_purchase_order){
		$result = $this->db->setTable('job_ipo ji')
							->setFields('ji.job_no AS job_numbers')
							->setWhere("ji.voucher_no = '$import_purchase_order'")
							->runSelect()
							->getRow();
		
		return $result;
	}

	public function getSumOfAp($job_no) {

		$result = $this->db->setTable('ap_details ad')
							->setFields('SUM(ad.converteddebit) AS debit')
							->setWhere("ad.job_no LIKE '%$job_no%'")
							->runSelect()
							->getRow();
							// echo $this->db->getQuery();

		return $result;
	}

	public function getTotalItemsInJob($job_no) {
		$result = $this->db->setTable('job_details jd')
							->setFields('SUM(qty) AS qty')
							->setWhere("jd.job_no = '$job_no'")
							->runSelect()
							->getRow();
		
		return $result;
	}

	// public function getJobNo($import_purchase_order, $item_code) {
	// 	$fields = array(
	// 		'jd.ipo_no',
	// 		'jd.itemcode',
	// 		'jd.job_no',
	// 		'jd.qty'
	// 	);

	// 	$result = $this->db->setTable('job_details jd')
	// 						->setFields($fields)
	// 						->setWhere('jd.itemcode = "$item_code" AND jd.ipo_no = "$import_purchase_order"')
	// 						->runSelect();

	// 	return $result;
	// }

}	