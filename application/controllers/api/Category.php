<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Category extends RestController
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");
        $this->load->model('M_category', 'category');
    }

    public function getCategory_get()
    {
        header("Access-Control-Allow-Origin: *");
    
        $this->load->library('Authorization_Token');

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE):

            $id                  = $this->get('id'); 
             
            if($id === null):
                 $data = $this->category->getCategory();
            else:
                 $data = $this->category->getCategory($id);
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

    public function createdCategory_post()
    {
        header("Access-Control-Allow-Origin: *");
    
        $this->load->library('Authorization_Token');

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE):

            $name 	        = $this->input->post("name");

            $ck_name        = $this->api->get_one_row("ut_categories","name",array("name" => $name));
            
            if($name == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Nama tidak boleh kosong"
                ],RestController::HTTP_BAD_REQUEST);      
            elseif($ck_name):
                $this->response([
                    'status'    => false,
                    'message'   => "Nama sudah digunakan"
                ],RestController::HTTP_BAD_REQUEST);     
            else:
                
                $data = array(
                    "user_id"       => $is_valid_token['data']->id,
                    "name"		    => $name, 
                );

                $this->main->general_save("ut_categories", $data);
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

    public function updatedCategory_put()
    {
        header("Access-Control-Allow-Origin: *");
    
        $this->load->library('Authorization_Token');

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE):

            $id         = $this->put("id");
            $name       = $this->put("name");

            if($id == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "id tidak boleh kosong"
                ],RestController::HTTP_BAD_REQUEST);      
            elseif($name == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Nama tidak boleh kosong"
                ],RestController::HTTP_BAD_REQUEST);          
            else:
                
                $data = array(
                    "name"          => $name,
                    "user_id"       => $is_valid_token['data']->id,
                );

                $this->main->general_update("ut_categories", $data, array("id" => $id));

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

    public function deleteCategory_delete($id)
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

                $output   = $this->category->delete_category($data);
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
