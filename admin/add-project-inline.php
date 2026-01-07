<?php
include "../config/db.php";

$edit = false;
$success = "";
$error = "";

$id = $_GET['id'] ?? "";

// FETCH DATA IF EDIT
if ($id) {
    $edit = true;
    $res = mysqli_query($conn,"SELECT * FROM portfolios WHERE id=$id");
    $row = mysqli_fetch_assoc($res);
}

// SAVE / UPDATE
if ($_POST) {
    $name   = $_POST['name'];
    $desc   = $_POST['description'];
    $area   = $_POST['area'];
    $floors = $_POST['floors'];
    $value  = $_POST['value'];

    if ($edit) {
        // UPDATE
        if (!empty($_FILES['image']['tmp_name'])) {
            $img = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
            mysqli_query($conn,"UPDATE portfolios SET 
                project_name='$name',
                description='$desc',
                build_area='$area',
                floors='$floors',
                value='$value',
                image='$img'
                WHERE id=$id");
        } else {
            mysqli_query($conn,"UPDATE portfolios SET 
                project_name='$name',
                description='$desc',
                build_area='$area',
                floors='$floors',
                value='$value'
                WHERE id=$id");
        }
        $success="Updated successfully!";
    } else {
        // INSERT
        $img = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
        mysqli_query($conn,"INSERT INTO portfolios 
        (project_name,description,build_area,floors,value,image)
        VALUES('$name','$desc','$area','$floors','$value','$img')");
        $success="Added successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title><?= $edit ? "Edit" : "Add" ?> Portfolio</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f4f6f9; font-family:'Segoe UI'; }
.content {
    margin-left:20px;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
}
.card {
    max-width:600px;
    width:100%;
    padding:40px;
    border-radius:14px;
}
</style>
</head>

<body>

<div class="content">
<form method="POST" enctype="multipart/form-data" class="card shadow-sm">

<h3 class="text-center mb-4"><?= $edit ? "Edit Portfolio" : "Add Portfolio" ?></h3>

<?php if($success){ ?>
<div class="alert alert-success"><?= $success ?></div>
<?php } ?>

<input class="form-control mb-3" name="name" value="<?= $row['project_name'] ?? '' ?>" placeholder="Project Name" required>

<textarea class="form-control mb-3" name="description" placeholder="Description" required><?= $row['description'] ?? '' ?></textarea>

<input class="form-control mb-3" name="area" value="<?= $row['build_area'] ?? '' ?>" placeholder="Build-up Area" required>

<input class="form-control mb-3" name="floors" value="<?= $row['floors'] ?? '' ?>" placeholder="No of Floors" required>

<input class="form-control mb-3" name="value" value="<?= $row['value'] ?? '' ?>" placeholder="Project Value" required>

<input type="file" class="form-control mb-4" name="image">

<button class="btn btn-primary w-100">
<?= $edit ? "Update Project" : "Save Project" ?>
</button>

</form>
</div>

</body>
</html>
