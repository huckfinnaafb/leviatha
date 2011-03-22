<?php

/**
	MongoDB Mapper for the PHP Fat-Free Framework

	The contents of this file are subject to the terms of the GNU General
	Public License Version 3.0. You may not use this file except in
	compliance with the license. Any of the license terms and conditions
	can be waived if you get permission from the copyright holder.

	Copyright (c) 2009-2010 F3 Factory
	Bong Cosca <bong.cosca@yahoo.com>

		@package M2
		@version 1.4.2
**/

//! MongoDB Mapper
class M2 extends Core {

	//! Minimum framework version required to run
	const F3_Minimum='1.4.2';

	//@{
	//! Locale-specific error/exception messages
	const
		TEXT_DBConnect='Database connection failed',
		TEXT_M2Empty='M2 is empty',
		TEXT_M2Collection='Collection {@CONTEXT} does not exist';
	//@}

	//@{
	//! M2 properties
	private $db=NULL;
	private $collection=NULL;
	private $object=NULL;
	private $criteria=NULL;
	private $order=NULL;
	private $offset=NULL;
	//@}

	/**
		Retrieve from cache; or save query results to cache if not
		previously executed
			@param $query array
			@param $ttl integer
			@private
	**/
	private function cache(array $query,$ttl) {
		$hash='mdb.'.F3::hashCode(serialize($query));
		$db=&self::$global[$this->db];
		$cached=Cache::cached($hash);
		if ($cached && (time()-$cached['time'])<$ttl) {
			// Gather cached queries for profiler
			if (!isset(self::$stats[$this->db]))
				self::$stats[$this->db]=array(
					'cache'=>array(),
					'queries'=>array()
				);
			$json=json_encode($query,TRUE);
			if (!isset(self::$stats[$this->db]['cache'][$json]))
				self::$stats[$this->db]['cache'][$json]=0;
			self::$stats[$this->db]['cache'][$json]++;
			// Retrieve from cache
			$db=Cache::fetch($hash);
		}
		else {
			$this->exec($query);
			if (!self::$global['ERROR'])
				// Save to cache
				Cache::store($db);
		}
	}

	/**
		Execute MongoDB query
			@param $query array
			@private
	**/
	private function exec(array $query) {
		$db=&self::$global[$this->db];
		// Except for save method, collection must exist
		$list=$db['mdb']->listCollections();
		foreach ($list as &$coll)
			$coll=$coll->getName();
		if ($query['method']!='save' &&
			!in_array($this->collection,$list)) {
				self::$global['CONTEXT']=$this->collection;
				trigger_error(self::TEXT_M2Collection);
				return;
		}
		if (isset($query['mapreduce'])) {
			// Create temporary collection
			$ref=$db['mdb']->selectCollection(
				'm2.'.F3::hashCode(json_encode($query))
			);
			$ref->batchInsert(iterator_to_array($out,FALSE));
			$map=$query['mapreduce'];
			$func='function() {}';
			// Map-reduce
			$tmp=$db['mdb']->command(
				array(
					'mapreduce'=>$ref->getName(),
					'map'=>isset($map['map'])?
						$map['map']:$func,
					'reduce'=>isset($map['reduce'])?
						$map['reduce']:$func,
					'finalize'=>isset($map['finalize'])?
						$map['finalize']:$func
				)
			);
			if (!$tmp['ok']) {
				trigger_error($tmp['errmsg']);
				return FALSE;
			}
			$ref->remove();
			foreach (iterator_to_array(
				$db['mdb']->selectCollection($tmp['result'])->find(),
				FALSE) as $agg)
				$ref->insert($agg['_id']);
			$out=$ref->find();
			$ref->drop();
		}
		else {
			// Execute command
			$out=preg_match('/find/',$query['method'])?
				// find and findOne methods allow selection of fields
				call_user_func(
					array(
						$db['mdb']->selectCollection($this->collection),
						$query['method']
					),
					isset($query['criteria'])?$query['criteria']:array(),
					isset($query['fields'])?$query['fields']:array()
				):
				// count and remove methods can specify criteria
				(preg_match('/count|remove/',$query['method'])?
					call_user_func(
						array(
							$db['mdb']->selectCollection($this->collection),
							$query['method']
						),
						isset($query['criteria'])?$query['criteria']:array()
					):
					// All other methods
					call_user_func(
						array(
							$db['mdb']->selectCollection($this->collection),
							$query['method']
						),
						$this->object
					));
		}
		if (preg_match('/find/',$query['method'])) {
			if (isset($query['order']))
				// Sort results
				$out=$out->sort($query['order']);
			if (isset($query['offset']))
				// Skip to record offset
				$out=$out->skip($query['offset']);
			if (isset($query['limit']))
				// Limit number of results
				$out=$out->limit($query['limit']);
			// Convert cursor to PHP array
			$db['result']=iterator_to_array($out,FALSE);
		}
		else
			$db['result']=array($query['method']=>$out);
		// Gather real queries for profiler
		if (!isset(self::$stats[$this->db]))
			self::$stats[$this->db]=array(
				'cache'=>array(),
				'queries'=>array()
			);
		$json=json_encode($query,TRUE);
		if (!isset(self::$stats[$this->db]['queries'][$json]))
			self::$stats[$this->db]['queries'][$json]=0;
		self::$stats[$this->db]['queries'][$json]++;
		return $db['result'];
	}

