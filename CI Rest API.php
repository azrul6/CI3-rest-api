<!-- 

    Install and configure the REST Server library for CodeIgniter by following the instructions provided in the library's documentation: https://github.com/chriskacerguis/codeigniter-restserver

    Create a new controller file, Api.php, in the application/controllers directory. This will be your API controller where you define your API endpoints and authentication logic.
 -->


<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Api extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Load necessary models, libraries, etc.
        $this->load->model('user_model'); // Example model
        $this->load->library('authorization_token');
    }

    public function login_post()
    {
        $username = $this->post('username');
        $password = $this->post('password');

        // Validate username and password
        if ($username && $password) {
            // Authenticate the user
            $user = $this->user_model->authenticate($username, $password); // Example method from the user_model

            if ($user) {
                // Generate and store the authorization token
                $tokenData = [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    // Add more relevant data to the token if needed
                ];

                $token = $this->authorization_token->generateToken($tokenData);

                $this->response(['token' => $token], REST_Controller::HTTP_OK);
            } else {
                $this->response(['message' => 'Invalid username or password'], REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response(['message' => 'Username and password are required'], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    // Protected endpoint that requires authentication
    public function users_get()
    {
        // Verify the authorization token
        if ($this->authorization_token->validateToken()) {
            $users = $this->user_model->get_users(); // Example method from the user_model

            if ($users) {
                $this->response($users, REST_Controller::HTTP_OK);
            } else {
                $this->response(['message' => 'No users found'], REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $this->response(['message' => 'Unauthorized'], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    // Add other API methods as per your project requirements
}
/*
Note that this example uses the authorization_token library to handle token-based authentication. Make sure you have the library installed and configured properly.

Create a new model file, User_model.php, in the application/models directory. This will be your example model for handling user-related data and authentication.
*/


<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    public function authenticate($username, $password)
    {
        // Perform the authentication logic and return the user object if authenticated
        $user = $this->db->get_where('users', ['username' => $username, 'password' => $password])->row();

        return $user;
    }

    public function get_users()
    {
        // Retrieve and return the users data from the database
        $users = $this->db->get('users')->result();

        return $users;
    }

    // Add other methods as per your project requirements
}
/*
Configure the routes for your API endpoints by adding the following lines to the application/config/routes.php file:
*/


    $route['api/login'] = 'api/login';
    $route['api/users'] = 'api/users';
/*
    You can add more routes for additional API methods.

    Now you can test your REST API with authentication by sending requests to the defined endpoints:
        To authenticate and obtain a token: POST http://your-domain/api/login with username and password parameters.
        To access the protected endpoint: GET http://your-domain/api/users with the obtained token set as the Authorization header.

    Make sure to replace your-domain with the actual domain or local server URL where your CodeIgniter application is running.

This is a basic example of setting up a simple REST API with authentication in CodeIgniter 3. You can expand upon this foundation by adding more API methods, implementing different authentication mechanisms (e.g., JWT), integrating with a database, handling request parameters, and customizing the responses based on your project requirements.
*/

