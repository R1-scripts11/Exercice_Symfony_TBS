<?php
// tests/Entity/ContactTest.php

namespace App\Tests\Entity;

use App\Entity\Contact;
use PHPUnit\Framework\TestCase;

class ContactTest extends TestCase
{
    public function testContactEntity()
    {
        // Créer une instance de Contact
        $contact = new Contact();

        // Tester le setter et le getter pour Name
        $contact->setName('Doe');
        $this->assertEquals('Doe', $contact->getName());

        // Tester le setter et le getter pour Firstname
        $contact->setFirstname('John');
        $this->assertEquals('John', $contact->getFirstname());

        // Vérifier les valeurs par défaut
        $this->assertNull($contact->getId()); // ID devrait être null avant la persistance
    }
}
