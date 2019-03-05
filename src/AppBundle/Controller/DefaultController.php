<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use AppBundle\Entity\Contact;
use AppBundle\Form\ContactType;


class DefaultController extends Controller
{

    /**
     * display the list of contacts
     * @Route("/", name="home")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $contacts = $this->getDoctrine()->getRepository(Contact::class)
            ->findAll();
        return $this->render('address/index.html.twig',
            array('contacts' => $contacts));
    }

    /**
     * create new contact
     * @Route("/create", name="create_contact", methods={"GET", "POST"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $newContact = new Contact();
        $form = $this->createForm(ContactType::class, $newContact);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newContact);
            $em->flush();
            $this->addFlash(
                'success',
                'Contact added successfully.'
            );
            return $this->redirect('/');
        }

        return $this->render('address/create.html.twig',
            array('form' => $form->createView()));
    }

    /**
     * display the form to edit contact
     * @Route("/edit/{contact}", name="edit_contact", methods={"GET", "POST"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \AppBundle\Entity\Contact                 $contact
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Contact $contact)
    {
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('address/edit.html.twig', [
            'form'    => $form->createView()
        ]);
    }

    /**
     * @param $id
     * @Route("/delete/{id}", name="delete_contact", methods={"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $contact = $this->getDoctrine()
            ->getRepository(Contact::class)
            ->find($id);

        if (empty($contact)) {
            $this->addFlash('error', 'Contact not found');
            return $this->redirectToRoute('home');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($contact);
        $em->flush();

        $this->addFlash('success', 'Contact was removed successfully');
        return $this->redirectToRoute('home');
    }
}
