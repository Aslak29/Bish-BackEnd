# Review du 22 12 22

=================================================================================

# En téléprésentiel : Romain et Antoine

# Sur place pour le groupe : Angélique, Corentin, Alexandre, Othmane et Florent

# Pour l'incubateur : Marine, Fethi

---

**Catégorie**

    - utiliser un booléen pour qu'elle soit ON/OFF, plutôt que de la supprimer, ce qui obligerait à la réinsérer si nécessaire.

**Promos**

    - Créer des tranches (5/10/15/20)
    - Actuellement, on peut passer une date antérieure (en date de fin) à la date de début.

**Commande - côté client et côté back**

    -> Vérifier si le prix unitaire est remisé ou pas.
       (les totaux sont actuellement = Prix Unitaire * quantité)
    -> Ajouter une colonne Prix remisé que l'on place entre remise et total.

**Compte Admin**

## Gestion des utilisateurs :

    -> le compte Admin peut-être supprimé ...
    -> Il ne devrait même pas être visible dans liste des utilisateurs.
    -> Intégrer les boutons de modifications de données Utilisateurs à chaque colonne (plutôt que le formulaire actuel)
    -> Adapter le message lorsqu'une action est validée, actuellement :"Etes-vous sûr de vouloir changer le produit ?"
    -> Penser à un type de rôle supérieur
    -> Mettre en forme le numéro de téléphone
    -> Le mot de passe ne doit pas être présent
    -> Revoir la suppression d'un utilisateur
        - Mettre un booléen pour désactiver le compte
        - A la tentative de connexion, vérifier ce booléen

**Administration Produits**

    -> Rendre les cases "Tendances" et "Visibles" en fonctionnement
    -> Archiver les produits d'une commande déjà payée et livrée
    -> Que les états de commande soient en fonctionnement
    -> Si un produit n'est plus en stock mais archivé, le rendre "indisponible"
    -> Créer des boutons pour tout sélectionner ou désélectionner.
    -> Limiter les tailles des images.

## Si on déselectionne tous les produits Tendances -> le Carousel est vide !!!

## On ne doit pas pouvoir modifier quoique ce soit sur une commande en tant qu'admin.

Attention à ne pas pouvoir supprimer un utilisateur si il a une commande en cours

**Blog**

    __"En savoir plus"__

        -> Peut-être effectuer un tri par type de catégorie produit ou de catégorie d'article
        -> Ok pour intégrer un éditeur de texte

**Contacts**

    - afficher les messages reçus par date et non par ID de message
