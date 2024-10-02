<?php defined('BASEPATH') OR exit('بسم الله الرحمن الرحيم');
class Bdb extends Bismillah_Model{
    public function getlastfaktur($key, $tgl, $lupdate=false, $kd_cabang=''){
            
        $kd_cabang = ($kd_cabang == '') ? $this->aruser['datas']['kd_cabang'] : $kd_cabang;
        
        $key = $key. $kd_cabang .date("ymd",strtotime($tgl));
        return $key . $this->Bdb->getIncrement($key, $lupdate, 5);
        
        
    }
}

//fdiv hanya untuk php 8 
function fdiv1($a,$b){
    $nRetval = 0 ;
    if(empty($a) || empty($b) || $a == 0 || $b == 0){
      $nRetval = 0 ;
    }else{
      $nRetval = $a / $b ;
    }
    return $nRetval ;
} 

