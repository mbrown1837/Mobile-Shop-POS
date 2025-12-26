<?php
defined('BASEPATH') OR exit('');

/**
 * Description of Customer
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 4th RabThaani, 1437AH (15th Jan, 2016)
 */
class Item extends CI_Model{
    public function __construct(){
        parent::__construct();
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function getAll($orderBy, $orderFormat, $start=0, $limit=''){
        $this->db->limit($limit, $start);
        $this->db->order_by($orderBy, $orderFormat);
        $run_q = $this->db->get('inventory_available'); // Use view for accurate quantities
        return $run_q->num_rows() > 0 ? $run_q->result() : FALSE;
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    
    /**
     * 
     * @param type $itemName
     * @param type $itemQuantity
     * @param type $itemPrice
     * @param type $itemDescription
     * @param type $itemCode
     * @return boolean
     */
    public function add($itemName, $itemQuantity, $itemPrice, $itemDescription, $itemCode){
        $data = ['name'=>$itemName, 'quantity'=>$itemQuantity, 'unitPrice'=>$itemPrice, 'description'=>$itemDescription, 'code'=>$itemCode];
                
        //set the datetime based on the db driver in use
        $this->db->platform() == "sqlite3" 
                ? 
        $this->db->set('dateAdded', "datetime('now')", FALSE) 
                : 
        $this->db->set('dateAdded', "NOW()", FALSE);
        
        $this->db->insert('items', $data);
        
        if($this->db->insert_id()){
            return $this->db->insert_id();
        }
        
        else{
            return FALSE;
        }
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * Enhanced unified search - searches by name, code, IMEI, brand, model
     * @param type $value
     * @return boolean
     */
    public function itemsearch($value){
        $escaped_value = $this->db->escape_like_str($value);
        
        // Search in items table AND item_serials table (for IMEI)
        $q = "SELECT DISTINCT items.* FROM items 
            LEFT JOIN item_serials ON items.id = item_serials.item_id
            WHERE 
            items.name LIKE '%".$escaped_value."%'
            OR items.code LIKE '%".$escaped_value."%'
            OR items.brand LIKE '%".$escaped_value."%'
            OR items.model LIKE '%".$escaped_value."%'
            OR item_serials.imei_number LIKE '%".$escaped_value."%'
            ORDER BY items.name ASC";
        
        $run_q = $this->db->query($q);
        
        if($run_q->num_rows() > 0){
            return $run_q->result();
        }
        
        else{
            return FALSE;
        }
    }
    
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * To add to the number of an item in stock
     * @param type $itemId
     * @param type $numberToadd
     * @return boolean
     */
    public function incrementItem($itemId, $numberToadd){
        $q = "UPDATE items SET quantity = quantity + ? WHERE id = ?";
        
        $this->db->query($q, [$numberToadd, $itemId]);
        
        if($this->db->affected_rows() > 0){
            return TRUE;
        }
        
        else{
            return FALSE;
        }
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    public function decrementItem($itemCode, $numberToRemove){
        $q = "UPDATE items SET quantity = quantity - ? WHERE code = ?";
        
        $this->db->query($q, [$numberToRemove, $itemCode]);
        
        if($this->db->affected_rows() > 0){
            return TRUE;
        }
        
        else{
            return FALSE;
        }
    }


    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    
   public function newstock($itemId, $qty){
       $q = "UPDATE items SET quantity = quantity + $qty WHERE id = ?";
       
       $this->db->query($q, [$itemId]);
       
       if($this->db->affected_rows()){
           return TRUE;
       }
       
       else{
           return FALSE;
       }
   }
   
   
   /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
   
   public function deficit($itemId, $qty){
       $q = "UPDATE items SET quantity = quantity - $qty WHERE id = ?";
       
       $this->db->query($q, [$itemId]);
       
       if($this->db->affected_rows()){
           return TRUE;
       }
       
       else{
           return FALSE;
       }
   }
   
   /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
   
   /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
   
	public function getActiveItems($orderBy, $orderFormat){
        $this->db->order_by($orderBy, $orderFormat);
		
		$this->db->where('quantity >=', 1);
        
        $run_q = $this->db->get('items');
        
        if($run_q->num_rows() > 0){
            return $run_q->result();
        }
        
        else{
            return FALSE;
        }
    }


    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

    /**
     * array $where_clause
     * array $fields_to_fetch
     * 
     * return array | FALSE
     */
    public function getItemInfo($where_clause, $fields_to_fetch){
        $this->db->select($fields_to_fetch);
        
        $this->db->where($where_clause);

        $run_q = $this->db->get('items');
        
        return $run_q->num_rows() ? $run_q->row() : FALSE;
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

    public function getItemsCumTotal(){
        $this->db->select("SUM(unitPrice*quantity) as cumPrice");

        $run_q = $this->db->get('items');
        
        return $run_q->num_rows() ? $run_q->row()->cumPrice : FALSE;
    }
    
    // ========================================
    // PHASE 2: Mobile Shop POS Methods
    // ========================================
    
    public function addWithType($data){
        if(empty($data['name']) || empty($data['code']) || empty($data['item_type']) || !isset($data['unitPrice'])) return FALSE;
        if(!in_array($data['item_type'], ['standard', 'serialized'])) return FALSE;
        
        $insert_data = ['name' => $data['name'], 'code' => $data['code'], 'item_type' => $data['item_type'], 'unitPrice' => $data['unitPrice']];
        $insert_data['quantity'] = $data['item_type'] === 'serialized' ? 0 : (isset($data['quantity']) ? $data['quantity'] : 0);
        
        foreach(['brand', 'model', 'warranty_months', 'warranty_terms', 'description'] as $field) {
            if(isset($data[$field])) $insert_data[$field] = $data[$field];
        }
        if(isset($data['category']) && in_array($data['category'], ['mobile', 'accessory', 'other'])) {
            $insert_data['category'] = $data['category'];
        }
        
        $this->db->platform() == "sqlite3" ? $this->db->set('dateAdded', "datetime('now')", FALSE) : $this->db->set('dateAdded', "NOW()", FALSE);
        $this->db->insert('items', $insert_data);
        return $this->db->insert_id() ?: FALSE;
    }
    
    public function addSerialNumber($itemId, $imeiData){
        $item = $this->getItemInfo(['id' => $itemId], 'id, item_type');
        if(!$item || $item->item_type !== 'serialized' || empty($imeiData) || !is_array($imeiData)) return FALSE;
        if(isset($imeiData['imei'])) $imeiData = [$imeiData];
        
        $errors = []; $success_count = 0;
        foreach($imeiData as $index => $serial){
            if(empty($serial['imei']) || !isset($serial['cost_price'])) { $errors[] = "Record $index: Missing required fields"; continue; }
            $imei = trim($serial['imei']);
            if(!preg_match('/^\d{15}$/', $imei)) { $errors[] = "Record $index: Invalid IMEI format: $imei"; continue; }
            if($this->db->where('imei_number', $imei)->get('item_serials')->num_rows() > 0) { $errors[] = "Record $index: IMEI exists: $imei"; continue; }
            
            $insert_data = ['item_id' => $itemId, 'imei_number' => $imei, 'cost_price' => $serial['cost_price'], 'status' => 'available'];
            foreach(['color', 'storage', 'selling_price', 'serial_number', 'notes'] as $field) {
                if(isset($serial[$field])) $insert_data[$field] = $serial[$field];
            }
            if($this->db->insert('item_serials', $insert_data) && $this->db->insert_id()) $success_count++;
            else $errors[] = "Record $index: Failed to insert IMEI: $imei";
        }
        
        return ($success_count > 0 && empty($errors)) ? TRUE : (($success_count > 0) ? $errors : FALSE);
    }
    
    public function getAvailableSerials($itemId, $status = 'available', $orderBy = 'created_at', $orderFormat = 'DESC'){
        if(empty($itemId)) return FALSE;
        $this->db->where('item_id', $itemId);
        if(!empty($status)) $this->db->where('status', $status);
        $this->db->order_by($orderBy, $orderFormat);
        $run_q = $this->db->get('item_serials');
        return $run_q->num_rows() > 0 ? $run_q->result() : FALSE;
    }
    
    public function lockSerial($imeiNumber){
        if(empty($imeiNumber)) return FALSE;
        if($this->db->where('imei_number', $imeiNumber)->where('status', 'available')->get('item_serials')->num_rows() === 0) return FALSE;
        $this->db->where('imei_number', $imeiNumber)->update('item_serials', ['status' => 'reserved']);
        return $this->db->affected_rows() > 0;
    }
    
    public function releaseSerial($imeiNumber){
        if(empty($imeiNumber)) return FALSE;
        if($this->db->where('imei_number', $imeiNumber)->where('status', 'reserved')->get('item_serials')->num_rows() === 0) return FALSE;
        $this->db->where('imei_number', $imeiNumber)->update('item_serials', ['status' => 'available']);
        return $this->db->affected_rows() > 0;
    }
    
    public function markSerialSold($imeiNumber, $transactionId = NULL){
        if(empty($imeiNumber)) return FALSE;
        if($this->db->where('imei_number', $imeiNumber)->where_in('status', ['available', 'reserved'])->get('item_serials')->num_rows() === 0) return FALSE;
        
        $update_data = ['status' => 'sold'];
        $this->db->platform() == "sqlite3" ? $this->db->set('sold_date', "datetime('now')", FALSE) : $this->db->set('sold_date', "NOW()", FALSE);
        if(!empty($transactionId)) $update_data['sold_transaction_id'] = $transactionId;
        $this->db->where('imei_number', $imeiNumber)->update('item_serials', $update_data);
        return $this->db->affected_rows() > 0;
    }
    
    public function getSerialInfo($imeiNumber){
        if(empty($imeiNumber)) return FALSE;
        $this->db->select('item_serials.*, items.name as item_name, items.code as item_code, items.brand, items.model, items.category, items.unitPrice, items.warranty_months, items.warranty_terms');
        $this->db->from('item_serials')->join('items', 'items.id = item_serials.item_id', 'left')->where('item_serials.imei_number', $imeiNumber);
        $run_q = $this->db->get();
        return $run_q->num_rows() > 0 ? $run_q->row() : FALSE;
    }
    
    public function edit($itemId, $data){
        if(empty($itemId) || empty($data)) return FALSE;
        $allowed = ['name', 'unitPrice', 'description', 'brand', 'model', 'category', 'warranty_months', 'warranty_terms'];
        $update_data = [];
        foreach($data as $key => $value) { if(in_array($key, $allowed)) $update_data[$key] = $value; }
        if(empty($update_data)) return FALSE;
        $this->db->where('id', $itemId)->update('items', $update_data);
        return TRUE;
    }
}
