<?php
defined('BASEPATH') or exit('');

/**
 * Description of Dashboard
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 31st Dec, 2015
 */
class Dashboard extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    // For AJAX requests, return JSON error instead of redirect
    if ($this->input->is_ajax_request() && empty($_SESSION['admin_id'])) {
      $this->output->set_content_type('application/json')
                   ->set_output(json_encode(['status' => 0, 'error' => 'Session expired']))
                   ->_display();
      exit;
    }

    $this->genlib->checkLogin();

    $this->load->model(['item', 'transaction', 'analytic']);
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
  public function index()
  {
    $data['topDemanded'] = $this->analytic->topDemanded();
    $data['leastDemanded'] = $this->analytic->leastDemanded();
    $data['highestEarners'] = $this->analytic->highestEarners();
    $data['lowestEarners'] = $this->analytic->lowestEarners();
    $data['totalItems'] = $this->db->count_all('items');
    $data['totalSalesToday'] = (int)$this->analytic->totalSalesToday();
    $data['totalTransactions'] = $this->transaction->totalTransactions();
    $data['dailyTransactions'] = $this->analytic->getDailyTrans();
    $data['transByDays'] = $this->analytic->getTransByDays();
    $data['transByMonths'] = $this->analytic->getTransByMonths();
    $data['transByYears'] = $this->analytic->getTransByYears();
    
    // New metrics for v1.1.0
    // Today's total sales amount
    $todaySales = $this->db->select('SUM(totalMoneySpent) as total')
                           ->where('DATE(transDate)', date('Y-m-d'))
                           ->get('transactions')->row();
    $data['totalEarnedToday'] = $todaySales->total ?? 0;
    
    // Today's profit
    $todayProfit = $this->db->select('SUM(profit_amount) as total')
                            ->where('DATE(transDate)', date('Y-m-d'))
                            ->get('transactions')->row();
    $data['totalProfitToday'] = $todayProfit->total ?? 0;
    
    // Outstanding khata
    $khata = $this->db->select('SUM(current_balance) as total, COUNT(*) as count')
                      ->where('current_balance >', 0)
                      ->get('customers')->row();
    $data['totalKhataOutstanding'] = $khata->total ?? 0;
    $data['customersWithBalance'] = $khata->count ?? 0;
    
    // Low stock items (less than 10)
    $lowStock = $this->db->where('quantity <', 10)
                         ->where('item_type', 'standard')
                         ->count_all_results('items');
    $data['lowStockItems'] = $lowStock;

    $values['pageContent'] = $this->load->view('dashboard', $data, TRUE);

    $values['pageTitle'] = "Dashboard";

    $this->load->view('main', $values);
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
   * @param type $year year of earnings to fetch
   * @param boolean $not_ajax if request if ajax request or not
   * @return int
   */
  public function earningsGraph($year = "", $not_ajax = false)
  {
    //set the year of expenses to show
    $year_to_fetch = $year ? $year : date('Y');

    $earnings = $this->genmod->getYearEarnings($year_to_fetch);
    $lastEarnings = 0;
    $monthEarnings = array();
    $hightEarn['highestEarning'] = 0;
    $dataarr = [];
    $allMonths = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    if ($earnings) {
      foreach ($allMonths as $allMonth) {
        foreach ($earnings as $get) {
          $earningMonth = date("M", strtotime($get->transDate));

          if ($allMonth == $earningMonth) {
            $lastEarnings += $get->totalPrice;

            $monthEarnings[$allMonth] = $lastEarnings;
          } else {
            if (!array_key_exists($allMonth, $monthEarnings)) {
              $monthEarnings[$allMonth] = 0;
            }
          }
        }

        if ($lastEarnings > $hightEarn['highestEarning']) {
          $hightEarn['highestEarning'] = $lastEarnings;
        }

        $lastEarnings = 0;
      }

      foreach ($monthEarnings as $me) {
        $dataarr[] = $me;
      }
    } else { //if no earning, set earning to 0
      foreach ($allMonths as $allMonth) {
        $dataarr[] = 0;
      }
    }

    //add info into array
    $json = array("total_earnings" => $dataarr, 'earningsYear' => $year_to_fetch);

    //set final output based on where the request is coming from
    if ($not_ajax) {
      return $json;
    } else {
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
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
   */
  function paymentMethodChart($year = '')
  {
    $year_to_fetch = $year ? $year : date('Y');

    $payment_methods = $this->genmod->getPaymentMethods($year_to_fetch);

    $json['status'] = 0;
    $cash = 0;
    $pos = 0;
    $cash_and_pos = 0;
    $json['year'] = $year_to_fetch;

    if ($payment_methods) {
      foreach ($payment_methods as $get) {
        $payment = strtolower(trim($get->modeOfPayment));
        
        if ($payment == "cash") {
          $cash++;
        } else if ($payment == "pos") {
          $pos++;
        } else if ($payment == "cash and pos" || $payment == "partial") {
          $cash_and_pos++;
        }
      }

      //calculate the percentage of each
      $total = $cash + $pos + $cash_and_pos;

      // Prevent division by zero
      if ($total > 0) {
        $cash_percentage = round(($cash / $total) * 100, 2);
        $pos_percentage =  round(($pos / $total) * 100, 2);
        $cash_and_pos_percentage = round(($cash_and_pos / $total) * 100, 2);
      } else {
        $cash_percentage = 0;
        $pos_percentage = 0;
        $cash_and_pos_percentage = 0;
      }

      $json['status'] = 1;
      $json['cash'] = $cash_percentage;
      $json['pos'] = $pos_percentage;
      $json['cashAndPos'] = $cash_and_pos_percentage;
    }

    //set final output
    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }
}
