<!doctype html>
<html>
<head><title>PHP-SOM RGB Example</title>
<script src="http://code.jquery.com/jquery-1.11.2.min.js"></script>
<script src="jquery-minicolors/jquery.minicolors.min.js"></script>
<script src="rgb.js"></script>
<link rel="stylesheet" href="rgb.css" />
</head>
<body>

<?php if (file_exists('som.dat')): ?>
<h1>RGB test</h1>

<?php
function print_color_options()
{
  for ($i = 0; $i < 256; $i++)
    print "<option>$i</option>";
}
?>

<div id="left">
   <h2>Map surface</h2>
   <img src="rgb.png" />
   <h2>Similarity map</h2>
   <img src="map.png" />
</div>

<div id="right">
<div id="form">
<form>
   Pick a color:<br /><input type="text" size="7" class="color">
   <div id="demo"></div>
</form>
</div>

<div id="result"></div>
</div>
</div>

<?php else: ?>
<p>Something is missing. Please read <a href="README.txt">the documentation for this example</a>.</p>
<p>Refresh this page when ready.</p>
<?php endif ?>
</body>
</html>

