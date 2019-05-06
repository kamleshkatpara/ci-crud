<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
| https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
| $route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
| $route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
| $route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples: my-controller/index -> my_controller/index
|   my-controller/my-method -> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = TRUE;

// User API Routes
$route['api/user/register'] = 'api/users/register';
$route['api/user/login'] = 'api/users/login';
$route['api/article/upload'] = 'api/articles/upload';

// Users Article Routes
$route['api/article/create'] = 'api/articles/createArticle';

$route['api/manufacturer/create'] = 'api/manufacturers/createManufacturer';
$route['api/manufacturers'] = 'api/manufacturers/getManufacturers';
$route['api/manufacturer/(:num)'] = 'api/manufacturers/getManufacturer/$1';
$route['api/manufacturer/update/(:num)']["post"] = 'api/manufacturers/updateManufacturer/$1';
$route['api/manufacturer/delete/(:num)']["delete"] = 'api/manufacturers/deleteManufacturer/$1';


$route['api/vehicle/create'] = 'api/vehicles/createVehicle';
$route['api/vehicles'] = 'api/vehicles/getVehicles';
$route['api/vehicle/(:num)'] = 'api/vehicles/getVehicle/$1';
$route['api/vehicle/update/(:num)']["post"] = 'api/vehicles/updateVehicle/$1';
$route['api/vehicle/delete/(:num)']["delete"] = 'api/vehicles/deleteVehicle/$1';
$route['api/vehicle/sell/(:num)']["get"] = 'api/vehicles/sellVehicle/$1';


// Deleta an Article Routes
# https://codeigniter.com/user_guide/general/routing.html#using-http-verbs-in-routes
$route['api/article/(:num)/delete']["DELETE"] = 'api/articles/deleteArticle/$1';
// Update and Article Route :: PUT API Request
$route['api/article/update']["put"] = 'api/articles/updateArticle';
$route['api/article/test/(:num)']["post"] = 'api/articles/test/$1';

/*
| -------------------------------------------------------------------------
| Sample REST API Routes
| -------------------------------------------------------------------------
*/
$route['api/example/users/(:num)'] = 'api/example/users/id/$1'; // Example 4
$route['api/example/users/(:num)(\.)([a-zA-Z0-9_-]+)(.*)'] = 'api/example/users/id/$1/format/$3$4'; // Example 8
