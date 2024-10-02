<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
class Conv extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('v1/Bdb') ; 
    }

    public function index(){
        $this->db->truncate('brg_stok');
        $tx = '2023-12-31';
        $db = $this->db->select('*')->from('_conv1708')->get();
        foreach($db->result_array() as $r){
            $sku = '#170821#' . $r['id'];
            $id_sat = $this->id_sat($r['satuan']);
            if($r['hj'] == 0) $r['hj'] = $r['hj_'];
            $vs = array('sku'=>$sku, 'nama'=>$r['nama'], 'id_kat'=>1, 'id_sat'=>$id_sat, 'tgl_ex'=>$tx, 'min'=>1, 'awal'=>$r['qty'], 
                        'akhir'=>$r['qty'], 'hp'=>$r['hj'], 'harga'=>$r['hj'], 'cabang'=>1);
            $this->db->insert('brg_stok', $vs);
            $idp = $this->db->insert_id();

            if($r['hj'] > 0 && $r['hj_'] > 0 && $r['hj'] > $r['hj_']){
                // bisa dijual dengan parent
                if($r['satuan'] == 'strip'){
                    $vs['id_sat'] = 3;
                }else{
                    $vs['id_sat'] = 1;
                }

                $vs['sku'] .= '#' . $idp;
                $vs['awal'] = 0;
                $vs['akhir'] = 0;
                $vs['hp'] = $r['hj_'];
                $vs['harga'] = $r['hj_'];
                $vs['datas'] = json_encode(array('konversi'=>array('id'=>$idp, 'qty'=>10, 'id_qty'=>1)));
                $this->db->insert('brg_stok', $vs);
            }
        }
    }

    private function id_sat($sat){
        $sat = strtoupper($sat);
        $id = $this->Bdb->getOne('id', 'mst_brg_sat', array('satuan'=>$sat));
        if($id == ''){
            $this->db->insert('mst_brg_sat', array('satuan'=>$sat));
            $id = $this->db->insert_id();
        }
        return $id;
    }
}