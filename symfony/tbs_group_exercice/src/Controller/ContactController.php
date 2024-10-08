<?php
// src/Controller/ContactController.php
namespace App\Controller;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    // GET /contact/{id}
    #[Route('/contact/{id}', name: 'get_contact', methods: ['GET'])]
    public function getContact(int $id, ContactRepository $contactRepository): JsonResponse
    {
        $contact = $contactRepository->find($id);

        if (!$contact) {
            return $this->json(['error' => 'Contact not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($contact, Response::HTTP_OK);
    }

    // GET /contact 
    #[Route('/contact', name: 'get_all_contacts', methods: ['GET'])]
    public function getAllContacts(ContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findAll();
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <title>Liste des Contacts</title>
        </head>
        <body>
            <h1>Liste des Contacts</h1>';

        if (empty($contacts)) {
            $html .= '<p>Aucun contact trouvé.</p>';
        } else {
            $html .= '<ul>';
            foreach ($contacts as $contact) {
                $html .= '<li>ID: ' . htmlspecialchars($contact->getId()) . ' - ' . htmlspecialchars($contact->getName()) . ' ' . htmlspecialchars($contact->getFirstname()) . '</li>';
            }
            $html .= '</ul>';
        }

        $html .= '<h2>Ajouter un Nouveau Contact</h2>
                  <form action="/contact" method="post">
                      <label for="name">Nom:</label>
                      <input type="text" id="name" name="name" required><br><br>
                      
                      <label for="firstname">Prénom:</label>
                      <input type="text" id="firstname" name="firstname" required><br><br>
                      
                      <button type="submit">Ajouter Contact</button>
                  </form>';

        $html .= '</body>
        </html>';
        return new Response($html);
    }

    // POST /contact - Créer un nouveau contact
    #[Route('/contact', name: 'create_contact', methods: ['POST'])]
    public function createContact(Request $request, EntityManagerInterface $em): Response
    {
        // data du form
        $data = $request->request->all();

        $contact = new Contact();
        $contact->setName($data['name']);
        $contact->setFirstname($data['firstname']);

        $em->persist($contact);
        $em->flush();
        return $this->redirectToRoute('get_all_contacts');
    }

    // PUT /contact/{id}
    #[Route('/contact/{id}', name: 'update_contact', methods: ['PUT'])]
    public function updateContact(int $id, Request $request, EntityManagerInterface $em, ContactRepository $contactRepository): JsonResponse
    {
        $contact = $contactRepository->find($id);

        if (!$contact) {
            return $this->json(['error' => 'Contact not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $contact->setName($data['name']);
        }
        if (isset($data['firstname'])) {
            $contact->setFirstname($data['firstname']);
        }

        $em->flush();

        return $this->json($contact, Response::HTTP_OK);
    }

    // DELETE /contact/{id}
    #[Route('/contact/{id}', name: 'delete_contact', methods: ['DELETE'])]
    public function deleteContact(int $id, EntityManagerInterface $em, ContactRepository $contactRepository): JsonResponse
    {
        $contact = $contactRepository->find($id);

        if (!$contact) {
            return $this->json(['error' => 'Contact not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($contact);
        $em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
