<?php

/**
	PHP Fat-Free Framework - Less Hype, More Meat.

	Fat-Free is a powerful yet lightweight PHP 5.3+ Web development
	framework designed to help build dynamic Web sites - fast! The
	latest version of the software can be downloaded at:-

	http://sourceforge.net/projects/fatfree

	See the accompanying HISTORY.TXT file for information on the changes
	in this release.

	If you use the software for business or commercial gain, permissive
	and closed-source licensing terms are available. For personal use, the
	PHP Fat-Free Framework and other files included in the distribution
	are subject to the terms of the GNU GPL v3. You may not use the
	software, documentation, and samples except in compliance with the
	license.

	Copyright (c) 2009-2010 F3 Factory
	Bong Cosca <bong.cosca@yahoo.com>

		@package Core
		@version 1.4.4
**/

//! Base class
class Core {

	//@{
	//! Framework details
	const
		TEXT_AppName='PHP Fat-Free Framework',
		TEXT_Version='1.4.4';
	//@}

	//@{
	//! Locale-specific error/exception messages
	const
		TEXT_Object='{@CONTEXT} cannot be used in object context',
		TEXT_Class='Undefined class {@CONTEXT}',
		TEXT_Method='Undefined method {@CONTEXT}',
		TEXT_PHPExt='PHP extension {@CONTEXT} is not enabled';
	//@}

	protected static
		//! Fat-Free global variables
		$global,
		//! Profiler statistics
		$stats;

	/**
		Intercept calls to undefined static methods
			@param $func string
			@param $args array
			@public
	**/
	public static function __callStatic($func,array $args) {
		self::$global['CONTEXT']=get_called_class().'::'.$func;
		trigger_error(self::TEXT_Method);
	}

	/**
		Class constructor
			@public
	**/
	public function __construct() {
		// Prohibit use of class as an object
		self::$global['CONTEXT']=get_called_class();
		trigger_error(self::TEXT_Object);
	}

}

//! Framework code
final class F3 extends Core {

	//@{
	//! Locale-specific error/exception messages
	const
		TEXT_NotFound='The requested URL {@CONTEXT} was not found',
		TEXT_Route='The route {@CONTEXT} cannot be resolved',
		TEXT_Handler='The route handler {@CONTEXT} is invalid',
		TEXT_NoRoutes='No routes specified',
		TEXT_HTTP='HTTP status code {@CONTEXT} is invalid',
		TEXT_PCRE1='PCRE internal error',
		TEXT_PCRE2='PCRE backtrack limit error',
		TEXT_PCRE3='PCRE internal error',
		TEXT_PCRE4='PCRE UTF-8 error',
		TEXT_MSet='Invalid multi-variable assignment',
		TEXT_Variable='Framework variable must be specified',
		TEXT_Illegal='{@CONTEXT} is not a valid framework variable name',
		TEXT_Directive='Custom directive {@CONTEXT} is not implemented',
		TEXT_Attrib='Attribute {@CONTEXT} cannot be resolved',
		TEXT_Config='The configuration file {@CONTEXT} was not found',
		TEXT_Section='{@CONTEXT} is not a valid section';
	//@}

	//@{
	//! HTTP status codes (RFC 2616)
	const
		HTTP_100='Continue',
		HTTP_101='Switching Protocols',
		HTTP_200='OK',
		HTTP_201='Created',
		HTTP_202='Accepted',
		HTTP_203='Non-Authorative Information',
		HTTP_204='No Content',
		HTTP_205='Reset Content',
		HTTP_206='Partial Content',
		HTTP_300='Multiple Choices',
		HTTP_301='Moved Permanently',
		HTTP_302='Found',
		HTTP_303='See Other',
		HTTP_304='Not Modified',
		HTTP_305='Use Proxy',
		HTTP_306='Temporary Redirect',
		HTTP_400='Bad Request',
		HTTP_401='Unauthorized',
		HTTP_402='Payment Required',
		HTTP_403='Forbidden',
		HTTP_404='Not Found',
		HTTP_405='Method Not Allowed',
		HTTP_406='Not Acceptable',
		HTTP_407='Proxy Authentication Required',
		HTTP_408='Request Timeout',
		HTTP_409='Conflict',
		HTTP_410='Gone',
		HTTP_411='Length Required',
		HTTP_412='Precondition Failed',
		HTTP_413='Request Entity Too Large',
		HTTP_414='Request-URI Too Long',
		HTTP_415='Unsupported Media Type',
		HTTP_416='Requested Range Not Satisfiable',
		HTTP_417='Expectation Failed',
		HTTP_500='Internal Server Error',
		HTTP_501='Not Implemented',
		HTTP_502='Bad Gateway',
		HTTP_503='Service Unavailable',
		HTTP_504='Gateway Timeout',
		HTTP_505='HTTP Version Not Supported';
	//@}

	//@{
	//! HTTP headers (RFC 2616)
	const
		HTTP_AcceptEnc='Accept-Encoding',
		HTTP_Agent='User-Agent',
		HTTP_Cache='Cache-Control',
		HTTP_Connect='Connection',
		HTTP_Content='Content-Type',
		HTTP_Disposition='Content-Disposition',
		HTTP_Encoding='Content-Encoding',
		HTTP_Expires='Expires',
		HTTP_Host='Host',
		HTTP_IfMod='If-Modified-Since',
		HTTP_Keep='Keep-Alive',
		HTTP_LastMod='Last-Modified',
		HTTP_Length='Content-Length',
		HTTP_Location='Location',
		HTTP_Partial='Accept-Ranges',
		HTTP_Powered='X-Powered-By',
		HTTP_Pragma='Pragma',
		HTTP_Referer='Referer',
		HTTP_Transfer='Content-Transfer-Encoding',
		HTTP_WebAuth='WWW-Authenticate';
	//@}

	const
		//! Framework-mapped PHP globals
		PHP_Globals='GET|POST|COOKIE|REQUEST|SESSION|FILES|SERVER|ENV',
		//! HTTP methods for RESTful interface
		HTTP_Methods='GET|HEAD|POST|PUT|DELETE|OPTIONS',
		//! Default extensions allowed in templates
		FUNCS_Default='standard|date|pcre',
		//! Empty HTML tags
		HTML_Tags='area|base|br|col|frame|hr|img|input|link|meta|param';

	//@{
	//! Framework array variable sort options
	const
		SORT_Asc=1,
		SORT_Desc=-1;
	//@}

	private static
		//! Functions permitted in templates
		$funcs,
		//! NULL reference
		$NULL=NULL;

	/**
		Convert PHP expression to string
			@return string
			@param $val mixed
			@public
	**/
	public static function stringify($val) {
		return is_object($val) && !method_exists($val,'__set_state')?
			('\''.(method_exists($val,'__toString')?
				$val:get_class($val)).'\''):
			(var_export(is_string($val)?self::resolve($val):$val,TRUE));
	}

	/**
		Flatten array values and return as a comma-separated string
			@return string
			@param $args mixed
			@private
	**/
	public static function listArgs($args) {
		if (!is_array($args))
			$args=array($args);
		$str='';
		foreach ($args as $key=>$val) {
			$str.=($str?',':'');
			if (is_string($key))
				$str.=var_export($key,TRUE).'=>';
			$str.=is_array($val)?
				('array('.self::listArgs($val).')'):
					self::stringify($val);
		}
		return htmlspecialchars(
			stripslashes($str),ENT_COMPAT,self::$global['ENCODING'],FALSE
		);
	}

	/**
		Convert Windows double-backslashes to slashes
			@return string
			@param $str string
			@public
	**/
	public static function fixSlashes($str) {
		return $str?strtr($str,'\\','/'):$str;
	}

	/**
		Convert double quotes to equivalent XML entities (&#34;)
			@return string
			@param $val string
			@public
	**/
	public static function fixQuotes($val) {
		return is_array($val)?
			array_map('self::fixQuotes',$val):
			(is_string($val)?
				str_replace('"','&#34;',self::resolve($val)):$val);
	}

