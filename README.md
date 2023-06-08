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
Configurer laconnexion à la base de données ds le fichier .env (voir cours),
puis taper les commandes suivantes:
...

symfony console doctrine:database:create
symfony console doctrine:migrations:migrate
...

### Lancer le serveur
```
symfony serve
```