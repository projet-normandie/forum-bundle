ALTER TABLE `forum_forum` CHANGE `idCategory` `idCategory` INT NULL DEFAULT '0';

ALTER TABLE `forum_topic` ADD `boolArchive` TINYINT NOT NULL DEFAULT '0' AFTER `idMessageMax`;

ALTER TABLE `forum_topic` ADD INDEX `idxArchive` (`boolArchive`);