	/**
		Fix mangled braces
			@return string
			@param $str string
			@public
	**/
	public static function fixBraces($str) {
		// Fix mangled braces
		return strtr(
			$str,array('%7B'=>'{','%7D'=>'}','%5B'=>'[','%5D'=>']','%20'=>' ')
		);
	}

	/**
		Convert engineering-notated string to bytes
			@return integer
			@param $str string
			@public
	**/
	public static function bytes($str) {
		$greek='KMGT';
		$exp=strpbrk($str,$greek);
		return pow(1024,strpos($greek,$exp)+1)*(int)$str;
	}

	/**
		Normalize array subscripts
			@return string
			@param $str string
			@param $f3var boolean
			@private
	**/
	private static function remix($str,$f3var=TRUE) {
		$out='';
		return array_reduce(
			preg_split(
				'/\[\h*[\'"]?|[\'"]?\h*\]|\./',$str,0,PREG_SPLIT_NO_EMPTY
			),
			function($out,$fix) use($f3var) {
				if ($f3var || $out)
					$fix='[\''.$fix.'\']';
				return $out.$fix;
			}
		);
	}

	/**
		Generate Base36/CRC32 hash code
			@return string
			@param $str string
			@public
	**/
	public static function hashCode($str) {
		return str_pad(
			base_convert(sprintf('%u',crc32($str)),10,36),7,'0',STR_PAD_LEFT
		);
	}

	/**
		Return TRUE if specified string is a valid framework variable name
			@return boolean
			@param $name string
			@private
	**/
	private static function valid($name) {
		if (preg_match('/^\w+(?:\[[^\]]+\]|\.\w+)*$/',$name))
			return TRUE;
		// Invalid variable name
		self::$global['CONTEXT']=var_export($name,TRUE);
		trigger_error(self::TEXT_Illegal);
		return FALSE;
	}

	/**
		Get framework variable reference
			@return mixed
			@param $name string
			@param $set boolean
			@private
	**/
	private static function &ref($name,$set=FALSE) {
		// Referencing a SESSION variable element auto-starts a session
		if (preg_match('/^SESSION\b/',$name) && !strlen(session_id())) {
			session_start();
			// Sync framework and PHP global
			self::$global['SESSION']=&$_SESSION;
		}
		$name=self::remix($name);
		// Traverse array
		$matches=preg_split(
			'/\[\h*[\'"]?|[\'"]?\h*\]/',$name,0,PREG_SPLIT_NO_EMPTY
		);
		if ($set)
			$var=&self::$global;
		else
			$var=self::$global;
		// Grab the specified array element
		foreach ($matches as $match)
			if ($set) {
				if (!is_array($var))
					$var=array();
				$var=&$var[$match];
			}
			elseif (is_array($var) && isset($var[$match]))
				$var=$var[$match];
			else
				return self::$NULL;
		return $var;
	}

	/**
		Return TRUE if framework variable has been assigned a value
			@return boolean
			@param $name string
			@public
	**/
	public static function exists($name) {
		$var=&self::ref($name,TRUE);
		return isset($var);
	}

	/**
		Return value of framework variable
			@return mixed
			@param $name string
			@public
	**/
	public static function get($name) {
		if (preg_match('/{.+}/',$name))
			// Variable variable
			$name=self::resolve($name);
		if (!self::valid($name))
			return NULL;
		$val=self::ref($name);
		if (is_null($val)) {
			// Attempt to retrieve from cache
			$hash='var.'.self::hashCode(self::remix($name));
			$cached=Cache::cached($hash);
			if ($cached)
				return Cache::fetch($hash);
		}
		return $val;
	}

	/**
		Bind value to framework variable
			@param $name string
			@param $val mixed
			@param $persist boolean
			@public
	**/
	public static function set($name,$val,$persist=FALSE) {
		if (preg_match('/{.+}/',$name))
			// Variable variable
			$name=self::resolve($name);
		if (!self::valid($name))
			return;
		if ($persist) {
			$hash='var.'.self::hashCode(self::remix($name));
			Cache::store($hash,$val);
			return;
		}
		// Assign value by reference
		$var=&self::ref($name,TRUE);
		$val=self::fixQuotes($val);
		$var=$val;
		// Initialize cache if explicitly defined
		if ($name=='CACHE' && !is_bool($val))
			Cache::prep();
	}

	/**
		Multi-variable assignment using associative array
			@param $arg string
			@public
	**/
	public static function mset($arg) {
		if (!is_array($arg)) {
			// Invalid argument
			trigger_error(self::TEXT_MSet);
			return;
		}
		// Bind key-value pairs
		array_map('self::set',array_keys($arg),$arg);
	}

	/**
		Unset framework variable
			@param $name string
			@public
	**/
	public static function clear($name) {
		if (preg_match('/{.+}/',$name))
			// Variable variable
			$name=self::resolve($name);
		if (!self::valid($name))
			return;
		// Clearing SESSION array ends the current session
		if ($name=='SESSION' && strlen(session_id()))
			session_destroy();
		// Remove from cache
		$hash='var.'.self::hashCode(self::remix($name));
		$cached=Cache::cached($hash);
		if ($cached) {
			Cache::remove($hash);
			return;
		}
		if (preg_match('/^('.self::PHP_Globals.')\b/',$name))
			eval('unset($_'.self::remix($name,FALSE).');');
		eval('unset(self::$global'.self::remix($name).');');
	}

	/**
		Determine if framework variable has been cached
			@param $name string
			@public
	**/
	public static function cached($name) {
		if (preg_match('/{.+}/',$name))
			// Variable variable
			$name=self::resolve($name);
		return self::valid($name)?
			Cache::cached('var.'.self::hashCode(self::remix($name))):
			FALSE;
	}

	/**
		Configure framework according to .ini file settings;
		Cache auto-generated PHP code to speed up execution
			@param $file string
			@public
	**/
	public static function config($file) {
		// Generate hash code for config file
		$hash='php.'.self::hashCode($file);
		$cached=Cache::cached($hash);
		if ($cached && filemtime($file)<$cached['time'])
			// Retrieve from cache
			$save=Cache::fetch($hash);
		else {
			if (!is_file($file)) {
				// .ini file not found
				self::$global['CONTEXT']=$file;
				trigger_error(self::TEXT_Config);
				return;
			}
			// Map sections to framework methods
			$map=array('global'=>'set','routes'=>'route','maps'=>'map');
			// Read the .ini file
			preg_match_all(
				'/\s*(?:\[(.+?)\]|(?:;.+?)?|(?:([^=]+)=(.+?)))(?:\v|$)/s',
					file_get_contents($file),$matches,PREG_SET_ORDER
			);
			$cfg=array();
			$ptr=&$cfg;
			foreach ($matches as $match) {
				if (isset($match[1]) && !empty($match[1])) {
					// Section header
					if (!isset($map[$match[1]])) {
						// Unknown section
						self::$global['CONTEXT']=$section;
						trigger_error(self::TEXT_Section);
						return;
					}
					$ptr=&$cfg[$match[1]];
				}
				elseif (isset($match[2]) && !empty($match[2])) {
					$csv=array_map(
						function($val) {
							// Typecast if necessary
							return is_numeric($val) ||
								preg_match('/^(TRUE|FALSE)\b/i',$val)?
									eval('return '.$val.';'):$val;
						},
						str_getcsv($match[3])
					);
					// Convert comma-separated values to array
					$match[3]=count($csv)>1?$csv:$csv[0];
					if (preg_match('/([^\[]+)\[([^\]]*)\]/',$match[2],$sub)) {
						if ($sub[2])
							// Associative array
							$ptr[$sub[1]][$sub[2]]=$match[3];
						else
							// Numeric-indexed array
							$ptr[$sub[1]][]=$match[3];
					}
					else
						// Key-value pair
						$ptr[$match[2]]=$match[3];
				}
			}
			ob_start();
			foreach ($cfg as $section=>$pairs)
				if (isset($map[$section]) && is_array($pairs)) {
					$func=$map[$section];
					foreach ($pairs as $key=>$val)
						// Generate PHP snippet
						echo 'F3::'.$func.'('.var_export($key,TRUE).','.
							($func=='set' || !is_array($val)?
								var_export($val,TRUE):self::listArgs($val)).
						');'."\n";
				}
			$save=ob_get_clean();
			// Compress and save to cache
			Cache::store($hash,$save);
		}
		// Execute cached PHP code
		eval($save);
		if (!is_null(self::$global['ERROR']))
			// Remove from cache
			Cache::remove($hash);
	}

