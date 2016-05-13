/* Création de la table Journaux */
create table Journaux(
    id int not null primary key auto_increment,
    titre varchar(256),
    editeur varchar(256)
);

/* Création de la table EditionsJournal */
create table EditionsJournal(
    id_journal int not null,
    numero_edition int not null,
    date_edition date,
    primary key(id_journal, numero_edition),

    /* Clé étrangère */
    foreign key(id_journal) references Journaux(id)
);

create table Conferences(
    id int not null primary key auto_increment,
    nom varchar(256),
    date_conference date,
    lieu varchar(256)
);

/* Création de la table Fichiers */
create table Fichiers(
    id int not null primary key auto_increment,
    nom_original varchar(256) not null,
    chemin_server varchar(1024) not null
);

/* Création de la table Publications */
create table Publications(
    id int not null primary key auto_increment,
    titre varchar(256) not null,
    description mediumtext,
    statut enum('Soumis', 'En révision', 'Publié') not null,
    categorie enum('RI', 'CI', 'RF', 'CF', 'OS', 'TD', 'BV', 'AP') not null,
    journal_id int,
    edition_id int,
    pages varchar(32),
    conference_id int,
    fichier_id int,

    /* Clés étrangères */
    foreign key(fichier_id) references Fichiers(id),
    foreign key(journal_id, edition_id) references EditionsJournal(id_journal, numero_edition),
    foreign key(conference_id) references Conferences(id)
);

create table Auteurs(
    id int not null primary key auto_increment,
    nom varchar(128) not null,
    prenom varchar(128) not null,
    organisation varchar(128),
    equipe varchar(128)
);

create table Utilisateurs(
    id int not null primary key,
    login varchar(64) not null,
    mdp varchar(64) not null,

    /* Clé étrangère */
    foreign key(id) references Auteurs(id)
);

create table RelationsAuteurs(
    publication_id int not null,
    numero_auteur int not null,
    auteur_id int not null,
    primary key(publication_id, numero_auteur),

    /* Clé étrangère */
    foreign key(publication_id) references Publications(id),
    foreign key(auteur_id) references Auteurs(id)
);
