<?php defined('BASEPATH') OR exit('بسم الله الرحمن الرحيم');
/**
 * BCore Helper
 * 
 * @copyright   CV. ILMION KREATIF - ILMION STUDIO
 * @link        https://ilmion.com
 * @mail        hi@ilmion.com | ilmionstudio@gmail.com
 * @author      Mirza Ramadhany (amir.ramadhany@gmail.com)
 * @version     0.0.1
 * @since       17 Aug 18 
 * @access      public       
 */

// set default time zone
date_default_timezone_set('Asia/Jakarta') ;

/**
 * @param   string password
 * @return  string key encrypt
 */
function pass_key($p){
    return sha1((md5($p.md5($p)) . ord('b') . ord('b') . "BISMILLAHSUKSESDUNIAAKHIRATAAMIIN") );  
}

/**
 * @param   string password
 * @return  string encrypt
 */
function pass_hash($p){
    $key    = pass_key($p) ;
    return password_hash($p . $key, PASSWORD_BCRYPT, ['cost' => 4]) ;
}

/**
 * @param   string password
 * @param   string password encrypt
 * @return  boolean
 */
function pass_verify($p, $h){
    $key    = pass_key($p) ;
    return password_verify($p.$key, $h) ;
}

/**
 * Set cookies function
 * 
 * @param   string $name name
 * @param   string $val value
 * @param   string $dur duration
 * @param   string $path path must be according url without localhost
 * @return  bool 
 */
function cookie($name, $val, $dur, $path="", $server_name=''){
    if($server_name == '')
        $server_name = $_SERVER["SERVER_NAME"];
        
    if($path == "") 
        $path   = substr($_SERVER['REQUEST_URI'], 0 , strpos($_SERVER['REQUEST_URI'], "v1")) ; 

    // disable security for http
    // enable security for https
    setcookie($name, $val, time()+$dur, $path, $server_name, false, true);
}

/**
 * image 2 base64
 * 
 * @param   string $path location
 * @return  string base64
 */
function image2base($path){
    $path   = $path;
    $type   = pathinfo($path, PATHINFO_EXTENSION);
    $data   = file_get_contents($path);
    return 'data:image/' . $type . ';base64,' . base64_encode($data);
}

/**
 * tmp file
 * 
 * @param   string  $folder namae
 * @param   array   $va data kiriman
 * @return  string  Hasil Folder TMP
 */
function tmp_file($folder, $va){
    $CI     =& get_instance();
    $tmp    = $CI->config->item('bcore_tmp');
    $folder = $tmp . $folder . "/";
    @mkdir($folder,0777,true) ; 
    $folder.= md5(json_encode($va) . date("Ymd")) . ".bismillah"; 
    return $folder;
}

/**
 * Array to PDF Table
 */
function pdf_tbl($opt, $data, $attr=array(), $showHeader=true, $vaheader=array()){
    // Looping option sebagai header dan text attr
    $vo     = array('col'=>array(), 'widths'=>array());
    pdf_tbl_opt($vo, $opt);
    $body   = array();
    //print_r($data) ;
    pdf_tbl_body($body, $vo['col'], $data);
    //print_r($body) ;
    $cheader = 0 ;
    if($showHeader){
        if(empty($vaheader)){
            $vo['h']= pdf_arr2h($opt);
        }else{
            $vo['h']= $vaheader ;
        }
        
        $body   = array_merge($vo['h'], $body);
        $cheader= count($vo['h']) ;
    }
    $tbl    = array("table"=>array( "headerRows"=>$cheader, "widths"=> $vo['widths'], "body"=> $body));
    if( ! empty($attr) ){
        $tbl= array_merge($attr, $tbl);
    }
    return $tbl;
}

function pdf_tbl_body(&$va, $col, $data){
    foreach($data as $key => $val){
        $arr = array();
        foreach($col as $f => $opt){
            $isi    = "";
            if( isset($val[$f]) ){
                if(isset($opt['image']) && $val[$f] !== "") $val[$f]['image'] = image2base($val[$f]['image']);
                $isi= $val[$f];
            }else if( strpos($f, ";") ){ // loop
                $isi    = $val ;
                foreach(explode(";", $f) as $fk => $fv){
                    if(isset( $isi[$fv] )){
                        if(isset($opt['image']) && $isi[$fv] !== "") $isi[$fv]['image'] = image2base($isi[$fv]['image']);
                        $isi    = $isi[$fv];
                    }else{
                        $isi    = "";
                    }
                }
            }

            if( !is_array($isi) ){
                $isi= array_merge( array("text"=>$isi),$opt);
            }
            $arr[]  = $isi;
        }
        $va[]     = $arr;
    }
}

