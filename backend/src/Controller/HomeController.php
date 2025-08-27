<?php

namespace App\Controller;

use App\Repository\StoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(StoryRepository $storyRepository): Response
    {
        $stories = $storyRepository->findBy(['status' => 'published'], ['createdAt' => 'DESC']);
        $topStory = $storyRepository->findTopStoryOfCurrentMonth();
        return $this->render('home/index.html.twig', [
            'stories' => $stories,
            'topStory' => $topStory,
        ]);
    }
}
