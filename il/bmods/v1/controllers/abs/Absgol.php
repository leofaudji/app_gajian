<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
class Absgol extends Bismillah_Controller{
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
            $this->lv_e = boolval(strpos($this->aruser['menu_md5'], md5("absgol.e")));
            $this->lv_d = boolval(strpos($this->aruser['menu_md5'], md5("absgol.d")));
        }

        $bs = isset($va['bsearch']) ? $va['bsearch'] : array();
        $s = isset($va['search']) ? $va['search'][0]['value'] : '';
        $this->gr1_where($bs, $s);
        $db = $this->Bdb->db->select("count(id) jml")
                            ->from("mst_abs_golongan")
                            ->get();
        $r  = $db->row_array();
        if(isset($r)){
            $r['jml']   = intval($r['jml']);
            if($r['jml'] > 0){
                $re['total'] = $r['jml'];
                $this->gr1_where($bs, $s);
                $db = $this->Bdb->db->select("*")
                                    ->from("mst_abs_golongan")
                                    ->limit($va['limit'], $va['offset'])
                                    ->order_by('id DESC')
                                    ->get();  
                foreach($db->result_array() as $r){
                    $r['recid'] = $r['id'];

                    $r['cmd'] = '<div class="btn-group w-100">';
                    if($this->lv_e) $r['cmd'] .= '<button type="button" onClick=bo.absgol.edit("'.$r['kode'].'") class="btn btn-default btn-w2gr w-100">Koreksi</button>';
                    if($this->lv_d) $r['cmd'] .= '<button type="button" onClick=bo.absgol.delete("'.$r['id'].'") class="btn btn-danger btn-w2gr w-100">Hapus</button>';
                    $r['cmd'] .= '</div>';

                    //append
                    $re['records'][]   = $r ;   
                }
            }
        }

        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function gr2_post(){
        // grid
        $va = json_decode($this->post()['request'], true); 
        $re = array('total'=>0, 'records'=>array());
        $kode = $va['kode'] ?? "" ;
        //print_r($va) ;  
        //->where("golongan_absensi = '{$va['kode']}")
        $db = $this->Bdb->db->select("count(id) jml")
                            ->from("mst_abs_golongan_cfg")
                            ->where("golongan_absensi = '$kode'")
                            ->get();
        $r  = $db->row_array();
        if(isset($r)){
            $r['jml']   = intval($r['jml']);
            if($r['jml'] > 0){ 
                $re['total'] = $r['jml'];
                $db = $this->Bdb->db->select("*")
                                    ->from("mst_abs_golongan_cfg")
                                    ->where("golongan_absensi = '$kode'")
                                    ->limit($va['limit'], $va['offset'])
                                    ->order_by('id ASC')
                                    ->get();  
                foreach($db->result_array() as $r){
                    $r['recid'] = $r['id'];
                    //append
                    $re['records'][]   = $r ;   
                }
            }else{
                $re['total'] = 7 ;
                for($i=0;$i<=6;$i++){
                    $hari = array("Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu") ; 
                    $r = array("recid"=>$i,"hari"=>$hari[$i],"jam_masuk"=>"08.00 am","jam_pulang"=>"05.00 pm","toleransi"=>"08.05 am") ;
                    $re['records'][] = $r ;
                }
            }
        }     



        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function index_get($kode){
        // koreksi
        $r = $this->Bdb->getOne('*', 'mst_abs_golongan', array('kode'=>$kode));
        if(!empty($r)){ 
            $this->set_response($r, Bismillah_Controller::HTTP_OK);
        }
    }

    public function hapus_get($id){ 
        // delete
        $lresult = false;
        $r = $this->Bdb->getOne('*', 'mst_abs_golongan', array('id'=>$id));
        if(!empty($r)){
          $this->db->where('id', $id);
          $this->db->delete("mst_abs_golongan"); 
          $lresult = true;
        }

        $this->set_response(['deleted' => $lresult], Bismillah_Controller::HTTP_OK);    
    }

    public function index_post($kode){
        // saving
        $va = $this->post();
        
        //print_r($va['gr2']) ;  
        $username = $this->aruser['username'] ;
        $va1 = array("kode"=>$kode,"keterangan"=>$va['keterangan'],"username" =>$username) ; 
        $this->Bdb->upsert('mst_abs_golongan', $va1, array("kode"=>$kode)) ;   

        $tgl = date("Y-m-d") ;
        foreach($va['gr2'] as $key=>$value){      
            $va2 = array("tgl"=>$tgl,"hari"=>$value['hari'],"golongan_absensi"=>$kode,"jam_masuk"=>$value['jam_masuk'],"jam_pulang"=>$value['jam_pulang'],"toleransi"=>$value['toleransi'],"username"=>$username) ;
            $this->Bdb->upsert('mst_abs_golongan_cfg', $va2, array("hari"=>$value['hari'],"golongan_absensi"=>$kode)) ;    
        }

        // response
        $this->set_response(['saved' => true], Bismillah_Controller::HTTP_OK);
    }
}