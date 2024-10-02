<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
class Krydiv extends Bismillah_Controller{
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

        $this->lv_e = true;
        $this->lv_d = true;
        if($this->aruser['lv'] !== "0000"){
            $this->lv_e = boolval(strpos($this->aruser['menu_md5'], md5("krydiv.e")));
            $this->lv_d = boolval(strpos($this->aruser['menu_md5'], md5("krydiv.d")));
        }

        $bs = isset($va['bsearch']) ? $va['bsearch'] : array();
        $s = isset($va['search']) ? $va['search'][0]['value'] : '';
        $this->gr1_where($bs, $s);
        $db = $this->Bdb->db->select("count(id) jml")
                            ->from("mst_divisi")
                            ->get();
        $r  = $db->row_array();
        if(isset($r)){
            $r['jml']   = intval($r['jml']);
            if($r['jml'] > 0){
                $re['total'] = $r['jml'];
                $this->gr1_where($bs, $s);
                $db = $this->Bdb->db->select("*")
                                    ->from("mst_divisi")
                                    ->limit($va['limit'], $va['offset'])
                                    ->order_by('id DESC')
                                    ->get();  
                foreach($db->result_array() as $r){
                    $r['recid'] = $r['id'];

                    $r['cmd'] = '<div class="btn-group w-100">';
                    if($this->lv_e) $r['cmd'] .= '<button type="button" onClick=bo.krydiv.edit("'.$r['kode'].'") class="btn btn-default btn-w2gr w-100">Koreksi</button>';
                    if($this->lv_d) $r['cmd'] .= '<button type="button" onClick=bo.krydiv.delete("'.$r['id'].'") class="btn btn-danger btn-w2gr w-100">Hapus</button>';
                    $r['cmd'] .= '</div>';

                    //append
                    $re['records'][]   = $r ;  
                }
            }
        }

        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function index_get($kode){
        // koreksi
        $r = $this->Bdb->getOne('*', 'mst_divisi', array('kode'=>$kode));
        if(!empty($r)){
            $this->set_response($r, Bismillah_Controller::HTTP_OK);
        }
    }

    public function hapus_get($id){ 
        // delete
        $lresult = false;
        $r = $this->Bdb->getOne('*', 'mst_divisi', array('id'=>$id));
        if(!empty($r)){
          $this->db->where('id', $id);
          $this->db->delete("mst_divisi"); 
          $lresult = true;
        }

        $this->set_response(['deleted' => $lresult], Bismillah_Controller::HTTP_OK);    
    }

    public function index_post($kode){
        // saving
        $va = $this->post();
        $va['username'] = $this->aruser['username'];
        $this->Bdb->upsert('mst_divisi', $va, array("kode"=>$kode)) ; 

        // response
        $this->set_response(['saved' => true], Bismillah_Controller::HTTP_OK);
    }
}