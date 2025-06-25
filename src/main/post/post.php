<?php
session_start();

// Set hardcoded user ID 1 in session for testing only if not set
/*
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; 
}
*/

/* tomoka add */
if (!isset($_SESSION['users'])) {
    echo "<p>Please <a href='login.php'>log in</a> to access your profile.</p>";
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo "セッションIDが存在しません。ログインし直してください。";
    exit;
}

//$users = $_SESSION['users'];
/* add finish */

// Logged-in user ID (who is using the app)
$loggedInUserId = $_SESSION['user_id'];

// Profile user ID (whose profile is being viewed), default to logged-in user if none provided
/* user not fuund */
/* tomoka change */
//$profileUserId = isset($_GET['id']) ? intval($_GET['id']) : $loggedInUserId;
/* ↓ */
$profileUserId = $_SESSION['users']['id'];


// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "testtest";



// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message for feedback
$message = "";


###pagination
$postsPerPage = 8; // adjust number of posts per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $postsPerPage;
#################3


######story upload###################
// Accept JSON input
// Check if the request Content-Type is JSON and contains image upload data
$contentType = $_SERVER["CONTENT_TYPE"] ?? '';

if (strpos($contentType, 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['image']) && isset($data['user_id'])) {
        $base64Image = $data['image'];
        $userId = intval($data['user_id']);

        $imageData = base64_decode($base64Image);
        if ($imageData === false) {
            http_response_code(400);
            echo "Invalid image data.";
            exit;
        }

        // Update the story for this user with the image blob
        $stmt = $conn->prepare("UPDATE users SET story = ?, story_created_at = CURRENT_TIMESTAMP WHERE id = ?");
        if (!$stmt) {
            http_response_code(500);
            echo "Prepare failed: " . $conn->error;
            exit;
        }

        // Bind the blob and user id
        //$stmt->bind_param("bi", $null, $userId);
        //$null = NULL;

        // Send long data in chunks (for blob)
        //$stmt->send_long_data(0, $imageData);

        $null = NULL;
        $stmt->bind_param("bi", $null, $userId);
        $stmt->send_long_data(0, $imageData);


        if ($stmt->execute()) {
            echo "Image saved successfully.";
        } else {
            http_response_code(500);
            echo "Error saving image: " . $stmt->error;
        }

        $stmt->close();
        exit; // Important: stop further output to avoid breaking the JSON API
    } else {
        http_response_code(400);
        echo "Missing image or user_id data.";
        exit;
    }
}

###################################




// Handle POST request (when user submits a new post)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $post_content = trim($_POST['post_content'] ?? '');
    if ($post_content !== "") {
        $stmt = $conn->prepare("INSERT INTO posts (user_id, post_date, post_time, post_content) VALUES (?, CURDATE(), CURTIME(), ?)");
        $stmt->bind_param("is", $loggedInUserId, $post_content);
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
/* tomoka change */

$stmt = $conn->prepare("SELECT name, mail, profile_pic FROM users WHERE id = ?");
$stmt->bind_param("i", $profileUserId);
$stmt->execute();
$result = $stmt->get_result();


/* start */
/*
$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
*/
/* end */

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
// Fetch all posts joined with users info and likes/dislikes, ordered by post_id ascending
// Assuming $id is current user ID
##$sqlPosts = "
##    SELECT p.post_id, p.post_date, p.post_time, p.post_content, 
##           p.likes, p.dislikes,
##           u.name, u.mail, u.profile_pic,
##           r.reaction AS user_reaction
##    FROM posts p
##    JOIN users u ON p.user_id = u.id
##    LEFT JOIN post_reactions r ON r.post_id = p.post_id AND r.user_id = ?
##    ORDER BY p.post_date DESC, p.post_time DESC
##";
##$stmt = $conn->prepare($sqlPosts);
##$stmt->bind_param("i", $loggedInUserId);
##$stmt->execute();
##$resultPosts = $stmt->get_result();

$sqlPosts = "
    SELECT p.post_id, p.post_date, p.post_time, p.post_content, 
           p.likes, p.dislikes,
           u.name, u.mail, u.profile_pic, u.story,
           r.reaction AS user_reaction
    FROM posts p
    JOIN users u ON p.user_id = u.id
    LEFT JOIN post_reactions r ON r.post_id = p.post_id AND r.user_id = ?
    ORDER BY p.post_date DESC, p.post_time DESC
    LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($sqlPosts);
$stmt->bind_param("iii", $loggedInUserId, $postsPerPage, $offset);
$stmt->execute();
$resultPosts = $stmt->get_result();

#####pagination
$countResult = $conn->query("SELECT COUNT(*) as total FROM posts");
$totalPosts = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalPosts / $postsPerPage);

