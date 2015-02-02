Introduction
============

This is an example of a self-organizing map network used to classify
colors in the RGB color-space.


How to run
==========

1. Copy/link this directory to a folder that can be acessed by a
   PHP-enabled webserver.


2. If you obtained the library using GIT, then you will need to update
   the required submodules. Run the following commands on the top-level
   directory of your working tree:

   $ git submodule init
   $ git submodule update


3. Run train.php **on the command line** to generate the network
   databases and images. I.e.:

   $ php train.php


4. Open your web browser and navigate to the URL of this folder.
