<?php defined('BASEPATH') OR exit('بسم الله الرحمن الرحيم');

class Bismillah extends CI_Controller {
	public function index(){	
		$va 	= array("status"=>200, "message"=>"Bismillah Sukses Dunia Akhirat. CV. ILMION KREATIF - " . date("Y") ) ; 
		header('Content-Type: application/json');
		ob_clean() ; 
		echo(json_encode($va)) ;   
	}  
} 
?>