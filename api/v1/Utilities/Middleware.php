<?php
    class Middleware {

        private $userDAO;

        public function __construct() {
            $this->userDAO = new UserDAO();
        }

        public function getRequest() {

        }

        public function checkAuth() {
            $request = array();    
            $token = $this->getBearerToken();

            if ($this->userDAO->verifyAuthenticationToken()) {
                $request['inputs'] = (array) json_decode(file_get_contents("php://input")); 
                return $request;
            }

            Response::json(403, array(
                "error" => "Insufficient privilegies"
            ));
        }
        
        private function getAuthorizationHeader(){
            $headers = null;
       
            if (isset($_SERVER['Authorization'])) {
                $headers = trim($_SERVER["Authorization"]);
            } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
            } elseif (function_exists('apache_request_headers')) {
                $requestHeaders = apache_request_headers();

                $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
                
                if (isset($requestHeaders['Authorization'])) {
                    $headers = trim($requestHeaders['Authorization']);
                }
            }
            return $headers;
        }

        private function getBearerToken() {
            $headers = $this->getAuthorizationHeader();
            
            if (!empty($headers)) {
                if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                    return $matches[1];
                }
            }
            
            return null;
        }
    }
?>
