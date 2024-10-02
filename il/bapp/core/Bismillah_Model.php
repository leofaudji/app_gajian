<?php (defined('BASEPATH')) OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
/**
 *  Bismillah Model
 * 
 * @copyright   CV. ILMION KREATIF - ILMION STUDIO
 * @link        https://ilmion.com
 * @mail        hi@ilmion.com | ilmionstudio@gmail.com
 * @author      Mirza Ramadhany (amir.ramadhany@gmail.com)
 * @version     0.0.1
 * @since       17 Aug 18
 */

class Bismillah_Model extends CI_Model{
	/**
	 * Constructor Databas
	 * 
     * @access      public
     */
	public function __construct(){
		$this->load->database() ;
	}

	/**
     * getSQL
     * 
	 * @access		public
     * @return      string sql
     */
	public function getSql(){
		return $this->db->last_query() ;
	}

	/**
     * Save Config to configs table
     * 
	 * @access		public
	 * @param 		string $key
	 * @param 		string $val Value
     * @return      boolean
     */
	public function saveConfig($key, $val){
		$arr 	= array('title' => $key, 'val'=>$val) ;
		$this->upsert('configs', $arr, array("title"=>$key)) ;
	}

	/**
     * Get Config from configs table
     * 
	 * @access		public
	 * @param 		string $key
	 * @param 		string $val Default Value
     * @return      string,array $val
     */
	public function getConfig($key, $val=''){
		$this->db->where('title', $key) ; 
        $this->db->select("val") ; 
        $db     = $this->db->get("configs") ; 
        if($row = $db->row_array()){
			$val= $row['val'] ; 
		}

		return $val ; 
	}

	/**
	 * Update / Insert Data Check Data first
	 * If there is data do update query
	 * If there is no data do insert query
	 * 
	 * @param		string $table
	 * @param		array $value
	 * @param		array $where
	 * @param		string $id primary or uniqe key of table target
	 * @author      Denny's Alfian (alfiandennys33@gmai.com)
	 */
	public function upsert($table, $value, $where, $id='id'){
		$query	= $this->db->select($id)->from($table)
					->where($where)->get();
		$row	= $query->row_array();
		if(isset($row)){
			//update query
			$this->db->where($where);
			$this->db->update($table, $value);
		}else{
			//insert query
			$this->db->insert($table, $value);
		}
	}

	/** 
	 * To get Quick Simple Select Query Result
	 * @param		string $field field for result
	 * @param		string $table
	 * @param		array $where array optional default id=$id
	 * @author      Denny's Alfian (alfiandennys33@gmai.com)
	 * @author 		Mirza Ramadhany updated at Sept 13 '18
	 */

	public function getOne($field, $table, $where){
		$count	= $field == "*" ? 2 : count(explode(",",$field)) ;

		/**
		 * @var 	string $result if there is one selected field 
		 * @var 	array $result if there is more then one selected field
		 */
		$result	= ($count==1) ? "" : array() ;

		$this->db->reset_query();
		$query	= $this->db->select($field)->from($table)->where($where)->limit(1,0)->get() ;
		$r   	= $query->row_array() ;
		if(isset($r)){
			$result = ($count==1) ? $r[$field] : $r ;
		}
		return $result ;
	}

	/** 
	 * To get Last Increment number from Config (Copy from Kopkar) HAHAHAHAA
	 * @param		string $key increament key
	 * @param		string $update update to config default true
	 * @param		int $length lengt padl default 0
	 */
	public function getIncrement($key,$update=true,$lenght=0){
		$inc 	= 1 ;
		$key	= "inc_" . $key ;
		$val 	= intval($this->getConfig($key)) ;
		$inc 	= ($val > 0) ? $val+1 : $inc ;
		if($update){
			$this->saveConfig($key, $inc) ;
		}
		return str_pad($inc, $lenght, "0", STR_PAD_LEFT) ;
	}
}
?>
