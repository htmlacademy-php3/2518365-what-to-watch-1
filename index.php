<?php

require_once __DIR__ . '/vendor/autoload.php';
use WhatToWatch\Services\MovieService\MovieRepository;

$client = new \GuzzleHttp\Client();
$repository = new MovieRepository($client);

$movies = $repository->findMovieById('tt39105467134');
