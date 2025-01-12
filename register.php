<?php
session_start();
include('db_connection.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL query to insert the new user
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    
    // Execute the query
    if ($stmt->execute([$username, $hashedPassword])) {
        echo "<p style='color: green;'>Registration successful!</p>";
        header('Location: login.php'); // Redirect to login after successful registration
    } else {
        echo "<p style='color: red;'>Error: Could not register the user.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f4f7;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 0;
        }

        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 500px;
        }

        h1 {
            font-size: 36px;
            text-align: center;
            color: #0288d1;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        label {
            font-size: 18px;
            color: #333;
        }

        input[type="text"], input[type="password"] {
            padding: 10px;
            font-size: 18px;
            border: 2px solid #0288d1;
            border-radius: 8px;
            width: 100%;
            box-sizing: border-box;
        }

        button {
            background-color: #0288d1;
            color: white;
            padding: 15px;
            font-size: 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0277bd;
        }

        .message {
            text-align: center;
            font-size: 18px;
        }

        footer {
            text-align: center;
            font-size: 14px;
            margin-top: 30px;
            color: #777;
        }

        footer a {
            color: #0288d1;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Create Account</h1>

    <form action="register.php" method="POST">
        <label for="username">Username</label>
        <input type="text" name="username" required><br>

        <label for="password">Password</label>
        <input type="password" name="password" required><br>

        <button type="submit">Register</button>
    </form>

    <div class="message">
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>

</div>

</body>
</html>