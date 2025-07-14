<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
class Aidas extends Bismillah_Controller{
    public function __construct(){
        parent::__construct();  
        $this->auth();
        $this->load->model('v1/Bdb') ; 
        $this->load->model('v1/Telegram') ; 
    }

    
 
    public function index_get($kode){         
        // koreksi
        $r = $this->Bdb->getOne('*', 'mst_abs_golongan', array('kode'=>$kode));
        if(!empty($r)){ 
            $this->set_response($r, Bismillah_Controller::HTTP_OK);
        }
    }


    public function index_post($kode){
        // saving
        $va = $this->post();
        
        //print_r($va['gr1']) ;  
        $username = $this->aruser['username'] ;

        // khusus combobox di dalam table
        
        foreach($va['gr1'] as $key=>$value){       
            $mtd    = $this->Bdb->getOne('kode', 'mst_abs_metode', array('keterangan'=>$value['metode'])) ;
            $metode = $value['w2ui']['changes']['metode']['id'] ?? $mtd ;        
            $va2 = array("kode_kantor"=>$value['kode'],"metode"=>$metode,"username"=>$username) ;   
            $this->Bdb->upsert('mst_abs_cfg', $va2, array("kode_kantor"=>$value['kode'])) ;     
        }

        // response
        $this->set_response(['saved' => true], Bismillah_Controller::HTTP_OK); 
    }

    public function initdata_get(){
      // grid
      $re = array() ;
      $db = $this->Bdb->db->select("*")
                          ->from("mst_abs_status")
                          ->order_by('id ASC')
                          ->get();  
      $i = 0 ;
      foreach($db->result_array() as $r){
          //append
          //$re['recid'] = $i++ ;
          $re[] = array("id"=>$r['kode'],"text"=>$r['keterangan']) ;
      }
      $this->set_response($re, Bismillah_Controller::HTTP_OK);
  }
}