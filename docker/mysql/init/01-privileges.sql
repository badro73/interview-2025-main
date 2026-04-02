-- Créer DB dev + test
CREATE DATABASE IF NOT EXISTS `app` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS `app_test` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Droits COMPLETS pour app (dev + test + create)
GRANT ALL PRIVILEGES ON `app`.* TO 'app'@'%';
GRANT ALL PRIVILEGES ON `app_test`.* TO 'app'@'%';
GRANT ALL PRIVILEGES ON *.* TO 'app'@'%' WITH GRANT OPTION;

-- Recharger
FLUSH PRIVILEGES;
