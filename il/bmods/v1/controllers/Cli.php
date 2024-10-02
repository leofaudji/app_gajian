<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
class Cli extends CI_Controller{
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        // if(sync_srv !== 0 && sync_srv !== 1) define("__disalow_log", true);
        // harus cek dahulu apakah server 0 menyala
        $this->posting();
    }
 
    /**
     * POSTING HARIAN DILAKUKAN SETIAP JAM 11 MALAM
     */
    private function posting(){
        $time = array('mulai'=>date('Y-m-d H:i:s'), 'selesai'=>'');
        $this->load->model('v1/Bdb'); 
        $this->load->model('v1/Stok'); 
        $this->load->model('v1/Piut'); 
        $this->load->model('v1/Htg'); 

        $tgl = date('Y-m-d');
        $this->Stok->upd_posting($tgl);

        $time['selesai'] = date('Y-m-d H:i:s');
        $this->Bdb->saveConfig('waktu_posting', json_encode($time));
    }

    public function posting_tgl($tgl=''){
        $this->load->model('v1/Bdb'); 
        $this->load->model('v1/Stok'); 
        $this->load->model('v1/Piut'); 
        $this->load->model('v1/Htg'); 

        $this->Stok->upd_posting($tgl);
        $this->Htg->upd_posting($tgl);
        $this->Piut->upd_posting($tgl);
    }

    public function api_sync_2_db(){
        $this->load->model('v1/Bdb'); 
        $this->load->helper('directory');
        define("__disalow_log", true);

        $cfg = 'api_sync_2_db';
        $lproses = $this->Bdb->getConfig($cfg, '0');
        if( !boolval($lproses) ){
            $this->Bdb->saveConfig($cfg, '1');

            $tbl = $this->db->database . '_sync.api_sync';

            $dir = './tmp/.api_sync/';
            $vsql = directory_map($dir);
            asort($vsql); //urut nama
            
            foreach($vsql as $bf){
                $bf = $dir . $bf;
                $vs = json_decode( file_get_contents( $bf ), TRUE );
                // insert ke db_sync
                $this->db->insert($tbl, $vs);
                
                unlink($bf);
            }

            $this->Bdb->saveConfig($cfg, '0');
        }
    }

}