<?php

class Special_Needs extends CI_Controller {


	private $userId = NULL;
	private $locationId = NULL;
	private $isMgr = FALSE;


	function Special_Needs(){
		parent::__construct();
		$this->setUserData();
		$this->setIsMgr();
	}

	
	public function index(){
		$this->load->library('Array2XML');
	}

	
	function setUserData(){
		$this->userId = $this->session->userdata('user_id');
		$this->locationId = $this->session->userdata('location_Id');
	}


	function setIsMgr(){
		$this->isMgr = $this->session->userdata('orderManager');
	}
	

	function queryOrderData($orderId){
		$this->db->where('order_id', $orderId);
		$table = 'special_needs_orders';
		$orderData['queryOrder']= $this->db->get($table)->row_array();
		$this->db->where('fk_order_id', $orderId);
		$orderData['queryNotes']= $this->db->get('view_sn_order_notes')->result_array();
		return $orderData;
	}	

	
	function manage_order($orderNum=NULL){
		if($orderNum!=NULL){
		
			// get the order data and coresponding notes
		 	$order = $this->queryOrderData($orderNum);
		 					 	
		 	// print the order
			echo json_encode($order);
		}
	}
	
	function manage_orderXML($orderNum=NULL){
		if($orderNum!=NULL){
			
			// get the order data and coresponding notes
		 	$order = $this->queryOrderData($orderNum);
		 	
			// print the order
			$xml = Array2XML::createXML('order', $order);
			echo $xml->saveXML();
			 	
		}
	}

	
	function process_form(){

		/*foreach($_POST as $key=>$value) {
			echo "$key: $value\n";
		}*/

		$this->load->library('form_validation');
		$this->form_validation->set_rules('emlpoyee_name', 'Emlpoyee Name', 'trim|required');
		$this->form_validation->set_rules('part_num', 'Part Number', 'trim|required');
		$this->form_validation->set_rules('item_desc', 'Item Description', 'trim|required');
		$this->form_validation->set_rules('customer_name', 'Customer Name', 'trim|required');
		$this->form_validation->set_rules('customer_contact', 'Contact Info', 'trim|required');
		$this->form_validation->set_rules('fk_category_id', 'Item Category', 'trim|required');
		$this->form_validation->set_rules('fk_vendor_id', 'Vendor', 'trim|required');
		$this->form_validation->set_rules('customer_deposit', 'Deposit', 'required');
		$this->form_validation->set_rules('qty', 'Quantity', 'required');
		$this->form_validation->set_rules('notes', 'Notes');

		if ($this->form_validation->run() == FALSE){

			echo validation_errors();
	
		} else {		
			if (isset($_POST['notes'])){
				$notes = $_POST['notes'];
				unset($_POST['notes']);
			}
			if (isset($_POST['formUser'])){
				$formUser = $_POST['formUser'];
				unset($_POST['formUser']);
			}
			
			$this->db->trans_start();

			$validOrderId = (
				isset($_POST['order_id']) 
				&& $_POST['order_id'] > 0 
			) ? true : false;
				
			if($validOrderId){
				$orderId = $_POST['order_id'];
				unset($_POST['order_id']);
				$this->db->where('order_id', $orderId);
				$this->db->update('special_needs_orders',$_POST);
			} else {
				$this->db->insert('special_needs_orders',$_POST);
				$orderId = $this->db->insert_id();
			}
			if ($notes){
				$notesData = array(
				   'fk_order_id' => $orderId,
				   'fk_user_id' => $formUser,
				   'notes' => $notes,
				);
				$this->db->insert('special_needs_notes', $notesData); 
			}
			$this->db->trans_complete();

		}
	}


	function ordersXML($complete=false){
	
		$data = $this->ordersData($complete);		
		$xml = Array2XML::createXML('root', $data);
		echo $xml->saveXML();
		
	}
				
	
	function optionsXML(){
	
		$data["categories"] = array();
		$categories = $this->categoryOptionsData();
		array_push($data["categories"], $categories);
		
		$data["statuses"] = array();
		$statuses = $this->statusOptionsData();
		array_push($data["statuses"], $statuses);

		$data["vendors"] = array();
		$vendors = $this->vendorOptionsData();
		array_push($data["vendors"], $vendors);
		
		$data["session"] = array();
		$session = $this->session->all_userdata();
		array_push($data["session"], $session);
		
		$xml = Array2XML::createXML('options', $data);
		echo $xml->saveXML();	
	}


	function ordersData ($complete=false){
	
		// Initialize the array we will return 
		$data["item"] = array();

		// Get the orders from DB
		$table = 'view_orders';
		
		if ($complete == true) {
			$table = 'view_orders_complete';
		}
		
		if (!$this->isMgr) {
			$this->db->where('fk_user_id_creator', $this->userId);
		}

		$this->db->order_by("created", "desc"); 
		$this->db->select('location_name,status_name,customer_name,item_desc,created,modified,vendor_code,cat_code,part_num,qty,order_id');
		$query = $this->db->get($table)->result_array();

		// loop through the result set and add the items to the array
		foreach ($query as $row){
			array_push($data["item"], $row);
		}
		
		// return the Array
		return $data;
	}
	
	
	function vendorOptionsData (){
		
		// Initialize the array we will return 
		$data["vendor"] = array();

		// Get the vendor options from DB and put the in an array for a form dropdown
		$this->db->order_by("order", "asc"); 
		$query = $this->db->get_where('product_vendors', array('is_active' => true));

		// loop through the result set and add the items to the array
		foreach ($query->result_array() as $row){
			$push = array(	
							"@attributes" => array(
								"id" => $row["vendor_id"],
								"name" => $row['name'], 
								"vendor_code" => $row['short_name'], 
								"order" => $row['order']
            			),
						"@value" => $row["vendor_id"]					
			);
			array_push($data["vendor"], $push);
		}
		
		// return the Array
		return $data;
	}
	
	
	function statusOptionsData (){
		
		// Initialize the array we will return 
		$data["status"] = array();

		// Get the orders status options from DB and put the in an array for a form dropdown
		$this->db->order_by("order", "asc"); 
		$query = $this->db->get_where('special_needs_status', array('is_active' => true));

		// loop through the result set and add the items to the array
		foreach ($query->result_array() as $row){
			$push = array(	
							"@attributes" => array(
								"id" => $row["status_id"],
								"name" => $row['status_name'], 
								"order" => $row['order']
            			),
						"@value" => $row["status_id"]					
			);
			array_push($data["status"], $push);
		}
		
		// return the Array
		return $data;
	}

	
	function categoryOptionsData (){
		
		// Initialize the array we will return 
		$data["category"] = array();

		//Get the category options from DB and put the in an array for a form dropdown
		$this->db->order_by("order", "asc"); 
		$query = $this->db->get_where('product_category', array('is_active' => true));

		// loop through the result set and add the items to the array
		foreach ($query->result_array() as $row){
			$push = array(	
							"@attributes" => array(
								"id" => $row["category_id"],
              					"name" => $row["name"], 
								"category_code" => $row["short_name"], 
								"order" => $row["order"]
            			),
						"@value" => $row["category_id"]					
			);
			array_push($data["category"], $push);
		}
		
		// return the Array
		return $data;
	}

} 
?>