<?php

/**
	Input data handler/validation plugin for the PHP Fat-Free Framework

	The contents of this file are subject to the terms of the GNU General
	Public License Version 3.0. You may not use this file except in
	compliance with the license. Any of the license terms and conditions
	can be waived if you get permission from the copyright holder.

	Copyright (c) 2009-2010 F3 Factory
	Bong Cosca <bong.cosca@yahoo.com>

		@package Data
		@version 1.4.2
**/

//! Input data handlers and validators
class Data extends Core {

	//! Minimum framework version required to run
	const F3_Minimum='1.4.2';

	//@{
	//! Locale-specific error/exception messages
	const
		TEXT_Form='The form field hander {@CONTEXT} is invalid';
	//@}

	/**
		Remove HTML tags (except those enumerated) to protect against
		XSS/code injection attacks
			@return mixed
			@param $input string
			@param $tags string
			@public
	**/
	public static function scrub($input,$tags=NULL) {
		if (is_array($input))
			foreach ($input as &$val)
				$val=self::scrub($val,$tags);
		return is_string($input)?
			htmlspecialchars(
				strip_tags($input,is_string($tags)?
					('<'.implode('><',explode('|',$tags)).'>'):$tags),
				ENT_COMPAT,self::$global['ENCODING'],FALSE
			):$input;
	}

	/**
		Call form field handler
			@param $fields string
			@param $funcs mixed
			@param $tags string
			@param $filter integer
			@param $options mixed
			@public
	**/
	public static function input(
		$fields,
		$funcs,
		$tags=NULL,
		$filter=FILTER_UNSAFE_RAW,
		$options=array()) {
			$global=&self::$global;
			foreach (explode('|',$fields) as $field) {
				// Sanitize relevant globals
				$php=$_SERVER['REQUEST_METHOD'].'|REQUEST|FILES';
				foreach (explode('|',$php) as $var)
					if (isset($global[$var][$field]))
						$global[$var][$field]=filter_var(
							self::scrub($global[$var][$field],$tags),
							$filter,$options
						);
				$input=&$global
					[isset($global['FILES'][$field])?'FILES':'REQUEST']
					[$field];
				if (is_string($funcs)) {
					// String passed
					foreach (explode('|',$funcs) as $func) {
						if (!is_callable($func)) {
							// Invalid handler
							$global['CONTEXT']=$include;
							trigger_error(self::TEXT_Form);
						}
						else
							// Call lambda function
							call_user_func($func,$input,$field);
					}
				}
				else {
					// Closure
					if (!is_callable($funcs)) {
						// Invalid handler
						$global['CONTEXT']=$funcs;
						trigger_error(self::TEXT_Form);
					}
					else
						// Call lambda function
						call_user_func($funcs,$input,$field);
				}
			}
	}

	/**
		Return TRUE if string is a valid e-mail address with option to check
		if DNS MX records exist for the domain
			@return boolean
			@param $text string
			@param $mx boolean
			@public
	**/
	public static function validEmail($text,$mx=FALSE) {
		return is_string(filter_var($text,FILTER_VALIDATE_EMAIL)) &&
			(!$mx || getmxrr(substr($text,strrpos($text,'@')+1),$hosts));
	}

	/**
		Return TRUE if string is a valid URL
			@return boolean
			@param $text string
			@public
	**/
	public static function validURL($text) {
		return is_string(filter_var($text,FILTER_VALIDATE_URL));
	}

	/**
		Return TRUE if string and generated CAPTCHA image are identical
			@return boolean
			@param $text string
			@public
	**/
	public static function validCaptcha($text) {
		$result=FALSE;
		if (isset($_SESSION['captcha'])) {
			$result=($text==$_SESSION['captcha']);
			unset($_SESSION['captcha']);
		}
		return $result;
	}

}
