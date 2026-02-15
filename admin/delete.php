<?php
include "../config/db.php";

$id = intval($_GET['id']);
mysqli_query($conn,"DELETE FROM portfolios WHERE id=$id");

header("Location: dashboard.php?page=manage");
exit;
