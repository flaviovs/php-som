PHP-SOM - A lightweight Self-organizing Map implementation in PHP
=================================================================

Self-organizing maps (SOMs), sometimes called *Kohonen networks*, are
a special kind of neural networks. Unlike neuron-based backpropagation
networks, which need supervised learning (you need to feed your
features **plus** the correspondent outcome to allow the net to
"learn" something), SOMs learn in an unsupervised manner, which means
that you do not need to feed to the network the outcome that will want
it to learn. In fact, you may not even know the outcome -- the network
will "discover" the possibilities itself.

For more information about SOMs, see the following resources:

* http://en.wikipedia.org/wiki/Self-organizing_map

* http://www.ai-junkie.com/ann/som/som1.html

* http://www.willamette.edu/~gorr/classes/cs449/Unsupervised/SOM.html


How to use
----------

Using this library is straightforward. There are basically two classes
that you need to care about:

* `SOM` (in file `som.php`) - Objects of this class represent
  self-organizing maps. This is the only file you need to include in
  your application to actually *use* a SOM.

* `SOMTrainer` (`trainer.php`) - This class is used to create *trainer
  objects*. Trainer objects are used to train a SOM so that it can do
  *classification* later.

The basic workflow is:

### Training ###

    require_once('som.php');
    require_once('trainer.php');

    // Create a SOM with a 20x20 surface for feature vectors that hold 5
    // elements
    $som = new SOM(20, 20, 5);
    $trainer = new SOMTrainer($som);
    // Let our feature vectors elements be numbers between 0 and 1
    // (inclusive). We can also use an array the size of our feature to
    // set the minimum or maximum value for each element of input
    // vectors individually.
    $trainer->setLimits(0, 1);

    // Let's train our network
    foreach ($mydata_vectors as $vector)
		// NB: $vector is a 5-elements feature vector array that holds
		// the data we want to classify
		$trainer->train($vector);

	// Now save the SOM
	file_put_contents('som.dat', serialize($som));

### Using the network ###

	require_once('som.php');

	$data = file_get_contents('som.dat');

	$som = unserialize($data);

	// Get best matching unit (BMU) -- the vector that best match the
	// input vector, that's it
	$vec = $som->getBMU($input_vector);
	print_r($vec);

	// Get coordinates of the BMU
	list ($x, $y) = $som->getBMUCoords($input_vector)
	print "Best match is at ($x, $y)\n";


Customizing the SOMs
--------------------

You can customize the SOM and the trainer by simply subclassing the
classes above. Subclasses may override many functions and parameters
used to generate the network, such as the initialization algorithm,
the learning rate decay, the neighborhood function, etc. Please see
the files `som.php` and `trainer.php` for more information.


Examples
--------
Ses the examples in the `examples/` folder.



Bugs?
-----

Check the issue queue on the
[PHP-SOM page on GitHub](https://github.com/flaviovs/php-som/issues)
to see if it was not fixed yet. If not, please open a new issue.


Copyright
---------

Copyright (C) 2007-2015 Flávio Veloso

For licensing details, see the file `LICENSE`.
