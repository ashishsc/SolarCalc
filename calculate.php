<?php
  // Ashish Chandwani
  // Solar Calculator
  // TODO: Convert to object oriented
  // Notes: security features must be enabled

  // Adjustable Constants
  #################################################
  define("KWH_FIXED", 0.14);                      #
  define("SYS_DIVISOR", 1440); // Used version    #
  define("WATTS_PER_PANEL", 260);                 #
  define("ANNUAL_INFLATION_RATE", 8.25); // %     #
  define("COST_PER_WATT", 3.75);                  #
  define("SRR_REBATE", 1); #                      #
  define('TAX_CREDIT', 0.3);                      #
  define('REC_CREDIT', 0.09);                     #
  define('REC_YEARS', 10);                        #
  #################################################


  $monthly_savings = $_GET["savings"];
  include 'top.php';
?>

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
  $rec_yearly = $rec_monthly * 12;
  $subtotal = 
      addCalculationsTable($system_size, $kw_monthly,
      $kw_yearly, $num_panels, $gross_sys_cost,
      $rec_monthly, $total_watts);
      $net_cost = constant('REC_YEARS') * $rec_monthly * 12 * -1 + $subtotal;
  addPaybackTable($rec_yearly, $kw_monthly, $subtotal, $net_cost);
}


// Perform secondary calculations and add the the calculations formatted to
// 2 decimal places by default
function addCalculationsTable($system_size, $kw_monthly, $kw_yearly,
    $num_panels, $gross_sys_cost, $rec_monthly, $total_watts) {

  $fed_tax_credit_percent = constant("TAX_CREDIT") * 100;
  $solar_rewards_rebate =  $total_watts * constant("SRR_REBATE") * -1;
  $after_rebate = $gross_sys_cost + $solar_rewards_rebate;
  $tax_credit = constant("TAX_CREDIT") * $after_rebate * -1;
  $subtotal = $after_rebate + $tax_credit;
  $rec_after_years = constant('REC_YEARS') * $rec_monthly * 12 * -1;
  $net_cost = $subtotal + $rec_after_years;
  ?>
  <table id="calculations">
    <!-- Section 1 -->
    <tr><th colspan="3">System Size and Production</th></tr>
    <tr><td>System Size</td>         <td>KwH per Month produced</td> <td>KwH Annual produced</td></tr>
    <tr><td><?= number_format($system_size, 2) ?></td> <td><?= number_format($kw_monthly, 2) ?></td>   <td><?= number_format($kw_yearly, 2) ?></td></tr>
    <tr><td>Number of Panels</td><td><?= $num_panels ?></td></tr>

    <!-- Section 2 -->
    <tr><th colspan="2">Rebates</th></tr>
    <tr><td>Solar Rewards Rebate</td>                               <td>$<?= constant("SRR_REBATE") ?> per watt</td></tr>
    <tr><td>REC Payment</td>                                        <td>$<?=number_format($rec_monthly, 2)?> per month</td></tr>
    <tr><td>Federal Energy Tax Credit</td>                          <td>$<?=number_format($tax_credit, 2) ?></td></tr>

    <tr><th colspan="2">System</th></tr>
    <tr><td>Gross Cost of System</td>                                               <td>$<?= number_format($gross_sys_cost, 2) ?></td></tr>
    <tr><td>Solar Rewards Rebate</td>                                               <td>$<?= number_format($solar_rewards_rebate, 2) ?></td></tr>
    <tr><td>After Rebate Cost</td>                                                  <td>$<?= number_format($after_rebate, 2) ?></td></tr>
    <tr><td>Federal Tax Credit (<?=$fed_tax_credit_percent?>% of gross)</td>        <td>$<?= number_format($tax_credit, 2) ?></td></tr>
    <tr><td>Subtotal</td>                                                           <td>$<?= number_format($subtotal, 2) ?></td></tr>
    <tr><td>REC credit (<?= constant('REC_YEARS') ?> years paid monthly)</td>       <td>$<?= number_format($rec_after_years, 2) ?></td></tr>
    <tr><td>Net Cost of Solar System</td>                                           <td>$<?= number_format($net_cost, 2) ?></td></tr>
  </table>
  <?php
  return $subtotal;
}

// Adds the payback rows to the output table
function addPaybackTable($rec_yearly, $kw_monthly, $subtotal, $net_cost) {
  $kwh_cost = constant('KWH_FIXED') * 1.04;
  $inflation_rate = constant("ANNUAL_INFLATION_RATE") / 100;
  $savings_per_month = $kw_monthly * $inflation_rate; // Note: Not sure if this is correct 
  $savings_per_year = $savings_per_month * 12;
  $total_savings = $rec_yearly + $savings_per_year;

  ?>
  <table id="payback">
      <caption>
        Solar System payback spreadsheet (Payback based on the period of warranty for most equipment - 25 years.
        Note: Solar systems can last for many years beyond.)
     </caption>
    <tr>
      <th>Year</th>
      <th>KwH Cost(with <?= constant("ANNUAL_INFLATION_RATE") ?>% inflation)</th>
      <th>Savings per month</th>
      <th>Savings per year</th>
      <th>REC payment per month</th>
      <th>Total Savings</th>
    </tr>
    <?php

    //Fence-post
    addRow(1, $kwh_cost, $savings_per_month, $savings_per_year, $rec_yearly, $total_savings, $subtotal); 
    
    // Add the next of 25 rows
    for ($year = 2; $year <= 25 ; $year++) { 
      $kwh_cost += ($kwh_cost * $inflation_rate);
      $savings_per_month = $kwh_cost * $kw_monthly;
      $savings_per_year = $savings_per_month * 12;
      if ($year > 10) {
        $rec_yearly = 0;
      }
      $total_savings = $rec_yearly + $savings_per_year;
      addRow($year, $kwh_cost, $savings_per_month, $savings_per_year, $rec_yearly, $total_savings, $subtotal); 
    }
    $total_saved = $total_savings - $net_cost;
    ?>
    <tr><th colspan="5">Amount saved after payback of Solar System</th><td>$<?= number_format($total_saved,2) ?></td></tr>
  </table>
  <?php
}


// adds a row to the payback table, if the system is fully paid off, then that row will be marked with
// the balance-point ID
// returns whether the balance point was found
function addRow($year, $kwh_cost, $savings_per_month, $savings_per_year, $rec_yearly, $total_savings, $subtotal) {
  
  $balance_point_found = False;
  ?> 
  <tr <?php $balance_point_found = balanceMet($total_savings, $subtotal); ?>>
    <td><?= $year ?></td>
    <td><?= number_format($kwh_cost, 3) ?></td>
    <td><?= number_format($savings_per_month, 2) ?></td>
    <td><?= number_format($savings_per_year, 2) ?></td>
    <td><?= number_format($rec_yearly, 2) ?></td>
    <td><?= number_format($total_savings, 2) ?></td>
  </tr>
  <?php
  return $balance_point_found;
}

// adds the special id if the system is all paid back
// Returns true if $total_savings == $subtotal
function balanceMet($total_savings, $subtotal) {
  if ($total_savings >= $subtotal) {
    echo('class="paid-back"');
    return True;
  } else {
    return False;
  }
}
?>