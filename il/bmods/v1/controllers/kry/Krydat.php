<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
class Krydat extends Bismillah_Controller{
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

        $this->lv_e = true;
        $this->lv_d = true;
        if($this->aruser['lv'] !== "0000"){
            $this->lv_e = boolval(strpos($this->aruser['menu_md5'], md5("krydat.e")));
            $this->lv_d = boolval(strpos($this->aruser['menu_md5'], md5("krydat.d")));
        }

        $bs = isset($va['bsearch']) ? $va['bsearch'] : array();
        $s = isset($va['search']) ? $va['search'][0]['value'] : ''; 
        $this->gr1_where($bs, $s);
        $db = $this->Bdb->db->select("count(id) jml")
                            ->from("mst_karyawan")
                            ->get();
        $r  = $db->row_array();
        if(isset($r)){
            $r['jml']   = intval($r['jml']);
            if($r['jml'] > 0){
                $re['total'] = $r['jml'];
                $this->gr1_where($bs, $s);
                $db = $this->Bdb->db->select("*")
                                    ->from("mst_karyawan")
                                    ->limit($va['limit'], $va['offset'])
                                    ->order_by('id ASC') 
                                    ->get();  
                foreach($db->result_array() as $r){
                    $r['recid'] = $r['id'];

                    $r['cmd']                    = '<div class="btn-group w-100">';
                    if($this->lv_e) $r['cmd']   .= '<button type="button" onClick=bo.krydat.edit("'.$r['kode'].'") class="btn btn-default btn-w2gr w-100">Koreksi</button>';
                    if($this->lv_d) $r['cmd']   .= '<button type="button" onClick=bo.krydat.delete("'.$r['id'].'") class="btn btn-danger btn-w2gr w-100">Hapus</button>';
                    $r['cmd']                   .= '</div>';

                    $r['umur']                   = hitung_umur($r['tgl_lahir']) ;
                    $r['ttl']                    = $r['tempat_lahir'] . ", " . date_2s($r['tgl_lahir'],"d-m-Y") ;  
                    //append
                    $re['records'][]   = $r ;  
                }
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

    public function index_post($kode){ 
        // saving
        $va = $this->post();
        $va['kode'] = $this->Bdb->getIncrement("karyawan-" . $va['kode_kantor'], true, 10);
        $va['username'] = $this->aruser['username'];
        //print_r($va) ; 
        $this->Bdb->upsert('mst_karyawan', $va, array("kode"=>$kode)) ; 

        // response
        $this->set_response(['saved' => true], Bismillah_Controller::HTTP_OK);
    }

    public function initkode_get(){ 
        $kode =  $this->Bdb->getIncrement("karyawan", false, 10);
        $this->set_response(['kode'=>$kode], Bismillah_Controller::HTTP_OK);

        // konversi data karyawan
        /*$conn = mysqli_connect("aa.akt.sis1.net","Assist","Irac","assist_akt") ;
        $db = mysqli_query($conn,"select * from hrd_karyawan order by NIP") ; 
        while($row = mysqli_fetch_array($db)){ 
            //$kode = $this->Bdb->getIncrement("karyawan-01", true, 10);
        
            $va = array("kode"=>$row['NIP'],"kode_lama"=>$row['NIP'],"tgl_masuk"=>$row['Tgl'],"nama"=>$row['Nama'],
                        "alamat_ktp"=>$row['Alamat'],"alamat_tinggal"=>$row['AlamatTinggal'],"agama"=>$row['Agama'],
                        "tempat_lahir"=>$row['TempatLahir'],"tgl_lahir"=>$row['TglLahir'],"ktp"=>$row['NoKTP'],"npwp"=>$row['NPWP'],
                        "email"=>$row['email'],"jenis_kelamin"=>$row['JenisKelamin'],"telepon"=>$row['NoTelp']) ;
            $this->Bdb->upsert('mst_karyawan', $va, array("kode"=>$row['NIP'])) ; 
        }*/
    }

}