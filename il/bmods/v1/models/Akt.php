<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
/**
 * @copyright   MDT SOLUTION
 * @link        https://ilmion.com
 * @author      Riski Aris
 * @version     0.0.1
 * @since       24 Jan 22
 */

class Akt extends Bismillah_Model{

//cek saldo terakhir / awal  
public function get_saldo($tgl,$kd_rekening,$kd_cabang='',$penihilan='T'){  
    if(empty($tgl)) $tgl = date('Y-m-d'); 
    if(empty($kd_cabang)) $kd_cabang = '01';
    

    $tgl     = date_2s($tgl);  
    $sum     = "debet-kredit" ; 
     
    if(substr($kd_rekening,0,1) == "2" or substr($kd_rekening,0,1) == "3" or substr($kd_rekening,0,1) == "4"){
        $sum = "kredit-debet" ;
    }
     
    
    $this->db->where('tgl <=', $tgl); 
    $this->db->where('kd_cabang', $kd_cabang);
    $this->db->like('kd_rekening',$kd_rekening, 'both');
    $db = $this->db->select("IFNULL(sum(".$sum."),0) as saldo")
               ->from("akt_bukubesar")
               ->get() ; 
    $r  = $db->row_array();

    //var_dump($r);

    return floatval($r['saldo']); 
  }

  //Cek saldo mutasi dalam range tanggal
    public function get_mutasi($tgl_awal,$tgl_akhir,$kd_rekening,$kd_cabang='',$penihilan='T'){   
      if($kd_cabang == '') $kd_cabang = '001';
  
      $tgl_awal   = date_2s($tgl_awal);  
      $tgl_akhir  = date_2s($tgl_akhir);  

      $sum        = "debet-kredit" ; 
      if(substr($kd_rekening,0,1) == "2" or substr($kd_rekening,0,1) == "3" or substr($kd_rekening,0,1) == "4"){
          $sum = "kredit-debet" ;
      }
  
      $this->db->where('tgl >=', $tgl_awal); 
      $this->db->where('tgl <=', $tgl_akhir); 
      $this->db->where('kd_cabang', $kd_cabang);
      $this->db->like('kd_rekening',$kd_rekening, 'after');
      $db = $this->db->select("IFNULL(sum(".$sum."),0) as saldo")
                 ->from("akt_jurnal")
                 ->get() ; 
      $r  = $db->row_array();
      return floatval($r['saldo']); 
    }
 
  //  HEAD
    function get_saldo_debet($tgl_awal,$tgl_akhir,$kd_rekening,$kd_rekening2 = '',$kd_cabang='',$penihilan="T"){
      if($kd_cabang == '') $kd_cabang = '01';
      $tgl_awal   = date_2s($tgl_awal);  
      $tgl_akhir  = date_2s($tgl_akhir);  
             
      $this->db->where('tgl >=', $tgl_awal); 
      $this->db->where('tgl <=', $tgl_akhir); 
      $this->db->where('kd_cabang', $kd_cabang);
      $this->db->like('kd_rekening',$kd_rekening, 'after');  
      $db  = $this->db->select("IFNULL(sum(debet),0) as debet")
                 ->from("akt_jurnal")
                 ->get() ;

                 $r  = $db->row_array();
      return floatval($r['debet']); 
                       
    }

    function updjurnalumum($tgl,$faktur,$rekening,$keterangan,$debet=0,$kredit=0,$cabang='',$updatebukubesar = true){
      //$cDateTime  = date('Y-m-d H:i:s') ;
      if($cabang == "")$cabang    = '001';
      $tgl = date_2s($tgl);
      $username  = $this->aruser['username'] ;
      if($debet + $kredit !== 0){
        $array = array('faktur'=>$faktur,'kd_cabang'=>$cabang,'kd_rekening'=>$rekening,'tgl'=>$tgl,'keterangan'=>$keterangan,'debet'=>$debet,'kredit'=>$kredit,'username'=>$username) ;
        $this->db->insert('akt_jurnal', $array);

        if($updatebukubesar){
          $this->updrekjurnal($faktur,$cabang) ; 
        }
      }
    }

    function updrekjurnal($faktur,$cabang=''){
      if($cabang == "")$cabang    = '001';
      $this->deletebukubesar($faktur) ;

      $this->db->where('faktur', $faktur);
      $db = $this->db->select("*")
                          ->from("akt_jurnal")
                          ->get(); 
      foreach($db->result_array() as $r){
        $this->updbukubesar($faktur, $cabang, $r['tgl'], $r['kd_rekening'], $r['keterangan'], $r['username'], $r['debet'], $r['kredit']) ;
      }                         
    }

