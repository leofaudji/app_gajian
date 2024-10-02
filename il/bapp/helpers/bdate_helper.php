<?php defined('BASEPATH') OR exit('بسم الله الرحمن الرحيم');
/**
 * BDate Helper
 * 
 * @copyright   CV. ILMION KREATIF - ILMION STUDIO
 * @link        https://ilmion.com
 * @mail        hi@ilmion.com | ilmionstudio@gmail.com
 * @author      Mirza Ramadhany (amir.ramadhany@gmail.com)
 * @version     0.0.1
 * @since       17 Aug 18
 * @access      public       
 */

function date_2s($date, $option='Y-m-d'){
	$newdate 	= new DateTime($date) ;
	$result 	= $newdate->format($option) ;
	if ($result) {
		return  $result;
	} else { // format failed
		return  "Unknown Time";
	}
}
function date_2d($tgl){
    $time   = strtotime($tgl) ;

    return date('d-m-Y', $time) ;
}
function date_eom($tgl){
    $time   = strtotime($tgl) ;
    $month  = intval(date('m', $time)) ;
    $year   = date('Y', $time) ;

    return date('Y-m-d', mktime(0, 0, 0, $month+1, 0, $year)) ;
}

function date_bom($tgl){
    $time   = strtotime($tgl) ;
    $month  = date('m', $time) ;
    $year   = date('Y', $time) ;

    return date('Y-m-d', mktime(0, 0, 0, $month, 1, $year)) ;
}

function date_set($lt=false){
    $cf	= 'DD-MM-YYYY' ;
    if($lt)
        $cf .= ' HH:mm:ss' ;
    return 'data-date-format="'.$cf.'"' ;
}

function date_day($v, $l=0){
    $va  = array("Minggu","Senin","Selasa","Rabu","Kamis","Jum'at","Sabtu") ;
    if($l == 2) $va  = array("Mg","Sn","Sl","Rb","Km","Jm","Sb") ;
    return strtoupper($va[$v]) ;
}

function date_month($v, $l=0){
    $v  = intval($v); 
    $va = array("Januari","Februari","Maret","April","Mei","Juni","Juli",
                "Agustus","September","Oktober","November","Desember") ;
    if($l==1) $va = array("I","II","III","IV","V","VI","VII", "VIII","IX","X","XI","XII") ;
    $key = max(0,$v-1) ;
    return $va[$key] ;
}

function date_2b($date=''){
    //date 2 bahasa
    if($date == "") $date = date("Y-m-d");
    $vad = getdate(strtotime($date)) ;
    $va  = array("d"=>$vad['mday'],"day"=> date_day($vad['wday']),
                "m"=> date_month($vad['mon']),"y"=>$vad['year']) ;
    return $va ;
}

function date_nextmonth($date, $n){
    $date   = date_2s($date) ;
    $time   = strtotime($date) ;
    $day    = date("d",$time) ;
    $month  = date("m",$time) ;
    $year   = date("Y",$time) ;
  
    $n1 = mktime(0,0,0,$month + $n,$day,$year) ;
    $n2 = mktime(0,0,0,$month+$n+1,0,$year) ;

    return min($n1,$n2) ;
}

function date_nextday($date, $n){
    $date   = date_2s($date) ;
    $time   = strtotime($date) ;
    $day    = date("d",$time) ;
    $month  = date("m",$time) ;
    $year   = date("Y",$time) ;
  
    $n1 = mktime(0,0,0,$month, intval($day) + $n, $year) ;

    return $n1;
}

function date_keday($tglawal, $tglakhir){
    $tglawal = date_2s($tglawal) ;
    $tglakhir= date_2s($tglakhir) ;

    $ke       = 0 ;
    for($tgl=$tglawal; $tgl<=$tglakhir; $tgl=date('Y-m-d', date_nextday($tgl, 1))){
        $ke++ ;
    }

    return $ke ;
}

function date_ke($tglawal, $tglakhir){
    $tglawal = date_2s($tglawal) ;
    $tglakhir= date_2s($tglakhir) ;

    $ke       = 0 ;
    for($tgl=$tglawal; $tgl<=$tglakhir; $tgl=date('Y-m-d', date_nextmonth($tgl, 1))){
        $ke++ ;
    }

    return $ke ;
}

function hitung_umur($tanggal_lahir){
	$birthDate = new DateTime($tanggal_lahir);
	$today = new DateTime("today");
	if ($birthDate > $today) { 
	    //exit("0 tahun 0 bulan 0 hari");
	}
	$y = $today->diff($birthDate)->y;
	$m = $today->diff($birthDate)->m;
	$d = $today->diff($birthDate)->d;
	return $y." Tahun " ; //.$m." bulan ".$d." hari";
}
?>
