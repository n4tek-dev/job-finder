<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginControllerTest extends WebTestCase
{
    private const LOGIN_URL = '/login';
    private const LOGOUT_URL = '/logout';
    private const LOGIN_BUTTON = 'login';
    private const LOGIN_SUCCESS_BUTTON_TEXT = 'Zaloguj';
    private const ALERT_DANGER_CLASS = '.alert.alert-danger';

    public function testLoginPageIsSuccessful()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', self::LOGIN_URL);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('button', self::LOGIN_SUCCESS_BUTTON_TEXT);
    }

    public function testLoginWithInvalidCredentials()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', self::LOGIN_URL);

        $form = $crawler->selectButton(self::LOGIN_BUTTON)->form();
        $form['_username'] = 'invalid_user';
        $form['_password'] = 'invalid_password';

        $client->submit($form);

        $this->assertResponseRedirects(self::LOGIN_URL);
        $client->followRedirect();

        $this->assertSelectorExists(self::ALERT_DANGER_CLASS);
    }

    public function testLogout()
    {
        $client = static::createClient();
        $client->request('GET', self::LOGOUT_URL);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}