<?php
	class bank extends wc_model
	{
		

		public function retrieveExchangeRateDropdown()
		{
			$result = $this->db->setTable('currency')
							->setFields('currencycode ind, currency val')
							->setWhere("stat = 'active'")
							->runSelect()
							->getResult();
                           
            return $result;
        }
		
		public function retrieveListing($search="", $sort ,$limit)
		{
			$add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND (shortname LIKE '%$search%' OR bankcode LIKE '%$search%'  OR accountno LIKE '%$search%') " 	: 	"";

			$fields 	=	array('id','shortname','bankcode','accountno','stat', 'checking_account');

			$result = $this->db->setTable('bank')
							->setFields($fields)
							->setWhere(" stat IN  ('active','inactive') $add_cond ")
							->setOrderBy($sort)
							->runPagination();
			return $result;
		}

		public function retrieveExistingBank($data, $id)
		{
			$condition 		=	" id = '$id' ";
			
			return $this->db->setTable('bank')
							->setFields($data)
							->setWhere($condition)
							->runSelect()
							->getRow();
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

		public function insertBank($data)
		{
			$data_post_dtl['gl_code'] 		= $data['gl_code'];
			$data_post_dtl['shortname'] 	= $data['shortname'];
			$data_post_dtl['bankcode'] 		= $data['bankcode'];
			$data_post_dtl['accountno'] 	= $data['accountno'];
			$data_post_dtl['address1'] 		= $data['address1'];
			$data_post_dtl['currency'] 		= $data['currency'];
			$data_post_dtl['checking_account'] 	= $data['checking_account'];

			$result = $this->db->setTable('bank')
					->setValues($data_post_dtl)
					->runInsert();
			return $result;
		}

		public function updateBank($data, $id)
		{
			
			$condition 			   = " id = '$id' ";
			$result 			   = $this->db->setTable('bank')
											  ->setValues($data)
											  ->setWhere($condition)
											  ->setLimit(1)
											  ->runUpdate();

			return $result;
		}

		public function deleteBank($id)
		
		{
			$condition   = "";
			$id_array 	 = explode(',', $id['id']);
			$errmsg 	 = array();

			for($i = 0; $i < count($id_array); $i++)
			{
				$id    = $id_array[$i];

				$condition 		= " id = '$id'";
				
				$result 		= $this->db->setTable('bank')
										->setWhere($condition)
										->runDelete();

				$error 			= $this->db->getError();		

				if ($error == 'locked') {
					$errmsg[]  = "<p class = 'no-margin'>Deleting Bank: $id </p>";
				}
			
			}
			
			return $errmsg;
		}

		public function check_duplicate($current)
		{
			$result = $this->db->setTable('bank')
							->setFields('COUNT(accountno) count')
							->setWhere(" accountno = '$current'")
							->runSelect()
							->getResult();
			return $result;
		}

		public function retrieveGLDropdown(){
			$result = $this->db->setTable('chartaccount')
							->setFields('segment5 ind, accountname val ')
							->setWhere(" fspresentation = 'BS' AND accountclasscode = 'CASH' AND accounttype = 'C' AND accountnature = 'Debit' ")
							->setOrderBY("id DESC")
							->runSelect()
							->getResult();
                           
            return $result;
		}

		public function insertCheck($data2){
			$data_post_dtl['bank_id'] 			= $data2['bank_id'];
			$data_post_dtl['booknumber'] 		= $data2['booknumber'];
			$data_post_dtl['firstchequeno'] 	= $data2['firstchequeno'];
			$data_post_dtl['lastchequeno'] 		= $data2['lastchequeno'];
			$data_post_dtl['nextchequeno'] 		= $data2['firstchequeno'];

			$result = $this->db->setTable('bankdetail')
					->setValues($data_post_dtl)
					->runInsert();
			return $result;

		}
		public function getAccountname($id){
			$result = $this->db->setTable('bank b')
					->setFields('id, shortname, firstchequeno, lastchequeno ')
					->setWhere("id = '$id'")
					->leftJoin("bankdetail bd ON b.id = bd.bank_id ")
					->runSelect()
					->getResult();
			return $result;


		}
		public function checkListing($search="", $sort ,$limit, $id){
			$add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND (shortname LIKE '%$search%' OR bankcode LIKE '%$search%'  OR accountno LIKE '%$search%') " 	: 	"";

			$fields 	=	array("b.accountno","bank_id","id","booknumber","CONCAT(firstchequeno, ' - ' ,lastchequeno) batch" ,"nextchequeno");

			$result = $this->db->setTable('bankdetail bd')
							->setFields($fields)
							->leftJoin("bank b ON b.id = bd.bank_id ")
							->setWhere(" bd.stat = 'open' AND bank_id = '$id' $add_cond ")
							->setOrderBy($sort)
							->runPagination();
			return $result;
		}

		public function retrieveCheck($id, $bookno){
			$result = $this->db->setTable('bankdetail')
					->setFields('booknumber, firstchequeno,lastchequeno ')
					->setWhere(" bank_id = '$id' AND booknumber = '$bookno'")
					->runSelect()
					->getResult();
			return $result;
		}

		public function update_check($id, $data, $old){
			$bno = $data['bank_id'];
			$new = $data['booknumber'];
			$data['nextchequeno'] = $data['firstchequeno'];
			
			$condition 	= "booknumber = '$old'";
			$result 	= $this->db->setTable('bankdetail')
							->setWhere($condition)
							->runDelete();

			if($result){
				$result  = $this->db->setTable('bankdetail')
						->setValues($data)
						->runInsert();
			}
	
			return $result;
		}

		public function deactivateBank($id, $data){
			$con			   	   = " id = '$id' ";
			$result 			   = $this->db->setTable('bank')
											  ->setValues($data)
											  ->setWhere($con)
											  ->setLimit(1)
											  ->runUpdate();
			return $result;

		}

		public function getBank($id){
			$condition   = "";
			$id_array 	 = explode(',', $id['id']);
			foreach ($id_array as $id ) {
				
				$condition 		= " id = '$id'";
				$fields = array(
							'shortname',
							'accountno',
						);
			
				$result = $this->db->setTable('bank')
							->setWhere($condition)
							->setFields($fields)
							->runSelect()
							->getResult();	
			}

			return $result; 

		}

		public function getInfo($id){
			$fields = array(
				'shortname',
				'accountno',
			);
			$condition 		= " id = '$id'";
			$result = $this->db->setTable('bank')
							->setWhere($condition)
							->setFields($fields)
							->runSelect()
							->getResult();	
			return $result;
		}

		public function deleteCheck($id){
			$condition 		= "booknumber = '$id'";
			$result 		= $this->db->setTable('bankdetail')
								->setWhere($condition)
								->runDelete();
			return $result ;
		}

		public function check_duplicate_booknums($current){
			$result = $this->db->setTable('bankdetail')
							->setFields('COUNT(booknumber) count')
							->setWhere("booknumber = '$current'")
							->runSelect()
							->getResult();
			return $result;

		}

		
	}
?>