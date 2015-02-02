<?php

require('../../som.php');
require('../../trainer.php');


class RGBSOM extends SOM {

  public function getImage($scale = NULL)
  {
    if (!$scale)
      $scale = max(1, floor(600 / $this->width));

    $img = imagecreatetruecolor($this->width * $scale,
                                $this->height * $scale);

    $bg = imagecolorallocate($img, 0, 0, 0);

    for ($i = 0; $i < $this->width; $i++)
    {
      for ($j = 0; $j < $this->height; $j++)
      {
        list($r, $g, $b) = $this->grid[$i][$j];
        $color = imagecolorexact($img, $r, $g, $b);
        if ($color == -1)
          $color = imagecolorallocate($img, $r, $g, $b);
        $x = $i * $scale;
        $y = $j * $scale;
        imagefilledrectangle($img, $x, $y, $x + $scale, $y + $scale, $color);
      }
    }
    return $img;
  }
}


class ColorDatabase {
  public $colors = array();

  public function load()
  {
    $fd = fopen('ntc.js', 'r');
    if (!$fd)
      exit();

    $this->colors = array();
    while (($line = fgets($fd)) !== FALSE)
    {
      if (!preg_match('/^\["([\dA-F][\dA-F])([\dA-F][\dA-F])([\dA-F][\dA-F])", "(.+)"\],/', $line, $matches))
        continue;
      $this->colors[$matches[4]] = array(hexdec($matches[1]),
                                   hexdec($matches[2]),
                                   hexdec($matches[3]));
    }

    fclose($fd);
  }
}

class RGBTrainer extends SOMTrainer {
  public function __construct(SOM $som)
  {
    parent::__construct($som);
    $this->setLimits(0, 255);
  }

  public function trainAll($times = 1)
  {
    for ($i = 0; $i < 5000; $i++)
      $this->train(array(mt_rand(0, 1) * 255,
                         mt_rand(0, 1) * 255,
                         mt_rand(0, 1) * 255));
  }

  public function initialize()
  {
    $this->initializeRandom();
  }
}
