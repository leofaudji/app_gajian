<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
class Absdas extends Bismillah_Controller{
    public function __construct(){
        parent::__construct();  
        $this->auth();
        $this->load->model('v1/Bdb') ; 
    }

    public function gr1_where($bs, $s){  
        if($s !== ''){
            $this->db->group_start();
            $this->db->or_like(array('nama'=>$s)); 
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
        $db = $this->Bdb->db->select("count(a.id) jml")
                                    ->from("abs_tmp a")
                                    ->join("mst_karyawan m","m.kode = a.kode_kry","left") 
                                    ->get();
        $r  = $db->row_array();
        if(isset($r)){
            $r['jml']   = intval($r['jml']);
            if($r['jml'] > 0){
                $re['total'] = $r['jml'];
                $this->gr1_where($bs, $s);
                $db = $this->Bdb->db->select("a.*,m.nama,ms.keterangan abs_status")
                                    ->from("abs_tmp a")
                                    ->join("mst_karyawan m","m.kode = a.kode_kry","left") 
                                    ->join("mst_abs_status ms","ms.kode = a.abs_status","left")  
                                    ->limit($va['limit'], $va['offset'])
                                    ->order_by('a.id DESC') 
                                    ->get();  
                foreach($db->result_array() as $r){
                    $r['recid'] = $r['id'];  

                    $r['tgl_absen'] = date_2d($r['tgl_absen']) ;
                    //append
                    $re['records'][]   = $r ;       
                }
            }
        }

        // konversi data karyawan
        /*$conn = mysqli_connect("aa.akt.sis1.net","Assist","Irac","assist_akt") ;
        $db = mysqli_query($conn,"select * from hrd_absensi_tmp where tgl = '2024-10-01' order by NIP") ; 
        while($row = mysqli_fetch_array($db)){ 
        
            $va = array("kode_kry"=>$row['NIP'],"tgl"=>$row['Tgl'],"jam"=>$row['Jam'],"tgl_absen"=>$row['TglAbsen'],
                        "abs_status"=>"001","keterangan"=>$row['Keterangan'],"kode_kantor"=>$row['Cabang']) ;
            $this->Bdb->upsert('abs_tmp', $va, array("kode_kry"=>$row['NIP'],"tgl"=>$row['Tgl'])) ;  
        }*/

        $this->set_response($re, Bismillah_Controller::HTTP_OK);
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