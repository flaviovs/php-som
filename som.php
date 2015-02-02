<?php

class SOM {
  public $width, $height, $inputs, $grid = array();

  public function __construct($width, $height, $inputs)
  {
    $this->width = $width;
    $this->height = $height;
    $this->inputs = $inputs;

    $vec = array_fill(0, $inputs, NULL);
    $row = array_fill(0, $width, $vec);
    $this->grid = array_fill(0, $height, $row);
  }

  public function distance(array $vec1, array $vec2)
  {
    for ($i = 0, $dist = 0; $i < $this->inputs; $i++)
    {
      $delta = $vec1[$i] - $vec2[$i];
      $dist += ($delta * $delta);
    }

    return sqrt($dist);
  }

  public function getBMUCoord(array $vec)
  {
    $shortest = INF;

    $bmu_x = $bmu_y = NULL;

    for ($i = 0; $i < $this->width; $i++)
    {
      for ($j = 0; $j < $this->height; $j++)
      {
        $distance = $this->distance($this->grid[$i][$j], $vec);
        if ( $distance < $shortest )
        {
          $shortest = $distance;
          $bmu_x = $i;
          $bmu_y = $j;
        }
      }
    }

    return array($bmu_x, $bmu_y);
  }


  public function getBMU(array $vec)
  {
    list($x, $y) = $this->getBMUCoord($vec);
    return $this->grid[$x][$y];
  }


  public function __toString()
  {
    $out = '';
    for ($i = 0; $i < $this->width; $i++)
    {
      for ($j = 0; $j < $this->height; $j++)
      {
        if ($j)
          $out .= ' ';
        $out .= '[';
        for ($k = 0; $k < $this->inputs; $k++)
        {
          if ($k)
            $out .= ' ';
          $out .= $this->grid[$i][$j][$k];
        }
        $out .= ']';
      }
      $out .= "\n";
    }
    return $out;
  }

}
