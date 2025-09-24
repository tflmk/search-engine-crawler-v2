CREATE TABLE `search` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cachetext` text DEFAULT NULL,
  `url` text DEFAULT NULL,
  `date` date,
   PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;