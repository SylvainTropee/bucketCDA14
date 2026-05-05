<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/wish', name: 'wish_')]
final class WishController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function list(WishRepository $wishRepository): Response
    {

        $wishes = $wishRepository->findBy(['isPublished' => true], ['dateCreated' => 'DESC']);

        return $this->render('wish/list.html.twig', [
            'wishes' => $wishes
        ]);
    }

    #[Route('/{id}', name: 'detail', requirements: ['id' => '[0-9]+'])]
    public function detail(int $id, WishRepository $wishRepository): Response
    {
        $wish = $wishRepository->find($id);

        if (!$wish) {
            throw $this->createNotFoundException('Ooops ! Not found');
        }

        return $this->render('wish/detail.html.twig', [
            'wish' => $wish
        ]);
    }


    #[Route('/create', name: 'create')]
    #[Route('/{id}/update', name: 'update', requirements: ['id' => '\d+'])]
    #[IsGranted("ROLE_USER")]
    public function createOrUpdate(
        WishRepository         $wishRepository,
        Request                $request,
        EntityManagerInterface $entityManager,
        int                    $id = null
    ): Response
    {
        $wish = new Wish();
        if ($id) {
            $wish = $wishRepository->find($id);
            if (!$wish) {
                throw $this->createNotFoundException("Wish not found !");
            }

            $this->denyAccessUnlessGranted("WISH_EDIT", $wish, "Ooops ! You can't update this wish");

        }
        $wishForm = $this->createForm(WishType::class, $wish);

        $wishForm->handleRequest($request);

        if ($wishForm->isSubmitted() && $wishForm->isValid()) {

            $wish->setUser($this->getUser());

            $entityManager->persist($wish);
            $entityManager->flush();
            $this->addFlash('success', 'Idea sucessfully ' . (!$id ? 'added !' : 'updated !'));
            return $this->redirectToRoute('wish_detail', ['id' => $wish->getId()]);
        }
        return $this->render('wish/' . ($id ? 'update' : 'create') . '.html.twig', [
            'wishForm' => $wishForm
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\d+'])]
    #[IsGranted("WISH_DELETE", 'wish')]
    public function delete(
        Wish                   $wish,
        WishRepository         $wishRepository,
        EntityManagerInterface $entityManager
    ): Response
    {

        // $wish = $wishRepository->find($id);
        //$this->isGranted("WISH_DELETE", $wish);

        $entityManager->remove($wish);
        $entityManager->flush();

        $this->addFlash('success', 'Idea sucessfully deleted !');
        return $this->redirectToRoute('wish_list');
    }


}
