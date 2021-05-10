<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\SchoolYear;
use App\Entity\Project;
use EasySlugger\Slugger;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // Création User Admin
        $user = new User();
        $user->setFirstname('Foo');
        $user->setLastname('Bar');
        $user->setEmail('admin@example.com');
        $user->setRoles(['ROLE_ADMIN']);
        // Encodage du mot de passe
        $password = $this->encoder->encodePassword($user, 'motdepasse');
        $user->setPassword($password);
        $manager->persist($user);

        // Création du générateur de fausses données
        $faker = \Faker\Factory::create('fr_FR');

        // Création de 60 Student
        for ($i = 0; $i < 60; $i++) {

            // Déclaration variable qui générent les fausses données
            $firstname = $faker->firstName;
            $lastname = $faker->lastName;
            $email = Slugger::slugify($firstname).'.'.Slugger::slugify($lastname).'@'.$faker->safeEmailDomain;

            $user = new User();
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setEmail($email);
            $user->setRoles(['ROLE_STUDENT']);
            $user->setPassword($password);
            $manager->persist($user);
        }

        // Création de 5 Teacher
        for ($i = 0; $i < 5; $i++) {

            // Déclaration variable qui générent les fausses données
            $firstname = $faker->firstName;
            $lastname = $faker->lastName;
            $email = Slugger::slugify($firstname).'.'.Slugger::slugify($lastname).'@'.$faker->safeEmailDomain;

            $user = new User();
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setEmail($email);
            $user->setRoles(['ROLE_TEACHER']);
            $user->setPassword($password);
            $manager->persist($user);
        }

        // Création de 15 Client
        for ($i = 0; $i < 15; $i++) {

            // Déclaration variable qui générent les fausses données
            $firstname = $faker->firstName;
            $lastname = $faker->lastName;
            $email = Slugger::slugify($firstname).'.'.Slugger::slugify($lastname).'@'.$faker->safeEmailDomain;

            $user = new User();
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setEmail($email);
            $user->setRoles(['ROLE_CLIENT']);
            $user->setPassword($password);
            $manager->persist($user);
        }

        // Création de 3 SchoolYear
        for ($i = 0; $i < 3; $i++) {

            // Déclaration variable qui générent les fausses données
            $name = $faker->word;

            $schoolyear = new SchoolYear();
            $schoolyear->setName($name);
            $manager->persist($schoolyear);
        }

        // Création de 20 Project
        for ($i = 0; $i < 20; $i++) {

            // Déclaration variable qui générent les fausses données
            $name = $faker->word;

            $project = new Project();
            $project->setName($name);
            $manager->persist($project);
        }

        // Sauvegarde dans la BDD
        $manager->flush();
    }
}