#####


// 2. Get user with most liked post
$topResult = $conn->query("
    SELECT users.name, users.profile_pic
    FROM posts
    JOIN users ON posts.user_id = users.id
    ORDER BY posts.likes DESC
    LIMIT 1
");
$topUser = $topResult && $topResult->num_rows > 0 ? $topResult->fetch_assoc() : null;

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
    width: 60vw;
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
    width: 60vw;
    max-height:  90vh; /*500px*/
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
    width: 70px;
    height: 70px;
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
    right: 0; /* adjust as needed */
    width: 15vw;
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
    font-size: 1.7em;
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
    right: 0; /* same horizontal alignment as date box */
    width: 12vh;
    height: 400px; /* fill bottom two thirds minus some margin */
    border: 2px solid rgb(247, 151, 194);
    border-radius: 10px;
    background-color: #fff;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(255, 105, 180, 0.4);
    z-index: 1001;
  }

  .video-box video {
    width: 100%;
    /*height: 100%;*/
    object-fit: cover;
    border-radius: 8px;
  }

  .home-button {
    position: fixed;
    /*top: 5px;*/
    top: 5px;
    left: 20px;
    padding: 8px 15px;
    width: 12vw;
    background-color: #ff69b4;
    color: white;
    font-weight: bold;
    text-align: center;
    text-decoration: none;
    border-radius: 6px;
    box-shadow: 0 2px 5px rgba(255, 105, 180, 0.7);
    z-index: 1100;
    transition: background-color 0.3s ease;
  }

  .home-button:hover {
    background-color: #ff1493;
  }

  .search-box {
    position: fixed;
    top: 50px; /* below homepage button */
    left: 20px;
    width: 12vw;
    background-color: #fff0f6;
    border: 2px solid #ff69b4;
    border-radius: 8px;
    padding: 10px;
    box-shadow: 0 2px 5px rgba(255, 105, 180, 0.5);
    z-index: 1100;
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .search-header {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .search-icon {
    width: 24px;
    height: 24px;
  }

  .search-title {
    font-weight: bold;
    color: #ff1493;
    user-select: none;
    font-size: 1.1em;
  }

  #search-input {
    width: 90%;
    padding: 6px 8px;
    border: 1px solid #ff69b4;
    border-radius: 4px;
    font-size: 1em;
  }

  .search-buttons {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
  }

  #search-btn {
    flex: 1;
    background-color: #ff69b4;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 6px 0;
    cursor: pointer;
    font-weight: bold;
    user-select: none;
    transition: background-color 0.3s ease;
  }

  #search-btn:hover {
    background-color: #ff1493;
  }

  .cross-icon {
    width: 28px;
    height: 28px;
    cursor: pointer;
  }

  button.reacted {
    background-color: #ffb6c1; /* light pink */
    border-color: #ff69b4;
    font-weight: bold;
  }  

  .like-btn {
    background-color:rgb(255, 235, 243);  /* light green background */
    border: 1px solid rgb(247, 151, 194);  /* dark green border */
    color:rgb(46, 0, 27);             /* dark green text */
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: normal;
    transition: background-color 0.3s, color 0.3s;
  }

  .like-btn:hover {
    background-color:rgb(249, 203, 221); /* dark green */
    color: white;
  }

  .dislike-btn {
    background-color: rgb(255, 235, 243);  /* light red/pink background */
    border: 1px solid rgb(247, 151, 194);  /* dark red border */
    color: rgb(46, 0, 27);             /* dark red text */
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: normal;
    transition: background-color 0.3s, color 0.3s;
  }

  .dislike-btn:hover {
    background-color: rgb(249, 203, 221); /* dark red */
    color: white;
  }

  /* Keep reacted overrides as well, or customize: */
  .like-btn.reacted {
    background-color:rgb(252, 211, 226); /* dark green */
    color: white;
    font-weight: bold;
  }

  .dislike-btn.reacted {
    background-color: rgb(252, 211, 226); /* dark red */
    color: white;
    font-weight: bold;
  }

  .floating-heart {
    position: absolute;
    pointer-events: none;
    font-size: 34px;
    color: #ff69b4; /* pink hearts */
    animation: floatUp 3s ease forwards;
    user-select: none;
    z-index: 2000;
  }

  /* Animation: floating upward and fading out */
  @keyframes floatUp {
    0% {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
    100% {
      opacity: 0;
      transform: translateY(-100px) scale(1.5);
    }
  }

  #top-left-box { /*バズキング*/
      position: fixed;
      /*top: 190px;*/
      top: 200px;
      left: 20px;
      width: 12vw;
      padding: 10px;
      background-color: #fff0f6;
      border: 2px solid #ff69b4;
      border-radius: 8px;
      text-align: center;
      box-shadow: 2px 2px 8px rgba(0,0,0,0.1);
      font-family: sans-serif;
      font-size: 1.5vw;
  }
  #top-left-box img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 50%;
      margin: 0 0 7px 0;
      padding: 10px;

  }


  .overlay-image {
    position: fixed;
    top: 200px;        
    left: 50px;      
    width: 100px;      
    height: 100px;  
  }

  .overlay-image2 {
    position: fixed;
    top: 1px;       
    right: 0;    
    margin-right: -20px; 
    width: 70px;     
    height: 70px;
    z-index: 9999;  
    padding: 0;
  }



  #top-left-box h3 {
      margin: 0;
      font-size: 19px;
      color: #333;
  }

  .posts-container a {
    font-weight: bold;
    color: white;
  }

  .posts-container a:hover {
    color: #ffe0f0;
    text-decoration: underline;
  }

  
  .background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('pawbg.svg');
    background-size: cover;
    background-position: center;
    z-index: -1;
  }

  /* Bottom-left rectangle button */
  #picture-corner {
    position: fixed;
    top: 10px;
    right: 25px;
    background-color: #ff69b4;
    color: white;
    padding: 10px 15px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    z-index: 1000;
  }

  #camera-container {
    position: fixed;
    top: 10px;
    right: 10px;
    background: #ff69b4;
    padding: 10px;
    border-radius: 10px;
    display: none;
    align-items: center;  
    flex-direction: column; 
  }

  #video {
    width: 240px;
    height: 180px;
    border-radius: 10px;
  }

  #photo-result {
    position: fixed;
    top: 10px;
    right: 10px;
    background: #ff69b4;
    padding: 10px;
    border-radius: 10px;
    text-align: center;
  }

  #canvas {
    width: 240px;
    height: 180px;
    border-radius: 10px;
    border: 2px solid #ff69b4;
  }

  button {
    margin: 5px;
    padding: 5px 10px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
  }

  #save-btn {
    background-color:rgb(148, 207, 255);
    color: black;
  }

  #exit-btn {
    background-color:rgb(255, 170, 248);
    color: black;
  }

  #upload-btn {
    background-color:rgb(199, 255, 238);
    color: black;
  }

  #snap {
    margin: 5px;
    width: auto;           /* Ensure it only takes as much space as needed */
    align-self: center;    /* ⬅️ Ensures centering within flex container */
  }

  /*.story-btn {
    background-color:rgb(235, 137, 255);
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.9em;
  }*/

  .story-btn {
    width: 70px;
    height: 70px;
    background: gold;
    clip-path: polygon(
      50% 5%,    /* top point */
      65% 35%,   /* right upper */
      95% 35%,   /* right edge */
      70% 55%,   /* right lower */
      80% 90%,   /* bottom right */
      50% 70%,   /* bottom center */
      20% 90%,   /* bottom left */
      30% 55%,   /* left lower */
      5% 35%,    /* left edge */
      35% 35%    /* left upper */
    );
    border: none;
    cursor: pointer;
    transition: background 0.3s ease;
    margin-right: 15px;
  }


  .story-btn:hover {
    background-color:rgb(251, 190, 243);
  }

  .story-floating-panel button {
    background-color:rgb(222, 54, 244); /* Red color */
    color: white;
    border: none;
    padding: 8px 16px;
    font-size: 14px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    user-select: none;
  }

  .story-floating-panel button:hover {
    background-color:rgb(245, 176, 241); /* Darker red on hover */
  }

  .profile-pic-container {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
  }


