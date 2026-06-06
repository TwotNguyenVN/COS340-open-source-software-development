<?php
class AccountModel
{
    private $conn;
    private $table_name = "account";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAccountByUsernameOrEmail($input)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :input OR email = :input";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':input', $input, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    function save($username, $email, $fullname, $password, $role="user"){
        $query = "INSERT INTO " . $this->table_name . "(username, email, fullname, password, role) VALUES (:username, :email, :fullname, :password, :role)";

        $stmt = $this->conn->prepare($query);

        // Làm sạch dữ liệu
        $fullname = htmlspecialchars(strip_tags($fullname));
        $username = htmlspecialchars(strip_tags($username));
        $email = htmlspecialchars(strip_tags($email));

        // Gán dữ liệu vào câu lệnh
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);

        // Thực thi câu lệnh
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function createPasswordReset($email, $otp) {
        $del = $this->conn->prepare("DELETE FROM password_resets WHERE email = :email");
        $del->execute([':email' => $email]);

        $query = "INSERT INTO password_resets(email, otp_code, expires_at) VALUES (:email, :otp, DATE_ADD(NOW(), INTERVAL 15 MINUTE))";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':email' => $email, ':otp' => $otp]);
    }

    public function verifyOtp($email, $otp) {
        $query = "SELECT * FROM password_resets WHERE email = :email AND otp_code = :otp AND expires_at > NOW()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':email' => $email, ':otp' => $otp]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function updatePassword($email, $hashed_password) {
        $query = "UPDATE account SET password = :pwd WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $result = $stmt->execute([':pwd' => $hashed_password, ':email' => $email]);
        if ($result) {
            $del = $this->conn->prepare("DELETE FROM password_resets WHERE email = :email");
            $del->execute([':email' => $email]);
        }
        return $result;
    }
}
?>
