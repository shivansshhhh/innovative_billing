<?php
session_start();
function createRandomPassword() {
	$chars = "003232303232023232023456789";
	srand((double)microtime()*1000000);
	$i = 0;
	$pass = '' ;
	while ($i <= 7) {
		$num = rand() % 33;
		$tmp = substr($chars, $num, 1);
		$pass = $pass . $tmp;
		$i++;
	}
	return $pass;
}
$finalcode='Purchase No-  '.createRandomPassword();
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">

<?php 
require "includes/head.php";
?>

<body>
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
        data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
        <?php require "includes/header.php"?>
        <?php require "includes/aside.php";?>
        <div class="page-wrapper">
            <div class="page-breadcrumb">
                <div class="row align-items-center">
                    <div class="col-5">
                        <h4 class="page-title">Dashboard</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Pay</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-md-flex align-items-center">
                                    <div>
                                        <h4 class="card-title"> Pay </h4>  
                                    </div> 
							</div>
                                    <?php
                                    if (isset($_GET['purchase_no'])) {
                                        $purchase_no = $_GET['purchase_no'];
                                    }
                                    ?>
                            </div>
            <form action="includes/payinvoice_inc.php" method="post" id="pay_invoice">
                <div class="col-md-12">
                    <h5><?php echo "$purchase_no";?></h5>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-2 form-group">
                            <label class="control-label">Date</label>
                            <input type="date" name="date" class="form-control text-right prc" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    <?php 
                    require "includes/conn.php";            
                    $sql ="SELECT * FROM purchase_order WHERE purchase_no = '$purchase_no'";
                    $res = mysqli_query($conn,$sql);
                    $grand_total = 0;
                    if ($res && mysqli_num_rows($res) > 0) {
                        while ($rows=mysqli_fetch_assoc($res)) {
                            $purchase_id = isset($rows['purchase_id']) ? $rows['purchase_id'] : '';
                            $medicine_name = isset($rows['medicine_name']) ? $rows['medicine_name'] : '';
                            $qty = isset($rows['qty']) ? $rows['qty'] : 0;
                            $price = isset($rows['price']) ? $rows['price'] : 0;
                            $total = $qty * $price;
                            $grand_total += $total;
                    ?>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="control-label">Medicine Name</label>
                            <input type="text" name="medicine_name[]" value="<?php echo $medicine_name;?>" class="form-control text-right" readonly>
                        </div>
                        <div class="col-md-2 form-group">
                            <label class="control-label">Qty</label>
                            <input type="number" value="<?php echo $qty;?>" name="qty[]" class="form-control text-right" readonly>
                        </div>
                        <div class="col-md-2 form-group">
                            <label class="control-label">Price</label>
                            <input type="number" value="<?php echo $price;?>" name="price[]" class="form-control text-right" readonly>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="control-label">Total</label>
                            <input type="number" value="<?php echo $total;?>" name="total[]" class="form-control text-right" readonly>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
                    <div class="row mb-3">
                        <div class="col-md-3 offset-md-6">
                            <label class="control-label"><strong>Grand Total</strong></label>
                            <input type="number" value="<?php echo $grand_total;?>" class="form-control text-right" readonly>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
                    <input type="hidden" value="<?php echo $purchase_no;?>" name="purchase_no">
                    <input type="hidden" value="1" name="status">
                    <div class="col-md-12 mb-3 float-right">
                        <label class="control-label">&nbsp</label>
                        <button class="btn btn-block btn-lg btn-success" name="submit" type="submit">
                            <i class="mdi mdi-cash"></i> Pay
                        </button>
                    </div>
                </div>
            </form>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="footer text-center">
                All Rights Reserved 
            </footer>
        </div>
    </div>
    <script src="assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <script src="assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="dist/js/app-style-switcher.js"></script>
    <script src="dist/js/waves.js"></script>
    <script src="dist/js/sidebarmenu.js"></script>
    <script src="dist/js/custom.js"></script>
    <script src="assets/libs/chartist/dist/chartist.min.js"></script>
    <script src="assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>
    <script src="dist/js/pages/dashboards/dashboard1.js"></script>
</body>
</html>
