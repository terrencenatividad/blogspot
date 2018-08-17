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
							->setWhere(" stat = 'active' $add_cond ")
							->setOrderBy($sort)
							->runPagination();
			return $result;
			// echo $this->db->getQuery();
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
						// echo $this->db->getQuery();
			return $result;
		}

		public function insertBank($data)
		{
			// $bankname 	= $data['shortname'];
			// $accountno 	= $data['accountno'];
		
			// $cashAccount	= $this->GetValue("chartaccount","segment5","(id != '' AND id != '-')  AND accountclasscode = 'CASH' AND accounttype = 'B' LIMIT 1 ");
			// $cashAccount   	= $cashAccount[0]->segment5;
			// $cashAccount_Arr= explode('-',$cashAccount);
			// $cashLevel		= $this->GetValue("chartaccount","id","(id != '' AND id != '-')  AND accountclasscode = 'CASH'  AND accounttype = 'B' LIMIT 1 ");
			// $childCount		= $this->GetValue("chartaccount","COUNT(segment5) seg5"," parentaccountcode = '$cashAccount' AND accountclasscode = 'CASH' AND accounttype = 'C'  ");
			// $childCount     = $childCount[0]->seg5;
			// $childCount		= $childCount + 1;
			
			// $accountinfo['segment5']			= $cashAccount_Arr[0].'-'.str_pad($cashAccount_Arr[1] + $childCount, 3, 0, STR_PAD_LEFT);
			// $accountinfo['accountname']			= "Cash in Bank - ".$bankname." (".$accountno.")";
			// $accountinfo['accounttype']			= 'C';
			// $accountinfo['accountnature']		= 'Debit';
			// $accountinfo['fspresentation']		= 'BS';
			// $accountinfo['accountclasscode']	= 'CASH';
			// $accountinfo['parentaccountcode']	= $cashAccount;

			// $result = $this->db->setTable('chartaccount')
			// 		->setValues($accountinfo)
			// 		->runInsert();

			// $accountlevel = $this->db->getInsertId();

			// if ($result) {
			$data_post_dtl['gl_code'] 		= $data['gl_code'];
			$data_post_dtl['shortname'] 	= $data['shortname'];
			$data_post_dtl['bankcode'] 		= $data['bankcode'];
			$data_post_dtl['accountno'] 	= $data['accountno'];
			$data_post_dtl['address1'] 		= $data['address1'];
			$data_post_dtl['currency'] 		= $data['currency'];
			// $data_post_dtl['accountlevel'] 	= $accountlevel;
			$data_post_dtl['checking_account'] 	= $data['checking_account'];

			$result = $this->db->setTable('bank')
					->setValues($data_post_dtl)
					->runInsert();
			// }
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
							// echo $this->db->getQuery();
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
			$data_post_dtl['nextchequeno'] 		= $data2['firstchequeno'] + 1;

			$result = $this->db->setTable('bankdetail')
					->setValues($data_post_dtl)
					->runInsert();
			return $result;

		}
		public function getAccountname($id){
			$result = $this->db->setTable('bank')
					->setFields('id, shortname ')
					->setWhere(" id = '$id'")
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

		public function update_check($id, $data){
			$bno = $data['bank_id'];
			$condition 			   = " booknumber = '$bno' ";

			$result 			   = $this->db->setTable('bankdetail')
											  ->setValues($data)
											  ->setWhere($condition)
											  ->setLimit(1)
											  ->runUpdate();
			return $result;
		}

		
	}
?>