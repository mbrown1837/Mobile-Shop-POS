<?php
defined('BASEPATH') OR exit('');

/**
 * Customers Controller
 * Handles customer credit (Khata) management
 * 
 * @author Mobile Shop POS
 * @date 2024-12-27
 */
class Customers extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        $this->genlib->checkLogin();
        $this->load->model('customer');
    }

    /**
     * Customer list view
     */
    public function index() {
        $data['pageContent'] = $this->load->view('customers/customers', [], TRUE);
        $data['pageTitle'] = "Customers";

        $this->load->view('main', $data);
    }

    /**
     * Load customer list via AJAX
     */
    public function loadCustomers() {
        $this->genlib->ajaxOnly();

        $orderBy = $this->input->get('orderBy', TRUE) ?: 'name';
        $orderFormat = $this->input->get('orderFormat', TRUE) ?: 'ASC';
        $limit = $this->input->get('limit', TRUE) ?: 10;
        $pageNumber = $this->uri->segment(3, 0);
        $start = $pageNumber == 0 ? 0 : ($pageNumber - 1) * $limit;

        $totalCustomers = $this->customer->getTotalCount();

        $this->load->library('pagination');
        $config = $this->genlib->setPaginationConfig($totalCustomers, "customers/loadCustomers", $limit, ['onclick' => 'return loadCustomers(this.href);']);
        $this->pagination->initialize($config);

        $data['customers'] = $this->customer->getAll($orderBy, $orderFormat, $start, $limit);
        $data['range'] = $totalCustomers > 0 ? ($start + 1) . "-" . ($start + ($data['customers'] ? count($data['customers']) : 0)) . " of " . $totalCustomers : "";
        $data['links'] = $this->pagination->create_links();
        $data['sn'] = $start + 1;

        $json['customerTable'] = $this->load->view('customers/customer_list', $data, TRUE);

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    /**
     * Add new customer
     */
    public function add() {
        $this->genlib->ajaxOnly();

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('', '');
        
        $this->form_validation->set_rules('customerName', 'Customer name', ['required', 'trim', 'max_length[100]']);
        $this->form_validation->set_rules('customerPhone', 'Phone number', ['required', 'trim', 'max_length[20]', 'is_unique[customers.phone]']);
        $this->form_validation->set_rules('customerEmail', 'Email', ['trim', 'valid_email', 'max_length[100]']);
        $this->form_validation->set_rules('customerAddress', 'Address', ['trim', 'max_length[255]']);
        $this->form_validation->set_rules('customerCnic', 'CNIC', ['trim', 'max_length[20]']);
        $this->form_validation->set_rules('creditLimit', 'Credit limit', ['required', 'trim', 'numeric', 'greater_than_equal_to[0]']);

        if ($this->form_validation->run() !== FALSE) {
            $customerData = [
                'name' => $this->input->post('customerName', TRUE),
                'phone' => $this->input->post('customerPhone', TRUE),
                'email' => $this->input->post('customerEmail', TRUE),
                'address' => $this->input->post('customerAddress', TRUE),
                'cnic' => $this->input->post('customerCnic', TRUE),
                'credit_limit' => $this->input->post('creditLimit', TRUE)
            ];

            $customerId = $this->customer->add($customerData);

            if ($customerId) {
                $json['status'] = 1;
                $json['msg'] = "Customer added successfully";
                $json['customer_id'] = $customerId;

                // Add event log
                $this->genmod->addevent("New Customer", $customerId, "Customer '{$customerData['name']}' added with credit limit ₨" . number_format($customerData['credit_limit'], 2), 'customers', $this->session->admin_id);
            } else {
                $json['status'] = 0;
                $json['msg'] = "Failed to add customer";
            }
        } else {
            $json = $this->form_validation->error_array();
            $json['status'] = 0;
            $json['msg'] = "Validation failed";
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    /**
     * Edit customer
     */
    public function edit() {
        $this->genlib->ajaxOnly();

        $customerId = $this->input->post('customerId', TRUE);

        if (empty($customerId)) {
            $json['status'] = 0;
            $json['msg'] = "Customer ID is required";
            $this->output->set_content_type('application/json')->set_output(json_encode($json));
            return;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('', '');
        
        $this->form_validation->set_rules('customerName', 'Customer name', ['required', 'trim', 'max_length[100]']);
        $this->form_validation->set_rules('customerPhone', 'Phone number', ['required', 'trim', 'max_length[20]']);
        $this->form_validation->set_rules('customerEmail', 'Email', ['trim', 'valid_email', 'max_length[100]']);
        $this->form_validation->set_rules('customerAddress', 'Address', ['trim', 'max_length[255]']);
        $this->form_validation->set_rules('customerCnic', 'CNIC', ['trim', 'max_length[20]']);
        $this->form_validation->set_rules('creditLimit', 'Credit limit', ['required', 'trim', 'numeric', 'greater_than_equal_to[0]']);
        $this->form_validation->set_rules('customerStatus', 'Status', ['required', 'in_list[active,inactive,blocked]']);

        if ($this->form_validation->run() !== FALSE) {
            $customerData = [
                'name' => $this->input->post('customerName', TRUE),
                'phone' => $this->input->post('customerPhone', TRUE),
                'email' => $this->input->post('customerEmail', TRUE),
                'address' => $this->input->post('customerAddress', TRUE),
                'cnic' => $this->input->post('customerCnic', TRUE),
                'credit_limit' => $this->input->post('creditLimit', TRUE),
                'status' => $this->input->post('customerStatus', TRUE)
            ];

            $updated = $this->customer->update($customerId, $customerData);

            if ($updated) {
                $json['status'] = 1;
                $json['msg'] = "Customer updated successfully";

                // Add event log
                $this->genmod->addevent("Customer Updated", $customerId, "Customer '{$customerData['name']}' details updated", 'customers', $this->session->admin_id);
            } else {
                $json['status'] = 0;
                $json['msg'] = "No changes made or update failed";
            }
        } else {
            $json = $this->form_validation->error_array();
            $json['status'] = 0;
            $json['msg'] = "Validation failed";
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    /**
     * Delete customer (soft delete)
     */
    public function delete() {
        $this->genlib->ajaxOnly();

        $customerId = $this->input->post('customerId', TRUE);

        if (empty($customerId)) {
            $json['status'] = 0;
            $json['msg'] = "Customer ID is required";
            $this->output->set_content_type('application/json')->set_output(json_encode($json));
            return;
        }

        $customer = $this->customer->getById($customerId);

        if (!$customer) {
            $json['status'] = 0;
            $json['msg'] = "Customer not found";
            $this->output->set_content_type('application/json')->set_output(json_encode($json));
            return;
        }

        if ($customer->current_balance > 0) {
            $json['status'] = 0;
            $json['msg'] = "Cannot delete customer with outstanding balance";
            $this->output->set_content_type('application/json')->set_output(json_encode($json));
            return;
        }

        $deleted = $this->customer->delete($customerId);

        if ($deleted) {
            $json['status'] = 1;
            $json['msg'] = "Customer deactivated successfully";

            // Add event log
            $this->genmod->addevent("Customer Deleted", $customerId, "Customer '{$customer->name}' deactivated", 'customers', $this->session->admin_id);
        } else {
            $json['status'] = 0;
            $json['msg'] = "Failed to delete customer";
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    /**
     * View customer ledger
     */
    public function viewLedger() {
        $customerId = $this->uri->segment(3);

        if (empty($customerId)) {
            redirect('customers');
            return;
        }

        $customer = $this->customer->getById($customerId);

        if (!$customer) {
            redirect('customers');
            return;
        }

        $data['customer'] = $customer;
        $data['ledger'] = $this->customer->getLedger($customerId);
        $data['stats'] = $this->customer->getStats($customerId);

        $pageData['pageContent'] = $this->load->view('customers/ledger', $data, TRUE);
        $pageData['pageTitle'] = "Customer Ledger - " . $customer->name;

        $this->load->view('main', $pageData);
    }

    /**
     * Record payment from customer
     */
    public function recordPayment() {
        $this->genlib->ajaxOnly();

        $customerId = $this->input->post('customerId', TRUE);
        $amount = $this->input->post('amount', TRUE);
        $notes = $this->input->post('notes', TRUE);

        if (empty($customerId) || empty($amount)) {
            $json['status'] = 0;
            $json['msg'] = "Customer ID and amount are required";
            $this->output->set_content_type('application/json')->set_output(json_encode($json));
            return;
        }

        if ($amount <= 0) {
            $json['status'] = 0;
            $json['msg'] = "Amount must be greater than 0";
            $this->output->set_content_type('application/json')->set_output(json_encode($json));
            return;
        }

        $customer = $this->customer->getById($customerId);

        if (!$customer) {
            $json['status'] = 0;
            $json['msg'] = "Customer not found";
            $this->output->set_content_type('application/json')->set_output(json_encode($json));
            return;
        }

        if ($amount > $customer->current_balance) {
            $json['status'] = 0;
            $json['msg'] = "Payment amount exceeds outstanding balance";
            $this->output->set_content_type('application/json')->set_output(json_encode($json));
            return;
        }

        $recorded = $this->customer->recordPayment($customerId, $amount, $notes);

        if ($recorded) {
            $json['status'] = 1;
            $json['msg'] = "Payment recorded successfully";

            // Add event log
            $this->genmod->addevent("Payment Received", $customerId, "Payment of ₨" . number_format($amount, 2) . " received from '{$customer->name}'", 'customer_ledger', $this->session->admin_id);
        } else {
            $json['status'] = 0;
            $json['msg'] = "Failed to record payment";
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    /**
     * Search customers (AJAX)
     */
    public function search() {
        $this->genlib->ajaxOnly();

        $query = $this->input->get('q', TRUE);

        if (empty($query)) {
            $json['customers'] = [];
            $this->output->set_content_type('application/json')->set_output(json_encode($json));
            return;
        }

        $results = $this->customer->search($query, 10);

        $json['customers'] = $results ?: [];

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    /**
     * Get customer info (AJAX)
     */
    public function getCustomerInfo() {
        $this->genlib->ajaxOnly();

        $customerId = $this->input->get('id', TRUE);

        if (empty($customerId)) {
            $json['status'] = 0;
            $json['msg'] = "Customer ID is required";
            $this->output->set_content_type('application/json')->set_output(json_encode($json));
            return;
        }

        $customer = $this->customer->getById($customerId);

        if (!$customer) {
            $json['status'] = 0;
            $json['msg'] = "Customer not found";
            $this->output->set_content_type('application/json')->set_output(json_encode($json));
            return;
        }

        $json['status'] = 1;
        $json['customer'] = $customer;

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }
}
