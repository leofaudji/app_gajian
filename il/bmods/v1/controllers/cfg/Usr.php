<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
class Usr extends Bismillah_Controller{
    public function __construct(){
        parent::__construct();
        $this->auth() ;
        $this->load->model("v1/Bdb");
    }

    public function grid_get(){
        $va = $this->get();
            
        $re     = array("data"=>array()) ;
        $db     = $this->Bdb->db->select("id, username, fullname, lv")->from("users")->get(); 
        foreach($db->result_array() as $r){
            $cmde   = '<div class="btn-group pull-right">
                        <button type="button" class="btn btn-primary btn-sm" onClick="bo.usr.cmdEdit(\''.$r['id'].'\')">Edit</button>
                        </div>' ; 
            $lv = $this->Bdb->getOne('name', 'levels', array('id'=>$r['lv']));
            $d   = array("<mark>".$r['username']."</mark> ". $r['fullname'], $lv, $cmde) ;

            //append
            $re['data'][]   = $d ;  
        } 
    

        $this->set_response($re, Bismillah_Controller::HTTP_OK) ;
    }

    public function edit_get($id){
        $this->db->where("id", $id) ; 
        $this->db->select("id, username, fullname, datas, lv") ; 
        $db     = $this->db->get("users") ; 
        if($r   = $db->row_array()){
            $lv         = $r['lv'];
            $lv = array();
            foreach(explode(',', $r['lv']) as $mlv){
                $lv[]   = array("id"=>$mlv, "text"=>"(".$mlv.") " . $this->Bdb->getOne("name", "levels", array("id"=>$mlv)));
            }
            $r['lv']    = $lv;
            $this->set_response($r, Bismillah_Controller::HTTP_OK);
        } 
    } 

    public function save_post($id){
        $va = $this->post() ;
        $re = array() ;

        if($va['password'] !== ""){
            $va['password'] = pass_hash($va['password']);
        }else{
            unset($va['password']);
        }
        
        $va['id']   = $id ; 
        $this->Bdb->upsert('users', $va, array("id"=>$va['id'])) ;

        $this->set_response($re, Bismillah_Controller::HTTP_OK) ;
    }

    public function loadLv_get(){
        $va     = $this->get() ; 
        $q      = isset($va['q']) ? $va['q'] : ""; 
        
        $d      = array() ; 
        if($q !== "") {
            $this->Bdb->db->like("id", $q);
            $this->Bdb->db->or_like("name", $q);
        }else{
            $d[]= array("id"=>"0000", "text"=>"Administrator");
        }
        $db     = $this->Bdb->db->select("id, name")
                            ->from("levels")
                            ->limit(5,0)
                            ->get() ; 
        foreach($db->result_array() as $r){
            $d[]    = array("id"=>$r['id'],"text"=>"(".$r['id'].") " . $r['name']) ; 
        }
        $vare = array("results"=>$d);

        $this->set_response($vare, Bismillah_Controller::HTTP_OK);     
    }
}