function pdf_tbl_opt(&$vo, $opt, $parent=""){
    foreach($opt as $key => $val){
        if( is_array($val) && ! isset($val['width']) ){
            pdf_tbl_opt($vo, $val, $parent . $key . ";" );
        }else{
            $vo['widths'][] = $val['width'];
            unset($val['width']);
            $vo['col'][$parent.$key]= $val;
        }
    }
}

function pdf_attr($ds){
    $df = array("th"=>array("style"=>"th"), 
                "r"=>array("alignment"=>"right"),
                "c"=>array("alignment"=>"center"),
                "b"=>array("bold"=>"true"),
                "i"=>array("italics"=>"true"),
                "u"=>array("underline"=>"true"));
    $re = isset($df[$ds]) ? $df[$ds] : "";
    return $re;
}

/**
 * Set array awal untuk pdf
 * 
 * @param   string $text
 * @param   enum $ds 
 * @param   array $style default style
 * @return  array default
 */
function pdf_text($text, $ds='th', $attr=array()){
    if(is_array($text)){
        $re = $text;
    }else{
        $re = array("text"=>$text);
    }
    
    // get style
    foreach(explode(",", $ds) as $key => $def){
        $def    = trim($def);
        if( pdf_attr($def) !== "" ) $re    = array_merge_recursive($re, pdf_attr($def) );
    }

    // new attr 
    if(!empty($attr)){
        $re = array_merge_recursive($re, $attr);
    }

    return $re;
}

/**
 * Set Pdf Array to Header
 */
function pdf_arr2h($arr){
    $vhead  = array();
    pdf_arr2h_arr($vhead, $arr);
    return $vhead;
}

function pdf_arr2h_arr(&$vhead, $arr, $v=array('ke'=>0, 'null'=>0, 'lnull'=>false)){
    $lke= false;
    $ke = $v['ke'];
    
    if($v['null'] > 0 && !$v['lnull']) $vhead[$ke]  = array_fill(0, $v['null'], '');

    if($ke > 0){
        for($i = $ke-1; $i >= 0; $i--){
            for($o = 0; $o < $v['null']; $o++){
                $val    = $vhead[$i][$o]; 
                if($val !== "" && is_array($val)){
                    $np     = (isset($val['rowSpan'])) ? $val['rowSpan']++ : 2;
                    $vhead[$i][$o]['rowSpan'] = $np;
                }
            }
        }
    }

    // loop head
    foreach($arr as $okey => $visi){
        if( ! $lke ){
            $lke    = true;
            $v['ke']++;
        }
        if( is_array($visi) && !isset($visi['text'])  && !isset($visi['image'])  && !isset($visi['width']) ){
            $count  = count($visi);
            $opt = array("text"=> $okey, "style"=>"th", "colSpan"=> $count) ;
            if(isset($visi['fontSize'])) $opt['fontSize'] = $visi['fontSize']; 
            if(isset($visi['caption'])) $opt['text'] = $visi['caption']; 
            $vhead[$ke][] = $opt ;
            for($o = 1; $o < $count; $o++){
                $vhead[$ke][] = ''; 
            }
            
            pdf_arr2h_arr($vhead, $visi, $v);
            $v['lnull'] = true;
        }else{
            //if(is_array($visi)) $visi = $okey;
            $opt = array("text"=> $okey, "style"=>"th") ;
            if(isset($visi['fontSize'])) $opt['fontSize'] = $visi['fontSize']; 
            if(isset($visi['caption'])) $opt['text'] = $visi['caption']; 
            $vhead[$ke][] = $opt;
            $v['null']++;
        }
    }
}

function parse_number($number, $dec_point=null) {
    if (empty($dec_point)) {
        $locale = localeconv();
        $dec_point = $locale['decimal_point'];
    }
    return floatval(str_replace($dec_point, '.', preg_replace('/[^\d'.preg_quote($dec_point).']/', '', $number)));
}

