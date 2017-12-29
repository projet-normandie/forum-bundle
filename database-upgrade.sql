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

ALTER TABLE forum_topic DROP FOREIGN KEY forum_topic_ibfk_1;
UPDATE forum_topic t, vgr_player p
SET t.idUser = p.normandie_user_id
WHERE t.idUser = p.idPlayer;
ALTER TABLE `forum_topic` ADD CONSTRAINT `forum_topic_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `member`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `forum_message` CHANGE `idMembre` `idUser` INT(13) NOT NULL DEFAULT '0';
ALTER TABLE `forum_message` CHANGE `dateCreation` `created_at` DATETIME NOT NULL;
ALTER TABLE `forum_message` CHANGE `dateModification` `updated_at` DATETIME NOT NULL;
