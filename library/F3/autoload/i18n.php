<?php

/**
	I18n extension for the PHP Fat-Free Framework

	The contents of this file are subject to the terms of the GNU General
	Public License Version 3.0. You may not use this file except in
	compliance with the license. Any of the license terms and conditions
	can be waived if you get permission from the copyright holder.

	Copyright (c) 2009-2010 F3 Factory
	Bong Cosca <bong.cosca@yahoo.com>

		@package I18n
		@version 1.4.0
**/

//! I18n extension
class I18n extends Core {

	//! Minimum framework version required to run
	const F3_Minimum='1.4.0';

	//! Dictionary
	public static $dict=array();

	/**
		Auto-detect default locale
			@return boolean
			@param $lang string
			@public
	**/
	public static function setDefault($lang=NULL) {
		if (!$lang) {
			$header=$_SERVER['HTTP_ACCEPT_LANGUAGE'];
			if (isset(self::$global['LANGUAGE']))
				// Framework variable defined
				$lang=self::$global['LANGUAGE'];
			elseif (isset($header))
				// Found in HTTP header
				$lang=Locale::acceptFromHttp($header);
			else
				// Use default_locale
				$lang=Locale::getDefault();
		}
		// Set default language
		$ok=Locale::setDefault($lang);
		if ($ok) {
			self::$global['LANGUAGE']=$lang;
			self::$dict=array();
		}
		return $ok;
	}

	/**
		Load appropriate language dictionaries
			@public
	**/
	public static function loadDict() {
		// Build up list of languages
		$list=array();
		foreach (func_get_args() as $lang) {
			$list[]=$lang;
			$list[]=Locale::getPrimaryLanguage($lang);
		}
		// Add default language to list
		$list[]=Locale::getDefault();
		$list[]=Locale::getPrimaryLanguage(Locale::getDefault());
		// Use generic English as fallback
		$list[]='en';
		foreach (array_reverse(array_unique($list)) as $dict) {
			$file=self::$global['DICTIONARY'].$dict.'.php';
			if (is_file($file) &&
				!in_array(realpath($file),get_included_files())) {
				$xl8=include $file;
				// Combine all translations
				self::$dict=array_merge(self::$dict,$xl8);
			}
		}
	}

	/**
		Template directive handler
			@param $tree DOMDocument
			@public
	**/
	public static function locale($tree) {
		$node=&$tree->nodeptr;
		$vars=array();
		foreach ($node->attributes as $attr)
			$vars[]=$attr->value;
		$vars=is_array($vars)?array_map('F3::resolve',$vars):array();
		if (!count(self::$dict))
			// Load default dictionary
			self::loadDict();
		$msg=msgfmt_create(
			Locale::getDefault(),self::$dict[$node->nodeValue]
		);
		$block=$msg?XMLdata::encode($msg->format($vars),TRUE):'';
		if (strlen($block)) {
			$tree->fragment->appendXML($block);
			// Insert fragment before current node
			$node->parentNode->
				insertBefore($tree->fragment,$node);
		}
	}

	/**
		Bootstrap for I18n extension
			@return boolean
			@public
	**/
	public static function onLoad() {
		// PHP intl extension required
		if (!extension_loaded('intl')) {
			// Unable to continue
			self::$global['CONTEXT']='intl';
			trigger_error(self::TEXT_PHPExt);
			return;
		}
		self::$global['DICTIONARY']=self::$global['BASE'].'dict/';
		self::setDefault();
	}

}
