<?php
class controller extends wc_controller 
{

	public function __construct()
	{
		parent::__construct();
		$this->companyclass = new companyclass();
		$this->input        = new input();
	}

	public function edit()
	{
		$this->url 			= new url();
		$ui 	 			= new ui();
		$this->view->title  = 'Company';
		
		/**
		 * Initiate Variables
         */
		$data_var = array(
			'companycode' 	=> '',
			'companyimage' 	=> '',
			'companyname' 	=> '',
			'businesstype'	=> '',
			'contactname'	=> '',
			'contactrole'	=> '',
			'phone'			=> '',
			'mobile'		=> '',
			'address'		=> '',
			'email'			=> '',
			'taxyear' 		=> '',
			'periodstart' 	=> '',
			'wtax_option' 	=> '',
			'lastname'		=> '',
			'firstname'		=> '',
			'middlename'	=> '',
			'businessline'	=> '',
			'signatory_name'=> '',
			'signatory_role'=> '',
			'signatory_tin'	=> '',
			'tin'			=> ''
		);

		/**
		* Retrieve Data
		*/
		$companydata = $this->companyclass->retrieveData($data_var," companycode = 'CID' ");
		if(!empty($companydata))
		{
			/**
			* Convert retrieved data to array
			*/
			$data_var = (array) $companydata;
		}

		/**
		* Pass ui class object to be tagged 
		*/
		$data_var['ui'] 				= $ui;
		$data_var['taxyear_list']		= $this->companyclass->getOption("tax_year");
		$data_var['wtax_option_list']	= $this->companyclass->getOption("wtax_option");
		$data_var['period_list']		= $this->companyclass->getPeriods();
		$data_var['taxyear_lock']   	= ($data_var['taxyear'] == 'calendar') ? "disabled" : "";
		$data_var['businesstype_list']	= $this->companyclass->getOption("businesstype");
		$this->view->load('company', $data_var);
	}

	/**
	* Function to handle ajax calls
	*/
	public function ajax($task)
	{
		header('Content-type: application/json');

		if ($task == 'add') {
			$result = $this->add();
		}else if($task == 'update'){
			$condition = " companycode = 'CID' ";
			$result = $this->update($condition);
		}else if($task == 'upload'){
			$result = $this->upload();
		}

		echo json_encode($result);
	}

	/**
	* Function to add new record
	*/
	private function add()
	{
		$data_var = array(
			'companycode',
			'companyimage',
			'companyname',
			'businesstype',
			'contactname',
			'contactrole',
			'phone',
			'mobile',
			'address',
			'email',
			'taxyear',
			'periodstart',
			'wtax_option',
			'lastname',
			'firstname',
			'middlename',
			'businessline',
			'signatory_name',
			'signatory_role',
			'signatory_tin',
			'tin'
		);
		
		/**
		 * Handle POST values
         */
		$data = $this->input->post($data_var);

		/**
		* Insert to Database
		*/
		$result = $this->companyclass->updateData('add',$data);

		$code   = ($result) ? 1 : 0;
		$msg 	= $result;

		$returnArray	= array(
						'code'=>$code,
						'msg'=>$msg
					);
	
		return $returnArray;
	}

	/**
	* Function to update record
	* @param condition
	*/
	private function update($condition)
	{
		$data_var = array(
			'companycode',
			'companyname',
			'businesstype',
			'contactname',
			'contactrole',
			'phone',
			'mobile',
			'address',
			'email',
			'taxyear',
			'periodstart',
			'wtax_option',
			'lastname',
			'firstname',
			'middlename',
			'businessline',
			'signatory_name',
			'signatory_role',
			'signatory_tin',
			'tin'
		);

		/**
		 * Handle POST values
         */
		$data = $this->input->post($data_var);

		/**
		* Update Database
		*/
		$result = $this->companyclass->updateData('update',$data,$condition);

		$code   = ($result) ? 1 : 0;
		$msg 	= $result;

		$returnArray	= array(
						'code'=>$code,
						'msg'=>$msg
					);
	
		return $returnArray;
	}

