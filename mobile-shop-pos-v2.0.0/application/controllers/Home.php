<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Description of Home
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 20th Jan, 2016
 */
class Home extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    if (!empty($_SESSION['admin_id'])) {
      redirect('dashboard');
    }
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
    $this->output->set_header('Access-Control-Allow-Origin: *');
    $this->load->view('home');
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
  public function login()
  {
    $this->genlib->ajaxOnly();

    $this->load->library('form_validation');

    $this->form_validation->set_error_delimiters('', '');

    $this->form_validation->set_rules('email', 'E-mail', ['required', 'trim', 'valid_email']);
    $this->form_validation->set_rules('password', 'Password', ['required']);

    if ($this->form_validation->run() !== FALSE) {
      $givenEmail = strtolower(set_value('email'));
      $givenPassword = set_value('password');

      $passwordInDb = $this->genmod->getTableCol('admin', 'password', 'email', $givenEmail);
      
      // Check if account_status column exists, default to 1 if not
      $account_status = 1;
      $deleted = 0;
      try {
        $account_status = $this->genmod->getTableCol('admin', 'account_status', 'email', $givenEmail);
        if ($account_status === FALSE) $account_status = 1;
      } catch (Exception $e) {
        $account_status = 1;
      }
      try {
        $deleted = $this->genmod->getTableCol('admin', 'deleted', 'email', $givenEmail);
        if ($deleted === FALSE) $deleted = 0;
      } catch (Exception $e) {
        $deleted = 0;
      }

      // Verify password - support both bcrypt and MD5 (legacy)
      $verifiedPassword = FALSE;
      if ($passwordInDb) {
        // Try bcrypt first
        if (password_verify($givenPassword, $passwordInDb)) {
          $verifiedPassword = TRUE;
        }
        // Fallback to MD5 for legacy passwords
        elseif (md5($givenPassword) === $passwordInDb) {
          $verifiedPassword = TRUE;
          // Upgrade to bcrypt
          $newHash = password_hash($givenPassword, PASSWORD_DEFAULT);
          $this->genmod->updateTableCol('admin', 'password', $newHash, 'email', $givenEmail);
        }
      }

      //allow log in if password and email matches and admin's account has not been suspended or deleted
      if ($verifiedPassword && $account_status != 0 && $deleted != 1) {
        $this->load->model('admin');

        //set session details
        $admin_info = $this->admin->get_admin_info($givenEmail);

        if ($admin_info) {
          foreach ($admin_info as $get) {
            $admin_id = $get->id;

            $_SESSION['admin_id'] = $admin_id;
            $_SESSION['admin_email'] = $givenEmail;
            $_SESSION['admin_role'] = $get->role;
            $_SESSION['admin_initial'] = strtoupper(substr($get->first_name, 0, 1));
            $_SESSION['admin_name'] = $get->first_name . " " . $get->last_name;
          }

          //update user's last log in time
          $this->admin->update_last_login($admin_id);
        }

        $json['status'] = 1; //set status to return
      } else { //if password is not correct
        $json['msg'] = "Incorrect email and password combination";
        $json['status'] = 0;
      }
    } else { //if form validation fails            
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
}
