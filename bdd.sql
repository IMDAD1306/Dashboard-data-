 CREATE TABLE `users` 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `token_expire` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`);

  CREATE TABLE fichiers_json (
    ->     id INT AUTO_INCREMENT PRIMARY KEY,
    ->     nom_fichier VARCHAR(255) NOT NULL,
    ->     date_integration DATETIME DEFAULT CURRENT_TIMESTAMP,
    ->     description TEXT,
    ->     chemin_stockage VARCHAR(512) NOT NULL
    -> );