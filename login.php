<?php
// login.php
session_start();
include('config.php');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $sonuc=$stmt->fetchAll(PDO::FETCH_ASSOC);
    
    
echo 1;
    // $stmt->store_result();

    if(count($sonuc) > 0) {
        // $stmt->bind_result($id, $db_password);
        
        
$db_password=$sonuc[0]['password'];
$id=$sonuc[0]['id'];

        if(password_verify($password,$db_password)) {
            $_SESSION['user_id'] = $id;
            echo "Giriş başarılı!";
        } else {
            echo "Yanlış şifre.";
        }
    } else {
        echo "Böyle bir kullanıcı bulunamadı.";
    }

    $stmt->close();
    $conn->close();
}
?>