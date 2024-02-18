<?php

namespace App\Test\Controller;

use App\Entity\Code;
use App\Repository\CodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CodeControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private CodeRepository $repository;
    private string $path = '/code/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Code::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Code index');

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
            'code[code]' => 'Testing',
            'code[reduction]' => 'Testing',
            'code[date_debut]' => 'Testing',
            'code[date_fin]' => 'Testing',
        ]);

        self::assertResponseRedirects('/code/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Code();
        $fixture->setCode('My Title');
        $fixture->setReduction('My Title');
        $fixture->setDate_debut('My Title');
        $fixture->setDate_fin('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Code');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Code();
        $fixture->setCode('My Title');
        $fixture->setReduction('My Title');
        $fixture->setDate_debut('My Title');
        $fixture->setDate_fin('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'code[code]' => 'Something New',
            'code[reduction]' => 'Something New',
            'code[date_debut]' => 'Something New',
            'code[date_fin]' => 'Something New',
        ]);

        self::assertResponseRedirects('/code/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getCode());
        self::assertSame('Something New', $fixture[0]->getReduction());
        self::assertSame('Something New', $fixture[0]->getDate_debut());
        self::assertSame('Something New', $fixture[0]->getDate_fin());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Code();
        $fixture->setCode('My Title');
        $fixture->setReduction('My Title');
        $fixture->setDate_debut('My Title');
        $fixture->setDate_fin('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/code/');
    }
}