</style>

</head>
<body>

<input type="hidden" id="user_id" value="<?php echo $profileUserId; ?>" />

<a href="/php/main/home/homepage.php" class="home-button">HOMEPAGE</a>

<div class="background"></div>

<div class="search-box">
  <div class="search-header">
    <img src="search.png" alt="Search Icon" class="search-icon" />
    <span class="search-title">SEARCH</span>
  </div>
  <input type="text" id="search-input" placeholder="Type keyword..." />
  <div class="search-buttons">
    <button id="search-btn">検索</button>
    <img src="cross.jpg" alt="Clear Search" id="clear-btn" class="cross-icon" />
  </div>
</div>



<div class="profile-box">


  <div id="picture-corner">📸 Take Picture</div>

  <!-- Hidden Camera UI -->
  <div id="camera-container">
    <video id="video" autoplay></video><br>
    <button id="snap">📷 Snap</button>
  </div>

  <!-- Result Display -->
  <div id="photo-result" style="display: none;">
    <canvas id="canvas"></canvas><br>
    <button id="upload-btn">🌟 Post</button>
    <button id="save-btn">💾 Save</button>
    <button id="exit-btn">❌ Exit</button>
  </div>


  <img src="side_cat.svg" alt="Overlay" class="overlay-image2">
  <!-- tomoka change -->
  <!--
  <img src="<?php echo $profilePic; ?>" alt="Profile Picture" class="profile-pic" />
  -->
  <!-- start -->
  <img src="show_profile_pic.php?id=<?= htmlspecialchars($profileUserId) ?>" alt="Profile Picture" class="profile-pic" />
  <!-- end -->
  <div class="profile-info">
    <div class="name"><?php echo $name; ?></div>
    <div class="mail"><?php echo $mail; ?></div>
    
    <form method="POST" action="post.php?id=<?php echo $profileUserId; ?>">
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

