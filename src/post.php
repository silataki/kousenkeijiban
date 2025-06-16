<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "testtest";

// Get the user id from URL, default to 1 if not provided
$id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message for feedback
$message = "";

// Handle POST request (when user submits a new post)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $post_content = trim($_POST['post_content'] ?? '');
    if ($post_content !== "") {
        $stmt = $conn->prepare("INSERT INTO posts (user_id, post_date, post_time, post_content) VALUES (?, CURDATE(), CURTIME(), ?)");
        $stmt->bind_param("is", $id, $post_content);
        if ($stmt->execute()) {
            $message = "Post submitted successfully!";
        } else {
            $message = "Error inserting post: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Post content cannot be empty.";
    }
}

// Fetch current user info for top box
$stmt = $conn->prepare("SELECT name, mail, profile_pic FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = htmlspecialchars($row['name']);
    $mail = htmlspecialchars($row['mail']);
    $profilePic = htmlspecialchars($row['profile_pic']);
} else {
    die("User not found.");
}
$stmt->close();

// Fetch all posts joined with users info, ordered by post_id ascending
$sqlPosts = "
    SELECT p.post_id, p.post_date, p.post_time, p.post_content, 
           u.name, u.mail, u.profile_pic
    FROM posts p
    JOIN users u ON p.user_id = u.id
    ORDER BY p.post_id ASC
";
$resultPosts = $conn->query($sqlPosts);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>User Profile & Posts</title>
<style>
  body {
    margin: 0;
    font-family: Arial, sans-serif;
  }
  /* Fixed top profile box */
  .profile-box {
    position: fixed;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    align-items: center;
    border: 2px solid rgb(247, 151, 194);
    padding: 15px;
    width: 700px;
    background-color:rgb(248, 242, 244);
    border-radius: 0 0 8px 8px;
    z-index: 1000;
  }
  .profile-pic {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 6px;
    margin-right: 15px;
    border: 3px solid rgb(247, 151, 194); 
  }
  .profile-info {
    flex: 1;
  }
  .profile-info .name {
    font-size: 1.2em;
    font-weight: bold;
    margin-bottom: 5px;
  }
  .profile-info .mail {
    color: #555;
  }
  form {
    display: flex;
    margin-top: 10px;
    width: 100%;
  }
  input[type="text"] {
    flex: 1;
    padding: 8px;
    font-size: 1em;
    border: 2px solid rgb(247, 151, 194);
    border-radius: 6px 0 0 6px;
    outline: none;
  }
  button {
    padding: 8px 15px;
    font-size: 1em;
    border: 1px solid rgb(247, 151, 194);
    background-color:rgb(247, 151, 194);
    color: white;
    border-radius: 0 6px 6px 0;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  button:hover {
    background-color:rgb(244, 104, 148);
  }
  .message {
    width: 100%;
    text-align: center;
    margin-top: 10px;
    color: green;
  }
  .error {
    color: red;
  }

  /* Container for posts, below fixed profile box */
  .posts-container {
    margin: 130px auto 30px auto; /* space for fixed header */
    width: 700px;
    max-height: 500px;
    overflow-y: auto;
    border: 2px solid rgb(247, 151, 194);
    padding: 10px;
    border-radius: 8px;
    background-color: rgb(247, 151, 194);
  }

  /* Scrollbar styling for WebKit browsers (Chrome, Edge, Safari) */
  .posts-container::-webkit-scrollbar {
    width: 8px;              /* thinner width */
  }

  .posts-container::-webkit-scrollbar-track {
    background: #f1f1f1;     /* track background */
    border-radius: 10px;
  }

  .posts-container::-webkit-scrollbar-thumb {
    background-color:rgb(243, 129, 191); /* pink color */
    border-radius: 10px;
    border: 2px solid #f1f1f1; /* some padding around thumb */
  }

  /* For Firefox (limited styling) */
  .posts-container {
    scrollbar-width: thin;
    scrollbar-color: #ff69b4 #fafafa;
  }

  /* Each post rectangle */
  .post-box {
    display: flex;
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 10px;
    background-color: #fafafa;
  }
  .post-profile-pic {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 6px;
    margin-right: 15px;
  }
  .post-content {
    flex: 1;
  }
  .post-header {
    font-weight: bold;
    margin-bottom: 3px;
  }
  .post-email {
    color: #555;
    font-size: 0.9em;
    margin-bottom: 5px;
  }
  .post-date-time {
    font-size: 0.8em;
    color: #888;
    margin-bottom: 8px;
  }
  .post-text {
    white-space: pre-wrap;
  }


  .date-box {
    position: fixed;
    top: 0;
    right: 50px; /* adjust as needed */
    width: 200px;
    height: 160px; /* roughly top third height */
    border: 2px solid rgb(247, 151, 194);
    border-radius: 10px;
    background-color: #fff0f6; /* a light pinkish background */
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    font-family: 'Arial Black', Arial, sans-serif;
    box-shadow: 0 2px 6px rgba(255, 105, 180, 0.4);
    z-index: 1001;
  }

  .date-box .day {
    font-size: 2.5em;
    font-weight: bold;
    color: #ff1493; /* deep pink */
    margin-bottom: 10px;
  }

  .date-box .date-jp {
    font-size: 1.8em;
    color: #d81b60; /* a slightly darker pink */
  }

  .video-box {
    position: fixed;
    top: 170px;  /* below the date box (which is 160px tall) */
    right: 50px; /* same horizontal alignment as date box */
    width: 200px;
    height: calc(100vh - 190px); /* fill bottom two thirds minus some margin */
    border: 2px solid rgb(247, 151, 194);
    border-radius: 10px;
    background-color: #fff;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(255, 105, 180, 0.4);
    z-index: 1001;
  }

  .video-box video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
  }

  .home-button {
    position: fixed;
    top: 5px;
    left: 20px;
    padding: 8px 15px;
    width: 195px;
    background-color: #ff69b4;
    color: white;
    font-weight: bold;
    text-decoration: none;
    border-radius: 6px;
    box-shadow: 0 2px 5px rgba(255, 105, 180, 0.7);
    z-index: 1100;
    transition: background-color 0.3s ease;
  }

  .home-button:hover {
    background-color: #ff1493;
  }


