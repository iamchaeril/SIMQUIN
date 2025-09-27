<?php
include 'config.php';
$stmt = $conn->prepare("SELECT * FROM clients");
$stmt->execute();
$clients = $stmt->get_result();
?>

<?php
$pageTitle = "Form Tambah Quotation";
include 'template/header.php';
include 'template/navbar.php';
?>

<div class="container content">
    <div class="content-card p-4">
        <h2 class="mb-4 text-center">Form Quotation</h2>
        <form action="save_quotation.php" method="post">
            <div class="row">
                <div class="col-sm-3 mb-3">
                    <label>Nomor Quotation</label>
                    <input type="text" name="nomor" class="form-control" id="nomor" readonly required />
                </div>
                <div class="col-sm-3 mb-3">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" required />
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
                                data-address="<?= htmlspecialchars($c['client_address']) ?>">
                                <?= htmlspecialchars($c['client_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-sm-4 mb-3">
                    <label>Nama Attention</label>
                    <input type="text" name="attn" id="attn" class="form-control" readonly required />
                </div>
                <div class="col-sm-4 mb-3">
                    <label>Nomor HP / Telepon</label>
                    <input type="text" name="phone" id="phone" class="form-control" readonly required />
                </div>
                <div class="col-sm-4 mb-3">
                    <label>Email</label>
                    <input type="text" name="email" id="email" class="form-control" readonly required />
                </div>
                <div class="col-sm-12 mb-3">
                    <label>Alamat Client</label>
                    <textarea name="client_address" id="client_address" class="form-control" readonly required></textarea>
                </div>
            </div>


            <h5 class="mt-3">Item Quotation</h5>
            <div id="item-list">
                <div class="row mb-2 item-row">
                    <div class="col">
                        <select name="product_id[]" class="form-control" required>
                            <option value="">-- Select Product --</option>
                            <?php
                            $q = $conn->query("SELECT id, product_name, category FROM products ORDER BY product_name");
                            while ($row = $q->fetch_assoc()):
                            ?>
                                <option value="<?= $row['id'] ?>">
                                    <?= $row['product_name'] ?> (<?= ucfirst($row['category']) ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-2">
                        <input type="number" name="qty[]" class="form-control" placeholder="Qty" required>
                    </div>
                    <div class="col-3">
                        <input type="number" name="harga[]" class="form-control" placeholder="Harga (opsional)">
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>


            <button type="button" class="btn btn-secondary btn-sm" onclick="addItemRow()"><i class="fas fa-plus"></i></button>
            <br /> <br />
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="quotations.php" class="btn btn-success">Lihat Quotation</a>
        </form>
    </div>
</div>


<?php include 'template/scriptjs.php'; ?>
<!-- add quotation -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const input = document.getElementById('nomor');

        function generateQuotationNumber() {
            const year = new Date().getFullYear();
            const letters = Array.from({
                length: 3
            }, () => String.fromCharCode(65 + Math.floor(Math.random() * 26))).join('');
            const numbers = Math.floor(10 + Math.random() * 90); // 2 digit random number (10–99)
            return `QUO/${year}/${letters}${numbers}`;
        }

        input.value = generateQuotationNumber();
    });
</script>
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
    function addItemRow() {
        const container = document.getElementById('item-list');

        const itemRow = document.createElement('div');
        itemRow.className = 'row mb-2 item-row';

        itemRow.innerHTML = `
        <div class="col">
            <select name="product_id[]" class="form-control" required>
                <option value="">-- Select Product --</option>
                <?php
                $q = $conn->query("SELECT id, product_name, category FROM products ORDER BY product_name");
                while ($row = $q->fetch_assoc()):
                ?>
                    <option value="<?= $row['id'] ?>">
                        <?= $row['product_name'] ?> (<?= ucfirst($row['category']) ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-2">
            <input type="number" name="qty[]" class="form-control" placeholder="Qty" required>
        </div>
        <div class="col-3">
            <input type="number" name="harga[]" class="form-control" placeholder="Harga (opsional)">
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;

        container.appendChild(itemRow);
    }

    function removeItem(button) {
        const row = button.closest('.item-row');
        row.remove();
    }
</script>



</body>

</html>