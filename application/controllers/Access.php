<?php
defined('BASEPATH') or exit('');

/**
 * Access Controller
 * Handles authentication and session checks
 */
class Access extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Check Session Status (css)
     * Returns JSON indicating if user is logged in
     */
    public function css()
    {
        // Set JSON header
        $this->output->set_content_type('application/json');
        
        // Check if admin is logged in
        if (isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id'])) {
            // User is logged in
            $response = ['status' => 1, 'message' => 'Logged in'];
        } else {
            // User is not logged in
            $response = ['status' => 0, 'message' => 'Not logged in'];
        }
        
        $this->output->set_output(json_encode($response));
    }

    /**
     * Login function
     * Handles user authentication
     */
    public function login()
    {
        // Set JSON header
        $this->output->set_content_type('application/json');
        
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        
        if (empty($email) || empty($password)) {
            $response = ['status' => 0, 'msg' => 'Email and password required'];
            $this->output->set_output(json_encode($response));
            return;
        }
        
        // Load admin model
        $this->load->model('admin');
        
        // Check credentials
        $admin = $this->db->where('email', $email)->get('admins')->row();
        
        if ($admin && password_verify($password, $admin->password)) {
            // Set session
            $_SESSION['admin_id'] = $admin->id;
            $_SESSION['admin_name'] = $admin->name;
            $_SESSION['admin_email'] = $admin->email;
            $_SESSION['admin_role'] = $admin->role;
            
            $response = ['status' => 1, 'msg' => 'Login successful'];
        } else {
            $response = ['status' => 0, 'msg' => 'Invalid credentials'];
        }
        
        $this->output->set_output(json_encode($response));
    }
}
