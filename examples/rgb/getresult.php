<?php

require('rgbtrainer.php');

$res = array('error' => NULL);

$dat = @file_get_contents('som.dat');
if (!$dat)
{
  $res['error'] = 'Could not open som.dat';
  print json_encode($res);
  exit();
}
$som = unserialize($dat);

$dat = @file_get_contents('grid.dat');
if (!$dat)
{
  $res['error'] = 'Could not open grid.dat';
  print json_encode($res);
  exit();
}

if (!preg_match('/^#([\dA-F][\dA-F])([\dA-F][\dA-F])([\dA-F][\dA-F])$/i',
                $_GET['rgb'],
                $matches))
{
  $res['error'] = 'Could not parse ' . $_GET['rgb'];
  print json_encode($res);
  exit();
}

$grid = unserialize($dat);

$rgb = array(hexdec($matches[1]),
             hexdec($matches[2]),
             hexdec($matches[3]));

list($x, $y) = $som->getBMUCoord($rgb);

$res['best'] = array();
foreach ($grid[$x][$y] as $color_name => $rgb)
  $res['best'][$color_name] = $rgb;

$res['other'] = array();
$si = max(0, $x - 1);
$sj = max(0, $y - 1);
$ei = min($som->width, $x + 1);
$ej = min($som->height, $y + 1);
for ($i = $si; $i < $ei; $i++)
{
  for ($j = $sj; $j < $ej; $j++)
  {
    if ($i == $x && $j == $y)
      continue;
    if (!$grid[$i][$j])
      continue;
    foreach ($grid[$i][$j] as $color_name => $rgb)
      $res['other'][$color_name] = $rgb;
  }
}

print json_encode($res);
