<?php

namespace App\Controller;

use App\Entity\Track;
use App\Repository\TrackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/tracks')]
class TrackController extends AbstractController
{
    #[Route('', name: 'api_tracks_index', methods: ['GET'])]
    public function index(TrackRepository $trackRepo): JsonResponse
    {
        $tracks = $trackRepo->findAll();

        $data = array_map(function (Track $track) {
            return [
                'id' => $track->getId(),
                'title' => $track->getTitle(),
                'artist' => $track->getArtist(),
                'duration' => $track->getDuration(),
                'isrc' => $track->getIsrc(),
            ];
        }, $tracks);

        return $this->json($data);
    }

    #[Route('', name: 'api_tracks_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $track = new Track();
        $track->setTitle($data['title'] ?? '');
        $track->setArtist($data['artist'] ?? '');
        $track->setDuration($data['duration'] ?? '');
        $track->setIsrc($data['isrc'] ?? '');

        $em->persist($track);
        $em->flush();

        return $this->json([
            'id' => $track->getId(),
            'title' => $track->getTitle(),
            'artist' => $track->getArtist(),
            'isrc' => $track->getIsrc(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_tracks_update', methods: ['PUT'])]
    public function update(int $id, Request $request, TrackRepository $trackRepo, EntityManagerInterface $em): JsonResponse
    {
        $track = $trackRepo->find($id);
        if (!$track) {
            return $this->json(['error' => 'Track not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $track->setTitle($data['title'] ?? $track->getTitle());
        $track->setArtist($data['artist'] ?? $track->getArtist());
        $track->setDuration($data['duration'] ?? $track->getDuration());
        $track->setIsrc($data['isrc'] ?? $track->getIsrc());

        $em->flush();

        return $this->json([
            'id' => $track->getId(),
            'title' => $track->getTitle(),
            'artist' => $track->getArtist(),
            'duration' => $track->getDuration(),
            'isrc' => $track->getIsrc(),
        ]);
    }

    #[Route('/{id}', name: 'api_tracks_delete', methods: ['DELETE'])]
    public function delete(int $id, TrackRepository $trackRepo, EntityManagerInterface $em): JsonResponse
    {
        $track = $trackRepo->find($id);
        if (!$track) {
            return $this->json(['error' => 'Track not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($track);
        $em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
