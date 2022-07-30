<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Comments extends RestController
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

            $id                  = $this->get('id'); 
             
            if($id === null):
                 $data = $this->comments->getComments();
            else:
                 $data = $this->comments->getComments($id);
            endif;

            if($data):
                $this->response([
                    'status'    => true,
                    'data'      => $data
                ],RestController::HTTP_OK);
            else:
                 $this->response([
                    'status'    => true,
                    'message'   => "data tidak ditemukan"
                ],RestController::HTTP_NOT_FOUND);
            endif;         

        else:
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], RestController::HTTP_NOT_FOUND);
        endif;
    }

    public function createdComments_post()
    {
        header("Access-Control-Allow-Origin: *");
    
        $this->load->library('Authorization_Token');

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE):

            $post_id        = $this->input->post("post_id");
            $name 	        = $this->input->post("name");
            $email 	        = $this->input->post("email");
            $body 	        = $this->input->post("body");

            $ck_post        = $this->api->get_one_row("ut_posts","id",array("id" => $post_id));
            
            if($name == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Nama tidak boleh kosong"
                ],RestController::HTTP_BAD_REQUEST);      
            elseif(!$ck_post):
                $this->response([
                    'status'    => false,
                    'message'   => "Posts id tidak ditemukan"
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
            elseif($body == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "body tidak boleh kosong"
                ],RestController::HTTP_BAD_REQUEST);         
            else:
                
                $data = array(
                    "post_id"		=> $post_id, 
                    "user_id"       => $is_valid_token['data']->id,
                    "name"		    => $name, 
                    "email"		    => $email, 
                    "body"		    => $body, 
                );

                $this->main->general_save("ut_comments", $data);
                $this->response([
                    'status'    => true,
                    'message'   => "data berhasil ditambahkan",
                    'data'      => $data
                ],RestController::HTTP_OK);
            endif;
        else:
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], RestController::HTTP_NOT_FOUND);
        endif;

    }

    public function updatedComments_put()
    {
        header("Access-Control-Allow-Origin: *");
    
        $this->load->library('Authorization_Token');

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE):

            $id 	        = $this->put("id");
            $body 	        = $this->put("body");
         
            if($id == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "id tidak boleh kosong"
                ],RestController::HTTP_BAD_REQUEST);      
            elseif($body == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "body tidak boleh kosong"
                ],RestController::HTTP_BAD_REQUEST);      
            else:
                
                $data = array(
                    "body"          => $body,
                );

                $this->main->general_update("ut_comments", $data, array("id" => $id));

                $this->response([
                    'status'    => true,
                    'message'   => "data berhasil diubah",
                    'data'      => $data
                ],RestController::HTTP_OK);
            endif;

        else:
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], RestController::HTTP_NOT_FOUND);
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
                $this->response(['status' => FALSE, 'message' => 'data id tidak ditemukan' ], RestController::HTTP_NOT_FOUND);
            }
            else
            {
                $data = [
                    'id' => $id,
                    'user_id' => $is_valid_token['data']->id,
                ];

                $output   = $this->comments->delete_comments($data);
                if ($output > 0 AND !empty($output))
                {
                    $message = [
                        'status'    => true,
                        'message'   => "data berhasil dihapus"
                    ];
                    $this->response($message, RestController::HTTP_OK);
                } else
                {
                    $message = [
                        'status'    => FALSE,
                        'message'   => "data gagal dihapus"
                    ];
                    $this->response($message, RestController::HTTP_NOT_FOUND);
                }
            }

        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], RestController::HTTP_NOT_FOUND);
        }
    }
}
