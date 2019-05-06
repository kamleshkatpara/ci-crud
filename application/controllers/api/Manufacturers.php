<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';
 
class Manufacturers extends \Restserver\Libraries\REST_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->model('manufacturer_model', 'ManufacturerModel');
        $this->load->helper(array('form', 'url'));

        // Load Authorization Token Library
        $this->load->library('Authorization_Token');

        header("Access-Control-Allow-Origin: *");
    }


    /**
     * Add new Manufacturer with API
     * -------------------------
     * @method: POST
     */

    public function createManufacturer_post() {
   
        /**
         * User Token Validation
        */

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE)
        {
            # XSS Filtering (https://www.codeigniter.com/user_guide/libraries/security.html)

            $_POST = $this->security->xss_clean($_POST);

            # Form Validation
            
            $this->form_validation->set_rules('manufacturer_name', 'Manufacturer Name', 'trim|required|max_length[100]');
            
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
                $insert_manufacturer = [
                    'manufacturer_name' => $this->input->post('manufacturer_name', TRUE),
                    'created_at' => date("Y-m-d H:i:s")
                ];
                
                // Insert Manufacturer
                $output = $this->ManufacturerModel->create_manufacturer($insert_manufacturer);

                if ($output > 0 AND !empty($output))
                {
                    // Success
                    $message = [
                        'status' => true,
                        'message' => "Manufacturer created !"
                    ];

                    $this->response($message, REST_Controller::HTTP_OK);
                } else
                {
                    // Error
                    $message = [
                        'status' => FALSE,
                        'message' => "Manufacturer cannot be created !"
                    ];

                    $this->response($message, REST_Controller::HTTP_NOT_ACCEPTABLE);
                }
            }
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }


    
    /**
     * Get All Manufacturers with API
     * @method: GET
    */

    public function getManufacturers_get() {
        /**
         * User Token Validation
        */
        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE)
        {
                // Get Manufacturers
                $query = $this->db->get('manufacturers');

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
                        'message' => "Cannot get manufacturers !"
                    ];
                    $this->response($message, REST_Controller::HTTP_NOT_ACCEPTABLE);
                }
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    
    /**
     * Get Manufacturer by ID with API
     * @method: GET
    */

    public function getManufacturer_get($id) {
        
        /**
         * User Token Validation
        */

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE)
        {
           $id = $this->security->xss_clean($id);

           if (empty($id) AND !is_numeric($id))
           {
                $this->response(['status' => FALSE, 'message' => 'Invalid Manufacturer ID' ], REST_Controller::HTTP_NOT_ACCEPTABLE);
           } else {
             // Get Manufacturer by ID
             $query = $this->db->get_where('manufacturers', array('id' => $id));
                                
             $output = $query->result_array();
  
             if ($output > 0 AND !empty($output)) {
              // Success
              $message = [
                    'status' => true,
                    'data' => $output
              ];
              
              $this->response($message, REST_Controller::HTTP_OK);
            } else {
               // Error
               $message = [
                    'status' => FALSE,
                    'message' => "Cannot get manufacturer !"
               ];
             $this->response($message, REST_Controller::HTTP_NOT_ACCEPTABLE);
            }
          }
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    
    /**
     * Update the Manufacturer by ID with API
     * @method: POST
    */

    public function updateManufacturer_post($id) {

        /**
         * User Token Validation
        */

        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE)
        {
            $id = $this->security->xss_clean($id);

            if (empty($id) AND !is_numeric($id))
            {
                $this->response(['status' => FALSE, 'message' => 'Invalid Manufacturer ID' ], REST_Controller::HTTP_NOT_ACCEPTABLE);
            }
            else {

                $update_manufacturer = [
                    'id' => $id,
                    'manufacturer_name' => $this->input->post('manufacturer_name'),
                    'updated_at' => date("Y-m-d H:i:s")
                ];
                
                // Update the manufacturer
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
                    $this->response($message, REST_Controller::HTTP_NOT_ACCEPTABLE);
                }

            }
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Delete Manufacturer by ID with API
     * @method: DELETE
    */
    public function deleteManufacturer_delete($id)
    {
        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE)
        {
            # XSS Filtering (https://www.codeigniter.com/user_guide/libraries/security.html)

            $id = $this->security->xss_clean($id);
            
            if (empty($id) AND !is_numeric($id))
            {
                $this->response(['status' => FALSE, 'message' => 'Invalid Manufacturer ID' ], REST_Controller::HTTP_NOT_ACCEPTABLE);
            }
            else
            {
                $delete_manufacturer = [
                    'id' => $id
                ];
                $output = $this->ManufacturerModel->delete_manufacturer($delete_manufacturer);
                if ($output > 0 AND !empty($output))
                {
                    // Success
                    $message = [
                        'status' => true,
                        'message' => "Manufacturer Deleted"
                    ];
                    $this->response($message, REST_Controller::HTTP_OK);
                } else
                {
                    // Error
                    $message = [
                        'status' => FALSE,
                        'message' => "Manufacturer cannot be deleted !"
                    ];
                    $this->response($message, REST_Controller::HTTP_NOT_ACCEPTABLE);
                }
            }
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }
}