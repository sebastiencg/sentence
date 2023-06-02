<?php

namespace App\Test\Controller;

use App\Entity\Sentence;
use App\Repository\SentenceRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SentenceControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private SentenceRepository $repository;
    private string $path = '/sentence/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Sentence::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Sentence index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'sentence[sentence]' => 'Testing',
            'sentence[createdAt]' => 'Testing',
            'sentence[author]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sentence/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Sentence();
        $fixture->setSentence('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setAuthor('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Sentence');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Sentence();
        $fixture->setSentence('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setAuthor('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'sentence[sentence]' => 'Something New',
            'sentence[createdAt]' => 'Something New',
            'sentence[author]' => 'Something New',
        ]);

        self::assertResponseRedirects('/sentence/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getSentence());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getAuthor());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Sentence();
        $fixture->setSentence('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setAuthor('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/sentence/');
    }
}
