<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $users = [];

        //create 10 users
        for ($i=0; $i < 10; $i++) { 
            $user = new User();
            $user->setUsername($faker->userName)
                ->setPassword($this->encoder->hashPassword($user, 'password'))
                ->setRoles(['ROLE_USER']);
            $manager->persist($user);     
            $users[] = $user;    
        }

        //create a new post
        for ($i=0; $i < 20; $i++) { 
            $post = new Post();

            $title = $faker->sentence();
            $content = '' . join('', $faker->paragraphs(3)) . '';
            $image = $faker->imageUrl(1280, 490);
  
            $post->setTitle($title)
                ->setContent($content)
                ->setImage($image)
                ->setAuthor($faker->randomElement($users));
                
            $manager->persist($post); 

            //create comments
            for ($j=0; $j < mt_rand(0, 10); $j++) { 
                $comment = new Comment();
                $comment->setKommentar($faker->sentence())
                    ->setName($faker->userName)
                    ->setMail($faker->email)
                    ->setUrl($faker->url)
                    ->setPost($post);
                $manager->persist($comment); 
            }
        }

        $manager->flush();
    }
}
