<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
class D extends Bismillah_Controller{
    public function __construct(){
        parent::__construct();
        $this->auth('v1/dash/d'); 

        $this->load->model('v1/Bdb'); 

        $this->lapp = false;
        if( $this->aruser['lv'] == '0000' ){
            $this->lapp = true;
        }
    }

    public function index_get(){
        $re = array();

        $tgl = date('Y-m-d');
        $bom = date_bom($tgl);
        $eom = date_eom($tgl);

        $re['lab'] = "";
        $re['cab_alamat'] = 'Semua Lokasi';
        if(!$this->lapp){
            // $re['cab_alamat'] = $this->Bdb->getOne('alamat', 'mst_cabang', array('id'=>$this->aruser['datas']['cabang']));
        }
        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function periode_get($periode=''){   
        // sleep(15);
        $re = array();
        if(empty($periode))$periode = 'h';
        
        $this->db->where('status',"1"); 
        $db = $this->Bdb->db->select("id,kode,keterangan")
                            ->from("gj_periode")
                            ->get();
        $r  = $db->row_array();   
        $re = array('kode'=>$r['kode'],'keterangan'=>$r['keterangan']) ;
        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }


    public function kry_get($periode=''){
        $re = array('nom'=>0,'fkt'=>0);
        if(empty($periode))$periode = 'h';
        
        $tgl = date("Y-m-d"); 
        $thn = date("Y");
        $bln = date("m");

        $tglawal = $tgl;
        $tglakhir = $tgl;

        if($periode == 'b'){
            $tglawal = date("Y-m-d",mktime(0,0,0,$bln,1,$thn));
            $tglakhir = date("Y-m-d",mktime(0,0,0,$bln+1,0,$thn));
        }else if($periode == 't'){
            $tglawal = date("Y-m-d",mktime(0,0,0,1,1,$thn));
            $tglakhir = date("Y-m-d",mktime(0,0,0,12,31,$thn));
        }

        $re['periode'] = $this->ketperiode($periode);

        //$this->db->where('tgl >=',$tglawal);   
        $this->db->where('tgl_keluar <=',$tglakhir);

        $db = $this->Bdb->db->select("count(id) fkt,id nom")
                            ->from("mst_karyawan") 
                            ->get();
        $r  = $db->row_array();
        $re['fkt'] = floatval($r['fkt']);
        $re['nom'] = floatval($r['nom']);
        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function abs_get($periode=''){
        $re = array('nom'=>0,'fkt'=>0);
        if(empty($periode))$periode = 'h';
        
        $tgl = date("Y-m-d");
        $thn = date("Y");
        $bln = date("m");

        $tglawal = $tgl;
        $tglakhir = $tgl;

        if($periode == 'b'){
            $tglawal = date("Y-m-d",mktime(0,0,0,$bln,1,$thn));
            $tglakhir = date("Y-m-d",mktime(0,0,0,$bln+1,0,$thn));
        }else if($periode == 't'){
            $tglawal = date("Y-m-d",mktime(0,0,0,1,1,$thn));
            $tglakhir = date("Y-m-d",mktime(0,0,0,12,31,$thn));
        }

        $re['periode'] = $this->ketperiode($periode);

        $this->db->where('tgl >=',$tglawal);
        $this->db->where('tgl <=',$tglakhir); 
        $this->db->where('abs_status<>','001'); 

        //echo $tglakhir ;
        
        $db = $this->Bdb->db->select("count(id) fkt,id nom")
                            ->from("abs_tmp")    
                            ->get(); 
        $r  = $db->row_array();
        $re['fkt'] = floatval($r['fkt']);
        $re['nom'] = floatval($r['nom']); 
        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    private function ketperiode($periode){
        $return = array("h"=>"Hari Ini","b"=>"Bulan Ini","t"=>"Tahun Ini");
        return $return[$periode];
    }
}