</style>
</head>
<body>

<a href="home.php" class="home-button">HOMEPAGE</a>


<div class="profile-box">
  <img src="<?php echo $profilePic; ?>" alt="Profile Picture" class="profile-pic" />
  <div class="profile-info">
    <div class="name"><?php echo $name; ?></div>
    <div class="mail"><?php echo $mail; ?></div>
    
    <form method="POST" action="post.php?id=<?php echo $id; ?>">
      <input type="text" name="post_content" placeholder="Write your post here..." autocomplete="off" />
      <button type="submit">POST</button>
    </form>

    <?php if ($message !== ""): ?>
      <div class="message <?php echo strpos($message, 'Error') === 0 ? 'error' : ''; ?>">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php
// Get current day name in uppercase English
$currentDay = strtoupper(date('l')); // e.g. MONDAY

// Get current date in Japanese format: MM月DD日
$currentDateJP = date('m') . '月' . date('d') . '日';
?>
<div class="date-box">
  <div class="day"><?php echo $currentDay; ?></div>
  <div class="date-jp"><?php echo $currentDateJP; ?></div>
</div>

<div class="video-box">
  <video src="cat.mp4" autoplay loop muted playsinline></video>
</div>

<div class="posts-container">
  <?php
  if ($resultPosts && $resultPosts->num_rows > 0) {
      while ($post = $resultPosts->fetch_assoc()) {
          echo '<div class="post-box">';
          echo '<img src="' . htmlspecialchars($post['profile_pic']) . '" alt="Profile Pic" class="post-profile-pic" />';
          echo '<div class="post-content">';
          echo '<div class="post-header">' . htmlspecialchars($post['name']) . '</div>';
          echo '<div class="post-email">' . htmlspecialchars($post['mail']) . '</div>';
          echo '<div class="post-date-time">' . htmlspecialchars($post['post_date']) . ' ' . htmlspecialchars($post['post_time']) . '</div>';
          echo '<div class="post-text">' . nl2br(htmlspecialchars($post['post_content'])) . '</div>';
          echo '</div>';
          echo '</div>';
      }
  } else {
      echo '<p>No posts yet.</p>';
  }
  ?>
</div>

</body>
</html>
