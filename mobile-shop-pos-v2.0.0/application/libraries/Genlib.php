<?php
defined('BASEPATH') or exit('Access Denied');

/**
 * Description of Genlib
 * Class deals with functions needed in multiple controllers to avoid repetition in each of the controllers
 *
 * @author Amir <amirsanni@gmail.com>
 */
class Genlib
{
  protected $CI;

  public function __construct()
  {
    $this->CI = &get_instance();
  }



  /**
   * 
   * @param type $sname
   * @param type $semail
   * @param type $rname
   * @param type $remail
   * @param type $subject
   * @param type $message
   * @param type $replyToEmail
   * @param type $files
   * @return type
   */
  public function send_email($sname, $semail, $rname, $remail, $subject, $message, $replyToEmail = "", $files = "")
  {
    $this->CI->email->from($semail, $sname);
    $this->CI->email->to($remail, $rname);
    $replyToEmail ? $this->CI->email->reply_to($replyToEmail, $sname) : "";
    $this->CI->email->subject($subject);
    $this->CI->email->message($message);

    //include attachment if $files is set
    if ($files) {
      foreach ($files as $fileLink) {
        $this->CI->email->attach($fileLink, 'inline');
      }
    }

    $send_email = $this->CI->email->send();


    return $send_email ? TRUE : FALSE;
  }

