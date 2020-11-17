Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require projet-normandie/forum-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require projet-normandie/forum-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    ProjetNormandie\ForumBundle\ProjetNormandieForumBundle::class => ['all' => true],
];
```

Configuration
============

### Database

In order to link your User entity to this module you should add the following configuration:
(Replace AppBundle\Entity\User with your user class).

[Official documentation](http://symfony.com/doc/current/cookbook/doctrine/resolve_target_entity.html)

```yaml
# Doctrine Configuration - config.yml
doctrine:
    orm:
        ...
        resolve_target_entities:
            ProjetNormandie\ForumBundle\Entity\UserInterface: AppBundle\Entity\User
```

After resolving the entity you can update your database schema.