<!--
<div class="video-box">
  <video src="cat.mp4" autoplay loop muted playsinline></video>
</div>
-->
<div class="posts-container">

  <?php
  if ($resultPosts && $resultPosts->num_rows > 0) {
    while ($post = $resultPosts->fetch_assoc()) {
        $likeClass = ($post['user_reaction'] === 'like') ? 'reacted' : '';
        $dislikeClass = ($post['user_reaction'] === 'dislike') ? 'reacted' : '';

      // Check if this post owner has a story
        $hasStory = !empty($post['story']);
        $storyData = $hasStory ? base64_encode($post['story']) : '';

        echo '<div class="post-box">';
        echo '<div class="profile-pic-container">';
        echo '<img src="' . htmlspecialchars($post['profile_pic']) . '" alt="Profile Pic" class="post-profile-pic" />';

        if ($hasStory) {
          echo '<button class="story-btn" data-postid="' . $post['post_id'] . '" data-story="' . $storyData .'" ;">📸</button>';
        }
        echo '</div>';

        echo '<div class="post-content">';
        echo '<div class="post-header">' . htmlspecialchars($post['name']) . '</div>';
        echo '<div class="post-email">' . htmlspecialchars($post['mail']) . '</div>';
        echo '<div class="post-date-time">' . htmlspecialchars($post['post_date']) . ' ' . htmlspecialchars($post['post_time']) . '</div>';
        echo '<div class="post-text">' . nl2br(htmlspecialchars($post['post_content'])) . '</div>';
        echo '<div class="post-reactions">';
        echo '<button class="like-btn ' . $likeClass . '" data-postid="' . $post['post_id'] . '">👍 ' . intval($post['likes']) . '</button>';
        echo ' &nbsp; ';
        echo '<button class="dislike-btn ' . $dislikeClass . '" data-postid="' . $post['post_id'] . '">👎 ' . intval($post['dislikes']) . '</button>';
        echo '</div>';
        //echo '<button class="story-btn" data-postid="' . $post['post_id'] . '">story</button>';

        echo '</div>';
        echo '</div>';
    }
  }
  ?>




  <div style="text-align: center; margin-top: 20px; font-size: 1.2em;">
    <?php if ($page > 1): ?>
      <a href="?id=<?= $profileUserId ?>&page=<?= $page - 1 ?>"
        style="margin-right: 20px; text-decoration: none; color:rgb(255, 255, 255); font-size: 2em;">⇦</a>
    <?php else: ?>
      <span style="margin-right: 20px; color: #fefefe; font-size: 2em;"> </span>
    <?php endif; ?>

    <span style="font-weight: bold; font-family: 'Courier New', monospace; font-size: 1.4em; color: rgba(82, 2, 39, 0.71);">
      page <?= $page ?>/<?= $totalPages ?>
    </span>

    <?php if ($page < $totalPages): ?>
      <a href="?id=<?= $profileUserId ?>&page=<?= $page + 1 ?>"
        style="margin-left: 20px; text-decoration: none; color:rgb(255, 255, 255); font-size: 2em;">⇨</a>
    <?php else: ?>
      <span style="margin-left: 20px; color: #fefefe; font-size: 2em;"> </span>
    <?php endif; ?>
  </div>
