<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';
 
class Vehicles extends \Restserver\Libraries\REST_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->model('vehicle_model', 'VehicleModel');
        $this->load->helper(array('form', 'url'));

        // Load Authorization Token Library
        $this->load->library('Authorization_Token');

        header("Access-Control-Allow-Origin: *");
    }

    /**
     * Add new Vehicle with API
     * -------------------------
     * @method: POST
    */

    public function createVehicle_post() {
        /**
         * User Token Validation
        */
        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE)
        {
            # XSS Filtering (https://www.codeigniter.com/user_guide/libraries/security.html)

            $_POST = $this->security->xss_clean($_POST);

            # File Uploading Configuration

            $config['upload_path']          = './uploads/';
            $config['allowed_types']        = 'gif|jpg|png';
            $config['overwrite'] = TRUE;

            $this->load->library('upload', $config, 'firstimageupload');
            $this->load->library('upload', $config, 'secondimageupload');

            $this->firstimageupload->initialize($config);
            $this->secondimageupload->initialize($config);

            $firstImage = $this->firstimageupload->do_upload('firstimage');
            $secondImage = $this->secondimageupload->do_upload('secondimage');

            # Form Validation

            $this->form_validation->set_rules('model_name', 'Model Name', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('manufacturer_id', 'Manufacturer ID', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('color', 'Color', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('manufacturing_year', 'Manufacturing Year', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('registration_number', 'Registration Number', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('note', 'Note', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('model_count', 'Model Count', 'trim|required|max_length[100]');

            if ($this->form_validation->run() == FALSE)
            {
                // Form Validation Errors

                $message = array(
                    'status' => false,
                    'error' => $this->form_validation->error_array(),
                    'message' => validation_errors()
                );
                $this->response($message, REST_Controller::HTTP_NOT_ACCEPTABLE);
            }
            else
            {
                if( ! $this->post('submit')) {
                    $this->response(NULL, 400);
                }
                if ( ! $firstImage && $secondImage)
                {
                    $insert_vehicle = [
                        'model_name' => $this->input->post('model_name', TRUE),
                        'manufacturer_id' => $this->input->post('manufacturer_id', TRUE),
                        'color' => $this->input->post('color', TRUE),
                        'manufacturing_year' => $this->input->post('manufacturing_year', TRUE),
                        'registration_number' => $this->input->post('registration_number', TRUE),
                        'note' => $this->input->post('note', TRUE),
                        'model_count' => $this->input->post('model_count', TRUE),
                        'created_at' => date("Y-m-d H:i:s")
                    ];

                } else {

                    $firstimage = array('firstimage' => $this->firstimageupload->data());

                    $secondimage = array('secondimage' => $this->secondimageupload->data());

                    $insert_vehicle = [
                        'model_name' => $this->input->post('model_name', TRUE),
                        'manufacturer_id' => $this->input->post('manufacturer_id', TRUE),
                        'color' => $this->input->post('color', TRUE),
                        'manufacturing_year' => $this->input->post('manufacturing_year', TRUE),
                        'registration_number' => $this->input->post('registration_number', TRUE),
                        'note' => $this->input->post('note', TRUE),
                        'firstimage' => $firstimage['firstimage']['file_name'],
                        'secondimage' => $secondimage['secondimage']['file_name'],
                        'model_count' => $this->input->post('model_count', TRUE),
                        'created_at' => date("Y-m-d H:i:s")
                    ];
                }

                // Insert Vehicle
                $output = $this->VehicleModel->create_vehicle($insert_vehicle);

                if ($output > 0 AND !empty($output))
                {
                    // Success
                    $message = [
                        'status' => true,
                        'message' => "Vehicle Added !"
                    ];
                    $this->response($message, REST_Controller::HTTP_OK);
                } else
                {
                    // Error
                    $message = [
                        'status' => FALSE,
                        'message' => "Cannot create Vehicle !"
                    ];
                    $this->response($message, REST_Controller::HTTP_NOT_ACCEPTABLE);
                }
            }
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Get All Vehicles with API
     * @method: GET
    */

    public function getVehicles_get() {
        
        /**
         * User Token Validation
        */
        
        $is_valid_token = $this->authorization_token->validateToken();
        
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE)
        {
                // Get the Vehicles
                $query = $this->db->get('models');

                $output = $query->result_array();

                if ($output > 0 AND !empty($output))
                {
                    // Success
                    $message = [
                        'status' => true,
                        'data' => $output
                    ];
                    $this->response($message, REST_Controller::HTTP_OK);
                } else
                {
                    // Error
                    $message = [
                        'status' => FALSE,
                        'message' => "Not able to get Vehicles"
                    ];
                    $this->response($message, REST_Controller::HTTP_NOT_ACCEPTABLE);
                }
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Get Vehicle by ID with API
     * @method: GET
    */

    public function getVehicle_get($id) {
        
        /**
         * User Token Validation
        */

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE)
        {
            # XSS Filtering (https://www.codeigniter.com/user_guide/libraries/security.html)

            $id = $this->security->xss_clean($id);

            if (empty($id) AND !is_numeric($id))
            {
                $this->response(['status' => FALSE, 'message' => 'Invalid Vehicle ID' ], REST_Controller::HTTP_NOT_ACCEPTABLE);
            } else {
                // Get Vehicles
                $query = $this->db->get_where('models', array('id' => $id));
                
                $output = $query->result_array();

                if ($output > 0 AND !empty($output))
                {
                    // Success
                    $message = [
                        'status' => true,
                        'data' => $output
                    ];
                    $this->response($message, REST_Controller::HTTP_OK);
                } else
                {
                    // Error
                    $message = [
                        'status' => FALSE,
                        'message' => "Not able to get Vehicle"
                    ];
                    $this->response($message, REST_Controller::HTTP_NOT_ACCEPTABLE);
                }
            }
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }


    /**
     * Update the Vehicle by ID with API
     * @method: POST
    */

    public function updateVehicle_post($id) {

        /**
         * User Token Validation
        */

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE)
        {
            # XSS Filtering (https://www.codeigniter.com/user_guide/libraries/security.html)

            $id = $this->security->xss_clean($id);

            // File Uploading Configuration

            $config['upload_path']          = './uploads/';
            $config['allowed_types']        = 'gif|jpg|png';
            $config['overwrite'] = TRUE;

            $this->load->library('upload', $config, 'firstimageupload');
            $this->load->library('upload', $config, 'secondimageupload');

            $this->firstimageupload->initialize($config);
            $this->secondimageupload->initialize($config);

            $firstImage = $this->firstimageupload->do_upload('firstimage');
            $secondImage = $this->secondimageupload->do_upload('secondimage');

            if (empty($id) AND !is_numeric($id))
            {
                $this->response(['status' => FALSE, 'message' => 'Invalid Vehicle ID' ], REST_Controller::HTTP_NOT_ACCEPTABLE);
            }
            else
            {
                if( ! $this->post('submit')) {
                    $this->response(NULL, 400);
                }

                // if no images are uploaded

                if ( ! $firstImage && $secondImage)
                {
                    $update_vehicle = [
                        'id' => $id,
                        'model_name' => $this->input->post('model_name', TRUE),
                        'manufacturer_id' => $this->input->post('manufacturer_id', TRUE),
                        'color' => $this->input->post('color', TRUE),
                        'manufacturing_year' => $this->input->post('manufacturing_year', TRUE),
                        'registration_number' => $this->input->post('registration_number', TRUE),
                        'note' => $this->input->post('note', TRUE),
                        'model_count' => $this->input->post('model_count', TRUE),
                        'updated_at' => date("Y-m-d H:i:s")
                    ];
                } else {
                    // if images are uploaded
                    $firstimage = array('firstimage' => $this->firstimageupload->data());

                    $secondimage = array('secondimage' => $this->secondimageupload->data());

                    $update_vehicle = [
                        'id' => $id,
                        'model_name' => $this->input->post('model_name', TRUE),
                        'manufacturer_id' => $this->input->post('manufacturer_id', TRUE),
                        'color' => $this->input->post('color', TRUE),
                        'manufacturing_year' => $this->input->post('manufacturing_year', TRUE),
                        'registration_number' => $this->input->post('registration_number', TRUE),
                        'note' => $this->input->post('note', TRUE),
                        'firstimage' => $firstimage['firstimage']['file_name'],
                        'secondimage' => $secondimage['secondimage']['file_name'],
                        'model_count' => $this->input->post('model_count', TRUE),
                        'updated_at' => date("Y-m-d H:i:s")
                    ];
                }
                
                // Delete the old files

                $this->db->where('id', $id);
                $q = $this->db->get('models');
                $old_data = $q->result_array();
                $old_firstimage_path = './uploads/'.$old_data[0]['firstimage'];
                $old_secondimage_path = './uploads/'.$old_data[0]['secondimage'];
                
                unlink($old_firstimage_path);
                unlink($old_secondimage_path);

                // Update the vehicle

                $output = $this->VehicleModel->update_vehicle($update_vehicle);

                if ($output > 0 AND !empty($output))
                {
                    // Success
                    $message = [
                        'status' => true,
                        'message' => "Vehicle Updated"
                    ];
                    $this->response($message, REST_Controller::HTTP_OK);
                } else
                {
                    // Error
                    $message = [
                        'status' => FALSE,
                        'message' => "Vehicle not update"
                    ];
                    $this->response($message, REST_Controller::HTTP_NOT_ACCEPTABLE);
                }

            }
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }


    /**
     * Delete Vehicle by ID with API
     * @method: DELETE
    */

    public function deleteVehicle_delete($id)
    {

        /**
         * User Token Validation
        */

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE)
        {
            # XSS Filtering (https://www.codeigniter.com/user_guide/libraries/security.html)

            $id = $this->security->xss_clean($id);
            
            if (empty($id) AND !is_numeric($id)) {
                $this->response(['status' => FALSE, 'message' => 'Invalid Vehicle ID' ], REST_Controller::HTTP_NOT_ACCEPTABLE);
            }
            else
            {
                $delete_vehicle = ['id' => $id];

                // Delete the old vehicle file if exists

                $this->db->where('id', $id);
                $q = $this->db->get('models');
                $old_data = $q->result_array();

                $old_firstimage_path = './uploads/'.$old_data[0]['firstimage'];
                $old_secondimage_path = './uploads/'.$old_data[0]['secondimage'];
                
                unlink($old_firstimage_path);
                unlink($old_secondimage_path);

                // Delete the vehicle
                $output = $this->VehicleModel->delete_vehicle($delete_vehicle);

                if ($output > 0 AND !empty($output))
                {
                    // Success
                    $message = [
                        'status' => true,
                        'message' => "Vehicle Deleted"
                    ];
                    $this->response($message, REST_Controller::HTTP_OK);
                } else
                {
                    // Error
                    $message = [
                        'status' => FALSE,
                        'message' => "Vehicle cannot be deleted !"
                    ];
                    $this->response($message, REST_Controller::HTTP_NOT_ACCEPTABLE);
                }
            }
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Update Vehicle Count
     * @method: GET
     */
    public function sellVehicle_get($id) {
        
        /**
         * User Token Validation
        */

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE)
        {
            # XSS Filtering (https://www.codeigniter.com/user_guide/libraries/security.html)

            $id = $this->security->xss_clean($id);
            
            if (empty($id) AND !is_numeric($id)) {
                $this->response(['status' => FALSE, 'message' => 'Invalid Vehicle ID' ], REST_Controller::HTTP_NOT_ACCEPTABLE);
            }
            else
            {
                $this->db->where('id', $id);
                $q = $this->db->get('models');
                $check = $q->result_array();

                if($check[0]['model_count'] !== 0) {
                    $query = $this->db->query("UPDATE models SET model_count = GREATEST(0, model_count - 1) WHERE id = ${id}");
                }
                print_r($query);    
            }
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }
}