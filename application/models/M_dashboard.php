<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_dashboard extends CI_Model {

	var $table  	= 'ut_users';
	var $column 	= array("ut_users.ID",
                        "ut_users.Nama_personil",
                        "ut_users.Status_pegawai",
                        "ut_users.Nama_perusahaan"
                    ); 

	public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");
        $this->load->library('Authorization_Token');
    }

    private function _get_datatables_query()
	{
        
        $table          = $this->table;
		$searchx 	    = $this->input->post('Searchx');
        $is_valid_token = $this->authorization_token->validateToken();
        $Id             = $is_valid_token['data']->id;

		$this->db->select("
            (select count(Id) from ut_comments where User_id = $Id) as total_comments,
            (select count(Id) from ut_posts where User_id = $Id) as total_posts,
		");

		$this->db->from("ut_users");
        $this->db->where("ut_users.Id", $Id);
        $this->db->group_by("ut_users.Id");


		$i = 0;
	
		foreach ($this->column as $item) // loop column 
		{
			if($searchx) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND. 
					$this->db->like($item, $searchx);
				}
				else
				{
					$this->db->or_like($item, $searchx);
				}

				if(count($this->column) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$column[$i] = $item; // set column array variable to order processing
			$i++;
		}
		
		if($this->input->post('order')) // here order processing
		{
			$this->db->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}


	function get_datatables()
	{
		$this->_get_datatables_query();
		if($this->input->post('length') != -1)
		$this->db->limit($this->input->post('length'), $this->input->post('start'));
		$query = $this->db->get();
		return $query->result();
	}


	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{   
        $is_valid_token = $this->authorization_token->validateToken();
        $Id             = $is_valid_token['data']->id;
		$this->db->from("ut_users");
        $this->db->join("ut_categories as categories", "categories.User_id = ut_users.Id", "left");
        $this->db->join("ut_comments as comments", "comments.User_id = ut_users.Id", "left");
        $this->db->join("ut_posts as posts", "posts.User_id = ut_users.Id", "left");
        $this->db->where("ut_users.Id", $Id);
		return $this->db->count_all_results();
	}

}