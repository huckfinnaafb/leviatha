<?php

/**
	Custom Log for the PHP Fat-Free Framework

	The contents of this file are subject to the terms of the GNU General
	Public License Version 3.0. You may not use this file except in
	compliance with the license. Any of the license terms and conditions
	can be waived if you get permission from the copyright holder.

	Copyright (c) 2009-2010 F3 Factory
	Bong Cosca <bong.cosca@yahoo.com>

		@package Log
		@version 1.4.0
**/

//! Custom log plugin
class Log extends Core {

	//! Minimum framework version required to run
	const F3_Minimum='1.4.0';

	//@{
	//! Locale-specific error/exception messages
	const
		TEXT_LogOpen='Unable to open log file',
		TEXT_LogLock='Unable to gain exclusive access to log file',
		TEXT_Write='{@CONTEXT.0} must have write permission on {@CONTEXT.1}';
	//@}

	//! Seconds before framework gives up trying to lock resource
	const LOG_Timeout=30;

	//! Maximum log file size
	const LOG_Size='2M';

	//@{
	//! Log file properties
	private $filename;
	private $handle;
	//@}

	/**
		Return TRUE if log file is locked before timer expires
			@return boolean
			@private
	**/
	private function ready() {
		$time=microtime(TRUE);
		while (!flock($this->handle,LOCK_EX)) {
			if ((microtime(TRUE)-$time)>self::LOG_Timeout)
				// Give up
				return FALSE;
			usleep(mt_rand(1,3000));
		}
		return TRUE;
	}

	/**
		Write specified text to log file
			@param $text string
			@public
	**/
	public function write($text) {
		if (!self::ready()) {
			// Lock attempt failed
			trigger_error(self::TEXT_LogLock);
			return;
		}
		$path=self::$global['LOGS'];
		clearstatcache();
		if (filesize($path.$this->filename)>F3::bytes(self::LOG_Size)) {
			// Perform log rotation sequence
			if (is_file($path.$this->filename.'.1'))
				copy($path.$this->filename.'.1',$path.$this->filename.'.2');
			copy($path.$this->filename,$path.$this->filename.'.1');
			ftruncate($this->handle,0);
		}
		// Prepend text with timestamp, source IP, file name and
		// line number for tracking origin
		$trace=debug_backtrace(FALSE);
		fwrite(
			$this->handle,
			date('r').' ['.$_SERVER['REMOTE_ADDR'].'] '.
				F3::fixSlashes($trace[0]['file']).':'.
				$trace[0]['line'].' '.
				preg_replace('/\s+/',' ',$text)."\n"
		);
		flock($this->handle,LOCK_UN);
	}

	/**
		Bootstrap code
			@public
	**/
	public static function onLoad() {
		if (!isset(self::$global['LOGS']))
			self::$global['LOGS']=self::$global['BASE'].'logs/';
	}

	/**
		Intercept calls to undefined object methods
			@param $func string
			@param $args array
			@public
	**/
	public function __call($func,array $args) {
		self::$global['CONTEXT']=$func;
		trigger_error(self::TEXT_Method);
	}

	/**
		Logger constructor; requires path/file name as argument (location
		relative to path pointed to by LOGS global variable)
			@public
	**/
	public function __construct() {
		// Reconstruct arguments lost during autoload
		$trace=debug_backtrace(FALSE);
		if (count($trace[0]['args'])) {
			if (!is_dir(self::$global['LOGS'])) {
				if (!is_writable(dirname(self::$global['LOGS'])) &&
					function_exists('posix_getpwuid')) {
						$uid=posix_getpwuid(posix_geteuid());
						self::$global['CONTEXT']=array(
							$uid['name'],
							realpath(dirname(self::$global['LOGS']))
						);
						trigger_error(self::TEXT_Write);
						return;
				}
				// Create log folder
				mkdir(self::$global['LOGS'],0755);
			}
			$this->filename=$trace[0]['args'][0];
			$this->handle=fopen(
				self::$global['LOGS'].$this->filename,'a+'
			);
		}
		if (!is_resource($this->handle)) {
			// Unable to open file
			trigger_error(self::TEXT_LogOpen);
			return;
		}
	}

	/**
		Logger destructor
			@public
	**/
	public function __destruct() {
		if (is_resource($this->handle))
			fclose($this->handle);
	}

}
