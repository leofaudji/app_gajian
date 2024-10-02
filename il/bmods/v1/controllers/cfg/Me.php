<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
class Me extends Bismillah_Controller{
    public function __construct(){
        parent::__construct();
        $this->auth();
        $this->load->model('v1/Bdb') ; 
    }

    public function savepass_post(){
        $va     = $this->post() ;  
        $va['password'] = pass_hash($va['password']);
        $va['updated_password'] = date('Y-m-d H:i:s');
        $this->Bdb->db->update('users', $va, array("username"=>$this->aruser['username'])) ;

        // response 
        $this->set_response(['saved' => true], Bismillah_Controller::HTTP_OK);
    } 

    public function upload_f_post(){
        $user   = $this->aruser['username'];
        $re     = array("success"=>true, "files"=>array() ,"message"=>array());
        $upl    = $this->config->item("bcore_uploads") . "profile/";
        $fcfg	= array("upload_path"=>$upl, "allowed_types"=>"jpeg|jpg|png", 'file_name'=>$user) ;
        @unlink($upl . $user . '.jpg'); @unlink($upl . $user . '.jpeg'); @unlink($upl . $user . '.png');

        $this->load->library('upload', $fcfg) ; 
        $foto   = '';
        foreach($_FILES as $key => $file){    
             $re["message"][]    =$this->upload->do_upload($key);
            if ( ! $this->upload->do_upload($key) ){
                $re["message"][]    = "Error file ke-". ($key+1);//. "  ".$this->upload->display_errors();
            }else{
                $data   = $this->upload->data();
                $data['url']    = base_url() . $upl . $data['orig_name'];
                $foto = $upl . $data['orig_name'];

                $re["files"][]  = $data; 
            }  
        }
        if($foto !== ''){
            $datas = $this->Bdb->getOne('datas', 'users', array('username'=>$user));
            $datas = json_decode($datas, true);
            $datas['foto'] = $foto;
            $this->Bdb->db->update('users', array('datas'=>json_encode($datas)), array('username'=>$user));
        }

        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }
} 