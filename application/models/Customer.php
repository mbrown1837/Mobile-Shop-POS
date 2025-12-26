<?php
defined('BASEPATH') OR exit('');

/**
 * Customer Model
 * Handles customer credit (Khata) management
 * 
 * @author Mobile Shop POS
 * @date 2024-12-27
 */
class Customer extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }

    /**
     * Add new customer
     * @param array $data - customer data
     * @return int|bool - customer ID or FALSE
     */
    public function add($data) {
        if (empty($data['name']) || empty($data['phone'])) {
            return FALSE;
        }

        $insertData = [
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => isset($data['email']) ? $data['email'] : '',
            'address' => isset($data['address']) ? $data['address'] : '',
            'cnic' => isset($data['cnic']) ? $data['cnic'] : '',
            'credit_limit' => isset($data['credit_limit']) ? $data['credit_limit'] : 0,
            'current_balance' => 0,
            'status' => 'active'
        ];

        $this->db->platform() == "sqlite3" 
            ? $this->db->set('created_at', "datetime('now')", FALSE) 
            : $this->db->set('created_at', "NOW()", FALSE);

        $this->db->insert('customers', $insertData);

        return $this->db->insert_id() ?: FALSE;
    }

    /**
     * Get all customers with pagination
     * @param string $orderBy
     * @param string $orderFormat
     * @param int $start
     * @param int $limit
     * @return array|bool
     */
    public function getAll($orderBy = 'name', $orderFormat = 'ASC', $start = 0, $limit = 10) {
        $this->db->order_by($orderBy, $orderFormat);
        $this->db->limit($limit, $start);
        
        $query = $this->db->get('customers');
        
        return $query->num_rows() > 0 ? $query->result() : FALSE;
    }

    /**
     * Get customer by phone number
     * @param string $phone
     * @return object|bool
     */
    public function getByPhone($phone) {
        if (empty($phone)) return FALSE;

        $this->db->where('phone', $phone);
        $query = $this->db->get('customers');

        return $query->num_rows() > 0 ? $query->row() : FALSE;
    }

    /**
     * Get customer by ID
     * @param int $id
     * @return object|bool
     */
    public function getById($id) {
        if (empty($id)) return FALSE;

        $this->db->where('id', $id);
        $query = $this->db->get('customers');

        return $query->num_rows() > 0 ? $query->row() : FALSE;
    }

    /**
     * Update customer balance
     * @param int $customerId
     * @param float $amount - amount to add (positive) or subtract (negative)
     * @return bool
     */
    public function updateBalance($customerId, $amount) {
        if (empty($customerId) || !is_numeric($amount)) return FALSE;

        $this->db->where('id', $customerId);
        $this->db->set('current_balance', 'current_balance + ' . $amount, FALSE);
        
        $this->db->platform() == "sqlite3" 
            ? $this->db->set('updated_at', "datetime('now')", FALSE) 
            : $this->db->set('updated_at', "NOW()", FALSE);

        $this->db->update('customers');

        return $this->db->affected_rows() > 0;
    }

    /**
     * Get customer ledger entries
     * @param int $customerId
     * @param int $limit
     * @return array|bool
     */
    public function getLedger($customerId, $limit = 50) {
        if (empty($customerId)) return FALSE;

        $this->db->where('customer_id', $customerId);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);

        $query = $this->db->get('customer_ledger');

        return $query->num_rows() > 0 ? $query->result() : FALSE;
    }

    /**
     * Record payment from customer
     * @param int $customerId
     * @param float $amount
     * @param string $notes
     * @return bool
     */
    public function recordPayment($customerId, $amount, $notes = '') {
        if (empty($customerId) || $amount <= 0) return FALSE;

        $customer = $this->getById($customerId);
        if (!$customer) return FALSE;

        $this->db->trans_start();

        // Update customer balance (subtract payment)
        $this->updateBalance($customerId, -$amount);

        // Get new balance
        $newCustomer = $this->getById($customerId);
        $newBalance = $newCustomer ? $newCustomer->current_balance : 0;

        // Create ledger entry
        $ledgerData = [
            'customer_id' => $customerId,
            'transaction_type' => 'payment',
            'amount' => $amount,
            'payment_method' => 'cash', // Default, can be parameterized
            'description' => $notes,
            'notes' => $notes
        ];

        $this->db->platform() == "sqlite3" 
            ? $this->db->set('created_at', "datetime('now')", FALSE) 
            : $this->db->set('created_at', "NOW()", FALSE);

        $this->db->insert('customer_ledger', $ledgerData);

        $this->db->trans_complete();

        return $this->db->trans_status() !== FALSE;
    }

    /**
     * Check if customer can take more credit
     * @param int $customerId
     * @param float $amount
     * @return bool
     */
    public function checkCreditLimit($customerId, $amount) {
        if (empty($customerId) || $amount <= 0) return FALSE;

        $customer = $this->getById($customerId);
        if (!$customer) return FALSE;

        $newBalance = $customer->current_balance + $amount;

        return $newBalance <= $customer->credit_limit;
    }

    /**
     * Search customers by name or phone
     * @param string $query
     * @param int $limit
     * @return array|bool
     */
    public function search($query, $limit = 10) {
        if (empty($query)) return FALSE;

        $this->db->like('name', $query);
        $this->db->or_like('phone', $query);
        $this->db->limit($limit);

        $result = $this->db->get('customers');

        return $result->num_rows() > 0 ? $result->result() : FALSE;
    }

    /**
     * Update customer details
     * @param int $customerId
     * @param array $data
     * @return bool
     */
    public function update($customerId, $data) {
        if (empty($customerId) || empty($data)) return FALSE;

        $allowed = ['name', 'phone', 'email', 'address', 'cnic', 'credit_limit', 'status'];
        $updateData = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $updateData[$key] = $value;
            }
        }

        if (empty($updateData)) return FALSE;

        $this->db->platform() == "sqlite3" 
            ? $this->db->set('updated_at', "datetime('now')", FALSE) 
            : $this->db->set('updated_at', "NOW()", FALSE);

        $this->db->where('id', $customerId);
        $this->db->update('customers', $updateData);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Soft delete customer (set status to inactive)
     * @param int $customerId
     * @return bool
     */
    public function delete($customerId) {
        if (empty($customerId)) return FALSE;

        return $this->update($customerId, ['status' => 'inactive']);
    }

    /**
     * Get total number of customers
     * @param string $status - optional filter by status
     * @return int
     */
    public function getTotalCount($status = null) {
        if ($status) {
            $this->db->where('status', $status);
        }

        return $this->db->count_all_results('customers');
    }

    /**
     * Get customers with outstanding balance
     * @param int $limit
     * @return array|bool
     */
    public function getWithBalance($limit = 50) {
        $this->db->where('current_balance >', 0);
        $this->db->order_by('current_balance', 'DESC');
        $this->db->limit($limit);

        $query = $this->db->get('customers');

        return $query->num_rows() > 0 ? $query->result() : FALSE;
    }

    /**
     * Get customer statistics
     * @param int $customerId
     * @return array|bool
     */
    public function getStats($customerId) {
        if (empty($customerId)) return FALSE;

        $customer = $this->getById($customerId);
        if (!$customer) return FALSE;

        // Get total credit sales
        $this->db->select_sum('credit_amount');
        $this->db->where('customer_id', $customerId);
        $this->db->where('payment_status !=', 'paid');
        $creditSales = $this->db->get('transactions')->row();

        // Get total payments
        $this->db->select_sum('amount');
        $this->db->where('customer_id', $customerId);
        $this->db->where('transaction_type', 'payment');
        $payments = $this->db->get('customer_ledger')->row();

        // Get transaction count
        $this->db->where('customer_id', $customerId);
        $transactionCount = $this->db->count_all_results('transactions');

        return [
            'customer' => $customer,
            'total_credit_sales' => $creditSales->credit_amount ?? 0,
            'total_payments' => $payments->amount ?? 0,
            'current_balance' => $customer->current_balance,
            'credit_limit' => $customer->credit_limit,
            'available_credit' => $customer->credit_limit - $customer->current_balance,
            'transaction_count' => $transactionCount
        ];
    }
}
