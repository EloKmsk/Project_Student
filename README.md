# ECF_Back avec Symfony PARTIE 1

Première partie d'un projet Symfony avec mise en place d'une BDD.

## Structure de la BDD

Avant de commencer un projet, il est important d'organiser la structure de sa BDD.

### user

- id: clé primaire
- email: varchar 190, unique
- roles: text
- password: varchar 190
- firstname: varchar 190
- lastname: varchar 190
- phone: varchar 20, nullable
- school_year_id: clé étrangère qui pointe vers school_year.id

### school_year

- id: clé primaire
- name: varchar 190
- date_start: datetime, nullable
- date_end: datetime, nullable

### project

- id: clé primaire
- name: varchar 190
- description: text, nullable

### project_user

- project_id: clé étrangère qui pointe vers project.id
- user_id: clé étrangère qui pointe vers user.id

1. L'entité User a une relation ManyToMany avec Project.
2. L'entité User a une relation ManyToOne avec SchoolYear.

## Création d'un utilisateur pour se connecter à la BDD

Pour créer un utilisateur, on ouvre le terminal et on lance ces commandes :

1. `sudo mysql`
   - Cette commande nous permet de rentrer dans le serveur MYSQL.
2. `CREATE USER 'nouveau_utilisateur'@'localhost' IDENTIFIED BY 'mot_de_passe';`
   - Cette commande nous permet de créer un utilisateur pour se connecter à la BDD. 'nouveau_utilisateur' est le nom de l'utilisateur, 'localhost' correspond au serveur local de l'ordinateur et 'mot_de_passe' est le mot de passe qui nous permettra de se connecter.
3. `GRANT ALL PRIVILEGES ON * . * TO 'nouveau_utilisateur'@'localhost';`
   - Cette commande nous permet d'accorder tous les privilèges pour la BDD, pour le nouvel utilisateur.
4. `FLUSH PRIVILEGES;`
   - Cette commande nous permet de prendre en compte les changements.
5. `exit;`
   - Cette commande nous permet de sortir du serveur MYSQL.

## Création de la BDD

Pour créer un BDD, on ouvre le terminal et on lance ces commandes :

1. `sudo mysql`
   - Cette commande nous permet de rentrer dans le serveur MYSQL.
2. `CREATE DATABASE nomdelabdd;`
   - Cette commande nous permet de créer la BDD que nous retrouverons dans PMA.
3. `exit;`
   - Cette commande nous permet de sortir du serveur MYSQL.

## Création d'un projet Symfony

Pour créer un projet Symfony, on ouvre un terminal et on exécute cette commande :

1. `symfony new nomduprojet --full`
   - Cette commande nous permet de créer un projet Symfony. A la place de 'nomduprojet' nous mettons le nom que nous souhaiton donner au projet.

## Liaison de l'accès à la BDD avec Symfony

Pour que la BDD soit liée au projet Symfony et que les commandes s'appliquent sur la BDD, il faut suivre ces démarches :

1. Créer un fichier .env.local à la racine du projet
   - Dans ce fichier on y met ceci : `DATABASE_URL="mysql://user:motdepasse@127.0.0.1:3306/nomdelabdd?serverVersion=5.7"`
   - 'user' correspond au nom d'utilisateur que vous avez créé précédemment.
   - 'motdepasse' correspond au mot de passe de l'utilisateur que vous avez créé précédemment.
   - 'nomdelabdd' correspond au nom de la BDD que vous avez créé précédemment.

## Création des tables de la BDD

Pour créer les tables de la BDD, nous allons utiliser Doctrine. Voici les démarches à suivre :

1. `php bin/console make:entity`
   - Cette commande va nous permettre de créer une entité (une table) dans la BDD. La première question qui va être posé est le nom de l'entité. Ensuite, on nous demandera les propriétés (les colonnes de la table), donc :
     1. Le nom de la propriété
     2. Le type de propriété
     - string
     - integer
     - relation
     - boolean
     3. La longueur (si le type est 'string'), la relation (ManyToMany, ManyToOne, OneToMany, OneToOne)..
     4. Si la propriété peut-être nullable ou non
2. Appuyer sur la touche Return pour arrêter l'ajout de propriété.
3. `php bin/console make:migration`
   - Cette commande nous permet de créer une migration. Celle-ci va récupérer tout ce que l'on vient de créer (les entités).
4. `php bin/console doctrine:migrations:migrate`
   - Cette commande nous permet d'éxécuter les requêtes. Après cette commande, nous pourrons voir que les tables se sont bien créés dans la BDD.
5. `php bin/console doctrine:schema:validate`
   - Cette commande nous permet de vérifier si tout est à jour au niveau du projet Symfony et la BDD.

## Installation des dépendances nécessaires

