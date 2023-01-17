<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Users;
use App\Entity\Articles;
use App\Entity\Categories;
use App\Entity\Commentaires;
use App\Entity\Temoignages;
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

        $adminUser = new Users();

        $adminUser->setPrenom('Frederic')
                  ->setNom('Husson')
                  ->setEmail('mega-services@hotmail.fr')
                  ->setPassword($this->encoder->hashPassword($adminUser, 'Laura@14111990'))
                  ->setAvatar('https://randomuser.me/api/portraits/men/29.jpg')
                  ->setRoles(['ROLE_ADMIN'])
                  ->setIsVerified(1)
                  ->setAdresse($faker->streetAddress)
                 ->setCodePostal($faker->postcode)
                 ->setVille($faker->city)
                 ->setPays($faker->country)
                 ->setSociete($faker->company)
                 ->setTelephone($faker->phoneNumber)
                 ->setUsername($faker->userName());

        $manager->persist($adminUser);

        // Nous gérons les utilisateurs
         
        $users = [];
        $genres = ['male', 'female'];
         
        for ($i=1; $i<=10; $i++) {
         
            $user = new Users;
         
            $genre = $faker->randomElement($genres);
            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';
            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;
         
            $hash = $this->encoder->hashPassword($user, 'password');
         
            $user->setEmail($faker->email)
                 ->setPassword($hash)
                 ->setIsVerified(1)
                 ->setAvatar($picture)
                 ->setNom($faker->lastName($genre))
                 ->setPrenom($faker->firstName($genre))
                 ->setAdresse($faker->streetAddress)
                 ->setCodePostal($faker->postcode)
                 ->setVille($faker->city)
                 ->setPays($faker->country)
                 ->setSociete($faker->company)
                 ->setTelephone($faker->phoneNumber)
                 ->setUsername($faker->userName());
         
            $manager->persist($user);
            $users[] = $user;        
         
        }

        // Nous gérons les Témoignages

        $temoignages = [];
        $note = ['0', '0.5', '1', '1.5', '2', '2.5', '3', '3.5', '4', '4.5', '5'];

        for ($i=1; $i<=5; $i++) {

            $temoignage = new Temoignages();

            $temoignage->setClient($faker->randomElement($users))
                       ->setDescription($faker->text(255))
                       ->setActif(1)
                       ->setCreatedAt($faker->dateTime())
                       ->setNote($faker->randomElement($note));

            $manager->persist($temoignage);
            $temoignages[] = $temoignage;
        }


        // Nous gérons les catégories
        $categories = [];

        for ($i=1; $i<=10; $i++) {

            $categorie = new Categories;

            $mots = rand(1,3);

            $categorie->setNom($faker->words($mots, true));

            $manager->persist($categorie);
            $categories[] = $categorie;


        }

        // Nous gérons les articles

        $articles = [];
        for ($i=1; $i<=500; $i++) {

            $article = new Articles();

            $article->setDate($faker->dateTime('now'))
                    ->setContenu($faker->paragraph(5))
                    ->setTitre($faker->text(50))
                    ->setImage($faker->imageUrl(1024, 768))
                    ->setCategories($faker->randomElement($categories))
                    ->setAuteur($faker->randomElement($users));

            $manager->persist($article);
            $articles[] = $article;
        }

        // Nous gérons les commentaires

        $commentaires = [];
        for ($i=1; $i<=500; $i++) {
            $commentaire = new Commentaires();

            $commentaire->setArticle($faker->randomElement($articles))
                        ->setContenu($faker->paragraph(1))
                        ->setActif(true)
                        ->setCreatedAt($faker->dateTime('now'))
                        ->setAuteur($faker->randomElement($users));

            $manager->persist($commentaire);
            $commentaires[] = $commentaire;
        }


        $manager->flush();
    }
}
