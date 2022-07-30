<?php
defined('BASEPATH') or exit('No direct script access allowed');

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

            $id                  = $this->get('id'); 
             
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

            $category_id    = $this->input->post("category_id");
            $title 	        = $this->input->post("title");
            $slug 	        = $this->input->post("title");
            $body 	        = $this->input->post("body");
            $image 	        = $this->input->post("post_image");

            $image 			= $this->save_image();

            $ck_title       = $this->api->get_one_row("ut_posts","title",array("title" => $title));
            $ck_slug        = $this->api->get_one_row("ut_posts","slug",array("slug" => $slug));
            $ck_category    = $this->api->get_one_row("ut_categories","id",array("id" => $category_id));
            
            if($category_id == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "Category tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST);      
            elseif(!$ck_category):
                $this->response([
                    'status'    => false,
                    'message'   => "Id category tidak ditemukan"
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
                    'message'   => "slug tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST);      
            elseif($ck_slug):
                $this->response([
                    'status'    => false,
                    'message'   => "slug sudah digunakan"
                ],REST_Controller::HTTP_BAD_REQUEST);
            elseif($body == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "body tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST);      
            elseif($image == ''):
                $this->response([
                    'status'    => false,
                    'message'   => "image tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST);      
            else:
                
                $data = array(
                    "category_id"   => $category_id,
                    "user_id"       => $is_valid_token['data']->id,
                    "title"		    => $title,
                    "slug"	        => $this->main->textToslug($slug),
                    "body"	        => $body, 
                );

                if($image):
                    $data['post_image']  = $image;
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

            $id             = $this->put("id");
            $category_id    = $this->put("category_id");
            $title 	        = $this->put("title");
            $slug 	        = $this->put("title");
            $body 	        = $this->put("body");

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
                    'message'   => "body tidak boleh kosong"
                ],REST_Controller::HTTP_BAD_REQUEST);      
            else:
                
                $data = array(
                    "category_id"   => $category_id,
                    "user_id"       => $is_valid_token['data']->id,
                    "title"		    => $title,
                    "slug"	        => $this->main->textToslug($slug),
                    "body"	        => $body, 
                );

                $this->main->general_update("ut_posts", $data, array("id" => $id));

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
                    'id' => $id,
                    'user_id' => $is_valid_token['data']->id,
                ];

                $this->main->remove_file("ut_posts","post_image",array("id" => $id,"user_id" => $is_valid_token['data']->id));
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

        $slug 	                    = $this->input->post("title");
        $nmfile                     = 'test-backend'."-".$this->main->textToslug($slug);
	    $config['upload_path']      = './img/article'; //path folder 
	    $config['allowed_types']    = 'gif|jpg|png|jpeg|bmp|PNG|JPG'; //type yang dapat diakses bisa anda sesuaikan 
	    $config['max_size']         = '99999'; //maksimum besar file 2M 
	    $config['max_width']        = '99999'; //lebar maksimum 1288 px 
	    $config['max_height']       = '99999'; //tinggi maksimu 768 px 
	    $config['file_name']        = $nmfile; //nama yang terupload nantinya 
	    $this->upload->initialize($config); 
	    $upload                     = $this->upload->do_upload('post_image');
	    $gbr                        = $this->upload->data();
        
        $this->load->library('Authorization_Token');
        $is_valid_token = $this->authorization_token->validateToken();

        $image = '';
        
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE):
            if($upload):
                $image 	= "img/article/".$gbr['file_name'];
                $data   = array(
                    "post_image" => $image
                );

                $ck_image    = $this->api->get_one_row("ut_posts","post_image",array("user_id" => $is_valid_token['data']->id, "title" => $slug));
                if($ck_image == $data):
                    $this->main->remove_file("ut_posts","post_image",array("user_id" => $is_valid_token['data']->id));
                endif;

                return $image;
            endif;
        endif;

	}

}
