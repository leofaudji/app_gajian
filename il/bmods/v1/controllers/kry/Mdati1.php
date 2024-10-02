<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
class Mdati1 extends Bismillah_Controller{
    public function __construct(){
        parent::__construct();
        $this->auth();
        $this->load->model('v1/Bdb') ; 
    }

    public function gr1_where($bs, $s){
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
                            ->from("dati_1")
                            ->get();
        $r  = $db->row_array();
        if(isset($r)){
            $r['jml']   = intval($r['jml']);
            if($r['jml'] > 0) {
                $re['total'] = $r['jml'];
                $this->gr1_where($bs, $s);
                $db = $this->Bdb->db->select("*")
                                    ->from("dati_1")
                                    ->limit($va['limit'], $va['offset'])
                                    ->order_by('id DESC')
                                    ->get(); 
                foreach($db->result_array() as $r){
                    $r['recid'] = $r['id'];
                    $r['edit'] = ' 
                                <div class="btn-group w-100">
                                    <button type="button" class="btn btn-default btn-w2gr" onClick=bo.mdati1.edit("'.$r['kode'].'")><i class="fa fa-pencil-square-o me-1"></i>&nbsp;Koreksi</button>
                                </div>';
                                // <button type="button" onClick=bo.mdati1.delete("'.$r['kode'].'") class="btn btn-danger btn-w2gr"><i class="fa fa-trash me-1"></i>Hapus</button>
                    $re['records'][]   = $r ;  
                }
            }
        }

        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function index_get($kode){
        // koreksi
        $r = $this->Bdb->getOne('*', 'dati_1', array('kode'=>$kode));
        if(!empty($r)){
            $this->set_response($r, Bismillah_Controller::HTTP_OK);
        }
    }

    public function index_post($kode){
        // saving
        $va = $this->post();

        if($kode == 0){
            $va['kode'] =  $this->Bdb->getIncrement("DT1", true, 3); 
        } else {
            $va['kode'] = $kode;
        }
  
        $va['username'] = $this->aruser['username'];
        $this->Bdb->upsert('dati_1', $va, array("kode"=>$kode));

        // response
        $this->set_response(['saved' => true], Bismillah_Controller::HTTP_OK);
    }

    public function hapus_get($kode){
        // delete
        $lvalid = false; 

        $datti1 = $this->Bdb->getOne('*', 'dati_2', array('kd_dati_1'=>$kode));
        if(empty($datti1)) {
            $this->db->where('kode', $kode);
            $this->Bdb->db->delete("dati_1");
 
            $this->set_response(['deleted' => true,'kode'=>$kode, "msg"=>"Berhasil menghapus Data"], Bismillah_Controller::HTTP_OK);
            $lvalid = true;
        }  
        
        $this->set_response(['deleted' => $lvalid,'kode'=>$kode, "msg"=>"Gagal menghapus Data"], Bismillah_Controller::HTTP_OK);
    }

    public function initkode_get() {
        // cek
        $this->cekkode();
        $kode = $this->Bdb->getIncrement("DT1", false, 3); 

        $this->set_response(['kode'=>$kode], Bismillah_Controller::HTTP_OK);
    }

    public function cekkode() {
        $kode = $this->Bdb->getIncrement("DT1", false, 3);
        $rcek = $this->Bdb->getOne('*', 'dati_1', array('kode' => $kode));
        if (!empty($rcek)) {
            $kode = $this->Bdb->getIncrement("DT1", true, 3);
            $this->cekkode($kode);
        }
    }
}