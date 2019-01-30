<?php

    class sales_relief extends wc_model {        
		public function getCompany($code){
			$fields = "companyname, address, tin, taxyear, periodstart";

			$result = $this->db->setTable('company')
								->setFields($fields)
								->setWhere("companycode = '$code'")
								->setWhere(1)
								->setLimit('1')
								->runSelect()
								->getRow();

			return $result;
		}

        public function getSalesReliefPagination($customer, $sort, $start, $end) {
            $result	= $this->getQueryDetails($customer, $sort, $start, $end)
                            ->runPagination();
            // echo $this->db->getQuery();
            return $result;
        }

        public function getSalesReliefDetails($customer, $sort, $start, $end) {
            $result	= $this->getQueryDetails($customer, $sort, $start, $end)
                            ->runSelect()
                            ->getResult();
            // echo $this->db->getQuery();
            return $result;
        }

        private function getQueryDetails($customer, $sort, $start, $end) {
            $condition = '';
            if ($start && $end) {
                $condition .= " AND (inv.transactiondate >= '$start' AND inv.transactiondate <= '$end')";
            }
            if($customer!="" && $customer != "none"){
                $condition .= " AND inv.customer = '$customer'";
            } 
            $query = $this->db->setTable("salesinvoice inv")
                              ->setFields('inv.transactiondate, inv.voucherno, inv.period, inv.fiscalyear, p.partnername, p.tinno, inv.netamount, inv.vat_exempt, inv.vat_zerorated, inv.vat_sales, inv.taxamount, inv.amount')
                              ->leftJoin('partners p ON p.partnercode = inv.customer AND p.companycode = inv.companycode AND p.partnertype = "customer"')
                              ->setWhere("inv.stat NOT IN ('cancelled','temporary')".$condition)
                              ->setGroupBy('inv.voucherno')
                              ->setOrderBy($sort);
    
            return $query;
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

        public function getAmountTotal($customer, $sort, $start, $end) {
			$fields 	= 	array('inv.transactiondate, inv.period, inv.fiscalyear, p.partnername, p.tinno, SUM(inv.netamount) netamount, SUM(inv.vat_exempt) vat_exempt, SUM(inv.vat_zerorated) vat_zerorated, SUM(inv.vat_sales) vat_sales, SUM(inv.taxamount) taxamount, SUM(inv.amount) amount');

			$having_cond 	=	"";
			$group_by 		=	"";

			$db	= $this->getQueryDetails($customer, $sort, $start, $end) 
						->setFields($fields);

			if (!empty($start) && !empty($end)) {
				$having_cond .= " ( inv.transactiondate >= '$start' AND inv.transactiondate <= '$end' ) ";
            } 
            
            $result = $db->setGroupBy($group_by)
                        ->setHaving($having_cond)
                        ->runSelect()
						->getRow();
			return $result;
		}
    }

?>