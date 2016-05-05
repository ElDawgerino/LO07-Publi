# Migration de la base de données
#
# Date et heure : 5 mai 2016 à 13:45
#
# Rôle : Rédéfinir la colonne username comme étant UNIQUE

alter table Users add unique(username);
