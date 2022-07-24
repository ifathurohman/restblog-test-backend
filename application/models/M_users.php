<?php

class M_users extends CI_Model
{
    public function getUsers($p1="")
    {
        $this->db->select("
            id,
            name,
            username,
            status,
            useradd,
            dateadd
        "); 
        $this->db->from("ut_users");
        if($p1 != null){
            $this->db->where("username", $p1);
        }

        $query = $this->db->get();

        return $query->result_array();
    }
}
