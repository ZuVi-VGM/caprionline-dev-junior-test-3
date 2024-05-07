<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

use App\Repository\MovieGenreRepository;

#[Route('/movies', name: 'movies_')]
class MoviesController extends AbstractController
{
    public function __construct(
        private MovieRepository $movieRepository,
        //Temporary
        private MovieGenreRepository $movieGenreRepository,
        private SerializerInterface $serializer
    ) {}

    // Get film list
    #[Route('', methods: ['GET'], name: 'list')]
    public function list(): JsonResponse
    {
        $movies = $this->movieRepository->findAll();
        $data = $this->serializer->serialize($movies, "json", ["groups" => "default"]);

        return new JsonResponse($data, json: true);
    }

    // Sort films
    #[Route('/{orderBy}', methods: ['GET'], requirements: ['orderBy'=>'^(?=.*[a-zA-Z])[a-zA-Z0-9]+$'], name: 'sort')]
    public function sort(string $orderBy): JsonResponse
    {
        $movies = [];

        if($orderBy == 'release')
            $movies = $this->movieRepository->findBy([], ['releaseDate' => 'DESC']);
        elseif ($orderBy == 'rating')
            $movies = $this->movieRepository->findBy([], ['rating' => 'DESC']);

        $data = $this->serializer->serialize($movies, "json", ["groups" => "default"]);

        return new JsonResponse($data, json: true);
    }

    // Get list of movies for given genre
    #[Route('/{genre}', methods: ['GET'], requirements: ['genre' => '\d+'], name: 'genre_list')]
    public function listByGenre(int $genre): JsonResponse
    {
        $movies = [];

        $movieForGenre = $this->movieGenreRepository->findByGenreIdJoinedToMovie($genre);
        
        foreach($movieForGenre as $movieData)
            $movies[] = $movieData->getMovie();

        $data = $this->serializer->serialize($movies, "json", ["groups" => "default"]);

        return new JsonResponse($data, json:true);
    }

    // Get sorted list of movies for given genre
    #[Route('/{genre}/{orderBy}', methods: ['GET'], name: 'genre_sort')]
    public function sortByGenre(int $genre, string $orderBy): JsonResponse
    {
        //TODO: maybe a better error handling
        $movies = [];
        $movieForGenre = [];

        if($orderBy == 'release')
            $movieForGenre = $this->movieGenreRepository->findByGenreIdJoinedToMovie($genre, orderBy: ['c.releaseDate' => 'DESC']);
        elseif ($orderBy == 'rating')
            $movieForGenre = $this->movieGenreRepository->findByGenreIdJoinedToMovie($genre, orderBy: ['c.rating' => 'DESC']);

        foreach($movieForGenre as $movieData)
            $movies[] = $movieData->getMovie();

        $data = $this->serializer->serialize($movies, "json", ["groups" => "default"]);

        return new JsonResponse($data, json:true);
    }
}
