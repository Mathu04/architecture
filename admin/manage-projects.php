<?php
include "../config/db.php";
$data = mysqli_query($conn,"SELECT * FROM portfolios ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Portfolio</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
body { background:#f4f6f9; font-family:'Segoe UI'; }
.content {
    margin-left:150px;
    padding:30px;
    margin-top:10px;
}
.table img {
    border-radius:8px;
}
.action-btns a {
    margin-right:8px;
}
</style>
</head>

<body>

<div class="content">
<h3 class="mb-4">Manage Portfolio</h3>

<table class="table table-bordered bg-white shadow-sm align-middle">
<thead class="table-light">
<tr>
<th>Image</th>
<th>Project</th>
<th>Area</th>
<th>Floors</th>
<th>Value</th>
<th width="120">Action</th>
</tr>
</thead>

<tbody>
<?php while($row=mysqli_fetch_assoc($data)){ ?>
<tr>
<td>
<img src="data:image/jpeg;base64,<?= $row['image'] ?>" width="90">
</td>
<td><?= $row['project_name'] ?></td>
<td><?= $row['build_area'] ?></td>
<td><?= $row['floors'] ?></td>
<td><?= $row['value'] ?></td>
<td class="action-btns">
<a href="dashboard.php?page=add&id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
<i class="fa fa-pen"></i>
</a>
<a href="delete.php?id=<?= $row['id'] ?>" 
   onclick="return confirm('Delete this project?')" 
   class="btn btn-sm btn-danger">
<i class="fa fa-trash"></i>
</a>
</td>
</tr>
<?php } ?>
</tbody>

</table>
</div>

</body>
</html>
