<?php

class M_category extends CI_Model
{
    public function getCategory($id = null)
    {
        if($id === null):
            return $this->db->get("ut_categories")->result_array();
        else:
            return $this->db->get_where('ut_categories',['id' => $id])->result_array();
        endif;
    }

    public function delete_category(array $data)
    {
        $query = $this->db->get_where("ut_categories", $data);
        if ($this->db->affected_rows() > 0) {
            $this->db->delete("ut_categories", $data);
            if ($this->db->affected_rows() > 0) {
                return true;
            }
            return false;
        }   
        return false;
    }
}
