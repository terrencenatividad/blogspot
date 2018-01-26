<?php
	class exchange_rate extends wc_model
	{
		public function retrieveListing($search="", $sort, $limit)
		{
			$add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND ( basecurrencycode LIKE '%$search%' OR exchangecurrencycode LIKE '%$search%'  ) " 	: 	"";

			$fields 	=	array('basecurrencycode','exchangecurrencycode','exchangerate','effectivedate','code');

			$result = $this->db->setTable('exchangerate')
							->setFields($fields)
							->setWhere(" stat = 'active' $add_cond ")
							->setOrderBy($sort)
							->runPagination();
			return $result;
		}

		public function retrieveExistingRate($data, $code)
		{
			$condition 		=	" code = '$code' ";
			return $this->db->setTable('exchangerate')
							->setFields($data)
							->setWhere($condition)
							->runSelect()
							->getRow();
		}

		public function retrieveExchangeRateDropdown()
		{
			$result = $this->db->setTable('currency')
							->setFields('currencycode ind, currency val')
							->runSelect()
							->getResult();
                           
            return $result;
        }

		public function insertExchangeRate($data)
		{	
			$data['code'] 			=	$data['basecurrencycode'].'TO'.$data['exchangecurrencycode'];

			$effectivedate 			=	$data['effectivedate'];
			$data['effectivedate'] 	=	date("Y-m-d",strtotime($effectivedate));
			$result = $this->db->setTable('exchangerate')
					->setValues($data)
					->runInsert();

			return $result;
		}

		public function updateExchangeRate($data, $code)
		{

			$condition 			   	= " code = '$code' ";
			$effectivedate 			=	$data['effectivedate'];
			$data['effectivedate'] 	=	date("Y-m-d",strtotime($effectivedate));

			$result 			   	= $this->db->setTable('exchangerate')
											  ->setValues($data)
											  ->setWhere($condition)
											  ->setLimit(1)
											  ->runUpdate();
			return $result;
		}

		public function deleteExchangeRate($id)
		{
			$condition   = "";
			$id_array 	 = explode(',', $id['id']);
			$errmsg 	 = array();

			for($i = 0; $i < count($id_array); $i++)
			{
				$code 	= $id_array[$i];

				$condition 		= " code = '$code' ";
				
				$result 		= $this->db->setTable('exchangerate')
										->setWhere($condition)
										->runDelete();

				$error 			= $this->db->getError();		

				if ($error == 'locked') {
					$errmsg[]  = "<p class = 'no-margin'>Deleting Rate: $base -> $exchange</p>";
				}
			}
			
			return $errmsg;
		}
	}
?>