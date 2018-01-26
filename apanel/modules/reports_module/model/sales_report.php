<?php

    class sales_report extends wc_model
    {        
        public function retrieveCustomerDetails($vendor_code)
		{
			$fields = "address1, tinno, terms, email, partnername AS name";
			$cond 	= "partnercode = '$vendor_code' AND partnertype = 'customer' ";

			$result = $this->db->setTable('partners')
								->setFields($fields)
								->setWhere($cond)
								->setLimit('1')
								->runSelect()
								->getRow();

			return $result;
		}

		public function retrieveCustomerList(){
			$result = $this->db->setTable('partners')
						->setFields("partnercode ind, partnername val")
						->setWhere("partnercode != '' AND partnertype = 'customer' AND stat = 'active'")
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

		public function retrieveMainListing($year, $customer){
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

			$customer_cond 		=	($customer!="none" && $customer != "") 	?  "AND main.partnercode = '$customer'" 	: 	"";	
				
			$result 	=	$this->db->setTable('partners main')
									 ->setFields($fields)
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '1' AND year = '$year' GROUP BY customercode ) january ON january.companycode = main.companycode AND january.customercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '2' AND year = '$year' GROUP BY customercode ) february ON february.companycode = main.companycode AND february.customercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '3' AND year = '$year' GROUP BY customercode ) march ON march.companycode = main.companycode AND march.customercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '4' AND year = '$year' GROUP BY customercode ) april  ON april.companycode = main.companycode AND april.customercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '5' AND year = '$year' GROUP BY customercode ) may ON may.companycode = main.companycode AND may.customercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '6' AND year = '$year' GROUP BY customercode ) june ON june.companycode = main.companycode AND june.customercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '7' AND year = '$year' GROUP BY customercode ) july ON july.companycode = main.companycode AND july.customercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '8' AND year = '$year' GROUP BY customercode ) august ON august.companycode = main.companycode AND august.customercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '9' AND year = '$year' GROUP BY customercode ) september ON september.companycode = main.companycode AND september.customercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '10' AND year = '$year' GROUP BY customercode ) october ON october.companycode = main.companycode AND october.customercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '11' AND year = '$year' GROUP BY customercode ) november ON november.companycode = main.companycode AND november.customercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '12' AND year = '$year' GROUP BY customercode ) december ON december.companycode = main.companycode AND december.customercode = main.partnercode")
									 ->setWhere("main.partnertype = 'customer' $customer_cond")									 
									 ->setGroupBy('main.partnercode')
									 ->runPagination();
				return $result;						 

		}

		public function export_main($year, $customer) {
			
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

			$customer_cond 		=	($customer!="none" && $customer != "") 	?  "AND main.partnercode = '$customer'" 	: 	"";	
								
			$result 	=	$this->db->setTable('partners main')
									 ->setFields($fields)
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '1' AND year = '$year' GROUP BY customercode ) january ON january.companycode = main.companycode AND january.customercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '2' AND year = '$year' GROUP BY customercode ) february ON february.companycode = main.companycode AND february.customercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '3' AND year = '$year' GROUP BY customercode ) march ON march.companycode = main.companycode AND march.customercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '4' AND year = '$year' GROUP BY customercode ) april  ON april.companycode = main.companycode AND april.customercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '5' AND year = '$year' GROUP BY customercode ) may ON may.companycode = main.companycode AND may.customercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '6' AND year = '$year' GROUP BY customercode ) june ON june.companycode = main.companycode AND june.customercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '7' AND year = '$year' GROUP BY customercode ) july ON july.companycode = main.companycode AND july.customercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '8' AND year = '$year' GROUP BY customercode ) august ON august.companycode = main.companycode AND august.customercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '9' AND year = '$year' GROUP BY customercode ) september ON september.companycode = main.companycode AND september.customercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '10' AND year = '$year' GROUP BY customercode ) october ON october.companycode = main.companycode AND october.customercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '11' AND year = '$year' GROUP BY customercode ) november ON november.companycode = main.companycode AND november.customercode = main.partnercode")
									 ->leftJoin("(SELECT month, SUM(amount) total, companycode, customercode FROM balance_table_sales WHERE month = '12' AND year = '$year' GROUP BY customercode ) december ON december.companycode = main.companycode AND december.customercode = main.partnercode")
									 ->setWhere("main.partnertype = 'customer' $customer_cond")
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
			$customer 	=	isset($data['customer'])? 	trim($data['customer']) : 	"";
			
			$condition 	= " (si.voucherno != '' AND si.voucherno != '-')  AND si.stat NOT IN ('temporary', 'cancelled') ";
			$condition .= (!empty($data) && !empty($year)) 	? 	" AND ( MONTH(si.transactiondate) = '$month' AND YEAR(si.transactiondate) = '$year') "	: 	"";
			$condition .= (!empty($customer)) 	? 	" AND sect.partnercode =  '$customer'"	: 	"";
            $fields     =   array('si.transactiondate as date','si.voucherno invoice','ar.voucherno ar','si.amount as totalamount, si.referenceno reference');

            $result = $this->db->setTable("salesinvoice as si")
								->leftJoin('accountsreceivable ar ON ar.invoiceno = si.voucherno AND si.companycode = ar.companycode')
                                ->leftJoin('partners as sect ON sect.partnercode = si.customer AND sect.partnertype = "customer" ') 
                                ->setFields($fields)
								->setWhere($condition)
								->setGroupBy('si.voucherno')
								->runPagination();
									
			return $result;
		}

		public function export_daily($data){

			$month 		=	isset($data['month']) 	?	trim($data['month']) 	: 	"";
			$year 		=	isset($data['year']) 	?	trim($data['year']) 	: 	"";
			$customer 	=	isset($data['customer'])? 	trim($data['customer']) : 	"";
			
			$condition 	= " (si.voucherno != '' AND si.voucherno != '-')  AND si.stat NOT IN ('temporary', 'cancelled') ";
			$condition .= (!empty($data) && !empty($year)) 	? 	" AND ( MONTH(si.transactiondate) = '$month' AND YEAR(si.transactiondate) = '$year') "	: 	"";
			$condition .= (!empty($customer)) 	? 	" AND sect.partnercode =  '$customer'"	: 	"";
			$fields     =   array('si.transactiondate as date','si.voucherno invoice','ar.voucherno ar','si.amount as totalamount, si.referenceno reference');

			$result = $this->db->setTable("salesinvoice as si")
								->leftJoin('accountsreceivable ar ON ar.invoiceno = si.voucherno AND si.companycode = ar.companycode')
								->leftJoin('partners as sect ON sect.partnercode = si.customer AND sect.partnertype = "customer" ') 
								->setFields($fields)
								->setWhere($condition)
								->setGroupBy('si.voucherno')
								->runSelect()
								->getResult();
								
			return $result;
		}
    }

?>