</div>


<div id="top-left-box">
    <div>👑バズキング👑</div>
    <?php if ($topUser): ?>
      <!--
      <img src="crown2.svg" alt="Overlay" class="overlay-image">
    
      <img src="crown2.svg" alt="Overlay" >
    -->
      <img src="<?= htmlspecialchars($topUser['profile_pic']) ?>" alt="Profile Picture">
        
        

      <div><?= htmlspecialchars($topUser['name'])?></div>
    <?php else: ?>
        <div>データがありません</div>
    <?php endif; ?>
</div>


<script>

  const userId = document.getElementById('user_id').value;


  const searchBtn = document.getElementById('search-btn');
  const clearBtn = document.getElementById('clear-btn');
  const searchInput = document.getElementById('search-input');
  const postsContainer = document.querySelector('.posts-container');

  searchBtn.addEventListener('click', () => {
    const keyword = searchInput.value.trim().toLowerCase();
    if (!keyword) return;

    // Get all posts
    const posts = postsContainer.querySelectorAll('.post-box');
    posts.forEach(post => {
      // Get post content text only (you can adjust if needed)
      const content = post.querySelector('.post-text').textContent.toLowerCase();
      //const content = post.querySelector('.post-content').textContent.toLowerCase();
      if (content.includes(keyword)) {
        post.style.display = '';  // show post
      } else {
        post.style.display = 'none'; // hide post
      }
    });
  });

  clearBtn.addEventListener('click', () => {
    searchInput.value = '';
    // Show all posts again
    const posts = postsContainer.querySelectorAll('.post-box');
    posts.forEach(post => {
      post.style.display = '';
    });
  });

  document.querySelectorAll('.like-btn').forEach(button => {
    button.addEventListener('click', () => {
      const postId = button.getAttribute('data-postid');
      updateReaction(postId, 'like', button);
      createFloatingHeart(event);
    });
  });

  document.querySelectorAll('.dislike-btn').forEach(button => {
    button.addEventListener('click', () => {
      const postId = button.getAttribute('data-postid');
      updateReaction(postId, 'dislike', button);
      createAngryFace(event);
    });
  });

  function updateReaction(postId, type, clickedButton) {
    fetch('update_reaction.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `post_id=${postId}&reaction=${type}`
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Find buttons for this post
        const postBox = clickedButton.closest('.post-box');
        const likeBtn = postBox.querySelector('.like-btn');
        const dislikeBtn = postBox.querySelector('.dislike-btn');

        // Update counts
        likeBtn.textContent = `👍 ${data.likes}`;
        dislikeBtn.textContent = `👎 ${data.dislikes}`;

        // Update reacted class on buttons
        likeBtn.classList.toggle('reacted', data.user_reaction === 'like');
        dislikeBtn.classList.toggle('reacted', data.user_reaction === 'dislike');
      } else {
        alert(data.message || 'Error updating reaction.');
      }
    })
    .catch(() => alert('Request failed.'));
  }
 
