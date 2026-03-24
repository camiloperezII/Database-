<?php
session_start();
include 'database.php';

// === 1. DELETE LOGIC ===
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM students WHERE id=" . (int)$_GET['delete']);
    $_SESSION['msg'] = "Record Deleted Successfully!";
    header("Location: view_records.php");
    exit();
}

// Get filter parameters
$selected_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';
$view_all = isset($_GET['view_all']) && $_GET['view_all'] == 'true';
$today = date('Y-m-d');

// === 2. CSV EXPORT LOGIC ===
if (isset($_GET['export'])) {
    ob_clean();
    $filename = $selected_date ? "Records_$selected_date.csv" : ($view_all ? "All_Records.csv" : "Today_Records.csv");
    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename=$filename");
    
    $output = fopen('php://output', 'w');
    fputcsv($output, array('No.', 'Name', 'Gender', 'Age', 'Address', 'Purpose', 'Contact', 'Date', 'Time'));
    
    // Determine Export Query
    if ($selected_date) {
        $export_sql = "SELECT * FROM students WHERE DATE(created_at) = '$selected_date' ORDER BY created_at ASC";
    } elseif ($view_all) {
        $export_sql = "SELECT * FROM students ORDER BY created_at ASC";
    } else {
        $export_sql = "SELECT * FROM students WHERE DATE(created_at) = '$today' ORDER BY created_at ASC";
    }
    
    $result = $conn->query($export_sql);
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, array(
            $no++, 
            $row['name'], 
            $row['gender'], 
            $row['age'], 
            $row['address'], 
            $row['purpose'], 
            $row['numv'], 
            date("Y-m-d", strtotime($row['created_at'])), 
            date("h:i A", strtotime($row['created_at']))
        ));
    }
    fclose($output); 
    exit();
}

// === 3. VIEW LOGIC (The Query) ===
if ($selected_date) {
    $query = "SELECT *, DATE(created_at) as log_date FROM students WHERE DATE(created_at) = '$selected_date' ORDER BY created_at DESC";
} elseif ($view_all) {
    $query = "SELECT *, DATE(created_at) as log_date FROM students ORDER BY created_at DESC";
} else {
    $query = "SELECT *, DATE(created_at) as log_date FROM students WHERE DATE(created_at) = '$today' ORDER BY created_at DESC";
}

$res = $conn->query($query);
$current_date = "";
$no = 1; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?v=<?= time(); ?>">
    <title>Daily Records - Paombong</title>
</head>
<body>

<button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" id="topBtn">↑</button>

<div class="topcontainer">
    <nav class="topnav">
        <div class="listtop">
            <img src="images/Paombong.png" alt="Municipality Logo" class="navlogo">
            <p class="navtext"> | Human Resource Management Office</p>
        </div>
    </nav>
</div>

<?php if(isset($_SESSION['msg'])): ?>
    <div id="toast" class="toast-show"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
    <script>setTimeout(() => { document.getElementById('toast').className = 'toast-hide'; }, 3000);</script>
<?php endif; ?>

<div class="container">
    <h2 class="titleviewrecords">Daily Visitor / Concerns Logs</h2>
    
    <div class="filter-container">
        <a href="index.php" class="btn-nav">← Back</a>

        <?php if ($view_all || $selected_date): ?>
            <a href="view_records.php" class="btn-filter" style="background: #1e5aa8; text-decoration: none;">📅 View Today Only</a>
        <?php else: ?>
            <a href="view_records.php?view_all=true" class="btn-filter" style="background: #e09114; text-decoration: none;">📚 View All History</a>
        <?php endif; ?>

        <form method="GET" class="filter-form">
            <label>Search Date:</label>
            <input type="date" name="filter_date" value="<?= $selected_date ?>">
            <button type="submit" class="btn-filter">Apply</button>
            <?php if($selected_date): ?> 
                <a href="view_records.php" class="clear-link" style="margin-left:10px; color:#e09114; font-weight:bold; text-decoration:none;">Clear</a>
            <?php endif; ?>
        </form>

        <a href="view_records.php?export=true&filter_date=<?= $selected_date ?>&view_all=<?= $view_all ? 'true' : 'false' ?>" class="btn-export">
            📥 Export CSV
        </a>
    </div>

    <?php if ($res && $res->num_rows > 0): ?>
        <?php while($row = $res->fetch_assoc()):
            $row_date = date("F j, Y", strtotime($row['log_date']));
            $today_formatted = date("F j, Y");

            if ($current_date != $row_date):
                $no = 1;
                if($current_date != "") echo "</tbody></table></div>";
                $current_date = $row_date;
        ?>
            <div class="date-group-header" style="<?= ($current_date == $today_formatted) ? 'border-left: 10px solid #1e5aa8;' : '' ?>">
                <span><?= ($current_date == $today_formatted) ? " TODAY'S TRANSACTIONS ($current_date)" : "📅 Records for: $current_date" ?></span>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Age</th>
                            <th>Address</th>
                            <th>Purpose</th>
                            <th>Contact</th>
                            <th>Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
            <?php endif; ?>
            
            <tr>
                <td style="color: #e09114; font-weight: bold;"><?= $no++ ?></td>
                <td style="font-weight:600;"><?= htmlspecialchars($row['name']) ?></td>
                <td><?= $row['gender'] ?></td>
                <td><?= $row['age'] ?></td>
                <td><?= htmlspecialchars($row['address'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($row['purpose']) ?></td>
                <td><?= htmlspecialchars($row['numv']) ?></td>
                <td class="date-cell"><?= date("h:i A", strtotime($row['created_at'])) ?></td>
                <td>
                    <a href="index.php?edit=<?= $row['id'] ?>" style="color: #1e5aa8; font-weight:bold; text-decoration:none;">EDIT</a> | 
                    <a href="view_records.php?delete=<?= $row['id'] ?>" style="color:red; font-weight:bold; text-decoration:none;" onclick="return confirm('Permanently delete this record?')">DEL</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody></table></div>

    <?php else: ?>
        <div class="table-wrapper" style="padding: 60px; text-align: center;">
            <p style="color:#1e5aa8; font-weight: bold; font-size: 1.2rem;">
                <?= $selected_date ? "No records found for $selected_date." : "No logs recorded for today yet." ?>
            </p>
            <?php if (!$view_all): ?>
                <a href="view_records.php?view_all=true" style="color: #e09114; font-weight: bold; text-decoration: underline;">Click here to view previous history</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<footer class="info">
    <div class="footercontent">
        <p class="footer-location">
            <span>📍</span> Municipal Hall, Poblacion Road, Paombong, Bulacan, Philippines
        </p>
        <p class="copyright">
            &copy; 2026 Municipality of Paombong | <span class="goldtext">HRMS v1.0</span>
        </p>
    </div>
</footer>

<script src="script.js"></script>
</body>
</html>