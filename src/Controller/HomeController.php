<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteFormType;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class HomeController extends AbstractController
{
    /**
 * @Route("/", name="home")
 */
    public function index(UrlGeneratorInterface $router, Request $request,
                          EntityManagerInterface $em,
//                          Security $security,
                          NoteRepository $noteRepository)
    {
        $noteList = $noteRepository->findAll();
        $note = new Note();
        $noteForm = $this->createForm(NoteFormType::class, $note);
        $noteForm->handleRequest($request);

        if ($noteForm->isSubmitted() && $noteForm->isValid()) {
            $note = $noteForm->getData();
            $note->getValue($request->request->get("value"));

            $em->persist($note);
            $em->flush();

            $this->addFlash('success', 'Note enregistrée');
            return $this->redirectToRoute('home');
        }


        return $this->render('Notation.html.twig', [
            'note_form' => $noteForm->createView(),
            'note_list'=>$noteList]);
    }

    /**
     * @Route("/note-delete/{id}", name="delete_note")
     */
    public function deleteNote(UrlGeneratorInterface $router, Request $request,
                          EntityManagerInterface $em,
//                          Security $security,
                          NoteRepository $noteRepository)
    {

        $id = $request->get('id');
        $note_curr = $noteRepository->findOneBy(["id" => $id]);
        $em->remove($note_curr);
        $em->flush();
        $this->addFlash('success', 'votre note/commentaire a bien était supprimée.');
        return $this->redirectToRoute('home');
    }
}
