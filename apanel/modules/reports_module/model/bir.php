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
            ->setWhere(" (apd.taxcode IS NOT NULL AND apd.taxcode != '') AND apv.fiscalyear = '$year' AND apv.$months AND apv.stat = 'posted'")
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

    public function getPrivate($period, $fiscalyear) {
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('fintaxcode AS tax ON tax.fstaxcode = sd.taxcode')
        ->setFields('SUM(si.amount) as sum_amount , SUM(si.taxamount) as sum_taxamount')
        ->setWhere("sd.taxcode IN('VATG','VATS') AND si.period = '$period' AND si.stat NOT IN('cancelled','temporary') AND si.fiscalyear = '$fiscalyear' ")
        ->runSelect()
        ->getRow();
    }

    public function getGov($period, $fiscalyear) {
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('fintaxcode AS tax ON tax.fstaxcode = sd.taxcode')
        ->setFields('SUM(sd.amount) as sum_amount , SUM(sd.taxamount) as sum_taxamount')
        ->setWhere("sd.taxcode = 'SG' AND si.period = '$period' AND si.stat NOT IN('cancelled','temporary') AND si.fiscalyear = '$fiscalyear' ")
        ->runSelect()
        ->getRow();
    }

    public function getZero($period, $fiscalyear) {
        // return $this->db->setTable('salesinvoice_details as sd')
        // ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        // ->leftJoin('items AS i ON sd.itemcode = i.itemcode')
        // ->leftJoin('itemclass as ic ON i.classid = ic.id')
        // ->setFields('SUM(si.amount) as sum_amount , SUM(si.taxamount) as sum_taxamount')
        // ->setWhere("ic.expensetype = 'vat_zero' AND si.period = '$period'")
        // ->runSelect()
        // ->getRow();
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('fintaxcode AS tax ON tax.fstaxcode = sd.taxcode')
        ->setFields('SUM(sd.amount) as sum_amount , SUM(sd.taxamount) as sum_taxamount')
        ->setWhere("sd.taxcode = 'ZRS' AND si.period = '$period' AND si.stat NOT IN('cancelled','temporary') AND si.fiscalyear = '$fiscalyear' ")
        ->runSelect()
        ->getRow();
    }

    public function getExempt($period, $fiscalyear) {
        // return $this->db->setTable('salesinvoice_details as sd')
        // ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        // ->leftJoin('items AS i ON sd.itemcode = i.itemcode')
        // ->leftJoin('itemclass as ic ON i.classid = ic.id')
        // ->setFields('SUM(si.amount) as sum_amount , SUM(si.taxamount) as sum_taxamount')
        // ->setWhere("ic.expensetype = 'vat_exempt' AND si.period = '$period' ")
        // ->runSelect()
        // ->getRow();
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('fintaxcode AS tax ON tax.fstaxcode = sd.taxcode')
        ->setFields('SUM(sd.amount) as sum_amount , SUM(sd.taxamount) as sum_taxamount')
        ->setWhere("sd.taxcode = 'ES' AND si.period = '$period' AND si.stat NOT IN('cancelled','temporary') AND si.fiscalyear = '$fiscalyear' ")
        ->runSelect()
        ->getRow();
    }

    public function getNotPurchasesExceeded($period, $fiscalyear) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.total_tax) as sum_taxamount')
        ->setWhere("ic.expensetype = 'vat_not_exceed' AND pr.period = '$period' AND pr.fiscalyear = '$fiscalyear' ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchasesExceeded($period, $fiscalyear) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.total_tax) as sum_taxamount')
        ->setWhere("ic.expensetype = 'vat_exceed' AND pr.period = '$period' AND pr.fiscalyear = '$fiscalyear'  ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchaseGoods($period, $fiscalyear) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.total_tax) as sum_taxamount')
        ->setWhere("ic.expensetype = 'vat_domestic_goods' AND pr.period = '$period' AND pr.fiscalyear = '$fiscalyear'  ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchaseImport($period, $fiscalyear) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.total_tax) as sum_taxamount')
        ->setWhere("ic.expensetype = 'vat_import_goods' AND pr.period = '$period' AND pr.fiscalyear = '$fiscalyear'  ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchaseServices($period, $fiscalyear) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.total_tax) as sum_taxamount')
        ->setWhere("ic.expensetype = 'vat_domestic_services' AND pr.period = '$period' AND pr.fiscalyear = '$fiscalyear'  ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchaseNonResident($period, $fiscalyear) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.total_tax) as sum_taxamount')
        ->setWhere("ic.expensetype = 'vat_non_resident' AND pr.period = '$period' AND pr.fiscalyear = '$fiscalyear'  ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchaseNotTax($period, $fiscalyear) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.total_tax) as sum_taxamount')
        ->setWhere("ic.expensetype = 'vat_not' AND pr.period = '$period' ")
        ->runSelect()
        ->getRow();
    }

    public function getPrivateMonthly($period, $fiscalyear) {
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('fintaxcode AS tax ON tax.fstaxcode = sd.taxcode')
        ->setFields('SUM(si.amount) as sum_amount , SUM(si.taxamount) as sum_taxamount')
        ->setWhere("sd.taxcode IN('VATG','VATS') AND si.period = '$period' AND si.stat NOT IN('cancelled','temporary') AND si.fiscalyear = '$fiscalyear' ")
        ->runSelect()
        ->getRow();
    }

    public function getGovMonthly($period, $fiscalyear) {
        // return $this->db->setTable('salesinvoice_details as sd')
        // ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        // ->leftJoin('items AS i ON sd.itemcode = i.itemcode')
        // ->leftJoin('itemclass as ic ON i.classid = ic.id')
        // ->setFields('SUM(si.amount) as sum_amount , SUM(si.taxamount) as sum_taxamount')
        // ->setWhere("ic.expensetype = 'vat_gov' AND si.period = '$period'")
        // ->runSelect()
        // ->getRow();
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('fintaxcode AS tax ON tax.fstaxcode = sd.taxcode')
        ->setFields('SUM(sd.amount) as sum_amount , SUM(sd.taxamount) as sum_taxamount')
        ->setWhere("sd.taxcode = 'SG' AND si.period = '$period' AND si.stat NOT IN('cancelled','temporary') AND si.fiscalyear = '$fiscalyear' ")
        ->runSelect()
        ->getRow();
    }

    public function getZeroMonthly($period, $fiscalyear) {
        // return $this->db->setTable('salesinvoice_details as sd')
        // ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        // ->leftJoin('items AS i ON sd.itemcode = i.itemcode')
        // ->leftJoin('itemclass as ic ON i.classid = ic.id')
        // ->setFields('SUM(si.amount) as sum_amount , SUM(si.taxamount) as sum_taxamount')
        // ->setWhere("ic.expensetype = 'vat_zero' AND si.period = '$period")
        // ->runSelect()
        // ->getRow();
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('fintaxcode AS tax ON tax.fstaxcode = sd.taxcode')
        ->setFields('SUM(sd.amount) as sum_amount , SUM(sd.taxamount) as sum_taxamount')
        ->setWhere("sd.taxcode = 'ZRS' AND si.period = '$period'  AND si.stat NOT IN('cancelled','temporary') AND si.fiscalyear = '$fiscalyear' ")
        ->runSelect()
        ->getRow();
    }

    public function getExemptMonthly($period, $fiscalyear) {
        // return $this->db->setTable('salesinvoice_details as sd')
        // ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        // ->leftJoin('items AS i ON sd.itemcode = i.itemcode')
        // ->leftJoin('itemclass as ic ON i.classid = ic.id')
        // ->setFields('SUM(si.amount) as sum_amount , SUM(si.taxamount) as sum_taxamount')
        // ->setWhere("ic.expensetype = 'vat_exempt' AND si.period = '$period'")
        // ->runSelect()
        // ->getRow();
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('fintaxcode AS tax ON tax.fstaxcode = sd.taxcode')
        ->setFields('SUM(sd.amount) as sum_amount , SUM(sd.taxamount) as sum_taxamount')
        ->setWhere("sd.taxcode = 'ES' AND si.period = '$period' AND si.stat NOT IN('cancelled','temporary') AND si.fiscalyear = '$fiscalyear' ")
        ->runSelect()
        ->getRow();
    }

    public function getNotPurchasesExceededMonthly($period, $fiscalyear) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.total_tax) as sum_taxamount')
        ->setWhere("ic.expensetype = 'vat_not_exceed' AND pr.period = '$period' AND pr.fiscalyear = '$fiscalyear' ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchasesExceededMonthly($period, $fiscalyear) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.total_tax) as sum_taxamount')
        ->setWhere("ic.expensetype = 'vat_exceed' AND pr.period = '$period' AND pr.fiscalyear = '$fiscalyear' ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchaseGoodsMonthly($period, $fiscalyear) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.total_tax) as sum_taxamount')
        ->setWhere("ic.expensetype = 'vat_domestic_goods' AND pr.period = '$period' AND pr.fiscalyear = '$fiscalyear' ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchaseImportMonthly($period, $fiscalyear) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.total_tax) as sum_taxamount')
        ->setWhere("ic.expensetype = 'vat_import_goods' AND pr.period = '$period' AND pr.fiscalyear = '$fiscalyear' ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchaseServicesMonthly($period, $fiscalyear) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.total_tax) as sum_taxamount')
        ->setWhere("ic.expensetype = 'vat_domestic_services' AND pr.period = '$period' AND pr.fiscalyear = '$fiscalyear' ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchaseNonResidentMonthly($period, $fiscalyear) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.total_tax) as sum_taxamount')
        ->setWhere("ic.expensetype = 'vat_non_resident' AND pr.period = '$period' AND pr.fiscalyear = '$fiscalyear' ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchaseNotTaxMonthly($period, $fiscalyear) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.total_tax) as sum_taxamount')
        ->setWhere("ic.expensetype = 'vat_not' AND pr.period = '$period' AND pr.fiscalyear = '$fiscalyear' ")
        ->runSelect()
        ->getRow();
    }

    public function getPrivate2550Q($period, $fiscalyear) {
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('fintaxcode AS tax ON tax.fstaxcode = sd.taxcode')
        ->setFields('SUM(si.amount) as sum_amount , SUM(si.taxamount) as sum_taxamount')
        ->setWhere("sd.taxcode IN('VATG','VATS') AND si.period = '$period' AND si.stat NOT IN('cancelled','temporary') AND si.fiscalyear = '$fiscalyear' ")
        ->runSelect()
        ->getRow();
    }

    public function getGov2550Q($period, $fiscalyear) {
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('fintaxcode AS tax ON tax.fstaxcode = sd.taxcode')
        ->setFields('SUM(sd.amount) as sum_amount , SUM(sd.taxamount) as sum_taxamount')
        ->setWhere("sd.taxcode = 'SG' AND si.period IN $period AND si.stat NOT IN('cancelled','temporary') AND si.fiscalyear = '$fiscalyear' ")
        ->runSelect()
        ->getRow();
    }

    public function getZero2550Q($period, $fiscalyear) {
        // return $this->db->setTable('salesinvoice_details as sd')
        // ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        // ->leftJoin('items AS i ON sd.itemcode = i.itemcode')
        // ->leftJoin('itemclass as ic ON i.classid = ic.id')
        // ->setFields('SUM(si.amount) as sum_amount , SUM(si.taxamount) as sum_taxamount')
        // ->setWhere("ic.expensetype = 'vat_zero' AND si.period IN $period")
        // ->runSelect()
        // ->getRow();
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('fintaxcode AS tax ON tax.fstaxcode = sd.taxcode')
        ->setFields('SUM(sd.amount) as sum_amount , SUM(sd.taxamount) as sum_taxamount')
        ->setWhere("sd.taxcode = 'ZRS' AND si.period IN $period AND si.stat NOT IN('cancelled','temporary') AND si.fiscalyear = '$fiscalyear' ")
        ->runSelect()
        ->getRow();
    }

    public function getExempt2550Q($period, $fiscalyear) {
        // return $this->db->setTable('salesinvoice_details as sd')
        // ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        // ->leftJoin('items AS i ON sd.itemcode = i.itemcode')
        // ->leftJoin('itemclass as ic ON i.classid = ic.id')
        // ->setFields('SUM(si.amount) as sum_amount , SUM(si.taxamount) as sum_taxamount')
        // ->setWhere("ic.expensetype = 'vat_exempt' AND si.period = '$period' ")
        // ->runSelect()
        // ->getRow();
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('fintaxcode AS tax ON tax.fstaxcode = sd.taxcode')
        ->setFields('SUM(sd.amount) as sum_amount , SUM(sd.taxamount) as sum_taxamount')
        ->setWhere("sd.taxcode = 'ES' AND si.period IN $period AND si.stat NOT IN('cancelled','temporary') AND si.fiscalyear = '$fiscalyear' ")
        ->runSelect()
        ->getRow();
    }

    public function getNotPurchasesExceeded2550Q($period, $fiscalyear) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.total_tax) as sum_taxamount')
        ->setWhere("ic.expensetype = 'vat_not_exceed' AND pr.period IN $period AND pr.fiscalyear = '$fiscalyear' ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchasesExceeded2550Q($period, $fiscalyear) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.total_tax) as sum_taxamount')
        ->setWhere("ic.expensetype = 'vat_exceed' AND pr.period IN $period AND pr.fiscalyear = '$fiscalyear'  ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchaseGoods2550Q($period, $fiscalyear) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.total_tax) as sum_taxamount')
        ->setWhere("ic.expensetype = 'vat_domestic_goods' AND pr.period IN $period AND pr.fiscalyear = '$fiscalyear'  ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchaseImport2550Q($period, $fiscalyear) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.total_tax) as sum_taxamount')
        ->setWhere("ic.expensetype = 'vat_import_goods' AND pr.period IN $period AND pr.fiscalyear = '$fiscalyear'  ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchaseServices2550Q($period, $fiscalyear) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.total_tax) as sum_taxamount')
        ->setWhere("ic.expensetype = 'vat_domestic_services' AND pr.period IN $period AND pr.fiscalyear = '$fiscalyear'  ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchaseNonResident2550Q($period, $fiscalyear) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.total_tax) as sum_taxamount')
        ->setWhere("ic.expensetype = 'vat_non_resident' AND pr.period IN $period AND pr.fiscalyear = '$fiscalyear'  ")
        ->runSelect()
        ->getRow();
    }

    public function getPurchaseNotTax2550Q($period, $fiscalyear) {
        return $this->db->setTable('purchasereceipt_details as pd')
        ->leftJoin('purchasereceipt as pr ON pd.voucherno = pr.voucherno')
        ->leftJoin('items AS i ON pd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(pr.amount) as sum_amount , SUM(pr.total_tax) as sum_taxamount')
        ->setWhere("ic.expensetype = 'vat_not' AND pr.period IN $period ")
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

        $company_info   = $this->getCompanyInfo('wtax_option');
        $wtax_option    = $company_info->wtax_option;
        if($wtax_option == 'AP'){
            return $this->db->setTable('ap_details a')
            ->setFields('SUM(a.convertedcredit) tax')
            ->leftJoin('accountspayable ap ON ap.voucherno = a.voucherno')
            ->setWhere("MONTH(transactiondate) = '$i' and YEAR(transactiondate) = '$year' AND taxcode != ''")
            ->runSelect()
            ->getResult();
        }else if($wtax_option == 'PV'){
            return $this->db->setTable('pv_details a')
            ->setFields('SUM(a.convertedcredit) tax')
            ->leftJoin('paymentvoucher ap ON ap.voucherno = a.voucherno')
            ->setWhere("MONTH(transactiondate) = '$i' and YEAR(transactiondate) = '$year' AND taxcode != ''")
            ->runSelect()
            ->getResult();
        }
    }

    public function getTotalRemittance($month, $year){
        $company_info   = $this->getCompanyInfo('wtax_option');
        $wtax_option    = $company_info->wtax_option;
        if($wtax_option == 'AP'){
            $result =  $this->db->setTable('ap_details apd')
            ->setFields(array('SUM(convertedcredit) convertedcredit'))
            ->leftJoin('accountspayable ap ON ap.voucherno = apd.voucherno')
            ->setWhere("taxcode IS NOT NULL AND taxcode != '' AND ap.stat = 'posted' AND (MONTH(transactiondate) = '$month' AND YEAR(transactiondate) = '$year')")
            ->runSelect()
            ->getResult();
        }else if($wtax_option == 'PV'){
            $result = $this->db->setTable('pv_details pvd')
            ->setFields(array('SUM(convertedcredit) convertedcredit'))
            ->leftJoin('paymentvoucher pv ON pv.voucherno = pvd.voucherno')
            ->setWhere("taxcode IS NOT NULL AND taxcode != '' AND pv.stat = 'posted' AND (MONTH(transactiondate) = '$month' AND YEAR(transactiondate) = '$year')")   
            ->runSelect()
            ->getResult();
        }

        return $result[0]->convertedcredit;
    }
}	