	private function upload()
	{
		$this->session        = new session();
		// GET SESSION VARIABLE
		$login 			= $this->session->get('login');
		$companycode 	= $login['companycode'];

		$data = $this->input->post();
		if(isset($data))
		{
			define ('SITE_ROOT', realpath(dirname(__FILE__)));

			############ Edit settings ##############
			$ThumbSquareSize 		= 100; //Thumbnail will be 200x200
			$BigImageMaxSize 		= 200; //Image Maximum height or width
			$ThumbPrefix			= "thumb_"; //Normal thumb Prefix
			//$DestinationDirectory	= SITE_ROOT.'/uploads/company_logo/'; //specify upload directory ends with / (slash)
			$DestinationDirectory	= str_replace('apanel/index.php', '', $_SERVER["SCRIPT_FILENAME"])."uploads/company_logo/";
			$Quality 				= 90; //jpeg quality
			##########################################
		
			//check if this is an ajax request
			if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
				die();
			}
		
			// check $_FILES['ImageFile'] not empty
			if(!isset($_FILES['upload_logo']) || !is_uploaded_file($_FILES['upload_logo']['tmp_name']))
			{
				die('Something wrong with uploaded file, something missing!'); // output error when above checks fail.
			}

			// Random number will be added after image name
			$RandomNumber 	= rand(0, 9999999999); 

			$companydata 	= $this->companyclass->retrieveData(array('companyimage' => '')," companycode = '$companycode' AND stat = 'active' ");
			$data_var 		= (array) $companydata;
			
			$company_logo	= $data_var['companyimage'];
			if(file_exists($DestinationDirectory.$company_logo) && !empty($company_logo)){
				unlink($DestinationDirectory.$company_logo);
			}

			$ImageName 		= str_replace(' ','-',strtolower($_FILES['upload_logo']['name'])); //get image name
			$ImageSize 		= $_FILES['upload_logo']['size']; // get original image size
			$TempSrc	 	= $_FILES['upload_logo']['tmp_name']; // Temp name of image file stored in PHP tmp folder
			$ImageType	 	= $_FILES['upload_logo']['type']; //get file type, returns "image/png", image/jpeg, text/plain etc.
		
			//Let's check allowed $ImageType, we use PHP SWITCH statement here
			switch(strtolower($ImageType))
			{
				case 'image/png':
					//Create a new image from file 
					$CreatedImage =  imagecreatefrompng($_FILES['upload_logo']['tmp_name']);
					break;
				case 'image/gif':
					$CreatedImage =  imagecreatefromgif($_FILES['upload_logo']['tmp_name']);
					break;			
				case 'image/jpeg':
				case 'image/pjpeg':
					$CreatedImage = imagecreatefromjpeg($_FILES['upload_logo']['tmp_name']);
					break;
				default:
					die('Unsupported File!'); //output error and exit
			}

			//PHP getimagesize() function returns height/width from image file stored in PHP tmp folder.
			//Get first two values from image, width and height. 
			//list assign svalues to $CurWidth,$CurHeight
			list($CurWidth,$CurHeight)=getimagesize($TempSrc);
		
			//Get file extension from Image name, this will be added after random name
			$ImageExt = substr($ImageName, strrpos($ImageName, '.'));
			$ImageExt = str_replace('.','',$ImageExt);
		
			//remove extension from filename
			$ImageName 		= preg_replace("/\\.[^.\\s]{3,4}$/", "", $ImageName); 
		
			//Construct a new name with random number and extension.
			//$NewImageName = $ImageName.'-'.$RandomNumber.'.'.$ImageExt;
			$NewImageName = $RandomNumber.'.'.$ImageExt;
		
			//set the Destination Image
			$thumb_DestRandImageName 	= $DestinationDirectory.$ThumbPrefix.$NewImageName; //Thumbnail name with destination directory
			$DestRandImageName 			= $DestinationDirectory.$NewImageName; // Image with destination directory

			//Resize image to Specified Size by calling resizeImage function.
			if($this->resizeImage($CurWidth,$CurHeight,$BigImageMaxSize,$DestRandImageName,$CreatedImage,$Quality,$ImageType))
			{
				/**UPDATE COMPANY**/
				$update_info['companyimage']  	= $NewImageName;
				$result = $this->companyclass->updateData('update',$update_info," stat = 'active' ");

				$code 	= 1;
				$msg 	= 'System Logo successfully uploaded.';
			}else{
				$code 	= 0;
				$msg 	= 'Upload Error!';
			}

			$returnArray = array(
				'code'=> $code,
				'msg'=> $msg
			);
			return $returnArray;
		}
	}

	// This function will proportionally resize image 
	private function resizeImage($CurWidth,$CurHeight,$MaxSize,$DestFolder,$SrcImage,$Quality,$ImageType)
	{
		//Check Image size is not 0
		if($CurWidth <= 0 || $CurHeight <= 0) 
		{
			return false;
		}
	
		//Construct a proportional size of new image
		$ImageScale      	= min($MaxSize/$CurWidth, $MaxSize/$CurHeight); 
		$NewWidth  			= ceil($ImageScale*$CurWidth);
		$NewHeight 			= ceil($ImageScale*$CurHeight);
		$NewCanves 			= imagecreatetruecolor($NewWidth, $NewHeight);
	
		imagealphablending($NewCanves, false);
		$background = imagecolorallocatealpha($NewCanves, 255, 255, 255, 127);
		imagefilledrectangle($NewCanves, 0, 0, $NewWidth, $NewHeight, $background);
		imagealphablending($NewCanves, true);

		// Resize Image
		if(imagecopyresampled($NewCanves, $SrcImage,0, 0, 0, 0, $NewWidth, $NewHeight, $CurWidth, $CurHeight))
		{
			switch(strtolower($ImageType))
			{
				case 'image/png':
					imagealphablending($NewCanves, true);        
					imagesavealpha($NewCanves, true);
				   
					imagepng($NewCanves,$DestFolder);
					break;
				case 'image/gif':
					imagegif($NewCanves,$DestFolder);
					break;			
				case 'image/jpeg':
				case 'image/pjpeg':
					imagejpeg($NewCanves,$DestFolder,$Quality);
					break;
				default:
					return false;
			}
		
			chmod($DestFolder, 0777);
		
			//Destroy image, frees memory	
			if(is_resource($NewCanves)) {imagedestroy($NewCanves);} 
			return true;
		}
	}

	//This function corps image to create exact square images, no matter what its original size!
	private function cropImage($CurWidth,$CurHeight,$iSize,$DestFolder,$SrcImage,$Quality,$ImageType)
	{	 
		//Check Image size is not 0
		if($CurWidth <= 0 || $CurHeight <= 0) 
		{
			return false;
		}
	
		//abeautifulsite.net has excellent article about "Cropping an Image to Make Square bit.ly/1gTwXW9
		if($CurWidth>$CurHeight)
		{
			$y_offset = 0;
			$x_offset = ($CurWidth - $CurHeight) / 2;
			$square_size 	= $CurWidth - ($x_offset * 2);
		}else{
			$x_offset = 0;
			$y_offset = ($CurHeight - $CurWidth) / 2;
			$square_size = $CurHeight - ($y_offset * 2);
		}
	
		$NewCanves 	= imagecreatetruecolor($iSize, $iSize);	
		if(imagecopyresampled($NewCanves, $SrcImage,0, 0, $x_offset, $y_offset, $iSize, $iSize, $square_size, $square_size))
		{
			switch(strtolower($ImageType))
			{
				case 'image/png':
					imagepng($NewCanves,$DestFolder);
					break;
				case 'image/gif':
					imagegif($NewCanves,$DestFolder);
					break;			
				case 'image/jpeg':
				case 'image/pjpeg':
					imagejpeg($NewCanves,$DestFolder,$Quality);
					break;
				default:
					return false;
			}
		
			chmod($DestFolder, 0777);
			//Destroy image, frees memory	
			if(is_resource($NewCanves)) {imagedestroy($NewCanves);} 
			return true;

		}
		  
	}

}