	/**
		Send HTTP status header; Return text equivalent of status code
			@return mixed
			@param $code integer
			@public
	**/
	public static function httpStatus($code) {
		if (!defined('self::HTTP_'.$code)) {
			// Invalid status code
			self::$global['CONTEXT']=$code;
			trigger_error(self::TEXT_HTTP);
			return FALSE;
		}
		// Get description
		$response=constant('self::HTTP_'.$code);
		// Send raw HTTP header
		if (PHP_SAPI!='cli')
			header($_SERVER['SERVER_PROTOCOL'].' '.$code.' '.$response);
		return $response;
	}

	/**
		Trigger an HTTP 404 error
			@public
	**/
	public static function http404() {
		// Strip query string
		self::$global['CONTEXT']=parse_url(
			substr($_SERVER['REQUEST_URI'],strlen(self::$global['BASE'])),
			PHP_URL_PATH
		);
		Runtime::error(
			self::resolve(self::TEXT_NotFound),404,debug_backtrace(FALSE)
		);
	}

	/**
		Retrieve HTTP headers
			@return array
			@public
	**/
	public static function httpHeaders() {
		if (PHP_SAPI!='cli') {
			if (function_exists('getallheaders'))
				// Apache server
				return getallheaders();
			// Workaround
			$req=array();
			foreach ($_SERVER as $key=>$val)
				if (substr($key,0,5)=='HTTP_')
					$req[preg_replace_callback(
						'/\w+\b/',
						function($word) {
							return ucfirst(strtolower($word[0]));
						},
						strtr(substr($key,5),'_','-')
					)]=$val;
			return $req;
		}
		return array();
	}

	/**
		Send HTTP header with expiration date (seconds from current time)
			@param $secs integer
			@public
	**/
	public static function httpCache($secs=0) {
		if (PHP_SAPI!='cli') {
			header(self::HTTP_Powered.': '.self::TEXT_AppName);
			if ($secs) {
				header_remove(self::HTTP_Pragma);
				header(self::HTTP_Expires.': '.gmdate('r',time()+$secs));
				header(self::HTTP_Cache.': max-age='.$secs);
				header(self::HTTP_LastMod.': '.gmdate('r',time()));
			}
			else {
				header(self::HTTP_Pragma.': no-cache');
				header(self::HTTP_Cache.': no-cache, must-revalidate');
			}
		}
	}

	/**
		Reroute to specified URI
			@param $uri string
			@public
	**/
	public static function reroute($uri=NULL) {
		session_commit();
		if (PHP_SAPI!='cli') {
			// HTTP redirect
			self::httpStatus($_SERVER['REQUEST_METHOD']!='GET'?303:301);
			header(self::HTTP_Location.': '.
				self::$global['BASE'].self::resolve($uri));
			die;
		}
		self::mock('GET '.self::resolve($uri));
		self::run();
	}

	/**
		Validate route pattern and break it down to an array consisting
		of the request method and request URI
			@return mixed
			@param $pattern string
			@public
	**/
	public static function checkRoute($pattern) {
		preg_match('/(\S+)\s+(\S+)/',$pattern,$parts);
		$parts=array_slice($parts,1);
		$valid=TRUE;
		foreach (explode('|',$parts[0]) as $method)
			if (!preg_match('/('.self::HTTP_Methods.')/',$method)) {
				$valid=FALSE;
				break;
			}
		if ($valid)
			return $parts;
		// Invalid route
		self::$global['CONTEXT']=$pattern;
		trigger_error(self::TEXT_Route);
		return FALSE;
	}

	/**
		Assign handler to route pattern
			@param $pattern string
			@param $funcs mixed
			@param $ttl integer
			@param $allow integer
			@public
	**/
	public static function route($pattern,$funcs,$ttl=0,$allow=TRUE) {
		// Check if valid route pattern
		$route=self::checkRoute($pattern);
		// Valid URI pattern
		if (is_string($funcs)) {
			// String passed
			foreach (explode('|',$funcs) as $func) {
				// Not a lambda function
				if ($func[0]==':') {
					// PHP include file specified
					$file=self::fixSlashes(substr($func,1)).'.php';
					if (!is_file(self::$global['IMPORTS'].$file)) {
						// Invalid route handler
						self::$global['CONTEXT']=$file;
						trigger_error(self::TEXT_Handler);
						return;
					}
				}
				elseif (!is_callable($func)) {
					// Invalid route handler
					self::$global['CONTEXT']=$func;
					trigger_error(self::TEXT_Handler);
					return;
				}
			}
		}
		elseif (!is_callable($funcs)) {
			// Invalid route handler
			self::$global['CONTEXT']=$funcs;
			trigger_error(self::TEXT_Handler);
			return;
		}
		// Assign name to URI variable
		$regex=preg_replace(
			'/{?@(\w+\b)}?/i',
			// Valid URL characters (RFC 1738)
			'(?P<$1>[\w\-\.!~\*\'"(),]+\b)',
			// Wildcard character in URI
			str_replace('\*','(.*)',preg_quote($route[1],'/'))
		);
		// Use pattern and HTTP method as array indices
		// Save handlers and cache timeout
		self::$global['ROUTES']['/^'.$regex.'\/?(?:\?.*)?$/i']
			[$route[0]]=array($funcs,$ttl,$allow);
	}

	/**
		Provide REST interface by mapping URL to object/PHP class
			@param $url string
			@param $obj mixed
			@public
	**/
	public static function map($url,$obj) {
		foreach (explode('|',self::HTTP_Methods) as $method) {
			if (method_exists($obj,$method))
				self::route(
					strtoupper($method).' '.$url,array($obj,$method)
				);
		}
	}

