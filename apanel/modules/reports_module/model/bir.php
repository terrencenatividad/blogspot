<?php
class bir extends wc_model {

    public function getCompanyInfo($fields) {
        return $this->db->setTable('company')
        ->setFields($fields)
        ->setWhere("companycode != ''")
        ->setLimit('1')
        ->runSelect()
        ->getRow();
    }

    public function getQuarterlyRemittance($data){
        $year           = $data['year'];
        $quarter        = $data['quarter'];
        $months         = '';
        switch ($quarter) {
            case 1:
            $months = "period IN(1,2,3)";
            break;
            
            case 2:
            $months = "period IN(4,5,6)";
            break;
            case 3:
            $months = "period IN(7,8,9)";
            break;
            case 4:
            $months = "period IN(10,11,12)";
            break;
            default:
            $months = "";
            break;
        }
        $company_info   = $this->getCompanyInfo('wtax_option');
        $wtax_option    = $company_info->wtax_option;
        if($wtax_option == 'AP'){
            $result = $this->db->setTable("ap_details apd")
            ->setFields(
                array(
                    'apv.period period',
                    'atc.atc_code atccode',
                    'SUM(apd.taxbase_amount) taxbase',
                    'atc.tax_rate taxrate',
                    'apd.credit taxwithheld'
                )
            )
            ->leftJoin("atccode atc ON atc.atcId = apd.taxcode")
            ->leftJoin("accountspayable apv ON apv.voucherno = apd.voucherno")
            ->setGroupBy("apv.period, atc.atc_code")
            ->setWhere(" (apd.taxcode IS NOT NULL AND apd.taxcode != '') AND apv.fiscalyear = '$year' AND apv.$months ")
            ->setOrderBy("atc.tax_rate")
            ->runSelect()
            ->getResult();
        }else if($wtax_option == 'PV'){
            $result = $this->db->setTable("pv_details apd")
            ->setFields(
                array(
                    'apv.period period',
                    'atc.atc_code atccode',
                    'SUM(apd.taxbase_amount) taxbase',
                    'atc.tax_rate taxrate',
                    'apd.credit taxwithheld'
                )
            )
            ->leftJoin("atccode atc ON atc.atcId = apd.taxcode")
            ->leftJoin("paymentvoucher apv ON apv.voucherno = apd.voucherno")
            ->setGroupBy("apv.period, atc.atc_code")
            ->setWhere(" (apd.taxcode IS NOT NULL AND apd.taxcode != '') AND apv.fiscalyear = '$year' AND apv.$months ")
            ->setOrderBy("atc.tax_rate")
            ->runSelect()
            ->getResult();
        }
        return $result;
    }

    public function getWithheld($data) {
        $year       = $data['year'];
        $quarter    = $data['quarter']; 
    }

