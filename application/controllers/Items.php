<?php
defined('BASEPATH') or exit('');

/**
 * Description of Items
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 31st Dec, 2015
 */
class Items extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->genlib->checkLogin();
    $this->genlib->superOnly();

    $this->load->model(['item']);
  }
  
  private function generateItemCode($category = 'other') {
    $prefix = ['mobile' => 'MOB', 'accessory' => 'ACC', 'other' => 'OTH'][$category] ?? 'OTH';
    $base = $prefix . date('Ymd');
    $this->db->select('code')->from('items')->like('code', $base, 'after')->order_by('code', 'DESC')->limit(1);
    $query = $this->db->get();
    $seq = 1;
    if($query->num_rows() > 0) {
      $last = $query->row()->code;
      if(strlen($last) >= 3) $seq = (int)substr($last, -3) + 1;
    }
    $code = $base . str_pad($seq, 3, '0', STR_PAD_LEFT);
    if($this->db->where('code', $code)->get('items')->num_rows() > 0) {
      $code = $base . str_pad($seq + 1, 3, '0', STR_PAD_LEFT);
    }
    return $code;
  }

  /**
   * 
   */
  public function index()
  {
    $data['pageContent'] = $this->load->view('items/items', '', TRUE);
    $data['pageTitle'] = "Items";

    $this->load->view('main', $data);
  }

  /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

  /**
   * "lilt" = "load Items List Table"
   */
  public function lilt()
  {
    $this->genlib->ajaxOnly();

    $this->load->helper('text');

    //set the sort order
    $orderBy = $this->input->get('orderBy', TRUE) ? $this->input->get('orderBy', TRUE) : "name";
    $orderFormat = $this->input->get('orderFormat', TRUE) ? $this->input->get('orderFormat', TRUE) : "ASC";
    $category = $this->input->get('category', TRUE);
    $searchTerm = $this->input->get('search', TRUE);

    // If search term provided, use search method
    if (!empty($searchTerm)) {
      $data['allItems'] = $this->item->itemsearch($searchTerm);
      $totalItems = $data['allItems'] ? count($data['allItems']) : 0;
      $data['range'] = $totalItems > 0 ? "Found " . $totalItems . " item(s)" : "No items found";
      $data['links'] = '';
      $data['sn'] = 1;
      $data['cum_total'] = $this->item->getItemsCumTotal();
      
      $json['itemsListTable'] = $this->load->view('items/itemslisttable', $data, TRUE);
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
      return;
    }

    //count the total number of items in db
    if ($category) {
      $totalItems = $this->db->where('category', $category)->count_all_results('items');
    } else {
      $totalItems = $this->db->count_all('items');
    }

    $this->load->library('pagination');

    $pageNumber = $this->uri->segment(3, 0); //set page number to zero if the page number is not set in the third segment of uri

    $limit = $this->input->get('limit', TRUE) ? $this->input->get('limit', TRUE) : 10; //show $limit per page
    $start = $pageNumber == 0 ? 0 : ($pageNumber - 1) * $limit; //start from 0 if pageNumber is 0, else start from the next iteration

    //call setPaginationConfig($totalRows, $urlToCall, $limit, $attributes) in genlib to configure pagination
    $config = $this->genlib->setPaginationConfig($totalItems, "items/lilt", $limit, ['onclick' => 'return lilt(this.href);']);

    $this->pagination->initialize($config); //initialize the library class

    //get all items from db with optional category filter
    if ($category) {
      $this->db->where('category', $category);
    }
    $data['allItems'] = $this->item->getAll($orderBy, $orderFormat, $start, $limit);
    $data['range'] = $totalItems > 0 ? "Showing " . ($start + 1) . "-" . ($start + count($data['allItems'])) . " of " . $totalItems : "";
    $data['links'] = $this->pagination->create_links(); //page links
    $data['sn'] = $start + 1;
    $data['cum_total'] = $this->item->getItemsCumTotal();

    $json['itemsListTable'] = $this->load->view('items/itemslisttable', $data, TRUE); //get view with populated items table

    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }

  /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */



  public function add() {
    $this->genlib->ajaxOnly();
    $this->load->library('form_validation');
    $this->form_validation->set_error_delimiters('', '');
    $this->form_validation->set_rules('itemName', 'Item name', ['required', 'trim', 'max_length[80]', 'is_unique[items.name]'], ['required' => "required"]);
    $this->form_validation->set_rules('itemPrice', 'Item Price', ['required', 'trim', 'numeric'], ['required' => "required"]);
    $this->form_validation->set_rules('itemType', 'Item Type', ['required', 'trim', 'in_list[standard,serialized]'], ['required' => "required"]);
    $this->form_validation->set_rules('itemBrand', 'Brand', ['trim', 'max_length[50]']);
    $this->form_validation->set_rules('itemModel', 'Model', ['trim', 'max_length[50]']);
    $this->form_validation->set_rules('itemCategory', 'Category', ['trim', 'in_list[mobile,accessory,other]']);
    $this->form_validation->set_rules('warrantyMonths', 'Warranty Months', ['trim', 'numeric']);
    
    $itemType = $this->input->post('itemType', TRUE);
    if($itemType === 'standard') {
      $this->form_validation->set_rules('itemQuantity', 'Item quantity', ['required', 'trim', 'numeric'], ['required' => "required"]);
    } elseif($itemType === 'serialized') {
      $imeiNumbers = $this->input->post('imeiNumbers', TRUE);
      if(empty($imeiNumbers) || !is_array($imeiNumbers)) {
        $json['status'] = 0; $json['msg'] = "At least one IMEI number is required for serialized items";
        $this->output->set_content_type('application/json')->set_output(json_encode($json)); return;
      }
    }
    
    if($this->form_validation->run() !== FALSE) {
      $this->db->trans_start();
      $category = $this->input->post('itemCategory', TRUE) ?: 'other';
      $generatedCode = $this->generateItemCode($category);
      
      $itemData = ['name' => set_value('itemName'), 'code' => $generatedCode, 'item_type' => set_value('itemType'), 'unitPrice' => set_value('itemPrice'), 'description' => set_value('itemDescription')];
      foreach(['itemBrand' => 'brand', 'itemModel' => 'model', 'itemCategory' => 'category', 'warrantyMonths' => 'warranty_months', 'warrantyTerms' => 'warranty_terms'] as $post => $field) {
        if($this->input->post($post, TRUE)) $itemData[$field] = $this->input->post($post, TRUE);
      }
      if($itemType === 'standard') $itemData['quantity'] = set_value('itemQuantity');
      
      $insertedId = $this->item->addWithType($itemData);
      
      if($insertedId && $itemType === 'serialized') {
        $imeiNumbers = $this->input->post('imeiNumbers', TRUE);
        $imeiData = []; $imeiErrors = [];
        foreach($imeiNumbers as $index => $imei) {
          $imeiClean = trim($imei['imei']);
          if(!preg_match('/^\d{15}$/', $imeiClean)) { $imeiErrors[] = "IMEI #".($index+1).": Invalid format"; continue; }
          $imeiData[] = ['imei' => $imeiClean, 'color' => $imei['color'] ?? '', 'storage' => $imei['storage'] ?? '', 'cost_price' => $imei['cost_price'] ?? set_value('itemPrice')];
        }
        if(!empty($imeiData)) {
          $imeiResult = $this->item->addSerialNumber($insertedId, $imeiData);
          if(is_array($imeiResult)) $imeiErrors = array_merge($imeiErrors, $imeiResult);
          elseif($imeiResult === FALSE) $imeiErrors[] = "Failed to add IMEI numbers";
        }
      }
      
      $itemName = set_value('itemName');
      $itemPrice = "Rs. " . number_format(set_value('itemPrice'), 2);
      $desc = $itemType === 'standard' ? "Addition of ".set_value('itemQuantity')." quantities of '{$itemName}' (Code: {$generatedCode}) at {$itemPrice}" : "Addition of serialized item '{$itemName}' (Code: {$generatedCode}) with ".(isset($imeiData) ? count($imeiData) : 0)." IMEIs at {$itemPrice}";
      $insertedId ? $this->genmod->addevent("Creation of new item", $insertedId, $desc, "items", $this->session->admin_id) : "";
      
      $this->db->trans_complete();
      
      if($this->db->trans_status() !== FALSE) {
        $json['status'] = 1; $json['msg'] = "Item successfully added! Code: {$generatedCode}"; $json['item_code'] = $generatedCode;
        if(isset($imeiErrors) && !empty($imeiErrors)) { $json['warnings'] = $imeiErrors; $json['msg'] .= " (with some IMEI warnings)"; }
      } else {
        $json['status'] = 0; $json['msg'] = "Oops! Unexpected server error!";
      }
    } else {
      $json = $this->form_validation->error_array();
      $json['msg'] = "One or more required fields are empty or not correctly filled";
      $json['status'] = 0;
    }
    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }


  /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */


  /**
   * Primarily used to check whether an item already has a particular random code being generated for a new item
   * @param type $selColName
   * @param type $whereColName
   * @param type $colValue
   */
  public function gettablecol($selColName, $whereColName, $colValue)
  {
    $a = $this->genmod->gettablecol('items', $selColName, $whereColName, $colValue);

    $json['status'] = $a ? 1 : 0;
    $json['colVal'] = $a;

    $this->output->set_content_type('application/json')->set_output(json_encode($json));
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
   */
  public function gcoandqty()
  {
    $json['status'] = 0;

    $itemCode = $this->input->get('_iC', TRUE);

    if ($itemCode) {
      $item_info = $this->item->getItemInfo(['code' => $itemCode], ['quantity', 'unitPrice', 'description']);

      if ($item_info) {
        $json['availQty'] = (int)$item_info->quantity;
        $json['unitPrice'] = $item_info->unitPrice;
        $json['description'] = $item_info->description;
        $json['status'] = 1;
      }
    }

    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }



  /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */


  public function updatestock()
  {
    $this->genlib->ajaxOnly();

    $this->load->library('form_validation');

    $this->form_validation->set_error_delimiters('', '');

    $this->form_validation->set_rules('_iId', 'Item ID', ['required', 'trim', 'numeric'], ['required' => "required"]);
    $this->form_validation->set_rules('_upType', 'Update type', ['required', 'trim', 'in_list[newStock,deficit]'], ['required' => "required"]);
    $this->form_validation->set_rules('qty', 'Quantity', ['required', 'trim', 'numeric'], ['required' => "required"]);
    $this->form_validation->set_rules('desc', 'Update Description', ['required', 'trim'], ['required' => "required"]);

    if ($this->form_validation->run() !== FALSE) {
      //update stock based on the update type
      $updateType = set_value('_upType');
      $itemId = set_value('_iId');
      $qty = set_value('qty');
      $desc = set_value('desc');

      $this->db->trans_start();

      $updated = $updateType === "deficit"
        ?
        $this->item->deficit($itemId, $qty, $desc)
        :
        $this->item->newstock($itemId, $qty, $desc);

      //add event to log if successful
      $stockUpdateType = $updateType === "deficit" ? "Deficit" : "New Stock";

      $event = "Stock Update ($stockUpdateType)";

      $action = $updateType === "deficit" ? "removed from" : "added to"; //action that happened

      $eventDesc = "<p>{$qty} quantities of {$this->genmod->gettablecol('items', 'name', 'id',$itemId)} was {$action} stock</p>
                Reason: <p>{$desc}</p>";

      //function header: addevent($event, $eventRowId, $eventDesc, $eventTable, $staffId)
      $updated ? $this->genmod->addevent($event, $itemId, $eventDesc, "items", $this->session->admin_id) : "";

      $this->db->trans_complete(); //end transaction

      $json['status'] = $this->db->trans_status() !== FALSE ? 1 : 0;
      $json['msg'] = $updated ? "Stock successfully updated" : "Unable to update stock at this time. Please try again later";
    } else {
      $json['status'] = 0;
      $json['msg'] = "One or more required fields are empty or not correctly filled";
      $json = $this->form_validation->error_array();
    }


    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }


  /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

  public function edit() {
    $this->genlib->ajaxOnly();
    $this->load->library('form_validation');
    $this->form_validation->set_error_delimiters('', '');
    $this->form_validation->set_rules('_iId', 'Item ID', ['required', 'trim', 'numeric']);
    $this->form_validation->set_rules('itemName', 'Item Name', ['required', 'trim', 'callback_crosscheckName['.$this->input->post('_iId', TRUE).']'], ['required' => 'required']);
    $this->form_validation->set_rules('itemCode', 'Item Code', ['required', 'trim', 'callback_crosscheckCode['.$this->input->post('_iId', TRUE).']'], ['required' => 'required']);
    $this->form_validation->set_rules('itemPrice', 'Item Unit Price', ['required', 'trim', 'numeric']);
    $this->form_validation->set_rules('itemDesc', 'Item Description', ['trim']);
    $this->form_validation->set_rules('itemBrand', 'Brand', ['trim', 'max_length[50]']);
    $this->form_validation->set_rules('itemModel', 'Model', ['trim', 'max_length[50]']);
    $this->form_validation->set_rules('itemCategory', 'Category', ['trim', 'in_list[mobile,accessory,other]']);
    $this->form_validation->set_rules('warrantyMonths', 'Warranty Months', ['trim', 'numeric']);
    $this->form_validation->set_rules('warrantyTerms', 'Warranty Terms', ['trim', 'max_length[200]']);
    
    if($this->form_validation->run() !== FALSE) {
      $itemId = set_value('_iId');
      $itemCode = $this->input->post('itemCode', TRUE);
      $updateData = ['name' => set_value('itemName'), 'unitPrice' => set_value('itemPrice'), 'description' => set_value('itemDesc')];
      foreach(['itemBrand' => 'brand', 'itemModel' => 'model', 'itemCategory' => 'category', 'warrantyMonths' => 'warranty_months', 'warrantyTerms' => 'warranty_terms'] as $post => $field) {
        if($this->input->post($post, TRUE) !== NULL && $this->input->post($post, TRUE) !== '') $updateData[$field] = $this->input->post($post, TRUE);
      }
      $updated = $this->item->edit($itemId, $updateData);
      $json['status'] = $updated ? 1 : 0;
      $this->genmod->addevent("Item Update", $itemId, "Details of item with code '$itemCode' was updated", 'items', $this->session->admin_id);
    } else {
      $json['status'] = 0;
      $json = $this->form_validation->error_array();
    }
    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }




  /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

  public function crosscheckName($itemName, $itemId)
  {
    //check db to ensure name was previously used for the item we are updating
    $itemWithName = $this->genmod->getTableCol('items', 'id', 'name', $itemName);

    //if item name does not exist or it exist but it's the name of current item
    if (!$itemWithName || ($itemWithName == $itemId)) {
      return TRUE;
    } else { //if it exist
      $this->form_validation->set_message('crosscheckName', 'There is an item with this name');

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
   * 
   * @param type $item_code
   * @param type $item_id
   * @return boolean
   */
  public function crosscheckCode($item_code, $item_id)
  {
    //check db to ensure item code was previously used for the item we are updating
    $item_with_code = $this->genmod->getTableCol('items', 'id', 'code', $item_code);

    //if item code does not exist or it exist but it's the code of current item
    if (!$item_with_code || ($item_with_code == $item_id)) {
      return TRUE;
    } else { //if it exist
      $this->form_validation->set_message('crosscheckCode', 'There is an item with this code');

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


  public function delete()
  {
    $this->genlib->ajaxOnly();

    $json['status'] = 0;
    $item_id = $this->input->post('i', TRUE);

    if ($item_id) {
      $this->db->where('id', $item_id)->delete('items');

      $json['status'] = 1;
    }

    //set final output
    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }
  
  public function getItemSerials() {
    $this->genlib->ajaxOnly();
    $json = ['status' => 0, 'serials' => []];
    $itemId = $this->input->get('itemId', TRUE);
    $status = $this->input->get('status', TRUE);
    if($itemId) {
      $serials = $this->item->getAvailableSerials($itemId, $status);
      if($serials) { $json['status'] = 1; $json['serials'] = $serials; $json['count'] = count($serials); }
      else $json['msg'] = "No serial numbers found for this item";
    } else $json['msg'] = "Item ID is required";
    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }
  
  public function checkImeiAvailability() {
    $this->genlib->ajaxOnly();
    $json = ['status' => 0, 'available' => FALSE];
    $imei = $this->input->get('imei', TRUE);
    if($imei) {
      if(!preg_match('/^\d{15}$/', $imei)) { $json['msg'] = "Invalid IMEI format (must be 15 digits)"; }
      else {
        $this->db->where('imei_number', $imei);
        $existing = $this->db->get('item_serials');
        if($existing->num_rows() > 0) {
          $serial = $existing->row();
          $json['status'] = 1; $json['exists'] = TRUE; $json['available'] = ($serial->status === 'available'); $json['serial_info'] = $serial;
          $json['msg'] = $serial->status === 'sold' ? "IMEI already sold" : ($serial->status === 'reserved' ? "IMEI is reserved (in cart)" : ($serial->status === 'available' ? "IMEI is available" : "IMEI status: ".$serial->status));
        } else { $json['status'] = 1; $json['exists'] = FALSE; $json['available'] = TRUE; $json['msg'] = "IMEI not found in database (available for use)"; }
      }
    } else $json['msg'] = "IMEI number is required";
    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }

  /**
   * Get item info by code (for POS)
   */
  public function getItemInfo() {
    $this->genlib->ajaxOnly();
    
    $code = $this->input->get('code', TRUE);
    
    if (empty($code)) {
      $json['status'] = 0;
      $json['msg'] = "Item code is required";
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
      return;
    }

    $itemInfo = $this->item->getItemInfo(['code' => $code], 'id, name, code, unitPrice, item_type, quantity, brand, model, category, warranty_months, warranty_terms, description');

    if (!$itemInfo) {
      $json['status'] = 0;
      $json['msg'] = "Item not found";
    } else {
      $json['status'] = 1;
      $json['item'] = $itemInfo;
    }

    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }
}

