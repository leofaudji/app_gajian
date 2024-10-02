<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
class Mdati2 extends Bismillah_Controller{
    public function __construct(){
        parent::__construct();
        $this->auth();
        $this->load->model('v1/Bdb') ; 
    }

    public function gr1_where($bs, $s){
        if(isset($bs['skd_dati_1'])){
            $this->db->where('kd_dati_1', $bs['skd_dati_1']);
        }
        if($s !== ''){
            $this->db->group_start();
                $this->db->or_like(array('keterangan'=>$s));
            $this->db->group_end();
        }
    }

    public function gr1_post(){
        // grid
        $va = json_decode($this->post()['request'], true);
        $re = array('total'=>0, 'records'=>array());

        $bs = isset($va['bsearch']) ? $va['bsearch'] : array();
        $s = isset($va['search']) ? $va['search'][0]['value'] : '';
        $this->gr1_where($bs, $s);
        $db = $this->Bdb->db->select("count(id) jml")
                            ->from("dati_2")
                            ->get();
        $r  = $db->row_array();
        if(isset($r)){
            $r['jml']   = intval($r['jml']);
            if($r['jml'] > 0) {
                $re['total'] = $r['jml'];
                $this->gr1_where($bs, $s);
                $db = $this->Bdb->db->select("d2.*, d1.keterangan as dati_1")
                                    ->from("dati_2 d2")
                                    ->join("dati_1 d1","d1.kode = d2.kd_dati_1","left")
                                    ->limit($va['limit'], $va['offset'])
                                    ->order_by('d2.id DESC')
                                    ->get(); 
                foreach($db->result_array() as $r){
                    $r['recid'] = $r['id'];
                    $r['edit'] = ' 
                                <div class="btn-group w-100">
                                    <button type="button" class="btn btn-default btn-w2gr" onClick=bo.mdati2.edit("'.$r['kode'].'")><i class="fa fa-pencil-square-o me-1"></i>&nbsp;Koreksi</button>
                                </div>';
                                // <button type="button" onClick=bo.mdati2.delete("'.$r['kode'].'") class="btn btn-danger btn-w2gr"><i class="fa fa-trash me-1"></i>Hapus</button>
                    $re['records'][]   = $r ;  
                }
            }
        }

        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function index_get($kode){
        // koreksi
        $r = $this->Bdb->getOne('*', 'dati_2', array('kode'=>$kode));
        if(!empty($r)){
            $r['kd_dati_1'] = array( array('id'=>$r['kd_dati_1'], 'text'=>$this->Bdb->getOne('keterangan', 'dati_1', array('kode'=>$r['kd_dati_1']))) ); 
            $this->set_response($r, Bismillah_Controller::HTTP_OK);
        }
    }

    public function index_post($kode){
        // saving
        $va = $this->post();

        if($kode == 0){
            $va['kode'] =  $this->Bdb->getIncrement("DT2", true, 5); 
        } else {
            $va['kode'] = $kode;
        }

        $va['username'] = $this->aruser['username'];
        $this->Bdb->upsert('dati_2', $va, array("kode"=>$kode));

        // response
        $this->set_response(['saved' => true], Bismillah_Controller::HTTP_OK);
    }

    public function hapus_get($kode){
        // delete
        $lvalid = false; 

        // $r = $this->Bdb->getOne('*', 'gd_pelanggan', array('kd_tr'=>$kode));
        if(true) {
            $this->db->where('kode', $kode);
            $this->Bdb->db->delete("dati_2");
 
            $this->set_response(['deleted' => true,'kode'=>$kode, "msg"=>"Berhasil menghapus Data"], Bismillah_Controller::HTTP_OK);
            $lvalid = true;
        }  
        
        $this->set_response(['deleted' => $lvalid,'kode'=>$kode, "msg"=>"Gagal menghapus Data"], Bismillah_Controller::HTTP_OK);
    }

    public function initkode_get() {
        // cek
        $this->cekkode();
        $kode = $this->Bdb->getIncrement("DT2", false, 5); 

        $this->set_response(['kode'=>$kode], Bismillah_Controller::HTTP_OK);
    }

    public function cekkode() {
        $kode = $this->Bdb->getIncrement("DT2", false, 5);
        $rcek = $this->Bdb->getOne('*', 'dati_2', array('kode' => $kode));
        if (!empty($rcek)) {
            $kode = $this->Bdb->getIncrement("DT2", true, 5); 
            $this->cekkode($kode);
        }
    }
}