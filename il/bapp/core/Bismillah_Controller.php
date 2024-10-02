<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
/**
 * Bismillah Controller untuk Smartphone
 * 
 * @copyright   CV. ILMION KREATIF - ILMION STUDIO
 * @link        https://ilmion.com
 * @mail        hi@ilmion.com | ilmionstudio@gmail.com
 * @author      Mirza Ramadhany (amir.ramadhany@gmail.com)
 * @version     0.0.1
 * @since       17 Aug 18
 */

require_once APPPATH . '/libraries/REST_Controller.php';
require_once APPPATH . '/libraries/Format.php';
require_once APPPATH . '/libraries/JWT.php';
require_once APPPATH . '/libraries/BeforeValidException.php';
require_once APPPATH . '/libraries/ExpiredException.php';
require_once APPPATH . '/libraries/SignatureInvalidException.php';
use \Firebase\JWT\JWT;
use Restserver\Libraries\REST_Controller;

class Bismillah_Controller extends REST_Controller{
    public $aruser;
    public $token; 

    /**
     * Auth function
     * 
     * @param   string $page location page
     */
    public function auth($page='', $lcek=false, $token_name='bismillah_auth'){
        /**
         * JWT Auth middleware
         * Get Key From config
         */
        $token  = isset($_COOKIE[ $token_name ]) ? $_COOKIE[ $token_name ] : "bismillahtoken";
        $user   = $this->input->get_request_header('User-Agent');
        
        $key    = md5($this->config->item('encryption_key') . $user) ;
        
        try {
            $token = explode('bismillah', $token);
            $token = $token[0];
            $decoded = JWT::decode($token, $key, array('HS256'));
            $this->aruser   = (array) $decoded;
            $this->token    = $token ; 

            if(isset($this->aruser['datas'])){
                if($this->aruser['datas'] !== null){
                    $this->aruser['datas']  = get_object_vars($this->aruser['datas']);
                }else{
                    $this->aruser['datas']  = array('gudang'=>'','cabang'=>'');
                }
            }
            
            $menumd5= isset($_COOKIE['bismillah_md5']) ? $_COOKIE['bismillah_md5'] : "bismillahmd5";
            $this->aruser['menu_md5']    = $menumd5;

            if(isset( $this->aruser['lv'] )){
                // must cek page, is allowed?
                if( $this->aruser['lv'] !== "0000" && !$lcek){
                    $lcek = true;
                    if($page == '') $lcek = false;
                }
            }
            
            if($lcek){
                if($menumd5 == "bismillahmd5"){ // paksa untuk mengambil md5 dikarenakan data terlalu banyak 
                    $this->load->model('Bismillah_Model');
                    $armenu = array();
                    foreach(explode(',', $this->aruser['lv']) as $lv){
                        $db = $this->db->select('menus')->from('levels')->where('id', $lv)->get(); 
                        if($r = $db->row_array()){
                            $armenu[] = $r['menus'];
                        }
                    }
                    $menumd5 = implode(',', $armenu);
                    $this->aruser['menu_md5'] = $menumd5;
                }

                if($page !== ''){
                    $valid  = true;
                    if(boolval(strpos($page, ','))){
                        $valid = false;
                        $page  = explode(',', $page);
                        foreach($page as $p){
                            $p = trim($p);
                            if( boolval(strpos($menumd5, md5($p))) ) $valid = true;
                        }
                    }else{
                        $menumd5 = "mdt-".$menumd5;//tambahan risqi
                        $valid = boolval(strpos($menumd5, md5($page)));
                        $s = $page;                        
                        $s2 = $menumd5;

                    }

                    if(!$valid){
                        $invalid = ['status' => 'Not Allowed 1, must logout! ===='.$s ." ==== ".$s2]; 
                        $this->response($invalid, REST_Controller::HTTP_UNAUTHORIZED);
                    }
                }
            }
        } catch (Exception $e) { 
            $invalid = ['status' => $e->getMessage()]; 
            $this->response($invalid, REST_Controller::HTTP_UNAUTHORIZED);
        } 
    }
}

class Bismillah_HH_Controller extends REST_Controller{
    public $aruser;
    public $token; 

    /**
     * Auth function
     * 
     * @param   string $page location page
     */
    public function auth($page='', $lcek=false){
        /**
         * JWT Auth middleware
         * Get Key From config
         */
        $token  = isset($_COOKIE['bismillah_auth']) ? $_COOKIE['bismillah_auth'] : "bismillahtoken";
        $user   = $this->input->get_request_header('User-Agent');
        if($this->input->get_request_header('X-Bismillah-Api-Key') == 'bismillah-e777-7365-382b-d077-e03f-9ab8'){
            $mtoken = $this->input->get_request_header('X-Bismillah-Mobile-Token');
            if($mtoken !== '' && $mtoken !== NULL) $token = $mtoken;
        }
        
        $key    = md5($this->config->item('encryption_key') . $user) ;
        
        try {
            $decoded = JWT::decode($token, $key, array('HS256'));
            $this->aruser   = (array) $decoded;
            $this->token    = $token ; 

            if(isset($this->aruser['datas'])){
                if($this->aruser['datas'] !== null){
                    $this->aruser['datas']  = get_object_vars($this->aruser['datas']);
                }else{
                    $this->aruser['datas']  = array('nip'=>'','nis'=>'');
                }
            }
            
            $menumd5= isset($_COOKIE['bismillah_md5']) ? $_COOKIE['bismillah_md5'] : "bismillahmd5";
            $this->aruser['menu_md5']    = $menumd5;

            if(isset( $this->aruser['lv'] )){
                // must cek page, is allowed?
                if( $this->aruser['lv'] !== "0000" && !$lcek){
                    $lcek = true;
                    if($page == '') $lcek = false;
                }
            }
            
            if($lcek){
                if($menumd5 == "bismillahmd5"){ // paksa untuk mengambil md5 dikarenakan data terlalu banyak 
                    $this->load->model('Bismillah_Model');
                    $armenu = array();
                    foreach(explode(',', $this->aruser['lv']) as $lv){
                        $db = $this->db->select('menus')->from('levels')->where('id', $lv)->get(); 
                        if($r = $db->row_array()){
                            $armenu[] = $r['menus'];
                        }
                    }
                    $menumd5 = implode(',', $armenu);
                    $this->aruser['menu_md5'] = $menumd5;
                }

                if($page !== ''){
                    $valid  = true;
                    if(boolval(strpos($page, ','))){
                        $valid = false;
                        $page  = explode(',', $page);
                        foreach($page as $p){
                            $p = trim($p);
                            if( boolval(strpos($menumd5, md5($p))) ) $valid = true;
                        }
                    }else{
                        $valid = boolval(strpos($menumd5, md5($page)));
                    }

                    if(!$valid){
                        $invalid = ['status' => 'Not Allowed 2, must logout!']; 
                        $this->response($invalid, REST_Controller::HTTP_UNAUTHORIZED);
                    }
                }

                // cek untuk id sekolah jika tidak memiliki hak maka harus keluar
                $this->aruser['sid_sekolah'] = $this->input->get_request_header('sid_sekolah');
                $this->aruser['sid_tapel'] = $this->input->get_request_header('sid_tapel');
            }
        } catch (Exception $e) { 
            $invalid = ['status' => $e->getMessage()]; 
            $this->response($invalid, REST_Controller::HTTP_UNAUTHORIZED);
        } 
    }
}

