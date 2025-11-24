CREATE TABLE IF NOT EXISTS `products` (
  `product_id` INTEGER NOT NULL,
  `name` TEXT(100) NOT NULL,
  `sku` TEXT(14) NOT NULL,
  `price` REAL NOT NULL,
  `image` TEXT(50) NOT NULL,
  PRIMARY KEY (`product_id`),
  UNIQUE (`sku`)
);

CREATE TABLE IF NOT EXISTS `flag` (
  `secret` TEXT(255)
);

INSERT INTO `flag` (`secret`) VALUES ('{GZCTF_FLAG}');

INSERT INTO `products` (`product_id`, `name`, `sku`, `price`, `image`) VALUES
(1, 'Miku', 'VC001', 158.00, 'images/0831.jpg'),
(2, 'Teto', 'VC002', 159.50, 'images/0401.jpg'),
(3, 'Akita', 'VC003', 150.00, 'images/1101.jpg');