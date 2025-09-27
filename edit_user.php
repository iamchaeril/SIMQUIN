<?php
include 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: users.php");
    exit;
}

$q = $conn->query("SELECT * FROM users WHERE id = $id");
if ($q->num_rows === 0) {
    echo "<h3>Data tidak ditemukan</h3>";
    exit;
}
$users = $q->fetch_assoc();

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name_user = $conn->real_escape_string($_POST['name']);
    $username_user = $conn->real_escape_string($_POST['username']);
    $level_user = $conn->real_escape_string($_POST['level']);

    $conn->query("UPDATE users SET 
        name='$name_user', 
        username='$username_user', 
        level='$level_user'  
        WHERE id=$id");

    $success = true;
}
?>

<?php
$pageTitle = "Form Edit User";
include 'template/header.php';
include 'template/navbar.php';
?>

<div class="container content">
    <div class="content-card p-4">
        <h2 class="mb-4 text-center">Form User</h2>
        <form action="" method="post">
            <div class="row">
                <div class="col-sm-12 mb-3">
                    <label>Nama User</label>
                    <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($users['name']) ?>" />
                </div>
                <div class="col-sm-12 mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($users['username']) ?>" />
                </div>
                <div class="col-sm-12 mb-3">
                    <label>Level</label>
                    <input type="text" name="level" class="form-control" required value="<?= htmlspecialchars($users['level']) ?>" />
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="users.php" class="btn btn-success">Kembali</a>
        </form>
    </div>
</div>

<?php include 'template/scriptjs.php'; ?>
<script>
    <?php if ($success): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'User berhasil diperbarui.',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.location.href = 'users.php';
        });
    <?php endif; ?>
</script>
</body>

</html>