<?php

/**
	Authentication plugin for the PHP Fat-Free Framework

	The contents of this file are subject to the terms of the GNU General
	Public License Version 3.0. You may not use this file except in
	compliance with the license. Any of the license terms and conditions
	can be waived if you get permission from the copyright holder.

	Copyright (c) 2009-2010 F3 Factory
	Bong Cosca <bong.cosca@yahoo.com>

		@package Auth
		@version 1.4.2
**/

//! Plugin for various user authentication methods
class Auth extends Core {

	//! Minimum framework version required to run
	const F3_Minimum='1.4.2';

	//@{
	//! Locale-specific error/exception messages
	const
		TEXT_AuthSetup='Invalid AUTH global variable configuration',
		TEXT_IMAPConnect='Unable to connect to IMAP server {@CONTEXT}',
		TEXT_LDAPConnect='Unable to connect to LDAP server {@CONTEXT}',
		TEXT_LDAPBind='LDAP bind failure';
	//@}

	/**
		Authenticate against SQL database;
			AUTH global array elements:
				db:<SQL-database> (default:'DB'),
				table:<table-name>,
				id:<userID-field>,
				pw:<password-field>
			@return mixed
			@param $id string
			@param $pw string
			@public
	**/
	public static function sqlDB($id,$pw) {
		$auth=self::$global['AUTH'];
		$params='table|id|pw';
		foreach (explode('|',$params) as $param)
			if (!isset($auth[$param])) {
				trigger_error(self::TEXT_AuthSetup);
				return FALSE;
			}
		if (!isset($auth['db']))
			$auth['db']='DB';
		$axon=new Axon($auth['table'],$auth['db']);
		$axon->load('{@AUTH.id}="'.$id.'" AND {@AUTH.pw}="'.$pw.'"');
		return $axon->dry()?FALSE:$axon;
	}

	/**
		Authenticate against NoSQL database (MongoDB);
			AUTH global array elements:
				db:<database-id> (default:'DB'),
				collection:<collection-name>,
				id:<userID-field>,
				pw:<password-field>
			@return mixed
			@param $id string
			@param $pw string
			@public
	**/
	public static function nosqlDB($id,$pw) {
		$auth=self::$global['AUTH'];
		$params='collection|id|pw';
		foreach (explode('|',$params) as $param)
			if (!isset($auth[$param])) {
				trigger_error(self::TEXT_AuthSetup);
				return FALSE;
			}
		if (!isset($auth['db']))
			$auth['db']='DB';
		$m2=new M2($auth['collection'],$auth['db']);
		$m2->load(array('{@AUTH.id}'=>$id,'{@AUTH.pw}'=>$pw));
		return $m2->dry()?FALSE:$m2;
	}

	/**
		Authenticate against IMAP server;
			AUTH global array elements:
				server:<IMAP-server>,
				port:<TCP-port> (default:143)
			@return boolean
			@param $id string
			@param $pw string
			@public
	**/
	public static function IMAP($id,$pw) {
		// IMAP extension required
		if (!extension_loaded('imap')) {
			// Unable to continue
			self::$global['CONTEXT']='imap';
			trigger_error(self::TEXT_PHPExt);
			return;
		}
		$auth=self::$global['AUTH'];
		if (!isset($auth['server'])) {
			trigger_error(self::TEXT_AuthSetup);
			return FALSE;
		}
		if (!isset($auth['port']))
			$auth['port']=143;
		$ic=@fsockopen($auth['server'],$auth['port']);
		if (!is_resource($ic)) {
			// Connection failed
			self::$global['CONTEXT']=$auth['server'];
			trigger_error(self::TEXT_IMAPConnect);
			return FALSE;
		}
		$ibox='{'.$auth['server'].':'.$auth['port'].'}INBOX';
		$mbox=@imap_open($ibox,$id,$pw);
		$ok=is_resource($mbox);
		if (!$ok) {
			$mbox=@imap_open($ibox,$id.'@'.$auth['server'],$pw);
			$ok=is_resource($mbox);
		}
		imap_close($mbox);
		return $ok;
	}

	/**
		Authenticate via LDAP;
			AUTH global array elements:
				dc:<domain-controller>,
				rdn:<connection-DN>,
				pw:<connection-password>
			@return boolean
			@param $id string
			@param $pw string
			@public
	**/
	public static function LDAP($id,$pw) {
		// LDAP extension required
		if (!extension_loaded('ldap')) {
			// Unable to continue
			self::$global['CONTEXT']='ldap';
			trigger_error(self::TEXT_PHPExt);
			return;
		}
		$auth=self::$global['AUTH'];
		if (!isset($auth['dc'])) {
			trigger_error(self::TEXT_AuthSetup);
			return FALSE;
		}
		$dc=@ldap_connect($auth['dc']);
		if (!$dc) {
			// Connection failed
			self::$global['CONTEXT']=$auth['dc'];
			trigger_error(self::TEXT_LDAPConnect);
			return FALSE;
		}
		ldap_set_option($dc,LDAP_OPT_PROTOCOL_VERSION,3);
		ldap_set_option($dc,LDAP_OPT_REFERRALS,0);
		if (!@ldap_bind($dc,$auth['rdn'],$auth['pw'])) {
			// Bind failed
			trigger_error(self::TEXT_LDAPBind);
			return FALSE;
		}
		$result=ldap_search($dc,$auth['base_dn'],'uid='.$id);
		if (ldap_count_entries($dc,$result)!=1)
			// Didn't return a single record
			return FALSE;
		// Bind using credentials
		$info=ldap_get_entries($dc,$result);
		if (!@ldap_bind($dc,$info[0]['dn'],$pw))
			// Bind failed
			return FALSE;
		@ldap_unbind($dc);
		// Verify user ID
		return $info[0]['uid'][0]==$id;
	}

	/**
		Basic HTTP authentication
			@return boolean
			@param $auth mixed
			@param $realm string
			@public
	**/
	public static function basic($auth,$realm=NULL) {
		if (is_null($realm))
			$realm=$_SERVER['REQUEST_URI'];
		if (isset($_SERVER['PHP_AUTH_USER']))
			return call_user_func(
				array('self',$auth),
				$_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']
			);
		header(F3::HTTP_WebAuth.': Basic realm="'.$realm.'"',TRUE,401);
		return FALSE;
	}

}
