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
							->leftJoin("purchasereceipt pr ON pr.source_no = ipo.voucherno")
							->leftJoin("job_details jd ON jd.ipo_no = pr.voucherno")
							->setWhere("ipo.voucherno != '' AND ipo.stat = 'posted' AND jd.job_no != ''")
							->setGroupBy("val")
							->setOrderBy("val")
							->runSelect()
							->getResult();

		return $result;
    }

	public function getUnitCostLanded($startdate, $enddate, $import_purchase_order, $supplier, $tab) {

		$fields = array (
			'ipod.voucherno ipo_num',
			'ipod.linenum',
			'ipod.detailparticular',
			'ipod.receiptuom',
			'ipod.receiptqty',
			'ipod.unitprice',
			'ipod.amount',
			'ipod.basecurrency',
			'ipod.exchangecurrency',
			'ipod.exchangerate',
			'ipod.convertedamount',
			'ipod.stat',
			'ipo.transactiondate',
			'ipo.freight',
			'ipo.insurance',
			'ipo.packaging',
			'ipo.netamount',
			'ipo.converted_freight',
			'ipo.converted_insurance',
			'ipo.converted_packaging',
			'pr.voucherno pr_vouch',
			'pr.transactiondate receiptdate',
			'jd.job_no',
			'jd.itemcode',
			'jd.qty',
			'i.itemname'
		);

		$cond_dates = ($startdate && $enddate) ? " AND ipo.transactiondate >= '$startdate' AND ipo.transactiondate <= '$enddate'" : "";
		$cond_ipo = ($import_purchase_order != "none" && $import_purchase_order != "") ? "AND ipod.voucherno = '$import_purchase_order'" : "";
		$cond_supplier = ($supplier != "none" && $supplier != "") ? " AND ipo.vendor = '$supplier'" : "";
		
		if ($tab == "Completed"){
			$cond_tab = "AND j.stat = 'closed'";
		}elseif ($tab == "Partial"){
			$cond_tab = "AND j.stat = 'on-going'";
		}else{
			$cond_tab = "AND j.stat != 'cancelled'";
		}

		$result = $this->db->setTable('job_details jd')
							->setFields($fields)
							->leftJoin('purchasereceipt pr ON pr.voucherno = jd.ipo_no')
							->leftJoin('import_purchaseorder_details ipod ON ipod.voucherno = pr.source_no AND ipod.itemcode = jd.itemcode')
							->leftJoin('purchasereceipt_details prd ON prd.voucherno = pr.voucherno')
							->leftJoin('import_purchaseorder ipo ON ipo.voucherno = ipod.voucherno')
							->leftJoin('partners p ON ipo.vendor = p.partnercode')
							->leftJoin('job j ON jd.job_no = j.job_no')
							->leftJoin('items i on i.itemcode = ipod.itemcode')
							->setWhere("ipod.stat = 'open' $cond_ipo $cond_supplier $cond_dates AND jd.job_no != '' $cond_tab")
							->setOrderBy('ipod.voucherno ASC, ipod.linenum ASC')
							->setGroupBy('ipod.voucherno, jd.job_no, i.itemcode')
							->runPagination();
							// echo $this->db->getQuery();

		return $result;
	}

	public function exportUnitCostLanded($startdate, $enddate, $import_purchase_order, $supplier, $tab) {
		$fields = array (
			'ipod.voucherno ipo_num',
			'ipod.linenum',
			'ipod.detailparticular',
			'ipod.receiptuom',
			'ipod.receiptqty',
			'ipod.unitprice',
			'ipod.amount',
			'ipod.basecurrency',
			'ipod.exchangecurrency',
			'ipod.exchangerate',
			'ipod.convertedamount',
			'ipod.stat',
			'ipo.transactiondate',
			'ipo.freight',
			'ipo.insurance',
			'ipo.packaging',
			'ipo.netamount',
			'ipo.converted_freight',
			'ipo.converted_insurance',
			'ipo.converted_packaging',
			'pr.voucherno pr_vouch',
			'pr.transactiondate receiptdate',
			'jd.job_no',
			'jd.itemcode',
			'jd.qty',
			'i.itemname'
		);

		$cond_dates = ($startdate && $enddate) ? " AND ipo.transactiondate >= '$startdate' AND ipo.transactiondate <= '$enddate'" : "";
		$cond_ipo = ($import_purchase_order != "none" && $import_purchase_order != "") ? "AND ipod.voucherno = '$import_purchase_order'" : "";
		$cond_supplier = ($supplier != "none" && $supplier != "") ? " AND ipo.vendor = '$supplier'" : "";
		
		if ($tab == "Completed"){
			$cond_tab = "AND j.stat = 'closed'";
		}elseif ($tab == "Partial"){
			$cond_tab = "AND j.stat = 'on-going'";
		}else{
			$cond_tab = "AND j.stat != 'cancelled'";
		}

		$result = $this->db->setTable('job_details jd')
							->setFields($fields)
							->leftJoin('purchasereceipt pr ON pr.voucherno = jd.ipo_no')
							->leftJoin('import_purchaseorder_details ipod ON ipod.voucherno = pr.source_no AND ipod.itemcode = jd.itemcode')
							->leftJoin('purchasereceipt_details prd ON prd.voucherno = pr.voucherno')
							->leftJoin('import_purchaseorder ipo ON ipo.voucherno = ipod.voucherno')
							->leftJoin('partners p ON ipo.vendor = p.partnercode')
							->leftJoin('job j ON jd.job_no = j.job_no')
							->leftJoin('items i on i.itemcode = ipod.itemcode')
							->setWhere("ipod.stat = 'open' $cond_ipo $cond_supplier $cond_dates AND jd.job_no != '' $cond_tab")
							->setOrderBy('ipod.voucherno ASC, ipod.linenum ASC')
							->setGroupBy('ipod.voucherno, jd.job_no, i.itemcode')
							->runSelect()
							->getResult();
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

		$result = $this->db->setTable('ap_details apd')
							->setFields('SUM(apd.converteddebit) AS debit')
							->leftJoin('accountspayable ap ON ap.voucherno = apd.voucherno')
							->setWhere("ap.job_no LIKE '%$job_no%'")
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

	public function getTotalCostOfJob($job_no) {
		$result = $this->db->setTable('job_details jd')
							->setFields('SUM(jd.qty * ipod.convertedamount) total')
							->innerJoin('purchasereceipt pr ON pr.voucherno = jd.ipo_no')
							->innerJoin('import_purchaseorder_details ipod ON ipod.voucherno = pr.source_no AND ipod.itemcode = jd.itemcode')
							->setWhere("jd.job_no = '$job_no'")
							->runSelect()
							->getRow();

		return $result;
	}

}	