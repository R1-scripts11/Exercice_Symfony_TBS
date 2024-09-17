<?php 
// src/Controller/SubscriptionController.php
namespace App\Controller;

use App\Entity\Subscription;
use App\Repository\ContactRepository;
use App\Repository\ProductRepository;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    // GET / - Récupérer toutes les souscriptions et les afficher dans un tableau HTML
    #[Route('/', name: 'get_all_subscriptions', methods: ['GET'])]
    public function getAllSubscriptions(SubscriptionRepository $subscriptionRepository, ContactRepository $contactRepository, ProductRepository $productRepository): Response
    {
        $subscriptions = $subscriptionRepository->findAll();
        $contacts = $contactRepository->findAll();
        $products = $productRepository->findAll();

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <title>Liste des Souscriptions</title>
            <style>
                .table-container {
                    display: flex;
                    gap: 20px;
                }
                .table-container table {
                    border-collapse: collapse;
                    width: 100%;
                }
                .table-container th, .table-container td {
                    border: 1px solid #ddd;
                    padding: 8px;
                }
                .table-container th {
                    background-color: #f2f2f2;
                }
            </style>
        </head>
        <body>
            <h1>Liste des Souscriptions</h1>';

        if (empty($subscriptions)) {
            $html .= '<p>Aucune souscription trouvée.</p>';
        } else {
            $html .= '<div class="table-container">
                        <div>
                            <h2>Table des Souscriptions</h2>
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Contact</th>
                                        <th>Produit</th>
                                        <th>Date de début</th>
                                        <th>Date de fin</th>
                                    </tr>
                                </thead>
                                <tbody>';

            foreach ($subscriptions as $subscription) {
                $contactName = $subscription->getContact() ? $subscription->getContact()->getName() : 'N/A';
                $productName = $subscription->getProduct() ? $subscription->getProduct()->getId() : 'N/A';
                $beginDate = $subscription->getBeginDate() ? $subscription->getBeginDate()->format('Y-m-d') : 'N/A';
                $endDate = $subscription->getEndDate() ? $subscription->getEndDate()->format('Y-m-d') : 'N/A';

                $html .= '<tr>
                            <td>' . htmlspecialchars($subscription->getId()) . '</td>
                            <td>' . htmlspecialchars($contactName) . '</td>
                            <td>' . htmlspecialchars($productName) . '</td>
                            <td>' . htmlspecialchars($beginDate) . '</td>
                            <td>' . htmlspecialchars($endDate) . '</td>
                                   <td>
                            <form action="/subscription/' . htmlspecialchars($subscription->getId()) . '" method="post" onsubmit="return confirm(\'Êtes-vous sûr de vouloir supprimer cette souscription ?\');">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit">Supprimer</button>
                            </form>
                        </td>
                        </tr>';
            }

            $html .= '</tbody>
                    </table>
                </div>';
        }

        // Add tables for contacts and products
        $html .= '<div>
                    <h2>Table des Contacts</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                            </tr>
                        </thead>
                        <tbody>';

        foreach ($contacts as $contact) {
            $html .= '<tr>
                        <td>' . htmlspecialchars($contact->getId()) . '</td>
                    </tr>';
        }

        $html .= '</tbody>
                </table>
                 <a href="/contact/">
                        <button type="button">Ajouter Contact</button>
                </a>
            </div>';

        $html .= '<div>
                    <h2>Table des Produits</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                            </tr>
                        </thead>
                        <tbody>';

        foreach ($products as $product) {
            $html .= '<tr>
                        <td>' . htmlspecialchars($product->getId()) . '</td>
                    </tr>';
        }

        $html .= '</tbody>
                </table>
                                 <a href="/product/">
                        <button type="button">Ajouter produit</button>
                </a>
            </div>
        </div>';

        $html .= '<h2>Ajouter une Nouvelle Souscription</h2>
                <form action="/subscription" method="post">
                    <label for="contact_id">Contact ID:</label>
                    <input type="text" id="contact_id" name="contact_id" required><br><br>
                    
                    <label for="product_id">Product ID:</label>
                    <input type="text" id="product_id" name="product_id" required><br><br>
                    
                    <label for="beginDate">Date de début:</label>
                    <input type="date" id="beginDate" name="beginDate" required><br><br>
                    
                    <label for="endDate">Date de fin:</label>
                    <input type="date" id="endDate" name="endDate" required><br><br>
                    
                    <button type="submit">Ajouter Souscription</button>
                </form>';

        $html .= '</body>
        </html>';

        return new Response($html);
    }

    #[Route('/subscription/{idContact}', name: 'get_subscriptions_by_contact', methods: ['GET'])]
    public function getSubscriptionsByContact(int $idContact, SubscriptionRepository $subscriptionRepository): JsonResponse
    {
        $subscriptions = $subscriptionRepository->findBy(['contact' => $idContact]);

        if (!$subscriptions) {
            return $this->json(['error' => 'No subscriptions found for this contact'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($subscriptions, Response::HTTP_OK);
    }

// POST 
#[Route('/subscription', name: 'create_subscription', methods: ['POST'])]
public function createSubscription(
    Request $request, 
    EntityManagerInterface $em, 
    ContactRepository $contactRepository, 
    ProductRepository $productRepository
): Response {
    $data = $request->request->all();
    if (!isset($data['contact_id'], $data['product_id'], $data['beginDate'], $data['endDate'])) {
        return $this->json(['error' => 'Invalid input data. Please provide contact_id, product_id, beginDate, and endDate.'], Response::HTTP_BAD_REQUEST);
    }

    $contact = $contactRepository->find($data['contact_id']);
    $product = $productRepository->find($data['product_id']);

    if (!$contact || !$product) {
        return $this->json(['error' => 'Invalid contact or product'], Response::HTTP_BAD_REQUEST);
    }

    $subscription = new Subscription();
    $subscription->setContact($contact);
    $subscription->setProduct($product);
    $subscription->setBeginDate(new \DateTime($data['beginDate']));
    $subscription->setEndDate(new \DateTime($data['endDate']));

    $em->persist($subscription);
    $em->flush();

    return $this->redirectToRoute('get_all_subscriptions');
}

    // PUT /subscription/{idSubscription} 
    #[Route('/subscription/{idSubscription}', name: 'update_subscription', methods: ['PUT'])]
    public function updateSubscription(
        int $idSubscription, 
        Request $request, 
        EntityManagerInterface $em, 
        SubscriptionRepository $subscriptionRepository
    ): JsonResponse {
        $subscription = $subscriptionRepository->find($idSubscription);

        if (!$subscription) {
            return $this->json(['error' => 'Subscription not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['beginDate'])) {
            $subscription->setBeginDate(new \DateTime($data['beginDate']));
        }
        if (isset($data['endDate'])) {
            $subscription->setEndDate(new \DateTime($data['endDate']));
        }

        $em->flush();

        return $this->json($subscription, Response::HTTP_OK);
    }

    // DELETE /subscription/{idSubscription} - Supprimer une souscription
    #[Route('/subscription/{idSubscription}', name: 'delete_subscription', methods: ['POST','DELETE'])]
    public function deleteSubscription(
        int $idSubscription, 
        EntityManagerInterface $em, 
        SubscriptionRepository $subscriptionRepository
    ): Response {
        $subscription = $subscriptionRepository->find($idSubscription);

        if (!$subscription) {
            return $this->json(['error' => 'Subscription not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($subscription);
        $em->flush();

        return $this->redirectToRoute('get_all_subscriptions');
    }
}
