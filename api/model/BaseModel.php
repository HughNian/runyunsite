<?php
defined('APP_PATH') or die('No direct access allowed.');

/**
 * 数据库操作基类
 * 
 * @author niansong
 *
 */
class BaseModel
{
	//表名
	protected $table;
	
	//逻辑删除字段,删除状态,例如:Y-删除,N-正常,null表示无此字段
	protected $disabled = null;
	
	//逻辑删除字段名称,默认字段名为disabled
	protected $disabledName = 'disabled';
	
	//sql语句
	protected $query = "";
	
	//插入表数据的栏目
	private $columns = ""; 
	
	//插入表数据的值
	private $values  = ""; 
	
	/**
	 * @todo 查询某个表
	 * 
	 */
	public function select($column = "", $table = "")
	{
		if(!$column)
			$column = '*';
		if(!$table)
			$table = $this->table;
		
		$this->query = "SELECT $column FROM $table";
		return $this;
	}
	
	/**
	 * @todo where条件
	 *
	 */
	public function where($where = null)
	{
		if(!$where)
			$where = 1;
		
		if(!is_null($this->disabled))
		{
			$this->query .= " WHERE $where AND $this->disabledName = '$this->disabled'";
		}
		else
		{
			$this->query .= " WHERE $where";
		}
		
		return $this;
	}
	
	/**
	 * @todo left左连接
	 * 
	 * 
	 */
    public function left($table, $acolumn, $bcolumn)
    {
    	$this->query = $this->query . " LEFT JOIN $table ON $this->table.$acolumn = $table.$bcolumn";
    	return $this;
    }
	
    /**
     * @todo 获取记录集count
     *
     */
    public function count($where = null, $alias = 'count')
    {
    	$count = $this->select("COUNT(*) AS $alias")->where($where)->find();
    	return $count["$alias"];
    }
    
    /**
     * @todo 获取记录集和
     * 
     * 
     */
    public function sum($column)
    {
    	$this->query = "SELECT SUM($column) AS $column FROM $this->table";
    	
    	if(!strrpos($this->query, "WHERE"))
			$this->where();
    	
    	return $this;
    }
    
	/**
	 * @todo limit条件
	 * 
	 * 
	 */
	public function limit($offset, $limit)
	{
		if(!strrpos($this->query, "WHERE"))
			$this->where();
		
		$this->query = $this->query . " LIMIT $offset, $limit";
		return $this;
	}
	
	/**
	 * @todo order by条件
	 * 
	 * 
	 */
	public function order_by($column, $order = "DESC")
	{
		if(!strrpos($this->query, "WHERE"))
			$this->where();
		
		$this->query = $this->query . " ORDER BY $column $order";
		return $this;
	}
	
	/**
	 * @todo group by条件
	 * 
	 * 
	 */
	public function group_by($column)
	{
		if(!strrpos($this->query, "WHERE"))
			$this->where();
		
		$this->query = $this->query . " GROUP BY $column";
		return $this;
	}
	
	/**
	 * @todo 获取单行结果(数组)
	 * 
	 * 自动过滤已删除数据
	 * 
	 */
	public function find()
	{
		if(!strrpos($this->query, "WHERE"))
			$this->where();
		
		$result = mysql_query($this->query);
		$row = mysql_fetch_assoc($result);
		
		return $row;
	}
	
	/**
	 * @todo 获取单行结果(对象)
	 *
	 * 自动过滤已删除数据
	 *
	 */
	public function get()
	{
		if(!strrpos($this->query, "WHERE"))
			$this->where();
		
		$result = mysql_query($this->query);
		$row = mysql_fetch_object($result);
		return $row;
	}
	
	/**
	 * @todo 获取多行结果集(数组)
	 * 
	 * 自动过滤已删除数据
	 * 
	 * 
	 */
	public function find_all()
	{
		if(!strrpos($this->query, "WHERE"))
			$this->where();
		
		$result = mysql_query($this->query);
		$data = array();
		while($rows = mysql_fetch_assoc($result)){
			$data[] = $rows;
		}
		return $data;
	}
	
	/**
	 * @todo 获取多行结果集(对象)
	 * 
	 * 自动过滤已删除数据
	 * 
	 */
	public function find_object()
	{
		if(!strrpos($this->query, "WHERE"))
			$this->where();
		
		$result = mysql_query($this->query);
		$data = array();
		while($rows = mysql_fetch_object($result)){
			$data[] = $rows;
		}
		return $data;
	}
	
	/**
	 * @todo 插入记录的数据,配合insert()函数使用
	 * 
	 */
	public function data($data = array())
	{
		if(!$data)
			return $this;
		
		$columns = array_keys($data);
		$values   = array_values($data);
		foreach($values as $value){
			$this->values .= "'$value', ";
		}
		
		$this->columns = implode(",", $columns);
		$this->values = substr($this->values, 0, -2);
		
		return $this;
	}
	
	/**
	 * @todo 插入记录
	 * 
	 * @return 新插入id
	 * 
	 */
	public function insert()
	{
		if(!$this->columns || !$this->values)
			return false;
		else{
			$query = "INSERT INTO $this->table ( $this->columns ) VALUES ( $this->values )";
			$ret = mysql_query($query);
			if($ret)
				return $this->get_last_id();
			else
				return false;
		}
	}
	
	/**
	 * @todo 更新数据
	 * 
	 * @param $data 更新的数据
	 * 
	 */
	public function update($data = array())
	{
		if(!$data) return;
		
		$where = 1;
		if($this->query && strrpos($this->query, "WHERE")){
			$where = $this->query;
		}
		
		$datas = "";
		foreach($data as $key => $val){
			$datas .= "$key = '$val', ";
		}
		$datas = substr($datas, 0, -2);
		$this->query = "UPDATE $this->table SET $datas $where";
		return mysql_query($this->query);
	}
	
	/**
	 * @todo 删除记录(物理删除)
	 * 
	 */
	public function delete()
	{
		$where = 1;
		if($this->query && strrpos($this->query, "WHERE")){
			$where = $this->query;
		}
		$this->query = "DELETE FROM $this->table $where";
		return mysql_query($this->query);
	}
	
	/**
	 * @todo 获取最后一个记录的id
	 *
	 */
	public function get_last_id()
	{
		$id = $this->select('id')
		           ->order_by('id')
		           ->get();
	
		return $id->id;
	}
	
	/**
	 * @todo 打印/获取sql语句,$tag=true返回sql语句,$tag=false打印sql语句
	 * 
	 * 
	 */
	public function sql($tag = false)
	{
		if($tag)
			return $this->query;
		else
			exit($this->query);
	}
}