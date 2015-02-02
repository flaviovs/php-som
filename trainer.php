<?php

class SOMTrainer {
  protected $som, $min_vec, $max_vec, $iteration;

  public function __construct(SOM $som)
  {
    $this->som = $som;
    $this->min_vec = array_fill(0, $som->inputs, -0.5);
    $this->max_vec = array_fill(0, $som->inputs, 0.5);
  }

  protected function initializeRandom()
  {
    $vec_delta = array_fill(0, $this->som->inputs, 0);
    for ($k = 0; $k < $this->som->inputs; $k++)
      $vec_delta[$k] = $this->max_vec[$k] - $this->min_vec[$k];

    $rand_max = mt_getrandmax();

    for ($i = 0; $i < $this->som->width; $i++)
    {
      for ($j = 0; $j < $this->som->height; $j++)
      {
        for ($k = 0; $k < $this->som->inputs; $k++)
          $this->som->grid[$i][$j][$k] = $this->min_vec[$k] +
            ((mt_rand() / $rand_max) * $vec_delta[$k]);
      }
    }
  }

  protected function initializeGradient()
  {
    $vec_delta = array_fill(0, $this->som->inputs, 0);
    for ($k = 0; $k < $this->som->inputs; $k++)
      $vec_delta[$k] = $this->max_vec[$k] - $this->min_vec[$k];

    $width = $this->som->width - 1;
    $height = $this->som->height - 1;

    // FIXME: loop only over half the grid -- gradient vector grid is
    // symetrical
    for ($i = 0; $i < $this->som->width; $i++)
    {
      for ($j = 0; $j < $this->som->height; $j++)
      {
        for ($k = 0; $k < $this->som->inputs; $k++)
        {
          $this->som->grid[$i][$j][$k] = $this->min_vec[$k] +
            ($vec_delta[$k] * ($i / $width) * ($j / $height));
        }
      }
    }
  }

  protected function initializeMiddle()
  {
    $avec = array_fill(0, $this->som->inputs, NULL);

    for ($k = 0; $k < $this->som->inputs; $k++)
      $avec[$k] = $this->min_vec[$k]
        + (($this->max_vec[$k] - $this->min_vec[$k]) / 2);

    for ($i = 0; $i < $this->som->width; $i++)
    {
      for ($j = 0; $j < $this->som->height; $j++)
      {
        for ($k = 0; $k < $this->som->inputs; $k++)
        {
          $this->som->grid[$i][$j][$k] = $avec[$k];
        }
      }
    }
  }


  public function initialize()
  {
    $this->initializeRandom();
  }


  protected function learningRate()
  {
    return max(0.1, 1 - ($this->iteration * 0.001));
  }


  protected function neighborhoodRate($distance)
  {
    if ($distance < 0 || $distance > 1)
      throw new InvalidArgumentException("Distance must be between 0 and 1");
//    return cos($distance * 1.6) * $this->learningRate();
    $radius = $this->searchRadius();
    return exp(-6.666666667 * ($distance / $radius)) * $this->LearningRate();

  }


  protected function searchRadius()
  {
    return max(4, max($this->som->width, $this->som->height) * 2 / 3);
  }


  protected function move($i, $j, $dvec, $rate)
  {
    for ($k = 0; $k < $this->som->inputs; $k++)
      $this->som->grid[$i][$j][$k] += ($dvec[$k]
                                       - $this->som->grid[$i][$j][$k]) * $rate;
  }


  public function setLimits($min, $max)
  {
    if (!is_array($min))
      $min = array_fill(0, $this->som->inputs, $min);
    if (!is_array($max))
      $max = array_fill(0, $this->som->inputs, $max);

    $this->min_vec = $min;
    $this->max_vec = $max;
  }

  public function train(array $vec)
  {
    $this->iteration++;

    $grid = $this->som->grid;
    $radius = $this->searchRadius();

    $bmu_c = $this->som->getBMUCoord($vec);
    $bmu = $this->som->grid[$bmu_c[0]][$bmu_c[1]];

    $max_i = min($this->som->width, $bmu_c[0] + $radius);
    $max_j = min($this->som->height, $bmu_c[1] + $radius);

    for ($i = max(0, $bmu_c[0] - $radius); $i < $max_i; $i++)
    {
      for ($j = max(0, $bmu_c[1] - $radius); $j < $max_j; $j++)
      {
        if ($i == $bmu_c[0] && $j == $bmu_c[1])
          $this->move($i, $j,
                      $vec,
                      $this->neighborhoodRate(0));
        else
        {
          $di = $bmu_c[0] - $i;
          $dj = $bmu_c[1] - $j;
          $distance = sqrt(($di * $di) + ($dj * $dj));
          if ($distance < $radius)
            $this->move($i, $j,
                        $vec,
                        $this->neighborhoodRate($distance / $radius));
        }
      }
    }
  }
}


