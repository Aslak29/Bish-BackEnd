-- Different test request --

-- Select le total d'une commande via l'id de la commande | Select order total via order id
Select sum(price * quantite) from produit_in_commande left join commande c on c.id = produit_in_commande.commandes_id where commandes_id = 56;


-- Select la moyenne d'un produit
SELECT avg(note) from notation where produit_id = 696;