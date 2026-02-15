<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

include "../config/db.php";

// routing
$page = $_GET['page'] ?? 'overview';
$edit = false;
$row = [];

/* ---------- DELETE ---------- */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn,"DELETE FROM portfolios WHERE id=$id");
    header("Location: dashboard.php?page=manage");
    exit;
}

/* ---------- EDIT FETCH ---------- */
if ($page === 'add' && isset($_GET['id'])) {
    $edit = true;
    $id = (int)$_GET['id'];
    $res = mysqli_query($conn,"SELECT * FROM portfolios WHERE id=$id");
    $row = mysqli_fetch_assoc($res);
}

/* ---------- ADD / UPDATE ---------- */
$success = $error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name   = trim($_POST['name']);
    $desc   = trim($_POST['description']);
    $area   = trim($_POST['area']);
    $floors = trim($_POST['floors']);
    $value  = trim($_POST['value']);

    if ($name==""||$desc==""||$area==""||$floors==""||$value=="") {
        $error = "All fields are required";
    } else {
        if ($edit) {
            if (!empty($_FILES['image']['tmp_name'])) {
                $img = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
                mysqli_query($conn,"UPDATE portfolios SET 
                    project_name='$name',description='$desc',
                    build_area='$area',floors='$floors',
                    value='$value',image='$img' WHERE id=$id");
            } else {
                mysqli_query($conn,"UPDATE portfolios SET 
                    project_name='$name',description='$desc',
                    build_area='$area',floors='$floors',
                    value='$value' WHERE id=$id");
            }
            $success = "Project updated successfully";
        } else {
            $img = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
            mysqli_query($conn,"INSERT INTO portfolios
            (project_name,description,build_area,floors,value,image)
            VALUES('$name','$desc','$area','$floors','$value','$img')");
            $success = "Project added successfully";
        }
    }
}

// stats
$totalProjects = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) cnt FROM portfolios"))['cnt'];
$totalLeads    = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) cnt FROM contacts"))['cnt'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{background:#f4f6f9;font-family:'Segoe UI';margin:0}
.sidebar{
    width:260px;height:100vh;position:fixed;
    background:#111827;color:#fff;z-index:1050;   
}
.sidebar a{
    color:#cbd5e1;padding:14px 20px;display:block;text-decoration:none
}
.sidebar a:hover,.sidebar a.active{background:#1f2937;color:#fff}
.content{margin-left:260px;padding:30px}
.topbar{
    background:#fff;padding:15px 25px;border-radius:14px;
    display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;
}
.card{border-radius:14px}
@media (max-width: 992px){
    .sidebar{
        width:220px;
    }
    .content{
        margin-left:220px;
    }
}

@media (max-width: 768px){
    .sidebar{
        left:-260px;
    }
    .sidebar.show{
        left:0;
    }
    .content{
        margin-left:0;
        padding:15px;
    }
    .topbar{
        flex-direction:column;
        align-items:flex-start;
    }
    .table{
        font-size:14px;
    }
}

@media (max-width: 576px){
    h3,h4{
        font-size:18px;
    }
    .table img{
        width:60px;
    }
    .btn{
        font-size:14px;
    }
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <button class="btn btn-outline-dark d-md-none" onclick="toggleSidebar()">
    <i class="fa fa-bars"></i>
</button>

    <h4 class="text-center py-4">Hi Admin!</h4>
    <a href="?page=overview" class="<?= $page=='overview'?'active':'' ?>">Overview</a>
    <a href="?page=add" class="<?= $page=='add'?'active':'' ?>">Add Portfolio</a>
    <a href="?page=manage" class="<?= $page=='manage'?'active':'' ?>">Manage Portfolio</a>
    <a href="login.php" class="text-danger">Logout</a>
</div>

<!-- CONTENT -->
<div class="content">

<!-- TOP BAR -->
<div class="d-flex align-items-center gap-2">
    <button class="btn btn-outline-dark d-md-none" onclick="toggleSidebar()">
        <i class="fa fa-bars"></i>
    </button>
    <a href="../index.html" class="text-decoration-none d-flex align-items-center">
        <img src="../img/icons/icon-1.png" width="32" class="me-2">
        <strong class="text-dark">Arkitektur</strong>
    </a>
</div>

<br>
<?php if ($page=='overview'): ?>

<!-- OVERVIEW -->
<div class="row">
    <div class="col-md-4">
        <div class="card p-4 shadow-sm">
            <h6>Total Projects</h6>
            <h3><?= $totalProjects ?></h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-4 shadow-sm">
            <h6>Total Leads</h6>
            <h3><?= $totalLeads ?></h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 shadow-sm">
            <canvas id="chart"></canvas>
        </div>
    </div>
</div>

<div class="card mt-4 shadow-sm">
    <div class="card-header bg-white fw-semibold">Recent Projects</div>
    <table class="table mb-0">
        <tr><th>Name</th><th>Area</th><th>Floors</th><th>Value</th></tr>
        <?php
        $r=mysqli_query($conn,"SELECT * FROM portfolios ORDER BY id DESC LIMIT 5");
        while($p=mysqli_fetch_assoc($r)){ ?>
        <tr>
            <td><?= $p['project_name'] ?></td>
            <td><?= $p['build_area'] ?></td>
            <td><?= $p['floors'] ?></td>
            <td><?= $p['value'] ?></td>
        </tr>
        <?php } ?>
    </table>
</div>

<?php elseif ($page=='add'): ?>

<!-- ADD / EDIT -->
<form method="POST" enctype="multipart/form-data" class="card shadow-sm p-4 mx-auto" style="max-width:600px">
<h4 class="text-center mb-3"><?= $edit?'Edit':'Add' ?> Portfolio</h4>

<?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
<?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

<input class="form-control mb-3" name="name" value="<?= $row['project_name']??'' ?>" placeholder="Project Name" required>
<textarea class="form-control mb-3" name="description" placeholder="Description" required><?= $row['description']??'' ?></textarea>
<input class="form-control mb-3" name="area" value="<?= $row['build_area']??'' ?>" placeholder="Build-up Area" required>
<input class="form-control mb-3" name="floors" value="<?= $row['floors']??'' ?>" placeholder="Floors" required>
<input class="form-control mb-3" name="value" value="<?= $row['value']??'' ?>" placeholder="Value" required>

<?php if($edit && !empty($row['image'])): ?>
<img src="data:image/jpeg;base64,<?= $row['image'] ?>" class="mb-3 rounded" style="height:150px;object-fit:cover">
<?php endif; ?>

<input type="file" class="form-control mb-3" name="image">
<button class="btn btn-primary w-100"><?= $edit?'Update':'Save' ?></button>
</form>

<?php elseif ($page=='manage'): ?>

<!-- MANAGE -->
<table class="table bg-white shadow-sm align-middle">
<tr><th>Image</th><th>Name</th><th>Area</th><th>Floors</th><th>Value</th><th>Action</th></tr>
<?php
$m=mysqli_query($conn,"SELECT * FROM portfolios ORDER BY id DESC");
while($p=mysqli_fetch_assoc($m)){ ?>
<tr>
<td><img src="data:image/jpeg;base64,<?= $p['image'] ?>" width="80" class="rounded"></td>
<td><?= $p['project_name'] ?></td>
<td><?= $p['build_area'] ?></td>
<td><?= $p['floors'] ?></td>
<td><?= $p['value'] ?></td>
<td>
<a href="?page=add&id=<?= $p['id'] ?>" class="btn btn-sm btn-primary"><i class="fa fa-pen"></i></a>
<a href="?page=manage&delete=<?= $p['id'] ?>" onclick="return confirm('Delete?')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
</td>
</tr>
<?php } ?>
</table>

<?php endif; ?>

</div>

<script>
new Chart(document.getElementById('chart'),{
type:'pie',
data:{
labels:['Projects','Leads'],
datasets:[{data:[<?= $totalProjects ?>,<?= $totalLeads ?>],backgroundColor:['#1f2937','#dfa974']}]
}});
</script>
<script>
function toggleSidebar(){
    document.querySelector('.sidebar').classList.toggle('show');
}
</script>

</body>
</html>
