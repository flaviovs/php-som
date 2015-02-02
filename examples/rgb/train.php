<?php

require('rgbtrainer.php');

define('COLORS_PER_BUCKET', 2);

$db = new ColorDatabase();
print "Loading color database...\n";
$db->load();
$som_size = floor(sqrt(sizeof($db->colors) / COLORS_PER_BUCKET));

print "Our map will be ${som_size}x$som_size large\n";

$som = new RGBSOM($som_size, $som_size, 3);
$trainer = new RGBTrainer($som);

print "Training. Please wait...\n";
if ($som_size > 100)
  print "(This may take a while...)\n";
$trainer->trainAll();

print "Now classifying colors...\n";
$color_grid = array_fill(0, $som->width, array_fill(0, $som->height, array()));
foreach ($db->colors as $color => $rgb)
{
  $coord = $som->getBMUCoord($rgb);
  $color_grid[$coord[0]][$coord[1]][$color] = $db->colors[$color];
}

print "Generating similarity map image...\n";
$map = new SOMSimilarityMap($som);
$map->update();
$img = $map->getImage(max(1, floor(175 / $som->width)));
imagepng($img, 'map.png');
imagedestroy($img);
print "Done. Wrote map.png.\n";

print "Generating RGB surface image...\n";
$img = $som->getImage(max(1, floor(175 / $som->width)));
imagepng($img, 'rgb.png');
imagedestroy($img);
print "Done. Wrote rgb.png.\n";

$fd = fopen('som.dat', 'w');
fwrite($fd, serialize($som));
fclose($fd);

$fd = fopen('grid.dat', 'w');
fwrite($fd, serialize($color_grid));
fclose($fd);