	/**
		Process routes based on incoming URI
			@public
	**/
	public static function run() {
		// Validate user against spam blacklists
		if (isset(self::$global['DNSBL']) &&
			Network::spam(Network::realIP())) {
			if (isset(self::$global['SPAM']))
				// Spammer detected; Send to blackhole
				self::reroute(self::$global['SPAM']);
			else
				// HTTP 404 message
				self::http404();
		}
		$routes=&self::$global['ROUTES'];
		// Process routes
		if (!isset($routes)) {
			trigger_error(self::TEXT_NoRoutes);
			return;
		}
		$found=FALSE;
		// Detailed routes get matched first
		krsort($routes);
		// Save the current time
		$time=time();
		foreach ($routes as $regex=>$route) {
			if (!preg_match($regex,
				substr($_SERVER['REQUEST_URI'],strlen(self::$global['BASE'])),
				$args))
				continue;
			$found=TRUE;
			// Inspect each defined route
			foreach ($route as $method=>$proc) {
				if (!preg_match('/'.$method.'/',$_SERVER['REQUEST_METHOD']))
					continue;
				if (!$proc[2] && isset(self::$global['HOTLINK']) &&
					isset($_SERVER['HTTP_REFERER']) &&
					parse_url($_SERVER['HTTP_REFERER'],PHP_URL_HOST)!=
						$_SERVER['SERVER_NAME'])
					// Hot link detected; Reroute
					self::reroute(self::$global['HOTLINK']);
				if (is_array($proc[0]))
					for ($i=0;$i<2;$i++)
						if (is_string($proc[0][$i]))
							$proc[0][$i]=F3::resolve($proc[0][$i]);
				elseif (is_string($proc[0]))
					$proc[0]=F3::resolve($proc[0]);
				// Save named regex captures
				foreach ($args as $key=>$arg)
					// Remove non-zero indexed elements
					if (is_numeric($key) && $key)
						unset($args[$key]);
				self::$global['PARAMS']=$args;
				// Default: Do not cache
				self::httpCache(0);
				if ($_SERVER['REQUEST_METHOD']=='GET' && $proc[1]) {
					$_SERVER['REQUEST_TTL']=$proc[1];
					// Get HTTP request headers
					$req=self::httpHeaders();
					// Content divider
					$div=chr(0);
					// Get hash code for this Web page
					$hash='url.'.self::hashCode(
						$_SERVER['REQUEST_METHOD'].' '.
						$_SERVER['REQUEST_URI']
					);
					$cached=Cache::cached($hash);
					$regex='/^'.self::HTTP_Content.':.+/';
					$time=time();
					if ($cached && $time-$cached['time']<$proc[1]) {
						if (!isset($req[self::HTTP_IfMod]) ||
							$cached['time']>
								strtotime($req[self::HTTP_IfMod])) {
							// Activate cache timer
							self::httpCache(
								$cached['time']+$proc[1]-$time
							);
							// Retrieve from cache
							$buffer=Cache::fetch($hash);
							$type=strstr($buffer,$div,TRUE);
							if (PHP_SAPI!='cli' &&
								preg_match($regex,$type,$match))
								// Cached MIME type
								header($match[0]);
							// Save response
							self::$global['RESPONSE']=substr(
								strstr($buffer,$div),1
							);
						}
						else {
							// Client-side cache is still fresh
							self::httpStatus(304);
							die;
						}
					}
					else {
						// Cache this page
						ob_start();
						Runtime::call($proc[0]);
						self::$global['RESPONSE']=ob_get_clean();
						if (!self::$global['ERROR'] &&
							self::$global['RESPONSE']) {
							// Activate cache timer
							self::httpCache($proc[1]);
							$type='';
							foreach (headers_list() as $hdr)
								if (preg_match($regex,$hdr)) {
									// Add Content-Type header to buffer
									$type=$hdr;
									break;
								}
							// Compress and save to cache
							Cache::store(
								$hash,$type.$div.self::$global['RESPONSE']
							);
						}
					}
				}
				else {
					// Capture output
					ob_start();
					if ($_SERVER['REQUEST_METHOD']=='PUT') {
						// Associate PUT with file handle of stdin stream
						self::$global['PUT']=fopen('php://input','rb');
						Runtime::call($proc[0]);
						fclose(self::$global['PUT']);
					}
					else
						Runtime::call($proc[0]);
					self::$global['RESPONSE']=ob_get_clean();
				}
				$elapsed=time()-$time;
				if (self::$global['THROTTLE']/1e3>$elapsed)
					// Delay output
					usleep(1e6*(self::$global['THROTTLE']/1e3-$elapsed));
				if (self::$global['RESPONSE'] && !self::$global['QUIET'])
					// Display response
					echo self::$global['RESPONSE'];
				// Hail the conquering hero
				return;
			}
		}
		// No such Web page
		self::http404();
	}

	/**
		Evaluate template expressions in string
			@return mixed
			@param $str string
			@public
	**/
	public static function resolve($str) {
		// Analyze string for correct framework expression syntax
		$str=preg_replace_callback(
			// Expression
			'/{('.
				// Capture group
				'(?:'.
					// Look-ahead group
					'(?:'.
						// Variable token
						'@\w+(?:\[[^\]]+\]|\.\w+)*|'.
						// String
						'\'[^\']*\'|"[^"]*"|'.
						// Number
						'(?:\d+\.)?\d*(?:e[+\-]?\d+)?|'.
						// Null and boolean constants
						'NULL|TRUE|FALSE|'.
						// Function
						'\w+\h*(?=\(.*\))'.
					// End of look-ahead
					')(?!\h*[@\w\'"])|'.
					// Whitespace and operators
					'[\h\.\-\/()+*,%!?=<>|&:]'.
				// End of captured string
				')+'.
			// End of expression
			')}/i',
			function($expr) {
				// Evaluate expression
				$out=preg_replace_callback(
					// Framework variable
					'/(?<=@)\w+(?:\[[^\]]+\]|\.\w+)*/',
					function($var) {
						// Retrieve variable contents
						return F3::stringify(F3::get($var[0]));
					},
					preg_replace_callback(
						// Function
						'/\b(\w+)\h*\(([^\)]*)\)/',
						function($val) {
							// Transform empty array to NULL
							return ($val[1].trim($val[2]))=='array'?
								'NULL':
								// check if prohibited function
								(F3::allowed($val[1])?
									$val[0]:('\''.$val[0].'\''));
						},
						$expr[1]
					)
				);
				// Check for syntax errors
				return (string)(@eval('return TRUE; '.$out.';')?
					eval('return '.$out.';'):$out);
			},
			// Strip out comments in template
			preg_replace('/{\*.+?\*}/s','',self::fixBraces((string)$str))
		);
		$error=preg_last_error();
		if ($error!=PREG_NO_ERROR) {
			// Display PCRE-specific error message
			trigger_error(constant('self::TEXT_PCRE'.$error));
			return FALSE;
		}
		// Remove control characters except whitespaces
		return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/','',$str);
	}

	/**
		Process <F3:include> directives
			@return string
			@param $file string
			@param $path string
			@public
	**/
	public static function embed($file,$path) {
		if (!$file || !is_file($path.$file))
			return '';
		$file=$path.$file;
		$hash='tpl.'.self::hashCode($file);
		$cached=Cache::cached($hash);
		if (!isset(self::$stats['TEMPLATES']))
			self::$stats['TEMPLATES']=array(
				'cache'=>array(),
				'loaded'=>array()
			);
		if ($cached && filemtime($file)<$cached['time']) {
			$text=Cache::fetch($hash);
			// Gather template file info for profiler
			self::$stats['TEMPLATES']['cache'][$file]=$cached['size'];
		}
		else {
			$text=file_get_contents($file);
			Cache::store($hash,$text);
			// Gather template file info for profiler
			self::$stats['TEMPLATES']['loaded'][$file]=filesize($file);
		}
		// Search/replace <F3:include> regex pattern
		$regex='/<(?:F3:)?include\h*href\h*=\h*"([^"]+)"\h*\/>/i';
		return preg_match($regex,$text)?
			// Call recursively if included file also has <F3:include>
			preg_replace_callback(
				$regex,
				function($attr) use($path) {
					// Load file
					return F3::embed(F3::resolve($attr[1]),$path);
				},
				$text
			):
			$text;
	}