    function updbukubesar($faktur, $cabang, $tgl, $rekening, $keterangan, $username, $debet=0, $kredit=0){
      if($cabang == "") $cabang = '001';
      $tgl = date_2s($tgl) ;
      if(($debet <> 0 || $kredit <> 0)){
        #insert ke buku besar
        $array = array("faktur"=>$faktur,"kd_cabang"=>$cabang,"kd_rekening"=>$rekening,"tgl"=>$tgl,"keterangan"=>$keterangan,"debet"=>$debet,"kredit"=>$kredit,"username"=>$username) ;
        $this->db->insert('akt_bukubesar', $array);
      }
    }

    function deletebukubesar($faktur){
      if(!empty($faktur)){
        $this->db->like('faktur',$faktur);
        $this->db->delete("akt_bukubesar");
      }
        
    }

    function deletejurnalumum($faktur){
      $this->db->like('faktur',$faktur);
      $this->db->delete("akt_jurnal");
      $this->deletebukubesar($faktur) ;
    }

    
  /* Upd Transaksi */
  public function updrekpb($faktur){
   
    $this->db->delete("akt_bukubesar", "faktur = '$faktur'");

    $this->db->where('t.faktur ', $faktur);
    $field = "t.hutang,t.kas,t.diskon,t.persppn,t.faktur,t.kd_cabang,t.tgl,t.kd_gudang,t.username,t.faktur_supplier,
              s.nama as namaSupplier";
    $db = $this->db->select($field) 
                                ->from("pb t")
                                ->join("supplier s","t.kd_supplier = s.kode","left") 
                                ->get(); 
    foreach($db->result_array() as $r){

      $this->db->where('p.faktur ', $faktur);
      $field1 = "p.faktur,p.kd_barang,p.qty,p.jumlah,p.hp,
                g.keterangan as keteranganGolongan,g.kd_rek_persediaan,g.kd_rek_hpp,g.kd_rek_penjualan,
                b.kd_golongan"; 
      $db1 = $this->db->select($field1)
                                  ->from("pb_barang p")
                                  ->join("stk_barang b","p.kd_barang = b.kode","left") 
                                  ->join("stk_barang_gol g","b.kd_golongan = g.kode","left")  
                                  ->get(); 
      foreach($db1->result_array() as $r1){

        $rekhutang  = $this->Bdb->getConfig("hutang",'') ;
        $rekkas     = "1.010.03" ;
        $rekdiskon  = "1.010.03" ;

        $key  = $r1['kd_golongan'] ;
        if(!isset($array[$key])){
          $array[$key] = array("RekeningPersediaan"   => $r1['kd_rek_persediaan'],
                              "RekeningHutang"       => $rekhutang,
                              "KeteranganPersediaan" => "Pembelian Barang ". $r1['keteranganGolongan'] ." ". $r['faktur_supplier'],
                              "JumlahPersediaan"     => 0) ;
        }    
        $array[$key]['JumlahPersediaan'] += $r1['hp']*$r1['qty'] ; 
      }

      foreach($array as $key=>$value){
        //Debet
        $this->updbukubesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$value['RekeningPersediaan'],$value['KeteranganPersediaan'],$r['username'],$value['JumlahPersediaan'],0);
      }
      
      $keteranganhutang = "Hutang Pembelian an. ". $r['namaSupplier'] ." ". $r['faktur_supplier'] ;
      $keterangankas    = "Kas Pembelian an. ". $r['namaSupplier'] ." ". $r['faktur_supplier'] ;
      $keterangandiskon = "Diskon Pembelian an. ". $r['namaSupplier'] ." ". $r['faktur_supplier'] ; 
      
      //Kredit
      $this->updbukubesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$rekhutang,$keteranganhutang,$r['username'],0,$r['hutang']);
      $this->updbukubesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$rekkas,$keterangankas,$r['username'],0,$r['kas']);
      $this->updbukubesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$rekdiskon,$keterangandiskon,$r['username'],0,$r['diskon']);
  
    } 
   
  }

  public function updrekpbrt($faktur){
   
    $this->db->delete("akt_bukubesar", "faktur = '$faktur'");

    $this->db->where('t.faktur ', $faktur);
    $field = "t.hutang,t.kas,t.diskon,t.persppn,t.faktur,t.kd_cabang,t.tgl,t.kd_gudang,t.username,t.faktur_pb,
              s.nama as namaSupplier";
    $db = $this->db->select($field)
                                ->from("pb_trt t")
                                ->join("supplier s","t.kd_supplier = s.kode","left") 
                                ->get(); 
    foreach($db->result_array() as $r){

      $this->db->where('p.faktur ', $faktur);
      $field1 = "p.faktur,p.kd_barang,p.qty,p.jumlah,p.hp,
                g.keterangan as keteranganGolongan,g.kd_rek_persediaan,g.kd_rek_hpp,g.kd_rek_penjualan,
                b.kd_golongan"; 
      $db1 = $this->db->select($field1)
                                  ->from("pb_trt_barang p")
                                  ->join("stk_barang b","p.kd_barang = b.kode","left") 
                                  ->join("stk_barang_gol g","b.kd_golongan = g.kode","left")  
                                  ->get(); 
      foreach($db1->result_array() as $r1){

        $rekhutang  = $this->Bdb->getConfig("hutang",'') ;
        $rekkas     = "1.010.03" ;
        $rekdiskon  = "1.010.03" ;

        $key  = $r1['kd_golongan'] ;
        if(!isset($array[$key])){
          $array[$key] = array("RekeningPersediaan"   => $r1['kd_rek_persediaan'],
                              "RekeningHutang"       => $rekhutang,
                              "KeteranganPersediaan" => "Retur Pembelian Barang ". $r1['keteranganGolongan'] ." ". $r['faktur_pb'],
                              "JumlahPersediaan"     => 0) ;
        }    
        $array[$key]['JumlahPersediaan'] += $r1['hp']*$r1['qty'] ; 
      }

      foreach($array as $key=>$value){
        //Kredit
        $this->updbukubesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$value['RekeningPersediaan'],$value['KeteranganPersediaan'],$r['username'],0,$value['JumlahPersediaan']);
      }
      
      $keteranganhutang = "Retur Hutang Pembelian an. ". $r['namaSupplier'] ." ". $r['faktur_pb'] ;
      $keterangankas    = "Retur Kas Pembelian an. ". $r['namaSupplier'] ." ". $r['faktur_pb'] ;
      $keterangandiskon = "Retur Diskon Pembelian an. ". $r['namaSupplier'] ." ". $r['faktur_pb'] ; 
      
      //Debet
      $this->updbukubesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$rekhutang,$keteranganhutang,$r['username'],$r['hutang'],0);
      $this->updbukubesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$rekkas,$keterangankas,$r['username'],$r['kas'],0);
      $this->updbukubesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$rekdiskon,$keterangandiskon,$r['username'],$r['diskon'],0);
  
    } 
   
  } 
  
  // Pelunasan Hutang
  public function updrekpelhtg($faktur){
   
    $this->db->delete("akt_bukubesar", "faktur = '$faktur'");

    //Note :
    //Hutang = total
    //faktur supp = faktur_pb

    $this->db->where('t.faktur ', $faktur);
    $field = "t.pembelian,t.retur,t.subtotal,diskon,t.pembulatan,t.total,t.bayar,t.faktur_pb,t.kd_cabang,r.tgl,
              s.nama as namaSupplier";
    $db = $this->db->select($field) 
                                ->from("gd_htg_pelunasan t")
                                ->join("gd_supplier s","t.kd_supplier = s.kode","left") 
                                ->get(); 
    foreach($db->result_array() as $r){

        $rekhutang              = $this->Bdb->getConfig("hutang",'') ;
        $rekpembulatan          = $this->Bdb->getConfig("pb_pembulatan",'') ;
        $rekdiskon              = $this->Bdb->getConfig("pb_diskon",'') ; 
        $rekkas                 = "1.010.03" ; 

        $r['faktur_pb']         = (isset($r['faktur_pb'])) ? "[".$r['faktur_pb']."]" : "" ;

        $keteranganhutang       = "Pelunasan hutang an. ". $r['namaSupplier'] ." ". $r['faktur_pb'] ;
        $keteranganpembulatan   = "Pembulatan pelunasan hutang an. ". $r['namaSupplier'] ." ". $r['faktur_pb'] ;
        $keterangandiskon       = "Diskon Pembelian an. ". $r['namaSupplier'] ." ". $r['faktur_pb'] ; 
         
        // Kredit
        $this->UpdBukuBesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$rekhutang,$keteranganhutang,$r['username'],$r['subtotal']);
        $this->UpdBukuBesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$rekpembulatan,$keteranganpembulatan,$r['username'],$r['pembulatan']);
          // Debet
          $this->UpdBukuBesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$rekkas,$keteranganhutang,$r['username'],0,$r['total']);
          $this->UpdBukuBesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$rekdiskon,$keterangandiskon,$r['username'],0,$r['diskon']);
  
    } 
   
  } 

  /* Penjualan */
  public function updrekpj($faktur){
   
    $this->db->delete("akt_bukubesar", "faktur = '$faktur'");

    $this->db->where('t.faktur ', $faktur);
    $field = "t.faktur,t.faktur_so,t.kas,t.piutang,t.kd_gudang,t.kd_cabang,t.username";
    $db = $this->db->select($field) 
                                ->from("gd_pj t")
                                ->get(); 
    foreach($db->result_array() as $r){

      $this->db->where('p.faktur ', $faktur);
      $field1 = "p.faktur,p.kd_barang,p.qty,p.jumlah,p.hp,
                g.keterangan as keteranganGolongan,g.kd_rek_persediaan,g.kd_rek_hpp,g.kd_rek_penjualan,
                b.kd_golongan";  
      $db1 = $this->db->select($field1)
                                  ->from("gd_pj_barang p")
                                  ->join("gd_barang b","p.kd_barang = b.kode","left") 
                                  ->join("gd_barang_gol g","b.kd_golongan = g.kode","left")  
                                  ->get(); 
      foreach($db1->result_array() as $r1){

        $rekpiutang     = $this->Bdb->getConfig("piutang",'') ;
        $rekkas         = "1.010.03" ;
        $rekpenjualan   = $r1['kd_rek_penjualan'] ;

        $key     = $r1['kd_golongan'] ;
        if(!isset($array[$key])){
          $array[$key] = array( "RekeningPersediaan"   => $r1['kd_rek_persediaan'],
                                "RekeningHPP"          => $r1['kd_rek_hpp'], 
                                "Keterangan"           => "HPP Penjualan Barang ". $r1['keteranganGolongan'] ." ". $r['faktur_so'],
                                "JumlahPersediaan"     => 0) ;
        }    
        $array[$key]['JumlahPersediaan'] += $r1['hp']*$r1['qty'] ; 
      }

      foreach($array as $key=>$value){
        //Debet
        $this->UpdBukuBesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$value['RekeningHPP'],$value['Keterangan'],$r['username'],$value['JumlahPersediaan'],0);
          //Kredit 
          $this->UpdBukuBesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$value['RekeningPersediaan'],$value['Keterangan'],$r['username'],0,$value['JumlahPersediaan']);
      }
      
      $keterangan2        = "Penjualan Barang ". $r['faktur_so'] ; 
      $keteranganomset    = "Omset Retur Penjualan Barang ". $r1['keteranganGolongan'] ." ". $r['faktur_so'] ;
      
      //Debet
      $this->UpdBukuBesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$rekkas,$keterangan2,$r['username'],$r['kas'],0);
      $this->UpdBukuBesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$rekpiutang,$keterangan2,$r['username'],$r['piutang'],0);
        //Kredit 
        $this->UpdBukuBesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$rekpenjualan,$keteranganomset,$r['username'],0,$r['kas']);
  
    } 
   
  }
  
  /* Penjualan retur */
  public function updrekpjrt($faktur){
   
    $this->db->delete("akt_bukubesar", "faktur = '$faktur'");

    $this->db->where('t.faktur ', $faktur);
    $field = "t.piutang,t.kas,t.diskon,t.persppn,t.faktur,t.kd_cabang,t.tgl,t.kd_gudang,t.username,t.faktur_pj";
    $db = $this->db->select($field)
                                ->from("gd_pj_retur t")
                                ->get(); 
    foreach($db->result_array() as $r){

      $this->db->where('p.faktur ', $faktur);
      $field1 = "p.faktur,p.kd_barang,p.qty,p.jumlah,p.hp,
                g.keterangan as keteranganGolongan,g.kd_rek_persediaan,g.kd_rek_hpp,g.kd_rek_penjualan,
                b.kd_golongan"; 
      $db1 = $this->db->select($field1)
                                  ->from("gd_pj_retur_barang p")
                                  ->join("gd_barang b","p.kd_barang = b.kode","left") 
                                  ->join("gd_barang_gol g","b.kd_golongan = g.kode","left")  
                                  ->get(); 
      foreach($db1->result_array() as $r1){

        $rekpiutang     = $this->Bdb->getConfig("piutang",'') ;
        $rekkas         = "1.010.03" ;
        $rekpenjualan   = $r1['kd_rek_penjualan'] ;

        $key     = $r1['kd_golongan'] ;
        if(!isset($array[$key])){
          $array[$key] = array( "RekeningPersediaan"    => $r1['kd_rek_persediaan'],
                                "RekeningHPP"           => $r1['kd_rek_hpp'], 
                                "Keterangan"            => "Retur HPP Penjualan Barang ". $r1['keteranganGolongan'],
                                "KeteranganPersediaan"  => "Retur HPP Penjualan Barang ". $r1['keteranganGolongan'] ."". $r['faktur_pj'] ,
                                "JumlahPersediaan"      => 0) ;
        }    
        $array[$key]['JumlahPersediaan'] += $r1['hp']*$r1['qty'] ; 
      }

      foreach($array as $key=>$value){
        //Debet
        $this->UpdBukuBesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$value['RekeningPersediaan'],$value['KeteranganPersediaan'],$r['username'],$value['JumlahPersediaan'],0);
          //Kredit 
          $this->UpdBukuBesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$value['RekeningHPP'],$value['Keterangan'],$r['username'],0,$value['JumlahPersediaan']);
      }
      
      $keterangan       = "Retur Penjualan Barang ". $r['faktur_pj'] ; 
      $keteranganomset  = "Retur Omset Penjualan Barang ". $r['faktur_pj'] ;
      
        //Kredit
        $this->UpdBukuBesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$rekkas,$keterangan,$r['username'],0,$r['kas']);
        $this->UpdBukuBesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$rekpiutang,$keterangan2,$r['username'],0,$r['piutang']);
      //Debet 
      $this->UpdBukuBesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$rekpenjualan,$keterangan,$r['username'],$r['kas'],0);
  
    } 
   
  }

  
  // Pelunasan Piutang
  public function updrekpelpiut($faktur){
   
    $this->db->delete("akt_bukubesar", "faktur = '$faktur'");

    $this->db->where('t.faktur ', $faktur);
    $field = "t.pembelian,t.retur,t.subtotal,diskon,t.pembulatan,t.total,t.bayar,t.faktur_pb,t.kd_cabang,r.tgl,
              s.nama as namaSupplier";
    $db = $this->db->select($field) 
                                ->from("gd_piut_pelunasan t")
                                ->join("gd_supplier s","t.kd_supplier = s.kode","left") 
                                ->get(); 
    foreach($db->result_array() as $r){

        $rekhutang              = $this->Bdb->getConfig("hutang",'') ;
        $rekpembulatan          = $this->Bdb->getConfig("pb_pembulatan",'') ;
        $rekdiskon              = $this->Bdb->getConfig("pb_diskon",'') ; 
        $rekkas                 = "1.010.03" ; 

        $r['faktur_pb']         = (isset($r['faktur_pb'])) ? "[".$r['faktur_pb']."]" : "" ;

        $keteranganhutang       = "Pelunasan hutang an. ". $r['namaSupplier'] ." ". $r['faktur_pb'] ;
        $keteranganpembulatan   = "Pembulatan pelunasan hutang an. ". $r['namaSupplier'] ." ". $r['faktur_pb'] ;
        $keterangandiskon       = "Diskon Pembelian an. ". $r['namaSupplier'] ." ". $r['faktur_pb'] ; 
         
        // Kredit
        $this->UpdBukuBesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$rekhutang,$keteranganhutang,$r['username'],$r['subtotal']);
        $this->UpdBukuBesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$rekpembulatan,$keteranganpembulatan,$r['username'],$r['pembulatan']);
          // Debet
          $this->UpdBukuBesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$rekkas,$keteranganhutang,$r['username'],0,$r['total']);
          $this->UpdBukuBesar($r['faktur'],$r['kd_cabang'],$r['tgl'],$rekdiskon,$keterangandiskon,$r['username'],0,$r['diskon']);
  
    } 
   
  } 





}

