CREATE TABLE IF NOT EXISTS `PREFIX_dealt_cart_product_ref` (
  id INT AUTO_INCREMENT NOT NULL,
  id_cart INT NOT NULL,
  id_product INT NOT NULL,
  id_product_attribute INT NOT NULL,
  id_offer INT NOT NULL,
  PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;