<?php
$pageTitle = "Form Tambah User";
include 'template/header.php';
include 'template/navbar.php';
?>


<div class="container content">
    <div class="content-card p-4">
        <h2 class="mb-4 text-center">Form User</h2>
        <form action="save_user.php" method="post">
            <div class="row">
                <div class="col-sm-12 mb-3">
                    <label>Nama User</label>
                    <input type="text" name="name" class="form-control" required />
                </div>
                <div class="col-sm-12 mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required />
                </div>
                <div class="col-sm-12 mb-3">
                    <label>Password</label>
                    <input type="text" name="password" class="form-control" required />
                </div>
                <div class="col-sm-12 mb-3">
                    <label>Level</label>
                    <input type="text" name="level" class="form-control" required />
                </div>

            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="users.php" class="btn btn-success">Kembali</a>
        </form>
    </div>
</div>


</body>

</html>