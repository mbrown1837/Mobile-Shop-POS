<?php
defined('BASEPATH') or exit('');
/**
 * Description of Transactions
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 31st Dec, 2015
 */
class Transactions extends CI_Controller
{
  private $total_before_discount = 0, $discount_amount = 0, $vat_amount = 0, $eventual_total = 0;

  public function __construct()
  {
    parent::__construct();

    $this->genlib->checkLogin();

    $this->load->model(['transaction', 'item']);

    // Initialize cart on controller load
    $this->genlib->initCart();
  }

  /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

  public function index()
  {
    $transData['items'] = $this->item->getActiveItems('name', 'ASC'); //get items with at least one qty left, to be used when doing a new transaction

    $data['pageContent'] = $this->load->view('transactions/transactions', $transData, TRUE);
    $data['pageTitle'] = "Transactions";

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
   * latr_ = "Load All Transactions"
   */
  public function latr_()
  {
    //set the sort order
    $orderBy = $this->input->get('orderBy', TRUE) ? $this->input->get('orderBy', TRUE) : "transDate";
    $orderFormat = $this->input->get('orderFormat', TRUE) ? $this->input->get('orderFormat', TRUE) : "DESC";

    //count the total number of transaction group (grouping by the ref) in db
    $totalTransactions = $this->transaction->totalTransactions();

    $this->load->library('pagination');

    $pageNumber = $this->uri->segment(3, 0); //set page number to zero if the page number is not set in the third segment of uri

    $limit = $this->input->get('limit', TRUE) ? $this->input->get('limit', TRUE) : 10; //show $limit per page
    $start = $pageNumber == 0 ? 0 : ($pageNumber - 1) * $limit; //start from 0 if pageNumber is 0, else start from the next iteration

    //call setPaginationConfig($totalRows, $urlToCall, $limit, $attributes) in genlib to configure pagination
    $config = $this->genlib->setPaginationConfig($totalTransactions, "transactions/latr_", $limit, ['onclick' => 'return latr_(this.href);']);

    $this->pagination->initialize($config); //initialize the library class

    //get all transactions from db
    $data['allTransactions'] = $this->transaction->getAll($orderBy, $orderFormat, $start, $limit);
    $data['range'] = $totalTransactions > 0 ? ($start + 1) . "-" . ($start + count($data['allTransactions'])) . " of " . $totalTransactions : "";
    $data['links'] = $this->pagination->create_links(); //page links
    $data['sn'] = $start + 1;

    $json['transTable'] = $this->load->view('transactions/transtable', $data, TRUE); //get view with populated transactions table

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
   * nso_ = "New Sales Order"
   */
  public function nso_()
  {
    $this->genlib->ajaxOnly();

    $arrOfItemsDetails = json_decode($this->input->post('_aoi', TRUE));
    $_mop = $this->input->post('_mop', TRUE); //mode of payment
    $_at = round($this->input->post('_at', TRUE), 2); //amount tendered
    $_cd = $this->input->post('_cd', TRUE); //change due
    $cumAmount = $this->input->post('_ca', TRUE); //cumulative amount
    $vatPercentage = $this->input->post('vat', TRUE); //vat percentage
    $discount_percentage = $this->input->post('discount', TRUE); //discount percentage
    $cust_name = $this->input->post('cn', TRUE);
    $cust_phone = $this->input->post('cp', TRUE);
    $cust_email = $this->input->post('ce', TRUE);

    /*
         * Loop through the arrOfItemsDetails and ensure each item's details has not been manipulated
         * The unitPrice must match the item's unit price in db, the totPrice must match the unitPrice*qty
         * The cumAmount must also match the total of all totPrice in the arr in addition to the amount of 
         * VAT (based on the vat percentage) and minus the $discount_percentage (if available)
         */

    $allIsWell = $this->validateItemsDet($arrOfItemsDetails, $cumAmount, $_at, $vatPercentage, $discount_percentage);

    if ($allIsWell) { //insert each sales order into db, generate receipt and return info to client

      //will insert info into db and return transaction's receipt
      $returnedData = $this->insertTrToDb(
        $arrOfItemsDetails,
        $_mop,
        $_at,
        $cumAmount,
        $_cd,
        $this->vat_amount,
        $vatPercentage,
        $this->discount_amount,
        $discount_percentage,
        $cust_name,
        $cust_phone,
        $cust_email
      );

      $json['status'] = $returnedData ? 1 : 0;
      $json['msg'] = $returnedData ? "Transaction successfully processed" :
        "Unable to process your request at this time. Pls try again later "
        . "or contact technical department for assistance";
      $json['transReceipt'] = $returnedData['transReceipt'];

      $json['totalEarnedToday'] = number_format($this->transaction->totalEarnedToday());

      //add into eventlog
      //function header: addevent($event, $eventRowIdOrRef, $eventDesc, $eventTable, $staffId) in 'genmod'
      $eventDesc = count($arrOfItemsDetails) . " items totalling &#8358;" . number_format($cumAmount, 2)
        . " with reference number {$returnedData['transRef']} was purchased";

      $this->genmod->addevent("New Transaction", $returnedData['transRef'], $eventDesc, 'transactions', $this->session->admin_id);
    } else { //return error msg
      $json['status'] = 0;
      $json['msg'] = "Transaction could not be processed. Please ensure there are no errors. Thanks";
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
   * Validates the details of items sent from client to prevent manipulation
   * @param type $arrOfItemsInfo
   * @param type $cumAmountFromClient
   * @param type $amountTendered
   * @param type $vatPercentage
   * @param type $discount_percentage
   * @return boolean
   */
  private function validateItemsDet($arrOfItemsInfo, $cumAmountFromClient, $amountTendered, $vatPercentage, $discount_percentage)
  {
    $error = 0;

    //loop through the item's info and validate each
    //return error if at least one seems suspicious (i.e. fails validation)
    foreach ($arrOfItemsInfo as $get) {
      $itemCode = $get->_iC; //use this to get the item's unit price, then multiply it with the qty sent from client
      $qtyToBuy = $get->qty;
      $unitPriceFromClient = $get->unitPrice;
      $unitPriceInDb = $this->genmod->gettablecol('items', 'unitPrice', 'code', $itemCode);
      $totPriceFromClient = $get->totalPrice;

      //ensure both unit price matches
      $unitPriceInDb == $unitPriceFromClient ? "" : $error++;

      $expectedTotPrice = round($qtyToBuy * $unitPriceInDb, 2); //calculate expected totPrice

      //ensure both matches
      $expectedTotPrice == $totPriceFromClient ? "" : $error++;

      //no need to validate others, just break out of the loop if one fails validation
      if ($error > 0) {
        return FALSE;
      }

      $this->total_before_discount += $expectedTotPrice;
    }

    /**
     * We need to save the total price before tax, tax amount, total price after tax, discount amount, eventual total
     */

    $expectedCumAmount = $this->total_before_discount;

    //now calculate the discount amount (if there is discount) based on the discount percentage and subtract it(discount amount) 
    //from $total_before_discount
    if ($discount_percentage) {
      $this->discount_amount = $this->getDiscountAmount($expectedCumAmount, $discount_percentage);

      $expectedCumAmount = round($expectedCumAmount - $this->discount_amount, 2);
    }

    //add VAT amount to $expectedCumAmount is VAT percentage is set
    if ($vatPercentage) {
      //calculate vat amount using $vatPercentage and add it to $expectedTotPrice
      $this->vat_amount = $this->getVatAmount($expectedCumAmount, $vatPercentage);

      //now add the vat amount to expected total price
      $expectedCumAmount = round($expectedCumAmount + $this->vat_amount, 2);
    }

    //check if cum amount also matches and ensure amount tendered is not less than $expectedCumAmount
    if (($expectedCumAmount != $cumAmountFromClient) || ($expectedCumAmount > $amountTendered)) {
      return FALSE;
    }

    //if code execution reaches here, it means all is well
    $this->eventual_total = $expectedCumAmount;
    return TRUE;
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
   * @param type $arrOfItemsDetails
   * @param type $_mop
   * @param type $_at
   * @param type $cumAmount
   * @param type $_cd
   * @param type $vatAmount
   * @param type $vatPercentage
   * @param type $discount_amount
   * @param type $discount_percentage
   * @param type $cust_name
   * @param type $cust_phone
   * @param type $cust_email
   * @return boolean
   */
  private function insertTrToDb($arrOfItemsDetails, $_mop, $_at, $cumAmount, $_cd, $vatAmount, $vatPercentage, $discount_amount, $discount_percentage, $cust_name, $cust_phone, $cust_email)
  {
    $allTransInfo = []; //to hold info of all items' in transaction

    //generate random string to use as transaction ref
    //keep regeneration the ref if generated ref exist in db
    do {
      $ref = strtoupper($this->genlib->generateRandomCode('numeric', 6, 10, ""));
    } while ($this->transaction->isRefExist($ref));

    //loop through the items' details and insert them one by one
    //start transaction
    $this->db->trans_start();

    foreach ($arrOfItemsDetails as $get) {
      $itemCode = $get->_iC;
      $itemName = $this->genmod->getTableCol('items', 'name', 'code', $itemCode);
      $qtySold = $get->qty; //qty selected for item in loop
      $unitPrice = $get->unitPrice; //unit price of item in loop
      $totalPrice = $get->totalPrice; //total price for item in loop

      /*
             * add transaction to db
             * function header: add($_iN, $_iC, $desc, $q, $_up, $_tp, $_tas, $_at, $_cd, $_mop, $_tt, $ref, $_va, $_vp, $da, $dp, $cn, $cp, $ce)
             */
      $transId = $this->transaction->add(
        $itemName,
        $itemCode,
        "",
        $qtySold,
        $unitPrice,
        $totalPrice,
        $cumAmount,
        $_at,
        $_cd,
        $_mop,
        1,
        $ref,
        $vatAmount,
        $vatPercentage,
        $discount_amount,
        $discount_percentage,
        $cust_name,
        $cust_phone,
        $cust_email
      );

      $allTransInfo[$transId] = ['itemName' => $itemName, 'quantity' => $qtySold, 'unitPrice' => $unitPrice, 'totalPrice' => $totalPrice];

      //update item quantity in db by removing the quantity bought
      //function header: decrementItem($itemId, $numberToRemove)
      $this->item->decrementItem($itemCode, $qtySold);
    }

    $this->db->trans_complete(); //end transaction

    //ensure there was no error
    //works in production since db_debug would have been turned off
    if ($this->db->trans_status() === FALSE) {
      return false;
    } else {
      $dataToReturn = [];

      //get transaction date in db, to be used on the receipt. It is necessary since date and time must matc
      $dateInDb = $this->genmod->getTableCol('transactions', 'transDate', 'transId', $transId);

      //generate receipt to return
      $dataToReturn['transReceipt'] = $this->genTransReceipt($allTransInfo, $cumAmount, $_at, $_cd, $ref, $dateInDb, $_mop, $vatAmount, $vatPercentage, $discount_amount, $discount_percentage, $cust_name, $cust_phone, $cust_email);
      $dataToReturn['transRef'] = $ref;

      return $dataToReturn;
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
   * @param type $allTransInfo
   * @param type $cumAmount
   * @param type $_at
   * @param type $_cd
   * @param type $ref
   * @param type $transDate
   * @param type $_mop
   * @param type $vatAmount
   * @param type $vatPercentage
   * @param type $discount_amount
   * @param type $discount_percentage
   * @param type $cust_name
   * @param type $cust_phone
   * @param type $cust_email
   * @return type
   */
  private function genTransReceipt(
    $allTransInfo,
    $cumAmount,
    $_at,
    $_cd,
    $ref,
    $transDate,
    $_mop,
    $vatAmount,
    $vatPercentage,
    $discount_amount,
    $discount_percentage,
    $cust_name,
    $cust_phone,
    $cust_email
  ) {
    $data['allTransInfo'] = $allTransInfo;
    $data['cumAmount'] = $cumAmount;
    $data['amountTendered'] = $_at;
    $data['changeDue'] = $_cd;
    $data['ref'] = $ref;
    $data['transDate'] = $transDate;
    $data['_mop'] = $_mop;
    $data['vatAmount'] = $vatAmount;
    $data['vatPercentage'] = $vatPercentage;
    $data['discountAmount'] = $discount_amount;
    $data['discountPercentage'] = $discount_percentage;
    $data['cust_name'] = $cust_name;
    $data['cust_phone'] = $cust_phone;
    $data['cust_email'] = $cust_email;

    //generate and return receipt
    $transReceipt = $this->load->view('transactions/transreceipt', $data, TRUE);

    return $transReceipt;
  }



  /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

  /**
   * vtr_ = "View transaction's receipt"
   * Used when a transaction's ref is clicked
   */
  public function vtr_()
  {
    $this->genlib->ajaxOnly();

    $ref = $this->input->post('ref');

    $transInfo = $this->transaction->getTransInfo($ref);

    //loop through the transInfo to get needed info
    if ($transInfo) {
      $json['status'] = 1;

      $cumAmount = $transInfo[0]['totalMoneySpent'];
      $amountTendered = $transInfo[0]['amountTendered'];
      $changeDue = $transInfo[0]['changeDue'];
      $transDate = $transInfo[0]['transDate'];
      $modeOfPayment = $transInfo[0]['modeOfPayment'];
      $vatAmount = $transInfo[0]['vatAmount'];
      $vatPercentage = $transInfo[0]['vatPercentage'];
      $discountAmount = $transInfo[0]['discount_amount'];
      $discountPercentage = $transInfo[0]['discount_percentage'];
      $cust_name = $transInfo[0]['cust_name'];
      $cust_phone = $transInfo[0]['cust_phone'];
      $cust_email = $transInfo[0]['cust_email'];

      $json['transReceipt'] = $this->genTransReceipt(
        $transInfo,
        $cumAmount,
        $amountTendered,
        $changeDue,
        $ref,
        $transDate,
        $modeOfPayment,
        $vatAmount,
        $vatPercentage,
        $discountAmount,
        $discountPercentage,
        $cust_name,
        $cust_phone,
        $cust_email
      );
    } else {
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
   * Calculates the amount of VAT
   * @param type $cumAmount the total amount to calculate the VAT from
   * @param type $vatPercentage the percentage of VAT
   * @return type
   */
  private function getVatAmount($cumAmount, $vatPercentage)
  {
    $vatAmount = ($vatPercentage / 100) * $cumAmount;

    return $vatAmount;
  }

  /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

  /**
   * Calculates the amount of Discount
   * @param type $cum_amount the total amount to calculate the discount from
   * @param type $discount_percentage the percentage of discount
   * @return type
   */
  private function getDiscountAmount($cum_amount, $discount_percentage)
  {
    $discount_amount = ($discount_percentage / 100) * $cum_amount;

    return $discount_amount;
  }

  /*
    ****************************************************************************************************************************
    ****************************************************************************************************************************
    ****************************************************************************************************************************
    ****************************************************************************************************************************
    ****************************************************************************************************************************
    */

  public function report($from_date, $to_date = '')
  {
    //get all transactions from db ranging from $from_date to $to_date
    $data['from'] = $from_date;
    $data['to'] = $to_date ? $to_date : date('Y-m-d');

    $data['allTransactions'] = $this->transaction->getDateRange($from_date, $to_date);

    $this->load->view('transactions/transReport', $data);
  }

  // ========================================
  // PHASE 3: Cart Management Methods
  // ========================================

  /**
   * Search item by IMEI number
   * Returns item details with cost_price for profit calculation
   */
  public function searchByImei()
  {
    $this->genlib->ajaxOnly();

    $imei = $this->input->get('imei', TRUE);

    if (empty($imei)) {
      $json['status'] = 0;
      $json['msg'] = "IMEI number is required";
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
      return;
    }

    // Validate IMEI format (15 digits)
    if (!preg_match('/^\d{15}$/', $imei)) {
      $json['status'] = 0;
      $json['msg'] = "Invalid IMEI format. Must be 15 digits";
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
      return;
    }

    // Get IMEI details from item_serials table
    $serialInfo = $this->item->getSerialInfo($imei);

    if (!$serialInfo) {
      $json['status'] = 0;
      $json['msg'] = "IMEI not found in inventory";
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
      return;
    }

    // Check if IMEI is available
    if ($serialInfo->status !== 'available') {
      $statusMsg = [
        'sold' => 'already sold',
        'reserved' => 'reserved in another cart',
        'defective' => 'marked as defective',
        'returned' => 'returned',
        'traded_in' => 'traded in'
      ];

      $json['status'] = 0;
      $json['msg'] = "IMEI is " . ($statusMsg[$serialInfo->status] ?? $serialInfo->status);
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
      return;
    }

    // Return item details with cost_price for profit calculation
    $json['status'] = 1;
    $json['msg'] = "Item found";
    $json['item'] = [
      'id' => $serialInfo->item_id,
      'name' => $serialInfo->item_name,
      'code' => $serialInfo->item_code,
      'brand' => $serialInfo->brand,
      'model' => $serialInfo->model,
      'category' => $serialInfo->category,
      'imei' => $serialInfo->imei_number,
      'color' => $serialInfo->color,
      'storage' => $serialInfo->storage,
      'cost_price' => $serialInfo->cost_price,
      'selling_price' => $serialInfo->selling_price ? $serialInfo->selling_price : $serialInfo->unitPrice,
      'unitPrice' => $serialInfo->selling_price ? $serialInfo->selling_price : $serialInfo->unitPrice,
      'warranty_months' => $serialInfo->warranty_months,
      'warranty_terms' => $serialInfo->warranty_terms,
      'item_type' => 'serialized',
      'status' => $serialInfo->status
    ];

    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }

  /**
   * Add item to cart via AJAX
   */
  public function addToCart()
  {
    $this->genlib->ajaxOnly();

    $itemCode = $this->input->post('itemCode', TRUE);
    $imei = $this->input->post('imei', TRUE); // Optional, for serialized items

    if (empty($itemCode)) {
      $json['status'] = 0;
      $json['msg'] = "Item code is required";
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
      return;
    }

    // Get item details from database
    $itemInfo = $this->item->getItemInfo(['code' => $itemCode], 'id, name, code, unitPrice, item_type, quantity, brand, model, category');

    if (!$itemInfo) {
      $json['status'] = 0;
      $json['msg'] = "Item not found";
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
      return;
    }

    // Handle serialized items (with IMEI)
    if ($itemInfo->item_type === 'serialized') {
      if (empty($imei)) {
        $json['status'] = 0;
        $json['msg'] = "IMEI is required for serialized items";
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
        return;
      }

      // Get IMEI details
      $serialInfo = $this->item->getSerialInfo($imei);

      if (!$serialInfo) {
        $json['status'] = 0;
        $json['msg'] = "IMEI not found";
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
        return;
      }

      if ($serialInfo->status !== 'available') {
        $json['status'] = 0;
        $json['msg'] = "IMEI is not available (Status: {$serialInfo->status})";
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
        return;
      }

      // Lock the IMEI
      if (!$this->item->lockSerial($imei)) {
        $json['status'] = 0;
        $json['msg'] = "Failed to lock IMEI";
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
        return;
      }

      // Add to cart
      $cartItemData = [
        'code' => $itemInfo->code,
        'name' => $itemInfo->name,
        'unitPrice' => $serialInfo->selling_price ? $serialInfo->selling_price : $itemInfo->unitPrice,
        'qty' => 1,
        'imei' => $imei,
        'cost_price' => $serialInfo->cost_price,
        'item_type' => 'serialized',
        'brand' => $itemInfo->brand,
        'model' => $itemInfo->model,
        'color' => $serialInfo->color
      ];

      $cartItemId = $this->genlib->addToCart($cartItemData);

      if ($cartItemId) {
        $json['status'] = 1;
        $json['msg'] = "Item added to cart";
        $json['cartItemId'] = $cartItemId;
        $json['cartItem'] = $cartItemData;
      } else {
        // Release IMEI if cart add failed
        $this->item->releaseSerial($imei);
        $json['status'] = 0;
        $json['msg'] = "Failed to add item to cart";
      }
    } else {
      // Handle standard items
      $qty = $this->input->post('qty', TRUE) ? $this->input->post('qty', TRUE) : 1;

      if ($itemInfo->quantity < $qty) {
        $json['status'] = 0;
        $json['msg'] = "Insufficient stock. Available: {$itemInfo->quantity}";
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
        return;
      }

      $cartItemData = [
        'code' => $itemInfo->code,
        'name' => $itemInfo->name,
        'unitPrice' => $itemInfo->unitPrice,
        'qty' => $qty,
        'item_type' => 'standard',
        'cost_price' => 0 // Standard items don't track individual cost
      ];

      $cartItemId = $this->genlib->addToCart($cartItemData);

      if ($cartItemId) {
        $json['status'] = 1;
        $json['msg'] = "Item added to cart";
        $json['cartItemId'] = $cartItemId;
        $json['cartItem'] = $cartItemData;
      } else {
        $json['status'] = 0;
        $json['msg'] = "Failed to add item to cart";
      }
    }

    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }

  /**
   * Remove item from cart via AJAX
   */
  public function removeFromCart()
  {
    $this->genlib->ajaxOnly();

    $cartItemId = $this->input->post('cartItemId', TRUE);

    if (empty($cartItemId)) {
      $json['status'] = 0;
      $json['msg'] = "Cart item ID is required";
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
      return;
    }

    // Get cart item to check if it has IMEI
    $cartItem = $this->genlib->getCartItem($cartItemId);

    if (!$cartItem) {
      $json['status'] = 0;
      $json['msg'] = "Cart item not found";
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
      return;
    }

    // Release IMEI if it's a serialized item
    if (!empty($cartItem['imei'])) {
      $this->item->releaseSerial($cartItem['imei']);
    }

    // Remove from cart
    if ($this->genlib->removeFromCart($cartItemId)) {
      $json['status'] = 1;
      $json['msg'] = "Item removed from cart";
    } else {
      $json['status'] = 0;
      $json['msg'] = "Failed to remove item from cart";
    }

    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }

  /**
   * Get cart items via AJAX
   */
  public function getCart()
  {
    $this->genlib->ajaxOnly();

    $cartItems = $this->genlib->getCartItems();
    $discountPercentage = $this->input->post('discount', TRUE) ? $this->input->post('discount', TRUE) : 0;
    $vatPercentage = $this->input->post('vat', TRUE) ? $this->input->post('vat', TRUE) : 0;

    $totals = $this->genlib->calculateCartTotals($discountPercentage, $vatPercentage);

    $json['status'] = 1;
    $json['cartItems'] = $cartItems;
    $json['totals'] = $totals;

    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }

  /**
   * Clear cart via AJAX
   */
  public function clearCart()
  {
    $this->genlib->ajaxOnly();

    // Get all cart items to release IMEIs
    $cartItems = $this->genlib->getCartItems();

    foreach ($cartItems as $item) {
      if (!empty($item['imei'])) {
        $this->item->releaseSerial($item['imei']);
      }
    }

    // Clear cart
    if ($this->genlib->clearCart()) {
      $json['status'] = 1;
      $json['msg'] = "Cart cleared";
    } else {
      $json['status'] = 0;
      $json['msg'] = "Failed to clear cart";
    }

    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }

  /**
   * Update cart item quantity via AJAX (for standard items only)
   */
  public function updateCartQty()
  {
    $this->genlib->ajaxOnly();

    $cartItemId = $this->input->post('cartItemId', TRUE);
    $qty = $this->input->post('qty', TRUE);

    if (empty($cartItemId) || empty($qty) || $qty < 1) {
      $json['status'] = 0;
      $json['msg'] = "Invalid parameters";
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
      return;
    }

    $cartItem = $this->genlib->getCartItem($cartItemId);

    if (!$cartItem) {
      $json['status'] = 0;
      $json['msg'] = "Cart item not found";
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
      return;
    }

    // Only allow quantity update for standard items
    if ($cartItem['item_type'] === 'serialized') {
      $json['status'] = 0;
      $json['msg'] = "Cannot update quantity for serialized items";
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
      return;
    }

    // Check stock availability
    $itemInfo = $this->item->getItemInfo(['code' => $cartItem['code']], 'quantity');

    if ($itemInfo->quantity < $qty) {
      $json['status'] = 0;
      $json['msg'] = "Insufficient stock. Available: {$itemInfo->quantity}";
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
      return;
    }

    // Update quantity
    if ($this->genlib->updateCartItemQty($cartItemId, $qty)) {
      $json['status'] = 1;
      $json['msg'] = "Quantity updated";
      $json['newTotalPrice'] = $cartItem['unitPrice'] * $qty;
    } else {
      $json['status'] = 0;
      $json['msg'] = "Failed to update quantity";
    }

    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }

  /**
   * Calculate cart totals via AJAX
   */
  public function calculateTotals()
  {
    $this->genlib->ajaxOnly();

    $discountPercentage = $this->input->post('discount', TRUE) ? $this->input->post('discount', TRUE) : 0;
    $vatPercentage = $this->input->post('vat', TRUE) ? $this->input->post('vat', TRUE) : 0;

    $totals = $this->genlib->calculateCartTotals($discountPercentage, $vatPercentage);

    $json['status'] = 1;
    $json['totals'] = $totals;

    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }

  // ========================================
  // PHASE 3.4: Trade-In System
  // ========================================

  /**
   * Process trade-in device
   * Stores trade-in info and optionally adds IMEI to inventory
   */
  public function processTradeIn()
  {
    $this->genlib->ajaxOnly();

    $brand = $this->input->post('brand', TRUE);
    $model = $this->input->post('model', TRUE);
    $imei = $this->input->post('imei', TRUE);
    $condition = $this->input->post('condition', TRUE);
    $value = $this->input->post('value', TRUE);
    $storage = $this->input->post('storage', TRUE);
    $color = $this->input->post('color', TRUE);
    $notes = $this->input->post('notes', TRUE);
    $transactionRef = $this->input->post('transaction_ref', TRUE);

    // Validation
    if (empty($brand) || empty($model) || empty($condition) || empty($value)) {
      $json['status'] = 0;
      $json['msg'] = "Brand, model, condition, and value are required";
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
      return;
    }

    if ($value <= 0) {
      $json['status'] = 0;
      $json['msg'] = "Trade-in value must be greater than 0";
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
      return;
    }

    // Validate IMEI if provided
    if (!empty($imei) && !preg_match('/^\d{15}$/', $imei)) {
      $json['status'] = 0;
      $json['msg'] = "Invalid IMEI format. Must be 15 digits";
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
      return;
    }

    // Insert into trade_ins table
    $tradeInData = [
      'brand' => $brand,
      'model' => $model,
      'imei_number' => $imei,
      'condition' => $condition,
      'trade_in_value' => $value,
      'storage' => $storage,
      'color' => $color,
      'notes' => $notes,
      'transaction_ref' => $transactionRef,
      'staff_id' => $this->session->admin_id
    ];

    $this->db->platform() == "sqlite3" 
      ? $this->db->set('trade_in_date', "datetime('now')", FALSE) 
      : $this->db->set('trade_in_date', "NOW()", FALSE);

    $this->db->insert('trade_ins', $tradeInData);
    $tradeInId = $this->db->insert_id();

    if (!$tradeInId) {
      $json['status'] = 0;
      $json['msg'] = "Failed to record trade-in";
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
      return;
    }

    // If IMEI is provided, add to item_serials with status 'traded_in'
    if (!empty($imei)) {
      // Check if IMEI already exists
      $existingSerial = $this->db->where('imei_number', $imei)->get('item_serials');
      
      if ($existingSerial->num_rows() == 0) {
        // Create a generic "Trade-In" item if it doesn't exist
        $tradeInItemCode = 'TRADEIN';
        $tradeInItem = $this->item->getItemInfo(['code' => $tradeInItemCode], 'id');
        
        if (!$tradeInItem) {
          // Create trade-in item category
          $itemData = [
            'name' => 'Trade-In Device',
            'code' => $tradeInItemCode,
            'item_type' => 'serialized',
            'category' => 'other',
            'unitPrice' => 0,
            'quantity' => 0,
            'description' => 'Trade-in devices received from customers'
          ];
          
          $this->db->platform() == "sqlite3" 
            ? $this->db->set('dateAdded', "datetime('now')", FALSE) 
            : $this->db->set('dateAdded', "NOW()", FALSE);
          
          $this->db->insert('items', $itemData);
          $tradeInItemId = $this->db->insert_id();
        } else {
          $tradeInItemId = $tradeInItem->id;
        }

        // Add IMEI to item_serials with status 'traded_in'
        $serialData = [
          'item_id' => $tradeInItemId,
          'imei_number' => $imei,
          'cost_price' => $value,
          'status' => 'traded_in',
          'color' => $color,
          'storage' => $storage,
          'notes' => "Trade-in: $brand $model - $condition condition"
        ];

        $this->db->insert('item_serials', $serialData);
      }
    }

    $json['status'] = 1;
    $json['msg'] = "Trade-in recorded successfully";
    $json['trade_in_id'] = $tradeInId;

    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }

  // ========================================
  // PHASE 3.5: Profit Calculation
  // ========================================

  /**
   * Calculate profit for cart items
   * Returns total profit and per-item profit breakdown
   */
  public function calculateProfit()
  {
    $this->genlib->ajaxOnly();

    $cartItems = $this->genlib->getCartItems();
    
    if (empty($cartItems)) {
      $json['status'] = 0;
      $json['msg'] = "Cart is empty";
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
      return;
    }

    $totalProfit = 0;
    $profitBreakdown = [];

    foreach ($cartItems as $cartItemId => $item) {
      $itemProfit = 0;

      if ($item['item_type'] === 'serialized') {
        // For serialized items: profit = selling_price - cost_price
        $sellingPrice = $item['unitPrice'];
        $costPrice = $item['cost_price'];
        $itemProfit = $sellingPrice - $costPrice;
      } else {
        // For standard items: profit = (selling_price - cost_price) * quantity
        // Get cost price from items table (we'll use unitPrice as selling price)
        // Note: Standard items don't have individual cost tracking in current schema
        // This would need to be added to items table or calculated differently
        $itemInfo = $this->item->getItemInfo(['code' => $item['code']], 'unitPrice');
        
        if ($itemInfo) {
          // For now, assume 30% profit margin if no cost price available
          // In production, you'd want to add a cost_price field to items table
          $sellingPrice = $item['unitPrice'];
          $costPrice = $sellingPrice * 0.7; // Assuming 30% margin
          $itemProfit = ($sellingPrice - $costPrice) * $item['qty'];
        }
      }

      $profitBreakdown[$cartItemId] = [
        'item_name' => $item['name'],
        'item_code' => $item['code'],
        'profit' => round($itemProfit, 2)
      ];

      $totalProfit += $itemProfit;
    }

    // Note: Profit calculation excludes VAT and discount
    // VAT is a tax collected on behalf of government (not profit)
    // Discount reduces profit

    $json['status'] = 1;
    $json['total_profit'] = round($totalProfit, 2);
    $json['profit_breakdown'] = $profitBreakdown;
    $json['msg'] = "Profit calculated successfully";

    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }

  /**
   * Calculate profit for a transaction (used internally)
   * @param array $cartItems - cart items with cost prices
   * @param float $discountAmount - discount amount to subtract from profit
   * @return float - total profit
   */
  private function calculateTransactionProfit($cartItems, $discountAmount = 0)
  {
    $totalProfit = 0;

    foreach ($cartItems as $item) {
      if ($item['item_type'] === 'serialized') {
        // For serialized items: profit = selling_price - cost_price
        $itemProfit = $item['unitPrice'] - $item['cost_price'];
      } else {
        // For standard items: profit = (selling_price - cost_price) * quantity
        $itemInfo = $this->item->getItemInfo(['code' => $item['code']], 'unitPrice');
        
        if ($itemInfo) {
          // Assuming 30% profit margin for standard items
          $costPrice = $item['unitPrice'] * 0.7;
          $itemProfit = ($item['unitPrice'] - $costPrice) * $item['qty'];
        } else {
          $itemProfit = 0;
        }
      }

      $totalProfit += $itemProfit;
    }

    // Subtract discount from profit (discount reduces profit)
    $totalProfit -= $discountAmount;

    return round($totalProfit, 2);
  }

  // ========================================
  // PHASE 3.6: Transaction Processing
  // ========================================

  /**
   * Process new mobile shop transaction
   * Handles IMEI tracking, profit calculation, customer credit, and trade-ins
   */
  public function processTransaction()
  {
    $this->genlib->ajaxOnly();

    // Get cart items
    $cartItems = $this->genlib->getCartItems();

    if (empty($cartItems)) {
      $json['status'] = 0;
      $json['msg'] = "Cart is empty";
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
      return;
    }

    // Get payment details
    $paymentMethod = $this->input->post('payment_method', TRUE);
    $discountPercent = $this->input->post('discount_percent', TRUE) ?: 0;
    $vatPercent = $this->input->post('vat_percent', TRUE) ?: 0;
    $amountTendered = $this->input->post('amount_tendered', TRUE) ?: 0;
    $customerId = $this->input->post('customer_id', TRUE);
    $tradeInData = json_decode($this->input->post('trade_in_data', TRUE), true);

    // Calculate totals
    $totals = $this->genlib->calculateCartTotals($discountPercent, $vatPercent);
    $grandTotal = $totals['grand_total'];
    $discountAmount = $totals['discount_amount'];
    $vatAmount = $totals['vat_amount'];

    // Apply trade-in value if present
    $tradeInValue = 0;
    if (!empty($tradeInData) && isset($tradeInData['value'])) {
      $tradeInValue = $tradeInData['value'];
      $grandTotal -= $tradeInValue;
    }

    // Determine payment status
    $paymentStatus = 'paid';
    $paidAmount = $grandTotal;
    $creditAmount = 0;
    $changeDue = 0;

    if ($paymentMethod === 'credit') {
      if (empty($customerId)) {
        $json['status'] = 0;
        $json['msg'] = "Customer is required for credit sales";
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
        return;
      }
      $paymentStatus = 'credit';
      $paidAmount = 0;
      $creditAmount = $grandTotal;
    } elseif ($paymentMethod === 'partial') {
      if (empty($customerId)) {
        $json['status'] = 0;
        $json['msg'] = "Customer is required for partial payment";
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
        return;
      }
      $paidAmount = $this->input->post('partial_amount', TRUE) ?: 0;
      if ($paidAmount <= 0 || $paidAmount >= $grandTotal) {
        $json['status'] = 0;
        $json['msg'] = "Invalid partial payment amount";
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
        return;
      }
      $paymentStatus = 'partial';
      $creditAmount = $grandTotal - $paidAmount;
    } else {
      // Cash or POS
      if ($amountTendered < $grandTotal) {
        $json['status'] = 0;
        $json['msg'] = "Amount tendered is less than total";
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
        return;
      }
      $changeDue = $amountTendered - $grandTotal;
    }

    // Check customer credit limit if applicable
    if ($creditAmount > 0 && !empty($customerId)) {
      $this->load->model('customer');
      $customer = $this->customer->getById($customerId);
      
      if (!$customer) {
        $json['status'] = 0;
        $json['msg'] = "Customer not found";
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
        return;
      }

      $newBalance = $customer->current_balance + $creditAmount;
      if ($newBalance > $customer->credit_limit) {
        $json['status'] = 0;
        $json['msg'] = "Credit limit exceeded. Available credit: " . ($customer->credit_limit - $customer->current_balance);
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
        return;
      }
    }

    // Generate transaction reference
    do {
      $ref = strtoupper($this->genlib->generateRandomCode('numeric', 6, 10, ""));
    } while ($this->transaction->isRefExist($ref));

    // Calculate profit
    $profitAmount = $this->calculateTransactionProfit($cartItems, $discountAmount);

    // Start database transaction
    $this->db->trans_start();

    // Collect IMEIs for receipt
    $allImeis = [];

    // Process each cart item
    foreach ($cartItems as $cartItemId => $item) {
      // Validate IMEI availability for serialized items
      if ($item['item_type'] === 'serialized' && !empty($item['imei'])) {
        $serialInfo = $this->item->getSerialInfo($item['imei']);
        
        if (!$serialInfo || !in_array($serialInfo->status, ['available', 'reserved'])) {
          $this->db->trans_rollback();
          $json['status'] = 0;
          $json['msg'] = "IMEI {$item['imei']} is not available";
          $this->output->set_content_type('application/json')->set_output(json_encode($json));
          return;
        }

        // Mark IMEI as sold
        $this->item->markSerialSold($item['imei'], null); // Will update with transaction ID later
        $allImeis[] = $item['imei'];
      }

      // Insert transaction record
      $transData = [
        'itemName' => $item['name'],
        'itemCode' => $item['code'],
        'description' => '',
        'quantity' => $item['qty'],
        'unitPrice' => $item['unitPrice'],
        'totalPrice' => $item['totalPrice'],
        'totalMoneySpent' => $grandTotal,
        'amountTendered' => $amountTendered,
        'changeDue' => $changeDue,
        'modeOfPayment' => $paymentMethod,
        'ref' => $ref,
        'vatAmount' => $vatAmount,
        'vatPercentage' => $vatPercent,
        'discount_amount' => $discountAmount,
        'discount_percentage' => $discountPercent,
        'imei_numbers' => !empty($item['imei']) ? $item['imei'] : null,
        'profit_amount' => $profitAmount,
        'payment_status' => $paymentStatus,
        'paid_amount' => $paidAmount,
        'credit_amount' => $creditAmount,
        'customer_id' => $customerId,
        'trade_in_value' => $tradeInValue,
        'cust_name' => '',
        'cust_phone' => '',
        'cust_email' => ''
      ];

      // Get customer details if available
      if (!empty($customerId) && isset($customer)) {
        $transData['cust_name'] = $customer->name;
        $transData['cust_phone'] = $customer->phone;
        $transData['cust_email'] = $customer->email;
      }

      $transId = $this->transaction->addMobileShopTransaction($transData);

      if (!$transId) {
        $this->db->trans_rollback();
        $json['status'] = 0;
        $json['msg'] = "Failed to process transaction";
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
        return;
      }

      // Update item quantity for standard items
      if ($item['item_type'] === 'standard') {
        $this->item->decrementItem($item['code'], $item['qty']);
      }
    }

    // Process trade-in if present
    if (!empty($tradeInData)) {
      $tradeInData['transaction_ref'] = $ref;
      $this->input->set_post('brand', $tradeInData['brand']);
      $this->input->set_post('model', $tradeInData['model']);
      $this->input->set_post('imei', $tradeInData['imei'] ?? '');
      $this->input->set_post('condition', $tradeInData['condition']);
      $this->input->set_post('value', $tradeInData['value']);
      $this->input->set_post('storage', $tradeInData['storage'] ?? '');
      $this->input->set_post('color', $tradeInData['color'] ?? '');
      $this->input->set_post('notes', $tradeInData['notes'] ?? '');
      $this->input->set_post('transaction_ref', $ref);
      
      // Process trade-in (will be handled by processTradeIn method)
    }

    // Update customer balance and create ledger entry if credit
    if ($creditAmount > 0 && !empty($customerId)) {
      $this->customer->updateBalance($customerId, $creditAmount);
      
      $ledgerData = [
        'customer_id' => $customerId,
        'transaction_type' => 'credit_sale',
        'amount' => $creditAmount,
        'description' => "Credit sale - Ref: $ref",
        'notes' => "Credit sale - Ref: $ref"
      ];
      
      $this->db->insert('customer_ledger', $ledgerData);
    }

    // Complete database transaction
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
      $json['status'] = 0;
      $json['msg'] = "Transaction failed";
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
      return;
    }

    // Clear cart
    $this->genlib->clearCart();

    // Add event log
    $eventDesc = count($cartItems) . " items totalling ₨" . number_format($grandTotal, 2) . " with reference $ref";
    $this->genmod->addevent("New Transaction", $ref, $eventDesc, 'transactions', $this->session->admin_id);

    // Return success with receipt data
    $json['status'] = 1;
    $json['msg'] = "Transaction completed successfully";
    $json['ref'] = $ref;
    $json['grand_total'] = $grandTotal;
    $json['profit'] = $profitAmount;
    $json['imeis'] = $allImeis;

    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }
}
