<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Manufacturer_Model extends CI_Model
{
    protected $manufacturer_table = 'manufacturers';

    public function create_manufacturer(array $data) {
        $this->db->insert($this->manufacturer_table, $data);
        return $this->db->insert_id();
    }

    public function update_manufacturer(array $data)
    {
      
        $query = $this->db->get_where($this->manufacturer_table, [
            'id' => $data['id']
        ]);
        if ($this->db->affected_rows() > 0) {
            
            $update_data = [
                'manufacturer_name' =>  $data['manufacturer_name'],
                'updated_at' => $data['updated_at'],
            ];
            return $this->db->update($this->manufacturer_table, $update_data, ['id' => $query->row('id')]);
        }   
        return false;
    }


    public function delete_manufacturer(array $data)
    {

        $query = $this->db->get_where($this->manufacturer_table, $data);
        if ($this->db->affected_rows() > 0) {

            $this->db->delete($this->manufacturer_table, $data);
            if ($this->db->affected_rows() > 0) {
                return true;
            }
            return false;
        }   
        return false;
    }
}