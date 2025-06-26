<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private DecoderInterface $decoder,
        private FileSystem $fileSystem,
        private string $rootDir
    ) {}

    public function load(ObjectManager $manager): void
    {
        $data = $this->decoder->decode(
            $this->fileSystem->readFile($this->rootDir.'/mock_users.json'),
            'json'
        );

        foreach ($data as $user) {
            $newUser = new User();
            $newUser->setUsername($user['username']);
            $newUser->setFirstname($user['firstname']);
            $newUser->setLastname($user['lastname']);
            $newUser->setEmail($user['email']);
            $manager->persist($newUser);
        }

        $manager->flush();
    }
}