  /**
   * 
   */
  public function superOnly()
  {
    //prevent access if user is not logged in or role is not "Super"
    if (empty($_SESSION['admin_id']) || (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] !== "Super")) {
      redirect(base_url());
    }
  }


  /**
   * 
   * @return string
   */
  public function checkLogin()
  {
    if (empty($_SESSION['admin_id'])) {
      //redirect to log in page            
      redirect(base_url() . '?red_uri=' . uri_string()); //redirects to login page
    } else {
      return "";
    }
  }

  /**
   * Ensure request is an AJAX request
   * @return string
   */
  public function ajaxOnly()
  {
    //display uri error if request is not from AJAX
    if (!$this->CI->input->is_ajax_request()) {
      redirect(base_url());
    } else {
      return "";
    }
  }




  /**
   * Set and return pagination configuration
   * @param type $totalRows
   * @param type $urlToCall
   * @param type $limit
   * @param type $attributes
   * @return boolean
   */
  public function setPaginationConfig($totalRows, $urlToCall, $limit, $attributes)
  {
    $config = [
      'total_rows' => $totalRows, 'base_url' => base_url() . $urlToCall, 'per_page' => $limit, 'uri_segment' => 3,
      'num_links' => 5, 'use_page_numbers' => TRUE, 'first_link' => FALSE, 'last_link' => FALSE,
      'prev_link' => '&lt;&lt;', 'next_link' => '&gt;&gt;', 'full_tag_open' => "<ul class='pagination'>", 'full_tag_close' => '</ul>',
      'num_tag_open' => '<li>', 'num_tag_close' => '</li>', 'next_tag_open' => '<li>', 'next_tag_close' => '</li>',
      'prev_tag_open' => '<li>', 'prev_tag_close' => '</li>', 'cur_tag_open' => '<li><a><b style="color:black">',
      'cur_tag_close' => '</b></a></li>', 'attributes' => $attributes
    ];


    return $config;
  }

  /**
   * @description function to generate random string with an underscore in between
   * @param string $codeType string to pass as 2nd param to random_string() e.g. alnum, numeric
   * @param int $minLength minimum length of string to generate
   * @param int $maxLength maximum length of string to generate
   * @param string $delimiter [optional] The string to put in between the first and second strings Default is underscore
   * @return string $code the new randomly generated code
   */
  public function generateRandomCode($codeType, $minLength, $maxLength, $delimiter = "_")
  {
    $totLength = rand($minLength, $maxLength - 1);

    $b4_ = rand(1, $totLength - 1); //number of strings before the underscore
    $afta_ = $totLength - $b4_; //number of strings after the underscore

    $code = random_string($codeType, $b4_) . $delimiter . random_string($codeType, $afta_);

    return $code;
  }

  // ========================================
  // PHASE 3: Cart Management Methods
  // ========================================

  /**
   * Initialize cart in session if not exists
   */
  public function initCart()
  {
    if (!isset($_SESSION['pos_cart'])) {
      $_SESSION['pos_cart'] = [
        'items' => [],
        'last_activity' => time(),
        'timeout' => 1800 // 30 minutes
      ];
    }
  }

  /**
   * Add item to cart
   * @param array $itemData - item details including code, name, price, qty, imei (optional)
   * @return bool
   */
  public function addToCart($itemData)
  {
    $this->initCart();
    $this->checkCartTimeout();

    if (empty($itemData['code']) || empty($itemData['name']) || !isset($itemData['unitPrice']) || !isset($itemData['qty'])) {
      return FALSE;
    }

    // Generate unique cart item ID
    $cartItemId = uniqid('cart_', true);

    $_SESSION['pos_cart']['items'][$cartItemId] = [
      'code' => $itemData['code'],
      'name' => $itemData['name'],
      'unitPrice' => $itemData['unitPrice'],
      'qty' => $itemData['qty'],
      'totalPrice' => $itemData['unitPrice'] * $itemData['qty'],
      'imei' => isset($itemData['imei']) ? $itemData['imei'] : null,
      'cost_price' => isset($itemData['cost_price']) ? $itemData['cost_price'] : 0,
      'item_type' => isset($itemData['item_type']) ? $itemData['item_type'] : 'standard',
      'added_at' => time()
    ];

    $_SESSION['pos_cart']['last_activity'] = time();

    return $cartItemId;
  }

  /**
   * Remove item from cart
   * @param string $cartItemId
   * @return bool
   */
  public function removeFromCart($cartItemId)
  {
    $this->initCart();

    if (isset($_SESSION['pos_cart']['items'][$cartItemId])) {
      unset($_SESSION['pos_cart']['items'][$cartItemId]);
      $_SESSION['pos_cart']['last_activity'] = time();
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Get all cart items
   * @return array
   */
  public function getCartItems()
  {
    $this->initCart();
    $this->checkCartTimeout();

    return $_SESSION['pos_cart']['items'];
  }

  /**
   * Get cart item by ID
   * @param string $cartItemId
   * @return array|bool
   */
  public function getCartItem($cartItemId)
  {
    $this->initCart();

    return isset($_SESSION['pos_cart']['items'][$cartItemId]) ? $_SESSION['pos_cart']['items'][$cartItemId] : FALSE;
  }

  /**
   * Clear entire cart
   * @return bool
   */
  public function clearCart()
  {
    if (isset($_SESSION['pos_cart'])) {
      unset($_SESSION['pos_cart']);
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Calculate cart totals
   * @param float $discountPercentage
   * @param float $vatPercentage
   * @return array
   */
  public function calculateCartTotals($discountPercentage = 0, $vatPercentage = 0)
  {
    $this->initCart();

    $subtotal = 0;
    foreach ($_SESSION['pos_cart']['items'] as $item) {
      $subtotal += $item['totalPrice'];
    }

    $discountAmount = ($discountPercentage / 100) * $subtotal;
    $afterDiscount = $subtotal - $discountAmount;

    $vatAmount = ($vatPercentage / 100) * $afterDiscount;
    $grandTotal = $afterDiscount + $vatAmount;

    return [
      'subtotal' => round($subtotal, 2),
      'discount_percentage' => $discountPercentage,
      'discount_amount' => round($discountAmount, 2),
      'after_discount' => round($afterDiscount, 2),
      'vat_percentage' => $vatPercentage,
      'vat_amount' => round($vatAmount, 2),
      'grand_total' => round($grandTotal, 2),
      'item_count' => count($_SESSION['pos_cart']['items'])
    ];
  }

  /**
   * Calculate cart totals - Simplified (v1.1.0)
   * Only discount amount, no percentage or VAT
   * @param float $discountAmount
   * @return array
   */
  public function calculateCartTotalsSimple($discountAmount = 0)
  {
    $this->initCart();

    $subtotal = 0;
    foreach ($_SESSION['pos_cart']['items'] as $item) {
      $subtotal += $item['totalPrice'];
    }

    // Simple calculation: Subtotal - Discount = Grand Total
    $grandTotal = $subtotal - $discountAmount;
    
    // Ensure grand total is not negative
    if ($grandTotal < 0) {
      $grandTotal = 0;
    }

    return [
      'subtotal' => round($subtotal, 2),
      'discount_amount' => round($discountAmount, 2),
      'grand_total' => round($grandTotal, 2),
      'item_count' => count($_SESSION['pos_cart']['items'])
    ];
  }

  /**
   * Check cart timeout and release locked IMEIs if expired
   * @return bool
   */
  public function checkCartTimeout()
  {
    if (!isset($_SESSION['pos_cart'])) {
      return FALSE;
    }

    $lastActivity = $_SESSION['pos_cart']['last_activity'];
    $timeout = $_SESSION['pos_cart']['timeout'];

    if ((time() - $lastActivity) > $timeout) {
      // Release all locked IMEIs
      $this->CI->load->model('item');
      foreach ($_SESSION['pos_cart']['items'] as $item) {
        if (!empty($item['imei'])) {
          $this->CI->item->releaseSerial($item['imei']);
        }
      }

      // Clear cart
      $this->clearCart();
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Get cart item count
   * @return int
   */
  public function getCartItemCount()
  {
    $this->initCart();
    return count($_SESSION['pos_cart']['items']);
  }

  /**
   * Update cart item quantity (for standard items only)
   * @param string $cartItemId
   * @param int $qty
   * @return bool
   */
  public function updateCartItemQty($cartItemId, $qty)
  {
    $this->initCart();

    if (isset($_SESSION['pos_cart']['items'][$cartItemId]) && $qty > 0) {
      $_SESSION['pos_cart']['items'][$cartItemId]['qty'] = $qty;
      $_SESSION['pos_cart']['items'][$cartItemId]['totalPrice'] = $_SESSION['pos_cart']['items'][$cartItemId]['unitPrice'] * $qty;
      $_SESSION['pos_cart']['last_activity'] = time();
      return TRUE;
    }

    return FALSE;
  }
}
