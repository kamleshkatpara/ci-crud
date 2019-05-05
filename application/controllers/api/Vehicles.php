<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
require APPPATH . '/libraries/REST_Controller.php';
 
class Vehicles extends \Restserver\Libraries\REST_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->model('vehicle_model', 'VehicleModel');
        $this->load->helper(array('form', 'url'));
    }

    public function createVehicle_post() {
        header("Access-Control-Allow-Origin: *");
    
        $this->load->library('Authorization_Token');
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE)
        {
            $_POST = $this->security->xss_clean($_POST);

            $this->form_validation->set_rules('model_name', 'Model Name', 'trim|required|max_length[100]');
            if ($this->form_validation->run() == FALSE)
            {
                $message = array(
                    'status' => false,
                    'error' => $this->form_validation->error_array(),
                    'message' => validation_errors()
                );
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
            else
            {

                $insert_data = [
                    'model_name' => $this->input->post('model_name', TRUE),
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ];

                $output = $this->ManufacturerModel->create_manufacturer($insert_data);
                if ($output > 0 AND !empty($output))
                {
                    $message = [
                        'status' => true,
                        'message' => "Manufacturer Add"
                    ];
                    $this->response($message, REST_Controller::HTTP_OK);
                } else
                {
                    $message = [
                        'status' => FALSE,
                        'message' => "Not able to create Manufacturer"
                    ];
                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }
            }
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function getManufacturers_get() {
        header("Access-Control-Allow-Origin: *");
    
        $this->load->library('Authorization_Token');
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE)
        {


                $query = $this->db->get('manufacturers');

                $output = $query->result_array();
                if ($output > 0 AND !empty($output))
                {
                    $message = [
                        'status' => true,
                        'data' => $output
                    ];
                    $this->response($message, REST_Controller::HTTP_OK);
                } else
                {
                    $message = [
                        'status' => FALSE,
                        'message' => "Not able to create Manufacturer"
                    ];
                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function getManufacturer_get($id) {
        header("Access-Control-Allow-Origin: *");
    
        $this->load->library('Authorization_Token');
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE)
        {


                $query = $this->db->get_where('manufacturers', array('id' => $id));
                
                $output = $query->result_array();
                if ($output > 0 AND !empty($output))
                {
                    $message = [
                        'status' => true,
                        'data' => $output
                    ];
                    $this->response($message, REST_Controller::HTTP_OK);
                } else
                {
                    $message = [
                        'status' => FALSE,
                        'message' => "Not able to create Manufacturer"
                    ];
                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function updateManufacturer_post($id) {
        header("Access-Control-Allow-Origin: *");
    
        $this->load->library('Authorization_Token');


        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE)
        {
            $id = $this->security->xss_clean($id);
            if (empty($id) AND !is_numeric($id))
            {
                $this->response(['status' => FALSE, 'message' => 'Invalid Manufacturer ID' ], REST_Controller::HTTP_NOT_FOUND);
            }
            else
            {

                $update_manufacturer = [
                    'id' => $id,
                    'model_name' => $this->input->post('model_name'),
                    'updated_at' => date("Y-m-d H:i:s")
                ];
                
                
                $output = $this->ManufacturerModel->update_manufacturer($update_manufacturer);

                if ($output > 0 AND !empty($output))
                {
                    // Success
                    $message = [
                        'status' => true,
                        'message' => "Manufacturer Updated"
                    ];
                    $this->response($message, REST_Controller::HTTP_OK);
                } else
                {
                    // Error
                    $message = [
                        'status' => FALSE,
                        'message' => "Manufacturer not update"
                    ];
                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }

            }
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function deleteManufacturer_delete($id)
    {
        header("Access-Control-Allow-Origin: *");
    
        $this->load->library('Authorization_Token');

        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE)
        {
            $id = $this->security->xss_clean($id);
            
            if (empty($id) AND !is_numeric($id))
            {
                $this->response(['status' => FALSE, 'message' => 'Invalid Article ID' ], REST_Controller::HTTP_NOT_FOUND);
            }
            else
            {
                $delete_manufacturer = [
                    'id' => $id
                ];
                $output = $this->ManufacturerModel->delete_manufacturer($delete_manufacturer);
                if ($output > 0 AND !empty($output))
                {
                    $message = [
                        'status' => true,
                        'message' => "Manufacturer Deleted"
                    ];
                    $this->response($message, REST_Controller::HTTP_OK);
                } else
                {
                    $message = [
                        'status' => FALSE,
                        'message' => "Manufacturer not delete"
                    ];
                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }
            }
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
}