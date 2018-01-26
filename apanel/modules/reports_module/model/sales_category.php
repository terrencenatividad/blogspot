<?php

    class sales_category extends wc_model
    {        
        public function retrieveSalesReport($category, $startdate, $enddate, $warehouse) {
            $condition = "";

            if ($category && $category != 'none') 
			{
                $condition .= " AND itm.classid = '$category'";
            }
		
			if ($startdate && $enddate) 
			{
				$condition .= " AND ( inv.transactiondate >= '$startdate' AND inv.transactiondate <= '$enddate') ";
			}

			if ($warehouse && $warehouse != 'none') 
			{
                $condition .= " AND isd.warehouse = '$warehouse'";
            }

            $fields     =   array('itmclass.label category','SUM(isd.issueqty)  sales_qty', 'itm.uom_selling sales_uom','SUM(isd.convissueqty)  base_qty', 'itm.uom_base base_uom','SUM(isd.amount) amount');

            $result = $this->db->setTable("salesinvoice as inv")
								->setFields($fields)
                                ->leftJoin('salesinvoice_details as isd ON isd.voucherno=inv.voucherno')
                                ->leftJoin('items as itm ON itm.itemcode = isd.itemcode')
								->leftJoin('itemclass as itmclass ON itmclass.id = itm.classid')
                                ->setWhere(" inv.stat NOT IN ('temporary','cancelled') " .$condition)
								->setGroupBy("itm.classid")
								->setOrderBy("itm.classid")
                                ->runPagination();
						
            return $result;
        }

		public function fileExport($data) {
			$condition = '';
			$datefilter	= $data['daterangefilter'];
			$category 	= $data['category'];
			$warehouse 	= $data['warehouse'];
			$datefilter = explode('-', $datefilter);
			foreach ($datefilter as $date) {
				$dates[] = date('Y-m-d', strtotime($date));
			}
			$startdate  = 	$dates[0] ;
			$enddate	= 	$dates[1] ;

			if ($startdate && $enddate) 
			{
				$condition .= " AND ( inv.transactiondate >= '$startdate' AND inv.transactiondate <= '$enddate') ";
			}

			if ($category && $category != 'none') 
			{
                $condition .= " AND itm.classid = '$category'";
            }

			if ($warehouse && $warehouse != 'none') 
			{
                $condition .= " AND isd.warehouse = '$warehouse'";
            }

			$fields     =   array(
				'itmclass.label category',
				'SUM(isd.issueqty)  sales_qty',
				'itm.uom_selling sales_uom',
				'SUM(isd.convissueqty)  base_qty',
				'itm.uom_base base_uom',
				'SUM(isd.amount) amount'
			);

			$result = $this->db->setTable("salesinvoice as inv")
								->setFields($fields)
                                ->leftJoin('salesinvoice_details as isd ON isd.voucherno=inv.voucherno')
                                ->leftJoin('items as itm ON itm.itemcode = isd.itemcode')
								->leftJoin('itemclass as itmclass ON itmclass.id = itm.classid')
                                ->setWhere(" inv.stat NOT IN ('temporary','cancelled') " .$condition)
								->setGroupBy("itm.classid")
								->setOrderBy("itm.classid")
								->runSelect()
								->getResult();
			return $result;
		}

		public function getWarehouseList() {
			$result = $this->db->setTable('warehouse')
							->setFields("warehousecode ind, description val")
							->setWhere("stat = 'active'")
							->runSelect()
							->getResult();
			return $result;
		}
    }

?>