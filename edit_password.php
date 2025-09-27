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

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass2'];

    if ($pass1 !== $pass2) {
        $error = "Password tidak cocok!";
    } else {
        $hashed = password_hash($pass1, PASSWORD_DEFAULT);
        $update = $conn->query("UPDATE users SET password='$hashed' WHERE id=$id");

        if ($update) {
            $success = true;
        } else {
            $error = "Gagal mengupdate password!";
        }
    }
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
                    <input type="text" class="form-control" value="<?= htmlspecialchars($users['name']) ?>" disabled />
                </div>
                <div class="col-sm-12 mb-3">
                    <label>Username</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($users['username']) ?>" disabled />
                </div>
                <div class="col-sm-12 mb-3">
                    <label>Level</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($users['level']) ?>" disabled />
                </div>
                <div class="col-sm-12 mb-3">
                    <label>Password Baru</label>
                    <input type="password" name="pass1" class="form-control" required />
                </div>
                <div class="col-sm-12 mb-3">
                    <label>Masukan Ulang Password Baru</label>
                    <input type="password" name="pass2" class="form-control" required />
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
            text: 'Password berhasil diperbarui.',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.location.href = 'users.php';
        });
    <?php elseif (!empty($error)): ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '<?= $error ?>'
        });
    <?php endif; ?>
</script>
</body>

</html>