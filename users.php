<?php
include 'config.php';

function render_pagination($page, $totalPages)
{
    if ($totalPages <= 1) return;

    echo '<tr id="pagination-row"><td colspan="6" class="text-center">
        <nav><ul class="pagination justify-content-center">';

    if ($page > 1) {
        echo '<li class="page-item"><a href="#" class="page-link" data-page="' . ($page - 1) . '">«</a></li>';
    }

    $max = min($totalPages, 5);
    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i <= $max || $i == $totalPages) {
            if ($i == $page) {
                echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                echo '<li class="page-item"><a href="#" class="page-link" data-page="' . $i . '">' . $i . '</a></li>';
            }
        } elseif ($i == $max + 1) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    if ($page < $totalPages) {
        echo '<li class="page-item"><a href="#" class="page-link" data-page="' . ($page + 1) . '">»</a></li>';
    }

    echo '</ul></nav></td></tr>';
}

if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    $keyword = isset($_GET['keyword']) ? $conn->real_escape_string($_GET['keyword']) : '';
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $where = "WHERE 1=1";
    if ($keyword !== '') {
        $where .= " AND (username LIKE '%$keyword%' OR name LIKE '%$keyword%')";
    }


    $countSql = "SELECT COUNT(*) as total FROM users $where";
    $countResult = $conn->query($countSql);
    $totalData = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalData / $limit);

    $sql = "SELECT * FROM users $where ORDER BY id DESC LIMIT $limit OFFSET $offset";
    $result = $conn->query($sql);
    $no = $offset + 1;

    ob_start(); // buffer output
    if ($result->num_rows > 0):
        while ($row = $result->fetch_assoc()):


?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['username'] ?></td>
                <td><?= $row['level'] ?></td>
                <td>
                    <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning mb-1"><i class="fas fa-edit"></i></a>
                    <a href="edit_password.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info mb-1"><i class="fas fa-key"></i></a>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id'] ?>" data-user="<?= htmlspecialchars($row['username']) ?>"><i class="fas fa-trash-alt"></i></button>
                </td>
            </tr>
        <?php endwhile;
    else: ?>
        <tr>
            <td colspan="6" class="text-center">Data tidak ditemukan</td>
        </tr>
<?php endif;
    render_pagination($page, $totalPages);
    $content = ob_get_clean();
    echo $content;
    exit;
}

$result = $conn->query("SELECT * FROM users ORDER BY id DESC LIMIT 10");
$countSql = "SELECT COUNT(*) as total FROM users";
$countResult = $conn->query($countSql);
$totalData = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / 10);
?>

<?php
$pageTitle = "Users";
$activePage = 'users';
include 'template/header.php';
include 'template/navbar.php';
?>

<!-- Main Content -->
<div class="container content py-2">
    <h2 class="text-center" style="margin-top: 0.1rem;">Data Users</h2>
    <div class="row align-items-end g-2 mb-3">
        <div class="col-md-2 d-grid">
            <a href="add_user.php" class="btn btn-primary h-100">+ Tambah Baru</a>
        </div>

        <div class="col-md-4">
            <input type="text" id="search" class="form-control" placeholder="Cari Username atau nama...">
        </div>
        <div class="col-md-4"><a href="login_logs.php" class="btn btn-info h-100"> <i class="fas fa-user-clock me-1"></i>Login Logs</a></div>
    </div>


    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama User</th>
                <th>Username</th>
                <th>Level</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="quotation-body">
            <?php
            $no = 1;
            while ($row = $result->fetch_assoc()):

            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['username'] ?></td>
                    <td><?= $row['level'] ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning mb-1"><i class="fas fa-edit"></i></a>
                        <a href="edit_password.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info mb-1"><i class="fas fa-key"></i></a>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id'] ?>" data-user="<?= htmlspecialchars($row['username']) ?>"><i class="fas fa-trash-alt"></i></button>
                    </td>
                </tr>
            <?php endwhile ?>
            <?php render_pagination(1, $totalPages); ?>
        </tbody>
    </table>
</div>

<?php include 'template/scriptjs.php'; ?>

<script>
    let currentPage = 1;

    function fetchData(page = 1) {
        currentPage = page;
        const keyword = $('#search').val();

        $.get('<?= basename(__FILE__) ?>', {
            ajax: '1',
            keyword: keyword,
            page: page
        }, function(data) {
            let html = $('<div>').html(data);
            $('#quotation-body').html(html.find('#quotation-body').html() ?? data);
        });
    }

    $('#search').on('keyup', () => fetchData(1));
    $(document).on('click', '.page-link[data-page]', function(e) {
        e.preventDefault();
        fetchData($(this).data('page'));
    });
</script>

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'user berhasil ditambahkan.',
            showConfirmButton: false,
            timer: 2000
        });
    </script>
<?php endif; ?>

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


<script>
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        const username = $(this).data('user');

        Swal.fire({
            title: 'Yakin ingin menghapus?',
            html: `User <strong>${username}</strong> akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('delete_user.php', {
                    id: id
                }, function(response) {
                    if (response === 'OK') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: `User ${username} telah dihapus.`,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        fetchData(currentPage); // reload current page
                    } else {
                        Swal.fire('Gagal', 'Gagal menghapus data.', 'error');
                    }
                });
            }
        });
    });
</script>


</body>

</html>