<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }
    
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        for($j=1;$j<=mt_rand(4,6);$j++)
        {
            $category = new Categorie;
            $category ->setTitle($this->faker->sentence())
                      ->setDescription($this->faker->paragraph());
            $manager->persist($category);          

            // *chaque article a besoin d'une catégorie
            for($i=1; $i<=mt_rand(5,7); $i++)
            {
                $article = new Article;
                $article->setTitle($this->faker->sentence(6))
                        ->setContent($this->faker->paragraph(250))
                        ->setImage($this->faker->imageUrl( 640, 480))
                        ->setCreatedAt($this->faker->dateTimeBetween('-10 months'))
                        ->setCategorie($category);
                $manager->persist($article);
                // *ce qui attache à un article, c'est un commentaire
                for($k=1;$k<=mt_rand(8,10);$k++)
                {
                    $comment = new Comment;
    
                    $now = new \DateTime();
                    // *différence entre le jour article est crée
                    $interval = $now->diff($article->getCreatedAt());
                    // *nombre de jours qu'il y a de différence 
                    $days= $interval->days;
                    // *à partir le mimnum a été crée, "days" est un propiété
                    $minimum = '-' . $days . 'days';
    
    
                    $comment ->setAuthor($this->faker->name)
                             ->setContent($this->faker->paragraph())
                             ->setCreatedAt($this->faker->dateTimeBetween($minimum))
                             ->setArticle($article);
                    $manager->persist($comment);
    
    
                }
            }
        }


        $manager->flush();
    }
}
