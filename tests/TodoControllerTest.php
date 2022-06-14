<?php

namespace App\Tests;

use App\Repository\TodoRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TodoControllerTest extends WebTestCase
{
    public function testIndexPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('a', 'CRÉER UNE TODO');
    }

    public function testCreationPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $client->clickLink('CRÉER UNE TODO');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1:contains("Ajoute une todo à ta liste !")');
    }

    public function testDetailsPage(): void
    {
        $id = 1;
        $client = static::createClient();
        $client->request('GET', "/todo/$id");
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h5', 'Détails');
    }

    public function testTodoCreation()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/create');
        $buttonCrawlerNode = $crawler->selectButton('AJOUTER UNE TODO');
        $form = $buttonCrawlerNode->form();
        $form['todo_form[title]'] = 'Test';
        $form['todo_form[description]'] = 'Symfony rocks!';
        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals(1, $crawler->filter('div.alert-success:contains("La Todo a bien été créée !")')->count());
    }

    public function testEditStateTodo() {
        $id = 1;
        $client = static::createClient();
        $todoRepository = static::getContainer()->get(TodoRepository::class);
        $todo = $todoRepository->find($id);
        $client->xmlHttpRequest('POST', "/edit/state/$id", ['state' => 'Completed']);
        $this->assertEquals('Completed', $todo->getState());
    }
}
