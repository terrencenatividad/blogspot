<?php
class bir extends wc_model {
    public function getCompanyInfo($fields){
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
}	