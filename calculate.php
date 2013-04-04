<?php
  define("KWH_FIXED", 0.14);
  define("SYS_DIVISOR", 1440);
  define("WATTS_PER_PANEL", 260);

  $monthly_savings = $_GET["savings"];
  calculate($monthly_savings);
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

    </div> <!-- /container -->
</body>
</html>

<?php
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
  

}
?>