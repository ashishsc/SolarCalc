<?php
  // Ashish Chandwani
  // Solar Calculator
  // TODO: Convert to object oriented

  // Constant
  #################################################
  define("KWH_FIXED", 0.14);                      #
  define("SYS_DIVISOR", 1440); // Used version    #
  define("WATTS_PER_PANEL", 260);                 #
  define("ANNUAL_INFLATION_RATE", 8.25); // %     #
  define("COST_PER_WATT", 3.75);                  #
  define("SRR_REBATE", 1); #                      #
  define('TAX_CREDIT', 0.3);                      #
  define('REC_CREDIT', 0.09);                     #
  #################################################


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
    // Performs calculations and adds tables.
    calculate($monthly_savings);
    ?>


    </div> <!-- /container -->
</body>
</html>

<?php

// Functions as the main function
// Do the base calculations for the table
function calculate($monthly_savings) {
  // Get constants
  $watts_per_panel = constant("WATTS_PER_PANEL");

  # Convert savings per month
  $kw_monthly = ($monthly_savings / constant("KWH_FIXED"));
  $kw_yearly = $kw_monthly * 12; // yearly kwh usage
  $system_size = $kw_yearly / constant("SYS_DIVISOR"); // Note to PM: Dividing by 100 is wrong here
  $total_watts = $system_size * 1000;
  $num_panels = ceil($total_watts / $watts_per_panel); // round up for panels
  $gross_sys_cost = $system_size * 1000 * constant("COST_PER_WATT");
  // REC avg monthly = REC $/kwh * kwh annual
  $rec_monthly = constant("REC_CREDIT") * $kw_monthly;

  addCalculationsTable($system_size, $kw_monthly,
      $kw_yearly, $num_panels, $gross_sys_cost, $rec_monthly, $total_watts);
  addPaybackTable();
}


// Add the calculated info table
function addCalculationsTable($system_size, $kw_monthly, $kw_yearly,
    $num_panels, $gross_sys_cost, $rec_monthly, $total_watts) {

  $fed_tax_credit_percent = constant("TAX_CREDIT") * 100;
  $solar_rewards_rebate =  $total_watts * constant("SRR_REBATE") * -1;
  $after_rebate = $gross_sys_cost + $solar_rewards_rebate;
  $tax_credit = constant("TAX_CREDIT") * $after_rebate * -1;
  ?>
  <table id="calculations">
    <!-- Section 1 -->
    <tr><th colspan="3">System Size and Production</th></tr>
    <tr><td>System Size</td>         <td>KwH per Month produced</td> <td>KwH Annual produced</td></tr>
    <tr><td><?= $system_size ?></td> <td><?= $kw_monthly ?></td>   <td><?= $kw_yearly ?></td></tr>
    <tr><td>Number of Panels</td><td><?= $num_panels ?></td></tr>

    <!-- Section 2 -->
    <tr><th colspan="2">Rebates</th></tr>
    <tr><td>Solar Rewards Rebate</td>                               <td>$<?= constant("SRR_REBATE") ?> per watt</td></tr>
    <tr><td>REC Payment</td>                                        <td>$<?=$rec_monthly?> per month</td></tr>
    <tr><td>Federal Energy Tax Credit</td>                          <td>$<?=$tax_credit ?></td></tr>

    <tr><th colspan="2">System</th></tr>
    <tr><td>Gross Cost of System</td>                                               <td>$<?= $gross_sys_cost ?></td></tr>
    <tr><td>Solar Rewards Rebate</td>                                               <td>$<?= $solar_rewards_rebate ?></td></tr>
    <tr><td>After Rebate Cost</td>                                                  <td>$<?= $after_rebate ?></td></tr>
    <tr><td>Federal Tax Credit (<?=$fed_tax_credit_percent?>% of gross)</td>        <td>$<?= $tax_credit?>/td></tr>
    <tr><td>Subtotal</td>                                                           <td>$</td></tr>
    <tr><td>REC credit (10-20 years -- paid monthly)</td>                           <td>$</td></tr>
    <tr><td>Net Cost of Solar System</td>                                           <td>$</td></tr>
  </table>
  <?php
}

// Adds the payback rows to the output table
function addPaybackTable() {
  ?>

  <table id="payback">
      <caption>Solar System payback spreadsheet (Payback based on the period of warranty for most equipment - 25 years. Note: Solar systems can last for many years beyond.)</caption>
    <tr>
      <th>Year</th>
      <th>KwH Cost(with <?= constant("ANNUAL_INFLATION_RATE") ?>% inflation)</th>
      <th>Savings per month</th>
      <th>Savings per year</th>
      <th>REC payment per month</th>
      <th>Total Savings</th>
    </tr>
  </table>
  <?php
}


// adds a row to the payback table, if the system is fully paid off, then that row will be marked with
// the balance-point ID
function addRow($year, $kwh_cost, $savings_per_month, $savings_per_year, $rec_per_month, $total_savings, $subtotal) {
  
  $balance_point_found = False;
  ?> 
  <tr <?php $balance_point_found = balanceMet($total_savings, $subtotal); ?>>
    <td><?= $year ?></td>
    <td><?= $kwh_cost ?></td>
    <td><?= $savings_per_month ?></td>
    <td><?= $savings_per_year ?></td>
    <td><?= $rec_per_month ?></td>
    <td><?= $total_savings ?></td>
  </tr>
  <?php
  return $balance_point_found;
}

// adds the special id if the system is all paid back
// Returns true if $total_savings == $subtotal
function balanceMet($total_savings, $subtotal) {
  if ($total_savings >= $subtotal) {
    echo('id="balance-point"');
    return True;
  } else {
    return False;
  }
}
?>