<?php
class average_cost_model extends wc_model {

	public function getAverageCostPagination($search) {
		$query	= $this->getQueryDetails($search); 
		$result	= $query->runPagination();

		return $result;
	}
	
	public function getAverageCostBreakdownPagination($itemcode) {
		$query	= $this->getBreakdownQueryDetails($itemcode); 
		$result	= $query->runPagination();
	
		return $result;
	}

	public function getAverageCost($search) {
		$query	= $this->getQueryDetails($search); 
		$result	= $query->runSelect()
						->getResult();

		return $result;
	}
	
	public function getAverageCostBreakdown($itemcode) {
		$query	= $this->getBreakdownQueryDetails($itemcode); 
		$result	= $query->runSelect()
						->getResult();

		return $result;
	}



	private function getQueryDetails($search) {
		$condition = '';

		if ($search) {
			$condition = " AND " . $this->generateSearch($search, array('i.itemcode' , 'i.itemname' , 'i.itemdesc'));
		}

		$query = $this->db->setTable('items i')
							->setFields("i.itemcode, i.itemname, i.itemdesc, i.uom_base, pa.stock_quantity, pa.price_average")
							->leftJoin('price_average pa ON i.itemcode = pa.itemcode AND i.companycode = pa.companycode')
							->leftJoin('price_average pa2 ON i.itemcode = pa2.itemcode AND i.companycode = pa2.companycode AND pa.linenum < pa2.linenum')
							->setWhere('pa2.linenum IS NULL' . $condition);

		return $query;
	}

	private function getBreakdownQueryDetails($itemcode) {
		$query = $this->db->setTable('price_average i')
							->setFields("movementdate, documentno, doctype, movement_quantity, stock_quantity, purchase_price, price_average")
							->setWhere("itemcode = '$itemcode'")
							->setOrderBy('linenum DESC');
		return $query;
	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

}