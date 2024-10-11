<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
class Gjpay extends Bismillah_Controller{
    public function __construct(){
        parent::__construct();  
        $this->auth();
        $this->load->model('v1/Bdb') ; 
        $this->load->model('v1/Gj') ;  
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

        $kode_kantor    = $va['kode_kantor'] ?? "" ;       
        $periode        = $va['periode']  ?? "" ;
        $tgl            = date_2s($va['tgl'])  ?? date("Y-m-d") ;

        //print($tgl) ; 

        $this->gr1_where($bs, $s); 
        $this->db->where('kode_kantor', $kode_kantor); 
        $this->db->where('tgl_keluar >=', $tgl);

        $db = $this->Bdb->db->select("count(id) jml")
                            ->from("mst_karyawan")
                            ->get();
        $r  = $db->row_array();
        if(isset($r)){
            $r['jml']   = intval($r['jml']);
            if($r['jml'] > 0){
                $re['total'] = $r['jml'];
                $this->gr1_where($bs, $s);
                $this->db->where('tgl_keluar >=', $tgl);
                $this->db->where('kode_kantor', $kode_kantor); 
                $db = $this->Bdb->db->select("*")
                                    ->from("mst_karyawan")
                                    ->limit($va['limit'], $va['offset'])
                                    ->order_by('id ASC')
                                    ->get();  
                foreach($db->result_array() as $r){
                    $r['recid'] = $r['id'] ;
 
                    $datagaji           = $this->Gj->get_gaji($r['kode_kantor'],$periode,$r['kode'],$r['golongan']) ;    
                    $r['total_gaji']    = number_format($datagaji['gaji']['total_gaji'],0) ;                    
                    $r['masakerja']     = hitung_umur($r['tgl_masuk']) ;
                    //append
                    $re['records'][]    = $r ;  
                }

            }
        }


        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function gr2_post(){
        // grid
        $va = json_decode($this->post()['request'], true);
        $re = array('total'=>0, 'records'=>array());

        $this->db->where("dk","D");
        $db = $this->Bdb->db->select("count(id) jml")
                            ->from("gj_komponen")                                    
                            ->get();
        $r          = $db->row_array();
        $kode_kantor= $va['kode_kantor'] ?? "" ; 
        $kode       = $va['kode'] ?? "" ;
        $golongan   = $va['golongan'] ?? "" ;
        $periode    = $va['periode'] ?? "" ;

        //print_r($va) ;
        $datagaji = $this->Gj->get_gaji($kode_kantor,$periode,$kode,$golongan) ;  
        
        //print_r($datagaji) ; 

        if(isset($r)){
            $r['jml']   = intval($r['jml']);
            if($r['jml'] > 0){
                $re['total'] = $r['jml'];
                $i = 0 ;
                $data = array() ;
                $db = $this->Bdb->db->select("k.*,n.nominal")
                                    ->from("gj_komponen k")
                                    ->join("gj_komponen_nominal n","n.komponen = k.kode and n.golongan = '$golongan'","left") 
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
                    $r1['nominal'] = $datagaji['gaji'][$r['kode']] ?? $r['nominal'] ;   
                    $r1['nominalp'] = $datagaji['gaji'][$r['kode']] ?? $r['nominal'] ;   

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
                      $data1[$n]['perhitungan'] = ($value1['perhitungan'] == 'harian') ? $datagaji['absensi']['masuk'] : 1 ;
                      $data1[$n]['nominal']     = $value1['nominal'] ; 
                      $data1[$n]['jumlah']      = $value1['nominal'] * $data1[$n]['perhitungan'] ;
                      $data1[$n]['kodep']       = "" ;
                      $data1[$n]['dkp']         = "" ; 
                      $data1[$n]['potongan']    = "" ;
                      $data1[$n]['perhitunganp']= "" ; 
                      $data1[$n]['nominalp']    = "" ;
                      $data1[$n]['jumlahp' ]    = "" ;
                      $n++ ;                      
                    }
                  }else{
                    $n = 0 ;
                    foreach($value as $key1=>$value1){
                      $data1[$n]['kodep']       = $value1['kode'] ;   
                      $data1[$n]['dkp']         = $value1['dk'] ; 
                      $data1[$n]['potongan']    = $value1['potongan'] ;
                      $data1[$n]['perhitunganp'] = ($value1['perhitunganp'] == 'harian') ? $datagaji['absensi']['masuk'] : 1 ; 
                      $data1[$n]['nominalp']    = $value1['nominalp'] ; 
                      $data1[$n]['jumlahp']     = $value1['nominalp'] * $data1[$n]['perhitunganp'] ;
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
        $r = $this->Bdb->getOne('*', 'mst_karyawan', array('kode'=>$kode));
        if(!empty($r)){ 
            $r['kode_kantor']   = array( array('id'=>$r['kode_kantor'], 'text'=>$this->Bdb->getOne('keterangan', 'mst_kantor', array('kode'=>$r['kode_kantor']))) );
            $r['agama']         = array( array('id'=>$r['agama'], 'text'=>$this->Bdb->getOne('keterangan', 'mst_agama', array('kode'=>$r['agama']))) );
            $r['pendidikan']    = array( array('id'=>$r['pendidikan'], 'text'=>$this->Bdb->getOne('keterangan', 'mst_pendidikan', array('kode'=>$r['pendidikan']))) );
            $r['jabatan']       = array( array('id'=>$r['jabatan'], 'text'=>$this->Bdb->getOne('keterangan', 'mst_jabatan', array('kode'=>$r['jabatan']))) );         
            $r['golongan']       = array( array('id'=>$r['golongan'], 'text'=>$this->Bdb->getOne('keterangan', 'gj_golongan', array('kode'=>$r['golongan']))) );         
            $this->set_response($r, Bismillah_Controller::HTTP_OK);

        }
    }

    public function hapus_get($id){ 
        // delete
        $lresult = false;
        $r = $this->Bdb->getOne('*', 'mst_karyawan', array('id'=>$id));
        if(!empty($r)){
          $this->db->where('id', $id);
          $this->db->delete("mst_karyawan"); 
          $lresult = true;
        }

        $this->set_response(['deleted' => $lresult], Bismillah_Controller::HTTP_OK);    
    }

    public function egj_post($kode){  
        // saving
        $va = $this->post(); 
        
        //print("kode kry : " . $kode . "<br>") ;  
        //print_r($va) ;  
        $periode    = $va['periode'] ;
        $username   = $this->aruser['username'] ; 

        $tgl        = date_2s($va['tgl']) ; 
        
        foreach($va['gr2'] as $key=>$value){       
            $komponen = $value['kode']  ;   
            $where    = array("periode"=>$periode,"kode_kry"=>$kode,"komponen"=>$komponen) ; 
            $nominal  = $value['w2ui']['changes']['nominal'] ?? $value['nominal'] ;           
            $jml  = $value['w2ui']['changes']['perhitungan'] ?? $value['perhitungan'] ;           
                        
            $va2 = array("tgl"=>$tgl,"periode"=>$periode,"kode_kry"=>$kode,"komponen"=>$komponen,"nominal"=>$nominal,"perhitungan"=>$jml,"username"=>$username) ;    
            $this->Bdb->upsert('gj_komponen_nominal_kry', $va2, $where) ;     
            
            if($value['dkp'] == "K"){
              $komponen = $value['kodep']  ;      
              $where    = array("periode"=>$periode,"kode_kry"=>$kode,"komponen"=>$komponen) ; 
              $nominalp = $value['w2ui']['changes']['nominalp'] ?? $value['nominalp']  ;             
              $jmlp     = $value['w2ui']['changes']['perhitunganp'] ?? $value['perhitunganp'] ;           
              $va3 = array("tgl"=>$tgl,"periode"=>$periode,"kode_kry"=>$kode,"komponen"=>$komponen,"nominal"=>$nominalp,"perhitungan"=>$jmlp,"username"=>$username) ;    
              $this->Bdb->upsert('gj_komponen_nominal_kry', $va3, $where) ;      
            }
        }

        // response
        $this->set_response(['saved' => true], Bismillah_Controller::HTTP_OK); 
    }

    public function index_post($kode){   
        // saving
        $va = $this->post(); 
        
        //print("kode kry : " . $kode . "<br>") ;  
        //print_r($va['gr1']) ;  
        $kode_kantor= $va['kode_kantor'] ;
        $periode    = $va['periode'] ;
        $username   = $this->aruser['username'] ;                


        $tgl        = date_2s($va['tgl']) ; 
        
        foreach($va['gr1'] as $key=>$value){       
            $kode   = $value['kode'] ;
            $where  = array("kode_kantor"=>$kode_kantor,"periode"=>$periode,"kode_kry"=>$kode) ; 
                        
            $va2 = array("periode"=>$periode,"kode_kry"=>$kode,"tgl"=>$tgl,"rekening"=>$value['rekening'],
                         "total"=>parse_number($value['total_gaji'],2),"kode_kantor"=>$kode_kantor,"username"=>$username) ;       
            $this->Bdb->upsert('gj_payroll', $va2, $where) ;                   
        }

        // response
        $this->set_response(['saved' => true], Bismillah_Controller::HTTP_OK); 
    }


}