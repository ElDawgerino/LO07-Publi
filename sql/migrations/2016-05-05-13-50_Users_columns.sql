# Migration de la base de données
#
# Date et heure : 5 mai 2016 à 13:50
#
# Rôle : Ajoute d'autres colonnes à la table Users (last_name, first_name, organisation, team)

alter table Users add last_name VARCHAR(100) not null;
alter table Users add first_name VARCHAR(100) not null;
alter table Users add organisation VARCHAR(100);
alter table Users add team enum("CREIDD", "ERA", "GAMMA3", "LASMIS", "LM2S", "LNIO", "LOSI", "Tech-CICO", "Autre");
