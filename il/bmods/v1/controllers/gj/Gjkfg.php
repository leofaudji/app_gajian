<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
class Gjkfg extends Bismillah_Controller{
    public function __construct(){
        parent::__construct();  
        $this->auth();
        $this->load->model('v1/Bdb') ; 
    }


    public function gr1_post(){
        // grid
        $va = json_decode($this->post()['request'], true);
        $re = array('total'=>0, 'records'=>array());

        $this->db->where("dk","D");
        $db = $this->Bdb->db->select("count(id) jml")
                            ->from("gj_komponen")                                    
                            ->get();
        $r            = $db->row_array();
        $tgl          = $va['tgl'] ?? "" ;
        $kode_kantor  = $va['kode_kantor'] ?? "" ;
        $golongan     = $va['golongan'] ?? "" ;

        if(isset($r)){
            $r['jml']   = intval($r['jml']);
            if($r['jml'] > 0){
                $re['total'] = $r['jml'];
                $i = 0 ;
                $data = array() ;
                $db = $this->Bdb->db->select("k.*,n.nominal")
                                    ->from("gj_komponen k")
                                    ->join("gj_komponen_nominal n","n.komponen = k.kode and n.kode_kantor = '$kode_kantor' and n.golongan = '$golongan'","left")
                                    ->limit($va['limit'], $va['offset'])
                                    ->order_by('k.id ASC') 
                                    ->get();  
                foreach($db->result_array() as $r){
                    
                    $r1['recid'] = $i++ ; 
                    $r1['kode'] = $r['kode'] ;
                    $r1['dk'] = $r['dk'] ;
                    $r1['tambahan'] = $r['keterangan'] ;
                    $r1['perhitungan'] = $r['perhitungan'] ;
                    $r1['potongan'] = $r['keterangan'] ;
                    $r1['perhitunganp'] = $r['perhitungan'] ;
                    $r1['nominal'] = $r['nominal'] ;
                    $r1['nominalp'] = $r['nominal'] ;

                    //append
                    $re['records'][]   = $r1 ;        

                    $data[$r['dk']][$r['kode']] = $r1 ; 
                }

                //print_r($data) ; 
                $data1 = array() ;
                foreach($data as $key=>$value){
                  if($key == "D"){
                    $n = 0 ;
                    foreach($value as $key1=>$value1){
                      $data1[$n]['recid']       = $value1['recid'] ;
                      $data1[$n]['kode']        = $value1['kode'] ;
                      $data1[$n]['dk']          = $value1['dk'] ; 
                      $data1[$n]['tambahan']    = $value1['tambahan'] ; 
                      $data1[$n]['perhitungan'] = strtoupper($value1['perhitungan']) ;
                      $data1[$n]['nominal']     = $value1['nominal'] ;
                      $data1[$n]['kodep']       = "" ;
                      $data1[$n]['dkp']         = "" ; 
                      $data1[$n]['potongan']    = "" ;
                      $data1[$n]['perhitunganp']= "" ; 
                      $data1[$n]['nominalp']    = "" ;
                      $n++ ;                      
                    }
                  }else{
                    $n = 0 ;
                    foreach($value as $key1=>$value1){
                      $data1[$n]['kodep']       = $value1['kode'] ;   
                      $data1[$n]['dkp']         = $value1['dk'] ; 
                      $data1[$n]['potongan']    = $value1['potongan'] ;
                      $data1[$n]['perhitunganp']= strtoupper($value1['perhitunganp']) ; 
                      $data1[$n]['nominalp']    = $value1['nominalp'] ; ;
                      $n++ ;                      
                    }
                  } 
                }
                //print_r($data1);
                $re['records'] = $data1 ;
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


    public function index_post($kode){
        // saving
        $va = $this->post();
        
        //print_r($va['gr1']) ;  
        $username     = $this->aruser['username'] ;
        $kode_kantor  = $va['kode_kantor'] ;
        $golongan     = $va['golongan'] ;     
        
        foreach($va['gr1'] as $key=>$value){       
            $komponen = $value['kode']  ;   
            $where    = array("kode_kantor"=>$kode_kantor,"golongan"=>$golongan,"komponen"=>$komponen) ; 
            $n1       = $this->Bdb->getOne('nominal', 'gj_komponen_nominal',$where) ;   
            $nominal  = $value['w2ui']['changes']['nominal'] ?? $n1 ;           
            $va2 = array("kode_kantor"=>$kode_kantor,"golongan"=>$golongan,"komponen"=>$komponen,"nominal"=>$nominal,"username"=>$username) ;    
            $this->Bdb->upsert('gj_komponen_nominal', $va2, $where) ;     
        
            if($value['dkp'] == "K"){
              $komponen = $value['kodep']  ;      
              $where    = array("kode_kantor"=>$kode_kantor,"golongan"=>$golongan,"komponen"=>$komponen) ; 
              $n2       = $this->Bdb->getOne('nominal', 'gj_komponen_nominal',$where) ;   
              $nominalp = $value['w2ui']['changes']['nominalp'] ?? $n2 ;            
              $va3 = array("kode_kantor"=>$kode_kantor,"golongan"=>$golongan,"komponen"=>$komponen,"nominal"=>$nominalp,"username"=>$username) ;    
              $this->Bdb->upsert('gj_komponen_nominal', $va3, $where) ;      
            }
        } 

        // response
        $this->set_response(['saved' => true], Bismillah_Controller::HTTP_OK); 
    }

}