<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';

class Article extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");
        $this->load->model('M_article', 'article');
    }

    public function getArticle_get()
    {
        header("Access-Control-Allow-Origin: *");
    
        $this->load->library('Authorization_Token');

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE):

            $id                  = $this->get('Id'); 
             
            if($id === null):
                 $article = $this->article->getArticle();
            else:
                 $article = $this->article->getArticle($id);
            endif;

            if($article):
                $this->response([
                    'status'    => true,
                    'data'      => $article
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

    public function createdArticle_post()
    {
        header("Access-Control-Allow-Origin: *");
    
        $this->load->library('Authorization_Token');

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE):

            $category_id    = $this->input->post("Category_id");
            $title 	        = $this->input->post("Title");
            $slug 	        = $this->input->post("Title");
            $body 	        = $this->input->post("Body");
            $Image 	        = $this->input->post("Post_image");

            $Image 			= $this->save_image();

            $ck_title       = $this->api->get_one_row("ut_posts","Title",array("Title" => $title));
            $ck_slug        = $this->api->get_one_row("ut_posts","Slug",array("Slug" => $slug));
            $ck_category    = $this->api->get_one_row("ut_categories","Name",array("Name" => $category_id));
            
            if($category_id == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Category tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST);      
            elseif($ck_category):
                $this->response([
                    'status'    => false,
                    'message'   => "Nama category sudah digunakan"
                ],REST_Controller::HTTP_BAD_REQUEST);      
            elseif($title == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Judul tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST);      
            elseif($ck_title):
                $this->response([
                    'status'    => false,
                    'message'   => "Judul sudah digunakan"
                ],REST_Controller::HTTP_BAD_REQUEST);
            elseif($slug == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Slug tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST);      
            elseif($ck_slug):
                $this->response([
                    'status'    => false,
                    'message'   => "Slug sudah digunakan"
                ],REST_Controller::HTTP_BAD_REQUEST);
            elseif($body == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Body tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST);      
            elseif($Image == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Image tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST);      
            else:
                
                $data = array(
                    "Category_id"   => $category_id,
                    "User_id"       => $is_valid_token['data']->id,
                    "Title"		    => $title,
                    "Slug"	        => $this->main->textToSlug($slug),
                    "Body"	        => $body, 
                );

                if($Image):
                    $data['Post_image']  = $Image;
                endif;
                $this->main->general_save("ut_posts", $data);
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

    public function updatedArticle_put()
    {
        header("Access-Control-Allow-Origin: *");
    
        $this->load->library('Authorization_Token');

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE):

            $id             = $this->put("Id");
            $category_id    = $this->put("Category_id");
            $title 	        = $this->put("Title");
            $slug 	        = $this->put("Title");
            $body 	        = $this->put("Body");

            if($id == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Id tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST);      
            elseif($category_id == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Category tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST);      
            elseif($title == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Judul tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST);      
            elseif($slug == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Slug tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST);      
            elseif($body == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Body tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST);      
            else:
                
                $data = array(
                    "Category_id"   => $category_id,
                    "User_id"       => $is_valid_token['data']->id,
                    "Title"		    => $title,
                    "Slug"	        => $this->main->textToSlug($slug),
                    "Body"	        => $body, 
                );

                $this->main->general_update("ut_posts", $data, array("Id" => $id));

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

    public function deleteArticle_delete($id)
    {
        header("Access-Control-Allow-Origin: *");
    
        $this->load->library('Authorization_Token');

        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE)
        {
            $id = $this->security->xss_clean($id);
            
            if (empty($id) AND !is_numeric($id))
            {
                $this->response(['status' => FALSE, 'message' => 'Invalid Article ID' ], REST_Controller::HTTP_NOT_FOUND);
            }
            else
            {
                $delete_article = [
                    'Id' => $id,
                    'User_id' => $is_valid_token['data']->id,
                ];

                $this->main->remove_file("ut_posts","Post_image",array("Id" => $id,"User_id" => $is_valid_token['data']->id));
                $output   = $this->article->delete_article($delete_article);
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

    private function save_image(){

        $slug 	                    = $this->input->post("Title");
        $nmfile                     = 'test-backend'."-".$this->main->textToSlug($slug);
	    $config['upload_path']      = './img/article'; //path folder 
	    $config['allowed_types']    = 'gif|jpg|png|jpeg|bmp|PNG|JPG'; //type yang dapat diakses bisa anda sesuaikan 
	    $config['max_size']         = '99999'; //maksimum besar file 2M 
	    $config['max_width']        = '99999'; //lebar maksimum 1288 px 
	    $config['max_height']       = '99999'; //tinggi maksimu 768 px 
	    $config['file_name']        = $nmfile; //nama yang terupload nantinya 
	    $this->upload->initialize($config); 
	    $upload                     = $this->upload->do_upload('Post_image');
	    $gbr                        = $this->upload->data();
        
        $this->load->library('Authorization_Token');
        $is_valid_token = $this->authorization_token->validateToken();

        $image = '';
        
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE):
            if($upload):
                $image 	= "img/article/".$gbr['file_name'];
                $data   = array(
                    "Post_image" => $image
                );

                $ck_image    = $this->api->get_one_row("ut_posts","Post_image",array("User_id" => $is_valid_token['data']->id, "Title" => $slug));
                if($ck_image == $data):
                    $this->main->remove_file("ut_posts","Post_image",array("User_id" => $is_valid_token['data']->id));
                endif;

                return $image;
            endif;
        endif;

	}

}
