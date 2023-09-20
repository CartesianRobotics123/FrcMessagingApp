<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // $username = mysqli_real_escape_string($conn, $_POST['username']);
    // $email = mysqli_real_escape_string($conn, $_POST['email']);
    // $password = mysqli_real_escape_string($conn, $_POST['password']);
    // $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    echo $username.$email.$password;   

    if ($password != $confirm_password) {
        echo "Şifreler eşleşmiyor.";
        exit;
    }
 
    // Şifreyi hash'leyerek güvenli bir şekilde saklayın
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Prepared statement kullanarak SQL sorgusunu hazırlayın ve çalıştırın
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username,:email,:password)");

    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);
echo 1111;
    if ($stmt->execute()) {
        echo "Kayıt başarılı!";
    } else {
        echo "Hata: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Geçersiz istek.";
}
?>