//floating hearts
//document.querySelectorAll('.like-btn').forEach(button => {
 // button.addEventListener('click', (event) => {
   // const postId = button.getAttribute('data-postid');
    //updateReaction(postId, 'like', button);

    // Create floating heart on like click
    //createFloatingHeart(event);
  //});
//});

function createFloatingHeart(event) {
  const heart = document.createElement('div');
  heart.classList.add('floating-heart');
  heart.textContent = '❤️';

  // Position near the click
  // Use event.pageX/Y or clientX/Y + scroll offset
  heart.style.left = `${event.clientX - 10}px`;
  heart.style.top = `${event.clientY - 20}px`;


  document.body.appendChild(heart);

  // Remove the heart after 3 seconds (animation duration)
  setTimeout(() => {
    heart.remove();
  }, 3000);
}

function createAngryFace(event) {
  const heart = document.createElement('div');
  heart.classList.add('floating-heart');
  heart.textContent = '😠';

  // Position near the click
  // Use event.pageX/Y or clientX/Y + scroll offset
  heart.style.left = `${event.clientX - 10}px`;
  heart.style.top = `${event.clientY - 20}px`;


  document.body.appendChild(heart);

  // Remove the heart after 3 seconds (animation duration)
  setTimeout(() => {
    heart.remove();
  }, 3000);
}


const pictureCorner = document.getElementById('picture-corner');
const cameraContainer = document.getElementById('camera-container');
const video = document.getElementById('video');
const snapBtn = document.getElementById('snap');
const canvas = document.getElementById('canvas');
const photoResult = document.getElementById('photo-result');
const saveBtn = document.getElementById('save-btn');
const exitBtn = document.getElementById('exit-btn');
const uploadBtn = document.getElementById('upload-btn');

// Open camera when clicking the corner
pictureCorner.onclick = async () => {
  cameraContainer.style.display = 'block';
  cameraContainer.style.display = 'flex';
  

  try {
    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
    video.srcObject = stream;
    
  } catch (err) {
    alert("Camera not accessible.");
    console.error(err);
  }
};

// Take picture
snapBtn.onclick = () => {
  const context = canvas.getContext('2d');

  // Set small size
  const width = 160;
  const height = 120;
  canvas.width = width;
  canvas.height = height;

  context.drawImage(video, 0, 0, width, height);

  // Stop camera
  const stream = video.srcObject;
  stream.getTracks().forEach(track => track.stop());
  video.srcObject = null;

  // Hide camera, show photo result
  photoResult.style.display = 'block';
  cameraContainer.style.display = 'none';
  
};

