<?php
defined('BASEPATH') or exit('');

/**
 * Settings Controller for v1.1.0
 * Shop configuration and admin password management
 */
class Settings extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->genlib->checkLogin();
    }

    /**
     * Settings main page
     */
    public function index()
    {
        $data['pageTitle'] = 'Shop Settings';
        $data['pageContent'] = $this->load->view('settings/settings', '', TRUE);
        
        $this->load->view('main', $data);
    }

    /**
     * Change admin password
     */
    public function changePassword()
    {
        $this->genlib->ajaxOnly();
        
        $currentPassword = $this->input->post('current_password', TRUE);
        $newPassword = $this->input->post('new_password', TRUE);
        $confirmPassword = $this->input->post('confirm_password', TRUE);
        
        // Get current admin
        $admin = $this->db->where('id', $this->session->admin_id)->get('admin')->row();
        
        if (!$admin || !password_verify($currentPassword, $admin->password)) {
            $json['status'] = 0;
            $json['msg'] = 'Current password is incorrect';
        } elseif ($newPassword !== $confirmPassword) {
            $json['status'] = 0;
            $json['msg'] = 'New passwords do not match';
        } elseif (strlen($newPassword) < 6) {
            $json['status'] = 0;
            $json['msg'] = 'Password must be at least 6 characters';
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $this->db->where('id', $this->session->admin_id)
                     ->update('admin', ['password' => $hashedPassword]);
            
            $json['status'] = 1;
            $json['msg'] = 'Password changed successfully';
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }
}
