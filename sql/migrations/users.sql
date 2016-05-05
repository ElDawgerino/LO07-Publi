# Migration de la base de données
#
# Rôle : Créer la table Users

create table Users(
    id int primary key not null auto_increment,
    username varchar(100) not null,
    password char(64) not null, # SHA-256 => 256 bits = 64 caractères hexa
    prenom varchar(100) not null,
    nom varchar(100) not null,
    organisation varchar(100) not null,
    equipe  varchar(100) not null
);
