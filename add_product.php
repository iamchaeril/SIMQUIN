<?php
$pageTitle = "Form Tambah Product";
include 'template/header.php';
include 'template/navbar.php';
?>


<div class="container content">
    <div class="content-card p-4">
        <h2 class="mb-4 text-center">Form Product</h2>
        <form action="save_product.php" method="post">
            <div class="row">
                <div class="col-sm-12 mb-3">
                    <label>Nama Product</label>
                    <input type="text" name="product_name" class="form-control" required />
                </div>
                <div class="col-sm-12 mb-3">

                    <label for="category">Kategori Produk</label>
                    <select name="category" id="category" class="form-control" required>
                        <option value="" disabled selected>-- Pilih Kategori --</option>
                        <option value="barang">Barang</option>
                        <option value="jasa">Jasa</option>
                    </select>
                </div>
                <div class="col-sm-12 mb-3">
                    <label>Harga</label>
                    <input type="text" name="price" class="form-control" required />
                </div>


            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="products.php" class="btn btn-success">Kembali</a>
        </form>
    </div>
</div>


</body>

</html>