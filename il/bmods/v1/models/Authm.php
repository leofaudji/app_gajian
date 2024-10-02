<?php defined('BASEPATH') OR exit('بسم الله الرحمن الرحيم');
class Authm extends Bismillah_Model{
    public function getUser($user){
        $this->db->where("username", $user) ; 
        $this->db->select("id, username, password, updated_password, fullname, lv, datas") ; 
        $db     = $this->db->get("users") ; 
        return $db->row_array() ; 
    }

    public function cekApiKey($key){
        $this->db->where(array("key"=>$key, "active"=>'1')) ;
        $this->db->select("id") ; 
        $db     = $this->db->get("api_keys") ; 
        $row    = $db->row() ; 
        return isset($row) ;
    }

    public function getLevel($key){
        $re = array('menus'=>'', 'dashboard'=>'');
        foreach(explode(',', $key) as $lv){
            $lv = trim($lv);
            $db = $this->db->select('menus, dashboard')->from("levels")
                            ->where("id", $lv)->get(); 
            $r  = $db->row_array();
            if(isset($r)){
                if($re['dashboard'] == '' && $r['dashboard'] !== NULL){
                    $re['dashboard'] = $r['dashboard'];
                }
                if($re['menus'] !== '') $re['menus'] .= ',';
                $re['menus'] .= $r['menus'];
            }
        }
        
        return $re;
    }

    public function updUsersToken($jwt){
        $this->db->set( $jwt ) ;
        $this->db->insert('users_tokens');
    }
    
    public function refresh($user, $jwt){
        /**
         * Check token and exp
         * If time > exp return true
         */
        //$where  = array("username"=>$user, "token"=> $jwt, "exp > "=>time() ) ;
        $where  = array("username"=>$user, "exp > "=>time() ) ;
        $this->db->where($where) ; 
        $this->db->select("id") ; 
        $db     = $this->db->get("users") ; 
        $row    = $db->row() ; 
        return isset($row) ;
    }

    public function logout($user, $jwt){
        $where  = array("username"=>$user, "token"=> $jwt, "exp > "=>time() ) ;
        $this->db->where($where) ; 
        $this->db->select("id") ; 
        $db     = $this->db->get("users") ; 
        $row    = $db->row() ; 
        if(isset($row)){
            $arr    = array("token"=>"", "exp"=>"") ; 
            $this->db->set($arr) ;
            $this->db->where('username', $user) ; 
            $this->db->update("users") ;  
        }
        return isset($row) ;
    }
}