<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
require APPPATH . '/libraries/REST_Controller.php';
 
class Articles extends \Restserver\Libraries\REST_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
    }


    /**
     * Add new Article with API
     * -------------------------
     * @method: POST
     */
    public function createArticle_post()
    {
        header("Access-Control-Allow-Origin: *");
    
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        /**
         * User Token Validation
         */
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE)
        {
            # Create a User Article
            # XSS Filtering (https://www.codeigniter.com/user_guide/libraries/security.html)
            $_POST = $this->security->xss_clean($_POST);
            $config['upload_path']          = './uploads/';
            $config['allowed_types']        = 'gif|jpg|png';
            $config['overwrite'] = TRUE;

            $this->load->library('upload', $config);
            
            # Form Validation
            $this->form_validation->set_rules('title', 'Title', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('description', 'Description', 'trim|required|max_length[200]');
            if ($this->form_validation->run() == FALSE)
            {
                // Form Validation Errors
                $message = array(
                    'status' => false,
                    'error' => $this->form_validation->error_array(),
                    'message' => validation_errors()
                );
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
            else
            {
                // Load Article Model
                $this->load->model('article_model', 'ArticleModel');

                if( ! $this->post('submit')) {
                    $this->response(NULL, 400);
                }
                if ( ! $this->upload->do_upload('userfile'))
                {
                    $error = array('error' => $this->upload->display_errors());

                    $this->response(array('error' => strip_tags($this->upload->display_errors())), 404);
                }
                else
                {
                    $file = array('upload_data' => $this->upload->data());

                }
                $insert_data = [
                    'user_id' => $is_valid_token['data']->id,
                    'title' => $this->input->post('title', TRUE),
                    'description' => $this->input->post('description', TRUE),
                    'userfile' => $file['upload_data']['file_name'],
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ];

                // Insert Article
                $output = $this->ArticleModel->create_article($insert_data);
                if ($output > 0 AND !empty($output))
                {
                    // Success
                    $message = [
                        'status' => true,
                        'message' => "Article Add"
                    ];
                    $this->response($message, REST_Controller::HTTP_OK);
                } else
                {
                    // Error
                    $message = [
                        'status' => FALSE,
                        'message' => "Article not create"
                    ];
                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }
            }
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    /**
     * Delete an Article with API
     * @method: DELETE
     */
    public function deleteArticle_delete($id)
    {
        header("Access-Control-Allow-Origin: *");
    
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        /**
         * User Token Validation
         */
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE)
        {
            # Delete a User Article
            # XSS Filtering (https://www.codeigniter.com/user_guide/libraries/security.html)
            $id = $this->security->xss_clean($id);
            
            if (empty($id) AND !is_numeric($id))
            {
                $this->response(['status' => FALSE, 'message' => 'Invalid Article ID' ], REST_Controller::HTTP_NOT_FOUND);
            }
            else
            {
                // Load Article Model
                $this->load->model('article_model', 'ArticleModel');
                $delete_article = [
                    'id' => $id,
                    'user_id' => $is_valid_token['data']->id,
                ];
                // Delete an Article
                $output = $this->ArticleModel->delete_article($delete_article);
                if ($output > 0 AND !empty($output))
                {
                    // Success
                    $message = [
                        'status' => true,
                        'message' => "Article Deleted"
                    ];
                    $this->response($message, REST_Controller::HTTP_OK);
                } else
                {
                    // Error
                    $message = [
                        'status' => FALSE,
                        'message' => "Article not delete"
                    ];
                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }
            }
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    /**
     * Update an Article with API
     * @method: PUT
     */
    public function updateArticle_put()
    {
        header("Access-Control-Allow-Origin: *");
    
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        /**
         * User Token Validation
         */
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE)
        {
            # Update a User Article
            # XSS Filtering (https://www.codeigniter.com/user_guide/libraries/security.html)
            $_POST = json_decode($this->security->xss_clean(file_get_contents("php://input")), true);
            
            $this->form_validation->set_data([
                'id' => $this->input->post('id', TRUE),
                'title' => $this->input->post('title', TRUE),
                'description' => $this->input->post('description', TRUE),
            ]);
            
            # Form Validation
            $this->form_validation->set_rules('id', 'Article ID', 'trim|required|numeric');
            $this->form_validation->set_rules('title', 'Title', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('description', 'Description', 'trim|required|max_length[200]');
            if ($this->form_validation->run() == FALSE)
            {
                // Form Validation Errors
                $message = array(
                    'status' => false,
                    'error' => $this->form_validation->error_array(),
                    'message' => validation_errors()
                );
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
            else
            {
                // Load Article Model
                $this->load->model('article_model', 'ArticleModel');
                $update_data = [
                    'user_id' => $is_valid_token['data']->id,
                    'id' => $this->input->post('id', TRUE),
                    'title' => $this->input->post('title', TRUE),
                    'description' => $this->input->post('description', TRUE),
                ];
                // Update an Article
                $output = $this->ArticleModel->update_article($update_data);
                if ($output > 0 AND !empty($output))
                {
                    // Success
                    $message = [
                        'status' => true,
                        'message' => "Article Updated"
                    ];
                    $this->response($message, REST_Controller::HTTP_OK);
                } else
                {
                    // Error
                    $message = [
                        'status' => FALSE,
                        'message' => "Article not update"
                    ];
                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }
            }
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_NOT_FOUND);
        }
    }


    public function test_post($id)
    {
        header("Access-Control-Allow-Origin: *");
    
        $this->load->library('Authorization_Token');


        $is_valid_token = $this->authorization_token->validateToken();

        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE)
        {
            $id = $this->security->xss_clean($id);
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
                $this->response(['status' => FALSE, 'message' => 'Invalid Article ID' ], REST_Controller::HTTP_NOT_FOUND);
            }
            else
            {
                $this->load->model('article_model', 'ArticleModel');


                if( ! $this->post('submit')) {
                    $this->response(NULL, 400);
                }
                if ( ! $firstImage && $secondImage)
                {
                    $update_article = [
                        'id' => $id,
                        'user_id' => $is_valid_token['data']->id,
                        'title' => $this->input->post('title'),
                        'description' => $this->input->post('description'),
                        'updated_at' => date("Y-m-d H:i:s")
                    ];
                }
                else
                {
                    $firstimage = array('firstimage' => $this->firstimageupload->data());

                    $secondimage = array('secondimage' => $this->secondimageupload->data());
                                        
                    $update_article = [
                        'id' => $id,
                        'user_id' => $is_valid_token['data']->id,
                        'title' => $this->input->post('title'),
                        'description' => $this->input->post('description'),
                        'firstimage' => $firstimage['firstimage']['file_name'],
                        'secondimage' => $secondimage['secondimage']['file_name'],
                        'updated_at' => date("Y-m-d H:i:s")
                    ];
    
                }

                print_r(json_encode($update_article));
                
                $this->db->where('id', $id);
                $q = $this->db->get('articles');
                $old_data = $q->result_array();
                $old_firstimage_path = './uploads/'.$old_data[0]['firstimage'];
                $old_secondimage_path = './uploads/'.$old_data[0]['secondimage'];
                
                unlink($old_firstimage_path);
                unlink($old_secondimage_path);

                // Update an Article
                $output = $this->ArticleModel->update_article($update_article);

                if ($output > 0 AND !empty($output))
                {
                    // Success
                    $message = [
                        'status' => true,
                        'message' => "Article Updated"
                    ];
                    $this->response($message, REST_Controller::HTTP_OK);
                } else
                {
                    // Error
                    $message = [
                        'status' => FALSE,
                        'message' => "Article not update"
                    ];
                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }

            }
        } else {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message'] ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

}