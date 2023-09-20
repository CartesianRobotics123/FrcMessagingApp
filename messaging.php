<?php
try {
    // Database connection using PDO
    $host = "localhost";
$username = "username";
$password = "password";
$dbname = "efe_message_db";

    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// If this is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    if ($_POST['action'] === 'send') {
        $username = $_POST['username'];
        $message = $_POST['message'];
        
        $stmt = $conn->prepare("INSERT INTO messages (username, message) VALUES (:username, :message)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':message', $message);
        $stmt->execute();

        echo json_encode(["status" => "success"]);
    } elseif ($_POST['action'] === 'fetch') {
        $stmt = $conn->prepare("SELECT * FROM messages ORDER BY created_at DESC LIMIT 10");
        $stmt->execute();
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Update the seen status for fetched messages
        $stmt = $conn->prepare("UPDATE messages SET seen = 1 WHERE id = :id");
        foreach ($messages as $message) {
            $stmt->bindParam(':id', $message['id'], PDO::PARAM_INT);
            $stmt->execute();
        }
        
        echo json_encode($messages);
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Messaging Program</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .container {
            width: 100%;
            max-width: 600px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px;
            box-sizing: border-box;
        }
        h1, h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], textarea {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        input[type="submit"] {
            align-self: flex-end;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        li {
            background-color: #fff;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        li:last-child {
            border-bottom: none;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

<div class="container">
    <h1>Cartesian Group Messaging Program</h1>

    <!-- Message submission form -->
    <form id="messageForm">
        <label for="username">Username:</label>
        <input type="text" id="username" required>
        <label for="message">Message:</label>
        <textarea id="message" required></textarea>
        <input type="submit" value="Send">
    </form>
</div>

<div class="container">
    <!-- Display messages -->
    <h2>Messages:</h2>
    <ul id="messageList">
    </ul>
</div>


<script>
// Fetch messages initially
fetchMessages();

// Fetch messages every 5 seconds
setInterval(fetchMessages, 5000);

// Submit message using AJAX
$('#messageForm').on('submit', function(e) {
    e.preventDefault();
    
    const username = $('#username').val();
    const message = $('#message').val();
    
    $.post('messaging.php', {action: 'send', username: username, message: message}, function(data) {
        const result = JSON.parse(data);
        
        if (result.status === 'success') {
            $('#message').val('');
            fetchMessages();
        }
    });
});

// Fetch messages using AJAX
function fetchMessages() {
    $.post('messaging.php', {action: 'fetch'}, function(data) {
        const messages = JSON.parse(data);
        
        let html = '';
        for (const message of messages) {
            html += `<li><strong>${message.username}</strong>: ${message.message}`;
            if (message.seen === '1') {
                html += ' &#10004;';
            }
            html += '</li>';
        }
        
        $('#messageList').html(html);
    });
}
</script>

</body>
</html>

