<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Users extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_users', 'users');
    }

    public function register_post()
    {
        header("Access-Control-Allow-Origin: *");

		$id 			= $this->input->post("id");
		$name 			= $this->input->post("name");
		$username 	    = $this->input->post("username");
		$password 	    = $this->input->post("password");
		$email 	        = $this->input->post("email");
          
        $data = array(
			"name"		 => $name,
			"useradd"    => $name,
			"username"	 => $username,
			"email"	     => $email,
			"status"	 => 0,
		);

        $ck_username = $this->api->get_one_row("ut_users","username",array("username" => $username));
        $ck_email    = $this->api->get_one_row("ut_users","email",array("email" => $email));

        if($username == ''):
            $this->response([
                'status'    => false,
                'message'   => "username tidak boleh kosong"
            ],RestController::HTTP_BAD_REQUEST);      
        elseif($ck_username):
            $this->response([
                'status'    => false,
                'message'   => "username sudah digunakan"
            ],RestController::HTTP_BAD_REQUEST);
        elseif($password == ''):
            $this->response([
                'status'    => false,
                'message'   => "password tidak boleh kosong"
            ],RestController::HTTP_BAD_REQUEST);   
        elseif($email == ''):
            $this->response([
                'status'    => false,
                'message'   => "email tidak boleh kosong"
            ],RestController::HTTP_BAD_REQUEST);   
        elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)):  
            $this->response([
                'status'    => false,
                'message'   => "Gunakan email yang valid"
            ],RestController::HTTP_BAD_REQUEST);
        elseif($ck_email):
            $this->response([
                'status'    => false,
                'message'   => "email sudah digunakan"
            ],RestController::HTTP_BAD_REQUEST);
        else:
            $password         = $this->main->create_password($password);
            $data['password'] = $password;
            $this->main->general_save("ut_users", $data);
            $this->response([
                'status'    => true,
                'message'   => "data berhasil ditambahkan",
                'data'      => $data
            ],RestController::HTTP_OK);
        endif;

    }

    public function login_post()
    {
        header("Access-Control-Allow-Origin: *");

		$username 	    = $this->input->post("username");
		$password 	    = $this->input->post("password");
        
        $users 	        = $this->users->getUsers($username);

        $data = array(
			"username"	 => $username,
			"password"	 => $password,
		);

        $password    = $this->main->create_password($password);

        $ck_username = $this->api->get_one_row("ut_users","username",array("username" => $username));
        $ck_password = $this->api->get_one_row("ut_users","password",array("password" => $password));

        if($username == ''):
            $this->response([
                'status'    => false,
                'message'   => "username tidak boleh kosong"
            ],RestController::HTTP_BAD_REQUEST);
        elseif(!$ck_username):
            $this->response([
                'status'    => false,
                'message'   => "username tidak ditemukan"
            ],RestController::HTTP_BAD_REQUEST);  
        elseif($password == ''):
            $this->response([
                'status'    => false,
                'message'   => "password tidak boleh kosong"
            ],RestController::HTTP_BAD_REQUEST);
        elseif(!$ck_password):
            $this->response([
                'status'    => false,
                'message'   => "password tidak sesuai"
            ],RestController::HTTP_BAD_REQUEST);  
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
            $this->response($message, RestController::HTTP_OK);
        endif;
        
    }

    public function resetpassword_post()
    {
        header("Access-Control-Allow-Origin: *");
        
        $this->load->library('Authorization_Token');

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE):
    
            $username           = $this->post("username");
            $password           = $this->post("password");
            $confirmpassword    = $this->post("confirm_password");

            if($username == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "username tidak boleh kosong"
                ],RestController::HTTP_BAD_REQUEST);         
            elseif($password == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "password tidak boleh kosong"
                ],RestController::HTTP_BAD_REQUEST);      
            elseif($confirmpassword == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Confirm password tidak boleh kosong"
                ],RestController::HTTP_BAD_REQUEST);      
            elseif($password != $confirmpassword):  
                $this->response([
                    'status'    => false,
                    'message'   => "password tidak sesuai"
                ],RestController::HTTP_BAD_REQUEST);
            else:

                $password         = $this->main->create_password($password);
                $confirmpassword  = $this->main->create_password($confirmpassword);
                $data['password'] = $password;
                $this->main->general_update("ut_users", $data, array("username" => $username));

                $this->response([
                    'status'    => true,
                    'message'   => "data berhasil diubah",
                ],RestController::HTTP_OK);
            endif;
        else:
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], RestController::HTTP_NOT_FOUND);
        endif;

    }
}