Les dépendances ci-dessous nous permettrons de créer des fixtures, de créer de fausses données ainsi que de sluggifier, voici les commandes à suivre :

1. `composer require doctrine/doctrine-fixtures-bundle`
   - Cette commande va installer doctrine-fixtures-bundle. Elle va nous permettre de créer plus facilement des fixtures.
2. `composer require fzaninotto/faker`
   - Cette commande nous permet d'installer une dépendance qui va générer des données de façons aléatoires (des fausses données).
3. `composer require javiereguiluz/easyslugger`
   - Cette commande nous permet d'installer une dépendance qui va transformer toute chaîne de caractères en chaîne de caractères sans majuscules, sans accents et sans espaces.

## Injection de données indispensables

Ls données indispensables correspondent souvent aux données de l'administrateur de la BDD. Voici la procédure à suivre.

1. Dans `App/DataFixtures/AppFixtures.php`
   - Le fichier basique ressemble à cela :

   ```
   <?php
   namespace App\DataFixtures;

   use Doctrine\Bundle\FixturesBundle\Fixture;
   use Doctrine\Persistence\ObjectManager;

   class AppFixtures extends Fixture
   {
      public function load(ObjectManager $manager)
      {
         // Sauvegarde dans la BDD
         $manager->flush();
      }
   }
   ```
   - En haut du fichier, nous avons la balise d'ouverture de langage PHP, suivi de l'importation de dépendances et de fichier.

2. Création des données indispensables, toujours dans `App/DataFixtures/AppFixtures.php`
   - Nous allons créer un User avec un role ADMIN, sans oublier l'importation des fichiers d'entité et les dépendances :

   ```
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

         // Sauvegarde dans la BDD
         $manager->flush();
      }
   }
   ```
   - Avant de lancer la fixture, nous allons entrer les données de test qui vont être générer par la/les dépendance(s).

## Injection de données test

Les données test, comme son nom l'indique, correspondent à des données aléatoires qui vont être générer pour tester l'application.

1. Dans `App/DataFixtures/AppFixtures.php`, à la suite des données indispensables que nous avons fait précédemment :

   ```
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
   ```
2. Maintenant que les données sont dans le fichier `AppFixtures.php`, nous allons pousser ces données dans la BDD.
   - Pour cela, nous faisons la commande `php bin/console doctrine:fixtures:load`
   - On va nous demander si nous voulons effacer toutes les données déjà mises dans la BDD afin de les remplacer par les données du fichier `AppFixtures.php`, nous mettons 'yes' et on appuie sur Return.

# ECF_Back avec Symfony PARTIE 2

Deuxième partie du projet Symfony avec la création des URL qui permettent d'afficher des données de la BDD.

## Création des requêtes

Pour chaque entité, suivre ces étapes dans le terminal :

1. `php bin/console make:crud`
   - Cette commande permet de créer un controller pour l'entité voulu.
   - Tout d'abord, on va nous demander pour quelle entité souhaitons-nous créer le CRUD.
   - Pour finir, on nous demande le nom que l'on souhaite donner au controller de l'entité souhaitée.
2. Dans `App/src/Controller`
   - Nous y trouverons tout nos controllers créés grâce à la commande précédente, voici un exemple du résultat pour le controller de l'entité Project :
   ```
   <?php

   namespace App\Controller;

   use App\Entity\Project;
   use App\Form\ProjectType;
   use App\Repository\ProjectRepository;
   use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
   use Symfony\Component\HttpFoundation\Request;
   use Symfony\Component\HttpFoundation\Response;
   use Symfony\Component\Routing\Annotation\Route;

   /**
   * @Route("/project")
   */
   class ProjectController extends AbstractController
   {
      /**
      * @Route("/", name="project_index", methods={"GET"})
      */
      public function index(ProjectRepository $projectRepository): Response
      {
         return $this->render('project/index.html.twig', [
               'projects' => $projectRepository->findAll(),
         ]);
      }

      /**
      * @Route("/new", name="project_new", methods={"GET","POST"})
      */
      public function new(Request $request): Response
      {
         $project = new Project();
         $form = $this->createForm(ProjectType::class, $project);
         $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {
               $entityManager = $this->getDoctrine()->getManager();
               $entityManager->persist($project);
               $entityManager->flush();

               return $this->redirectToRoute('project_index');
         }

         return $this->render('project/new.html.twig', [
               'project' => $project,
               'form' => $form->createView(),
         ]);
      }

      /**
      * @Route("/{id}", name="project_show", methods={"GET"})
      */
      public function show(Project $project): Response
      {
         return $this->render('project/show.html.twig', [
               'project' => $project,
         ]);
      }

      /**
      * @Route("/{id}/edit", name="project_edit", methods={"GET","POST"})
      */
      public function edit(Request $request, Project $project): Response
      {
         $form = $this->createForm(ProjectType::class, $project);
         $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {
               $this->getDoctrine()->getManager()->flush();

               return $this->redirectToRoute('project_index');
         }

         return $this->render('project/edit.html.twig', [
               'project' => $project,
               'form' => $form->createView(),
         ]);
      }

      /**
      * @Route("/{id}", name="project_delete", methods={"POST"})
      */
      public function delete(Request $request, Project $project): Response
      {
         if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
               $entityManager = $this->getDoctrine()->getManager();
               $entityManager->remove($project);
               $entityManager->flush();
         }

         return $this->redirectToRoute('project_index');
      }
   }
   ```
   - Le controller nous a créé une requête pour :
      - Afficher toutes les données de la table Project.
      - Afficher les données de Project en fonction d'un id.
      - Permettre de modifier des données de Project en fonction d'un id.
      - Permettre de supprimer un Project en fonction d'un id.

