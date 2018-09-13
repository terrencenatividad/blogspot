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

    public function getWithheld($data) {
        $year       = $data['year'];
        $quarter    = $data['quarter']; 
    }

    public function getPeriod($period) {
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('items AS i ON sd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(si.amount) as sum_amount , SUM(si.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_private' AND si.period = '$period'")
        ->runSelect()
        ->getRow();
    }

    public function getGov($period) {
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('items AS i ON sd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(si.amount) as sum_amount , SUM(si.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_gov' AND si.period = '$period'")
        ->runSelect()
        ->getRow();
    }

    public function getZero($period) {
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('items AS i ON sd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(si.amount) as sum_amount , SUM(si.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_zero' AND si.period = '$period")
        ->runSelect()
        ->getRow();
    }

    public function getExempt($period) {
        return $this->db->setTable('salesinvoice_details as sd')
        ->leftJoin('salesinvoice as si ON sd.voucherno = si.voucherno')
        ->leftJoin('items AS i ON sd.itemcode = i.itemcode')
        ->leftJoin('itemclass as ic ON i.classid = ic.id')
        ->setFields('SUM(si.amount) as sum_amount , SUM(si.taxamount) as sum_taxamount')
        ->setWhere("ic.revenuetype = 'vat_exempt' AND si.period = '$period'")
        ->runSelect()
        ->getRow();
    }
}	