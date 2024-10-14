<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Post;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for($i=1; $i<=10; $i++) {
            $post = new Post();
            $post->setTitle('Post '.$i);
            $post->setText('This is the body of post '.$i);
            $manager->persist($post);
        }

        $manager->flush();
    }
}
