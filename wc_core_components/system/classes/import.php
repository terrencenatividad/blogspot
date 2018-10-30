<?php
class import {

	public function check_character_length($field_name, $field_value, $line, $max_length, $input_length){
        $error 	=	"";
        
        if($input_length > $max_length){
            $error 	= 	"$field_name [<strong>$field_value</strong>] on row $line exceeded the Max Character Length of $max_length.<br/>";
        }

        return $error;
    }

    public function check_empty($field_name, $field_value, $line){
        $error 	=	"";
        
        if($field_value  == ""){
            $error 	= 	"$field_name [<strong>$field_value</strong>] on row $line is empty.<br/>";
        }

        return $error;
    }

    public function check_numeric($field_name, $field_value, $line){
        $error 	=	"";
        
        if(!is_numeric($field_value)){
            $error 	= 	"$field_name [<strong>$field_value</strong>] on row $line is not a valid number.<br/>";
        }

        return $error;
    }

    public function check_negative($field_name, $field_value, $line){
        $error 	=	"";
        
        if($field_value < 0){
            $error 	= 	"$field_name [<strong>$field_value</strong>] on row $line has a negative value.<br/>";
        }

        return $error;
    }

    public function check_email($field_name, $field_value, $line){
        $error 	=	"";
        if (!filter_var($field_value, FILTER_VALIDATE_EMAIL)) {
            $error 	= 	"$field_name [<strong>$field_value</strong>] on row $line is not a valid E-mail.<br/>";
        }
        return $error;
    }

    public function check_business_type($field_name, $field_value, $line){
        $business_type 	=	array('Corporation','Individual');
        $error 			=	"";

        if (!in_array($field_value, $business_type)) {
            $error 	= 	"$field_name [<strong>$field_value</strong>] on row $line is not a valid Business Type.<br/>";
        }
        return $error;
    }

    public function check_special_characters($field_name,$field_value,$line){
        $error 	=	"";
        
        $value = str_replace(',', '', $field_value);
        if ( ! preg_match('/^[\\a-zA-Z0-9-_ !@#$%^&*()\/<>?,.{}:;=+\r\n"\']*$/', $value)) {
            $error 	= 	"$field_name [<strong>$field_value</strong>] on row $line contains invalid characters.<br/>";
        }
        return $error;
    }

    public function check_alpha_num($field_name,$field_value,$line){
        $error 	= "";

        $value = str_replace(',','',$field_value);
        if ( ! preg_match('/^[a-zA-Z0-9-_]*$/', $value)) {
            $error 	= 	"$field_name [<strong>$field_value</strong>] on row $line contains invalid characters.<br/>";
        }
        return $error;
    }

    //For Unusual Special Characters
    function trim_special_characters($text){
        return preg_replace('/[[:^print:]]/', '', $text);
    }
}