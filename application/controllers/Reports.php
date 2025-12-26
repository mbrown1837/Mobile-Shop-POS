<?php
defined('BASEPATH') or exit('');

/**
 * Description of Reports
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 20th Rab. Awwal, 1437AH
 * @date 1st Jan, 2016
 */
class Reports extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->genlib->checkLogin();

    $this->genlib->superOnly();
    
    $this->load->model(['transaction', 'item']);
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
    $data['pageContent'] = $this->load->view('reports', '', TRUE);
    $data['pageTitle'] = "Reports";

    $this->load->view('main', $data);
  }

  // ========================================
  // PHASE 5: Profit Reports
  // ========================================

  /**
   * Daily profit report
   */
  public function profitDaily()
  {
    $date = $this->input->get('date') ?: date('Y-m-d');
    
    $data['date'] = $date;
    $data['report_data'] = $this->getDailyProfitData($date);
    
    $pageData['pageContent'] = $this->load->view('reports/profit_daily', $data, TRUE);
    $pageData['pageTitle'] = "Daily Profit Report - " . date('d M Y', strtotime($date));

    $this->load->view('main', $pageData);
  }

  /**
   * Monthly profit report
   */
  public function profitMonthly()
  {
    $month = $this->input->get('month') ?: date('Y-m');
    
    $data['month'] = $month;
    $data['report_data'] = $this->getMonthlyProfitData($month);
    
    $pageData['pageContent'] = $this->load->view('reports/profit_monthly', $data, TRUE);
    $pageData['pageTitle'] = "Monthly Profit Report - " . date('F Y', strtotime($month . '-01'));

    $this->load->view('main', $pageData);
  }

  /**
   * Get daily profit data
   */
  private function getDailyProfitData($date)
  {
    $query = "SELECT 
                t.ref,
                t.transDate,
                t.totalMoneySpent,
                t.profit_amount,
                t.payment_status,
                t.modeOfPayment,
                CONCAT(a.first_name, ' ', a.last_name) as staff_name,
                c.name as customer_name
              FROM transactions t
              LEFT JOIN admin a ON t.staffId = a.id
              LEFT JOIN customers c ON t.customer_id = c.id
              WHERE DATE(t.transDate) = ?
              GROUP BY t.ref
              ORDER BY t.transDate DESC";
    
    $transactions = $this->db->query($query, [$date])->result();
    
    $totalSales = 0;
    $totalProfit = 0;
    $transactionCount = 0;
    
    foreach ($transactions as $trans) {
      $totalSales += $trans->totalMoneySpent;
      $totalProfit += $trans->profit_amount;
      $transactionCount++;
    }
    
    // Get profit by category
    $categoryQuery = "SELECT 
                        i.category,
                        SUM(t.profit_amount) as category_profit,
                        COUNT(DISTINCT t.ref) as transaction_count
                      FROM transactions t
                      JOIN items i ON t.itemCode = i.code
                      WHERE DATE(t.transDate) = ?
                      GROUP BY i.category";
    
    $categoryData = $this->db->query($categoryQuery, [$date])->result();
    
    // Get profit by staff
    $staffQuery = "SELECT 
                     CONCAT(a.first_name, ' ', a.last_name) as staff_name,
                     SUM(t.totalMoneySpent) as total_sales,
                     SUM(t.profit_amount) as total_profit,
                     COUNT(DISTINCT t.ref) as transaction_count
                   FROM transactions t
                   JOIN admin a ON t.staffId = a.id
                   WHERE DATE(t.transDate) = ?
                   GROUP BY t.staffId";
    
    $staffData = $this->db->query($staffQuery, [$date])->result();
    
    return [
      'transactions' => $transactions,
      'total_sales' => $totalSales,
      'total_profit' => $totalProfit,
      'transaction_count' => $transactionCount,
      'category_data' => $categoryData,
      'staff_data' => $staffData,
      'profit_margin' => $totalSales > 0 ? ($totalProfit / $totalSales) * 100 : 0
    ];
  }

  /**
   * Get monthly profit data
   */
  private function getMonthlyProfitData($month)
  {
    $startDate = $month . '-01';
    $endDate = date('Y-m-t', strtotime($startDate));
    
    // Daily breakdown
    $dailyQuery = "SELECT 
                     DATE(t.transDate) as date,
                     SUM(t.totalMoneySpent) as daily_sales,
                     SUM(t.profit_amount) as daily_profit,
                     COUNT(DISTINCT t.ref) as transaction_count
                   FROM transactions t
                   WHERE DATE(t.transDate) BETWEEN ? AND ?
                   GROUP BY DATE(t.transDate)
                   ORDER BY DATE(t.transDate)";
    
    $dailyData = $this->db->query($dailyQuery, [$startDate, $endDate])->result();
    
    $totalSales = 0;
    $totalProfit = 0;
    $totalTransactions = 0;
    
    foreach ($dailyData as $day) {
      $totalSales += $day->daily_sales;
      $totalProfit += $day->daily_profit;
      $totalTransactions += $day->transaction_count;
    }
    
    // Category breakdown
    $categoryQuery = "SELECT 
                        i.category,
                        SUM(t.totalMoneySpent) as category_sales,
                        SUM(t.profit_amount) as category_profit,
                        COUNT(DISTINCT t.ref) as transaction_count
                      FROM transactions t
                      JOIN items i ON t.itemCode = i.code
                      WHERE DATE(t.transDate) BETWEEN ? AND ?
                      GROUP BY i.category";
    
    $categoryData = $this->db->query($categoryQuery, [$startDate, $endDate])->result();
    
    // Staff performance
    $staffQuery = "SELECT 
                     CONCAT(a.first_name, ' ', a.last_name) as staff_name,
                     SUM(t.totalMoneySpent) as total_sales,
                     SUM(t.profit_amount) as total_profit,
                     COUNT(DISTINCT t.ref) as transaction_count
                   FROM transactions t
                   JOIN admin a ON t.staffId = a.id
                   WHERE DATE(t.transDate) BETWEEN ? AND ?
                   GROUP BY t.staffId
                   ORDER BY total_profit DESC";
    
    $staffData = $this->db->query($staffQuery, [$startDate, $endDate])->result();
    
    // Top selling items
    $topItemsQuery = "SELECT 
                        t.itemName,
                        t.itemCode,
                        SUM(t.quantity) as total_quantity,
                        SUM(t.totalPrice) as total_sales,
                        SUM(t.profit_amount) as total_profit
                      FROM transactions t
                      WHERE DATE(t.transDate) BETWEEN ? AND ?
                      GROUP BY t.itemCode
                      ORDER BY total_profit DESC
                      LIMIT 10";
    
    $topItems = $this->db->query($topItemsQuery, [$startDate, $endDate])->result();
    
    return [
      'daily_data' => $dailyData,
      'total_sales' => $totalSales,
      'total_profit' => $totalProfit,
      'total_transactions' => $totalTransactions,
      'category_data' => $categoryData,
      'staff_data' => $staffData,
      'top_items' => $topItems,
      'profit_margin' => $totalSales > 0 ? ($totalProfit / $totalSales) * 100 : 0,
      'average_daily_profit' => count($dailyData) > 0 ? $totalProfit / count($dailyData) : 0
    ];
  }

  /**
   * Profit by date range
   */
  public function profitRange()
  {
    $fromDate = $this->input->get('from') ?: date('Y-m-01');
    $toDate = $this->input->get('to') ?: date('Y-m-d');
    
    $data['from_date'] = $fromDate;
    $data['to_date'] = $toDate;
    $data['report_data'] = $this->getRangeProfitData($fromDate, $toDate);
    
    $pageData['pageContent'] = $this->load->view('reports/profit_range', $data, TRUE);
    $pageData['pageTitle'] = "Profit Report - " . date('d M Y', strtotime($fromDate)) . " to " . date('d M Y', strtotime($toDate));

    $this->load->view('main', $pageData);
  }

  /**
   * Get profit data for date range
   */
  private function getRangeProfitData($fromDate, $toDate)
  {
    $query = "SELECT 
                DATE(t.transDate) as date,
                SUM(t.totalMoneySpent) as daily_sales,
                SUM(t.profit_amount) as daily_profit,
                COUNT(DISTINCT t.ref) as transaction_count
              FROM transactions t
              WHERE DATE(t.transDate) BETWEEN ? AND ?
              GROUP BY DATE(t.transDate)
              ORDER BY DATE(t.transDate)";
    
    $dailyData = $this->db->query($query, [$fromDate, $toDate])->result();
    
    $totalSales = 0;
    $totalProfit = 0;
    
    foreach ($dailyData as $day) {
      $totalSales += $day->daily_sales;
      $totalProfit += $day->daily_profit;
    }
    
    return [
      'daily_data' => $dailyData,
      'total_sales' => $totalSales,
      'total_profit' => $totalProfit,
      'profit_margin' => $totalSales > 0 ? ($totalProfit / $totalSales) * 100 : 0
    ];
  }
}

