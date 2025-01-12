<?php
session_start();
include('db_connection.php');

// Check if the post ID is set
if (!isset($_GET['id'])) {
    header("Location: index.php"); // Redirect if no post ID is provided
    exit;
}

$id = $_GET['id'];

// Fetch the post to update
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id");
$stmt->execute(['id' => $id]);
$post = $stmt->fetch();

if (!$post) {
    header("Location: index.php"); // Redirect if the post doesn't exist
    exit;
}

// Handle form submission for updating a post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Update the post in the database
    $stmt = $pdo->prepare("UPDATE posts SET title = :title, content = :content WHERE id = :id");
    $stmt->execute(['title' => $title, 'content' => $content, 'id' => $id]);

    header("Location: index.php"); // Redirect after update
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Post</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
            text-align: center;
        }

        .container {
            width: 100%;
            max-width: 800px;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 36px;
            color: #0288d1;
            margin-bottom: 20px;
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-group input, .input-group textarea {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #0288d1;
        }

        .textarea {
            height: 200px;
        }

        .submit-button {
            padding: 12px;
            font-size: 18px;
            color: white;
            background-color: #0288d1;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-button:hover {
            background-color: #0277bd;
        }

        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }

        .footer a {
            color: #0288d1;
            text-decoration: none;
        }

        .footer a:hover {
            color: #0277bd;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Update Post</h1>

        <form method="POST">
            <div class="input-group">
                <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
            </div>
            <div class="input-group">
                <textarea class="textarea" name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
            </div>
            <button type="submit" class="submit-button">Update Post</button>
        </form>

        <div class="footer">
            <p><a href="index.php">Back to Home</a></p>
        </div>
    </div>

</body>
</html>