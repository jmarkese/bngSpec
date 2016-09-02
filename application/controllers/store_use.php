<?php

class Store_Use extends CI_Controller {


	function Store_Use(){
	
		parent::__construct();
	
	}

	
	public function index(){

	}

	
	public function create (){

		$this->load->model('store_use_items');
		$this->store_use_items->create_item();
				
	}
	
	public function get_items ($location_id = NULL){
		
		$this->load->library('Array2XML');
		$this->load->model('store_use_items');
		$items = $this->store_use_items->get_items($location_id);
		$xml = Array2XML::createXML('items', $items);
		echo $xml->saveXML();
		
	}

	public function approve ($item_id = NULL, $value = NULL){
		
		$this->load->model('store_use_items');
		$approve = $this->store_use_items->approve_item($item_id, $value);
		
	}

	public function send ($item_id = NULL, $value = NULL){
		
		$this->load->model('store_use_items');
		$send = $this->store_use_items->send_item($item_id, $value);
		
	}

	public function substitute ($item_id = NULL, $value = NULL){
		
		$this->load->model('store_use_items');
		$send = $this->store_use_items->sub_item($item_id, $value);
		
	}

	public function delete ($item_id = NULL){
		
		$this->load->model('store_use_items');
		$delete = $this->store_use_items->delete_item($item_id);
		
	}
	
	public function optionsXML (){
	
		$data["categories"] = array();
		$categories = $this->categoryOptionsData();
		array_push($data["categories"], $categories);
		
		$xml = Array2XML::createXML('options', $data);
		echo $xml->saveXML();	
		
	}
	
	function categoryOptionsData (){
		
		// Initialize the array we will return 
		$data["category"] = array();

		//Get the category options from DB and put the in an array for a form dropdown
		$this->db->order_by("order", "asc"); 
		$query = $this->db->get_where('store_use_category', array('is_active' => true));

		// loop through the result set and add the items to the array
		foreach ($query->result_array() as $row){
			$push = array(	
							"@attributes" => array(
								"id" => $row["su_cat_id"],
              					"name" => $row["name"], 
								"category_code" => $row["short_name"], 
								"order" => $row["order"]
            			),
						"@value" => $row["su_cat_id"]					
			);
			array_push($data["category"], $push);
		}
		
		// return the Array
		return $data;
	}

} 
?>