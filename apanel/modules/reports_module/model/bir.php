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

    public function getWithheld($data){
        $year       = $data['year'];
        $quarter    = $data['quarter'];
        
    }
}	