<?php
session_start(); // Required for Toast Notifications
require_once 'database.php';

$id = 0; $name = ""; $email = ""; $gender = ""; $age = ""; $purpose = ""; $mobile = ""; $update = false;

/* ================= ADD / UPDATE ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['add']) || isset($_POST['update']))) {
    $name = trim($_POST['name']); 
    $email = trim($_POST['email']); 
    $gender = $_POST['gender']; 
    $age = $_POST['age']; 
    $purpose = trim($_POST['purpose']); 
    $mobile = trim($_POST['numv']);
    
    if (isset($_POST['update'])) {
        $stmt = $conn->prepare("UPDATE students SET name=?, email=?, gender=?, age=?, purpose=?, numv=? WHERE id=?");
        $stmt->bind_param("sssissi", $name, $email, $gender, $age, $purpose, $mobile, $_POST['id']);
        $_SESSION['msg'] = "Record Updated Successfully!";
    } else {
        $stmt = $conn->prepare("INSERT INTO students (name, email, gender, age, purpose, numv) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiss", $name, $email, $gender, $age, $purpose, $mobile);
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
        $age = $data['age']; $purpose = $data['purpose']; $mobile = $data['numv']; $id = $data['id'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Paombong Database System</title>
    <link rel="icon" type="image/png" href="Images/Paombong.png">
    <link rel="stylesheet" href="style.css?v=<?= time(); ?>">
</head>
<body>

<div class="container">
    <h2>Municipality of Paombong</h2>
    <p style="text-align:right; margin-bottom: 20px;">
        <span class="system-time">SYSTEM TIME:</span> <span id="clock"></span>
    </p>

    <form method="POST" autocomplete="off">
        <input type="hidden" name="id" value="<?= $id ?>">
        
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" placeholder="e.g. Juan Dela Cruz" required>
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="email@example.com" required>
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
            <label>Contact Number (Mobile)</label>
            <input type="tel" name="numv" value="<?= htmlspecialchars($mobile) ?>" placeholder="09123456789" required maxlength="11" pattern="\d{11}" oninput="this.value=this.value.replace(/\D/g,'')">
        </div>

        <button type="submit" name="<?= $update ? 'update' : 'add' ?>" class="btn-submit">
            <?= $update ? 'Update Entry' : 'Submit  Entry' ?>
        </button>

        <a href="view_records.php" class="btn-export" style="text-align:center; display:block; margin-top: 15px; width: auto; color: white; text-decoration: none;">
            View All Registered Records
        </a>
    </form>
</div>

<script>
    function updateClock() {
        document.getElementById("clock").innerHTML = new Date().toLocaleString('en-US', { 
            hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true,
            month: 'short', day: '2-digit', year: 'numeric'
        });
    }
    setInterval(updateClock, 1000); updateClock();
</script>
</body>
</html>