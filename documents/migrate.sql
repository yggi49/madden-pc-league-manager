ALTER TABLE `fgmldb_game` ADD `inserted_new` INT UNSIGNED NOT NULL DEFAULT '0';
UPDATE `fgmldb_game` SET `inserted_new` = UNIX_TIMESTAMP(`inserted`);
ALTER TABLE `fgmldb_game` DROP `inserted`;
ALTER TABLE `fgmldb_game` CHANGE `inserted_new` `inserted` INT UNSIGNED NOT NULL DEFAULT '0';
