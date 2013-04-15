<?php
  define("KWH_FIXED", 0.14);
  define("SYS_DIVISOR", 1440); // Used version
  define("WATTS_PER_PANEL", 260);
  define("ANNUAL_INFLATION_RATE", 8.25); // percent
  define("COST_PER_WATT", 3.75);
  define("SSR_REBATE", 1);
  define('TAX_CREDIT', 1);
  define('REC_CREDIT', 1);



  $monthly_savings = $_GET["savings"];
?>
<!DOCTYPE html>
<html>
<head>
   <meta charset='utf-8'> 
	<title>Custom Solar: Solar Calculator</title>
	<script type="text/javascript" src="js/jqmin.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-responsive.min.css">
	<link rel="stylesheet" type="text/css" href="calculator.css">
  <script type="text/javascript" src="js/calculator.js"></script>
</head>
<body>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">Custom Solar</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li><a href="index.html">Home</a></li>
              <li><a href="#about">About</a></li>
              <li><a href="#contact">Contact</a></li>
              <li class="active"><a href="calculator.html">Solar Calculator</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">
    	<h1>Solar Calculator</h1>
    	<fieldset>
      <form action="calculate.php" method="get">
        <label>How much of your electric bill would you like to eliminate on a monthly basis:<br>
          <input type="number" name="savings">
          <input type="submit" value="Calculate">
        </label>

      </form>
    </fieldset>
    <?php
    calculate($monthly_savings);
    ?>
      <table id="payback">
        <tr>
          <th>Year</th>
          <th>KwH Cost(with <?= constant("ANNUAL_INFLATION_RATE") ?>% inflation)</th>
          <th>Savings per month</th>
          <th>Savings per year</th>
          <th>REC payment per month</th>
          <th>Total Savings</th>
        </tr>
      </table>

    </div> <!-- /container -->
</body>
</html>

<?php

// Do the base calculations for the table
function calculate($monthly_savings) {
  // Get constants
  $kwh_fixed = constant("KWH_FIXED");
  $system_size_constant = constant("SYS_DIVISOR");
  $watts_per_panel = constant("WATTS_PER_PANEL");

  # Convert savings per month
  $kw_per_month = ($monthly_savings / $kwh_fixed);
  $kw_yearly = $kw_per_month * 12; // yearly kwh usage
  $system_size = $kw_yearly / $system_size_constant / 100;
  $total_watts = $system_size * 1000;
  $num_panels = ceil($total_watts / $watts_per_panel); // round up for panels
  addCalculationsTable($system_size, $kw_per_month, $kw_yearly);
}


// Add the calculated info table
function addCalculationsTable($system_size, $kw_per_month, $kw_yearly) {
  ?>
  <table id="calculations">
    <!-- Section 1 -->
    <tr><th colspan="3">System Size and Production</th></tr>
    <tr><td>System Size</td>         <td>KwH per Month produced</td> <td>KwH Annual produced</td></tr>
    <tr><td><?= $system_size ?></td> <td><?= $kw_per_month ?></td>   <td><?= $kw_yearly ?></td></tr>
    <tr><td>Number of Panels</td><td><?= $num_panels ?></td></tr>




    <!-- Section 2 -->
    <tr><th colspan="2">Rebates</th></tr>
    <tr><td>Solar Rewards Rebate (current Xcel Rebate)</td>         <td>$</td></tr>
    <tr><td>REC Payment (Current Xcel - paid monthly to owner)</td> <td>$/month</td></tr>
    <tr><td>Federal Energy Tax Credit</td>                          <td>$</td></tr>

    <tr><th colspan="2">System</th></tr>
    <tr><td>Gross Cost of System</td>                     <td>$</td></tr>
    <tr><td>Solar Rewards Rebate</td>                     <td>$</td></tr>
    <tr><td>After Rebate Cost</td>                        <td>$</td></tr>
    <tr><td>Federal Tax Credit (30% of gross)</td>        <td>$</td></tr>
    <tr><td>Subtotal</td>                                 <td>$</td></tr>
    <tr><td>REC credit (10-20 years -- paid monthly)</td> <td>$</td></tr>
    <tr><td>Net Cost of Solar System</td>                 <td>$</td></tr>
  </table>
  <?php
}

// Adds the payback rows to the output table
function addPaybackTable() {

}


// adds a row to the table, if the system is fully paid off, then that row will be marked with
// the balance-point ID
function addRow($year, $kwh_cost, $savings_per_month, $savings_per_year, $rec_per_month, $total_savings, $subtotal) {
  ?> 
  <tr <?php balanceMet($total_savings, $subtotal); ?>>
    <td><?= $year ?></td>
    <td><?= $kwh_cost ?></td>
    <td><?= $savings_per_month ?></td>
    <td><?= $savings_per_year ?></td>
    <td><?= $total_savings ?></td>
  </tr>
  <?php
}

// adds the special id if the system is all paid back
function balanceMet($total_savings, $subtotal) {
  if ($total_savings >= $subtotal) {
    echo('id="balance-point"');
  }
}
?>