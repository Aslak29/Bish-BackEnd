# BISH Back_End

## Getting started

To make it easy for you to get started with GitLab, here's a list of recommended next steps.

Already a pro? Just edit this README.md and make it your own. Want to make it easy? [Use the template at the bottom](#editing-this-readme)!

## Add your files

- [ ] [Create](https://docs.gitlab.com/ee/user/project/repository/web_editor.html#create-a-file) or [upload](https://docs.gitlab.com/ee/user/project/repository/web_editor.html#upload-a-file) files
- [ ] [Add files using the command line](https://docs.gitlab.com/ee/gitlab-basics/add-file.html#add-a-file-using-the-command-line) or push an existing Git repository with the following command:

```
cd existing_repo
git remote add origin https://gitlab.com/incubateur_m2i_afpa_2/team-les-codetenus/back_end.git
git branch -M main
git push -uf origin main
```

## Initialisation du Projet avec la base de données

- [ ] [Installer Composer si vous ne l'avez pas !](https://getcomposer.org/)
- [ ] initialisation de composer avec le projet

```
cd back_end
composer update
```

- [ ] Configurer la connexion de votre base de données
- Copier Coller le `.env` et renomer le en `.env.local`
- Ensuite configurer le `.env.local`

```
DATABASE_URL="mysql://{NomUtilisateur}:{Motdepasse}@127.0.0.1:3306/#NomBDD#?serverVersion=5.7.36&charset=utf8mb4"
```

- [ ] Si la base de donnée est déjà existante avec des données
  - il faudra d'abord la supprimer (pour éviter toutes **_erreur_**)

```
php bin/console doctrine:database:drop --force
```

- [ ] Initialisation de la base de données

```
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migration:migrate
```

- [ ] Générer les données fictives

```
php bin/console doctrine:fixtures:load
```
## Générer les Clés privés pour Token JWT

Installer le .exe de [Win64 OpenSSL v3.0.7 Light](https://slproweb.com/productscd/Win32OpenSSL.html)

Dans votre terminal :

``` composer update ```

``` php bin/console lexik:jwt:generate-keypair ```

Vous pouvez maintenant vous connecter sur le site.

## Les Différentes requêtes
|           N°           | Entité  |                                         URI                                         | Method | Status HTTP |                   Description                   |
|:----------------------:|:-------:|:-----------------------------------------------------------------------------------:|:------:|:-----------:|:-----------------------------------------------:|
| <a id="request1">1</a> |  Blog   |                                      /api/blog                                      |  GET   |     200     |  Permet de retourner tout les blogs existants   |
| <a id="request2">2</a> |  User   |      /api/user/register/{name}/{surname}/{email}/{password}/{passwordConfirm}       |  POST  |     200     |       Permet d'enregister un utilisateur        |
| <a id="request3">3</a> | Produit |                                    /api/produit                                     |  GET   |     200     | Permet de retourner tout les produits existants |
| <a id="request4">4</a> | Produit | /api/produit/add/{name}/{description}/{pathImage}/{price}/{is_trend}/{is_available} |  POST  |     200     |           Permet d'ajouter un produit           |

## Erreur Gérer par l'application
| Code d'erreur |                     Message d'erreur                     | Status HTTP | Erreur généré par |
|:-------------:|:--------------------------------------------------------:|:-----------:|:-----------------:|
|      001      | L'adresse email est déjà inscrite dans la base de donnée |     409     |  [2](#request2)   | 

