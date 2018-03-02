<?php
	class sales_transfer extends wc_model{

		public function sales_transferlist($start, $end, $warehouse1, $warehouse2, $limit, $filter , $sort){
			$condition = '';
			$add = '';
			if ($start && $end) {
				$condition .= "AND sa.transactiondate >= '$start' AND sa.transactiondate <= '$end'";
			}
			

			if ($warehouse1 && $warehouse1 != 'none') {
				$condition .= "AND w.description  = '$warehouse1'";
			}

			if ($warehouse2 && $warehouse2 != 'none') {
				$condition .= "AND wh.description = '$warehouse2'";
			}
		
			$result = 	$this->db->setTable("stock_approval sa")
						->setFields('sa.transactiondate date ,w.description source, wh.description destination, sa.stocktransferno, itemcode, detailparticular,source_no ,qtytransferred, uom')
						->leftJoin("stock_approval_details sad ON sa.stocktransferno = sad.stocktransferno")
						->leftJoin("warehouse w ON sa.source = w.warehousecode")
						->leftJoin("warehouse wh ON sa.destination = wh.warehousecode")
						->setWhere("sa.stat != 'cancelled'" .$condition)
						->setOrderBy($sort)
						->runPagination();
			return $result;
		}

		public function fileExport($start, $end, $warehouse1, $warehouse2, $limit , $filter, $sort){
			$condition = '';
			$add = '';
			if ($start && $end) {
				$condition .= "AND sa.transactiondate >= '$start' AND sa.transactiondate <= '$end'";
			}
			

			if ($warehouse1 && $warehouse1 != 'none') {
				$condition .= "AND w.description  = '$warehouse1'";
			}

			if ($warehouse2 && $warehouse2 != 'none') {
				$condition .= "AND wh.description = '$warehouse2'";
			}
		
			$result = 	$this->db->setTable("stock_approval sa")
						->setFields('sa.transactiondate date,w.description source, wh.description destination, sa.stocktransferno, itemcode, detailparticular,source_no ,qtytransferred, uom')
						->leftJoin("stock_approval_details sad ON sa.stocktransferno = sad.stocktransferno")
						->leftJoin("warehouse w ON sa.source = w.warehousecode")
						->leftJoin("warehouse wh ON sa.destination = wh.warehousecode")
						->setWhere("sa.stat != 'cancelled'" .$condition)
						->setOrderBy($sort)
						->runSelect()
						->getResult();
			return $result;
		}

		public function getWarehouseList() {
			$result = $this->db->setTable('warehouse')
							->setFields("description ind, description val")
							->setWhere("stat = 'active'")
							->runSelect()
							->getResult();
			return $result;
		}

		public function getItemList() {
			$result = $this->db->setTable('items i')
							->setFields("i.itemcode ind, i.itemcode val")
							->runSelect()
							->getResult();

			return $result;
		}

		private function removeComma($data) {
			if (is_array($data)) {
				$temp = array();
				foreach ($data as $val) {
					$temp[] = $this->removeComma($val);
				}
				return $temp;
			} else {
				return str_replace(',', '', $data);
			}
		}

		private function generateSearch($search, $array) {
			$temp = array();
			foreach ($array as $arr) {
				$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
			}
			return '(' . implode(' OR ', $temp) . ')';
		}

		public function retrieveData($table, $fields = array(), $cond = "", $join = "", $orderby = "", $groupby = "")
		{
			$result = $this->db->setTable($table)
						->setFields($fields)
						->leftJoin($join)
						->setGroupBy($groupby)
						->setWhere($cond)
						->setOrderBy($orderby)
						->runSelect()
						->getResult();
			
			return $result;
		}

		public function getValue($table, $cols = array(), $cond, $orderby = "", $bool = "")
		{
			$result = $this->db->setTable($table)
						->setFields($cols)
						->setWhere($cond)
						->setOrderBy($orderby)
						->runSelect($bool)
						->getResult();

			return $result;
		}
	}
?>