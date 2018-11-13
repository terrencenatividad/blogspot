<?php 
    class brand extends wc_model
    {

        public function __construct() 
        {
            parent::__construct();
            $this->log = new log();
        }

        public function retrieveListing($search="", $sort ,$limit)
		{
			$add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND (brandcode LIKE '%$search%' OR brandname LIKE '%$search%' OR stat LIKE '%$search') " 	: 	"";

			$fields 	=	array('brandcode','brandname','stat');

			//added for proper order by of string
			if(empty($sort) || $sort = "") {
				$sort = "LENGTH(brandcode), brandcode";
			}

			$result = $this->db->setTable('brands')
							->setFields($fields)
							->setWhere(" stat != 'deleted' $add_cond ")
							->setOrderBy($sort)
							->runPagination();
			return $result;
			// echo $this->db->getQuery();
        }
        
        public function retrieveExistingBrand($data, $brandcode)
		{
			$condition 		=	" brandcode = '$brandcode' ";
			
			return $this->db->setTable('brands')
							->setFields($data)
							->setWhere($condition)
							->runSelect()
							->getRow();
		}
		
		public function check_duplicate($current)
		{
			return $this->db->setTable('brands')
							->setFields('COUNT(brandcode) count')
							->setWhere(" brandcode = '$current'")
							->runSelect()
							->getResult();
		}
		
		public function updateBrand($data, $brandcode)
		{
			
			$condition 			   = " brandcode = '$brandcode' ";

			$result 			   = $this->db->setTable('brands')
											  ->setValues($data)
											  ->setWhere($condition)
											  ->setLimit(1)
											  ->runUpdate();
			
			return $result;
		}

		public function insertBrand($data)
		{
			$result = $this->db->setTable('brands')
					->setValues($data)
					->runInsert();
			return $result;
		}

		public function deleteBrand($id)
		{
			$condition   = "";
			$id_array 	 = explode(',', $id['id']);
			$errmsg 	 = array();

			for($i = 0; $i < count($id_array); $i++)
			{
				$brandcode    = $id_array[$i];

				$condition 		= " brandcode = '$brandcode'";
				
				$result 		= $this->db->setTable('brands')
										->setWhere($condition)
										->runDelete();
										// echo $this->db->getQuery();

				$error 			= $this->db->getError();		

				if ($error == 'locked') {
					$errmsg[]  = "<p class = 'no-margin'>Deleting Currency: $brandcode</p>";
				}
			
			}
			
			return $errmsg;
		}

		public function updateStat($data,$code)
		{
			$condition 			   = " brandcode = '$code' ";

			$result 			   = $this->db->setTable('brands')
												->setValues($data)
												->setWhere($condition)
												->setLimit(1)
												->runUpdate();

			return $result;
		}

		public function getBrand($code) {
			$result = $this->db->setTable('brands')
						->setFields('brandcode, brandname')
						->setWhere("brandcode = '$code'")
						->runSelect()
						->getRow();
	
			return $result;
		}

		public function getExport($search, $sort)
		{
			$add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND (brandcode LIKE '%$search%' OR brandname LIKE '%$search%' OR stat LIKE '%$search') " 	: 	"";

			$fields 	=	array('brandcode','brandname','stat');

			//added for proper order by of string
			if(empty($sort) || $sort = "") {
				$sort = "LENGTH(brandcode), brandcode";
			}

			$result = $this->db->setTable('brands')
							->setFields($fields)
							->setWhere(" stat != 'deleted' $add_cond ")
							->setOrderBy($sort)
							->runSelect()
							->getResult();
			return $result;
		}

		public function importBrand($data)
		{
			$result = $this->db->setTable('brands')
					->setValuesFromPost($data)
					->runInsert();

			return $result;
		}

		public function checkExistingBrandcode($data) {
			$item_types = "'" . implode("', '", $data) . "'";
	
			$result = $this->db->setTable('brands')
								->setFields('brandcode')
								->setWhere("brandcode IN ($item_types)")
								->runSelect()
								->getResult();
			
			return $result;
		}

		public function checkAccess($groupname)
		{
			$result = $this->db->setTable( PRE_TABLE .'_module_access')
								->setFields('mod_edit')
								->setWhere("groupname = '$groupname' AND module_name='Brand'")
								->runSelect()
								->getRow();
			
			return $result;
		}
    }
?>