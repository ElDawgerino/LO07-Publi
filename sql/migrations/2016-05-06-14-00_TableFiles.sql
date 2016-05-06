# Migration de la base de données
#
# Date et heure : 6 mai 2016 à 14:00
#
# Rôle : Créer la table Files

create table Files(
    id int primary key not null auto_increment,
    original_name varchar(512) not null,
    path_on_server varchar(1024) not null
);

alter table Publications add constraint fk_file_id foreign key (file_id) references Files(id);
