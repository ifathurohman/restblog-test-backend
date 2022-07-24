<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';

class Users extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_users', 'users');
    }

    public function register_post()
    {
        header("Access-Control-Allow-Origin: *");

		$ID 			= $this->input->post("ID");
		$Name 			= $this->input->post("Name");
		$Username 	    = $this->input->post("Username");
		$Password 	    = $this->input->post("Password");
		$Email 	        = $this->input->post("Email");
          
        $data = array(
			"Name"		 => $Name,
			"Username"	 => $Username,
			"Email"	     => $Email,
		);

        $ck_username = $this->api->get_one_row("ut_users","Username",array("Username" => $Username));
        $ck_email    = $this->api->get_one_row("ut_users","Email",array("Email" => $Email));

        if($Username == ''):
            $this->response([
                'status'    => false,
                'message'   => "Username tidak boleh kosong"
            ],REST_Controller::HTTP_BAD_REQUEST);      
        elseif($ck_username):
            $this->response([
                'status'    => false,
                'message'   => "Username sudah digunakan"
            ],REST_Controller::HTTP_BAD_REQUEST);
        elseif($Password == ''):
            $this->response([
                'status'    => false,
                'message'   => "Password tidak boleh kosong"
            ],REST_Controller::HTTP_BAD_REQUEST);   
        elseif($Email == ''):
            $this->response([
                'status'    => false,
                'message'   => "Email tidak boleh kosong"
            ],REST_Controller::HTTP_BAD_REQUEST);   
        elseif(!filter_var($Email, FILTER_VALIDATE_EMAIL)):  
            $this->response([
                'status'    => false,
                'message'   => "Gunakan email yang valid"
            ],REST_Controller::HTTP_BAD_REQUEST);
        elseif($ck_email):
            $this->response([
                'status'    => false,
                'message'   => "Email sudah digunakan"
            ],REST_Controller::HTTP_BAD_REQUEST);
        else:
            $Password         = $this->main->create_password($Password);
            $data['Password'] = $Password;
            $this->main->general_save("ut_users", $data);
            $this->response([
                'status'    => true,
                'message'   => "data berhasil ditambahkan",
                'data'      => $data
            ],REST_Controller::HTTP_OK);
        endif;

    }

    public function login_post()
    {
        header("Access-Control-Allow-Origin: *");

		$Username 	    = $this->input->post("Username");
		$Password 	    = $this->input->post("Password");
        
        $users 	        = $this->users->getUsers($Username);

        $data = array(
			"Username"	 => $Username,
			"Password"	 => $Password,
		);

        $Password    = $this->main->create_password($Password);

        $ck_username = $this->api->get_one_row("ut_users","Username",array("Username" => $Username));
        $ck_password = $this->api->get_one_row("ut_users","Password",array("Password" => $Password));

        if($Username == ''):
            $this->response([
                'status'    => false,
                'message'   => "Username tidak boleh kosong"
            ],REST_Controller::HTTP_BAD_REQUEST);
        elseif(!$ck_username):
            $this->response([
                'status'    => false,
                'message'   => "Username tidak ditemukan"
            ],REST_Controller::HTTP_BAD_REQUEST);  
        elseif($Password == ''):
            $this->response([
                'status'    => false,
                'message'   => "Password tidak boleh kosong"
            ],REST_Controller::HTTP_BAD_REQUEST);
        elseif(!$ck_password):
            $this->response([
                'status'    => false,
                'message'   => "Password tidak sesuai"
            ],REST_Controller::HTTP_BAD_REQUEST);  
        else:

            $this->load->library('Authorization_Token');

            $data_token['id']           = $users[0]['id'];
            $data_token['name']         = $users[0]['name'];
            $data_token['username']     = $users[0]['username'];
            $data_token['status']       = $users[0]['status'];
            $data_token['useradd']      = $users[0]['useradd'];
            $data_token['dateadd']      = $users[0]['dateadd'];
            $data_token['time']         = time();

            $user_token                 = $this->authorization_token->generateToken($data_token);

            $data_login = array(
                'id'       => $users[0]['id'],
                'name'     => $users[0]['name'],
                'username' => $users[0]['username'],
                'status'   => $users[0]['status'],
                'useradd'  => $users[0]['useradd'],
                'dateadd'  => $users[0]['dateadd'],
                'token'    => $user_token,
            );

             $message = [
                'status'    => true,
                'message'   => "login berhasil",
                'data'      => $data_login,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        endif;
        
    }

    public function resetPassword_post()
    {
        header("Access-Control-Allow-Origin: *");
        
        $this->load->library('Authorization_Token');

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE):
    
            $Username           = $this->post("Username");
            $Password           = $this->post("Password");
            $ConfirmPassword    = $this->post("Confirm_password");

            if($Username == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Username tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST);         
            elseif($Password == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Password tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST);      
            elseif($ConfirmPassword == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Confirm Password tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST);      
            elseif($Password != $ConfirmPassword):  
                $this->response([
                    'status'    => false,
                    'message'   => "Password tidak sesuai"
                ],REST_Controller::HTTP_BAD_REQUEST);
            else:

                $Password         = $this->main->create_password($Password);
                $ConfirmPassword  = $this->main->create_password($ConfirmPassword);
                $data['Password'] = $Password;
                $this->main->general_update("ut_users", $data, array("Username" => $Username));

                $this->response([
                    'status'    => true,
                    'message'   => "data berhasil diubah",
                    'data'      => $data
                ],REST_Controller::HTTP_OK);
            endif;
        else:
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_NOT_FOUND);
        endif;

    }
}