	/**
		Parse all directives and render HTML/XML template
			@return mixed
			@param $file string
			@param $mime string
			@param $path string
			@public
	**/
	public static function serve($file,$mime='text/html',$path=NULL) {
		if (is_null($path))
			$path=self::fixSlashes(self::$global['GUI']);
		// Remove <F3::exclude> blocks
		$text=preg_replace(
			'/<(?:F3:)?exclude>.*?<\/(?:F3:)?exclude>/is','',
			// Link <F3:include> files
			self::embed($file,$path)
		);
		if (PHP_SAPI!='cli')
			// Send HTTP header with appropriate character set
			header(self::HTTP_Content.': '.$mime.'; '.
				'charset='.self::$global['ENCODING']);
		if (!preg_match('/<.+>/s',$text))
			// Plain text
			return self::resolve($text);
		// Initialize XML tree
		$tree=new XMLtree('1.0',self::$global['ENCODING']);
		// Suppress errors caused by invalid HTML structures
		$ishtml=($mime!='text/xml');
		libxml_use_internal_errors($ishtml);
		// Populate XML tree
		if ($ishtml) {
			// HTML template; Keep track of existing tags so those
			// added by libxml can be removed later
			$tags=array(
				'/<!DOCTYPE\s+html[^>]*>\h*\v*/is',
				'/<[\/]?html[^>]*>\h*\v*/is',
				'/<[\/]?head[^>]*>\h*\v*/is',
				'/<[\/]?body[^>]*>\h*\v*/is'
			);
			$undef=array();
			foreach ($tags as $regex)
				if (!preg_match($regex,$text))
					$undef[]=$regex;
			$tree->loadHTML($text);
		}
		else
			// XML template
			$tree->loadXML($text,LIBXML_COMPACT|LIBXML_NOERROR);
		// Prepare for XML tree traversal
		$tree->fragment=$tree->createDocumentFragment();
		$pass2=FALSE;
		$tree->traverse(
			function() use($tree,&$pass2) {
				$node=&$tree->nodeptr;
				$tag=$node->tagName;
				$next=$node;
				$parent=$node->parentNode;
				// Node removal flag
				$remove=FALSE;
				if ($tag=='repeat') {
					// Process <F3:repeat> directive
					$inner=$tree->innerHTML($node);
					if ($inner) {
						// Analyze attributes
						foreach ($node->attributes as $attr) {
							preg_match(
								'/{?(@\w+(\[[^\]]+\]|\.\w+)*)}?/',
									$attr->value,$cap);
							$name=$attr->name;
							if (!$cap[1] ||
								isset($cap[2]) && !empty($cap[2]) &&
								$name!='group') {
								// Invalid attribute
								F3::set('CONTEXT',$attr->value);
								trigger_error(Core::TEXT_Attrib);
								return;
							}
							elseif ($name=='key')
								$kvar='/'.$cap[1].'\b/';
							elseif ($name=='index' || $name=='value')
								$ivar='/'.$cap[1].'\b/';
							elseif ($name=='group') {
								$gcap=$cap[1];
								$gvar=F3::get(substr($cap[1],1));
							}
						}
						if (is_array($gvar) && count($gvar)) {
							ob_start();
							// Iterate thru group elements
							foreach (array_keys($gvar) as $key)
								echo preg_replace($ivar,
									// Replace index token
									$gcap.'[\''.$key.'\']',
									isset($kvar)?
										// Replace key token
										preg_replace($kvar,
											'\''.$key.'\'',$inner):
										$inner
								);
							$block=ob_get_clean();
							if (strlen($block)) {
								$tree->fragment->appendXML($block);
								// Insert fragment before current node
								$next=$parent->
									insertBefore($tree->fragment,$node);
							}
						}
					}
					$remove=TRUE;
				}
				elseif ($tag=='check' && !$pass2)
					// Found <F3:check> directive
					$pass2=TRUE;
				elseif (strpos($tag,'-')) {
					// Process custom template directive
					list($class,$method)=explode('-',$tag);
					// Invoke template directive handler
					call_user_func(array($class,$method),$tree);
					$remove=TRUE;
				}
				if ($remove) {
					// Find next node
					if ($node->isSameNode($next))
						$next=$node->nextSibling?
							$node->nextSibling:$parent;
					// Remove current node
					$parent->removeChild($node);
					// Replace with next node
					$node=$next;
				}
			}
		);
		if ($pass2) {
			// Template contains <F3:check> directive
			$tree->traverse(
				function() use($tree) {
					$node=&$tree->nodeptr;
					$parent=$node->parentNode;
					$tag=$node->tagName;
					// Process <F3:check> directive
					if ($tag=='check') {
						$cond=var_export(
							(boolean)F3::resolve(
								rawurldecode($node->getAttribute('if'))
							),TRUE
						);
						ob_start();
						foreach ($node->childNodes as $child)
							if ($child->nodeType==XML_ELEMENT_NODE &&
								preg_match('/'.$cond.'/i',$child->tagName))
									echo $tree->innerHTML($child)?:'';
						$block=ob_get_clean();
						if (strlen($block)) {
							$tree->fragment->appendXML($block);
							$parent->insertBefore($tree->fragment,$node);
						}
						// Remove current node
						$parent->removeChild($node);
						// Re-process parent node
						$node=$parent;
					}
				}
			);
		}
		if ($ishtml) {
			// Fix empty HTML tags
			$text=preg_replace(
				'/<((?:'.self::HTML_Tags.')\b.*?)\/?>/is','<$1/>',
				self::resolve(self::fixBraces($tree->saveHTML()))
			);
			// Remove tags inserted by libxml
			$text=preg_replace($undef,'',$text);
		}
		else
			$text=XMLdata::encode(
				self::resolve(self::fixBraces($tree->saveXML())),TRUE
			);
		return $text;
	}

	/**
		Allow PHP and user-defined functions to be used in templates
			@param $str string
			@public
	**/
	public static function allow($str='') {
		// Create lookup table of functions allowed in templates
		$legal=array();
		// Get list of all defined functions
		$dfuncs=get_defined_functions();
		foreach (explode('|',$str) as $ext) {
			$funcs=array();
			if (extension_loaded($ext))
				$funcs=get_extension_funcs($ext);
			elseif ($ext=='user')
				$funcs=$dfuncs['user'];
			$legal=array_merge($legal,$funcs);
		}
		// Remove prohibited functions
		$illegal='/^('.
			'apache_|call|chdir|env|escape|exec|extract|fclose|fflush|'.
			'fget|file_put|flock|fopen|fprint|fput|fread|fseek|fscanf|'.
			'fseek|fsockopen|fstat|ftell|ftp_|ftrunc|get|header|http_|'.
			'import|ini_|ldap_|link|log_|magic|mail|mcrypt_|mkdir|ob_|'.
			'php|popen|posix_|proc|rename|rmdir|rpc|set_|sleep|stream|'.
			'sys|thru|unreg'.
		')/i';
		$legal=array_merge(
			array_filter(
				$legal,
				function($func) use($illegal) {
					return !preg_match($illegal,$func);
				}
			),
			// PHP language constructs that may be used in expressions
			array('array','isset')
		);
		self::$funcs=array_map('strtolower',$legal);
	}

	/**
		Return TRUE if function can be used in templates
			@return boolean
			@param $func string
			@public
	**/
	public static function allowed($func) {
		if (!isset(self::$funcs))
			self::allow(self::FUNCS_Default);
		return in_array($func,self::$funcs);
	}

	/**
		Proxy method for sandboxing function/script
			@param $funcs mixed
			@public
	**/
	public static function call($funcs) {
		Runtime::call($funcs);
	}

	/**
		Return array of runtime performance analysis data
			@return array
			@public
	**/
	public static function &profile() {
		$stats=&self::$stats;
		// Compute elapsed time
		if (!isset($stats['TIME']))
			$stats['TIME']=array();
		$stats['TIME']['start']=&self::$global['TIME'];
		$stats['TIME']['elapsed']=time()-self::$global['TIME'];
		// Reset PHP's stat cache
		foreach (get_included_files() as $file) {
			// Gather includes
			if (!isset($stats['FILES']))
				$stats['FILES']=array('includes'=>array());
			$stats['FILES']['includes']
				[self::fixSlashes($file)]=filesize($file);
		}
		// Compute memory consumption
		if (!isset($stats['MEMORY']))
			$stats['MEMORY']=array();
		$stats['MEMORY']['current']=memory_get_usage();
		$stats['MEMORY']['peak']=memory_get_peak_usage();
		return $stats;
	}

	/**
		Mock environment for command-line use and/or unit testing
			@param $pattern string
			@param $params array
			@public
	**/
	public static function mock($pattern,array $params=NULL) {
		// Override PHP globals
		list($method,$uri)=F3::checkRoute($pattern);
		$method=strtoupper($method);
		$query=explode('&',parse_url($uri,PHP_URL_QUERY));
		foreach ($query as $pair)
			if (strpos($pair,'=')) {
				list($var,$val)=explode('=',$pair);
				F3::set($method.'.'.$var,$val);
				F3::set('REQUEST.'.$var,$val);
			}
		if (is_array($params))
			foreach ($params as $var=>$val) {
				F3::set($method.'.'.$var,$val);
				F3::set('REQUEST.'.$var,$val);
			}
		F3::set('SERVER.REQUEST_METHOD',$method);
		F3::set('SERVER.REQUEST_URI',$uri);
	}

	/**
		Proxy method for calling methods in non-F3 classes
			@public
			@return mixed
			@param $func string
			@param $args array
			@public
	**/
	public static function __callStatic($func,array $args) {
		return Runtime::proxy($func,$args);
	}

}

//! Run-time services
final class Runtime extends Core {

