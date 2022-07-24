<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class M_api extends CI_Model {
	public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");
    }

    public function get_one_row($table,$column,$where){
        $this->db->select($column);
        $this->db->where($where);
        $query = $this->db->get($table);

        return $query->row();
    }

    public function get_result($table,$column){
        $this->db->select($column);
        $query = $this->db->get($table);

        return $query->result_array();
    }

}