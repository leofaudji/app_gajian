<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
class Usr_lv extends Bismillah_Controller{
    public function __construct(){
        parent::__construct();
        $this->auth('v1/cfg/usr_lv') ; 

        $this->load->model('v1/Bdb') ; 
    }

    public function grid_get(){
        $va     = $this->get() ; 
        
        $re     = array("data"=>array()) ;

        $db     = $this->Bdb->db->select("id, name")->from("levels")->get() ; 
        foreach($db->result_array() as $r){
            $cmde   = '<div class="btn-group pull-right">
                        <button type="button" onClick="bo.usr_lv.cmdEdit(\''.$r['id'].'\')"
                        class="btn btn-default btn-sm"><i class="pt-1 pl-2 fa fa-edit"></i></button>
                        <button type="button" onClick="bo.usr_lv.cmdDelete(\''.$r['id'].'\')"
                        class="btn btn-danger btn-sm"><i class="pt-1 pl-2 fa fa-ban"></i></button>
                        </div>';
            $d      = array("(".$r['id'].") ". $r['name'], $cmde) ;

            //append
            $re['data'][]   = $d ;  
        }

        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    } 

    
    public function delete_get($id){
        $this->Bdb->db->where("id", $id) ;
        $this->Bdb->db->delete("levels") ;
        
        $this->set_response(['deleted' => true], Bismillah_Controller::HTTP_OK);
    }

    public function edit_get($id){
        $re     = array('id'=>$id, 'name'=>'', 'menus'=>'');
        $this->db->where("id", $id) ; 
        $this->db->select("id, name, menus") ; 
        $db     = $this->db->get("levels") ; 
        if($r   = $db->row_array()){
            $r['edit']  = true;
            $re = $r;
        }
        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    } 
 
    public function save_post($id){
        $va     = $this->post() ;  
        $va['id']   = $id ; 
        $this->Bdb->upsert('levels', $va, array("id"=>$va['id'])) ;

        // response
        $this->set_response(['saved' => true], Bismillah_Controller::HTTP_OK);
    } 

} 