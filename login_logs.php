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
        $where .= " AND (username LIKE '%$keyword%' OR status LIKE '%$keyword%')";
    }


    $countSql = "SELECT COUNT(*) as total FROM login_logs $where";
    $countResult = $conn->query($countSql);
    $totalData = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalData / $limit);

    $sql = "SELECT * FROM login_logs $where ORDER BY id DESC LIMIT $limit OFFSET $offset";
    $result = $conn->query($sql);
    $no = $offset + 1;

    ob_start(); // buffer output
    if ($result->num_rows > 0):
        while ($row = $result->fetch_assoc()):


?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['username'] ?></td>
                <td><?= $row['status'] ?></td>
                <td><?= $row['ip_address'] ?></td>
                <td><?= $row['user_agent'] ?></td>
                <td><?= $row['created_at'] ?></td>
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

$result = $conn->query("SELECT * FROM login_logs ORDER BY id DESC LIMIT 10");
$countSql = "SELECT COUNT(*) as total FROM login_logs";
$countResult = $conn->query($countSql);
$totalData = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / 10);
?>

<?php
$pageTitle = "login_logs";
$activePage = 'login_logs';
include 'template/header.php';
include 'template/navbar.php';
?>

<!-- Main Content -->
<div class="content">
    <h2 class="text-center" style="margin-top: 0.1rem;">Data Login Logs</h2>
    <div class="row align-items-end g-2 mb-3">
        <div class="col-md-4">
            <input type="text" id="search" class="form-control" placeholder="Cari User atau nama ...">
        </div>
    </div>


    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Status</th>
                <th>Ip Adress</th>
                <th>User Agent</th>
                <th>Datetime</th>
            </tr>
        </thead>
        <tbody id="quotation-body">
            <?php
            $no = 1;
            while ($row = $result->fetch_assoc()):

            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['username'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td><?= $row['ip_address'] ?></td>
                    <td><?= $row['user_agent'] ?></td>
                    <td><?= $row['created_at'] ?></td>
                </tr>
            <?php endwhile ?>
            <?php render_pagination(1, $totalPages); ?>
        </tbody>
    </table>
</div>

<?php include 'template/scriptjs.php'; ?>

<script>
    function fetchData(page = 1) {
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






</body>

</html>