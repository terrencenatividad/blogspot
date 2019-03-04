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

	public function exchangeRate()
	{
		$result = $this->db->setTable('currency')
		->setFields('currencycode')
		->setWhere("stat = 'active'")
		->runSelect()
		->getResult();

		return $result;
	}

	public function checkExisting()
	{
		$result = $this->db->setTable('bank')
		->setFields('gl_code')
		->setWhere("stat = 'active'")
		->runSelect()
		->getResult();

		return $result;
	}

	public function retrieveListing($search="", $sort ,$limit)
	{
		$add_cond 	=	( !empty($search) || $search != "" )  	? 	" AND (shortname LIKE '%$search%' OR b.bankcode LIKE '%$search%' OR b.accountno LIKE '%$search%') " 	: 	"";

		$fields 	=	array('id','shortname','b.bankcode','b.accountno','b.stat', 'checking_account','firstchequeno','nextchequeno');

		$result = $this->db->setTable('bank b')
		->setFields($fields)
		->leftJoin("bankdetail bd ON b.id = bd.bank_id ")
		->setWhere(" b.stat IN  ('active','inactive') " . $add_cond)
		->setOrderBy($sort)
		->setGroupBy("b.id")
		->runPagination();
		return $result;
	}

	public function exportBank($data, $search = '')
	{
		$add_cond =	( !empty($search) || $search != "" )  	? 	"(shortname LIKE '%$search%' OR bankcode LIKE '%$search%'  OR accountno LIKE '%$search%') " 	: 	"";

		$result = $this->db->setTable('bank')
		->setFields($data)
		->setWhere($add_cond)
		->runSelect()
		->getResult();

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

	public function saveUserCSV($values) {

		$result = $this->db->setTable('bank')
		->setValues($values)
		->runInsert();

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

	public function checkGL(){
		$result = $this->db->setTable('chartaccount')
		->setFields('segment5')
		->setWhere(" fspresentation = 'BS' AND accountclasscode = 'CASH' AND accounttype = 'C' AND accountnature = 'Debit' ")
		->setOrderBY("id DESC")
		->runSelect()
		->getResult();

		return $result;
	}

	public function insertCheck($data2){
		$result = $this->db->setTable('bankdetail')
		->setValues($data2)
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

		$fields 	=	array("b.accountno","bank_id","id","booknumber","firstchequeno","lastchequeno" ,"nextchequeno", "bd.entereddate", "bd.stat","shortname","has_cancelled", "bd.check_status","code");

		$result = $this->db->setTable('bankdetail bd')
		->setFields($fields)
		->leftJoin("bank b ON b.id = bd.bank_id ")
		->setWhere("bank_id = '$id' $add_cond ")
		->setOrderBy("nextchequeno + 0 ASC")
		->runPagination();
		return $result;
	}

	public function retrieveCheck($id, $bookno){
		$result = $this->db->setTable('bankdetail')
		->setFields('booknumber, firstchequeno,lastchequeno, code ')
		//->setWhere(" bank_id = '$id' AND firstchequeno = '$bookno'")
		->setWhere(" code = '$id' ")
		->runSelect()
		->getResult();
		return $result;
	}

	public function update_check($id, $data, $code){
		$bno = $data['bank_id'];
		$new = $data['booknumber'];
		$data['nextchequeno'] = $data['firstchequeno'];

		// $condition 	= "code = '$code'";
		// $result 	= $this->db->setTable('bankdetail')
		// ->setWhere($condition)
		// ->runDelete();

		// if($result){
		// 	$result  = $this->db->setTable('bankdetail')
		// 	->setValues($data)
		// 	->runInsert();
		// }
		unset($data['bank_id']);
		$result 	 = $this->db->setTable('bankdetail')
		->setValues($data)
		->setWhere("code = '$code'")
		->setLimit(1)
		->runUpdate();
		
		return $result;
	}

	public function activateCheck($fields, $booknumber){
		$result 			   = $this->db->setTable('bankdetail')
		->setValues($fields)
		->setWhere("code = '$booknumber'")
		->setLimit(1)
		->runUpdate();
		return $result;

	}

	public function deactivateCheck($fields, $booknumber){
		$result 			   = $this->db->setTable('bankdetail')
		->setValues($fields)
		->setWhere("code = '$booknumber'")
		->setLimit(1)
		->runUpdate();

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

	public function deleteCheck($posted_data, $id){
		//$condition 		= "firstchequeno = '$posted_data' AND bank_id = '$id'";
		$condition 		= " code = '$id' ";
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

	public function set_check($bank, $first){
		$con			   	   = " bank_id = '$bank' AND firstchequeno = '$first' ";
		$data['stat']          = 'open';
		$result 			   = $this->db->setTable('bankdetail')
		->setValues($data)
		->setWhere($con)
		->setLimit(1)
		->runUpdate();
		return $result;
	}

	public function check_duplicate_glcode($current){
		$result = $this->db->setTable('bank')
		->setFields('COUNT(accountno) count')
		->setWhere(" gl_code = '$current'")
		->runSelect()
		->getResult();
		return $result;
	}

	public function insertCancelledChecks($data1){
		$data['bank_id'] 			= $data1['id'];
		$data['firstchequeno'] 		= $data1['start'];
		$data['lastchequeno'] 		= $data1['end'];
		$data['firstcancelled'] 	= $data1['firstcancelled'];
		$data['lastcancelled'] 		= $data1['lastcancelled'];
		$data['remarks'] 			= $data1['remarks'];
		$data['stat']				= 'cancelled';
		$data['booknumber'] 		= $data1['booknumber'];
		$result = $this->db->setTable('cancelled_checks')
		->setValues($data)
		->runInsert();
		if ($result){
			$data_check['has_cancelled']          = 'yes';
			$check = $this->db->setTable('bankdetail')
			->setFields('lastchequeno')
			->setWhere("bank_id = {$data['bank_id']}")
			->runSelect()
			->getRow();
			if($check->lastchequeno == $data['lastcancelled']) {
				$data_check['nextchequeno']          = '';
			} else {
				$data_check['nextchequeno']          = $data['lastcancelled'] + 1;
			}

			$result 			   = $this->db->setTable('bankdetail')
			->setValues($data_check)
			->setWhere("bank_id = {$data1['id']} AND firstchequeno={$data1['start']}")
			->setLimit(1)
			->runUpdate();
		}
		return $result;
	}

	public function cancel_list($bank, $firstcheckno){
		$result = $this->db->setTable('cancelled_checks')
		->setFields('firstcancelled,lastcancelled,remarks,entereddate')
		->setWhere("bank_id = '$bank' AND firstchequeno = $firstcheckno ")
		->runSelect()
		->getResult();
		return $result;
	}

	public function get_cancel($bank, $first, $last){
		$result = $this->db->setTable('cancelled_checks')
		->setFields('bank_id')
		->setWhere("bank_id = '$bank' AND firstcancelled >= '$first' AND lastcancelled = '$last'")
		->runSelect()
		->getResult();
		return $result;
	}

	public function checkbooknumber($booknumber) {
		$result = $this->db->setTable('bankdetail')
		->setFields('booknumber')
		->setWhere("booknumber = '$booknumber'")
		->setLimit(1)
		->runSelect(false)
		->getRow();

		if ($result) {
			return false;
		} else {
			return true;
		}
	}

	public function checkpreviouslycancelled($book, $id, $input){
		$result = $this->db->setTable("cancelled_checks")
		->setFields(array("firstcancelled","lastcancelled"))
		->setWhere("bank_id = '$id' AND booknumber = '$book' AND (firstcancelled <= '$input' AND '$input' <= lastcancelled)")
		->runSelect()
		->getResult();
		return $result;
	}
}
?>