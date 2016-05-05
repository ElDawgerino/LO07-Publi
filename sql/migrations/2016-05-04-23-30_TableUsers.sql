# Migration de la base de données
#
# Date et heure : 4 mai 2016 à 23:30
#
# Rôle : Créer la table Users

create table Users(
    id int primary key not null auto_increment,
    username varchar(100) not null,
    password char(64) not null
);
