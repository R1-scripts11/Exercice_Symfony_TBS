<?php 
// tests/Entity/ProductTest.php

namespace App\Tests\Entity;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testGetSetLabel()
    {
        // Nouvelle instance de Product
        $product = new Product();
        // Définir le label
        $product->setLabel('Test Product');
        // Vérifier si getLabel renvoie BIEN la valeur
        $this->assertEquals('Test Product', $product->getLabel());
    }

    public function testGetId()
    {
        //Nouvelle instance de Product
        $product = new Product();
        //getId devrait retourner null car l'id est généré automatiquement
        $this->assertNull($product->getId());
    }
}
