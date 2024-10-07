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
    
    $va['periode']  = array('awal'=>'','akhir'=>'') ; 
    $va['gaji']     = array('total_gaji'=>0) ;
    $va['absensi']  = array('masuk'=>0,'tidak_masuk'=>0,'potongan'=>0) ;    

    $this->db->where('kode', $periode);
    $db = $this->db->select("awal,akhir")
               ->from("gj_periode")
               ->get() ;
    $va['periode']  = $db->row_array() ; 


    // Nominal Gaji ambil dari per karyawan
    $this->db->where('kode_kry', $kode_kry);
    $db1 = $this->db->select("komponen,nominal")   
                ->from("gj_komponen_nominal_kry")
                ->get() ;
    foreach($db1->result_array() as $r){
      $va['gaji'][$r['komponen']]  = $r['nominal'] ;  
      $va['gaji']['total_gaji']   += $r['nominal'] ;     
    }  


    // Nominal Gaji default ambil dari golongan Gaji
    if($va['gaji']['total_gaji'] == 0){
      $this->db->where('kode_kantor', $kode_kantor);
      $this->db->where('golongan', $golongan); 
      $db = $this->db->select("komponen,nominal")
                ->from("gj_komponen_nominal")
                ->get() ;
      foreach($db->result_array() as $r){
        $va['gaji'][$r['komponen']]  = $r['nominal'] ;  
        $va['gaji']['total_gaji']   += $r['nominal'] ;     
      }  
    }

    // Ambil data absensi karyawan
    $this->db->where('kode_kry', $kode_kry);
    $this->db->where('tgl >=', $va['periode']['awal']) ; 
    $this->db->where('tgl <=', $va['periode']['akhir']) ;           
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

    
    return $va ;   
  }


}

