<?php

class Store_Use_Items extends CI_Model {

    var $title   = '';
    var $content = '';
    var $date    = '';

    function __construct() {
	
		parent::__construct();
	
	}
	
	function create_item() {
		/*foreach($_POST as $key=>$value) {
			echo "$key: $value\n";
		}*/
		
		$item_data = array(
			'fk_location_id' => $this->input->post('location_id'),
			'part_num' => $this->input->post('part_num'),
			'item_desc' => $this->input->post('item_desc'),
			'qty' => $this->input->post('qty'),
			'employee_name' => $this->input->post('employee_name'),
			'notes' => $this->input->post('notes'),
			'fk_su_category' => $this->input->post('category')
		);
		
		$insert = $this->db->insert('store_use_items', $item_data);
		return $insert;
		
	}
	
	function update_item($itemId) {
		
		$item_data = array(
			'part_num' => $this->input->post('part_num'),
			'item_desc' => $this->input->post('item_desc'),
			'qty' => $this->input->post('qty'),
			'employee_name' => $this->input->post('employee_name'),
			'reason' => $this->input->post('reason')			
		);
		
		$this->db->where('item_id', $itemId);
		$update = $this->db->update('store_use_items', $item_data);
		return $update;
		
	}
	
	function approve_item($itemId, $value) {
		
		$value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
		
		$item_data = array(
			'approved' => $value
		);
		
		$this->db->where('item_id', $itemId);
		$update = $this->db->update('store_use_items', $item_data);
		return $update;
	
	}

	function send_item($itemId, $value) {

		$value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
		
		$item_data = array(
			'sent' => $value
		);
		
		$this->db->where('item_id', $itemId);
		$update = $this->db->update('store_use_items', $item_data);

		return $update ? $this->db->get('store_use_items')->result_array() : FALSE;
		
	}
	
	function sub_item($itemId, $value) {

		$value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
		
		$item_data = array(
			'substituted' => $value
		);
		
		$this->db->where('item_id', $itemId);
		$update = $this->db->update('store_use_items', $item_data);

		return $update ? $this->db->get('store_use_items')->result_array() : FALSE;
		
	}

	function delete_item($itemId) {
		
		$this->db->where('item_id', $itemId);
		$delete = $this->db->delete('store_use_items');
		return $delete;
		
	}
	
	function get_items($locationId) {

		$data["item"] = array();

		// Get the orders from DB
		$table = 'store_use_items';

		if ($locationId){
			$this->db->where('fk_location_id', $locationId);
		}
		$this->db->select('item_id, part_num, fk_location_id, item_desc, qty, employee_name, created, modified, approved, sent, notes, short_name, location_name, substituted');
		$this->db->join('store_use_category', 'su_cat_id = fk_su_category');
		$this->db->join('location', 'location_id = fk_location_id');
		$query = $this->db->get($table)->result_array();

		// loop through the result set and add the items to the array
		foreach ($query as $row){
			array_push($data["item"], $row);
		}

		// return the Array
		return $data;

	}
}
?>