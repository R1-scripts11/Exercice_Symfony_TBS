<?php 
// tests/Entity/SubscriptionTest.php

namespace App\Tests\Entity;

use App\Entity\Subscription;
use App\Entity\Contact;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class SubscriptionTest extends TestCase
{
    public function testSubscriptionEntity()
    {
        // Créer les entités Contact et Product
        $contact = $this->createMock(Contact::class);
        $product = $this->createMock(Product::class);

        // Créer une instance de Subscription
        $subscription = new Subscription();

        // Tester le setter et le getter pour Contact
        $subscription->setContact($contact);
        $this->assertSame($contact, $subscription->getContact());

        // Tester le setter et le getter pour Product
        $subscription->setProduct($product);
        $this->assertSame($product, $subscription->getProduct());

        // Tester le setter et le getter pour BeginDate
        $beginDate = new \DateTime('2024-01-01');
        $subscription->setBeginDate($beginDate);
        $this->assertSame($beginDate, $subscription->getBeginDate());

        // Tester le setter et le getter pour EndDate
        $endDate = new \DateTime('2024-12-31');
        $subscription->setEndDate($endDate);
        $this->assertSame($endDate, $subscription->getEndDate());

        // Vérifier les types des getters
        $this->assertInstanceOf(Contact::class, $subscription->getContact());
        $this->assertInstanceOf(Product::class, $subscription->getProduct());
        $this->assertInstanceOf(\DateTimeInterface::class, $subscription->getBeginDate());
        $this->assertInstanceOf(\DateTimeInterface::class, $subscription->getEndDate());
    }
}
