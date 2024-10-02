<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');

/**
 * Global File 
 * Digunakan untuk semua controller yang global agar tidak menulis 2x 
 * dan dapat dipanggil dari html manapun
 */

class Cons extends Bismillah_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('v1/Bdb') ;
    }

    public function loadLv_get(){
        $va     = $this->get() ; 
        $q      = isset($va['q']) ? $va['q'] : ""; 
        
        $d      = array() ; 
        if($q !== "") {
            $this->db->like("id", $q);
            $this->db->or_like("name", $q);
        }else{
            $d[]= array("id"=>"0000", "text"=>"Administrator");
        }
        $db     = $this->db->select("id, name")
                            ->from("levels")
                            ->limit(5,0)
                            ->get() ; 
        foreach($db->result_array() as $r){
            $d[]    = array("id"=>$r['id'],"text"=>"(".$r['id'].") " . $r['name']) ; 
        }
        $vare = array("results"=>$d);

        $this->set_response($vare, Bismillah_Controller::HTTP_OK);     
    }
    
    public function gambar_upload_post($n){
        $this->fupload_post($n, '1');
    }

    public function gambar_delete_get($f){
        $this->fdelete_get($f);
    }

    public function fupload_post($n, $tipe=''){
        $type = 'pdf|zip|doc|docx|xls|xlsx|jpeg|jpg|png';
        if($tipe == '1'){
            $type = 'jpg|jpeg|png';
        }

        $va         = $this->post() ;
        $re         = array("success"=>true, "files"=>array() ,"message"=>array(), "data"=>array());
        $tmp        = $this->config->item("bcore_tmp");
        $fcfg	    = array("upload_path"=>$tmp, "allowed_types"=>$type, "overwrite"=>true) ;
        $this->load->library('upload', $fcfg) ; 
        foreach($_FILES as $key => $file){     
            if ( ! $this->upload->do_upload( $key ) ){
                $re["success"]      = false;
                $re["message"][]    = "Error file ke-". ($key+1) . " err " . $this->upload->display_errors();
            }else{
                $data           = $this->upload->data();
                $caption        = getname_file($data['raw_name']);
                $fl             = $tmp . $data['file_name'];
                $re["files"][]  = array('div'=>bfile_div($fl, $caption), 'del'=>true, 'dir'=>$fl, 'name'=>$data['file_name'], "file_ke"=>$n++);
            }  
        }

        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function fdelete_get($f){
        $va = $this->get() ;
        $va['myfile'] = (!is_array($va['myfile'])) ? json_decode($va['myfile'], true) : $va['myfile'];
        $lfound = false ;
        $file = '';
        if(isset($va['myfile'][$f])){
            if( strpos($va['myfile'][$f]['dir'], 'tmp') > -1 ){
                @unlink($va['myfile'][$f]['dir']);
            }else{
                $file = $va['myfile'][$f]['dir'];
            }
            $lfound = true;
        }
        $this->set_response(['delete'=>$lfound, 'file'=>$file], Bismillah_Controller::HTTP_OK);
    }

    public function loadkantor_get(){
        $this->auth();
        $va = $this->get() ; 
        $q = isset($va['q']) ? $va['q'] : "";
        $d = array();

        if($q !== "") $this->db->or_like( array('keterangan'=>$q) );
        $db = $this->db->select("kode, keterangan")
                            ->from("mst_kantor")
                            ->limit(5,0)
                            ->get() ; 
        foreach($db->result_array() as $r){
            $d[] = array("id"=>$r['kode'],"text"=>$r['keterangan']) ; 
        }
        $vare = array("results"=>$d);

        $this->set_response($vare, Bismillah_Controller::HTTP_OK);  

    }

    public function loaddati1_get(){
        $this->auth();
        $va = $this->get() ; 
        $q = isset($va['q']) ? $va['q'] : "";
        $d = array();

        if($q !== ""){
            $this->db->group_start();
            $this->db->or_like( array('kode'=>$q) );
            $this->db->group_end();
        }

        $db = $this->db->select("kode, keterangan")
                            ->from("dati_1")
                            ->limit(10,0)
                            ->get() ; 
        foreach($db->result_array() as $r){
            $d[] = array("id"=>$r['kode'], "text"=>$r['kode']." - ".$r['keterangan']) ; 
        } 
        $vare = array("results"=>$d);

        $this->set_response($vare, Bismillah_Controller::HTTP_OK);  

    }

    public function loadagama_get(){
        $this->auth();
        $va = $this->get() ; 
        $q = isset($va['q']) ? $va['q'] : "";
        $d = array();

        if($q !== "") $this->db->or_like( array('keterangan'=>$q) );
        $db = $this->db->select("kode, keterangan")
                            ->from("mst_agama")
                            ->limit(6,0)
                            ->get() ; 
        foreach($db->result_array() as $r){
            $d[] = array("id"=>$r['kode'],"text"=>$r['keterangan']) ; 
        }
        $vare = array("results"=>$d);

        $this->set_response($vare, Bismillah_Controller::HTTP_OK);  

    }

    public function loadjabatan_get(){
        $this->auth();
        $va = $this->get() ; 
        $q = isset($va['q']) ? $va['q'] : "";
        $d = array();

        if($q !== "") $this->db->or_like( array('keterangan'=>$q) );
        $db = $this->db->select("kode, keterangan")
                            ->from("mst_jabatan")
                            ->limit(10,0)
                            ->get() ; 
        foreach($db->result_array() as $r){
            $d[] = array("id"=>$r['kode'],"text"=>$r['keterangan']) ; 
        }
        $vare = array("results"=>$d);

        $this->set_response($vare, Bismillah_Controller::HTTP_OK);  

    }

    public function loadpendidikan_get(){
        $this->auth();
        $va = $this->get() ; 
        $q = isset($va['q']) ? $va['q'] : "";
        $d = array();

        if($q !== "") $this->db->or_like( array('keterangan'=>$q) );
        $db = $this->db->select("kode, keterangan")
                            ->from("mst_pendidikan")
                            ->limit(10,0)
                            ->get() ; 
        foreach($db->result_array() as $r){
            $d[] = array("id"=>$r['kode'],"text"=>$r['keterangan']) ; 
        }
        $vare = array("results"=>$d);

        $this->set_response($vare, Bismillah_Controller::HTTP_OK);  

    }

    public function loadjam_get(){
        $this->auth() ;
        $va = array() ;
        for($i=0;$i<=23;$i++){
            $jam = str_pad($i,2,"0",STR_PAD_LEFT) . ".00" ;
            $va[] = array("id"=>$i,"text"=>$jam) ;
        }
        $vare = array("results"=>$va) ; 
        $this->set_response($vare, Bismillah_Controller::HTTP_OK);  
    }

    public function loadabsensigol_get(){
        $this->auth();
        $va = $this->get() ; 
        $q = isset($va['q']) ? $va['q'] : "";
        $d = array();

        if($q !== "") $this->db->or_like( array('keterangan'=>$q) );
        $db = $this->db->select("kode, keterangan")
                            ->from("mst_abs_golongan")
                            ->limit(10,0)
                            ->get() ; 
        foreach($db->result_array() as $r){
            $d[] = array("id"=>$r['kode'],"text"=>$r['keterangan']) ; 
        }
        $vare = array("results"=>$d);

        $this->set_response($vare, Bismillah_Controller::HTTP_OK);  

    }

    public function loadgajigol_get(){
        $this->auth();
        $va = $this->get() ; 
        $q = isset($va['q']) ? $va['q'] : "";
        $d = array();

        if($q !== "") $this->db->or_like( array('keterangan'=>$q) );
        $db = $this->db->select("kode, keterangan") 
                            ->from("gj_golongan")
                            ->limit(10,0)
                            ->get() ; 
        foreach($db->result_array() as $r){
            $d[] = array("id"=>$r['kode'],"text"=>$r['keterangan']) ; 
        }
        $vare = array("results"=>$d);

        $this->set_response($vare, Bismillah_Controller::HTTP_OK);  

    }
}