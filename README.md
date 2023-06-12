# Projet Le Blog de Batman

### Cloner le projet

```
(commande commander)
git clone 
https://github.com/sophieDubois/leblogdebatman.git
(github code)
```

### Déplacer le terminal dans le dossier cloné
```
cd leblogdebatman
```

### Installer les vendors (pour recréer le dossier vendor)
```
composer install
```

### Création base de données
Configurer la connexion à la base de données ds le fichier .env (voir cours),
puis taper les commandes suivantes:
...

symfony console doctrine:database:create
symfony console doctrine:migrations:migrate
...

### Creation des fixtures
symfony console doctrine:fixtures:load
...
Cette commande crééra:
*un compte admin(email: a@a.a , password: AAaaaa4$)
*10 comptes utilisateurs(email aléatoire,password: AAaaaa4$)
*50articles

###Installation fichiers front-end des bundles(CkEditor)
...
symfony console assets:install public
...

### Lancer le serveur
```
symfony serve
si bug=>   symfony server:stop
```