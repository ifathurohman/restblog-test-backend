<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';

class Comments extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");
        $this->load->model('M_comments', 'comments');
    }

    public function getComments_get()
    {
        header("Access-Control-Allow-Origin: *");
    
        $this->load->library('Authorization_Token');

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE):

            $id                  = $this->get('Id'); 
             
            if($id === null):
                 $data = $this->comments->getComments();
            else:
                 $data = $this->comments->getComments($id);
            endif;

            if($data):
                $this->response([
                    'status'    => true,
                    'data'      => $data
                ],REST_Controller::HTTP_OK);
            else:
                 $this->response([
                    'status'    => true,
                    'message'   => "data tidak ditemukan"
                ],REST_Controller::HTTP_NOT_FOUND);
            endif;         

        else:
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_NOT_FOUND);
        endif;
    }

    public function createdComments_post()
    {
        header("Access-Control-Allow-Origin: *");
    
        $this->load->library('Authorization_Token');

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE):

            $post_id        = $this->input->post("Post_id");
            $name 	        = $this->input->post("Name");
            $email 	        = $this->input->post("Email");
            $body 	        = $this->input->post("Body");

            $ck_post        = $this->api->get_one_row("ut_posts","Id",array("Id" => $post_id));
            
            if($name == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Nama tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST);      
            elseif(!$ck_post):
                $this->response([
                    'status'    => false,
                    'message'   => "Posts id tidak ditemukan"
                ],REST_Controller::HTTP_BAD_REQUEST);  
            elseif($email == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Email tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST); 
            elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)):  
                $this->response([
                    'status'    => false,
                    'message'   => "Gunakan email yang valid"
                ],REST_Controller::HTTP_BAD_REQUEST);        
            elseif($body == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Body tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST);         
            else:
                
                $data = array(
                    "Post_id"		=> $post_id, 
                    "User_id"       => $is_valid_token['data']->id,
                    "Name"		    => $name, 
                    "Email"		    => $email, 
                    "Body"		    => $body, 
                );

                $this->main->general_save("ut_comments", $data);
                $this->response([
                    'status'    => true,
                    'message'   => "data berhasil ditambahkan",
                    'data'      => $data
                ],REST_Controller::HTTP_OK);
            endif;
        else:
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_NOT_FOUND);
        endif;

    }

    public function updatedComments_put()
    {
        header("Access-Control-Allow-Origin: *");
    
        $this->load->library('Authorization_Token');

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE):

            $id 	        = $this->put("Id");
            $body 	        = $this->put("Body");
         
            if($id == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Id tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST);      
            elseif($body == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Body tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST);      
            else:
                
                $data = array(
                    "Body"          => $body,
                );

                $this->main->general_update("ut_comments", $data, array("Id" => $id));

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

    public function deleteComments_delete($id)
    {
        header("Access-Control-Allow-Origin: *");
    
        $this->load->library('Authorization_Token');

        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE)
        {
            $id = $this->security->xss_clean($id);
            
            if (empty($id) AND !is_numeric($id))
            {
                $this->response(['status' => FALSE, 'message' => 'data id tidak ditemukan' ], REST_Controller::HTTP_NOT_FOUND);
            }
            else
            {
                $data = [
                    'Id' => $id,
                    'User_id' => $is_valid_token['data']->id,
                ];

                $output   = $this->comments->delete_comments($data);
                if ($output > 0 AND !empty($output))
                {
                    $message = [
                        'status'    => true,
                        'message'   => "data berhasil dihapus"
                    ];
                    $this->response($message, REST_Controller::HTTP_OK);
                } else
                {
                    $message = [
                        'status'    => FALSE,
                        'message'   => "data gagal dihapus"
                    ];
                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }
            }

        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
}