class SOMSimilarityMap {

  public $map, $max_x = 0, $max_y = 0, $min_x = 0, $min_y = 0;
  protected $som, $distance;

  public function __construct(SOM $som, $distance = 3) {
    if ($distance < 1)
      throw new InvalidArgumentException("Distance must be greater than or equal to 1");
    $this->som = $som;
    $this->distance = $distance;
  }

  public function update()
  {
    $this->map = array_fill(0, $this->som->width,
                            array_fill(0, $this->som->height, NULL));
    $distance = $this->distance;

    $max = 0;
    $min = INF;

    for ($i = 0; $i < $this->som->width; $i++)
    {
      for ($j = 0; $j < $this->som->height; $j++)
      {
        $total = $n = 0;
        for ($ii = -$distance; $ii <= $distance; $ii++)
        {
          for ($jj = -$distance; $jj <= $distance; $jj++)
          {
            if (($ii == 0 && $jj == 0)
                || sqrt(($ii * $ii) + ($jj * $jj)) > $distance)
              continue;

            $ui = $i + $ii;
            $uj = $j + $jj;

            if ($ui < 0 || $ui >= $this->som->width)
              continue;
            if ($uj < 0 || $uj >= $this->som->height)
              continue;

            $total += $this->som->distance($this->som->grid[$i][$j],
                                           $this->som->grid[$ui][$uj]);
            $n++;
          }
        }

        $total /= $n;

        if ($total > $max)
        {
          $max = $total;
          $this->max_x = $i;
          $this->max_y = $j;
        }
        else if ($total < $min)
        {
          $min = $total;
          $this->min_x = $i;
          $this->min_y = $j;
        }

        $this->map[$i][$j] = $total;
      }
    }

    $delta = $max - $min;

    for ($i = 0; $i < $this->som->width; $i++)
    {
      for ($j = 0; $j < $this->som->height; $j++)
      {
        $this->map[$i][$j] = 1 - (($this->map[$i][$j] - $min) / $delta);
      }
    }
  }


  public function __toString()
  {
    $out = "Maximum similarity at ($this->max_x, $this->max_y)\n"
      . "Minimum similarity at ($this->min_x, $this->min_y)\n";
    for ($i = 0; $i < $this->som->width; $i++)
    {
      if ($i)
        $out .= "\n";
      $out .= "[";
      for ($j = 0; $j < $this->som->height; $j++)
      {
        $out .= " " . $this->map[$i][$j];
      }
      $out .= " ]";
    }
    return $out;
  }


  public function getImage($scale = NULL)
  {
    if (!$scale)
      $scale = max(1, floor(600 / $this->som->width));

    $img = imagecreate($this->som->width * $scale,
                       $this->som->height * $scale);

    $bg = imagecolorallocate($img, 0, 0, 0);

    for ($i = 0; $i < $this->som->width; $i++)
    {
      for ($j = 0; $j < $this->som->height; $j++)
      {
        $r = 255 * $this->map[$i][$j];
        $g = intval($r / 3);
        $b = 255 * ((1 - $this->map[$i][$j]) / 2);

        $color = imagecolorexact($img, $r, $g, $b);
        if ($color == -1)
          $color = imagecolorallocate($img, $r, $g, $b);

        $x = $i * $scale;
        $y = $j * $scale;
        imagefilledrectangle($img, $x, $y, $x + $scale, $y + $scale, $color);
      }
    }

    $green = imagecolorexact($img, 0, 255, 0);
    if ($green == -1)
      $green = imagecolorallocate($img, 0, 255, 0);
    $x = $this->max_x * $scale;
    $y = $this->max_y * $scale;
    imagefilledrectangle($img, $x, $y, $x + $scale, $y + $scale, $green);

    $yellow = imagecolorexact($img, 255, 255, 0);
    if ($yellow == -1)
      $yellow = imagecolorallocate($img, 255, 255, 0);
    $x = $this->min_x * $scale;
    $y = $this->min_y * $scale;
    imagefilledrectangle($img, $x, $y, $x + $scale, $y + $scale, $yellow);

    return $img;
  }
}
