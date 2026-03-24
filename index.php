<?php
session_start(); // Required for Toast Notifications
require_once 'database.php';

$id = 0; $name = ""; $address = ""; $email = ""; $gender = ""; $age = ""; $purpose = ""; $mobile = ""; $update = false;

/* ================= ADD / UPDATE ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['add']) || isset($_POST['update']))) {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']); 
    $email = trim($_POST['email']); 
    $gender = $_POST['gender']; 
    $age = $_POST['age']; 
    $purpose = trim($_POST['purpose']); 
    $mobile = trim($_POST['numv']);
    
    if (isset($_POST['update'])) {
        $stmt = $conn->prepare("UPDATE students SET name=?, address=?,email=?, gender=?, age=?, purpose=?, numv=? WHERE id=?");
        $stmt->bind_param("ssssissi", $name, $address, $email, $gender, $age, $purpose, $mobile, $_POST['id']);
        $_SESSION['msg'] = "Record Updated Successfully!";
    } else {
        $stmt = $conn->prepare("INSERT INTO students (name, address, email, gender, age, purpose, numv) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiss", $name, $address, $email, $gender, $age, $purpose, $mobile);
        $_SESSION['msg'] = "New Record Registered!";
    }
    $stmt->execute(); 
    header("Location: view_records.php"); 
    exit();
}

/* ================= EDIT LOGIC ================= */
if (isset($_GET['edit'])) {
    $update = true;
    $res = $conn->query("SELECT * FROM students WHERE id=" . (int)$_GET['edit']);
    if($data = $res->fetch_assoc()){
        $name = $data['name']; $email = $data['email']; $gender = $data['gender'];
        $address = $data['address'];
        $age = $data['age']; $purpose = $data['purpose']; $mobile = $data['numv']; $id = $data['id'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" id="topBtn">↑</button>
<header>

    <meta charset="utf-8">
    <title>Paombong Database System</title>
    <link rel="icon" type="image/png" href="Images/Paombong.png">
    <link rel="stylesheet" href="style.css?v=<?= time(); ?>">
</header>
<body>

    <div class = "topcontainer">
        <nav class = "topnav">
            <div class ="listtop">
                <img src="images/Paombong.png" alt="Municipality Logo" class="navlogo">
                <p class= "navtext"> | Human Resource Management Office</p>
            </div>
        </nav>
    </div>

<div class="container">
    <div class = "infoandgreet">
     <h2 id="greeting" class="greetingtext">Hello, </h2>
    <p style="text-align:right; margin-bottom: 20px;">
        <span class="systemtime">🕰️ SYSTEM TIME:</span> <span id="clock"></span>
    </p></div>

    <form method="POST" autocomplete="off">
        <input type="hidden" name="id" value="<?= $id ?>">

    

        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" placeholder="e.g. Juan Dela Cruz" required>
        </div>
        
        <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" value="<?= htmlspecialchars($address) ?>" placeholder="e.g. Paombong Bulacan" required>
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="email@example.com">
        </div>

        <div class="row">
            <div class="form-group half">
                <label>Gender</label>
                <div class="radio-group">
                    <label class="radio-label"><input type="radio" name="gender" value="Male" <?= ($gender=='Male')?'checked':'' ?> required> Male</label>
                    <label class="radio-label"><input type="radio" name="gender" value="Female" <?= ($gender=='Female')?'checked':'' ?>> Female</label>
                    <label class="radio-label"><input type="radio" name="gender" value="Others" <?= ($gender=='Others')?'checked':'' ?>> Others</label>
                </div>
            </div>
            <div class="form-group half">
                <label>Age</label>
                <input type="number" name="age" value="<?= htmlspecialchars($age) ?>" placeholder="00" required maxlength="3" min="1" max="120">
            </div>
        </div>

        <div class="form-group">
            <label>Purpose of Visit</label>
            <input type="text" name="purpose" value="<?= htmlspecialchars($purpose) ?>" placeholder="State your reason" required>
        </div>

        <div class="form-group">
            <label>Contact Number</label>
            <input type="tel" name="numv" value="<?= htmlspecialchars($mobile) ?>" placeholder="Enter Mobile No." required maxlength="11" pattern="\d{11}" oninput="this.value=this.value.replace(/\D/g,'')">
        </div>

        <button type="submit" name="<?= $update ? 'update' : 'add' ?>" class="btn-submit">
            <?= $update ? 'Update Entry' : 'Submit  Entry' ?>
        </button>

        <a href="view_records.php" class="btn-export" style="text-align:center; display:block; margin-top: 15px; width: auto; color: white; text-decoration: none;">
            View Registered Records
        </a>
    </form>
</div>
<script src="script.js"></script>
<footer class ="info">
    <div class="footercontent">
        <p class="footer-location">
            <span>📍</span> Municipal Hall, Poblacion Road, Paombong, Bulacan Philippines
        </p>
        <p class="copyright">
            &copy; 2026 Municipality of Paombong | <span class="goldtext">HRMS v1.0</span>
        </p>
    </div>
</footer>
</body>
</html>