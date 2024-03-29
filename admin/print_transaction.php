<?php
require_once 'process_inventory.php';

include('sidebar.php');
include('navbar.php');

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$getURI = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$_SESSION['getURI'] = $getURI.'?';

if(isset($_GET['id'])){
    $receipt_id = $_GET['id'];
}

$getReceipt = mysqli_query($mysqli, "SELECT * FROM issue_receipt WHERE id = '$receipt_id' ");
$newReceipt = $getReceipt->fetch_array();
$id = $newReceipt['transaction_id'];

$getTransaction = mysqli_query($mysqli, "SELECT * FROM transaction WHERE id = '$id' ");
$newTransaction = $getTransaction->fetch_array();
$series_id = $newTransaction['series_id'];
$balance = $newTransaction['amount_paid'] - $newTransaction['total_amount'];

?>
<title>SPCF Officie Receipt. Control ID: <?php echo $id; ?>  AR No: <?php echo sprintf('%08d',$receipt_id); ?> </title>
<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">
        <?php
        include('topbar.php');
        ?>
        <?php
        $counter = 0;
        while($counter<2){
            ?>
            <!-- Begin Page Content -->
            <p style="page-break-before: always">
            <div class="container-fluid">
                <br>
                <br>
                <!-- Page Heading -->
                <div class="align-items-center justify-content-between mb-4" >
                    <h4 style="text-align: center !important; font-family: 'Times New Roman' !important; margin-bottom: -5px;">
                        <img src="../img/logo.png" style="width: 40px;">
                        SYSTEMS PLUS COLLEGE FOUNDATION
                    </h4>
                    <div style="text-align: center;">Mc Arthur Hi-Way Balibago, Angeles City, Pampanga</div>
                </div>

                <!-- View Individual Transactions -->
                <div class="mb-4">
                    <div class="card-header py-3">
                        <span class="h6 m-0 font-weight-bold text-danger">Transaction Control ID: <?php echo sprintf('%08d',$id); ?></span>
                        <span class=" h6 m-0 font-weight-bold text-danger float-right">Series No: <?php echo sprintf('%08d',$series_id); ?></span>
                    </div>
                    <div class="card-body">
                        <span class="float-right">Date: <b><?php echo date('Y-m-d H:i:s');?></b></span>
                        Customer Name: <b><?php echo $newTransaction['full_name']; ?></b>
                        <br>
                        Address: <b><?php echo $newTransaction['address']; ?></b>
                        <br>
                        Phone Number: <b><?php echo $newTransaction['phone_num']; ?></b>
                        <br>
                        <br>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $getTransactionLists = mysqli_query($mysqli, "SELECT * FROM transaction_lists WHERE transaction_id = '$id' AND void = '0' ");
                                $total = 0;
                                while ($newTransactionList=$getTransactionLists->fetch_assoc()){
                                    $item_id = $newTransactionList['item_id'];
                                    $getItem = mysqli_query($mysqli, "SELECT * FROM inventory WHERE id = '$item_id' ");
                                    $newItem= $getItem->fetch_array();
                                    $itemName = $newItem['item_name'];

                                    $subTotal = $newTransactionList['price'] * $newTransactionList['qty'];
                                    ?>
                                    <tr>
                                        <td><?php echo strtoupper($itemName); ?></td>
                                        <td><?php echo $newTransactionList['qty'].' pc(s)'; ?></td>
                                        <td>₱ <?php echo number_format($newTransactionList['price'],2); ?></td>
                                        <td>₱ <?php echo number_format($subTotal,2); ?></td>
                                    </tr>
                                    <?php
                                    $total += $subTotal;
                                } ?>
                                <tr>
                                    <td colspan="3"><span class="float-right font-weight-bold ">Total:</span></td>
                                    <td><span class="font-weight-bold">₱<?php echo number_format($total,2); ?></span></td>
                                </tr>
                                </tbody>
                            </table>

                            <span class="float-right"><h6><b>Total Amount Paid: ₱<?php echo number_format($newTransaction['amount_paid'],2); ?></b></h6></span>
                            <br>
                            <br>
                            <span class="float-right"><h6><b>Change: ₱<?php echo number_format($newTransaction['amount_paid']-$newTransaction['total_amount'],2); ?></b></h6></span>
                            <br>
                            <br>
                            <span class="float-right"><h6><b style="display: none; color: <?php if($balance<0){ echo 'red';}else{echo 'green';} ?>">Balance: <?php echo $balance; ?></b></h6></span>
                            <div style="">
                                <span class="float-right">Cashier & Signature: <u>_____<?php echo ucfirst($_SESSION['account_full_name']); ?>_____</u></span>
                                <a style="display: none;" href="view_transaction.php?id=<?php echo $id; ?>" class="text-white btn btn-sm btn-info float-left"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
                                <?php if($counter==1){ ?>
                                    STUDENT'S COPY
                                <?php } else { ?>
                                    ACCOUNTING OFFICE'S COPY
                                <?php } ?>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- End View Individual Transactions -->

            </div>
            <?php $counter++;
        }
        ?>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <!-- JS here -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('#transactionTable').DataTable( {
                "pageLength": 25
            } );
        } );
    </script>
    <?php
    include('footer.php');
    ?>
    <style>
        .sidebar, .navbar, .sticky-footer{
            display: none;
        }
        html{
            font-size: 12px;
        }
    </style>