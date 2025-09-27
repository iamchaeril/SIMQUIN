<?php
$pageTitle = "Form Tambah Client";
include 'template/header.php';
include 'template/navbar.php';
?>


<div class="container content">
    <div class="content-card p-4">
        <h2 class="mb-4 text-center">Form Client</h2>
        <form action="save_client.php" method="post">
            <div class="row">
                <div class="col-sm-6 mb-3">
                    <label>Nama Client</label>
                    <input type="text" name="client_name" class="form-control" required />
                </div>
                <div class="col-sm-6 mb-3">
                    <label>Attention</label>
                    <input type="text" name="attn" class="form-control" required />
                </div>
                <div class="col-sm-12 mb-3">
                    <label>Alamat</label>
                    <input type="text" name="client_address" class="form-control" required />
                </div>
                <div class="col-sm-6 mb-3">
                    <label>Email</label>
                    <input type="text" name="email" class="form-control" required />
                </div>
                <div class="col-sm-6 mb-3">
                    <label>Nomor HP / Telepon</label>
                    <input type="number" name="phone" class="form-control" required />
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="clients.php" class="btn btn-success">Kembali</a>
        </form>
    </div>
</div>


</body>

</html>