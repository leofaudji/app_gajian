<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
class Mktr extends Bismillah_Controller{
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
                            ->from("mst_kantor")
                            ->get();
        $r  = $db->row_array();
        if(isset($r)){
            $r['jml']   = intval($r['jml']);
            if($r['jml'] > 0){
                $re['total'] = $r['jml'];
                $this->gr1_where($bs, $s);
                $db = $this->Bdb->db->select("*")
                                    ->from("mst_kantor")
                                    ->limit($va['limit'], $va['offset'])
                                    ->order_by('id DESC')
                                    ->get(); 
                foreach($db->result_array() as $r){
                    $r['recid'] = $r['id'];

                    $r['edit'] = '<div class="btn-group  w-100">
                                <button type="button" class="btn btn-default btn-w2gr" onClick=bo.mcbg.edit("'.$r['kode'].'") ><i class="fa fa-pencil-square-o"></i>&nbsp;Koreksi</button>
                            </div>';
                    //append
                    $re['records'][]   = $r ;  
                }
            }
        }

        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function index_get($kode){
        // koreksi
        $r = $this->Bdb->getOne('*', 'mst_kantor', array('kode'=>$kode));
        if(!empty($r)){
            $this->set_response($r, Bismillah_Controller::HTTP_OK);
        }
    }

    public function index_post($kode){
        // saving
        $va = $this->post();
        $va['username'] = $this->aruser['username'];
        $this->Bdb->upsert('mst_kantor', $va, array("kode"=>$kode)) ; 

        // response
        $this->set_response(['saved' => true], Bismillah_Controller::HTTP_OK);
    }
}