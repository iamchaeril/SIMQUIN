<?php
include 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: clients.php");
    exit;
}

$q = $conn->query("SELECT * FROM clients WHERE id = $id");
if ($q->num_rows === 0) {
    echo "<h3>Data tidak ditemukan</h3>";
    exit;
}
$client = $q->fetch_assoc();


$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = $conn->real_escape_string($_POST['client_name']);
    $attn = $conn->real_escape_string($_POST['attn']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);
    $client_address = $conn->real_escape_string($_POST['client_address']);


    $conn->query("UPDATE clients SET 
        client_name='$client_name', 
        attn='$attn', 
        phone='$phone', 
        email='$email', 
        client_address='$client_address' 
        WHERE id=$id");

    $success = true;
}
?>

<?php
$pageTitle = "Form Edit Client";
include 'template/header.php';
include 'template/navbar.php';
?>


<div class="container content">
    <div class="content-card p-4">
        <h2 class="mb-4 text-center">Form Client</h2>
        <form action=" " method="post">
            <div class="row">
                <div class="col-sm-6 mb-3">
                    <label>Nama Client</label>
                    <input type="text" name="client_name" class="form-control" required value="<?= htmlspecialchars($client['client_name']) ?>" />
                </div>
                <div class="col-sm-6 mb-3">
                    <label>Attention</label>
                    <input type="text" name="attn" class="form-control" required value="<?= htmlspecialchars($client['attn']) ?>" />
                </div>
                <div class="col-sm-12 mb-3">
                    <label>Alamat</label>
                    <input type="text" name="client_address" class="form-control" required value="<?= htmlspecialchars($client['client_address']) ?>" />
                </div>
                <div class="col-sm-6 mb-3">
                    <label>Email</label>
                    <input type="text" name="email" class="form-control" required value="<?= htmlspecialchars($client['email']) ?>" />
                </div>
                <div class="col-sm-6 mb-3">
                    <label>Nomor HP / Telepon</label>
                    <input type="number" name="phone" class="form-control" required value="<?= htmlspecialchars($client['phone']) ?>" />
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="clients.php" class="btn btn-success">Batal</a>
        </form>
    </div>
</div>

<?php include 'template/scriptjs.php'; ?>
<script>
    <?php if ($success): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Client berhasil diperbarui.',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.location.href = 'clients.php';
        });
    <?php endif; ?>
</script>

</body>

</html>