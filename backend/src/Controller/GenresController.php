<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

use App\Repository\GenreRepository;

class GenresController extends AbstractController
{
    public function __construct(
        private GenreRepository $genreRepository,
        private SerializerInterface $serializer
    ) {}

    #[Route('/genres', name: 'app_genres')]
    public function list(): JsonResponse
    {
        $genres = $this->genreRepository->findAll();
        $data = $this->serializer->serialize($genres, "json");

        return new JsonResponse($data, json: true);
    }
}
