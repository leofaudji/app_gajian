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

    public function lab_c_get(){
        $re = array('data'=>array(), 'categories'=>array());
        $red = array();
        $tgl_e = date('Y-m-d');
        $tgl_a = date('Y-m-d', strtotime($tgl_e) - (86400 * 7));
        

        $reda = array();
        foreach($red as $tipe => $vred){
            $reda[] = $vred;
        }

        $re['data'] = $reda;
        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function lab_a_get(){
        $re = array();

        $tgl = date('Y-m-d');
        $bom = date_bom($tgl);
        $eom = date_eom($tgl);

        $lab = array('n'=>0, 'b'=>0, 'h'=>0, 'a'=>0);
        
        $re['lab'] = $lab;
        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function lab_a_c_get(){
        $re = array('data'=>array(), 'categories'=>array());
        $red = array();
        $rec = array();
        $tgl_e = date('Y').'-12-31';
        $tgl_a = date('Y').'-01-01';
        
        $reda = array();
        foreach($red as $vred){
            unset($vred['datas']);
            $reda[] = $vred;
        }

        $re['data'] = $reda;
        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function kasir_w(){
        if(!$this->lapp){
            // $this->db->where('cabang', $this->aruser['datas']['cabang']);
        } 
    }

    public function pj_get($periode=''){
        // sleep(15);
        $re = array('nom'=>0,'fkt'=>0);
        if(empty($periode))$periode = 'h';
        
        $tgl = date("Y-m-d");
        $thn = 2022;//date("Y");
        $bln = 4;//date("m");

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
        $this->db->where('status',"1");

        $db = $this->Bdb->db->select("count(id) fkt,ifnull(sum(total),0) nom")
                            ->from("pj")
                            ->get();
        $r  = $db->row_array();
        $re['fkt'] = floatval($r['fkt']);
        $re['nom'] = floatval($r['nom']);
        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }


    public function pb_get($periode=''){
        $re = array('nom'=>0,'fkt'=>0);
        if(empty($periode))$periode = 'h';
        
        $tgl = date("Y-m-d");
        $thn = 2022;//date("Y");
        $bln = 4;//date("m");

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
        $this->db->where('status',"1");

        $db = $this->Bdb->db->select("count(id) fkt,ifnull(sum(total),0) nom")
                            ->from("pb")
                            ->get();
        $r  = $db->row_array();
        $re['fkt'] = floatval($r['fkt']);
        $re['nom'] = floatval($r['nom']);
        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function pj_r_get($periode=''){
        $re = array('nom'=>0,'fkt'=>0);
        if(empty($periode))$periode = 'h';
        
        $tgl = date("Y-m-d");
        $thn = 2022;//date("Y");
        $bln = 4;//date("m");

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
        $this->db->where('status',"1");

        $db = $this->Bdb->db->select("count(id) fkt,ifnull(sum(total),0) nom")
                            ->from("pj_rtr")
                            ->get();
        $r  = $db->row_array();
        $re['fkt'] = floatval($r['fkt']);
        $re['nom'] = floatval($r['nom']);
        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function pb_r_get($periode=''){
        $re = array('nom'=>0,'fkt'=>0);
        if(empty($periode))$periode = 'h';
        
        $tgl = date("Y-m-d");
        $thn = 2022;//date("Y");
        $bln = 4;//date("m");

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
        $this->db->where('status',"1");

        $db = $this->Bdb->db->select("count(id) fkt,ifnull(sum(total),0) nom")
                            ->from("pb_trt")
                            ->get();
        $r  = $db->row_array();
        $re['fkt'] = floatval($r['fkt']);
        $re['nom'] = floatval($r['nom']);
        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function pp_get($periode=''){
        $re = array('nom'=>0,'fkt'=>0);
        if(empty($periode))$periode = 'h';
        
        $tgl = date("Y-m-d");
        $thn = 2022;//date("Y");
        $bln = 4;//date("m");

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
        $this->db->where('status',"1");

        $db = $this->Bdb->db->select("count(id) fkt,ifnull(sum(total),0) nom")
                            ->from("pj_piut_pelunasan")
                            ->get();
        $r  = $db->row_array();
        $re['fkt'] = floatval($r['fkt']);
        $re['nom'] = floatval($r['nom']);
        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function ph_get($periode=''){
        $re = array('nom'=>0,'fkt'=>0);
        if(empty($periode))$periode = 'h';
        
        $tgl = date("Y-m-d");
        $thn = 2022;//date("Y");
        $bln = 4;//date("m");

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
        $this->db->where('status',"1");

        $db = $this->Bdb->db->select("count(id) fkt,ifnull(sum(total),0) nom")
                            ->from("pb_htg_pelunasan")
                            ->get();
        $r  = $db->row_array();
        $re['fkt'] = floatval($r['fkt']);
        $re['nom'] = floatval($r['nom']);
        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function plgn_get(){
        $re = array();
        
        $thn = 2022;//date("Y");
        $bln = 4;//date("m");


        $tglawal = date("Y-m-d",mktime(0,0,0,$bln,1,$thn));
        $tglakhir = date("Y-m-d",mktime(0,0,0,$bln+1,0,$thn));
        $this->db->where('status',"1");
        $db = $this->Bdb->db->select("count(id) jml")
                            ->from("pelanggan")
                            ->get();
        $r  = $db->row_array();
        $re['jml'] = floatval($r['jml']);

        $this->db->where('tgl >=',$tglawal);
        $this->db->where('tgl <=',$tglakhir);
        $this->db->where('status',"1");
        $this->db->where('kd_pelanggan is not null');
        $db = $this->Bdb->db->select("id")
                            ->from("pj")
                            ->group_by("kd_pelanggan")
                            ->get();
        $pj  = $db->num_rows();

        $re['pj'] = floatval($pj);

        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function so_get(){
        $re = array();
        
        $thn = 2022;//date("Y");
        $bln = 4;//date("m");


        $tglawal = date("Y-m-d",mktime(0,0,0,$bln,1,$thn));
        $tglakhir = date("Y-m-d",mktime(0,0,0,$bln+1,0,$thn));

        $this->db->where('tgl >=',$tglawal);
        $this->db->where('tgl <=',$tglakhir);
        $this->db->where('status',"1");
        $db = $this->Bdb->db->select("count(id) jml")
                            ->from("pj_so")
                            ->get();
        $r  = $db->row_array();
        $re['jml'] = floatval($r['jml']);

        $this->db->where('t.tgl >=',$tglawal);
        $this->db->where('t.tgl <=',$tglakhir);
        $this->db->where('t.status',"1");
        $db = $this->Bdb->db->select("b.id")
                            ->from("pj_so_barang b")
                            ->join("pj_so t","t.faktur = b.faktur")
                            ->group_by("b.kd_barang")
                            ->get();
        $sob  = $db->num_rows();

        $re['brg'] = floatval($sob);

        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function pjkrm_get(){
        $re = array();
        
        $thn = 2022;//date("Y");
        $bln = 4;//date("m");


        $tglawal = date("Y-m-d",mktime(0,0,0,$bln,1,$thn));
        $tglakhir = date("Y-m-d",mktime(0,0,0,$bln+1,0,$thn));

        $this->db->where('tgl >=',$tglawal);
        $this->db->where('tgl <=',$tglakhir);
        $this->db->where('status',"1");
        $db = $this->Bdb->db->select("count(id) jml")
                            ->from("pj_kirim")
                            ->get();
        $r  = $db->row_array();
        $re['krm'] = floatval($r['jml']);

        $this->db->where('tgl >=',$tglawal);
        $this->db->where('tgl <=',$tglakhir);
        $this->db->where('status',"1");
        $this->db->where('status_faktur',"2");
        $db = $this->Bdb->db->select("count(id) jml")
                            ->from("pj_kirim")
                            ->get();
        $r  = $db->row_array();

        $re['tkrm'] = floatval($r['jml']);

        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function brg_get(){
        $re = array();
        
        $this->db->where('status',"1");
        $db = $this->Bdb->db->select("count(id) jml")
                            ->from("stk_barang")
                            ->get();
        $r  = $db->row_array();
        $re['brg'] = floatval($r['jml']);

        $this->db->where('status',"1");
        $this->db->where('JSON_VALUE(saldo,"$.saldo")',0);
        $db = $this->Bdb->db->select("count(id) jml")
                            ->from("stk_barang")
                            ->get();
        $r  = $db->row_array();

        $re['brg_0'] = floatval($r['jml']);

        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    private function ketperiode($periode){
        $return = array("h"=>"Hari Ini","b"=>"Bulan Ini","t"=>"Tahun Ini");
        return $return[$periode];
    }
}