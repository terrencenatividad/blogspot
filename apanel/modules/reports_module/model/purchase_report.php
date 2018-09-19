<?php

    class purchase_report extends wc_model
    {        
		public function getCompany()
		{
			$fields = "taxyear, periodstart";

			$result = $this->db->setTable('company')
								->setFields($fields)
								->setWhere(1)
								->setLimit('1')
								->runSelect()
								->getRow();

			return $result;
		}

        public function getVendorDetails($vendor_code)
		{
			$fields = "address1, tinno, terms, email, partnername AS name";
			$cond 	= "partnercode = '$vendor_code' AND partnertype = 'supplier' ";

			$result = $this->db->setTable('partners')
								->setFields($fields)
								->setWhere($cond)
								->setLimit('1')
								->runSelect()
								->getRow();

			return $result;
		}
		
		public function retrieveSupplierList(){
			$result = $this->db->setTable('partners')
						->setFields("partnercode ind, partnername val")
						->setWhere("partnercode != '' AND partnertype = 'supplier' AND stat = 'active'")
						->setOrderBy("val")
						->runSelect()
						->getResult();
			return $result;
		}

        private function generateSearch($search, $array) {
            $temp = array();
            foreach ($array as $arr) {
                $temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
            }
            return '(' . implode(' OR ', $temp) . ')';
		}

		public function retrieveMainListing($year, $vendor){
			// echo $year;
			$fields =	array(	'main.partnername',
								'main.partnercode',
								'IF(january.total > 0, FORMAT(SUM(january.total), 2), 0) jan',
								'IF(february.total > 0, FORMAT(SUM(february.total), 2), 0) feb',
								'IF(march.total > 0, FORMAT(SUM(march.total), 2), 0) march',
								'IF(april.total > 0, FORMAT(SUM(april.total), 2), 0) april',
								'IF(may.total > 0, FORMAT(SUM(may.total), 2), 0) may',
								'IF(june.total > 0, FORMAT(SUM(june.total), 2), 0) june',
								'IF(july.total > 0, FORMAT(SUM(july.total), 2), 0) july',
								'IF(august.total > 0, FORMAT(SUM(august.total), 2), 0) aug',
								'IF(september.total > 0, FORMAT(SUM(september.total), 2), 0) sept',
								'IF(october.total > 0, FORMAT(SUM(october.total), 2), 0) oct',
								'IF(november.total > 0, FORMAT(SUM(november.total), 2), 0) nov',
								'IF(december.total > 0, FORMAT(SUM(december.total), 2), 0) decm');
			
			$vendor_cond 	=	($vendor!="none" && $vendor != "") 	?  "AND main.partnercode = '$vendor'" 	: 	"";	
			
			$result 	=	$this->db->setTable('partners main')
									 ->setFields($fields)
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '1' AND year = '$year' GROUP BY suppliercode ) january ON january.companycode = main.companycode AND january.suppliercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '2' AND year = '$year' GROUP BY suppliercode ) february ON february.companycode = main.companycode AND february.suppliercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '3' AND year = '$year' GROUP BY suppliercode ) march ON march.companycode = main.companycode AND march.suppliercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '4' AND year = '$year' GROUP BY suppliercode ) april  ON april.companycode = main.companycode AND april.suppliercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '5' AND year = '$year' GROUP BY suppliercode ) may ON may.companycode = main.companycode AND may.suppliercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '6' AND year = '$year' GROUP BY suppliercode ) june ON june.companycode = main.companycode AND june.suppliercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '7' AND year = '$year' GROUP BY suppliercode ) july ON july.companycode = main.companycode AND july.suppliercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '8' AND year = '$year' GROUP BY suppliercode ) august ON august.companycode = main.companycode AND august.suppliercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '9' AND year = '$year' GROUP BY suppliercode ) september ON september.companycode = main.companycode AND september.suppliercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '10' AND year = '$year' GROUP BY suppliercode ) october ON october.companycode = main.companycode AND october.suppliercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '11' AND year = '$year' GROUP BY suppliercode ) november ON november.companycode = main.companycode AND november.suppliercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '12' AND year = '$year' GROUP BY suppliercode ) december ON december.companycode = main.companycode AND december.suppliercode = main.partnercode")
									 ->setWhere("main.partnertype = 'supplier' $vendor_cond")
									 ->setGroupBy('main.partnercode')
									 ->runPagination();
				return $result;						 

		}

		public function export_main($year, $vendor) {
				
			$fields =	array(	'main.partnername',
								'main.partnercode',
								'IF(january.total > 0, FORMAT(SUM(january.total), 2), 0) jan',
								'IF(february.total > 0, FORMAT(SUM(february.total), 2), 0) feb',
								'IF(march.total > 0, FORMAT(SUM(march.total), 2), 0) march',
								'IF(april.total > 0, FORMAT(SUM(april.total), 2), 0) april',
								'IF(may.total > 0, FORMAT(SUM(may.total), 2), 0) may',
								'IF(june.total > 0, FORMAT(SUM(june.total), 2), 0) june',
								'IF(july.total > 0, FORMAT(SUM(july.total), 2), 0) july',
								'IF(august.total > 0, FORMAT(SUM(august.total), 2), 0) aug',
								'IF(september.total > 0, FORMAT(SUM(september.total), 2), 0) sept',
								'IF(october.total > 0, FORMAT(SUM(october.total), 2), 0) oct',
								'IF(november.total > 0, FORMAT(SUM(november.total), 2), 0) nov',
								'IF(december.total > 0, FORMAT(SUM(december.total), 2), 0) decm');

			$vendor_cond 	=	($vendor!="none" && $vendor != "") 	?  "AND main.partnercode = '$vendor'" 	: 	"";	
								
			$result 	=	$this->db->setTable('partners main')
									 ->setFields($fields)
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '1' AND year = '$year' GROUP BY suppliercode ) january ON january.companycode = main.companycode AND january.suppliercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '2' AND year = '$year' GROUP BY suppliercode ) february ON february.companycode = main.companycode AND february.suppliercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '3' AND year = '$year' GROUP BY suppliercode ) march ON march.companycode = main.companycode AND march.suppliercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '4' AND year = '$year' GROUP BY suppliercode ) april  ON april.companycode = main.companycode AND april.suppliercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '5' AND year = '$year' GROUP BY suppliercode ) may ON may.companycode = main.companycode AND may.suppliercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '6' AND year = '$year' GROUP BY suppliercode ) june ON june.companycode = main.companycode AND june.suppliercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '7' AND year = '$year' GROUP BY suppliercode ) july ON july.companycode = main.companycode AND july.suppliercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '8' AND year = '$year' GROUP BY suppliercode ) august ON august.companycode = main.companycode AND august.suppliercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '9' AND year = '$year' GROUP BY suppliercode ) september ON september.companycode = main.companycode AND september.suppliercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '10' AND year = '$year' GROUP BY suppliercode ) october ON october.companycode = main.companycode AND october.suppliercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '11' AND year = '$year' GROUP BY suppliercode ) november ON november.companycode = main.companycode AND november.suppliercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, suppliercode FROM balance_table_purchase WHERE month = '12' AND year = '$year' GROUP BY suppliercode ) december ON december.companycode = main.companycode AND december.suppliercode = main.partnercode")
									 ->setWhere("main.partnertype = 'supplier' $vendor_cond")
									 ->setGroupBy('main.partnercode')
									 ->runSelect()
									 ->getResult();
			return $result;
		}

		public function getYearList() {
			$year_list	= array();
			$year_now	= date('Y');
			for ($year = $year_now; $year > $year_now - 5; $year--) {
				$year_list[$year] = $year;
			}
			return $year_list;
		}

		public function getDaily($data){

			$month 		=	isset($data['month']) 	?	trim($data['month']) 	: 	"";
			$year 		=	isset($data['year']) 	?	trim($data['year']) 	: 	"";
			$supplier 	=	isset($data['vendor'])? 	trim($data['vendor']) : 	"";
			
			$condition 	= " (pr.voucherno != '' AND pr.voucherno != '-')  AND pr.stat NOT IN ('temporary', 'cancelled') ";
			$condition .= (!empty($data) && !empty($year)) 	? 	" AND ( MONTH(pr.transactiondate) = '$month' AND YEAR(pr.transactiondate) = '$year') "	: 	"";
			$condition .= (!empty($supplier)) 	? 	" AND sect.partnercode =  '$supplier'"	: 	"";
            $fields     =   array('pr.transactiondate as date','pr.voucherno invoice','ap.voucherno ap','pr.netamount as totalamount, pr.invoiceno reference');

            $result = $this->db->setTable("purchasereceipt as pr")
								->leftJoin('accountspayable ap ON ap.referenceno = pr.voucherno AND pr.companycode = ap.companycode')
                                ->leftJoin('partners as sect ON sect.partnercode = pr.vendor AND sect.partnertype = "supplier" ') 
                                ->setFields($fields)
								->setWhere($condition)
								->setGroupBy('pr.voucherno')
								->runPagination();
									
			return $result;
		}

		public function export_daily($data){

			$month 		=	isset($data['month']) 	?	trim($data['month']) 	: 	"";
			$year 		=	isset($data['year']) 	?	trim($data['year']) 	: 	"";
			$supplier 	=	isset($data['vendor'])? 	trim($data['vendor']) : 	"";
			
			$condition 	= " (pr.voucherno != '' AND pr.voucherno != '-')  AND pr.stat NOT IN ('temporary', 'cancelled') ";
			$condition .= (!empty($data) && !empty($year)) 	? 	" AND ( MONTH(pr.transactiondate) = '$month' AND YEAR(pr.transactiondate) = '$year') "	: 	"";
			$condition .= (!empty($supplier)) 	? 	" AND sect.partnercode =  '$supplier'"	: 	"";
			$fields     =   array('pr.transactiondate as date','pr.voucherno invoice','ap.voucherno ap','pr.netamount as totalamount, pr.invoiceno reference');

			$result = $this->db->setTable("purchasereceipt as pr")
								->leftJoin('accountspayable ap ON ap.referenceno = pr.voucherno AND pr.companycode = ap.companycode')
								->leftJoin('partners as sect ON sect.partnercode = pr.vendor AND sect.partnertype = "supplier" ') 
								->setFields($fields)
								->setWhere($condition)
								->setGroupBy('pr.voucherno')
								->runSelect()
								->getResult();
								
			return $result;
		}
    }

?>