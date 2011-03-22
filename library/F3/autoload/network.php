<?php

/**
	Network utilities for the PHP Fat-Free Framework

	The contents of this file are subject to the terms of the GNU General
	Public License Version 3.0. You may not use this file except in
	compliance with the license. Any of the license terms and conditions
	can be waived if you get permission from the copyright holder.

	Copyright (c) 2009-2010 F3 Factory
	Bong Cosca <bong.cosca@yahoo.com>

		@package Network
		@version 1.4.0
**/

//! Network utilities
class Network extends Core {

	//! Minimum framework version required to run
	const F3_Minimum='1.4.0';

	/**
		Send ICMP echo request to specified host; Return array containing
		minimum/average/maximum round-trip time (in millisecs) and number of
		packets received, or FALSE if host is unreachable
			@return mixed
			@param $addr string
			@param $dns boolean
			@param $count integer
			@param $wait integer
			@param $ttl integer
			@public
	**/
	public static function ping($addr,$dns=FALSE,$count=3,$wait=3,$ttl=30) {
		// ICMP transmit socket
		$tsocket=socket_create(AF_INET,SOCK_RAW,1);
		// Set TTL
		socket_set_option($tsocket,0,PHP_OS!='Linux'?4:2,$ttl);
		// ICMP receive socket
		$rsocket=socket_create(AF_INET,SOCK_RAW,1);
		// Bind to all network interfaces
		socket_bind($rsocket,0,0);
		// Initialize counters
		list($rtt,$rcv,$min,$max)=array(0,0,0,0);
		for ($i=0;$i<$count;$i++) {
			// Send ICMP header and payload
			$data=uniqid();
			$payload=Expansion::hexbin('0800000000000000').$data;
			// Recalculate ICMP checksum
			if (strlen($payload)%2)
				$payload.=Expansion::hexbin('00');
			$bits=unpack('n*',$payload);
			$sum=array_sum($bits);
			while ($sum>>16)
				$sum=($sum>>16)+($sum&0xFFFF);
			$payload=Expansion::hexbin('0800').pack('n*',~$sum).
				Expansion::hexbin('00000000').$data;
			// Transmit ICMP packet
			@socket_sendto($tsocket,$payload,strlen($payload),0,$addr,0);
			// Start timer
			$time=microtime(TRUE);
			$rset=array($rsocket);
			$tset=array();
			$xset=array();
			// Wait for incoming ICMP packet
			socket_select($rset,$tset,$xset,$wait);
			if ($rset &&
				@socket_recvfrom($rsocket,$reply,255,0,$host,$port)) {
				$elapsed=1e3*(microtime(TRUE)-$time);
				// Socket didn't timeout; Record round-trip time
				$rtt+=$elapsed;
				if ($elapsed>$max)
					$max=$elapsed;
				if (!($min>0) || $elapsed<$min)
					$min=$elapsed;
				// Count packets received
				$rcv++;
				if ($host)
					$addr=$host;
			}
		}
		socket_close($tsocket);
		socket_close($rsocket);
		return $rcv?
			array(
				'host'=>$dns?gethostbyaddr($addr):$addr,
				'min'=>(int)round($min),
				'max'=>(int)round($max),
				'avg'=>(int)round($rtt/$rcv),
				'packets'=>$rcv
			):
			FALSE;
	}

	/**
		Return the path taken by packets to a specified network destination
			@return array
			@param $addr string
			@param $dns boolean
			@param $wait integer
			@param $hops integer
			@public
	**/
	public static function traceroute($addr,$dns=FALSE,$wait=3,$hops=30) {
		$route=array();
		for ($i=0;$i<$hops;$i++) {
			set_time_limit(ini_get('default_socket_timeout'));
			$result=self::ping($addr,$dns,3,$wait,$i+1);
			$route[]=$result;
			if (gethostbyname($result['host'])==gethostbyname($addr))
				break;
		}
		return $route;
	}

	/**
		Return TRUE if IP address is local or within a private IPv4 range
			@param $addr string
			@public
	**/
	public static function privateIP($addr) {
		return preg_match('/^127\.0\.0\.\d{1,3}$/',$addr) ||
			!filter_var(
				$addr,
				FILTER_VALIDATE_IP,
				FILTER_FLAG_IPV4|FILTER_FLAG_NO_PRIV_RANGE
			);
	}

	/**
		Sniff headers for real IP address
			@return string
			@public
	**/
	public static function realIP() {
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			// Behind proxy
			return $_SERVER['HTTP_CLIENT_IP'];
		elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			// Use first IP address in list
			list($ip)=explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
			return $ip;
		}
		return $_SERVER['REMOTE_ADDR'];
	}

	/**
		Return TRUE if remote address is listed in spam database
			@return boolean
			@param $addr string
			@public
	**/
	public static function spam($addr) {
		if (!self::privateIP($addr) &&
			(!isset(self::$global['EXEMPT']) ||
				!in_array($addr,explode('|',self::$global['EXEMPT'])))) {
			// Convert to reverse IP dotted quad
			$addr=implode('.',array_reverse(explode('.',$addr)));
			foreach (explode('|',self::$global['DNSBL']) as $list)
				// Check against DNS blacklist
				if (gethostbyname($addr.'.'.$list)!=$addr.'.'.$list)
					return TRUE;
		}
		return FALSE;
	}

	/**
		Bootstrap code
			@public
	**/
	public static function onLoad() {
		// Sockets extension required
		if (!extension_loaded('sockets')) {
			// Unable to continue
			self::$global['CONTEXT']='sockets';
			trigger_error(self::TEXT_PHPExt);
			return;
		}
	}

}
