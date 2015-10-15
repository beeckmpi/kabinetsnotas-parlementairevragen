<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mongodb_model extends CI_Model
{
	// constructor flags
	const ENABLE_PROFILER					= 0x01;		// turn on profiler
	const ENABLE_DEBUGGING					= 0x02;		// enable debugging
	const DEFAULT_CONSTRUCTOR_PARMS			= 0x04;		// Default constructor parms

	// mongodb config attributes	
	private	$mg_config_file					= 'mongodb';
	private	$mg_connection;
	private	$mg_db;
	private	$mg_connection_string;
	private	$mg_host;
	private	$mg_port;
	private	$mg_user;
	private	$mg_pass;
	private	$mg_dbname;
	private	$mg_persist;
	private	$mg_persist_key;
	
	// mongodb dynamic attributes
	private	$mg_selects						= array();
	private	$mg_select_subkeys				= array();
	private	$mg_wheres						= array();
	private	$mg_sorts						= array();
	private	$mg_limit						= 999999;
	private	$mg_offset						= 0;
	private $mg_output_filter				= array();		
	
	// debugging attibutes
	private $debugging_on					= FALSE;	// output debugging information		
	
	/**
	*	--------------------------------------------------------------------------------
	*	CONSTRUCTOR
	*	--------------------------------------------------------------------------------
	*
	*	Automatically check if the Mongo PECL extension has been installed/enabled.
	*	Generate the connection string and establish a connection to the MongoDB.
	*/
	public function __construct($parms = self::DEFAULT_CONSTRUCTOR_PARMS)
	{	
		parent::__construct();
		
		if ($parms & self::ENABLE_DEBUGGING)
		{
			$this->set_debugging_on();
		}		
	
		parent::__construct();	

		if(!class_exists('Mongo'))
		{
			show_error("The MongoDB PECL extension has not been installed or enabled", 500);
		}
	
		$this->connection_string();
		$this->connect();	

		if ($parms & self::ENABLE_PROFILER)
		{
			$this->output->enable_profiler(TRUE);			
		}				
	}
	/**
	* 
	************************************************************************************
	*								DEBUGGING METHODS
	************************************************************************************
	*
	*/	
	protected function is_debugging_on()
	{
		return $this->debugging_on;
	}
	
	protected function log_message($message, $prefix = FALSE)
	{
		$msg = ($prefix == FALSE) ? $message : $prefix . ' ' . $message;
		
		if ($this->is_debugging_on() && !IS_AJAX)
		{
			echo $msg . '<br/>';
		}
		else
		{
			log_message('debug', 'COMPLEX_MODEL:'.$msg);
		}
	}
	
	protected function set_debugging_on()
	{
		$this->debugging_on = TRUE;
		return $this;
	}	

	protected function set_debugging_off()
	{
		$this->debugging_on = FALSE;
		return $this;
	}
		
	/**
	* 
	************************************************************************************
	*								MONGODB METHODS
	************************************************************************************
	*
	*	--------------------------------------------------------------------------------
	*	ADD_INDEX
	*	--------------------------------------------------------------------------------
	*
	*	Ensure an index of the keys in a collection with optional parameters. To set values to descending order,
	*	you must pass values of either -1, FALSE, 'desc', or 'DESC', else they will be
	*	set to 1 (ASC).
	*
	*	@usage : $this->mongo_db->add_index($collection, array('first_name' => 'ASC', 'last_name' => -1), array('unique' => TRUE));
	*/	
	public function add_index($collection = "", $keys = array(), $options = array())
	{
		if(empty($collection))
		{
			show_error("No Mongo collection specified to add index to", 500);
		}
		if(empty($keys) || !is_array($keys))
		{
			show_error("Index could not be created to MongoDB Collection because no keys were specified", 500);
		}
		
		foreach($keys as $col => $val)
		{
			if($val == -1 || $val === FALSE || strtolower($val) == 'desc')
			{
				$keys[$col] = -1;
			} 
			else
			{
				$keys[$col] = 1;
			}
		}
		
		if($this->mg_db->{$collection}->ensureIndex($keys, $options) == TRUE)
		{
			$this->clear();
			return($this);
		}
		else
		{
			show_error("An error occured when trying to add an index to MongoDB Collection", 500);
		}
	}
	/**
	*	--------------------------------------------------------------------------------
	*	APPLY_OUTPUT_FILTERS
	*	--------------------------------------------------------------------------------
	*	Apply output filters to value which will be returned.
	*
	*	@usage : $this->apply_output_filters($source);
	*/	
	private function apply_output_filters($source)
	{
		if ((count($this->mg_output_filter) < 1) || empty($source))
		{
			return $source;
		}
	
		$target = $source;
		
		foreach ($this->mg_output_filter as $i => $filter)
		{
			if (!isset($filter['filter_type']))
			{
				continue;
			}

			switch ($filter['filter_type'])
			{
				case 'subarray_filter':
					if (!isset($filter['key_specification']) || !isset($filter['matching_value']))
					{
						break;
					}
					$key_specification_array = explode('.', $filter['key_specification']);
					$target = $this->traverse_subarray($target, $key_specification_array, count($key_specification_array), $filter['matching_value']);
					break;
				default:
					break;
			}
			
			return $target;
		}
	}
	/**
	*	--------------------------------------------------------------------------------
	*	CLEAR
	*	--------------------------------------------------------------------------------
	*
	*	Resets the class variables to default settings
	*/	
	private function clear()
	{
		$this->mg_selects = array();
		$this->mg_select_subkeys = array();
		$this->mg_wheres = array();
		$this->mg_limit = NULL;
		$this->mg_offset = NULL;
		$this->mg_sorts = array();
		$this->mg_output_filter = array();
		if($this->mg_dbname != $this->config->item('mongo_db'))
		{
			$this->switch_db($this->config->item('mongo_db'));
		}
	}
	/**
	*	--------------------------------------------------------------------------------
	*	CONNECT TO MONGODB
	*	--------------------------------------------------------------------------------
	*
	*	Establish a connection to MongoDB using the connection string generated in
	*	the connection_string() method.  If 'mongo_persist_key' was set to true in the
	*	config file, establish a persistent connection.  We allow for only the 'persist'
	*	option to be set because we want to establish a connection immediately.
	*/
	private function connect()
	{
		$options = array();
		if($this->mg_persist === TRUE)
		{
			$options['persist'] = isset($this->mg_persist_key) && !empty($this->mg_persist_key) ? $this->mg_persist_key : 'ci_mongo_persist';
		}
		
		if($this->config->item('mongo_replicaset') === TRUE)
		{
			$options['replicaSet'] = TRUE;
		}
		
		try
		{
			$this->mg_connection = new MongoClient($this->mg_connection_string, $options);
			$this->mg_db = $this->mg_connection->{$this->mg_dbname};
		}
		catch(MongoConnectionException $e)
		{
			show_error("Unable to connect to MongoDB: {$e->getMessage()}", 500);
		}
		
		if($this->config->item('mongo_readfromslaves') === TRUE)
		{
			$this->mg_connection->setSlaveOkay(TRUE);
		}
		
		return($this);
	}
	/**
	*	--------------------------------------------------------------------------------
	*	BUILD CONNECTION STRING
	*	--------------------------------------------------------------------------------
	*
	*	Build the connection string from the config file.
	*/	
	private function connection_string()
	{
		$this->config->load($this->mg_config_file);
		
		$this->mg_hosts = trim(implode(',', $this->config->item('mongo_hosts')));
		$this->mg_user = trim($this->config->item('mongo_user'));
		$this->mg_pass = trim($this->config->item('mongo_pass'));
		$this->mg_dbname = trim($this->config->item('mongo_db'));
		$this->mg_persist = trim($this->config->item('mongo_persist'));
		$this->mg_persist_key = trim($this->config->item('mongo_persist_key'));
		
		$connection_string = "mongodb://";
		
		if(empty($this->mg_hosts))
		{
			show_error("The Host must be set to connect to MongoDB", 500);
		}
		
		if(empty($this->mg_dbname))
		{
			show_error("The Database must be set to connect to MongoDB", 500);
		}
		
		if(!empty($this->mg_user) && !empty($this->mg_pass))
		{
			$connection_string .= "{$this->mg_user}:{$this->mg_pass}@";
		}
		
		$connection_string .= "{$this->mg_hosts}";
		
		$this->mg_connection_string = trim($connection_string);
	}
	/**
	*	--------------------------------------------------------------------------------
	*	COUNT
	*	--------------------------------------------------------------------------------
	*
	*	Count the documents based upon the passed parameters
	*
	*	@usage : $this->mongo_db->get('foo');
	*
	*/
	public function count($collection = "")
	{
		if(empty($collection))
		{
			show_error("In order to retreive a count of documents from MongoDB, a collection name must be passed", 500);
		}
		$count = $this->mg_db->{$collection}->find($this->mg_wheres)->limit((int) $this->mg_limit)->skip((int) $this->mg_offset)->count();
		$this->clear();
		return($count);
	}
	/**
	*	--------------------------------------------------------------------------------
	*	DELETE
	*	--------------------------------------------------------------------------------
	*
	*	delete document from the passed collection based upon certain criteria
	*
	*	@usage : $this->mongo_db->delete('foo', $data = array());
	*/
	public function delete($collection = "")
	{
		if(empty($collection))
		{
			show_error("No Mongo collection selected to delete from", 500);
		}
		try
		{
			$this->mg_db->{$collection}->remove($this->mg_wheres, array('w' => 1, 'justOne' => TRUE));
			$this->clear();
			return(TRUE);
		}
		catch(MongoCursorException $e)
		{
			show_error("Delete of data into MongoDB failed: {$e->getMessage()}", 500);
		}
	}
	/**
	*	--------------------------------------------------------------------------------
	*	DELETE_ALL
	*	--------------------------------------------------------------------------------
	*
	*	Delete all documents from the passed collection based upon certain criteria
	*
	*	@usage : $this->mongo_db->delete_all('foo', $data = array());
	*/
	 public function delete_all($collection = "")
	 {
		if(empty($collection))
		{
			show_error("No Mongo collection selected to delete from", 500);
		}
		try
		{
			$this->mg_db->{$collection}->remove($this->mg_wheres, array('w' => 1, 'justOne' => FALSE));
			$this->clear();
			return(TRUE);
		}
		catch(MongoCursorException $e)
		{
			show_error("Delete of data into MongoDB failed: {$e->getMessage()}", 500);
		}		
	}
	/**
	*	--------------------------------------------------------------------------------
	*	Drop_collection
	*	--------------------------------------------------------------------------------
	*
	*	Drop a Mongo collection
	*	@usage: $this->mongo_db->drop_collection('foo', 'bar');
	*/
	public function drop_collection($db = "", $col = "")
	{
		if(empty($db))
		{
			show_error('Failed to drop MongoDB collection because database name is empty', 500);
		}
	
		if(empty($col))
		{
			show_error('Failed to drop MongoDB collection because collection name is empty', 500);
		}
		
		else
		{
			try
			{
				$this->mg_connection->{$db}->{$col}->drop();
				return TRUE;
			}
			catch (Exception $e)
			{
				show_error("Unable to drop Mongo collection `{$col}`: {$e->getMessage()}", 500);
			}
		}
		
		return($this);
	}	
	/**
	*	--------------------------------------------------------------------------------
	*	Drop_db
	*	--------------------------------------------------------------------------------
	*
	*	Drop a Mongo database
	*	@usage: $this->mongo_db->drop_db("foobar");
	*/
	public function drop_db($database = '')
	{
		if(empty($database))
		{
			show_error('Failed to drop MongoDB database because name is empty', 500);
		}
		
		else
		{
			try
			{
				$this->mg_connection->{$database}->drop();
				return TRUE;
			}
			catch (Exception $e)
			{
				show_error("Unable to drop Mongo database `{$database}`: {$e->getMessage()}", 500);
			}
		}
		return($this);
	}
	/**
	*	--------------------------------------------------------------------------------
	*	GET
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents based upon the passed parameters
	*
	*	@usage : $this->mongo_db->get('foo', array('bar' => 'something'));
	*/
	public function get($collection = "", $limit = NULL) 
	{
		if(!is_null($limit))
		{
			$this->limit($limit);
		}
		if(empty($collection))
		{
			show_error("In order to retreive documents from MongoDB, a collection name must be passed", 500);
		}
		$results = array();
		$documents = $this->mg_db->{$collection}
			->find($this->mg_wheres, $this->mg_selects)
			->limit((int) $this->mg_limit)
			->skip((int) $this->mg_offset)
			->sort($this->mg_sorts);
		
		$returns = array();

		foreach($documents as $doc)
		{
			if($this->mg_limit === 1)
			{
				$returns = $this->get_subkeys($doc);
				break;
			}
			else
			{
				$returns[] = $this->get_subkeys($doc);
			}
		}

		$returns = $this->apply_output_filters($returns);
		$this->clear();
		
		return $returns;
	}
	/**
	*	--------------------------------------------------------------------------------
	*
	*	GET COLLECTION HANDLE
	*
	*	Return a handle handle for the specied collection.
	*
	*	@usage : $this->get_collection_handel('foo')
	*
	**/	
	protected function get_collection_handel($collection)
	{
		if(empty($collection))
		{
			show_error("In order to retreive documents from MongoDB, a collection name must be passed", 500);
		}
		
		return $this->mg_db->{$collection};
	}
	/**
	*	--------------------------------------------------------------------------------
	*	GET_SUBKEYS
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents based upon the passed parameters
	*
	*	@usage : $this->mongo_db->get('foo', array('bar' => 'something'));
	*/
	private function get_subkeys($data)
	{
		if (empty($data))
		{
			return FALSE;
		}
		
		if (empty($this->mg_select_subkeys) || !is_array($this->mg_select_subkeys) || (count($this->mg_select_subkeys) < 1))
		{
			return $data;
		}
		
		if (!isset($data[0]))
		{	
			$returns = array();
			/*	
			if (isset($data['_id']))
			{
				$returns['_id'] = $data['_id'];
			}
			*/
		
			foreach ($this->mg_select_subkeys as $i => $key)
			{
				if (isset($data[$key]))
				{
					$returns[$key] = $data[$key];
				}
			}
		}
		else
		{
			$cnt = 0;
			$retuns = array();
			foreach ($data as $i => $row)
			{
				$inc_cnt = FALSE;
				/*
				if (isset($row['_id']))
				{
					$returns[$cnt]['_id'] = $row['_id'];
					$inc_cnt = TRUE;
				}
				*/
		
				foreach ($this->mg_select_subkeys as $j => $key)
				{
					if (isset($row[$key]))
					{
						$returns[$cnt][$key] = $row[$key];
						$inc_cnt = TRUE;
					}
				}

				if ($inc_cnt)
				{
					$cnt++;
				}
			}
		}
		
		return $returns;
	}
	/**
	*	--------------------------------------------------------------------------------
	*	GET_WHERE
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents based upon the passed parameters
	*
	*	@usage : $this->mongo_db->get_where('foo', array('bar' => 'something'));
	*/
	public function get_where($collection = "", $where = array(), $limit = 99999)
	{
		return($this->mg_where($where)->limit($limit)->get($collection));
	} 	
	/**
	*	--------------------------------------------------------------------------------
	*	INSERT
	*	--------------------------------------------------------------------------------
	*
	*	Insert a new document into the passed collection
	*
	*	@usage : $this->mongo_db->insert('foo', $data = array());
	*/
	public function insert($collection = "", $data = array())
	{
		if(empty($collection))
		{
			show_error("No Mongo collection selected to insert into", 500);
		}
		if(count($data) == 0 || !is_array($data))
		{
			show_error("Nothing to insert into Mongo collection or insert is not an array", 500);
		}
		
		try
		{
			$this->mg_db->{$collection}->insert($data, array('w' => 1));
			$this->clear();
			if(isset($data['_id']))
			{
				return($data['_id']);
			}
			else
			{
				return(FALSE);
			}
		}
		catch(MongoCursorException $e)
		{
			show_error("Insert of data into MongoDB failed: {$e->getMessage()}", 500);
		}	
	}
	/**
	*	--------------------------------------------------------------------------------
	*	LIKE PARAMETERS
	*	--------------------------------------------------------------------------------
	*	
	*	Get the documents where the (string) value of a $field is like a value. The defaults
	*	allow for a case-insensitive search.
	*
	*	@param $flags
	*	Allows for the typical regular expression flags:
	*		i = case insensitive
	*		m = multiline
	*		x = can contain comments
	*		l = locale
	*		s = dotall, "." matches everything, including newlines
	*		u = match unicode
	*
	*	@param $enable_start_wildcard
	*	If set to anything other than TRUE, a starting line character "^" will be prepended
	*	to the search value, representing only searching for a value at the start of 
	*	a new line.
	*
	*	@param $enable_end_wildcard
	*	If set to anything other than TRUE, an ending line character "$" will be appended
	*	to the search value, representing only searching for a value at the end of 
	*	a line.
	*
	*	@usage : $this->mongo_db->like('foo', 'bar', 'im', FALSE, TRUE);
	*/
	public function like($field = "", $value = "", $flags = "i", $enable_start_wildcard = TRUE, $enable_end_wildcard = TRUE)
	{
		$field = (string) trim($field);
		$this->where_init($field);
		$value = (string) trim($value);
		//$value = quotemeta($value);
		
		if($enable_start_wildcard !== TRUE)
		{
			$value = "^" . $value;
		}
		
		if($enable_end_wildcard !== TRUE)
		{
			$value .= "$";
		}
		
		$regex = "/$value/$flags";

		$this->mg_wheres[$field] = new MongoRegex($regex);
		return($this);
	}
	/**
	*	--------------------------------------------------------------------------------
	*	LIMIT DOCUMENTS
	*	--------------------------------------------------------------------------------
	*
	*	Limit the result set to $x number of documents
	*
	*	@usage : $this->mongo_db->limit($x);
	*/
	public function limit($x = 99999, $offset = NULL)
	{
		if(!is_null($offset))
		{
			$this->offset($offset);
		}
		
		if($x !== NULL && is_numeric($x) && $x >= 1)
		{
			$this->mg_limit = (int) $x;
		}
		return($this);
	}
	/**
	*	--------------------------------------------------------------------------------
	*	LIST_INDEXES
	*	--------------------------------------------------------------------------------
	*
	*	Lists all indexes in a collection.
	*
	*	@usage : $this->mongo_db->list_indexes($collection);
	*/
	public function list_indexes($collection = "")
	{
		if(empty($collection))
		{
			show_error("No Mongo collection specified to remove all indexes from", 500);
		}
		return($this->mg_db->{$collection}->getIndexInfo());
	}
	/**
	*	--------------------------------------------------------------------------------
	*	OFFSET DOCUMENTS
	*	--------------------------------------------------------------------------------
	*
	*	Offset the result set to skip $x number of documents
	*
	*	@usage : $this->mongo_db->offset($x);
	*/
	
	public function offset($x = 0)
	{
		if($x !== NULL && is_numeric($x) && $x >= 1)
		{
			$this->mg_offset = (int) $x;
		}
		return($this);
	}
	/**
	*	--------------------------------------------------------------------------------
	*	ORDER BY PARAMETERS
	*	--------------------------------------------------------------------------------
	*
	*	Sort the documents based on the parameters passed. To set values to descending order,
	*	you must pass values of either -1, FALSE, 'desc', or 'DESC', else they will be
	*	set to 1 (ASC).
	*
	*	@usage : $this->mongo_db->where_between('foo', 20, 30);
	*/
	public function order_by($fields = array())
	{
		foreach($fields as $col => $val)
		{
			if($val == -1 || $val === FALSE || strtolower($val) == 'desc')
			{
				$this->mg_sorts[$col] = -1;
			} 
			else
			{
				$this->mg_sorts[$col] = 1;
			}
		}
		return($this);
	}
	/**
	*	--------------------------------------------------------------------------------
	*	OR_WHERE PARAMETERS
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents where the value of a $field may be something else
	*
	*	@usage : $this->mongo_db->or_where(array( array('foo'=>'bar', 'bar'=>'foo' ))->get('foobar');
	*
	*/
	public function or_where($wheres = array())
	{
	   if(count($wheres) > 0)
	   {
		   if(!isset($this->mg_wheres['$or']) || !is_array($this->mg_wheres['$or']))
		   {
				   $this->mg_wheres['$or'] = array();
		   }

		   foreach($wheres as $wh => $val)
		   {
				   $this->mg_wheres['$or'][] = array($wh=>$val);
		   }
	   }
	   return($this);
	}
	
	/**
	*	--------------------------------------------------------------------------------
	*	REMOVE_INDEX
	*	--------------------------------------------------------------------------------
	*
	*	Remove an index of the keys in a collection. To set values to descending order,
	*	you must pass values of either -1, FALSE, 'desc', or 'DESC', else they will be
	*	set to 1 (ASC).
	*
	*	@usage : $this->mongo_db->remove_index($collection, array('first_name' => 'ASC', 'last_name' => -1));
	*/
	public function remove_index($collection = "", $keys = array())
	{
		if(empty($collection))
		{
			show_error("No Mongo collection specified to remove index from", 500);
		}
		if(empty($keys) || !is_array($keys))
		{
			show_error("Index could not be removed from MongoDB Collection because no keys were specified", 500);
		}
		if($this->mg_db->{$collection}->deleteIndex($keys, $options) == TRUE)
		{
			$this->clear();
			return($this);
		}
		else
		{
			show_error("An error occured when trying to remove an index from MongoDB Collection", 500);
		}
	}
	/**
	*	--------------------------------------------------------------------------------
	*	REMOVE_ALL_INDEXES
	*	--------------------------------------------------------------------------------
	*
	*	Remove all indexes from a collection.
	*
	*	@usage : $this->mongo_db->remove_all_index($collection);
	*/
	
	public function remove_all_indexes($collection = "")
	{
		if(empty($collection))
		{
			show_error("No Mongo collection specified to remove all indexes from", 500);
		}
		$this->mg_db->{$collection}->deleteIndexes();
		$this->clear();
		return($this);
	}
	/**
	*	--------------------------------------------------------------------------------
	*	SELECT FIELDS
	*	--------------------------------------------------------------------------------
	*
	*	Determine which fields to include OR which to exclude during the query process.
	*	Currently, including and excluding at the same time is not available, so the 
	*	$includes array will take precedence over the $excludes array.  If you want to 
	*	only choose fields to exclude, leave $includes an empty array().
	*
	*	@usage: $this->mongo_db->select(array('foo', 'bar'))->get('foobar');
	*/
	public function select($includes = array(), $excludes = array())
	{
		if(!is_array($includes))
		{
			$includes = array();
		}
		
		if(!is_array($excludes))
		{
			$excludes = array();
		}
		
		if(!empty($includes))
		{
			foreach($includes as $col)
			{
				$this->mg_selects[$col] = 1;
			}
		}
		
		if (!empty($excludes))
		{
			foreach($excludes as $col)
			{
				$this->mg_selects[$col] = 0;
			}
		}
		return($this);
	}
	/**
	*	--------------------------------------------------------------------------------
	*	SELECT SUBKEYS
	*	--------------------------------------------------------------------------------
	*
	*	Determine which subkeys to return for during gets and which subkeys to change during updates.
	*
	*	@usage: $this->mongo_db->select_subkeys(array('foo', 'bar'))->get('foobar');
	*/
	public function select_subkeys($subkeys = array())
	{	
		$this->mg_select_subkeys = $subkeys;
		return $this;
	}
	/**
	*	--------------------------------------------------------------------------------
	*	SUBARRAY_FILTER
	*	--------------------------------------------------------------------------------
	*	Apply subarray filter to returned value.
	*
	*	@usage : $this->mongodb_model->subarray_filter('foo.$.bar', 'matching value');
	*/	
	public function subarray_filter($key_specification, $matching_value)
	{
		if (empty($key_specification))
		{
			return $this;
		}
		
		$this->mg_output_filter[] = array(
			'filter_type'		=> 'subarray_filter',
			'key_specification'	=> $key_specification,
			'matching_value'	=> $matching_value
		);
		
		return $this;
	}
	/**
	*	--------------------------------------------------------------------------------
	*	Switch_db
	*	--------------------------------------------------------------------------------
	*
	*	Switch from default database to a different db
	*/	
	public function switch_db($database = '')
	{
		if(empty($database))
		{
			show_error("To switch MongoDB databases, a new database name must be specified", 500);
		}
		$this->mg_dbname = $database;
		try
		{
			$this->mg_db = $this->mg_connection->{$this->mg_dbname};
			return($this);
		}
		catch(Exception $e)
		{
			show_error("Unable to switch Mongo Databases: {$e->getMessage()}", 500);
		}
	}
	/**
	*	--------------------------------------------------------------------------------
	*	TRAVERSE_SUBARRAY
	*	--------------------------------------------------------------------------------
	*	Traverse the source array, locate matching keys and return the parent subarrays
	*
	*	@usage : $this->traverse_subarray($source, 'foo.$.bar', $cnt, 'matching value');
	*/
	private function traverse_subarray($source, $specification, $cnt, $matching_value)
	{	
		$return_array = FALSE;
		if (is_array($source))
		{
			foreach (array_keys($source) as $source_key)
			{
				foreach ($specification as $specification_key)
				{
					if (($source_key == $specification_key) || ($specification_key == '$'))
					{
						if (is_array($source[$source_key]) && (count($specification) > 1))
						{
							if ($sa = $this->traverse_subarray($source[$source_key], array_slice($specification, 1), $cnt, $matching_value))
							{
								if (count($specification) != ($cnt - 1))
								{
									$return_array[$source_key] = $sa;
								}
								else
								{
									$return_array[$source_key] = $source[$source_key];
								}
							}
						}
						elseif ($source[$source_key] == $matching_value)
						{
							$return_array[$source_key] = $matching_value;
						}
					}
				}
			}
		}		
		return $return_array;			
	}
	/**
	*	--------------------------------------------------------------------------------
	*	UPDATE
	*	--------------------------------------------------------------------------------
	*
	*	Updates a single document
	*
	*	@usage: $this->mongo_db->update('foo', $data = array());
	*
	*	TODO: implement subkeys for update methods
	*/	
	public function update($collection = "", $data = array(), $literal = FALSE, $upsert = FALSE, $subkey = 'set')
	{
		if(empty($collection))
		{
			show_error("No Mongo collection selected to update", 500);
		}
		if(count($data) == 0 || !is_array($data))
		{
			show_error("Nothing to update in Mongo collection or update is not an array", 500);
		}
		
		try
		{
			if ($subkey == "set")
			{
				$this->mg_db->{$collection}->update($this->mg_wheres, ($literal) ? $data : array('$set' => $data), array('w' => 1, 'multiple' =>  ($this->mg_limit == 1) ? FALSE : TRUE, 'upsert' => $upsert));
			}
			else 
			{
				$this->mg_db->{$collection}->update(array('type' => 'la'), array('$push' => array('items' => array('titel' => 'ir.', 'naam' => 'Van Bouwel', 'voornaam' => 'Philippe', 'actief' => 'ja' ))));
			}
			$this->clear();
			return(TRUE);
		}
		catch(MongoCursorException $e)
		{
			show_error("Update of data into MongoDB failed: {$e->getMessage()}", 500);
		}	
	}
	/**
	*	--------------------------------------------------------------------------------
	*	UPDATE_ALL
	*	--------------------------------------------------------------------------------
	*
	*	Updates a collection of documents
	*
	*	@usage: $this->mongo_db->update_all('foo', $data = array());
	*
	*	TODO: implement subkeys for update methods
	*/
	public function update_all($collection = "", $data = array(), $literal = FALSE)
	{
		if(empty($collection))
		{
			show_error("No Mongo collection selected to update", 500);
		}
		if(count($data) == 0 || !is_array($data))
		{
			show_error("Nothing to update in Mongo collection or update is not an array", 500);
		}
		try
		{
			$this->mg_db->{$collection}->update($this->mg_wheres, ($literal) ? $data : array('$set' => $data), array('w' => 1, 'multiple' => TRUE));
			$this->clear();
			return(TRUE);
		}
		catch(MongoCursorException $e)
		{
			show_error("Update of data into MongoDB failed: {$e->getMessage()}", 500);
		}
	}
	/**
	*	--------------------------------------------------------------------------------
	*	WHERE PARAMETERS
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents based on these search parameters.  The $wheres array should 
	*	be an associative array with the field as the key and the value as the search
	*	criteria.
	*
	*	@usage : $this->mongo_db->where(array('foo' => 'bar'))->get('foobar');
	*/
	public function where($wheres = array())
	{
		foreach($wheres as $wh => $val)
		{
			$this->mg_wheres[$wh] = $val;
		}
		return $this;
	}
	/**
	*	--------------------------------------------------------------------------------
	*	WHERE LESS THAN PARAMETERS
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents where the value of a $field is less than $x
	*
	*	@usage : $this->mongo_db->where_lt('foo', 20);
	*/
	
	public function where_lt($field = "", $x) {
		$this->where_init($field);
		$this->wheres[$field]['$lt'] = $x;
		return($this);
	}
	/**
	*	--------------------------------------------------------------------------------
	*	WHERE LESS THAN OR EQUAL TO PARAMETERS
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents where the value of a $field is less than or equal to $x
	*
	*	@usage : $this->mongo_db->where_lte('foo', 20);
	*/
	
	public function where_lte($field = "", $x) {
		$this->where_init($field);
		$this->wheres[$field]['$lte'] = $x;
		return($this);
	}
	/**
	*	--------------------------------------------------------------------------------
	*	WHERE BETWEEN PARAMETERS
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents where the value of a $field is between $x and $y
	*
	*	@usage : $this->mongo_db->where_between('foo', 20, 30);
	*/
	public function where_between($field = "", $x, $y)
	{
		$this->where_init($field);
		$this->mg_wheres[$field]['$gte'] = $x;
		$this->mg_wheres[$field]['$lte'] = $y;
		return($this);
	}
	/**
	*	--------------------------------------------------------------------------------
	*	WHERE BETWEEN AND NOT EQUAL TO PARAMETERS
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents where the value of a $field is between but not equal to $x and $y
	*
	*	@usage : $this->mongo_db->where_between_ne('foo', 20, 30);
	*/
	public function where_between_ne($field = "", $x, $y)
	{
		$this->where_init($field);
		$this->mg_wheres[$field]['$gt'] = $x;
		$this->mg_wheres[$field]['$lt'] = $y;
		return($this);
	}	
	/**
	*	--------------------------------------------------------------------------------
	*	WHERE GREATER THAN OR EQUAL TO PARAMETERS
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents where the value of a $field is greater than or equal to $x
	*
	*	@usage : $this->mongo_db->where_gte('foo', 20);
	*/	
	public function where_gte($field = "", $x)
	{
		$this->where_init($field);
		$this->mg_wheres[$field]['$gte'] = $x;
		return($this);
	}
	/**
	*	--------------------------------------------------------------------------------
	*	WHERE GREATER THAN PARAMETERS
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents where the value of a $field is greater than $x
	*
	*	@usage : $this->mongo_db->where_gt('foo', 20);
	*/
	public function where_gt($field = "", $x)
	{
		$this->where_init($field);
		$this->mg_wheres[$field]['$gt'] = $x;
		return($this);
	}
	/**
	*	--------------------------------------------------------------------------------
	*	WHERE_IN PARAMETERS
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents where the value of a $field is in a given $in array().
	*
	*	@usage : $this->mongo_db->where_in('foo', array('bar', 'zoo', 'blah'))->get('foobar');
	*/	
	public function where_in($field = "", $in = array())
	{
		$this->where_init($field);
		$this->mg_wheres[$field]['$in'] = $in;
		return($this);
	}
	/**
	*	--------------------------------------------------------------------------------
	*	WHERE_IN_ALL PARAMETERS
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents where the value of a $field is in all of a given $in array().
	*
	*	@usage : $this->mongo_db->where_in('foo', array('bar', 'zoo', 'blah'))->get('foobar');
	*/
	public function where_in_all($field = "", $in = array())
	{
		$this->where_init($field);
		$this->mg_wheres[$field]['$all'] = $in;
		return($this);
	}
	/**
	*	--------------------------------------------------------------------------------
	*	WHERE INITIALIZER
	*	--------------------------------------------------------------------------------
	*
	*	Prepares parameters for insertion in $wheres array().
	*/
	private function where_init($param)
	{
		if(!isset($this->mg_wheres[$param]))
		{
			$this->mg_wheres[$param] = array();
		}
	}
	/**
	*	--------------------------------------------------------------------------------
	*	WHERE NOT EQUAL TO PARAMETERS
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents where the value of a $field is not equal to $x
	*
	*	@usage : $this->mongo_db->where_not_equal('foo', 1)->get('foobar');
	*/
	public function where_ne($field = '', $x)
	{
		$this->where_init($field);
		$this->mg_wheres[$field]['$ne'] = $x;
		return($this);
	}
	/**
	*	--------------------------------------------------------------------------------
	*	WHERE NOT EQUAL TO PARAMETERS
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents nearest to an array of coordinates (your collection must have a geospatial index)
	*
	*	@usage : $this->mongo_db->where_near('foo', array('50','50'))->get('foobar');
	*/	
	function where_near($field = '', $co = array())
	{
		$this->_where_init($field);
		$this->mg_where[$what]['$near'] = $co;
		return($this);
	}	
	/**
	*	--------------------------------------------------------------------------------
	*	WHERE_NOT_IN PARAMETERS
	*	--------------------------------------------------------------------------------
	*
	*	Get the documents where the value of a $field is not in a given $in array().
	*
	*	@usage : $this->mongo_db->where_not_in('foo', array('bar', 'zoo', 'blah'))->get('foobar');
	*/	
	public function where_not_in($field = "", $in = array())
	{
		$this->where_init($field);
		$this->mg_wheres[$field]['$nin'] = $in;
		return($this);
	}
}

/* End of file mongodb_model.php */
/* Location: ./application/models/mongodb_model.php */
