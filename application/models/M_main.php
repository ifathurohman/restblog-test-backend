<?php

defined('BASEPATH') or exit('No direct script access allowed');
class M_main extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

     public function echoJson($data){
        header('Content-Type: application/json');
        echo json_encode($data,JSON_PRETTY_PRINT);
    }

    public function group_by($key, $data)
    {
        $result = array();

        foreach ($data as $val) {
            if (array_key_exists($key, $val)) {
                $result[$val[$key]][] = $val;
            } else {
                $result[""][] = $val;
            }
        }

        return $result;
    }

    public function groupBy($rows, ...$keys)
    {
        if ($key = array_shift($keys)) {
            $groups = array_reduce($rows, function ($groups, $row) use ($key) {
                $group = is_object($row) ? $row->{$key} : $row[$key]; // object is available too.
                $groups[$group][] = $row;
                return $groups;
            }, []);
            if ($keys) {
                foreach ($groups as $subKey => $subRows) {
                    $groups[$subKey] = self::groupBy($subRows, ...$keys);
                }
            }
        }
        return $groups;
    }

     public function create_password($password){
        $password = "asem".$password."119";
        $password = sha1($password);

        return $password;
    }

    public function general_save($table,$data){
        // $this->db->set("UserAdd",$data['Name']);
        $this->db->set("DateAdd",date("Y-m-d H:i:s"));
        $this->db->insert($table,$data);
        return $this->db->insert_id();
    }

     public function general_update($table,$data,$where){
        // $this->db->set("UserCh",$this->session->Name);
        // $this->db->set("DateCh",date("Y-m-d H:i:s"));
        $this->db->update($table, $data, $where);
        return $this->db->affected_rows();
    }

    public function textToSlug($text='') {
    $text = trim($text);
    if (empty($text)) return '';
        $text = preg_replace("/[^a-zA-Z0-9\-\s]+/", "", $text);
        $text = strtolower(trim($text));
        $text = str_replace(' ', '-', $text);
        $text = $text_ori = preg_replace('/\-{2,}/', '-', $text);
        return $text;
    }

    public function slugToText($slug='') {
    $slug = trim($slug);
    if (empty($slug)) return '';
        $slug = str_replace('-', ' ', $slug);
        $slug = ucwords($slug);
        return $slug;
    }

     public function remove_file($table,$column,$where){
        $this->db->select($column);
        $this->db->where($where);
        $this->db->from($table);
        $query = $this->db->get()->row();

        if($query):
            if(is_file($query->{$column})):
                unlink('./' . $query->{$column});
            endif;
        endif;
    }
}
