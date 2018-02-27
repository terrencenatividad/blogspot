<?php
	class sales_transfer extends wc_model{

		public function sales_transferlist($start, $end, $warehouse1, $warehouse2, $limit, $filter , $sort){
			$condition = '';
			$add = '';
			if ($start && $end) {
				$condition .= " main.date >= '$start' AND main.date <= '$end'";
			}

			if ($warehouse1) {
				$condition .= "AND main.source  = '$warehouse1'";
			}

			if ($warehouse2) {
				$condition .= "AND main.destination = '$warehouse2'";
			}

			if ($filter == 'all') {
				$condition .= "";
			} else if ($filter == 'transferred'){
				$condition .= "AND status = 'open' AND source_no != '' ";
			} else if ($filter == 'partial'){
				$condition .= "AND status = 'partial'";
			} else if ($filter == 'open'){
				$condition .= "AND status = 'open' AND source_no = '' ";
			} else if ($filter == 'rejected'){
				$condition .= "AND status = 'rejected'";
			} else if ($filter == 'approved'){
				$condition .= "AND status = 'approved'";
			} else if ($filter == 'closed'){
				$condition .= "AND status = 'closed'";
			} else if ($filter == 'posted'){
				$condition .= "AND status = 'posted'";
			}

			$sub = "SELECT st.stat status, st.stocktransferno st_no ,  st.transactiondate date, w.description source,  wh.description destination,SUM( std.qtytoapply ) qty, '' source_no,
			st.companycode
			FROM stock_transfer st
			LEFT JOIN warehouse w ON w.warehousecode = st.source
			AND w.companycode = st.companycode
			LEFT JOIN warehouse wh ON wh.warehousecode = st.destination
			AND wh.companycode = st.companycode
			LEFT JOIN stock_transfer_details std ON st.stocktransferno = std.stocktransferno
			LEFT JOIN stock_approval sa ON st.stocktransferno = sa.source_no
			AND std.companycode = st.companycode
			WHERE st.stat != 'cancelled'  
			GROUP BY st.stocktransferno 

			UNION 

			SELECT st.stat status, st.source_no  ,  st.transactiondate date, w.description source,  wh.description destination,SUM( std.qtytoapply ) qty, st.stocktransferno st_no ,st.companycode
			FROM stock_approval st
			LEFT JOIN warehouse w ON w.warehousecode = st.source
			AND w.companycode = st.companycode
			LEFT JOIN warehouse wh ON wh.warehousecode = st.destination
			AND wh.companycode = st.companycode
			LEFT JOIN stock_approval_details std ON st.stocktransferno = std.stocktransferno
			AND std.companycode = st.companycode
			WHERE st.stat != 'cancelled' 
			GROUP BY st.stocktransferno ";

			$result = 	$this->db->setTable("($sub) main")
						->setFields('main.status,main.st_no,main.date,main.source,main.destination,main.qty,source_no')
						->setWhere($condition)
						->setOrderBy($sort)
						->runPagination();
						// echo $this->db->getQuery();
			return $result;
		}

		public function fileExport($start, $end, $warehouse1, $warehouse2, $limit , $filter, $sort){
			$condition = '';
			$condition = '';
			$add = '';
			if ($start && $end) {
				$condition 	.= " main.date >= '$start' AND main.date <= '$end'";
			}

			if ($warehouse1) {
				$condition 	.= "AND main.source  = '$warehouse1'";
			}

			if ($warehouse2) {
				$condition 	.= "AND main.destination = '$warehouse2'";
			}

			if ($filter == 'all') {
				$condition .= "";
			} else if ($filter == 'transferred'){
				$condition .= "AND status = 'open' AND source_no != '' ";
			} else if ($filter == 'partial'){
				$condition .= "AND status = 'partial'";
			} else if ($filter == 'open'){
				$condition .= "AND status = 'open' AND source_no = '' ";
			} else if ($filter == 'rejected'){
				$condition .= "AND status = 'rejected'";
			} else if ($filter == 'approved'){
				$condition .= "AND status = 'approved'";
			} else if ($filter == 'closed'){
				$condition .= "AND status = 'closed'";
			} else if ($filter == 'posted'){
				$condition .= "AND status = 'posted'";
			}

			$sub = "SELECT st.stat status, st.stocktransferno st_no ,  st.transactiondate date, w.description source,  wh.description destination,SUM( std.qtytoapply ) qty, '' source_no,
			st.companycode
			FROM stock_transfer st
			LEFT JOIN warehouse w ON w.warehousecode = st.source
			AND w.companycode = st.companycode
			LEFT JOIN warehouse wh ON wh.warehousecode = st.destination
			AND wh.companycode = st.companycode
			LEFT JOIN stock_transfer_details std ON st.stocktransferno = std.stocktransferno
			LEFT JOIN stock_approval sa ON st.stocktransferno = sa.source_no
			AND std.companycode = st.companycode
			WHERE st.stat != 'cancelled'  
			GROUP BY st.stocktransferno 

			UNION 

			SELECT st.stat status, st.source_no  ,  st.transactiondate date, w.description source,  wh.description destination,SUM( std.qtytoapply ) qty, st.stocktransferno st_no ,st.companycode
			FROM stock_approval st
			LEFT JOIN warehouse w ON w.warehousecode = st.source
			AND w.companycode = st.companycode
			LEFT JOIN warehouse wh ON wh.warehousecode = st.destination
			AND wh.companycode = st.companycode
			LEFT JOIN stock_approval_details std ON st.stocktransferno = std.stocktransferno
			AND std.companycode = st.companycode
			WHERE st.stat != 'cancelled' 
			GROUP BY st.stocktransferno ";

			$result = 	$this->db->setTable("($sub) main")
						->setFields('main.status,main.st_no,main.date,main.source,main.destination,main.qty,source_no')
						->setWhere($condition)
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