<?php

class DB{
	private static $_instance=null;

	private $_pdo,
	 		$_query,
	 		$_error=false,
	 		$_result,
	 		$_count=0;
	private function __construct()
	{
		try{
			$this->_pdo= new pdo('mysql:host='.Config::get('mysql/host').';dbname='.Config::get('mysql/db'),Config::get('mysql/username'),Config::get('mysql/password'));
			
		}catch(PDOException $e){
			die($e->getMessage());

		}
	}
	public static function getInstance()
	{
		if(!isset(self::$_instance))
			{
				self::$_instance=new DB();
			}
		return self::$_instance;
	}

	public function query($sql, $params =array())
	{
		$this->_error=false;
		if($this->_query = $this->_pdo->prepare($sql))
		{
			$x=1;
			if(count($params))
			{
				foreach ($params as $param) {
					$this->_query->bindValue($x,$param);
					$x++;
					
				}
			}
			//echo $this->_query;
			if($this->_query->execute())
			{
				$this->_result=$this->_query->fetchAll(PDO::FETCH_OBJ);
				$this->_count=$this->_query->rowCount();
			}else 
			{
				$this->_error=true;
			}
		}
		return $this;
	}

	private function action($action ,$table, $where=array())
	{
		if(count($where)===3)
		{
			$operators=array('=','<','>','<=','>=');
			$field    =$where[0];
			$operator =$where[1];
			$value    =$where[2];
			if(in_array($operator, $operators))
			{
				$sql="{$action} from {$table} where {$field} {$operator} ?";

				if(!$this->query($sql,array($value))->error())
				{
	
					return $this;
				}
			} 
		}
		return false;
	}

	public function get($table, $where)
	{
		return $this->action('SELECT *', $table, $where);
	}

	public function delete($table, $where)
	{
		return $this->action('DELETE', $table, $where);
	}
	public function insert($table, $fields=array())
	{
		 if(count($fields))
		 {
		 	$keys=array_keys($fields);
		 	$values ='';
		 	$x=1;

		 	foreach ($fields as $field) {
		 		$values .='?';
		 		if($x <count($fields))
		 		{
		 			$values .=',';
		 		}
		 		$x++;
		 	}
		 	//die($values);

		 	$sql ="INSERT INTO users (`" .implode("`,`", $keys). "`) VALUES({$values})";
		 	
		 	if(!$this->query($sql,$fields)->error())
		 	{
		 		return true;
		 	}
		 	else 
		 		return false;
		 }
	}
	public function error()
	{
		return $this->_error;
	}
	public function result()
	{
		return $this->_result;
	}
	public function first()
	{
		return $this->result()[0];
	}
	public function count()
	{
		return $this->_count;
	}
}

?>