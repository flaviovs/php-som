PHP-SOM - A lightweight Self-organizing Map implementation in PHP
=================================================================

Self-organizing maps (SOMs), sometimes called *Kohonen networks*, are
a special kind of neural networks. Unlike the neuron-based
backpropagation networks, which need supervised learning (you need to
feed your features **plus** the correspondent outcome to allow the net
"learn"), SOMs learning are unsupervised, which means that you do not
need to feed to the network the outcome for it to learn. Actually, you
may not even know the outcome -- the network will "discover" the
possibilities itself.

For more information about, SOMs, see the following resources:

* http://en.wikipedia.org/wiki/Self-organizing_map

* http://www.ai-junkie.com/ann/som/som1.html

* http://www.willamette.edu/~gorr/classes/cs449/Unsupervised/SOM.html


How to use
----------

Using this library is straightforward. There are basically two classes
that you need to care about.

* SOM (som.php) - Objects of this class represent self-organizing
  maps. This is the only file you need to include in your application
  to actually *use* PHP-SOM

* SOMTrainer - This class is used to create *trainer* objects. Trainer
  object are used to train a SOM so that it can do classification
  latter.

In a nutshell, the basic workflow is:

### Training ###

   require_once('som.php');
   require_once('trainer.php');

   // Create a SOM with a 20x20 surface for feature vector that hold 5
   // elements
   $som = new SOM(20, 20, 5);
   $trainer = new SOMTrainer($som);
   // Let our features vectors elements be numbers between 0 and 1
   // (inclusive). We can also use a feature vector-sized array to
   // set the minimum or maximum value for each element of input vectors
   $trainer->setLimits(0, 1);

   // Let's train our network
   foreach ($mydata_vectors as $vector)
	   // NB: $vector is a 5-elements feature vector array that holds
	   // the data we want to classify
	   $trainer->train(array($vector);


   // Now save the SOM
   file_put_contents('som.dat', serialize($som));

### Using the network ###

   require_once('som.php');

   $data = file_get_contents('som.dat');

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
used to generate the network, such as initialization algorithm,
learning rate decay, neighborhood function, etc. Please see the
`som.php` and `trainer.php` files for more information.


Examples
--------
Se the examples in the `examples/` folder.



Bugs?
-----

Check the issue queue on the
[PHP-SOM page on GitHub](https://github.com/flaviovs/php-som/issues)
to see if it was not fixed yet. If not, please open a new issue.


Copyright
---------

Copyright (C) 2007-2015 Flávio Veloso

For licensing details, see the file LICENSE.
