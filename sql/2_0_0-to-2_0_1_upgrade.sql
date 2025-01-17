--
--  Comment Meta Language Constructs:
--
--  #IfNotTable
--    argument: table_name
--    behavior: if the table_name does not exist,  the block will be executed

--  #IfTable
--    argument: table_name
--    behavior: if the table_name does exist, the block will be executed

--  #IfColumn
--    arguments: table_name colname
--    behavior:  if the table and column exist,  the block will be executed

--  #IfMissingColumn
--    arguments: table_name colname
--    behavior:  if the table exists but the column does not,  the block will be executed

--  #IfNotColumnType
--    arguments: table_name colname value
--    behavior:  If the table table_name does not have a column colname with a data type equal to value, then the block will be executed

--  #IfNotRow
--    arguments: table_name colname value
--    behavior:  If the table table_name does not have a row where colname = value, the block will be executed.

--  #IfNotRow2D
--    arguments: table_name colname value colname2 value2
--    behavior:  If the table table_name does not have a row where colname = value AND colname2 = value2, the block will be executed.

--  #IfNotRow3D
--    arguments: table_name colname value colname2 value2 colname3 value3
--    behavior:  If the table table_name does not have a row where colname = value AND colname2 = value2 AND colname3 = value3, the block will be executed.

--  #IfNotRow4D
--    arguments: table_name colname value colname2 value2 colname3 value3 colname4 value4
--    behavior:  If the table table_name does not have a row where colname = value AND colname2 = value2 AND colname3 = value3 AND colname4 = value4, the block will be executed.

--  #IfNotRow2Dx2
--    desc:      This is a very specialized function to allow adding items to the list_options table to avoid both redundant option_id and title in each element.
--    arguments: table_name colname value colname2 value2 colname3 value3
--    behavior:  The block will be executed if both statements below are true:
--               1) The table table_name does not have a row where colname = value AND colname2 = value2.
--               2) The table table_name does not have a row where colname = value AND colname3 = value3.

--  #IfRow2D
--    arguments: table_name colname value colname2 value2
--    behavior:  If the table table_name does have a row where colname = value AND colname2 = value2, the block will be executed.

--  #IfRow3D
--        arguments: table_name colname value colname2 value2 colname3 value3
--        behavior:  If the table table_name does have a row where colname = value AND colname2 = value2 AND colname3 = value3, the block will be executed.

--  #IfIndex
--    desc:      This function is most often used for dropping of indexes/keys.
--    arguments: table_name colname
--    behavior:  If the table and index exist the relevant statements are executed, otherwise not.

--  #IfNotIndex
--    desc:      This function will allow adding of indexes/keys.
--    arguments: table_name colname
--    behavior:  If the index does not exist, it will be created

--  #EndIf
--    all blocks are terminated with a #EndIf statement.

--  #IfNotListReaction
--    Custom function for creating Reaction List

--  #IfNotListOccupation
--    Custom function for creating Occupation List

--  #IfTextNullFixNeeded
--    desc: convert all text fields without default null to have default null.
--    arguments: none

--  #IfTableEngine
--    desc:      Execute SQL if the table has been created with given engine specified.
--    arguments: table_name engine
--    behavior:  Use when engine conversion requires more than one ALTER TABLE

--  #IfInnoDBMigrationNeeded
--    desc: find all MyISAM tables and convert them to InnoDB.
--    arguments: none
--    behavior: can take a long time.

#IfMissingColumn history_data risk_factors
ALTER TABLE `history_data` ADD COLUMN `risk_factors` TEXT NULL DEFAULT NULL AFTER `exams`;
#EndIf

DELETE FROM `code_types` WHERE `code_types`.`ct_key` = 'ICD9';

#IfMissingColumn users menu_role
ALTER TABLE users ADD COLUMN menu_role varchar(100) NOT NULL default "Default User";
#EndIf

#IfMissingColumn users fullscreen_page
ALTER TABLE users ADD COLUMN fullscreen_page text NOT NULL;
#EndIf

#IfMissingColumn users fullscreen_enable
ALTER TABLE users ADD COLUMN fullscreen_enable int(11) NOT NULL default 0;
#EndIf

#IfMissingColumn users menu_role
ALTER TABLE users ADD COLUMN menu_role varchar(100) NOT NULL default "Default User";
#EndIf

#IfColumn users fullscreen_role
ALTER TABLE `users` DROP `fullscreen_role`;
#EndIf

DROP TABLE IF EXISTS `menu_trees`;

DROP TABLE IF EXISTS `menu_entries`;

--
-- Table structure for table `libreehr_modules`
--

DROP TABLE IF EXISTS `wms_wards`;
CREATE TABLE `wms_wards` (
  `wid` varchar(500) NOT NULL,
  `name` varchar(500) NOT NULL,
  `rooms` varchar(500) NOT NULL,
  `rooms_capacity` varchar(500) NOT NULL,
  `prefix` varchar(500) NOT NULL,
  `owner` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wms_rooms`;
CREATE TABLE `wms_rooms` (
  `rid` varchar(500) COLLATE utf8_persian_ci NOT NULL,
  `wid` varchar(500) COLLATE utf8_persian_ci NOT NULL,
  `pid` varchar(500) COLLATE utf8_persian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;