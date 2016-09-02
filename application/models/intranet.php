<?php

class Intranet extends CI_Model {


    function __construct() {
	
		parent::__construct();
	
	}

	function locationsData (){
		
		// Initialize the array we will return 
		$data["location"] = array();

		//Get the category options from DB and put the in an array for a form dropdown
		$this->db->order_by("location_id", "asc"); 
		$query = $this->db->get_where('location');

		// loop through the result set and add the items to the array
		foreach ($query->result_array() as $row){
			$push = array(	
							"@attributes" => array(
								"location_id" => $row["location_id"],
              					"location_name" => $row["location_name"], 
            			),
						"@value" => $row["location_id"]					
			);
			array_push($data["location"], $push);
		}
		
		// return the Array
		return $data;
	}

	function storeLocationsData (){
		
		// Initialize the array we will return 
		$data["location"] = array();

		//Get the category options from DB and put the in an array for a form dropdown
		$this->db->order_by("location_id", "asc"); 
		$query = $this->db->get_where('location','location_id != 1');

		// loop through the result set and add the items to the array
		foreach ($query->result_array() as $row){
			$push = array(	
							"@attributes" => array(
								"location_id" => $row["location_id"],
              					"location_name" => $row["location_name"], 
            			),
						"@value" => $row["location_id"]					
			);
			array_push($data["location"], $push);
		}
		
		// return the Array
		return $data;
	}

}
?>