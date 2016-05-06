# Migration de la base de données
#
# Date et heure : 6 mai 2016 à 10:20
#
# Rôle : Créer la table Publications

create table Publications(
    id int not null primary key auto_increment,
    title varchar(512) not null,
    description mediumtext,
    status enum('submitted', 'published', 'reviewing') not null,
    publication_title varchar(512),
    publication_year int,
    conference_location varchar(512),
    file_id int not null
);
