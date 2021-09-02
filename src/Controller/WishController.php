<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use App\Service\Censurator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WishController extends AbstractController
{
    /**
     * @Route("/wish", name="wish")
     */
    public function list(WishRepository $wishRepository): Response
    {
        $w = $wishRepository->findAll(['isPublished' => true], ['dateCreated' => 'DESC']);

        return $this->render('wish/index.html.twig', [
            'wishes' => $w
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/wish/new", name="wish_new")
     */
    public function addWish(Request $request, EntityManagerInterface $em, Censurator $censurator): Response
    {        
        $wish = new Wish();

        $connectedUser = $this->getUser()->getPseudo();
        $wish->setAuthor($connectedUser);

        $form = $this->createForm(WishType::class, $wish);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            
            $wish->setDateCreated(new \DateTime());
            $wish->setIsPublished(true);

            $cleanse = $censurator->purify($wish->getDescription());
            $wish->setDescription($cleanse);

            $em->persist($wish);
            $em->flush();

            $this->addFlash('success', 'Another beautiful wish to fulfill !');
            
            return $this->redirectToRoute('wish_detail', ['id' => $wish->getId()]);
        }

        return $this->render('wish/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/wish/{id}/edit", name="wish_edit")
     */
    public function editWish(Request $request, EntityManagerInterface $em, Wish $wish, Censurator $censurator): Response
    {
        $form = $this->createForm(WishType::class, $wish);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            
            
            $wish = $form->getData();

            $cleanse = $censurator->purify($wish->getDescription());
            $wish->setDescription($cleanse);

            $em->persist($wish);
            $em->flush();

            $this->addFlash('success', 'Wish updated');
            
            return $this->redirectToRoute('wish_detail', ['id' => $wish->getId()]);
        }   
        
        return $this->render('wish/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("wish/search/{keyword}", name="wish_search")
     */
    public function searchTitle(Request $request, WishRepository $wishRepository): Response
    {
        $keyword = $request->get('keyword');
        $rs = $wishRepository->findWishByWord($keyword);

        return $this->render('wish/search.html.twig', [
            'wishes' => $rs
        ]);
    }

    /**
     * @Route("/wish/{id}/show", name="wish_detail")
     */
    public function showWish(WishRepository $wishRepository, int $id): Response
    {
        $w = $wishRepository->find($id);

        return $this->render('wish/show.html.twig', [
            'wish' => $w
        ]);
    }

    /**
     * @Route("/wish/{id}/delete", name="wish_delete")
     */
    public function deleteWish(Wish $wish, EntityManagerInterface $em): RedirectResponse
    {
        $em->remove($wish);
        $em->flush();

        return $this->redirectToRoute('wish');
    }

}
