<?php
include("configASL.php");
session_start();
if(!isset($_SESSION['aid'])) {
    header("location:index.php");
}
$aid = $_SESSION['aid'];
$x = mysqli_query($al, "SELECT * FROM admin WHERE aid='$aid'");
$y = mysqli_fetch_array($x);
$name = $y['name'];

// Handle editing or deleting posts
$postId = $_GET['post_id'] ?? null; // Get post ID from query parameter

if ($postId) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete'])) {
            // Handle deletion
            $stmt = $al->prepare("DELETE FROM feedback WHERE id = ?");
            $stmt->bind_param('i', $postId);
            $stmt->execute();
            header("Location: feeds.php"); // Redirect after delete
            exit;
        } elseif (isset($_POST['edit'])) {
            // Handle editing
            $newContent = $_POST['content'];
            if ($newContent) {
                $stmt = $al->prepare("UPDATE feedback SET content = ? WHERE id = ?");
                $stmt->bind_param('si', $newContent, $postId);
                $stmt->execute();
                header("Location: feeds.php"); // Redirect after update
                exit;
            } else {
                echo "Post content cannot be empty.";
            }
        }
    } else {
        // Fetch post from database for editing
        $stmt = $al->prepare("SELECT * FROM feedback WHERE id = ?");
        $stmt->bind_param('i', $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        $post = $result->fetch_assoc();

        if ($post) {
            ?>
            <!doctype html>
            <html>
            <head>
            <meta charset="utf-8">
            <title>Edit/Delete Post</title>
            </head>
            <body>
            <?php if (isset($_GET['action']) && $_GET['action'] == 'edit') { ?>
                <form method="POST">
                    <textarea name="content"><?php echo htmlspecialchars($post['content']); ?></textarea>
                    <button type="submit" name="edit">Save Changes</button>
                </form>
            <?php } elseif (isset($_GET['action']) && $_GET['action'] == 'delete') { ?>
                <form method="POST">
                    <p>Are you sure you want to delete this post?</p>
                    <button type="submit" name="delete">Delete Post</button>
                </form>
            <?php } ?>
            </body>
            </html>
            <?php
        } else {
            echo "Post not found.";
        }
    }
    exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Student Feedback System</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="topHeader">
    Coimbatore Institute of Engineering and Technology<br />
    <span class="tag">STUDENT FEEDBACK SYSTEM</span>
</div>
<br>
<br>
<br>
<br>
<div id="content" align="center">
    <br>
    <br>
    <span class="SubHead">Student Feedback</span>
    <br>
    <br>

    <form method="post" action="feeds_2.php" >
    <div id="table"> 
        <div class="tr">
            <div class="td">
                <label>Faculty : </label>
            </div>
            <div class="td">
                <select name="faculty_id" required>
                    <option value="NA" disabled selected> - - Select Faculty - -</option>
                    <?php
                    $x = mysqli_query($al, "SELECT * FROM faculty");
                    while ($y = mysqli_fetch_array($x)) {
                        ?>
                        <option value="<?php echo $y['faculty_id'];?>"><?php echo $y['name'];?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="tr">
            <div class="td">
                <label>Subject : </label>
            </div>
            <div class="td">
                <div class="td">
                    <select name="subject" required>
                        <option value="NA" disabled selected> - - Select Subject - -</option>
                        <?php
                        $x = mysqli_query($al, "SELECT * FROM faculty");
                        while ($y = mysqli_fetch_array($x)) {
                            ?>
                            <option value="<?php echo $y['s1'];?>"><?php echo $y['s1'];?></option>
                            <option value="<?php echo $y['s2'];?>"><?php echo $y['s2'];?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
        
    <div class="tdd">
        <input type="button" onClick="window.location='home.php'" value="BACK">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="NEXT" />
    </div>
    
    <br>
    </form>
    
    <!-- Displaying feedback posts -->
    <div id="feedback_posts">
        <?php
        $stmt = $al->query("SELECT * FROM feedback");
        while ($post = $stmt->fetch_assoc()) {
            echo "<div>";
            echo "<p>" . htmlspecialchars($post['content']) . "</p>";
            echo "<a href='feeds.php?post_id=" . $post['id'] . "&action=edit'>Edit</a> | ";
            echo "<a href='feeds.php?post_id=" . $post['id'] . "&action=delete'>Delete</a>";
            echo "</div>";
        }
        ?>
    </div>
</div>
</body>
</html>
