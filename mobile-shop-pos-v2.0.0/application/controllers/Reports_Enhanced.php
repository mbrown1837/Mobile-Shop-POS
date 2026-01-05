<?php
defined('BASEPATH') or exit('');

/**
 * Enhanced Reports for v1.1.0
 * Simplified and comprehensive reporting
 */
class Reports_Enhanced extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->genlib->checkLogin();
        $this->load->model(['transaction', 'item', 'customer']);
    }

    /**
     * Sales Summary Report - Daily, Monthly, Item-wise
     */
    public function salesSummary()
    {
        $type = $this->input->get('type') ?: 'daily';
        $date = $this->input->get('date') ?: date('Y-m-d');
        $month = $this->input->get('month') ?: date('Y-m');
        
        $data['type'] = $type;
        $data['date'] = $date;
        $data['month'] = $month;
        
        if ($type === 'daily') {
            $data['report'] = $this->getDailySales($date);
            $data['title'] = 'Daily Sales - ' . date('d M Y', strtotime($date));
        } elseif ($type === 'monthly') {
            $data['report'] = $this->getMonthlySales($month);
            $data['title'] = 'Monthly Sales - ' . date('F Y', strtotime($month . '-01'));
        } elseif ($type === 'itemwise') {
            $data['report'] = $this->getItemWiseSales($month);
            $data['title'] = 'Item-wise Sales - ' . date('F Y', strtotime($month . '-01'));
        }
        
        $pageData['pageContent'] = $this->load->view('reports/sales_summary', $data, TRUE);
        $pageData['pageTitle'] = $data['title'];
        
        $this->load->view('main', $pageData);
    }
    
    /**
     * Get daily sales data
     */
    private function getDailySales($date)
    {
        // Total sales
        $this->db->select('COUNT(DISTINCT ref) as total_transactions, SUM(totalMoneySpent) as total_sales, SUM(profit_amount) as total_profit');
        $this->db->where('DATE(transDate)', $date);
        $summary = $this->db->get('transactions')->row();
        
        // Payment method breakdown
        $this->db->select('modeOfPayment, COUNT(DISTINCT ref) as count, SUM(totalMoneySpent) as amount');
        $this->db->where('DATE(transDate)', $date);
        $this->db->group_by('modeOfPayment');
        $payment_methods = $this->db->get('transactions')->result();
        
        // Top selling items - using transactions table directly
        $query = "SELECT t.itemName as name, t.itemCode as code, 
                  COUNT(DISTINCT t.ref) as times_sold, 
                  SUM(t.quantity) as total_qty, 
                  SUM(t.totalPrice) as total_amount
                  FROM transactions t
                  WHERE DATE(t.transDate) = ?
                  GROUP BY t.itemCode
                  ORDER BY times_sold DESC
                  LIMIT 10";
        $top_items = $this->db->query($query, [$date])->result();
        
        return [
            'summary' => $summary,
            'payment_methods' => $payment_methods,
            'top_items' => $top_items
        ];
    }
    
    /**
     * Get monthly sales data
     */
    private function getMonthlySales($month)
    {
        // Total sales for month
        $this->db->select('COUNT(DISTINCT ref) as total_transactions, SUM(totalMoneySpent) as total_sales, SUM(profit_amount) as total_profit');
        $this->db->where('DATE_FORMAT(transDate, "%Y-%m")', $month);
        $summary = $this->db->get('transactions')->row();
        
        // Daily breakdown
        $query = "SELECT DATE(transDate) as date, COUNT(DISTINCT ref) as transactions, SUM(totalMoneySpent) as sales, SUM(profit_amount) as profit
                  FROM transactions
                  WHERE DATE_FORMAT(transDate, '%Y-%m') = ?
                  GROUP BY DATE(transDate)
                  ORDER BY date ASC";
        $daily_breakdown = $this->db->query($query, [$month])->result();
        
        // Category breakdown - using transactions table
        $query = "SELECT i.category, COUNT(DISTINCT t.ref) as transactions, SUM(t.totalPrice) as sales
                  FROM transactions t
                  JOIN items i ON t.itemCode = i.code
                  WHERE DATE_FORMAT(t.transDate, '%Y-%m') = ?
                  GROUP BY i.category";
        $category_breakdown = $this->db->query($query, [$month])->result();
        
        return [
            'summary' => $summary,
            'daily_breakdown' => $daily_breakdown,
            'category_breakdown' => $category_breakdown
        ];
    }
    
    /**
     * Get item-wise sales
     */
    private function getItemWiseSales($month)
    {
        $query = "SELECT 
                    i.code,
                    i.name,
                    i.category,
                    i.brand,
                    i.unitPrice as selling_price,
                    i.cost_price,
                    COUNT(DISTINCT t.ref) as times_sold,
                    SUM(t.quantity) as total_quantity,
                    SUM(t.totalPrice) as total_sales,
                    SUM((i.unitPrice - COALESCE(i.cost_price, 0)) * t.quantity) as total_profit
                  FROM transactions t
                  JOIN items i ON t.itemCode = i.code
                  WHERE DATE_FORMAT(t.transDate, '%Y-%m') = ?
                  GROUP BY t.itemCode
                  ORDER BY total_sales DESC";
        
        $items = $this->db->query($query, [$month])->result();
        
        return ['items' => $items];
    }
    
    /**
     * Khata/Credit Report
     */
    public function khataReport()
    {
        // Customers with outstanding balance
        $this->db->select('id, name, phone, cnic, current_balance, status');
        $this->db->where('current_balance >', 0);
        $this->db->order_by('current_balance', 'DESC');
        $customers_with_balance = $this->db->get('customers')->result();
        
        // Total outstanding
        $this->db->select('SUM(current_balance) as total_outstanding, COUNT(*) as total_customers');
        $this->db->where('current_balance >', 0);
        $summary = $this->db->get('customers')->row();
        
        $data['customers'] = $customers_with_balance;
        $data['summary'] = $summary;
        
        $pageData['pageContent'] = $this->load->view('reports/khata_report', $data, TRUE);
        $pageData['pageTitle'] = 'Khata/Credit Report';
        
        $this->load->view('main', $pageData);
    }
}
