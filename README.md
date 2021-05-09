# ECF_Back avec Symfony

Projet Symfony avec mise en place d'une BDD.

## Structure de la BDD

Avant de commencer un projet, il est important d'organiser la structure de sa BDD.

1. user
   - id: clé primaire
   - email: varchar 190, unique
   - roles: text
   - password: varchar 190
   - firstname: varchar 190
   - lastname: varchar 190
   - phone: varchar 20, nullable
   - school_year_id: clé étrangère qui pointe vers school_year.id
2. school_year
   - id: clé primaire
   - name: varchar 190
   - date_start: datetime, nullable
   - date_end: datetime, nullable
3. project
   - id: clé primaire
   - name: varchar 190
   - description: text, nullable
4. project_user
   - project_id: clé étrangère qui pointe vers project.id
   - user_id: clé étrangère qui pointe vers user.id

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
     3. La longueur (si le type est 'string')
     4. Si la propriété peut-être nullable ou non
2. Appuyer sur la touche Return pour arrêter l'ajout de propriété.
3. `php bin/console make:migration`
   - Cette commande nous permet de créer une migration. Celle-ci va récupérer tout ce que l'on vient de créer (les entités).
4. `php bin/console doctrine:migrations:migrate`
   - Cette commande nous permet d'éxécuter les requêtes. Après cette commande, nous pourrons voir que les tables se sont bien créés dans la BDD.
