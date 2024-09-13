<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Candidates;

class RegistrationControllerTest extends WebTestCase
{
    private const REGISTER_URL = '/register';
    private const LOGIN_URL = '/login';
    private const REGISTER_BUTTON = 'register';
    private const TEST_PASSWORD = 'password123';
    private const TEST_PHONE_NUMBER = '+48123456789';

    public function testRegister()
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $candidateRepository = $entityManager->getRepository(Candidates::class);

        $initialCount = count($candidateRepository->findAll());

        $crawler = $client->request('GET', self::REGISTER_URL);

        $randomNumber = rand(1000, 9999);
        $testEmail = 'jan.kowalski' . $randomNumber . '@example.com';

        $form = $crawler->selectButton(self::REGISTER_BUTTON)->form([
            'candidate[firstName]' => 'Jan',
            'candidate[lastName]' => 'Kowalski',
            'candidate[email]' => $testEmail,
            'candidate[phoneNumber]' => self::TEST_PHONE_NUMBER,
            'candidate[password]' => self::TEST_PASSWORD,
        ]);

        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect(self::LOGIN_URL));

        $newCount = count($candidateRepository->findAll());
        $this->assertEquals($initialCount + 1, $newCount);

        $newCandidate = $candidateRepository->findOneBy(['email' => $testEmail]);
        $this->assertNotNull($newCandidate);
        $this->assertNotEquals(self::TEST_PASSWORD, $newCandidate->getPassword());
    }
}