	/**
		Similar to M2->find method but provides more fine-grained control
		over specific fields and mapping-reduction of results
			@return array
			@param $fields array
			@param $criteria mixed
			@param $mapreduce mixed
			@param $order mixed
			@param $limit mixed
			@param $offset mixed
			@param $ttl integer
			@public
	**/
	public function lookup(
		array $fields,
		$criteria=NULL,
		$mapreduce=NULL,
		$order=NULL,
		$limit=NULL,
		$offset=NULL,
		$ttl=0) {
		$query=array(
			'method'=>'find',
			'fields'=>$fields,
			'criteria'=>$criteria,
			'mapreduce'=>$mapreduce,
			'order'=>$order,
			'limit'=>$limit,
			'offset'=>$offset
		);
		if ($ttl)
			$this->cache($query,$ttl);
		else
			$this->exec($query);
		return self::$global[$this->db]['result'];
	}

	/**
		Alias of the lookup method
			@public
	**/
	public function select() {
		// PHP doesn't allow direct use as function argument
		$args=func_get_args();
		return call_user_func_array(array($this,'lookup'),$args);
	}

	/**
		Return an array of collection objects matching criteria
			@return array
			@param $criteria mixed
			@param $order mixed
			@param $limit mixed
			@param $offset mixed
			@param $ttl integer
			@public
	**/
	public function find(
		$criteria=NULL,$order=NULL,$limit=NULL,$offset=NULL,$ttl=0) {
		$query=array(
			'method'=>'find',
			'criteria'=>$criteria,
			'order'=>$order,
			'limit'=>$limit,
			'offset'=>$offset
		);
		if ($ttl)
			$this->cache($query,$ttl);
		else
			$this->exec($query);
		return self::$global[$this->db]['result'];
	}

	/**
		Return the first object that matches the specified criteria
			@return array
			@param $criteria mixed
			@param $order mixed
			@param $limit mixed
			@param $offset mixed
			@param $ttl integer
			@public
	**/
	public function findOne(
		$criteria=NULL,$order=NULL,$limit=NULL,$offset=NULL,$ttl=0) {
		list($result)=
			$this->find($criteria,$order,$limit,$offset,$ttl)?:array(NULL);
		return $result;
	}

	/**
		Return number of collection objects that match criteria
			@return integer
			@param $criteria mixed
			@public
	**/
	public function found($criteria=NULL) {
		$this->exec(
			array(
				'method'=>'count',
				'criteria'=>$criteria
			)
		);
		return self::$global[$this->db]['result']['count'];
	}

	/**
		Hydrate M2 with elements from framework array variable, keys of
		which will be identical to field names in collection object
			@param $name string
			@public
	**/
	public function copyFrom($name) {
		if (is_array(F3::get($name)))
			$this->object=F3::get($name);
	}

	/**
		Populate framework array variable with M2 properties, keys of
		which will have names identical to fields in collection object
			@param $name string
			@param $fields string
			@public
	**/
	public function copyTo($name,$fields=NULL) {
		if (is_string($fields))
			$list=explode('|',$fields);
		foreach (array_keys($this->object) as $field)
			if (!isset($list) || in_array($field,$list))
				F3::set($name.'.'.$field,$this->object[$field]);
	}

	/**
		Dehydrate M2
			@public
	**/
	public function reset() {
		// Dehydrate
		$this->object=NULL;
		$this->criteria=NULL;
		$this->order=NULL;
		$this->offset=NULL;
	}

