CREATE TABLE IF NOT EXISTS `PREFIX_dealt_mission_category` (
  id_mission INT NOT NULL,
  id_virtual_product INT NOT NULL,
  id_category INT NOT NULL
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;