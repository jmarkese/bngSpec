<?php

class Intranet_Utils extends CI_Controller {


	function Intranet_Utils(){
	
		parent::__construct();
	
	}

	
	public function index(){

		$this->load->library('Array2XML');
		is_logged_in();
		redirect(base_url('/bin-debug/AltgardenInternal.html'), 'refresh');
		
	}

	
	public function locationsXML(){
		
		$this->load->model('intranet');
		
		$locations = $this->intranet->locationsData();
		$xml = Array2XML::createXML('locations', $locations);
		echo $xml->saveXML();
			
	}
	
	public function storesXML(){
		
		$this->load->model('intranet');
		
		$locations = $this->intranet->storeLocationsData();
		$xml = Array2XML::createXML('locations', $locations);
		echo $xml->saveXML();
			
	}

	
	function sessionXML(){
	
		$data["session"] = array();
		$session = $this->session->all_userdata();
		array_push($data["session"], $session);
		$xml = Array2XML::createXML('sessionData', $data);
		echo $xml->saveXML();
	
	}
	
	
	function is_logged_in(){
		
		$is_logged_in = $this->session->userdata('is_logged_in');
		
		if(!isset($is_logged_in) || $is_logged_in != true){
			$data["session"] = array();
			$session = $this->session->all_userdata();
			array_push($data["session"], $session);
			$data["isLoggedIn"] = false;
			
			$xml = Array2XML::createXML('sessionData', $data);
			echo $xml->saveXML();
			
			//echo 'You don\'t have permission to access this page. ' . anchor('login', 'Login');	
			//$this->load->view('login_form');
			
			die();
			
		} else {
			
			// Do something else
			
		}
				
	}


} 
?>