## Tester les requêtes grâce à une URL

Pour tester les requêtes, nous devons lancer le server Symfony.

1. `symfony server:start`
   - Cette commande va nous permettre d'accéder au projet Symfony qui se lance à l'adresse suivante : `localhost:8000`

## Les requêtes disponibles

1. `localhost8000/user`
   - Renvoie la liste des users.
2. `localhost:8000/user/{id}` en remplaçant {id} par l'id que  l'on souhaite
   - Renvoie les données d'un user en fonction de son id.
3. `localhost:8000/user/search/{role}` en remplaçant {role} par le rôle que l'on souhaite (ROLE_ADMIN, ROLE_STUDENT, ROLE_TEACHER, ROLE_CLIENT)
   - Renvoie une liste de tous les users qui correspondent au rôle que l'on a mit dans l'URL.
4. `localhost:8000/schoolyear`
   - Renvoie la liste des schoolyears.
5. `localhost:8000/schoolyear/{id}` en remplaçant {id} par l'id que l'on souhaite
   - Renvoie les données d'une schoolyear en fonction de son id.
6. `localhost:8000/project`
   - Renvoie la liste des projects.
7. `localhost:8000/project/{id}` en remplaçant {id} par l'id que l'on souhaite
   - Renvoie les données d'un project en fonction de son id.

# ECF_Back avec Symfony PARTIE 3

Dernière partie du projet Symfony avec la création d'un formulaire de connexion ainsi que des URL pour regarder/modifier/supprimer des données de la BDD.

## Création du formulaire de connexion

Pour créer un formulaire suivons ces commandes :

1. `php bin/console make:form`
   - Cette commande va nous permettre de générer un formulaire qui prend comme données de vérification, les données de la table User.
   - Elle va nous générer un formulaire de connexion que nous accéderons par `localhost:8000/login`.
2. La commande précédente nous créait aussi un SecurityController dans `App/src/Controller/SecurityController.php`
   - Voici le contenu du fichier :
   ```
   <?php

   namespace App\Controller;

   use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
   use Symfony\Component\HttpFoundation\Response;
   use Symfony\Component\Routing\Annotation\Route;
   use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

   class SecurityController extends AbstractController
   {
      /**
      * @Route("/login", name="app_login")
      */
      public function login(AuthenticationUtils $authenticationUtils): Response
      {
         // if ($this->getUser()) {
         //     return $this->redirectToRoute('target_path');
         // }

         // get the login error if there is one
         $error = $authenticationUtils->getLastAuthenticationError();
         // last username entered by the user
         $lastUsername = $authenticationUtils->getLastUsername();

         return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
      }

      /**
      * @Route("/logout", name="app_logout")
      */
      public function logout()
      {
         throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
      }
   }
   ```
   - Nous pouvons remarquer que le COntroller a aussi généré un `localhost:8000/logout` qui va nous permettre de se déconnecter.

## Liste des URL disponibles
           
NOM DE LA ROUTE              METHODE              URL
  project_index              GET             /project/                          
  project_new                GET|POST        /project/new                       
  project_show               GET             /project/{id}                      
  project_edit               GET|POST        /project/{id}/edit                 
  project_delete             POST            /project/{id}                      
  school_year_index          GET             /schoolyear/                       
  school_year_new            GET|POST        /schoolyear/new                    
  school_year_show           GET             /schoolyear/{id}                   
  school_year_edit           GET|POST        /schoolyear/{id}/edit              
  school_year_delete         POST            /schoolyear/{id}                   
  app_login                  ANY             /login                             
  app_logout                 ANY             /logout                            
  user_index                 GET             /user                              
  user_new                   GET|POST        /user/new                          
  user_show                  GET             /user/{id}                         
  user_test                  GET             /user/search/{roles}               
  user_edit                  GET|POST        /user/{id}/edit                    
  user_delete                POST            /user/{id}                         



   

