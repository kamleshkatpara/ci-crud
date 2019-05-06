<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Vehicle_Model extends CI_Model
{
    protected $vehicle_table = 'models';

    // 
    public function create_vehicle(array $data) {
        $this->db->insert($this->vehicle_table, $data);
        return $this->db->insert_id();
    }

    public function update_vehicle(array $data)
    {
      
        $query = $this->db->get_where($this->vehicle_table, [
            'id' => $data['id']
        ]);
        if ($this->db->affected_rows() > 0) {
            
            $update_data = [
                'model_name' =>  $data['model_name'],
                'manufacturer_id' =>  $data['manufacturer_id'],
                'color' =>  $data['color'],
                'manufacturing_year' =>  $data['manufacturing_year'],
                'registration_number' =>  $data['registration_number'],
                'note' =>  $data['note'],
                'firstimage' =>  $data['firstimage'],
                'secondimage' =>  $data['secondimage'],
                'model_count' =>  $data['model_count'],
                'updated_at' => $data['updated_at'],
            ];
            return $this->db->update($this->vehicle_table, $update_data, ['id' => $query->row('id')]);
        }   
        return false;
    }


    public function delete_vehicle(array $data)
    {

        $query = $this->db->get_where($this->vehicle_table, $data);
        if ($this->db->affected_rows() > 0) {

            $this->db->delete($this->vehicle_table, $data);
            if ($this->db->affected_rows() > 0) {
                return true;
            }
            return false;
        }   
        return false;
    }
}