<?php

class M_article extends CI_Model
{
    public function getArticle($id = null)
    {
        if($id === null):
            return $this->db->get("ut_posts")->result_array();
        else:
            return $this->db->get_where('ut_posts',['id' => $id])->result_array();
        endif;
    }

    public function delete_article(array $data)
    {
        $query = $this->db->get_where("ut_posts", $data);
        if ($this->db->affected_rows() > 0) {
            $this->db->delete("ut_posts", $data);
            if ($this->db->affected_rows() > 0) {
                return true;
            }
            return false;
        }   
        return false;
    }
}
