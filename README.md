# Symfony-Arangodb

This is a simple "Hello World" example of using ArangoDB in Symfony2.

![Screenshot](http://www.arangodb.org/wp-content/uploads/2013/03/movies_demo.jpg)

This demo uses

* Symfony 2.4
* [ArangoDB 2](https://www.arangodb.org/download)
* [MopArangoDbBundle](https://github.com/m0ppers/MopArangoDbBundle)
* [arangodb-php](https://github.com/triagens/arangodb-php)


## Scope
The "app" demonstrates how to create, update and delete documents using the simple query API.
Additionally it has an example for ArangoDB's query language AQL (search for topics in the movies database).

## Installation
First install ArangoDB and make sure it is running. You'll find plenty of information how to do this in the ArangoDB
manual.

The installation of the demo app is pretty straight forward (the usual way for Symfony 2 apps which use composer).

1. set up a virtual domain [Here](http://stackoverflow.com/questions/8962054/symfony-2-on-virtual-hosts) is an
example for a vhost config for Apache)
2. clone the repository
3. cd symfony-arangodb
4. get [composer](http://getcomposer.org/download/)
5. Run "php composer.phar install" - this will fetch all required 3rd party libs from the internet and set everything up

## Running the app
Finally open the app in your browser. Let's say your virtual domain is called hellosymfony.dev, the app will pop up
using http://hellosymfony.dev/ or hellosymfony.dev/app_dev.php to run it in dev mode.

## Where to start digging in the code
There are settings for arangodb in app/config_dev.yml - look for "mop_arango_db".
Check out the documentation for MopArangoDbBundle for details on this (note: this bundle also offers integration with
the FOS UserBundle).

You'll find all the demo code in src/Triagens/ArangodbBundle/Controller/DefaultController.php





