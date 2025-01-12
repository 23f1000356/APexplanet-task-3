<?php
session_start();
include('db_connection.php');

// Search functionality
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Pagination logic
$postsPerPage = 5; // Number of posts per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $postsPerPage;

// Modify the query to include the search term if provided
$query = "SELECT * FROM posts WHERE title LIKE :searchTerm OR content LIKE :searchTerm ORDER BY created_at DESC LIMIT :start, :postsPerPage";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':postsPerPage', $postsPerPage, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

// Get the total number of posts for pagination
$totalPostsStmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE title LIKE :searchTerm OR content LIKE :searchTerm");
$totalPostsStmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
$totalPostsStmt->execute();
$totalPosts = $totalPostsStmt->fetchColumn();
$totalPages = ceil($totalPosts / $postsPerPage);

// Check if there are no posts available
$noPosts = count($posts) == 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Posts</title>
    
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJr4c2I0K5a8lB1R7vVdVvwv+Wyqj/7aBQq0jjm0e+yTgfFwKvW17DNO8slk" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .container {
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 36px;
            color: #0288d1;
        }

        .post {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #0288d1;
            border-radius: 8px;
            background-color: #fff;
        }

        .post h2 {
            font-size: 24px;
            color: #0288d1;
        }

        .post p {
            font-size: 16px;
            color: #777;
        }

        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }

        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }

        .pagination li {
            margin: 0 5px;
        }

        .pagination a {
            text-decoration: none;
            color: #0288d1;
            padding: 8px 15px;
            border: 1px solid #0288d1;
            border-radius: 5px;
        }

        .pagination a:hover {
            background-color: #0288d1;
            color: white;
        }

        /* Styling for blue box buttons */
        .button-group a {
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: #0288d1;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .button-group a:hover {
            background-color: #0277bd;
        }

        .footer p a {
            padding: 10px 20px;
            color: white;
            background-color: #0288d1;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
            display: inline-block;
        }

        .footer p a:hover {
            background-color: #0277bd;
        }

        /* Styling for Edit and Delete buttons in separate small boxes */
        .edit-delete-buttons a {
            padding: 8px 15px;
            font-size: 14px;
            color: white;
            background-color: #0288d1;
            border-radius: 5px;
            text-decoration: none;
            margin-right: 10px;
            display: inline-block;
        }

        .edit-delete-buttons a:hover {
            background-color: #0277bd;
        }

        /* Add margin for Back to Home and Logout buttons */
        .footer p {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Blog Posts</h1>

        <!-- Create Post Button -->
        <div class="button-group mb-4">
            <a href="create_post.php">Create Post</a>
        </div>

   

<form method="GET" action="" class="mb-4 d-flex justify-content-center">
    <input type="text" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" class="form-control" placeholder="Search posts by title or content" style="height: 45px; width: 85%; font-size: 16px;">
    <button type="submit" class="btn btn-primary" style="height: 45px; font-size: 16px;">Search</button>
</form>



        <!-- Display posts -->
        <?php if ($noPosts): ?>
            <p>No posts available.</p>
        <?php else: ?>
            <!-- Display all posts -->
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                    <p><strong>Posted on:</strong> <?php echo htmlspecialchars($post['created_at']); ?></p>
                    <div class="edit-delete-buttons">
                        <a href="update_post.php?id=<?php echo $post['id']; ?>">Edit</a>
                        <a href="delete_post.php?id=<?php echo $post['id']; ?>" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Pagination -->
        <nav>
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a href="?page=1&search=<?php echo urlencode($searchTerm); ?>" class="page-link">First</a></li>
                    <li class="page-item"><a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($searchTerm); ?>" class="page-link">Prev</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item"><a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($searchTerm); ?>" class="page-link"><?php echo $i; ?></a></li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item"><a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($searchTerm); ?>" class="page-link">Next</a></li>
                    <li class="page-item"><a href="?page=<?php echo $totalPages; ?>&search=<?php echo urlencode($searchTerm); ?>" class="page-link">Last</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Additional Links -->
        <div class="footer">
            <p><a href="index.php">Back to Home</a></p>
            <p><a href="logout.php">Logout</a></p>
        </div>
    </div>
    
    <div class="footer text-center mt-5">
        <p>&copy; 2025 Blogs | All Rights Reserved</p>
    </div>

    <!-- Bootstrap JS and Popper.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gyb6e+J6v67DdB7wZybE0A9MSMZ63Go5r/2ZxK63aVX1DpF+I8b" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0YfP4AbyXvR7Gd2AP92SgSFX5zFf5l5mSvnf5wWwrmUkTkHj" crossorigin="anonymous"></script>

</body>
</html>