	/**
		Retrieve first collection object that satisfies criteria
			@param $criteria mixed
			@param $order mixed
			@param $offset integer
			@public
	**/
	public function load($criteria=NULL,$order=NULL,$offset=0) {
		// Execute beforeLoad event
		if (method_exists($this,'beforeLoad') && !$this->beforeLoad())
			return;
		if (!is_null($offset) && $offset>-1) {
			// Retrieve object
			$result=$this->findone($criteria,$order,1,$offset);
			$this->offset=NULL;
			if ($result) {
				// Hydrate M2
				$this->object=$result;
				$this->criteria=$criteria;
				$this->order=$order;
				$this->offset=$offset;
				return;
			}
			else
				$this->reset();
		}
		else
			$this->reset();
		if (method_exists($this,'afterLoad'))
			// Execute afterLoad event
			$this->afterLoad();
	}

	/**
		Retrieve N-th object relative to current using the same criteria
		that hydrated M2
			@param $count integer
			@public
	**/
	public function skip($count=1) {
		if ($this->dry()) {
			trigger_error(self::TEXT_M2Empty);
			return;
		}
		self::load($this->criteria,$this->order,$this->offset+$count);
	}

	/**
		Insert/update collection object
			@public
	**/
	public function save() {
		if (is_null($this->object)) {
			// M2 is empty
			trigger_error(self::TEXT_M2Empty);
			return;
		}
		// Execute beforeSave event
		if (method_exists($this,'beforeSave') && !$this->beforeSave())
			return;
		// Let the MongoDB driver decide how to persist the
		// collection object in the database
		$obj=$this->object;
		$this->exec(array('method'=>'save'));
		if (!isset($obj['_id']))
			// Reload to retrieve MongoID of inserted object
			$this->object=
				$this->exec(array('method'=>'findOne','criteria'=>$obj));
		if (method_exists($this,'afterSave'))
			// Execute afterSave event
			$this->afterSave();
	}

	/**
		Delete collection object and reset M2
			@public
	**/
	public function erase() {
		if (is_null($this->object)) {
			trigger_error(self::TEXT_M2Empty);
			return;
		}
		// Execute beforeErase event
		if (method_exists($this,'beforeErase') && !$this->beforeErase())
			return;
		$this->exec(array('method'=>'remove','criteria'=>$this->criteria));
		$this->reset();
		if (method_exists($this,'afterErase'))
			// Execute afterErase event
			$this->afterErase();
	}

	/**
		Return TRUE if M2 is NULL
			@return boolean
			@public
	**/
	public function dry() {
		return is_null($this->object);
	}

	/**
		Synchronize M2 and MongoDB collection
			@param $coll string
			@param $id string
			@public
	**/
	public function sync($coll,$id='DB') {
		$db=&self::$global[$id];
		// Can't proceed until DSN is set
		if (!$db || !$db['dsn']) {
			trigger_error(self::TEXT_DBConnect);
			return;
		}
		if (!isset($db['mdb'])) {
			if (!in_array('mongo',get_loaded_extensions())) {
				// MongoDB extension not activated
				self::$global['CONTEXT']='mongo';
				trigger_error(F3::resolve(self::TEXT_PHPExt));
				return;
			}
			try {
				$db['mdb']=new MongoDB(
					new Mongo(
						$db['dsn'],
						is_array($db['options'])?
							$db['options']:array('connect'=>TRUE)
					),
					$id
				);
			} catch (Exception $xcpt) {}
			if (!isset($db['mdb'])) {
				// Unable to connect
				trigger_error(self::TEXT_DBConnect);
				return;
			}
		}
		// Execute beforeSync event
		if (method_exists($this,'beforeSync') && !$this->beforeSync())
			return;
		// Initialize M2
		$this->db=$id;
		$this->collection=$coll;
		$this->object=NULL;
		if (method_exists($this,'afterSync'))
			// Execute afterSync event
			$this->afterSync();
	}

	/**
		Return value of M2-mapped field
			@return boolean
			@param $name string
			@public
	**/
	public function __get($name) {
		return $this->object[$name];
	}

	/**
		Assign value to M2-mapped field
			@return boolean
			@param $name string
			@param $value mixed
			@public
	**/
	public function __set($name,$value) {
		$this->object[$name]=$value;
	}

	/**
		Clear value of M2-mapped field
			@return boolean
			@param $name string
			@public
	**/
	public function __unset($name) {
		unset($this->object[$name]);
	}

	/**
		Return TRUE if M2-mapped field exists
			@return boolean
			@param $name string
			@public
	**/
	public function __isset($name) {
		return array_key_exists($name,$this->object);
	}

	/**
		Display class name if conversion to string is attempted
			@public
	**/
	public function __toString() {
		return get_class($this);
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
		Mapper constructor
			@public
	**/
	public function __construct() {
		// Execute mandatory sync method of child class
		call_user_func_array(
			array(get_called_class(),'sync'),func_get_args()
		);
	}

}