	//@{
	//! Locale-specific error/exception messages
	const
		TEXT_Instance='The framework cannot be started more than once',
		TEXT_Minimum='Plug-in is not compatible with {@CONTEXT}';
	//@}

	private static
		//! Autoload class registry
		$loaded=array();

	/**
		Display default error page; Use custom page if found
			@param $str string
			@param $code integer
			@param $stack array
			@public
	**/
	public static function error($str,$code,$stack) {
		$prior=self::$global['ERROR'];
		// Remove framework methods and extraneous data
		$stack=array_merge(
			array_filter(
				$stack,
				function($nexus) {
					return TRUE;
					/*
					return isset($nexus['line']) &&
						((F3::get('DEBUG') || $nexus['file']!=__FILE__) &&
							!preg_match(
								'/^(call_user_func|include|trigger_error|'.
									'{.+?})/',$nexus['function']
							) &&
							(!isset($nexus['class']) ||
								$nexus['class']!='Runtime')
						);
					*/
				}
			)
		);
		// Generate internal server error if code is zero
		if (!$code)
			$code=500;
		$trace='';
		if (!self::$global['RELEASE']) {
			// Stringify the stack trace
			ob_start();
			foreach ($stack as $level=>$nexus)
				echo '#'.$level.' '.
					(isset($nexus['line'])?
						(F3::fixSlashes($nexus['file']).':'.
							$nexus['line'].' '):'').
					(isset($nexus['function'])?
						((isset($nexus['class'])?$nexus['class']:'').
							(isset($nexus['type'])?$nexus['type']:'').
								$nexus['function'].
						(!preg_match('/{.+}/',$nexus['function']) &&
							isset($nexus['args'])?
							('('.F3::listArgs($nexus['args']).')'):'')):'').
						"\n";
			$trace=ob_get_clean();
		}
		if (PHP_SAPI!='cli')
			// Remove all pending headers
			header_remove();
		// Save error details
		$error=&self::$global['ERROR'];
		$error=array(
			'code'=>$code,
			'title'=>F3::httpStatus($code),
			'text'=>preg_replace('/\v/','',F3::resolve($str)),
			'trace'=>$trace
		);
		unset(self::$global['CONTEXT']);
		if  (self::$global['QUIET'])
			return;
		// Write to server's error log (with complete stack trace)
		error_log($error['text']);
		foreach (explode("\n",$trace) as $str)
			if ($str)
				error_log($str);
		if ($prior)
			return;
		foreach (explode('|','title|text|trace') as $sub)
			// Convert to HTML entities for safety
			$error[$sub]=htmlspecialchars(
				rawurldecode($error[$sub]),
				ENT_COMPAT,self::$global['ENCODING']
			);
		$error['trace']=nl2br($error['trace']);
		// Find template referenced by the global variable E<code>
		if (isset(self::$global['E'.$error['code']])) {
			$file=F3::fixSlashes(self::$global['E'.$error['code']]);
			if (!is_null($file) && is_file(self::$global['GUI'].$file)) {
				// Render custom template stored in E<code>
				echo F3::serve($file);
				return;
			}
		}
		// Use default HTML response page
		echo F3::resolve(
			'<html>'.
				'<head>'.
					'<title>{@ERROR.code} {@ERROR.title}</title>'.
				'</head>'.
				'<body>'.
					'<h1>{@ERROR.title}</h1>'.
					'<p><i>{@ERROR.text}</i></p>'.
					'<p>{@ERROR.trace}</p>'.
				'</body>'.
			'</html>'
		);
	}

	/**
		Provide sandbox for functions and import files to prevent direct
		access to framework internals and other scripts
			@param $funcs mixed
			@public
	**/
	public static function call($funcs) {
		if (is_string($funcs)) {
			// Call each code segment
			foreach (explode('|',$funcs) as $func) {
				if ($func[0]==':')
					// Run external PHP script
					include self::$global['IMPORTS'].substr($func,1).'.php';
				else
					// Call lambda function
					call_user_func($func);
			}
		}
		else
			// Call lambda function
			call_user_func($funcs);

	}

	/**
		Intercept calls to static methods of non-F3 classes and proxy for
		the called class if found in the autoload folder
			@return mixed
			@param $func string
			@param $args array
			@public
	**/
	public static function proxy($func,array $args) {
		foreach (explode('|',self::$global['AUTOLOAD']) as $auto) {
			foreach (glob(F3::fixSlashes(realpath($auto)).
				'/*.php',GLOB_NOSORT) as $file) {
				$class=strstr(basename($file),'.php',TRUE);
				// Prevent recursive calls
				$found=FALSE;
				foreach (debug_backtrace(FALSE) as $trace)
					if (isset($trace['class']) &&
						preg_match('/'.$trace['class'].'/i',
						strtolower($class)) &&
						preg_match('/'.$trace['function'].'/i',
						strtolower($func))) {
						$found=TRUE;
						break;
					}
				if ($found)
					continue;
				$hash='reg.'.F3::hashCode(strtolower($class));
				$cached=Cache::cached($hash);
				$methods=array();
				if ((!$cached || $cached['time']<filemtime($file))) {
					if (!in_array(
						$file,
						array_map('F3::fixSlashes',get_included_files())))
							include $file;
					if (class_exists($class,FALSE)) {
						// Check version
						if (defined($class.'::F3_Minimum') &&
							self::TEXT_Version<$class::F3_Minimum) {
							self::$global['CONTEXT']=self::TEXT_AppName.' '.
								$class::F3_Minimum;
							trigger_error(self::TEXT_Minimum);
							return FALSE;
						}
						// Update cache
						$methods=array_map(
							'strtolower',get_class_methods($class)
						);
						Cache::store($hash,$methods);
					}
				}
				else
					// Retrieve from cache
					$methods=Cache::fetch($hash);
				if (in_array(strtolower($func),$methods)) {
					// Execute onLoad method if defined
					if (in_array('onload',$methods) && (!self::$loaded ||
						!in_array(strtolower($class),self::$loaded))) {
						call_user_func(array($class,'onload'));
						self::$loaded[]=strtolower($class);
					}
					// Proxy for method in autoload class
					return call_user_func_array(
						array($class,$func),$args
					);
				}
			}
		}
		// Trigger error if no other registered autoload functions
		if (count(spl_autoload_functions())==1) {
			self::$global['CONTEXT']=$func;
			trigger_error(self::TEXT_Method);
		}
		return FALSE;
	}

	/**
		Intercept instantiation of objects in undefined classes
			@param $class string
			@public
	**/
	public static function autoLoad($class) {
		foreach (explode('|',self::$global['AUTOLOAD']) as $auto) {
			// Allow namespaced classes
			$file=F3::fixSlashes(realpath($auto).'/'.$class).'.php';
			// Case-insensitive check for file presence
			$glob=glob(dirname($file).'/*.php',GLOB_NOSORT);
			$fkey=array_search(
				strtolower($file),array_map('strtolower',$glob)
			);
			if (is_int($fkey) &&
				!in_array($glob[$fkey],
					array_map('F3::fixSlashes',get_included_files()))) {
				include $glob[$fkey];
				// Verify that the class was loaded
				if (class_exists($class,FALSE)) {
					// Check version
					if (defined($class.'::F3_Minimum') &&
						self::TEXT_Version<$class::F3_Minimum) {
						self::$global['CONTEXT']=self::TEXT_AppName.' '.
							$class::F3_Minimum;
						trigger_error(self::TEXT_Minimum);
						return;
					}
					$hash='reg.'.F3::hashCode(strtolower($class));
					$cached=Cache::cached($hash);
					if (!$cached ||
						$cached['time']<filemtime($glob[$fkey])) {
							// Update cache
							$methods=array_map(
								'strtolower',get_class_methods($class)
							);
							Cache::store($hash,$methods);
					}
					else
						// Retrieve from cache
						$methods=Cache::fetch($hash);
					foreach (debug_backtrace(FALSE) as $trace)
						if (isset($trace['class']) &&
							$trace['class']==__CLASS__ &&
							$trace['function']=='route')
							// Nothing to execute
							return;
					// Execute onLoad method if defined
					if (in_array('onload',$methods) && 
						(!self::$loaded ||
						!in_array(strtolower($class),self::$loaded))) {
						call_user_func(array($class,'onload'));
						self::$loaded[]=strtolower($class);
					}
					return;
				}
			}
		}
		// Trigger error if no other registered autoload functions
		if (count(spl_autoload_functions())==1) {
			self::$global['CONTEXT']=$class;
			trigger_error(self::TEXT_Class);
		}
	}

