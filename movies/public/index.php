<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require 'helpers/apiHelpers.php';

$app = new \Slim\App;
$container = $app->getContainer();

$container['db'] = function($c) {
    $database = $user = $password = "sakila";
    $host = "mysql";
    
    return new PDO("mysql:host={$host};dbname={$database};charset=utf8", $user, $password);
};

// Returns list of all movies
// Example: http://localhost:3000/movies
$app->get('/movies', function (Request $request, Response $response, array $args) {
    $db = $this->get('db');

    $sql = "SELECT film_id, title, description, release_year, rating FROM sakila.film";
    $movies = getMovies($sql, $db);

    return $response->withJson($movies);
});


// Req 1.1 : The user should be able to search the movies by title
// Example: http://localhost:3000/movies/CELEBRITY%20HORN
$app->get('/movies/{movietitle}', function (Request $request, Response $response, array $args) {
    $db = $this->get('db');

    $sql = "SELECT * FROM sakila.film WHERE title='{$args['movietitle']}'";
    $movie = getMovie($sql, $db);

    return $response->withJson($movie);
});


// Req 1.2 : The user should be able to filter the movies by rating
// Example: http://localhost:3000/movies/rating/PG
$app->get('/movies/rating/{rating}', function (Request $request, Response $response, array $args) {
    $db = $this->get('db');

    $sql = "SELECT * FROM sakila.film WHERE rating='{$args['rating']}'";
    $movies = getMovies($sql, $db);

    return $response->withJson($movies);
});

// Req 1.3 : The user should be able to filter the movies by category
// Example: http://localhost:3000/movies/category/action
$app->get('/movies/category/{categoryname}', function (Request $request, Response $response, array $args) {
    $db = $this->get('db');

    $sql =  getCatagorySql($args['categoryname']);
    $movies = getMovies($sql, $db);

    return $response->withJson($movies);
});

// Req 2 : Movie details for each movie
// Example: http://localhost:3000/movies/details/CELEBRITY%20HORN
$app->get('/movies/details/{movietitle}', function (Request $request, Response $response, array $args) {
    $db = $this->get('db');

    $sql = getMovieDetailSql($args['movietitle']);
    $movieDetail = getMovieDetail($sql, $db);

    $sql = getAgentsSql($movieDetail['title']);
    $movieDetail['actors'] = getActors($sql, $db);

    return $response->withJson($movieDetail);
});

// Req 3 : A list of actors in a movie
// Example: http://localhost:3000/movies/actors/CELEBRITY%20HORN
$app->get('/movies/actors/{movietitle}', function (Request $request, Response $response, array $args) {
    $db = $this->get('db');

    $sql = getAgentsSql($args['movietitle']);
    $actors = getActors($sql, $db);

    return $response->withJson($actors);
});

$app->run();

