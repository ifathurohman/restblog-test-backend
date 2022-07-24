<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';

class Dashboard extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_dashboard', 'dashboard');
    }

    public function getDashboard_post()
    {
        header("Access-Control-Allow-Origin: *");

		$this->load->library('Authorization_Token');

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE):

            $list 	    = $this->dashboard->get_datatables();

            foreach ($list as $a):
                $row = array();
                $row['total_posts'] 	= $a->total_posts;
                $row['total_comments'] 	= $a->total_comments;
                $data[] = $row;
		    endforeach;

            $this->response([
                "status"            => true,
                "draw" 				=> $this->input->post('draw'),
                "recordsTotal" 		=> $this->dashboard->count_all(),
                "recordsFiltered" 	=> $this->dashboard->count_filtered(),
                "data" 				=> $data,
            ],REST_Controller::HTTP_OK);

        else:
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_NOT_FOUND);
        endif;

    }

}