	/**
		Execute shutdown function
			@public
	**/
	public static function stop() {
		if (isset(self::$global['UNLOAD'])) {
			ob_end_flush();
			if (PHP_SAPI!='cli')
				header(self::HTTP_Connect.': close');
			call_user_func(self::$global['UNLOAD']);
		}
	}

	/**
		Kickstart the framework
			@public
	**/
	public static function start() {
		// Get PHP settings
		$ini=ini_get_all(NULL,FALSE);
		$level=E_ALL;
		$exts=get_loaded_extensions();
		// Intercept errors and send output to browser
		set_error_handler(
			function($errno,$errstr) use($level) {
				if (error_reporting()) {
					ini_set('error_reporting',$level);
					// Bypass if error suppression (@) is enabled
					Runtime::error($errstr,500,debug_backtrace(FALSE));
				}
			},
			$level
		);
		// Do the same for PHP exceptions
		set_exception_handler(
			function($xcpt) {
				if (!count($xcpt->getTrace())) {
					// Translate exception trace
					list($trace)=debug_backtrace(FALSE);
					$arg=$trace['args'][0];
					$trace=array(
						array(
							'file'=>$arg->getFile(),
							'line'=>$arg->getLine(),
							'function'=>'{main}',
							'args'=>array()
						)
					);
				}
				else
					$trace=$xcpt->getTrace();
				Runtime::error(
					$xcpt->getMessage(),$xcpt->getCode(),$trace
				);
				// PHP aborts at this point
				return;
			}
		);
		if (isset(self::$global)) {
			// Multiple framework instances not allowed
			trigger_error(self::TEXT_Instance);
			return;
		}
		ini_set('display_errors',0);
		// Fix Apache's VirtualDocumentRoot limitation
		$_SERVER['DOCUMENT_ROOT']=str_replace(
			$_SERVER['SCRIPT_NAME'],'',$_SERVER['SCRIPT_FILENAME']); 
		// Hydrate framework variables
		$root=F3::fixSlashes(realpath('.')).'/';
		self::$global=array(
			'AUTOLOAD'=>$root.'autoload/',
			'BASE'=>preg_replace('/(.*)\/.+/','$1',$_SERVER['SCRIPT_NAME']),
			'CACHE'=>FALSE,
			'DEBUG'=>FALSE,
			'ENCODING'=>'UTF-8',
			'ERROR'=>NULL,
			'GUI'=>$root,
			'IMPORTS'=>$root,
			'MAXSIZE'=>F3::bytes($ini['post_max_size']),
			'QUIET'=>FALSE,
			'RELEASE'=>FALSE,
			'ROOT'=>$root,
			'SITEMAP'=>array(),
			'TIME'=>time(),
			'THROTTLE'=>0,
			'VERSION'=>self::TEXT_AppName.' '.self::TEXT_Version
		);
		// Create convenience containers for PHP globals
		foreach (explode('|',F3::PHP_Globals) as $var) {
			// Sync framework and PHP globals
			self::$global[$var]=&$GLOBALS['_'.$var];
			if ($ini['magic_quotes_gpc'] && preg_match('/^[GPCR]/',$var))
				// Corrective action on PHP magic quotes
				array_walk_recursive(
					self::$global[$var],
					function(&$val) {
						$val=stripslashes($val);
					}
				);
		}
		if (PHP_SAPI=='cli') {
			// Command line: Parse GET variables in URL, if any
			preg_match_all(
				'/[\?&]([^=]+)=([^&$]*)/',$_SERVER['argv'][1],
				$matches,PREG_SET_ORDER
			);
			foreach ($matches as $match) {
				$_REQUEST[$match[1]]=$match[2];
				$_GET[$match[1]]=$match[2];
			}
			// Detect host name from environment
			$_SERVER['SERVER_NAME']=gethostname();
			// Convert URI to human-readable string
			F3::mock('GET '.$_SERVER['argv'][1]);
		}
		// Initialize profiler
		self::$stats=array('MEMORY'=>array('start'=>memory_get_usage()));
		// Initialize autoload stack and shutdown sequence
		spl_autoload_register('Runtime::autoLoad');
		register_shutdown_function('Runtime::stop');
	}

}

//! Framework cache engine
class Cache extends Core {

	//@{
	//! Locale-specific error/exception messages
	const
		TEXT_Backend='Cache back-end is invalid',
		TEXT_Store='Unable to save {@CONTEXT} to cache',
		TEXT_Fetch='Unable to retrieve {@CONTEXT} from cache',
		TEXT_Clear='Unable to clear {@CONTEXT} from cache',
		TEXT_Write='{@CONTEXT.0} must have write permission on {@CONTEXT.1}';
	//@}

	private static
		//! Level-1 cached object
		$l1cache,
		//! Cache back-end
		$backend;

	/**
		Auto-detect extensions usable as cache back-ends; MemCache must be
		explicitly activated to work properly; Fall back to file system if
		none declared or detected
			@private
	**/
	private static function detect() {
		$exts=array_intersect(
			explode('|','apc|xcache'),
			array_map('strtolower',get_loaded_extensions())
		);
		$ref=array_merge($exts,array());
		self::$global['CACHE']=array_shift($ref)?:
			('folder='.self::$global['ROOT'].'cache/');
	}

	/**
		Initialize framework level-2 cache
			@return boolean
			@public
	**/
	public static function prep() {
		if (!self::$global['CACHE'])
			return TRUE;
		if (preg_match(
			'/^(apc)|(memcache)=(.+)|(xcache)|(folder)\=(.+\/)/i',
			self::$global['CACHE'],$match)) {
			if (isset($match[5]) && $match[5]) {
				if (!is_dir($match[6])) {
					if (!is_writable(dirname($match[6])) &&
						function_exists('posix_getpwuid')) {
							$uid=posix_getpwuid(posix_geteuid());
							self::$global['CONTEXT']=array(
								$uid['name'],realpath(dirname($match[6]))
							);
							trigger_error(self::TEXT_Write);
							return FALSE;
					}
					// Create the framework's cache folder
					umask(0);
					mkdir($match[6],0755);
				}
				// File system
				self::$backend=array('type'=>'folder','id'=>$match[6]);
			}
			else {
				$ext=strtolower($match[1]?:($match[2]?:$match[4]));
				if (!extension_loaded($ext)) {
					self::$global['CONTEXT']=$ext;
					trigger_error(self::TEXT_PHPExt);
					return FALSE;
				}
				if (isset($match[2]) && $match[2]) {
					// Open persistent MemCache connection(s)
					// Multiple servers separated by semi-colon
					$pool=explode(';',$match[3]);
					$mcache=NULL;
					foreach ($pool as $server) {
						// Hostname:port
						list($host,$port)=explode(':',$server);
						if (is_null($port))
							// Use default port
							$port=11211;
						// Connect to each server
						if (is_null($mcache))
							$mcache=memcache_pconnect($host,$port);
						else
							memcache_add_server($mcache,$host,$port);
					}
					// MemCache
					self::$backend=array('type'=>$ext,'id'=>$mcache);
				}
				else
					// APC and XCache
					self::$backend=array('type'=>$ext);
			}
			self::$l1cache=NULL;
			return TRUE;
		}
		// Unknown back-end
		trigger_error(self::TEXT_Backend);
		return FALSE;
	}

