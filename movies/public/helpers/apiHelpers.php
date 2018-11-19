<?php
function getMovieDetailSql($filmTitle){
    return "SELECT f.film_id, f.title, f.description, f.release_year, l.name as language, ol.name as original_language,
                 f.rental_duration, f.rental_rate, f.length, f.replacement_cost, f.rating, f.special_features, f.last_update,
                 c.name as category, ft.title as title_text, ft.description as description_text
             FROM sakila.film f
                 LEFT JOIN sakila.language l ON f.language_id=l.language_id
                 LEFT JOIN sakila.language ol ON f.original_language_id=ol.language_id
                 LEFT JOIN sakila.film_category fc ON f.film_id=fc.film_id
                 LEFT JOIN sakila.category c ON c.category_id=fc.category_id
                 LEFT JOIN sakila.film_text ft ON ft.film_id=f.film_id
             WHERE f.title='{$filmTitle}'";
}

function getAgentsSql($filmTitle){
    return "SELECT *
            FROM sakila.film f
                LEFT JOIN sakila.film_actor fa ON f.film_id=fa.film_id
                LEFT JOIN sakila.actor a ON a.actor_id=fa.actor_id
            WHERE f.title='{$filmTitle}'";
}

function getCatagorySql($categoryname){
    return "SELECT *
    		FROM sakila.film f
    			LEFT JOIN sakila.film_category fc ON f.film_id=fc.film_id
    			LEFT JOIN sakila.category c ON c.category_id=fc.category_id
    		WHERE c.name='{$categoryname}'";
}


function getMovie($sql, $db){
    $stmt = $db->prepare($sql);
    $stmt->execute();

    return buildMovie($stmt->fetch(PDO::FETCH_ASSOC));
}

function getMovieDetail($sql, $db){
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $movieDetail = $stmt->fetch(PDO::FETCH_ASSOC);
    $movieDetail['special_features']=explode( ",", $movieDetail['special_features']);

    return $movieDetail;
}

function getMovies($sql, $db){
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $movies = array();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        array_push($movies, buildMovie($row));
    }

    return $movies;
}

function getActors($sql, $db){
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $actors = array();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        array_push($actors, buildActor($row));
    }

    return $actors;
}


function buildMovie($row){
    $movie = array(
        "film_id" => $row["film_id"],
        "title"   => $row["title"],
        "description" => $row["description"],
        "release_year" => $row["release_year"],
        "rating" => $row["rating"]
    );
    return $movie;
}

function buildActor($row){
    $actor = array(
        "actor_id" => $row["actor_id"],
        "first_name"   => $row["first_name"],
        "last_name" => $row["last_name"],
        "last_update" => $row["last_update"]
    );
    return $actor;
}

?>