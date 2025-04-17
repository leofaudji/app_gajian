<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
/**
 * @copyright   ALKANTARA
 * @link        https://ilmion.com
 * @author      Faudji
 * @version     0.0.2
 * @since       04 Okt 24
 */

class Gj extends Bismillah_Model{

  public function get_gaji($kode_kantor,$periode,$kode_kry,$golongan=""){  
    
    $va['periode']  = array('awal'=>date("Y-m-d"),'akhir'=>date("Y-m-d")) ; 
    $va['gaji']     = array('total_gaji'=>0) ;
    $va['absensi']  = array('masuk'=>0,'tidak_masuk'=>0,'potongan'=>0) ;    

    $this->db->where('kode', $periode);
    $db = $this->db->select("awal,akhir")
               ->from("gj_periode")
               ->get() ;
    $va['periode']  = $db->row_array() ; 


    // Ambil data absensi karyawan
    $this->db->where('a.kode_kry', $kode_kry);   
    $this->db->where('a.tgl >=', $va['periode']['awal'] ?? date("Y-m-d")) ; 
    $this->db->where('a.tgl <=', $va['periode']['akhir'] ?? date("Y-m-d")) ;           
    $db1 = $this->db->select("a.tgl,a.jam,m.status")    
                ->from("abs_tmp a")
                ->join("mst_abs_status m","m.kode = a.abs_status","left")
                ->get() ;
    foreach($db1->result_array() as $r){
      if($r['status'] == "0"){
        $va['absensi']['masuk']++ ;  
      }else if($r['status'] == "1"){
        $va['absensi']['tidak_masuk']++ ;  
      }else{
        $va['absensi']['potongan']++ ;    
      }
    }  

    // Nominal Gaji ambil dari per karyawan
    $this->db->where('a.periode', $periode);   
    $this->db->where('a.kode_kry', $kode_kry);
    $db1 = $this->db->select("a.komponen,a.nominal,m.perhitungan,m.dk")   
                ->from("gj_komponen_nominal_kry a")
                ->join("gj_komponen m","m.kode = a.komponen","left")
                ->get() ;
    $tambahan = 0 ;
    $potongan = 0 ;
    foreach($db1->result_array() as $r){
      $jumlah = $r['nominal'] ; 
      if(strtoupper($r['perhitungan']) == 'HARIAN') $jumlah = $r['nominal'] * $va['absensi']['masuk'] ;    
      if($r['dk'] == 'D') $tambahan += $jumlah ; 
      if($r['dk'] == 'K') $potongan += $jumlah ; 
      $va['gaji'][$r['komponen']]  = $r['nominal'] ?? 0 ;  
      $va['gaji']['tambahan']      = $tambahan ;     
      $va['gaji']['potongan']      = $potongan ;     
      $va['gaji']['total_gaji']    = $tambahan - $potongan ;     
    }  


    // Nominal Gaji default ambil dari golongan Gaji
    if($va['gaji']['total_gaji'] == 0){
      $this->db->where('a.kode_kantor', $kode_kantor);
      $this->db->where('a.golongan', $golongan); 
      $db = $this->db->select("a.komponen,a.nominal,m.perhitungan,m.dk") 
                ->from("gj_komponen_nominal a")
                ->join("gj_komponen m","m.kode = a.komponen","left")
                ->get() ;
      $tambahan = 0 ;
      $potongan = 0 ;
      foreach($db->result_array() as $r){
        $jumlah = $r['nominal'] ; 
        if(strtoupper($r['perhitungan']) == 'HARIAN') $jumlah = $r['nominal'] * $va['absensi']['masuk'] ;    
        if($r['dk'] == 'D') $tambahan += $jumlah ; 
        if($r['dk'] == 'K') $potongan += $jumlah ; 
        $va['gaji'][$r['komponen']]  = $r['nominal'] ;  
        $va['gaji']['tambahan']      = $tambahan ;     
        $va['gaji']['potongan']      = $potongan ;     
        $va['gaji']['total_gaji']    = $tambahan - $potongan ;      
      }  
    }

    

    
    return $va ;   
  }


}

// coba scm jenkins
