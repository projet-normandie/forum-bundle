SET NAMES 'utf8';
SET CHARACTER SET utf8;

-- RENAME
RENAME TABLE t_forum_categorie TO forum_category;
RENAME TABLE t_forum TO forum_forum;
RENAME TABLE t_forum_message TO forum_message;
RENAME TABLE t_forum_topic TO forum_forumforum_topic;
RENAME TABLE t_forum_typetopic TO forum_topic_type;

DROP TRIGGER IF EXISTS `tForumTopicAfterDelete`;
DROP TRIGGER IF EXISTS `tForumTopicAfterInsert`;
DROP TRIGGER IF EXISTS `tForumTopicAfterUpdate`;

DROP TRIGGER IF EXISTS `tForumMessageAfterDelete`;
DROP TRIGGER IF EXISTS `tForumMessageAfterInsert`;


-- ALTER TABLE
ALTER TABLE `forum_category` CHANGE `idCategorie` `idCategory` INT(13) NOT NULL AUTO_INCREMENT;
ALTER TABLE `forum_category` DROP `libCategorie_fr`;
ALTER TABLE `forum_category` CHANGE `libCategorie_en` `libCategory` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `forum_category` ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `position`, ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;


ALTER TABLE `forum_forum` CHANGE `libForum_en` `libForum` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `forum_forum` CHANGE `idCategorie` `idCategory` INT(13) NOT NULL DEFAULT '0';
ALTER TABLE `forum_forum` CHANGE `statut` `status` ENUM('public','private') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'public';
ALTER TABLE `forum_forum` CHANGE `idForumPere` `idForumFather` INT(13) NULL DEFAULT NULL;
ALTER TABLE `forum_forum` ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `idMessageMax`, ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;


ALTER TABLE `forum_topic` CHANGE `idMembre` `idUser` INT(13) NOT NULL DEFAULT '0';
ALTER TABLE `forum_topic` ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `idLangue`, ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;
ALTER TABLE `forum_topic` CHANGE `idLangue` `idLanguage` INT(11) NOT NULL DEFAULT '1';
ALTER TABLE `forum_topic` ADD INDEX(`idLanguage`);
ALTER TABLE `forum_topic` ADD FOREIGN KEY (`idLanguage`) REFERENCES `forum_language`(`idLanguage`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE forum_topic DROP FOREIGN KEY forum_topic_ibfk_1;
UPDATE forum_topic t, vgr_player p
SET t.idUser = p.normandie_user_id
WHERE t.idUser = p.idPlayer;
ALTER TABLE `forum_topic` ADD CONSTRAINT `forum_topic_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `member`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `forum_message` CHANGE `idMembre` `idUser` INT(13) NOT NULL DEFAULT '0';
ALTER TABLE `forum_message` CHANGE `dateCreation` `created_at` DATETIME NOT NULL;
ALTER TABLE `forum_message` CHANGE `dateModification` `updated_at` DATETIME NOT NULL;
ALTER TABLE `forum_message` CHANGE `texte` `message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE forum_message DROP FOREIGN KEY forum_message_ibfk_2;
UPDATE forum_message m, vgr_player p
SET m.idUser = p.normandie_user_id
WHERE m.idUser = p.idPlayer;
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