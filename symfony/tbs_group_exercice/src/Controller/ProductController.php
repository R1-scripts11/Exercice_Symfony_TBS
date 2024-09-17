<?php
// src/Controller/ProductController.php
namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    // GET /product/{id} - Récupérer un produit par son ID
    #[Route('/product/{id}', name: 'get_product', methods: ['GET'])]
    public function getProduct(int $id, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->find($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($product, Response::HTTP_OK);
    }
  // GET /product - Récupérer tous les produits et afficher un formulaire pour en ajouter
  #[Route('/product', name: 'get_all_products', methods: ['GET'])]
  public function getAllProducts(ProductRepository $productRepository): Response
  {
      // Fetch all products
      $products = $productRepository->findAll();

      // Start building the HTML output
      $html = '<!DOCTYPE html>
      <html>
      <head>
          <title>Liste des Produits</title>
      </head>
      <body>
          <h1>Liste des Produits</h1>';

      // Check if there are products
      if (empty($products)) {
          $html .= '<p>Aucun produit trouvé.</p>';
      } else {
          $html .= '<ul>';
          // Loop through the products and create list items
          foreach ($products as $product) {
              $html .= '<li>ID: ' . htmlspecialchars($product->getId()) . ' - ' . htmlspecialchars($product->getLabel()) . '</li>';
          }
          $html .= '</ul>';
      }

      // Add a form to create a new product
      $html .= '<h2>Ajouter un Nouveau Produit</h2>
                <form action="/product" method="post">
                    <label for="label">Nom du produit:</label>
                    <input type="text" id="label" name="label" required><br><br>
                    <button type="submit">Ajouter Produit</button>
                </form>';
      $html .= '</body>
      </html>';

      // Return the HTML response
      return new Response($html);
  }

    // POST /product - Créer un nouveau produit
    #[Route('/product', name: 'create_product', methods: ['POST'])]
    public function createProduct(Request $request, EntityManagerInterface $em): Response
    {
       // Get data from the form
       $data = $request->request->all();

        $product = new Product();
        $product->setLabel($data['label']);

        $em->persist($product);
        $em->flush();

        // Redirect to the /product page after creation
        return $this->redirectToRoute('get_all_products');
    }

    // PUT /product/{id} - Mettre à jour un produit
    #[Route('/product/{id}', name: 'update_product', methods: ['PUT'])]
    public function updateProduct(int $id, Request $request, EntityManagerInterface $em, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->find($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['label'])) {
            $product->setLabel($data['label']);
        }

        $em->flush();

        return $this->json($product, Response::HTTP_OK);
    }

    // DELETE /product/{id} - Supprimer un produit
    #[Route('/product/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function deleteProduct(int $id, EntityManagerInterface $em, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->find($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($product);
        $em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
