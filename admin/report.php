<?php
require_once 'process_report.php';

include('sidebar.php');
include('navbar.php');

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$getURI = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$_SESSION['getURI'] = $getURI;

$getItem = mysqli_query($mysqli, " SELECT * FROM inventory  ");

$dateExist = false;
if(isset($_GET['from_date'])){
    $dateExist = true;
    $from_date = $_GET['from_date'].' 00:00:00';
    $to_date  = $_GET['to_date'].' 23:59:59';
    $getTransactions = mysqli_query($mysqli, " SELECT * FROM transaction WHERE (transaction_date BETWEEN '$from_date' AND '$to_date') AND status_transact = '1'  ");
}
//print_r($getTransactions);
?>
<title>SPCF - Accounting Office</title>
<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">
        <?php
        include('topbar.php');
        ?>
        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Transaction Report</h1>
            </div>

            <!-- Alert here -->
            <?php if (isset($_SESSION['message'])) { ?>
                <div class="alert alert-<?= $_SESSION['msg_type'] ?> alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    ?>
                </div>
            <?php } ?>
            <!-- End Alert here -->

            <!-- Add Inventory -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Select Dates</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <form method="post" action="process_report.php">
                            <table class="table" width="100%" cellspacing="0" id="">
                                <thead>
                                <tr>
                                    <th width="">From Date</th>
                                    <th width="">To Date</th>
                                    <th width="25%">Item</th>
                                    <th width="" style="display: none;">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><input type="date" class="form-control" name="from_date" required></td>
                                    <td><input type="date" class="form-control" name="to_date" required></td>
                                    <td style="display: none;">
                                        <select class="form-control" name="item_id">
                                            <option value="all" selected>ALL</option>
                                            <?php while($newItem = $getItem->fetch_array()){ ?>
                                                <option value="<?php echo $newItem['id'];?>"><?php echo $newItem['item_name'];?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" type="submit" name="get_report">Proceed</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>

                </div>
            </div>
            <!-- End Add Inventory -->

            <?php if($dateExist){ ?>
            <!-- List of Items -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">List of Reports from <?php echo $_GET['from_date'].' to '.$_GET['to_date'] ?></h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="reportTable" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>Control ID</th>
                                <th>Student ID</th>
                                <th>Full Name</th>
                                <th>Transaction Date and Time</th>
                                <th>Total Amount</th>
                                <th>Amount Paid</th>
                                <th>Change</th>
                                <th>Cashier Account</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $accumulatedEarnings = 0;
                            while($newTransactions = $getTransactions->fetch_assoc()){ ?>
                                <tr>
                                    <td><?php echo $newTransactions['id']; ?></td>
                                    <td><?php echo $newTransactions['student_id']; ?></td>
                                    <td><?php echo $newTransactions['full_name']; ?></td>
                                    <td><?php echo $newTransactions['transaction_date']; ?></td>
                                    <td>₱ <?php echo number_format($newTransactions['total_amount'],2); ?></td>
                                    <td>₱ <?php echo number_format($newTransactions['amount_paid'],2); ?></td>
                                    <td>₱ <?php echo number_format($newTransactions['amount_change'],2); ?></td>
                                    <td><?php echo $newTransactions['cashier_account']; ?></td>
                                </tr>
                            <?php
                            $accumulatedEarnings = $accumulatedEarnings + $newTransactions['total_amount'];
                            } ?>
                            </tbody>
                        </table>
                        <div style="text-align: center;" class="font-weight-bold">
                            Total Earnings on selected dates: <a class="text-success">₱<?php echo number_format($accumulatedEarnings,2); ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Item Lists -->
            <?php } ?>
        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <!-- JS here -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('#reportTable').DataTable( {
                "pageLength": 100
            } );
        } );
    </script>
    <?php
    include('footer.php');
    ?>