// Save image
saveBtn.onclick = () => {
  const link = document.createElement('a');
  link.href = canvas.toDataURL('image/jpeg', 0.6); // small size
  link.download = 'my-picture.jpg';
  link.click();
};

// Exit preview
exitBtn.onclick = () => {
  photoResult.style.display = 'none';
  canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
};

uploadBtn.onclick = () => {
  if (canvas.width === 0 || canvas.height === 0) {
    alert("Please take a picture first!");
    return;
  }

  // Grab the user ID from the element with id="id"
  const userId = document.getElementById('user_id').value; // or .textContent depending on your HTML

  if (!userId) {
    alert("User ID not found!");
    return;
  }

  const imageData = canvas.toDataURL('image/jpeg', 0.6);
  const base64Image = imageData.split(',')[1];

  fetch('post.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      image: base64Image,
      user_id: userId
    })
  })
  .then(response => response.text())
  .then(data => {
    alert('Image uploaded successfully!');
    console.log(data);
  })
  .catch(err => {
    alert('Upload failed.');
    console.error(err);
  });
};


//story button/////////////////////////////////////////
document.addEventListener('click', function (e) {
  if (e.target.classList.contains('story-btn')) {
    const storyData = e.target.getAttribute('data-story');
    if (!storyData) return;

    // Remove existing story panel if any
    const existingPanel = document.querySelector('.story-floating-panel');
    if (existingPanel) existingPanel.remove();

    // Create floating panel
    const panel = document.createElement('div');
    panel.classList.add('story-floating-panel');
    panel.style.position = 'absolute';
    panel.style.background = 'rgb(252, 203, 247)';
    panel.style.padding = '10px';
    panel.style.borderRadius = '8px';
    panel.style.boxShadow = '0 0 10px rgba(211, 15, 232, 0.3)';
    panel.style.zIndex = '9999';
    panel.style.textAlign = 'center';

    // Create image
    const img = document.createElement('img');
    img.src = 'data:image/jpeg;base64,' + storyData;
    img.style.borderRadius = '6px';
    img.style.display = 'block';
    img.style.margin = '0 auto';

    // Set image max size to half viewport, maintaining aspect ratio
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;
    img.style.maxWidth = (viewportWidth / 2) + 'px';
    img.style.maxHeight = (viewportHeight / 2) + 'px';
    img.style.width = 'auto';
    img.style.height = 'auto';

    // Create close button (below image)
    const closeBtn = document.createElement('button');
    closeBtn.textContent = '✖';
    closeBtn.style.marginTop = '10px';
    closeBtn.style.cursor = 'pointer';
    closeBtn.onclick = () => panel.remove();

    // Append in order: image first, then close button
    panel.appendChild(img);
    panel.appendChild(closeBtn);

    // Position under clicked button
    const rect = e.target.getBoundingClientRect();
    const scrollTop = window.scrollY || window.pageYOffset;
    const scrollLeft = window.scrollX || window.pageXOffset;

    // Desired top position just below the button + 5px margin
    let topPos = scrollTop + rect.bottom + 5;
    // Left position aligned to button left
    let leftPos = scrollLeft + rect.left;

    // Get viewport height
    const viewportHeightInner = window.innerHeight;
    // Calculate space available below button in viewport coords
    const spaceBelow = viewportHeightInner - rect.bottom - 5;

    // Temporarily add panel to measure height
    panel.style.top = '0px';
    panel.style.left = '0px';
    panel.style.visibility = 'hidden';
    document.body.appendChild(panel);
    const panelHeight = panel.offsetHeight;
    panel.style.visibility = 'visible';

    // If panel height > space below, position above button
    if (panelHeight > spaceBelow) {
      topPos = scrollTop + rect.top - panelHeight - 5; // 5px margin above button
    }

    // Apply final position
    panel.style.top = `${topPos}px`;
    panel.style.left = `${leftPos}px`;

    // Add panel to document
    document.body.appendChild(panel);
  }
});







</script>


</body>
</html>