function konversi($x){
    $x = abs($x);
    $angka = array ("","satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $temp = "";
    
    if($x < 12){
        $temp = " ".$angka[$x];
    }else if($x<20){
        $temp = konversi($x - 10)." belas";
    }else if ($x<100){
        $temp = konversi($x/10)." puluh". konversi($x%10);
    }else if($x<200){
        $temp = " seratus".konversi($x-100);
    }else if($x<1000){
        $temp = konversi($x/100)." ratus".konversi($x%100);   
    }else if($x<2000){
        $temp = " seribu".konversi($x-1000);
    }else if($x<1000000){
        $temp = konversi($x/1000)." ribu".konversi($x%1000);   
    }else if($x<1000000000){
        $temp = konversi($x/1000000)." juta".konversi($x%1000000);
    }else if($x<1000000000000){
        $temp = konversi($x/1000000000)." milyar".konversi($x%1000000000);
    }
    
    return $temp;
}
    
function tkoma($x){
    $str = stristr($x,".");
    $ex  = explode('.',$x);

    $a   = 0 ;
    if(($ex[1]/10) >= 1){
        $a = abs($ex[1]);
    }
    $string = array("nol", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan",   "sembilan","sepuluh", "sebelas");
    $temp = "";

    $a2 = $ex[1]/10;
    $pjg = strlen($str);
    $i =1;

    if($a>=1 && $a< 12){   
        $temp .= " ".$string[$a];
    }else if($a>12 && $a < 20){   
        $temp .= konversi($a - 10)." belas";
    }else if ($a>20 && $a<100){   
        $temp .= konversi($a / 10)." puluh". konversi($a % 10);
    }  
    return $temp;
}
   
function terbilang($x, $lrupiah=false){
    if($x<0){
        $hasil = "minus ".trim(konversi(x));
    }else{
        $poin = trim(tkoma($x));
        $hasil = trim(konversi($x));
    }

    if($poin){
        $hasil = $hasil." koma ".$poin;
    }else{
        $hasil = $hasil;
    }

    if($lrupiah) $hasil .= " rupiah" ;

    return ucwords($hasil) ;  
}

function string_replace($replace,$text){
    foreach ($replace as $key => $val) {
      $text  = str_replace("{{". $key ."}}", $val, $text) ; 
    }
    return $text ; 
}

function arrunset(&$arr, $arrunset){
    foreach($arrunset as $key => $r){
        unset($arr[$r]);
    }
}

function rand_color() {
    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}

function wopen_file($loc){
    $re= '';
    if( boolval(strpos($loc, '.pdf')) ){
        $re = str_replace('/il', '', base_url()) . 'index_flipbook.html#' . str_replace('./', '', $loc);
    }else{
        $re = base_url() . $loc;
    }
    return $re;
}

function bfile_ico($ext){
    $re  = 'fa-file-archive-o';
    if($ext == 'pdf'){
        $re  = 'fa-file-pdf-o';
    }else if($ext == 'xls' || $ext == 'xlsx'){
        $re  = 'fa-file-excel-o';
    }else if($ext == 'doc' || $ext == 'docx'){
        $re  = 'fa-file-word-o';
    }else if($ext == 'ppt' || $ext == 'pptx'){
        $re  = 'fa-file-powerpoint-o';
    }  
    return $re;
}

function bfile_div($loc, $caption=''){
    $div  = '';
    $vinfo = pathinfo($loc);
    $vinfo = strtolower($vinfo['extension']);
    if(strpos($loc, 'http') === false){
        if($vinfo == 'pdf'){
            $loc = str_replace('/il', '', base_url()) . 'index_flipbook.html#' . str_replace('./', '', $loc);
        }else{
            $loc = base_url() . $loc;
        }
    }

    if($vinfo == 'jpg' || $vinfo == 'jpeg' || $vinfo == 'png'){
        $div = bfile_div_img($loc, $caption);
    }else{
        $vcap= explode(' - ', $caption);
        if(!isset($vcap[0])) $vcap[0] = 'Lainnya';
        if($vcap[0] == 'Lainnya') $vcap[0] = $caption;
        $ico = bfile_ico($vinfo);
        $div = '<a href="'.$loc.'" target="_new">';
        $div.=  '<i class="fa '.$ico.'"></i>';
        $div.=  '<div class="hint-text small m-t-5">'.$vcap[0].'</div>';
        $div.= '</a>';
    }
    return $div;
}

function bfile_div_img($loc, $caption=''){
    $loc = $loc;
    $div = '<a href="'.$loc.'" data-fancybox="gallery" data-caption="'.$caption.'">';
    $div.=  '<img src="'.$loc.'" alt="'.$caption.'" class="img-fluid"/>';
    $div.= '</a>';

    return $div;
}

function getname_file($file){
    $file = preg_replace( '%\s*[-_\s]+\s*%', ' ',  $file);
    $file = ucwords( strtolower($file) );
    return $file;
}

function time_interval($time_now, $date_created){
    $date = new DateTime($date_created);
    $time_created = $date->getTimestamp();

    $time   = $time_now - $time_created ;

    $jam    = ($time - ($time % 3600)) / 3600 ;
    $menit  = ($time - ($time % 60) - ($jam * 3600)) / 60 ;
    $detik  = $time % 60 ;

    $waktu  = str_pad($jam, 2, "0", STR_PAD_LEFT) . ":" . str_pad($menit, 2, "0", STR_PAD_LEFT) . ":" . str_pad($detik, 2, "0", STR_PAD_LEFT) ;

    return $waktu ;
}
