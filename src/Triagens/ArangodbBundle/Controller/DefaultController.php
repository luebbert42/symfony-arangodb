<?php

namespace Triagens\ArangodbBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;

use triagens\ArangoDb\DocumentHandler as DocumentHandler;
use triagens\ArangoDb\CollectionHandler as CollectionHandler;
use triagens\ArangoDb\Statement as Statement;


use Triagens\ArangodbBundle\Form\AddmovieType;
use Triagens\ArangodbBundle\Form\EditmovieType;
use Triagens\ArangodbBundle\Form\SearchType;
use Triagens\ArangodbBundle\Entity\Movie;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction()
    {
        return new RedirectResponse($this->generateUrl('triagens_arangodb_default_list'));
    }

    /**
     * create collection & dummy documents
     * @Route("/setup")
     */
    public function setupAction()  {


        $connection = $this->get('mop_arangodb.default_connection');
        $documentHandler = new DocumentHandler($connection);
        $collectionHandler = new CollectionHandler($connection);
        // create new collection movies if it does not exist
        if (!$this->_collectionExists($this->_getCollectionName())) {
            $collectionHandler->create($this->_getCollectionName());
        }
        $movies = $this->_getDummyData();
        foreach ($movies as $m) {

            // create a new document
            $movie = new Movie();

            // use set method to set document properties
            $movie->set("genre", $m["genre"]);
            $movie->set("title", $m["title"]);
            $movie->set("released",  $m["released"]);
            $movie->set("topics", $m["topics"]);

            // send the document to the server
            $id = $documentHandler->save($this->_getCollectionName(), $movie);

            // print the document id created by the server
            $this->get('session')->getFlashBag()->add('success', "Added document with id {$id}.");
        }
        return new RedirectResponse($this->generateUrl('triagens_arangodb_default_list'));

    }


    /**
     * search for topic, this example demonstrates how to use ArangoDB's qery language AQL
     * @Route("/search")
     * @Template
     */
    public function searchAction() {

        $connection = $this->get('mop_arangodb.default_connection');

        $form = $this->createForm(new SearchType());

        $request = $this->get('request');
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $statement = new Statement($connection, array(
                    "query"     => '',
                    "count"     => true,
                    "batchSize" => 1000,
                    "_sanitize" => false,
                ));
                 ;
                $statement->setQuery("FOR m in `".$this->_getCollectionName()."` FILTER \"".$form->get("search")->getData()."\" in m.topics RETURN m");

                $cursor = $statement->execute();

                return $this->render(
                    'TriagensArangodbBundle:Default:list.html.twig',
                    array(
                        'movies' => $cursor->getAll(),
                        'desc' => " tagged with topic '".$form->get("search")->getData()."'"
                    )
                );
            }
        }

        return array("form" => $form->createView());
       /*


       */
    }

    /**
     * list movies, demonstrates the search for documents by example documents
     * @Route("/list/{genre}", defaults={"genre" = "sci-fi"})
     * @Template
     */
    public function listAction($genre) {
        $connection = $this->get('mop_arangodb.default_connection');
        $collectionHandler = new CollectionHandler($connection);


        // set everything up if collection is not there
        if (!$this->_collectionExists($this->_getCollectionName())) {
            return new RedirectResponse($this->generateUrl('triagens_arangodb_default_setup'));
        }

        // create example document which is used for the search by example afterwards
        $movie = new Movie();
        $movie->set("genre", $genre);

        $cursor = $collectionHandler->byExample($this->_getCollectionName(), $movie);
        return array(
            'movies' => $cursor->getAll(),
            'desc' => "in genre '".$genre."'"
        );
    }


    /**
     * delete movie from database
     * @Route("/delete/{id}")
     */
    public function deleteAction($id) {
        $connection = $this->get('mop_arangodb.default_connection');
        $documentHandler = new DocumentHandler($connection);
        if ($documentHandler->removeById($this->_getCollectionName(),$id)) {
            $this->get('session')->getFlashBag()->add('success', "removed document with id {$id}.");
        }
        return new RedirectResponse($this->generateUrl('triagens_arangodb_default_list'));
    }

    /**
     * edit existing movie
     * @Route("/edit/{id}")
     * @Template
     */
    public function editAction($id) {
        $connection = $this->get('mop_arangodb.default_connection');
        $documentHandler = new DocumentHandler($connection);

        // get existing data from database to preset the form
        $movie = $documentHandler->get($this->_getCollectionName(),$id);
        $presetData = $movie->getAll();
        // add id preset data to use it in the form as hidden field
        $presetData["id"] = $movie->getId();

        // create form with the data fetched from ArangoDB
        // Note the data transformer in Form/DataTransformer, it will implode/explode the topics array
        $form = $this->createForm(new EditmovieType(), $presetData);

        $request = $this->get('request');
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {

                // create Movie (extension of ArangodbDocument)
                $movie = new Movie();

                // populate with data fetched from form
                // note that transforming the topics list which is represented as string in the form
                // into an array is done
                // in Form/DataTransformer/TopicsToTopiclist/Transformer by Symfony
                $movie->populate($form->getData());

                // update in ArangoDB
                $documentHandler->updateById($this->_getCollectionName(),(int)$form->get('id')->getData(),$movie);

                $this->get('session')->getFlashBag()->add('success', "Movie was successfully updated.");
                return new RedirectResponse($this->generateUrl('triagens_arangodb_default_list'));
            }
        }
        return array(
            'id' => $id,
            'form' => $form->createView()
        );
    }

    /**
     * add movie
     * @Route("/add/")
     * @Template
     */
    public function addAction() {
        $connection = $this->get('mop_arangodb.default_connection');
        $documentHandler = new DocumentHandler($connection);

        $form = $this->createForm(new AddmovieType());
        $request = $this->get('request');
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                // create Movie (extension of ArangodbDocument)
                $movie = new Movie();

                // populate with data fetched from form
                // note that transforming the topics list which is represented as string in the form
                // into an array is done
                // in Form/DataTransformer/TopicsToTopiclist/Transformer by Symfony
                $movie->populate($form->getData());

                $documentHandler->save($this->_getCollectionName(),$movie);
                $this->get('session')->getFlashBag()->add('success', "New movie was successfully added.");
                return new RedirectResponse($this->generateUrl('triagens_arangodb_default_list'));
            }
        }
        return array(
            'form' => $form->createView()
        );
    }

    protected function _getCollectionName() {
        return "movies";
    }

    protected function _getDummyData()
    {
       $movies[] = array(
         "genre" => "sci-fi",
             "title" => "Star wars",
             "released" => 1983,
             "topics" => array("spaceship", "laser-games", "romance")
        );

        $movies[] = array(
            "genre" => "sci-fi",
            "title" => "Firefly",
            "released" => 2002,
            "topics" => array("spaceship", "cows", "rust")
        );

        $movies[] = array(
            "genre" => "sci-fi",
            "title" => "Star trek first contact",
            "released" => 1996,
            "topics" => array("spaceship", "laser-games", "borg", "romance")
        );

        $movies[] = array(
            "genre" => "trash",
            "title" => "Gone with the wind",
            "released" => 1939,
            "topics" => array("romance")
        );

        return $movies;
    }


    protected function _collectionExists($name) {
        if (!is_string($name)) {
            throw new \Symfony\Component\Routing\Exception\InvalidParameterException("invalid parameter - string expected");
        }

        $connection = $this->get('mop_arangodb.default_connection');
        $collectionHandler = new CollectionHandler($connection);

        $collections = $collectionHandler->getAllCollections(array("excludeSystem" => true, "keys" => "names"));
        if (in_array($name,array_keys($collections))) {
            return true;
        }
        else {
            return false;
        }

    }

}