	/**
		Store data in framework cache; Return TRUE/FALSE on success/failure
			@return boolean
			@param $name string
			@param $data mixed
			@public
	**/
	public static function store($name,$data) {
		if (!self::$global['CACHE'])
			return TRUE;
		if (is_null(self::$backend)) {
			// Auto-detect back-end
			self::detect();
			if (!self::prep())
				return FALSE;
		}
		$key=$_SERVER['SERVER_NAME'].'.'.$name;
		// Serialize data for storage
		$time=time();
		// Add timestamp
		$val=gzdeflate(serialize(array($time,$data)));
		// Instruct back-end to store data
		switch (self::$backend['type']) {
			case 'apc':
				$ok=apc_store($key,$val);
				break;
			case 'memcache':
				$ok=memcache_set(self::$backend['id'],$key,$val);
				break;
			case 'xcache':
				$ok=xcache_set($key,$val);
				break;
			case 'folder':
				$ok=file_put_contents(
					self::$backend['id'].$key,$val,LOCK_EX);
				break;
		}
		if (is_bool($ok) && !$ok) {
			self::$global['CONTEXT']=$name;
			trigger_error(self::TEXT_Store);
			return FALSE;
		}
		// Free up space for level-1 cache
		while (count(self::$l1cache) && strlen(serialize($data))+
			strlen(serialize(array_slice(self::$l1cache,1)))>
			ini_get('memory_limit')-memory_get_peak_usage())
				self::$l1cache=array_slice(self::$l1cache,1);
		self::$l1cache[$name]=array('data'=>$data,'time'=>$time);
		return TRUE;
	}

	/**
		Retrieve value from framework cache
			@return mixed
			@param $name string
			@param $quiet boolean
			@public
	**/
	public static function fetch($name,$quiet=FALSE) {
		if (!self::$global['CACHE'])
			return FALSE;
		if (is_null(self::$backend)) {
			// Auto-detect back-end
			self::detect();
			if (!self::prep())
				return FALSE;
		}
		if (!isset(self::$stats['CACHE']))
			self::$stats['CACHE']=array(
				'level-1'=>array('hits'=>0,'misses'=>0),
				'level-2'=>array('hits'=>0,'misses'=>0)
			);
		// Check level-1 cache first
		if (isset(self::$l1cache) && isset(self::$l1cache[$name])) {
			self::$stats['CACHE']['level-1']['hits']++;
			return self::$l1cache[$name]['data'];
		}
		else
			self::$stats['CACHE']['level-1']['misses']++;
		$key=$_SERVER['SERVER_NAME'].'.'.$name;
		// Instruct back-end to fetch data
		switch (self::$backend['type']) {
			case 'apc':
				$val=apc_fetch($key);
				break;
			case 'memcache':
				$val=memcache_get(self::$backend['id'],$key);
				break;
			case 'xcache':
				$val=xcache_get($key);
				break;
			case 'folder':
				$val=is_file(self::$backend['id'].$key)?
					file_get_contents(self::$backend['id'].$key):FALSE;
				break;
		}
		if (is_bool($val)) {
			self::$stats['CACHE']['level-2']['misses']++;
			// No error display if specified
			if (!$quiet) {
				self::$global['CONTEXT']=$name;
				trigger_error(self::TEXT_Fetch);
			}
			self::$l1cache[$name]=NULL;
			return FALSE;
		}
		self::$stats['CACHE']['level-2']['hits']++;
		// Unserialize timestamp and data
		list($time,$data)=unserialize(gzinflate($val));
		// Free up space for level-1 cache
		while (count(self::$l1cache) && strlen(serialize($data))+
			strlen(serialize(array_slice(self::$l1cache,1)))>
			ini_get('memory_limit')-memory_get_peak_usage())
				self::$l1cache=array_slice(self::$l1cache,1);
		self::$l1cache[$name]=array('data'=>$data,'time'=>$time);
		return $data;
	}

	/**
		Delete variable from framework cache
			@return boolean
			@param $name string
			@public
	**/
	public static function remove($name) {
		if (!self::$global['CACHE'])
			return TRUE;
		if (is_null(self::$backend)) {
			// Auto-detect back-end
			self::detect();
			if (!self::prep())
				return FALSE;
		}
		$key=$_SERVER['SERVER_NAME'].'.'.$name;
		// Instruct back-end to clear data
		switch (self::$backend['type']) {
			case 'apc':
				$ok=!apc_exists($key) || apc_delete($key);
				break;
			case 'memcache':
				$ok=memcache_delete(self::$backend['id'],$key);
				break;
			case 'xcache':
				$ok=!xcache_isset($key) || xcache_unset($key);
				break;
			case 'folder':
				$ok=is_file(self::$backend['id'].$key) &&
					unlink(self::$backend['id'].$key);
				break;
		}
		if (is_bool($ok) && !$ok) {
			self::$global['CONTEXT']=$name;
			trigger_error(self::TEXT_Clear);
			return FALSE;
		}
		// Check level-1 cache first
		if (isset(self::$l1cache) && isset(self::$l1cache[$name]))
			unset(self::$l1cache[$name]);
		return TRUE;
	}

	/**
		Return FALSE if specified variable is not in cache; otherwise,
		return array containing Un*x timestamp and data size
			@return mixed
			@param $name string
			@public
	**/
	public static function cached($name) {
		return self::fetch($name,TRUE)?
			array(
				'time'=>self::$l1cache[$name]['time'],
				'size'=>strlen(serialize(self::$l1cache[$name]['data']))
			):
			FALSE;
	}

}

//! PHP DOMDocument extension
class XMLtree extends DOMDocument {

	public
		//! Default DOMDocument fragment
		$fragment,
		//! Current node pointer
		$nodeptr;

	/**
		Get inner HTML contents of node
			@return string
			@param $node DOMElement
			@public
	**/
	public function innerHTML($node) {
		return preg_replace(
			'/^.+?>(.*)<.+?$/s','$1',
			$node->ownerDocument->saveXML($node)
		);
	}

	/**
		General-purpose pre-order XML tree traversal
			@param $pre mixed
			@param $type integer
			@public
	**/
	public function traverse($pre,$type=XML_ELEMENT_NODE) {
		// Start at document root
		$root=$this->documentElement;
		$node=&$this->nodeptr;
		$node=$root;
		$flag=FALSE;
		for (;;) {
			if (!$flag) {
				// Call pre-order handler for specified node type
				if (is_null($type) || $node->nodeType==$type)
					call_user_func($pre);
				if ($node->firstChild) {
					// Descend to branch
					$node=$node->firstChild;
					continue;
				}
			}
			if ($node->isSameNode($root))
				// Root node reached; Exit loop
				break;
			// Post-order sequence
			if ($node->nextSibling) {
				// Stay on same level
				$flag=FALSE;
				$node=$node->nextSibling;
			}
			else {
				// Ascend to parent node
				$flag=TRUE;
				$node=$node->parentNode;
			}
		}
	}

	/**
		Class constructor
			@public
	**/
	public function __construct() {
		// Default XMLTree settings
		$this->formatOutput=TRUE;
		$this->preserveWhiteSpace=TRUE;
		$this->strictErrorChecking=FALSE;
	}

}

//! XML data conversion tools
class XMLdata {

	//! XML translation table
	private static $xmltab=array();

	/**
		Return XML translation table
			@return array
			@param $latin boolean
			@public
	**/
	public static function table($latin=FALSE) {
		if (!isset(self::$xmltab[$latin])) {
			$xl8=get_html_translation_table(HTML_ENTITIES,ENT_COMPAT);
			foreach ($xl8 as $key=>$val)
				$tab[$latin?$val:$key]='&#'.ord($key).';';
			self::$xmltab[$latin]=$tab;
		}
		return self::$xmltab[$latin];
	}

	/**
		Convert plain text to XML entities
			@return string
			@param $str string
			@param $latin boolean
			@public
	**/
	public static function encode($str,$latin=FALSE) {
		return strtr($str,self::table($latin));
	}

	/**
		Convert XML entities to plain text
			@return string
			@param $str string
			@param $latin boolean
			@public
	**/
	public static function decode($str,$latin=FALSE) {
		return strtr($str,array_flip(self::table($latin)));
	}

}

// Quietly initialize the framework
Runtime::start();

