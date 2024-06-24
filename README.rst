================
PROJET PLADAPATH 2023-2024 - CPAM VAL-DE-MARNE
================

Le projet est hébergé sur
https://gitlab.cnqd.cnamts.fr/developpement_php_94/pladapath_94

================
Requirement
================

  - `PHP 7.2.5 or higher <https://symfony.com/doc/current/setup.html#technical-requirements>`_ 
  - `AuthSrvSecBundle <https://docs-projet.skphp.csh-dijon.ramage/docs/AuthSrvSecBundle/5.4.3/?id=c5e66b9f-53db-4878-ae5d-477b2b2fddef&label=5.4.3>`_ 
  - `SecurityBundle <https://docs-projet.skphp.csh-dijon.ramage/docs/SecurityBundle/5.80.0/?id=e339ddf3-3c59-47a9-a195-389b9e41ddf9&label=5.80.0>`_ 

================
Installation
================

   .. code-block:: sh

      git clone https://gitlab.cnqd.cnamts.fr/developpement_php_94/pladapath_94.git
      cd pladapath_94

   Récupérer les dernières modifications de votre dépôt distant

   .. code-block:: sh

      composer install
      symfony serve



=========
Variable d'environnement
=========

Créer une ficher `.env` à la racine de votre projet, et ajouter ces 2 variables, l'une pour la connection à votre base de donnée, et 
l'autre pour la connection à votre boîte mail.

  .. code-block:: sh

      DATABASE_URL=
      MAILER_DSN=



