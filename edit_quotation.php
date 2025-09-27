<?php
include 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: quotations.php");
    exit;
}



$clients = $conn->query("SELECT * FROM clients");

$q = $conn->query("
    SELECT 
        quotations.id AS quotation_id,
        quotations.nomor,
        quotations.tanggal,
        quotations.client_id,
        clients.id AS client_id,
        clients.client_name,
        clients.attn,
        clients.phone,
        clients.email,
        clients.client_address
    FROM quotations
    JOIN clients ON quotations.client_id = clients.id  
    WHERE quotations.id = $id
");


if ($q->num_rows === 0) {
    echo "<h3>Data tidak ditemukan</h3>";
    exit;
}
$quotation = $q->fetch_assoc();



$items = [];
$itemRes = $conn->query("SELECT * FROM quotation_items WHERE quotation_id = $id");
while ($row = $itemRes->fetch_assoc()) {
    $items[] = $row;
}

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor = $conn->real_escape_string($_POST['nomor']);
    $tanggal = date('Y-m-d', strtotime($_POST['tanggal']));
    $client_id = $conn->real_escape_string($_POST['client_id']);

    $conn->query("UPDATE quotations SET 
        nomor='$nomor', 
        tanggal='$tanggal', 
        client_id='$client_id' 
        WHERE id=$id");

    $conn->query("DELETE FROM quotation_items WHERE quotation_id = $id");

    foreach ($_POST['deskripsi'] as $i => $desc) {
        $desc = $conn->real_escape_string($desc);
        $qty = (int)$_POST['qty'][$i];
        $harga = (int)$_POST['harga'][$i];

        $conn->query("INSERT INTO quotation_items (quotation_id, deskripsi, qty, harga) 
                      VALUES ($id, '$desc', $qty, $harga)");
    }

    $success = true;
}
?>

<?php
$pageTitle = "Form Edit Quotation";
include 'template/header.php';
include 'template/navbar.php';
?>


<div class="container content">
    <div class="content-card p-4">
        <h2 class="mb-4 text-center">Form Quotation</h2>
        <form method="post">
            <div class="row">
                <div class="col-sm-3 mb-3">
                    <label>Nomor Quotation</label>
                    <input type="text" name="nomor" class="form-control" readonly required value="<?= htmlspecialchars($quotation['nomor']) ?>" />
                </div>
                <div class="col-sm-3 mb-3">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" required value="<?= $quotation['tanggal'] ?>" />
                </div>
                <div class="col-sm-6 mb-3">
                    <label>Nama Client / Perusahaan</label>
                    <select name="client_id" id="clientSelect" class="form-control" required>
                        <option value="">-- Pilih Client --</option>
                        <?php while ($c = $clients->fetch_assoc()): ?>
                            <option value="<?= $c['id'] ?>"
                                data-attn="<?= htmlspecialchars($c['attn']) ?>"
                                data-phone="<?= htmlspecialchars($c['phone']) ?>"
                                data-email="<?= htmlspecialchars($c['email']) ?>"
                                data-address="<?= htmlspecialchars($c['client_address']) ?>"
                                <?= ($quotation['client_id'] == $c['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['client_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-sm-4 mb-3">
                    <label>Nama Attention</label>
                    <input type="text" name="attn" id="attn" class="form-control" readonly required value="<?= htmlspecialchars($quotation['attn']) ?>" />
                </div>
                <div class="col-sm-4 mb-3">
                    <label>Nomor HP / Telepon</label>
                    <input type="text" name="phone" id="phone" class="form-control" readonly required value="<?= htmlspecialchars($quotation['phone']) ?>" />
                </div>
                <div class="col-sm-4 mb-3">
                    <label>Email</label>
                    <input type="text" name="email" id="email" class="form-control" readonly required value="<?= htmlspecialchars($quotation['email']) ?>" />
                </div>
                <div class="col-sm-12 mb-3">
                    <label>Alamat Client</label>
                    <textarea name="client_address" id="client_address" class="form-control" readonly required><?= htmlspecialchars($quotation['client_address']) ?></textarea>
                </div>

            </div>


            <h5 class="mt-3">Item Quotation</h5>
            <div id="item-list">
                <?php foreach ($items as $item): ?>
                    <div class="row mb-2 item-row">
                        <div class="col">
                            <input type="text" name="deskripsi[]" class="form-control" placeholder="Deskripsi" value="<?= htmlspecialchars($item['deskripsi']) ?>" />
                        </div>
                        <div class="col-2">
                            <input type="number" name="qty[]" class="form-control" placeholder="Qty" value="<?= $item['qty'] ?>" required />
                        </div>
                        <div class="col-3">
                            <input type="number" name="harga[]" class="form-control" placeholder="Harga" value="<?= $item['harga'] ?>" required />
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" class="btn btn-secondary btn-sm" onclick="addItem()"><i class="fas fa-plus"></i></button>
            <br /><br />
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="quotations.php" class="btn btn-success">Lihat Quotation</a>
        </form>
    </div>
</div>




<?php include 'template/scriptjs.php'; ?>

<script>
    document.getElementById('clientSelect').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        document.getElementById('attn').value = selected.getAttribute('data-attn') || '';
        document.getElementById('phone').value = selected.getAttribute('data-phone') || '';
        document.getElementById('email').value = selected.getAttribute('data-email') || '';
        document.getElementById('client_address').value = selected.getAttribute('data-address') || '';
    });
</script>
<script>
    function addItem() {
        const item = `
        <div class="row mb-2 item-row">
          <div class="col">
            <input type="text" name="deskripsi[]" class="form-control" placeholder="Deskripsi" required />
          </div>
          <div class="col-2">
            <input type="number" name="qty[]" class="form-control" placeholder="Qty" required />
          </div>
          <div class="col-3">
            <input type="number" name="harga[]" class="form-control" placeholder="Harga" required />
          </div>
          <div class="col-auto">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>
      `;
        document.getElementById("item-list").insertAdjacentHTML("beforeend", item);
    }

    function removeItem(button) {
        button.closest(".item-row").remove();
    }
</script>

<script>
    <?php if ($success): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Quotation berhasil diperbarui.',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.location.href = 'quotations.php';
        });
    <?php endif; ?>
</script>

</body>

</html>