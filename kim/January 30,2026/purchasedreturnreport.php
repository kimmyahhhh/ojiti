<?php
    session_start();
    if (isset($_SESSION['EMPNO']) && isset($_SESSION['USERNAME']) && isset($_SESSION["AUTHENTICATED"]) && $_SESSION["AUTHENTICATED"] === true) {
?>

<!doctype html>
<html lang="en" dir="ltr">
    <?php
        include('../../includes/pages.header.php');
    ?>
      <link rel="stylesheet" href="../../assets/datetimepicker/jquery.datetimepicker.css">
      <link rel="stylesheet" href="../../assets/select2/css/select2.min.css">

    <body class="  ">
        <!-- loader Start -->
        <div id="loading">
        <div class="loader simple-loader">
            <div class="loader-body"></div>
        </div>
        </div>
        <!-- loader END -->

        <?php
            include('../../includes/pages.sidebar.php');
            include('../../includes/pages.navbar.php');
        ?>

            <style>
                label,
                th {
                    color: #090909;
                }

                main {
                    background-color: #EAEAF6;
                    height: 100vh;
                }
            </style>

            <div class="container mt-4">
                <div class="shadow rounded-3 p-3" style="background-color: white;">
                    <p style="color: blue; font-weight: bold;" class="fs-5 my-2">Purchased Return Report</p>
                </div>
                <div class="shadow p-3 rounded-3 mt-4" style="background-color: white;">
                    <form method="GET">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="from-date" class="form-label">FROM:</label>
                                <input type="date" id="from-date" name="from-date" class="form-control" value="<?php echo isset($_GET['from-date']) ? $_GET['from-date'] : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="to-date" class="form-label">TO:</label>
                                <input type="date" id="to-date" name="to-date" class="form-control" value="<?php echo isset($_GET['to-date']) ? $_GET['to-date'] : ''; ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col text-end">
                                <button type="submit" class="btn btn-primary" id="search-btn">Search</button>
                            </div>
                        </div>
                    </form>
                </div>

                <?php
                    include_once("../../database/connection.php");
                    $db = new Database();
                    $conn = $db->conn;

                    if (isset($_GET['from-date']) && isset($_GET['to-date'])) {
                        $fromDate = $_GET['from-date'];
                        $toDate = $_GET['to-date'];
                ?>
                <div class="shadow p-3 rounded-3 mt-4" style="background-color: white;">
                    <div class="table-responsive">
                        <table class="table table-hover" id="reportTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Transaction No</th>
                                    <th>Product</th>
                                    <th>SI No</th>
                                    <th>Serial No</th>
                                    <th>Quantity</th>
                                    <th>Reason</th>
                                    <th>Branch</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $sql = "SELECT * FROM tbl_purchasereturned WHERE DATE(DateAdded) BETWEEN '$fromDate' AND '$toDate' ORDER BY DateAdded DESC";
                                    $result = $conn->query($sql);

                                    if ($result && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td><?php echo $row['DateAdded']; ?></td>
                                    <td><?php echo $row['TransactionNo']; ?></td>
                                    <td><?php echo $row['Product']; ?></td>
                                    <td><?php echo $row['SIno']; ?></td>
                                    <td><?php echo $row['Serialno']; ?></td>
                                    <td><?php echo $row['Quantity']; ?></td>
                                    <td><?php echo isset($row['Reason']) ? htmlspecialchars($row['Reason']) : ''; ?></td>
                                    <td><?php echo $row['Branch']; ?></td>
                                    <td><?php echo $row['User']; ?></td>
                                </tr>
                                <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='9' class='text-center'>No records found</td></tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php
                    }
                ?>
            </div>

        
        <?php
            include('../../includes/pages.footer.php');
        ?>

        <script src="../../assets/datetimepicker/jquery.datetimepicker.full.js"></script>
        <script src="../../assets/select2/js/select2.full.min.js"></script>
        <script src="../../js/generalledger/posting.js?<?= time() ?>"></script>

    </body>
</html>
<?php
  } else {
    echo '<script> window.location.href = "../../login.php"; </script>';
  }
?>

<script>
    // calendar function
    document.addEventListener('DOMContentLoaded', function() {
        const dateInputs = document.querySelectorAll('.form-control[type="date"]');

        dateInputs.forEach(function(input) {
            input.addEventListener('input', function() {
                let value = input.value;
                let parts = value.split('-');
                if (parts.length === 3) {
                    let year = parts[0].substring(0, 4); // Limiting to 4 digits
                    let month = parts[1];
                    let day = parts[2];
                    input.value = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
                }
            });

            input.addEventListener('blur', function() {
                let value = input.value;
                let parts = value.split('-');
                if (parts.length === 3) {
                    let year = parts[0].substring(0, 4); // Limiting to 4 digits
                    let month = parts[1].padStart(2, '0');
                    let day = parts[2].padStart(2, '0');
                    input.value = `${year}-${month}-${day}`;
                }
            });
        });
    });
</script>