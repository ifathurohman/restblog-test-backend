<?php

class M_comments extends CI_Model
{
    public function getComments($id = null)
    {
        if($id === null):
            return $this->db->get("ut_comments")->result_array();
        else:
            return $this->db->get_where('ut_comments',['id' => $id])->result_array();
        endif;
    }

    public function delete_comments(array $data)
    {
        $query = $this->db->get_where("ut_comments", $data);
        if ($this->db->affected_rows() > 0) {
            $this->db->delete("ut_comments", $data);
            if ($this->db->affected_rows() > 0) {
                return true;
            }
            return false;
        }   
        return false;
    }
}
