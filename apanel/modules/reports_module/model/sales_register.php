<?php

    class sales_register extends wc_model
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

		public function retrieveMainListing($datefilter, $customer){
			$condition = '';
			$filter = explode('-',$datefilter);
			foreach($filter as $date){
				$dates[] = date('Y-m-d',strtotime($date));
			}
			$start = $dates[0];
			$end   = isset($dates[1])	?	$dates[1] 	: 	$dates[0];
			
			$fields			=	array('main.transactiondate','main.voucherno','customer.partnername company','main.amount');

			$condition 		.=	($customer!="none" && $customer != "") 	?  "AND main.partnercode = '$customer'" 	: 	"";	
			
			if ($start && $end){
				$condition .=  "AND main.transactiondate >= '$start' AND main.transactiondate <= '$end' ";
			}
	
			$result 	=	$this->db->setTable('salesinvoice main')
									 ->setFields($fields)
									 ->leftJoin('partners customer ON customer.partnercode = main.customer')
									 ->setWhere('customer.stat = "active" AND customer.partnertype = "customer" AND main.stat = "posted" '.$condition)
									 ->runPagination();
			return $result;		
		}

		public function retrieveGrandTotal($datefilter, $customer){
			$condition = '';
			$filter = explode('-',$datefilter);
			foreach($filter as $date){
				$dates[] = date('Y-m-d',strtotime($date));
			}
			$start = $dates[0];
			$end   = isset($dates[1])	?	$dates[1] 	: 	$dates[0];

			$fields			=	array('SUM(main.amount) amount');

			$condition 		.=	($customer!="none" && $customer != "") 	?  "AND main.partnercode = '$customer'" 	: 	"";	
			
			if ($start && $end){
				$condition .=  "AND main.transactiondate >= '$start' AND main.transactiondate <= '$end' ";
			}
	
			$result 	=	$this->db->setTable('salesinvoice main')
									 ->setFields($fields)
									 ->leftJoin('partners customer ON customer.partnercode = main.customer')
									 ->setWhere('customer.stat = "active" AND customer.partnertype = "customer" AND main.stat = "posted" '.$condition)
									 ->runSelect()
									 ->getResult();
			return $result;				 

		}

		public function countDeliveryReceipt($datefilter, $customer){
			$condition = '';
			$filter = explode('-',$datefilter);
			foreach($filter as $date){
				$dates[] = date('Y-m-d',strtotime($date));
			}
			$start = $dates[0];
			$end   = isset($dates[1])	?	$dates[1] 	: 	$dates[0];

			$fields			=	array('stat, COUNT(*) total');

			$condition 		.=	($customer!="none" && $customer != "") 	?  "AND main.partnercode = '$customer'" 	: 	"";	
			
			if ($start && $end){
				$condition .=  "AND main.transactiondate >= '$start' AND main.transactiondate <= '$end' ";
			}
	
			$result 	=	$this->db->setTable('deliveryreceipt main')
									 ->setFields($fields)
									 ->setWhere(" stat IN ('Cancelled','With Invoice') ")
									 ->setGroupBy('stat')
									 ->runSelect()
									 ->getResult();
			return $result;				 

		}

		public function export_main($datefilter, $customer) {
			$condition = '';
			$filter = explode('-',$datefilter);
			foreach($filter as $date){
				$dates[] = date('Y-m-d',strtotime($date));
			}
			$start = $dates[0];
			$end   = isset($dates[1])	?	$dates[1] 	: 	$dates[0];

			$fields			=	array('main.transactiondate','main.voucherno','customer.partnername company','main.amount');

			$condition 		.=	($customer!="none" && $customer != "") 	?  "AND main.partnercode = '$customer'" 	: 	"";	
			
			if ($start && $end){
				$condition .=  "AND main.transactiondate >= '$start' AND main.transactiondate <= '$end' ";
			}
	
			$result 	=	$this->db->setTable('salesinvoice main')
									 ->setFields($fields)
									 ->leftJoin('partners customer ON customer.partnercode = main.customer')
									 ->setWhere('customer.stat = "active" AND customer.partnertype = "customer" AND main.stat = "posted"'.$condition)
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