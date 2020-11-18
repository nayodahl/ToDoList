# ToDoList - Project 8 - [![Codacy Badge](https://app.codacy.com/project/badge/Grade/91efb4c448a642e5bd0b50571f3d0cf0)](https://www.codacy.com/gh/nayodahl/ToDoList/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=nayodahl/ToDoList&amp;utm_campaign=Badge_Grade)

ToDoList app, done for OPENCLASSROOMS, made with Symfony

## What is this Project ?

This app is made of PHP, using old Symfony 3.1
The goal of this educational project is to update this app (LTS 4.4 has been chosen), correct coding mistakes, implement new features, and improve performance.

An exemple of this app is online here : https://todolist.nayo.cloud and can be tested

## Want to clone and test this app ?

- Clone Repository on your web server
- [Checkout](https://git-scm.com/docs/git-checkout) the branch you want to make changes on (it should be master).
- Install dependancies using Composer with dev depandancies (composer install, https://getcomposer.org/doc/01-basic-usage.md). You may need to remove composer.lock file.
- Create a database on your SQL server.
- Configure access to this database on .env file at source of the project (user, password, name of db, address etc..).
- Run doctrines migrations (php bin/console doctrine:migration:migrate). You can check your migration status with php bin/console doctrine:migration:status .
- Load initial dataset using Datafixtures (php bin/console doctrine:fixtures:load).
- Optional : you can remove dev dependancies (composer install --no-dev --optimize-autoloader) and switch app to prod environment editing .env config file.

## Let's go

- Users extracted from datafixtures : utilisateur1 (standard account), utilisateur2 (admin account).
- Passwords are @dmIn123

## Author

**Anthony Fachaux** - Openclassrooms Student - Dev PHP/Symfony