    public function getPrivate($period) {
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('items AS i ON sd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(si.amount) as sum_amount , SUM(si.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_private' AND si.period IN $period")
        ->runSelect()
        ->getRow();
    }

    public function getGov($period) {
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('items AS i ON sd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(si.amount) as sum_amount , SUM(si.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_gov' AND si.period IN $period")
        ->runSelect()
        ->getRow();
    }

    public function getZero($period) {
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('items AS i ON sd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(si.amount) as sum_amount , SUM(si.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_zero' AND si.period IN $period")
        ->runSelect()
        ->getRow();
    }

    public function getExempt($period) {
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('items AS i ON sd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(si.amount) as sum_amount , SUM(si.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_exempt' AND si.period IN $period ")
        ->runSelect()
        ->getRow();
    }

    public function getNotPurchasesExceeded($period) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_not_exceed' AND pr.period IN $period ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchasesExceeded($period) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_exceed' AND pr.period IN $period ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchaseGoods($period) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_domestic_goods' AND pr.period IN $period ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchaseImport($period) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_import_goods' AND pr.period IN $period ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchaseServices($period) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_domestic_services' AND pr.period IN $period ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchaseNonResident($period) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_non_resident' AND pr.period IN $period ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchaseNotTax($period) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_not' AND pr.period IN $period ")
        ->runSelect()
        ->getRow();
    }

    public function getPrivateMonthly($period) {
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('items AS i ON sd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(si.amount) as sum_amount , SUM(si.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_private' AND si.period = '$period'")
        ->runSelect()
        ->getRow();
    }

    public function getGovMonthly($period) {
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('items AS i ON sd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(si.amount) as sum_amount , SUM(si.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_gov' AND si.period = '$period'")
        ->runSelect()
        ->getRow();
    }

    public function getZeroMonthly($period) {
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('items AS i ON sd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(si.amount) as sum_amount , SUM(si.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_zero' AND si.period = '$period")
        ->runSelect()
        ->getRow();
    }

    public function getExemptMonthly($period) {
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('items AS i ON sd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(si.amount) as sum_amount , SUM(si.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_exempt' AND si.period = '$period'")
        ->runSelect()
        ->getRow();
    }

     public function getNotPurchasesExceededMonthly($period) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_not_exceed' AND pr.period IN $period ")
        ->runSelect()
        ->getRow();
    }

     public function getPurchasesExceededMonthly($period) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_exceed' AND pr.period IN $period ")
        ->runSelect()
        ->getRow();
    }

     public function getPurchaseGoodsMonthly($period) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_domestic_goods' AND pr.period IN $period ")
        ->runSelect()
        ->getRow();
    }

     public function getPurchaseImportMonthly($period) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_import_goods' AND pr.period IN $period ")
        ->runSelect()
        ->getRow();
    }

     public function getPurchaseServicesMonthly($period) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_domestic_services' AND pr.period IN $period ")
        ->runSelect()
        ->getRow();
    }

     public function getPurchaseNonResidentMonthly($period) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_non_resident' AND pr.period IN $period ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchaseNotTaxMonthly($period) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_not' AND pr.period IN $period ")
        ->runSelect()
        ->getRow();
    }

    public function getATCCode(){
        return $this->db->setTable('atccode')
        ->setFields('atc_code ind,atc_code val, stat stat')
        ->setWhere("tax_type = 'PT'")
        ->runSelect()
        ->getResult();
    }

    public function retrieveATCDetails($atc_code,$month){
        if($month=='1'){
            $monthlist	= " AND period IN('1','2','3')";
        }else if($month=='2'){
            $monthlist	= " AND period IN('4','5','6') ";
        }else if($month=='3'){
            $monthlist	= " AND period IN('7','8','9') ";
        }else if($month=='4'){
            $monthlist	= " AND period IN('10','11','12') ";
        }

        return $this->db->setTable('atccode a')
        ->setFields('atc_code, tax_rate, SUM(s.amount) taxamount')
        ->leftJoin('salesinvoice_details s ON s.companycode = a.companycode')
        ->leftJoin('salesinvoice si ON si.companycode = s.companycode AND si.voucherno = s.voucherno')
        ->setWhere("tax_type = 'PT' AND atc_code = '$atc_code' $monthlist")
        ->runSelect()
        ->getRow();
    }

    public function retrieveWTAX($data,$i){
        $year = $data['year'];
        
        return $this->db->setTable('ap_details a')
                ->setFields('SUM(a.taxbase_amount) tax')
                ->leftJoin('accountspayable ap ON ap.voucherno = a.voucherno')
                ->setWhere("MONTH(transactiondate) = '$i' and YEAR(transactiondate) = '$year'")
                ->runSelect()
                ->getResult();
    }

    public function getTotalRemittance(){
        $ap	= $this->db->setTable('ap_details')
								->setFields(array('credit'))
								->setWhere("taxcode IS NOT NULL AND taxcode != ''")
                                ->buildSelect();

        $pv	= $this->db->setTable('pv_details')
								->setFields(array('credit'))
								->setWhere("taxcode IS NOT NULL AND taxcode != ''")
                                ->buildSelect();
        
        $union = $ap . ' UNION ALL ' . $pv;
        
        $result = $this->db->setTable("($union) u")
                        ->setFields('SUM(credit) total')
                        ->setWhere(1)
                        ->runSelect(false)
                        ->getResult();
        return $result[0]->total;
    }
}	