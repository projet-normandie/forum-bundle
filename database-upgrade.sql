
UPDATE forum_topic t, vgr_player p
SET t.idUser = p.normandie_user_id
WHERE t.idUser = p.id;
ALTER TABLE `forum_topic` ADD CONSTRAINT `forum_topic_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `member`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `forum_message` CHANGE `idMessage` `id` INT(13) NOT NULL AUTO_INCREMENT;
ALTER TABLE `forum_message` CHANGE `idMembre` `idUser` INT(13) NOT NULL DEFAULT '0';
ALTER TABLE `forum_message` CHANGE `dateCreation` `created_at` DATETIME NOT NULL;
ALTER TABLE `forum_message` CHANGE `dateModification` `updated_at` DATETIME NOT NULL;
ALTER TABLE `forum_message` CHANGE `texte` `message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE forum_message DROP FOREIGN KEY forum_message_ibfk_2;
UPDATE forum_message m, vgr_player p
SET m.idUser = p.normandie_user_id
WHERE m.idUser = p.id;
ALTER TABLE `forum_message` ADD CONSTRAINT `forum_message_ibfk_2` FOREIGN KEY (`idUser`) REFERENCES `member`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;


CREATE TABLE `forum_language` (
  `idLangue` int(11) NOT NULL,
  `libLangue` varchar(30) NOT NULL DEFAULT 'French',
  `fichier` varchar(30) NOT NULL DEFAULT 'lang_french',
  `drapeau` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER  TABLE `forum_language` ADD PRIMARY KEY (`idLangue`);
ALTER  TABLE `forum_language` MODIFY `idLangue` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `forum_language` SELECT * FROM `vgr`.`t_langue`;

ALTER TABLE `forum_language` CHANGE `idLangue` `idLanguage` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `forum_language` CHANGE `libLangue` `libLanguage` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'French';
ALTER TABLE `forum_language` DROP `drapeau`;
ALTER TABLE `forum_language` CHANGE `fichier` `code` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'lang_french';

UPDATE `forum_language` SET `libLanguage` = 'French' WHERE `forum_language`.`idLanguage` = 1;


ALTER TABLE `forum_topic_user` CHANGE `idMembre` `idUser` INT(13) NOT NULL DEFAULT '0';
ALTER TABLE `forum_topic_user` CHANGE `estLu` `boolRead` TINYINT(4) NOT NULL DEFAULT '0';
ALTER TABLE `forum_topic_user` CHANGE `estNotif` `boolNotif` TINYINT(4) NOT NULL DEFAULT '0';


ALTER TABLE forum_topic_user DROP FOREIGN KEY forum_topic_user_ibfk_1;
TRUNCATE table forum_topic_user;
ALTER TABLE `forum_topic_user` ADD CONSTRAINT `forum_topic_user_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `member`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE forum_topic_user DROP PRIMARY KEY;
ALTER TABLE `forum_topic_user` ADD `id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);
ALTER TABLE `forum_topic_user` ADD UNIQUE( `idUser`, `idTopic`);


CREATE TABLE `forum_forum_user` (
  `id` int(11) NOT NULL,
  `idForum` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `boolRead` tinyint(4)	NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER  TABLE `forum_forum_user` ADD PRIMARY KEY (`id`);
ALTER  TABLE `forum_forum_user` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `forum_forum_user` ADD CONSTRAINT `forum_forum_user_ibfk_1` FOREIGN KEY (`idForum`) REFERENCES `forum_forum`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `forum_forum_user` ADD CONSTRAINT `forum_forum_user_ibfk_2` FOREIGN KEY (`idUser`) REFERENCES `member`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

-- MAJ nbMessage
UPDATE forum_topic t
SET t.nbMessage = (SELECT COUNT(id) FROM forum_message WHERE idTopic = t.id);


ALTER TABLE `forum_forum` ADD `role` VARCHAR(50) NULL AFTER `status`;
UPDATE `forum_forum` SET role = 'ROLE_FORUM_VGR_TEAM' WHERE id = 38;
UPDATE `forum_forum` SET role = 'ROLE_FORUM_ADMINISTRATION' WHERE id = 16;

DROP TABLE t_groupeutilisateur_membre;
DROP TABLE t_forum_groupeutilisateur;
DROP TABLE t_groupeutilisateur;





