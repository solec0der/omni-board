<?php

class AuthKeyManager {

    private $conn;
    
    public function __construct() {
	    $dbConn = new DBConnection();
        $this->conn = $dbConn->conn;
    }

    public function generateAuthKey($serverId) {
        try {
            $authKey = ""; 

            do {
                $authKey = bin2hex(openssl_random_pseudo_bytes(32));

                $sql = "SELECT * FROM auth_keys WHERE auth_key = :authKey";
                $stmt  = $this->conn->prepare($sql);
                $stmt->bindParam(':authKey', $authKey, PDO::PARAM_STR);
                $stmt->execute();

                $authKeyResults = $stmt->fetchAll(); 
            } while (!empty($authKeyResults));

            echo $authKey;

            $sql = "INSERT INTO auth_keys(auth_key, server_id) VALUES(:authKey, :serverId)";
            $stmt  = $this->conn->prepare($sql);
            $stmt->bindParam(':authKey', $authKey, PDO::PARAM_STR);
            $stmt->bindParam(':serverId', $serverId, PDO::PARAM_INT);
            $stmt->execute();

            return $authKey;    
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        } 
    }

    public function verifyAuthKey() {

    }

    public function deleteAuthKey($authKeyId): bool {
        try {
            $sql = "DELETE FROM auth_keys WHERE auth_key_id = :authKeyId";
            $stmt  = $this->conn->prepare($sql);
            $stmt->bindParam(':authKeyId', $authKeyId, PDO::PARAM_INT);
            $stmt->execute();

            return true;    
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }
}

?>
