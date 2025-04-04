# COMMENT LANCER L'APP.....................

## ÉTAPE 1 :
```bash
composer install && docker compose up -d && symfony serve:start
````

## ÉTAPE 2 :
Rendez vous sur http://localhost:9001/
Connectez vous avec ces identifiants
- username : access1234
- password : secret1234

Créez un bucket avec le nom **__symfony__** et mettez l'Access Policy en **__PUBLIC__**


## ÉTAPE 3 :
Rendez vous sur : http://localhost:8080 et créez un utilisateur ainsi que des posts (les images_id sont en fait des liens mennant vers les images..............)