<?php

    class purchase_relief extends wc_model {        
		public function getCompany($code){
			$fields = "companyname, address, tin, taxyear, periodstart, rdo_code";

			$result = $this->db->setTable('company')
								->setFields($fields)
								->setWhere("companycode = '$code'")
								->setWhere(1)
								->setLimit('1')
								->runSelect()
								->getRow();

			return $result;
		}

        public function getPurchaseReliefPagination($vendor, $sort, $start, $end) {
            $result	= $this->getQueryDetails($vendor, $sort, $start, $end)
                            ->runPagination();
            // echo $this->db->getQuery();
            return $result;
        }

        public function getPurchaseReliefDetails($vendor, $sort, $start, $end) {
            $result	= $this->getQueryDetails($vendor, $sort, $start, $end)
                            ->runSelect()
                            ->getResult();
            // echo $this->db->getQuery();
            return $result;
        }

        private function getQueryDetails($vendor, $sort, $start, $end) {
            $condition = '';
            if ($start && $end) {
                $condition .= " AND (rpt.transactiondate >= '$start' AND rpt.transactiondate <= '$end')";
            }
            if($vendor!="" && $vendor != "none"){
                $condition .= " AND rpt.vendor = '$vendor'";
            } 
            
            $service_query  =   $this->db->setTable('purchasereceipt_details dtl')
                                         ->setFields(array('dtl.voucherno, dtl.itemcode, SUM(dtl.amount) amt, dtl.companycode, dtl.linenum'))
                                         ->leftJoin("items as itm ON itm.itemcode = dtl.itemcode AND itm.companycode = dtl.companycode")
                                         ->leftJoin("itemclass as ic ON ic.id = itm.classid AND ic.companycode = itm.companycode")
                                         ->setWhere("dtl.stat IN ('Received','Posted') AND (itm.expenseType = 'vat_domestic_services' OR ic.expenseType = 'vat_domestic_services')")
                                         ->setGroupBy('dtl.voucherno')
                                         ->buildSelect();

            $goods_query  =   $this->db->setTable('purchasereceipt_details dtl')
                                         ->setFields(array('dtl.voucherno, dtl.itemcode, SUM(dtl.amount) amt, dtl.companycode, dtl.linenum'))
                                         ->leftJoin("items as itm ON itm.itemcode = dtl.itemcode AND itm.companycode = dtl.companycode")
                                         ->leftJoin("itemclass as ic ON ic.id = itm.classid AND ic.companycode = itm.companycode")
                                         ->setWhere("dtl.stat IN ('Received','Posted') AND (itm.expenseType = 'vat_domestic_goods' OR ic.expenseType = 'vat_domestic_goods')")
                                         ->setGroupBy('dtl.voucherno')
                                         ->buildSelect();

            $capital_query  =   $this->db->setTable('purchasereceipt_details dtl')
                                         ->setFields(array('dtl.voucherno, dtl.itemcode, SUM(dtl.amount) amt, dtl.companycode, dtl.linenum'))
                                         ->leftJoin("items as itm ON itm.itemcode = dtl.itemcode AND itm.companycode = dtl.companycode")
                                         ->leftJoin("itemclass as ic ON ic.id = itm.classid AND ic.companycode = itm.companycode")
                                         ->setWhere("dtl.stat IN ('Received','Posted') AND ((itm.expenseType = 'vat_exceed' OR itm.expenseType = 'vat_not_exceed') OR (ic.expenseType = 'vat_exceed' OR ic.expenseType = 'vat_not_exceed'))")
                                         ->setGroupBy('dtl.voucherno')
                                         ->buildSelect();

            $inner_query    =   $this->db->setTable("purchasereceipt_details dtl")
                                         ->setFields(array('dtl.companycode, dtl.voucherno, dtl.linenum, IFNULL(service.amt,0) service, IFNULL(goods.amt,0) goods, IFNULL(capital.amt, 0) capital'))
                                         ->leftJoin('('.$service_query.') service ON service.itemcode = dtl.itemcode AND service.companycode = dtl.companycode AND service.linenum = dtl.linenum')
                                         ->leftJoin('('.$goods_query.') goods ON goods.itemcode = dtl.itemcode AND goods.companycode = dtl.companycode AND goods.linenum = dtl.linenum')
                                         ->leftJoin('('.$capital_query.') capital ON capital.itemcode = dtl.itemcode AND capital.companycode = dtl.companycode AND capital.linenum = dtl.linenum')
                                         ->setGroupBy('dtl.voucherno')
                                         ->buildSelect();
                                         
            $query = $this->db->setTable("purchasereceipt rpt")
                              ->setFields('rpt.transactiondate, rpt.voucherno, rpt.period, rpt.fiscalyear, p.partnername, p.address1 address, p.tinno, rpt.netamount, "0" vat_exempt, "0" vat_zerorated, rpt.amount vat_sales, 
                                            COALESCE(in_query.service,0) service, COALESCE(in_query.goods,0) goods, COALESCE(in_query.capital, 0) capital, rpt.total_tax totaltax, rpt.netamount as grosstaxable')
                              ->leftJoin('partners p ON p.partnercode = rpt.vendor AND p.companycode = rpt.companycode AND p.partnertype = "supplier"')
                              ->leftJoin("($inner_query) in_query ON in_query.voucherno = rpt.voucherno AND in_query.companycode = rpt.companycode")
                              ->setWhere("rpt.stat NOT IN ('cancelled','temporary')".$condition)
                              ->setGroupBy('rpt.voucherno')
                              ->setOrderBy($sort);

            return $query;
        }

        public function retrieveVendorList(){
			$result = $this->db->setTable('partners')
						->setFields("partnercode ind, CONCAT(partnercode,' - ',partnername) val")
						->setWhere("partnercode != '' AND partnertype = 'supplier' AND stat = 'active'")
						->setOrderBy("val")
						->runSelect()
						->getResult();
			return $result;
        }

        public function getAmountTotal($vendor, $sort, $start, $end) {
			$fields 	= 	array('rpt.transactiondate, rpt.period, rpt.fiscalyear, p.partnername, p.tinno, SUM(rpt.netamount) netamount, 0 vat_exempt, 0 vat_zerorated, SUM(rpt.amount) vat_sales, SUM(in_query.service) service, SUM(in_query.goods) goods, SUM(in_query.capital) capital, SUM(rpt.total_tax) totaltax, SUM(rpt.netamount) grosstaxable');

			$having_cond 	=	"";
			$group_by 		=	"";

			$db	= $this->getQueryDetails($vendor, $sort, $start, $end) 
						->setFields($fields);

			if (!empty($start) && !empty($end)) {
				$having_cond .= " ( rpt.transactiondate >= '$start' AND rpt.transactiondate <= '$end' ) ";
            } 
            
            $result = $db->setGroupBy($group_by)
                        ->setHaving($having_cond)
                        ->runSelect()
                        ->getRow();
                        // echo  $this->db->getQuery();
			return $result;
		}
    }

?>