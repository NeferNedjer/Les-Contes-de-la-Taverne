<?php

namespace App\Controller;

use App\Entity\Story;
use App\Form\StoryType;
use App\Repository\StoryRepository;
use App\Entity\StoryLike;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/story')]
final class StoryController extends AbstractController
{
    #[Route(name: 'app_story_index', methods: ['GET'])]
    public function index(StoryRepository $storyRepository): Response
    {
        return $this->render('story/index.html.twig', [
            'stories' => $storyRepository->findAll(),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/new', name: 'app_story_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $story = new Story();
        $form = $this->createForm(StoryType::class, $story);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // attach current user as author
            $story->setUser($this->getUser());
            // generate slug from title
            $slug = strtolower($slugger->slug((string) $story->getTitle()));
            $story->setSlug($slug);
            // set creation timestamp
            if ($story->getCreatedAt() === null) {
                $story->setCreatedAt(new \DateTimeImmutable());
            }
            $entityManager->persist($story);
            $entityManager->flush();

            return $this->redirectToRoute('app_story_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('story/new.html.twig', [
            'story' => $story,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_story_show', methods: ['GET'])]
    public function show(Story $story): Response
    {
        return $this->render('story/show.html.twig', [
            'story' => $story,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_story_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Story $story, EntityManagerInterface $entityManager): Response
    {
        if ($story->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(StoryType::class, $story);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // update timestamp
            $story->setUpdateAt(new \DateTimeImmutable());
            $entityManager->flush();

            return $this->redirectToRoute('app_story_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('story/edit.html.twig', [
            'story' => $story,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_story_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Story $story, EntityManagerInterface $entityManager): Response
    {
        if ($story->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        if ($this->isCsrfTokenValid('delete'.$story->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($story);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_story_index', [], Response::HTTP_SEE_OTHER);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/{id}/like', name: 'app_story_like', methods: ['POST'])]
    public function like(Request $request, Story $story, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        // CSRF basic protection (optional add hidden input in form)
        if (!$this->isCsrfTokenValid('like'.$story->getId(), $request->getPayload()->getString('_token', ''))) {
            throw $this->createAccessDeniedException();
        }

        // Check if like exists
        $likeRepo = $entityManager->getRepository(StoryLike::class);
        $existing = $likeRepo->findOneBy(['story' => $story, 'user' => $user]);
        if ($existing) {
            // Unlike
            $entityManager->remove($existing);
            $entityManager->flush();
        } else {
            $like = new StoryLike();
            $like->setStory($story);
            $like->setUser($user);
            $like->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($like);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_story_show', ['id' => $story->getId()]);
    }
}
