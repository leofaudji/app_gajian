<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
class Gjdrft extends Bismillah_Controller{
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
        $this->db->where('a.kode_kantor', $kode_kantor); 
        $this->db->where('a.periode', $periode); 
        //$this->db->where('tgl_keluar >=', $tgl);

        $db = $this->Bdb->db->select("count(a.id) jml")
                            ->from("gj_payroll a") 
                            ->join("mst_karyawan m","m.kode = a.kode_kry","left")  
                            ->get();
        $r  = $db->row_array();
        if(isset($r)){
            $r['jml']   = intval($r['jml']);     
            if($r['jml'] > 0){
                $re['total'] = $r['jml'];
                $this->gr1_where($bs, $s);
                $this->db->where('a.kode_kantor', $kode_kantor); 
                $this->db->where('a.periode', $periode);  
                //$this->db->where('tgl_keluar >=', $tgl);
                $db = $this->Bdb->db->select("a.id,a.kode_kry,m.nama,m.tgl_masuk,a.total,a.rekening,m.golongan,mg.keterangan namagolongan,md.keterangan namadivisi,m.jabatan,mj.keterangan namajabatan")
                                    ->from("gj_payroll a")
                                    ->join("mst_karyawan m","m.kode = a.kode_kry","left")  
                                    ->join("gj_golongan mg","mg.kode = m.golongan","left")  
                                    ->join("mst_jabatan mj","mj.kode = m.jabatan","left")  
                                    ->join("mst_divisi md","md.kode = m.divisi","left")  
                                    ->limit($va['limit'], $va['offset'])
                                    ->order_by('m.kode ASC') 
                                    ->get();  
                foreach($db->result_array() as $r){
                    $r['recid'] = $r['id'] ;
                    $r['kode']  = $r['kode_kry'] ;
                    $r['nama']  = $r['nama'] ;
                    $r['golongan']  = $r['namagolongan'] ;
                    $r['divisi']    = $r['namadivisi'] ; 
                    $r['jabatan']   = $r['namajabatan'] ;
                    $r['total_gaji'] = number_format($r['total'],0) ;                     
                    $r['masakerja']  = hitung_umur($r['tgl_masuk'] ?? date("Y-m-d")) ;
                    $r['rekening']   = $r['rekening'] ;
                    
                    $r['detil'] = '<div class="btn-group w-100">
                                    <button type="button" onClick=bo.gjdrft.cetakdetil("'.$r['kode_kry'].'",1)
                                    class="btn btn-danger btn-w2gr w-100"><i class="fa fa-file-pdf-o"></i></button>
                                    
                                    <button type="button" onClick=bo.gjdrft.cetakdetil("'.$r['kode_kry'].'",2)
                                    class="btn btn-success btn-w2gr w-100"><i class="fa fa-file-excel-o"></i></button> 
                                    </div>
                                  ';

                    //append
                    $re['records'][]    = $r ;  
                }
            }
        } 

        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }


    public function cetak_get(){
        $va = $this->get() ;

        $title = "DAFTAR PAYROLL " ;  

        $bs = isset($va['bsearch']) ? $va['bsearch'] : array();
        
        $periode = $va['periode'] ;
        $kode_kantor = $va['kode_kantor'] ;
        $s = '';
        $this->gr1_where($bs, $s);
        $db = $this->Bdb->db->select("count(id) jml")->from("mst_karyawan")->get(); 
        $r  = $db->row_array();  
        if(isset($r)){
            $r['jml']   = intval($r['jml']);
            $totqty = 0 ;
            if($r['jml'] > 0){
                $cetak  = $xlsx = $vhead = array() ;
                
                $this->gr1_where($bs, $s);
                $this->db->where('a.kode_kantor', $kode_kantor); 
                $this->db->where('a.periode', $periode); 
                $db2 = $this->Bdb->db->select("a.id,a.kode_kry,m.nama,m.tgl_masuk,a.total,a.rekening,m.golongan,mg.keterangan namagolongan,md.keterangan namadivisi,m.jabatan,mj.keterangan namajabatan")
                                    ->from("gj_payroll a")
                                    ->join("mst_karyawan m","m.kode = a.kode_kry","left")   
                                    ->join("gj_golongan mg","mg.kode = m.golongan","left")  
                                    ->join("mst_jabatan mj","mj.kode = m.jabatan","left")  
                                    ->join("mst_divisi md","md.kode = m.divisi","left")
                            ->order_by('a.periode,a.kode_kry')
                            ->get();  

                $vhead[0][0]= array('text'=>'#', 'width'=>'*', 'bold'=>true, 'fillColor'=>'#eeeeee', 'alignment'=>'center', 'fontSize'=>9) ; 
                $vhead[0][1]= array('text'=>'Kode Kry', 'width'=>'*', 'bold'=>true, 'fillColor'=>'#eeeeee', 'alignment'=>'center', 'fontSize'=>9) ; 
                $vhead[0][2]= array('text'=>'Nama', 'width'=>'*', 'bold'=>true, 'fillColor'=>'#eeeeee', 'alignment'=>'center', 'fontSize'=>9) ; 
                $vhead[0][3]= array('text'=>'Jabatan', 'width'=>'*', 'bold'=>true, 'fillColor'=>'#eeeeee', 'alignment'=>'center', 'fontSize'=>9) ; 
                $vhead[0][4]= array('text'=>'Gaji', 'width'=>'*', 'bold'=>true, 'fillColor'=>'#eeeeee', 'alignment'=>'center', 'fontSize'=>9) ; 

                $dff        = array('text'=>0, 'alignment'=>'right', 'bold'=>true, 'italics'=>true) ;
                $arr        = array('b'=>array(), 'o'=>array(),'tb'=>array(),'to'=>array());
                $arr['o']   = array("#"=>array('width'=>18, 'alignment'=>'right') ,
                                    "kode"=>array('width'=>50,'alignment'=>'center',  'fontSize'=>7),
                                    "nama"=>array('width'=>'*','alignment'=>'left'), 
                                    "jabatan"=>array('width'=>100,'caption'=>'Jabatan', 'alignment'=>'left'),
                                    "gaji"=>array('width'=>80, 'alignment'=>'right')
                                );
                
                $arr['to']   = array("KET"=>array('width'=>'*', 'alignment'=>'right'),
                                "SALDO"=>array('width'=>80, 'alignment'=>'right')
                            );
                
                $no = 0;
                $total = 0 ;
                foreach($db2->result_array() as $key => $rb){
                    $total += $rb['total'] ;
                    $data = array("#"=>++$no, "kode"=>$rb['kode_kry'], "nama"=>$rb['nama'], "jabatan"=>$rb['namajabatan'],"gaji"=>number_format($rb['total'])); 
                    $arr['b'][]  = $data;
                    $xlsx['DETIL'][] = $data;  
                } 

                $data = array("KET"=>"Total","SALDO"=>number_format($total));
                $arr['tb'][]  = $data;
                $xlsx['DETIL'][] = $data;

                
    
                if(count($arr['b']) > 0){// generate table                    
                    $rexlsx['sheets'] = array('TOTAL') ;

                    // generate content
                    $namakantor = $this->Bdb->getOne('keterangan', 'mst_kantor', array('kode'=>$kode_kantor)) ;  
                    $cetak[]= array('text'=>$namakantor, 'fontSize'=>13, 'bold'=>true, 'alignment'=>'center');
                    $cetak[]= array('text'=>$title, 'fontSize'=>10, 'bold'=>true, 'alignment'=>'center');
                    $cetak[]= array('text'=>"Periode " . $periode, 'fontSize'=>8, 'bold'=>true, 'alignment'=>'center');
                    $cetak[]= chr(13) . chr(13);
                    $cetak[]= pdf_tbl($arr['o'], $arr['b'], array(), true, $vhead);
                    $cetak[]= pdf_tbl($arr['to'], $arr['tb'], array(), false);
                    

                    foreach($rexlsx['sheets'] as $k => $v){
                        $judul[$v]['A1'] = array("t"=>"s", "v"=>'DAFTAR PAYROLL ' . $v) ;

                        $rexlsx['contentXlsx']['data'][$v] = $xlsx['DETIL'] ;
                        $rexlsx['contentXlsx']['tambahan'][$v]   = $judul[$v] ;
                        $rexlsx['contentXlsx']['origin'][$v]     = 3 ;
                    }
                }


                $this->set_response(array("content"=>$cetak, 'xlsx'=>$rexlsx), Bismillah_Controller::HTTP_OK);
           
            }else{
                $this->set_response(array(), Bismillah_Controller::HTTP_OK);
            }
        }else{
            $this->set_response(array(), Bismillah_Controller::HTTP_OK);
        }
    }

    public function cetakdetil_get(){
        $va = $this->get() ;

        $title = "SLIP GAJI KARYAWAN " ;  

        $bs = isset($va['bsearch']) ? $va['bsearch'] : array();
        
        $periode = $va['periode'] ;
        $kode_kantor = $va['kode_kantor'] ;
        $kode_kry = $va['kode_kry'] ; 
        $s = '';
        $this->gr1_where($bs, $s);
        $db = $this->Bdb->db->select("count(id) jml")->from("mst_karyawan")->get(); 
        $r  = $db->row_array();  
        if(isset($r)){
            $r['jml']   = intval($r['jml']);
            if($r['jml'] > 0){
                $cetak  = $xlsx = $vhead = array() ;
                
                $this->gr1_where($bs, $s);
                //print($periode . "-" . $kode_kry) ;
                $this->db->where('a.periode', $periode);   
                $this->db->where('a.kode_kry', $kode_kry);    
                $db2 = $this->Bdb->db->select("a.*,g.dk,g.keterangan")   
                                    ->from("gj_komponen_nominal_kry a")
                                    ->join("gj_komponen g","g.kode = a.komponen","left")   
                                    ->order_by('a.periode,a.komponen ASC')   
                                    ->get();  

                $vhead[0][0]= array('text'=>'KD', 'width'=>'*', 'bold'=>true,  'alignment'=>'center', 'fontSize'=>10) ; 
                $vhead[0][1]= array('text'=>'Tambahan', 'width'=>'*', 'bold'=>true, 'alignment'=>'left', 'fontSize'=>10) ; 
                $vhead[0][2]= array('text'=>' ', 'width'=>'*', 'bold'=>true, 'alignment'=>'center', 'fontSize'=>10) ; 
                $vhead[0][3]= array('text'=>'KD', 'width'=>'*', 'bold'=>true, 'alignment'=>'center', 'fontSize'=>10) ; 
                $vhead[0][4]= array('text'=>'Potongan', 'width'=>'*', 'bold'=>true, 'alignment'=>'left', 'fontSize'=>10) ; 
                $vhead[0][5]= array('text'=>' ', 'width'=>'*', 'bold'=>true, 'alignment'=>'center', 'fontSize'=>10) ; 
                

                $dff        = array('text'=>0, 'alignment'=>'right', 'bold'=>true, 'italics'=>true) ;
                $arr        = array('b'=>array(), 'o'=>array(),'tb'=>array(),'to'=>array());
                $arr['o']   = array("kode"=>array('width'=>15,'alignment'=>'center',  'fontSize'=>8.5),
                                    "tambahan"=>array('width'=>"*", 'alignment'=>'left',  'fontSize'=>8.5),
                                    "jumlah"=>array('width'=>50, 'alignment'=>'right',  'fontSize'=>8.5),
                                    "kodep"=>array('width'=>15,'alignment'=>'center',  'fontSize'=>8.5),
                                    "potongan"=>array('width'=>"*", 'alignment'=>'left',  'fontSize'=>8.5),
                                    "jumlahp"=>array('width'=>50, 'alignment'=>'right',  'fontSize'=>8.5)
                                );
                 
                $arr['to']   = array("kettambahan"=>array('width'=>'*', 'bold'=>true, 'alignment'=>'right'),
                                     "saldotambahan"=>array('width'=>50, 'bold'=>true, 'alignment'=>'right'),
                                     "ketpotongan"=>array('width'=>'*', 'bold'=>true, 'alignment'=>'right'),
                                     "saldopotongan"=>array('width'=>50, 'bold'=>true, 'alignment'=>'right')
                            );

                $arr['tbb']   = array("kettambahan"=>array('width'=>'*', 'bold'=>true, 'alignment'=>'right'),
                            "saldotambahan"=>array('width'=>50, 'bold'=>true, 'alignment'=>'right'),
                            "ketpotongan"=>array('width'=>'*', 'bold'=>true, 'alignment'=>'right'),
                            "saldopotongan"=>array('width'=>50, 'bold'=>true, 'alignment'=>'right')
                   );

                $arr['ttd']  = array("karyawan"=>array('width'=>'*', 'alignment'=>'center'),"direktur"=>array('width'=>'*', 'alignment'=>'center'));
       
                $no = 0;
                $total = 0 ;
                $vadata = array() ;
                foreach($db2->result_array() as $key => $rb){ 
                    $data = array("komponen"=>$rb['komponen'], "tambahan"=>$rb['nominal'],"");  
                    $rb['tambahan'] = $rb['keterangan'] ;
                    $rb['potongan'] = $rb['keterangan'] ; 
                    $rb['nominalp'] = $rb['nominal'] ;  
                    $rb['perhitunganp'] = $rb['perhitungan'] ;  

                    //$arr['b'][]  = $data;
                    $vadata[$rb['dk']][$rb['komponen']] = $rb ;   
                    
                } 

                //print_r($vadata) ;

                $data1 = array() ; 
                $tottambahan = 0 ;
                $totpotongan = 0 ;
                foreach($vadata as $key=>$value){
                  if($key == "D"){
                    $n = 0 ;
                    foreach($value as $key1=>$value1){
                      $data1[$n]['kode']        = $value1['komponen'] ;
                      $data1[$n]['dk']          = $value1['dk'] ;  
                      $kettambahan              = ($value1['perhitungan'] <> '1') ? $value1['tambahan'] . " (" . $value1['perhitungan'] . " * " . parse_number($value1['nominal']) . ")" : $value1['tambahan'] ; 
                      $data1[$n]['tambahan']    = $kettambahan ;
                      $data1[$n]['perhitungan'] = $value1['perhitungan'] ;
                      $data1[$n]['nominal']     = $value1['nominal'] ; 
                      $tambahan                 = $value1['nominal'] * $data1[$n]['perhitungan'] ;
                      $data1[$n]['jumlah']      = number_format($tambahan,0) ;
                      $data1[$n]['kodep']       = "" ;
                      $data1[$n]['dkp']         = "" ; 
                      $data1[$n]['potongan']    = "" ;
                      $data1[$n]['perhitunganp']= "" ; 
                      $data1[$n]['nominalp']    = "" ;
                      $data1[$n]['jumlahp' ]    = "" ;
                      $tottambahan += $tambahan ;
                      $n++ ;                      
                    }
                  }else{
                    $n = 0 ;
                    foreach($value as $key1=>$value1){
                      $data1[$n]['kodep']       = $value1['komponen'] ;   
                      $data1[$n]['dkp']         = $value1['dk'] ; 
                      $ketpotongan              = ($value1['perhitungan'] <> '1') ? $value1['potongan'] . " (" . $value1['perhitungan'] . " * " . parse_number($value1['nominal']) . ")" : $value1['potongan'] ; 
                      $data1[$n]['potongan']    = $ketpotongan ;
                      $data1[$n]['perhitunganp'] = $value1['perhitungan'] ;
                      $data1[$n]['nominalp']    = $value1['nominalp'] ; 
                      $potongan                 = $value1['nominalp'] * $data1[$n]['perhitunganp'] ;
                      $data1[$n]['jumlahp']     = number_format($potongan,0) ;
                      $totpotongan += $potongan ; 
                      $n++ ;                       
                    }
                  } 
                }
                //print_r($data1); 
                $arr['b']  = $data1;
                $xlsx['DETIL'] = $data1;   

                $data = array("kettambahan"=>"Total Tambahan","saldotambahan"=>number_format($tottambahan,0),"ketpotongan"=>"Total Potongan","saldopotongan"=>number_format($totpotongan,0));
                $arr['tb'][]  = $data; 
                $xlsx['DETIL'][] = $data;

                $arr['tbbo'][]  = array("kettambahan"=>"","saldotambahan"=>"","ketpotongan"=>"Gaji Bersih","saldopotongan"=>number_format($tottambahan - $totpotongan,0));


                $namakantor = $this->Bdb->getOne('keterangan', 'mst_kantor', array('kode'=>$kode_kantor)) ;  
                    
                $arr['ttdo'][]  = array("karyawan"=>"Karyawan","direktur"=>"Malang, " . date("d-m-Y")) ;
                $arr['ttdo'][]  = array("karyawan"=>"","direktur"=>"") ;
                $arr['ttdo'][]  = array("karyawan"=>"","direktur"=>"") ;
                $arr['ttdo'][]  = array("karyawan"=>"","direktur"=>"") ;
                $arr['ttdo'][]  = array("karyawan"=>"","direktur"=>"") ;
                $arr['ttdo'][]  = array("karyawan"=>"","direktur"=>"") ;
                $arr['ttdo'][]  = array("karyawan"=>"","direktur"=>"") ;
                $arr['ttdo'][]  = array("karyawan"=>"","direktur"=>"") ;
                $arr['ttdo'][]  = array("karyawan"=>$kode_kry,"direktur"=>$namakantor) ;
                
    
                if(count($arr['b']) > 0){// generate table                     
                    $rexlsx['sheets'] = array('TOTAL') ;

                    // generate content
                    $cetak[]= array('text'=>$namakantor, 'fontSize'=>13, 'bold'=>true, 'alignment'=>'left');
                    $cetak[]= array('text'=>"Periode " . $periode, 'fontSize'=>8, 'bold'=>true, 'alignment'=>'left');
                    $cetak[]= chr(13) ;
                    $cetak[] = array('canvas' => array(array('type' => 'line', 'x1' => 0, 'y1' => 5, 'x2' => 515, 'y2' => 5, 'lineWidth' => 1)));
                    $cetak[]= chr(13);
                    $cetak[]= array('text'=>$title, 'fontSize'=>11, 'bold'=>true, 'alignment'=>'center');
                    $cetak[]= chr(13) ;
                    $cetak[] = array('canvas' => array(array('type' => 'line', 'x1' => 0, 'y1' => 5, 'x2' => 515, 'y2' => 5, 'lineWidth' => 1)));
                    $cetak[]= chr(13);
                    $cetak[]= pdf_tbl($arr['o'], $arr['b'], array('layout'=>'noBorders'), true, $vhead);
                    $cetak[] = array('canvas' => array(array('type' => 'line', 'x1' => 0, 'y1' => 5, 'x2' => 515, 'y2' => 5, 'lineWidth' => 1)));
                    $cetak[]= pdf_tbl($arr['to'], $arr['tb'], array('layout'=>'noBorders'), false);
                    $cetak[] = array('canvas' => array(array('type' => 'line', 'x1' => 0, 'y1' => 5, 'x2' => 515, 'y2' => 5, 'lineWidth' => 1)));
                    $cetak[]= pdf_tbl($arr['tbb'], $arr['tbbo'], array('layout'=>'noBorders'), false);
                    $cetak[] = array('canvas' => array(array('type' => 'line', 'x1' => 0, 'y1' => 5, 'x2' => 515, 'y2' => 5, 'lineWidth' => 1)));
                    $cetak[]= chr(13) . chr(13) ;
                    $cetak[]= pdf_tbl($arr['ttd'], $arr['ttdo'], array('layout'=>'noBorders'), false);
                    

                    foreach($rexlsx['sheets'] as $k => $v){
                        $judul[$v]['A1'] = array("t"=>"s", "v"=>'DAFTAR PAYROLL ' . $v) ;

                        $rexlsx['contentXlsx']['data'][$v] = $xlsx['DETIL'] ;
                        $rexlsx['contentXlsx']['tambahan'][$v]   = $judul[$v] ;
                        $rexlsx['contentXlsx']['origin'][$v]     = 3 ;
                    }
                }


                $this->set_response(array("content"=>$cetak, 'xlsx'=>$rexlsx), Bismillah_Controller::HTTP_OK);
           
            }else{
                $this->set_response(array(), Bismillah_Controller::HTTP_OK);
            }
        }else{
            $this->set_response(array(), Bismillah_Controller::HTTP_OK);
        }
    }    


}