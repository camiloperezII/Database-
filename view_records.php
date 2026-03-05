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

// Get the selected date from the URL
$selected_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';

// === 2. CSV EXPORT LOGIC ===
if (isset($_GET['export'])) {
    ob_clean();
    $filename = $selected_date ? "Records_$selected_date.csv" : "All_Records.csv";
    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename=$filename");
    
    $output = fopen('php://output', 'w');
    fputcsv($output, array('No.', 'Name', 'Gender', 'Age', 'Purpose', 'Contact', 'Date', 'Time'));
    
    $sql = "SELECT * FROM students" . ($selected_date ? " WHERE DATE(created_at) = '$selected_date'" : "") . " ORDER BY created_at ASC";
    $result = $conn->query($sql);
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, array($no++, $row['name'], $row['gender'], $row['age'], $row['purpose'], $row['numv'], date("Y-m-d", strtotime($row['created_at'])), date("h:i A", strtotime($row['created_at']))));
    }
    fclose($output); exit();
}

// === 3. VIEW LOGIC ===
$query = "SELECT *, DATE(created_at) as log_date FROM students" . ($selected_date ? " WHERE DATE(created_at) = '$selected_date'" : "") . " ORDER BY created_at ASC";
$res = $conn->query($query);
$current_date = "";
$no = 1; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css?v=<?= time(); ?>">
    <title>Daily Records - Paombong</title>
</head>
<body>

<?php if(isset($_SESSION['msg'])): ?>
    <div id="toast" class="toast-show"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
    <script>setTimeout(() => { document.getElementById('toast').className = 'toast-hide'; }, 3000);</script>
<?php endif; ?>

<div class="container">
    <h2>Daily Visitor Logs</h2>
    
    <div class="filter-container">
        <a href="index.php" class="btn-nav">← Back</a>

        <form method="GET" class="filter-form">
            <label>Search Date:</label>
            <input type="date" name="filter_date" value="<?= $selected_date ?>">
            <button type="submit" class="btn-filter">Apply</button>
            <?php if($selected_date): ?> <a href="view_records.php" class="clear-link">✕ Clear</a> <?php endif; ?>
        </form>

        <a href="view_records.php?export=true&filter_date=<?= $selected_date ?>" class="btn-export">
            📥 Export <?= $selected_date ? "($selected_date)" : "All" ?> CSV
        </a>
    </div>

    <?php if ($res && $res->num_rows > 0): ?>
        <?php while($row = $res->fetch_assoc()): 
            $row_date = date("F j, Y", strtotime($row['log_date']));
            if ($current_date != $row_date): 
                if ($current_date != "") echo "</tbody></table></div>"; 
                $current_date = $row_date;
        ?>
            <div class="date-group-header"><span>📅 Records for: <?= $current_date ?></span></div>
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>No.</th><th>Name</th><th>Gender</th><th>Age</th><th>Purpose</th><th>Contact</th><th>Time</th><th>Actions</th></tr></thead>
                    <tbody>
            <?php endif; ?>
            <tr>
                <td style="color: #e09114; font-weight: bold;"><?= $no++ ?></td>
                <td style="font-weight:600;"><?= htmlspecialchars($row['name']) ?></td>
                <td><?= $row['gender'] ?></td>
                <td><?= $row['age'] ?></td>
                <td><?= htmlspecialchars($row['purpose']) ?></td>
                <td><?= htmlspecialchars($row['numv']) ?></td>
                <td class="date-cell"><?= date("h:i A", strtotime($row['created_at'])) ?></td>
                <td>
                    <a href="index.php?edit=<?= $row['id'] ?>" style="color: #1e5aa8; font-weight:bold; text-decoration:none;">EDIT</a> | 
                    <a href="view_records.php?delete=<?= $row['id'] ?>" style="color:red; font-weight:bold; text-decoration:none;" onclick="return confirm('Delete this record?')">DEL</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody></table></div>
    <?php else: ?>
        <div class="table-wrapper" style="padding: 40px; text-align: center;"><p style="color:#1e5aa8; font-weight: bold;">No records found.</p></div>
    <?php endif; ?>
</div>
</body>
</html>