<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
class Gjper extends Bismillah_Controller{
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
                            ->from("gj_periode")
                            ->get();
        $r  = $db->row_array();
        if(isset($r)){
            $r['jml']   = intval($r['jml']);
            if($r['jml'] > 0){ 
                $re['total'] = $r['jml'];
                $this->gr1_where($bs, $s);
                $db = $this->Bdb->db->select("*")
                                    ->from("gj_periode")
                                    ->limit($va['limit'], $va['offset'])
                                    ->order_by('id DESC')
                                    ->get();  
                foreach($db->result_array() as $r){
                    $r['recid'] = $r['id'];

                    $r['awal']  = date_2d($r['awal']) ;
                    $r['akhir'] = date_2d($r['akhir']) ;
                    $r['cmd']   = '<div class="btn-group w-100">';
                    if($r['status'] == '0') $r['cmd']   .= '<button type="button" onClick=bo.gjper.edit("'.$r['kode'].'") class="btn btn-default btn-w2gr w-100">Batal</button>';
                    if($r['status'] == '1') $r['cmd']   .= '<button type="button" onClick=bo.gjper.edit("'.$r['kode'].'") class="btn btn-danger btn-w2gr w-100">Tutup</button>';
                    $r['cmd']   .= '</div>';

                    //append
                    $re['records'][]   = $r ;   
                }
            }
        }
 
        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function index_get($kode){
        // koreksi
        $r = $this->Bdb->getOne('*', 'gj_periode', array('kode'=>$kode));
        if(!empty($r)){   
            $this->set_response($r, Bismillah_Controller::HTTP_OK);
        }
    }

    public function hapus_get($id){ 
        // delete
        $lresult = false;
        $r = $this->Bdb->getOne('*', 'gj_periode', array('id'=>$id));
        if(!empty($r)){
          $this->db->where('id', $id);
          $this->db->delete("gj_periode"); 
          $lresult = true;
        }

        $this->set_response(['deleted' => $lresult], Bismillah_Controller::HTTP_OK);    
    }

    public function index_post($kode){
        // saving
        $va = $this->post();
        $va['username'] = $this->aruser['username'];
        $va['awal'] = date_2s($va['awal']) ;
        $va['akhir'] = date_2s($va['akhir']) ;
        $this->Bdb->upsert('gj_periode', $va, array("kode"=>$kode)) ; 

        // response
        $this->set_response(['saved' => true], Bismillah_Controller::HTTP_OK);
    }
}