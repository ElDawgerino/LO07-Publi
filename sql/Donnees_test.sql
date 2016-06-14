# Utilisateurs déjà définis pour le site #
#   login:        mdp:
#   admin         t~$n7A5L      (compte administrateur)
#   dupont1       Tb2~Gd6>
#   dupont2       5r*u+H7F
##########################################

--
-- Table `Auteurs`
--

INSERT INTO `Auteurs` (`id`, `nom`, `prenom`, `organisation`, `equipe`) VALUES
(1, 'Admin', 'Jean', 'UTT', 'Tech-CICO'),
(2, 'Dupont', 'Paul', 'UTT', 'GAMMA3'),
(3, 'Dupont', 'Jacques', 'UTT', 'LNIO'),
(4, 'Lu', 'Gérard', 'UTT', 'GAMMA3'),
(5, 'Bertrand', 'Jean', 'Paris VI', 'GAMMA3'),
(6, 'Marc', 'NonUTTiens', 'Université de Lyon', 'ABC'),
(7, 'Laurent', 'Guy', 'Paris IV', 'ABC');

--
-- Table `Conferences`
--

INSERT INTO `Conferences` (`id`, `nom`, `date_conference`, `lieu`) VALUES
(1, 'IC3K 2013; KDIR 2013 - 5th International Conference on Knowledge Discovery and Information Retrieval and KMIS 2013 - 5th International Conference on Knowledge Management and Information Sharing, Proc.', '2013-02-14', 'Quelquepart');

--
-- Table `Fichiers`
--

INSERT INTO `Fichiers` (`id`, `nom_original`, `chemin_server`) VALUES
(1, 'Inventaire-nanomateriaux-une-nouvelle-reglementation-exigeante-unique-au-monde.png', 'uploads/3_d35a19336ca11529518b8a447319ef1dda50fdd109a6e95ff5ed440f597178ef'),
(2, 'Pseudo-bond-graph-model.jpg', 'uploads/3_1e47fc4e966e464aa854adc2d20d29a8f4c2e156db4ea283ed2ddf04b45a1741'),
(3, 'projectdesign-objectives.png', 'uploads/1_fa67da77b06f4a096ebc39cb95b1e75dc85f75201058786fd5fe251a894ca750');

--
-- Table `Journaux`
--

INSERT INTO `Journaux` (`id`, `titre`, `editeur`) VALUES
(1, 'Solar Energy', 'Elsevier'),
(2, 'Thermal Science', 'VINČA Institute of Nuclear Sciences');

--
-- Table `Publications`
--

INSERT INTO `Publications` (`id`, `titre`, `description`, `statut`, `categorie`, `annee_publication`, `journal_id`, `journal_volume`, `pages`, `conference_id`, `fichier_id`) VALUES
(1, 'Review of life cycle assessment of nanomaterials in photovoltaics.', 'Photovoltaic (PV) technologies are gaining a share in the renewable energy production market. Recently nanomaterials have been used by researchers to improve the performance and efficiency of PVs. Consideration to the environmental aspects of nanomaterials infused PVs is a growing area of interest. Therefore, the objective of this paper is to investigate the application of LCA to PV technology. Particularly, the authors are interested in scrutinizing the application of LCA to PV systems infused with nanomaterials. In this paper, a literature review was performed to describe and assess the limitations of current research on the usage of life cycle assessment (LCA) methodologies to predict the environmental impact of nanomaterials usage on PVs. The approach to this review focuses on two sub-categories: production and/or use of PVs, and end-of-life of PVs. Following this approach the context and progress of LCA is described. Research gaps and opportunities for improved environmental performance throughout the life cycle of nano-infused PVs are identified and discussed. This work provides a basis for the continue analysis of emerging nanomaterials and PV technologies.', 'Soumis', 'RI', 2016, 1, '133', '249–258', NULL, 1),
(2, 'Pseudo-Bond Graph model for the analysis of the thermal behavior of buildings', NULL, 'Soumis', 'RI', 2010, 2, '17, issue 3', '723–732', NULL, 2),
(3, 'Memory meetings an approach to keep track of project knowledge in design', NULL, 'Soumis', 'BV', 2013, NULL, NULL, NULL, 1, 3),
(4, 'Doublon', 'Ceci est un doublon d\'une autre publication', 'En révision', 'CF', 2015, NULL, NULL, NULL, NULL, NULL),
(5, 'Doublon', 'Le 2ème doublon !', 'En révision', 'CI', 2014, NULL, NULL, NULL, NULL, NULL);

--
-- Table `RelationsAuteurs`
--

INSERT INTO `RelationsAuteurs` (`publication_id`, `numero_auteur`, `auteur_id`) VALUES
(3, 0, 1),
(4, 0, 1),
(5, 0, 1),
(1, 2, 3),
(1, 0, 4),
(1, 1, 5),
(2, 0, 6),
(2, 1, 7);

--
-- Table `Utilisateurs`
--

INSERT INTO `Utilisateurs` (`id`, `login`, `mdp`, `admin`) VALUES
(1, 'admin', 'cdf4770ec6bc665d80196545d03721a04384a308796ef2746b65aa25662b5e3c', 1),
(2, 'dupont1', '7947ae7bd9902da3570ef89bc003b3e224073bce0bc26e1b4b297bd4644f42fa', 0),
(3, 'dupont2', '9bdc7fcfc1d368996bb935a93c45344b8bcaedd38796814f5718f91d4dca49ad', 0);
