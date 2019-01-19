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
						->setFields("partnercode ind, CONCAT(partnercode,' - ',partnername) val")
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

			$condition 		.=	($customer!="none" && $customer != "") 	?  "AND main.customer = '$customer'" 	: 	"";	
			
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

			$condition 		.=	($customer!="none" && $customer != "") 	?  "AND main.customer = '$customer'" 	: 	"";	
			
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

			$condition 		.=	($customer!="none" && $customer != "") 	?  "AND main.customer = '$customer'" 	: 	"";	
			
			if ($start && $end){
				$condition .=  "AND main.transactiondate >= '$start' AND main.transactiondate <= '$end' ";
			}
	
			$result 	=	$this->db->setTable('deliveryreceipt main')
									 ->setFields($fields)
									 ->setWhere(" stat IN ('Cancelled','With Invoice') ".$condition)
									 ->setGroupBy('stat')
									 ->runSelect()
									 ->getResult();
									//  echo $this->db->getQuery();
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

			$condition 		.=	($customer!="none" && $customer != "") 	?  "AND main.customer = '$customer'" 	: 	"";	
			
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
    }

?>