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
          <div id='calc-form'>
            $<input type="number" name="savings" value=<?= '"' . $monthly_savings . '"'?>>
            <input type="submit" value="Calculate" class='btn' id='submit-btn'>
          </div>
        </label>
      </form>
    </fieldset>