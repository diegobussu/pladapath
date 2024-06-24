Guide du contributeur
#####################

.. contents:: Table des matières

Contribuer au projet PLADAPATH
***************************

Ce guide va vous permettre d'apporter votre contribution au projet.

Nous tenons particulièrement à vous remercier pour l'intérêt que vous lui portez
:-)

Pré-requis
==========

Vous devez connaître git pour pouvoir contribuer au projet. La lecture du
`book Git <https://git-scm.com/book/fr/v2>`_ est fortement recommandée.
Les contributeurs au SK PHP doivent désormais s'intéresser à la qualité du code et installer Grumphp. `GrumPHP <http://docs-projet.cnqd.cnamts.fr/docs/SK-PHP/latest/grumPHP.html>`_

Valeurs du SK PHP
=================

- Les normes **PSRAM** et **DOCSRAM** je respecterai.
- Fainéant en m'obligeant à réutiliser systématiquement les bundles des autres
  plutôt que de réinventer la roue, je serai.
- Mes idées, mon expertise, mon esprit ouvert, ma curiosité, ma bienveillance,
  avec les autres membres de la communauté SK PHP, je partagerai.
- Le `résumé <https://gist.github.com/triaubaral/b814311a375dde6605693ebf4394aff0>`_
  du livre `Coder proprement <https://www.amazon.fr/Clean-Code-Handbook-Software-Craftsmanship/dp/0132350882/ref=pd_cp_14_1?_encoding=UTF8&psc=1&refRID=2GZB9VSJT0CACRR1JH3E>`_ ,
  au moins une fois, je lirai.

Fonctionnement
==============

Nommage des branches
--------------------

Les branches doivent respecter le motif suivant :
**<première lettre du prénom><nom>**/**<feature|fix|security|doc|ci|test>**/**<nom de branche>**.

Chaque type de branche correspond à une situation bien précise.

- ``feature`` : évolution technique ou fonctionnelle
- ``fix`` : correctif technique ou fonctionnel
- ``security`` : corrige une faille de sécurité
- ``doc`` : ajout, mise à jour de la documentation
- ``ci`` : réservé au fonctionnement de la CI
- ``test`` : tests unitaires, fonctionnels, etc.

**Il est essentiel de respecter cette norme.**
Les branches ``security``, par exemple, ont une incidence sur la prise de décision
lors de la mise à jour des dépendances d'une application.

Exemples de branches:

- mmesse/``doc``/etude-conteneurisation
- arossat/``feature``/decorator-authapp-v3
- jschneider/``ci``/utilise-almalinux
- swollenschneider/``security``/fix-claim-issuer

Première contribution
---------------------

Afin de vous aider à faire votre première contribution, certaines issues
sont marquées comme étant plus faciles à traiter. Le lien suivant présente la
liste de ces issues, triées par popularité : `GitLab-issue-simple`_

GitLab
------

.. hint::
   Ajouter votre clé SSH (`GitLab-ajout-cle`_) sur le GitLab national en
   suivant les indications du tutorial : `GitLab-tutorial-cle`_.

   Configurer votre email et votre nom dans Git

   .. code-block:: sh

      git config --global user.email "<email>@assurance-maladie.fr"
      git config --global user.name "<NOM> <Prénom>"

   Récupérer les dernières modifications de votre dépôt distant

   .. code-block:: sh

      git fetch

Comment puis-je contribuer ?
============================

.. note::
   Pour des modifications simples, comme par exemple de la documentation,
   n'hésitez pas à utiliser directement **GitLab** via le **Web IDE** plutôt
   qu'à cloner un projet pour le modifier. Vous devrez simplement respecter le
   nommage de votre branche ainsi que la création de Merge Request.

1. Préparer votre environnement
-------------------------------

Vous pouvez travailler sur les bundles du SK PHP sur un projet existant ou en
créant un nouveau projet Symfony.

Une solution consiste à configurer **composer** pour qu'il utilise la
branche sur laquelle vous travaillez.

.. warning::
   Ce guide présente un exemple de modification sur le bundle
   **AuthSrvSecBundle** installé sur un serveur Linux.
   Vous pouvez bien sûr utiliser **git** intégré à votre IDE, ou procéder
   différemment. Ce qui compte est le résultat : **des merge requests propres**.

- Clonez le projet dans votre répertoire `/home/sk/`

.. code-block:: sh

   cd /home/sk/
   git clone git@gitlab.cnqd.cnamts.fr:STARTER_KIT_PHP-2015/Bundles/AuthSrvSecBundle.git

- Ajoutez les lignes suivantes dans le fichier **composer.json** de votre projet

.. code-block::

   "repositories": {
        "cnam/authsrvsec-bundle": {
            "type": "vcs",
            "url": "https://gitlab.cnqd.cnamts.fr/STARTER_KIT_PHP-2015/Bundles/AuthSrvSecBundle.git"
        }
    }

2. Créer votre propre branche sur le bundle auquel vous souhaitez contribuer
----------------------------------------------------------------------------

.. code-block:: sh

   cd /home/sk/AuthSrvSecBundle
   # Par exemple, origin/master-4.0 pour travailler sur la branche 4.0 du bundle
   git checkout -b <branche> origin/master-4.0

3. Enregistrer vos modifications dans le dépôt
----------------------------------------------

.. code-block:: sh

   git add <fichier>
   git commit
   git push origin <branche>

4. Utiliser votre propre branche du bundle dans votre projet
------------------------------------------------------------

- Faites un **composer require cnam/authsrvsec-bundle:"dev-<branche>"**
- Votre projet intègre à présent les modifications que vous avez apportées au bundle

Alternative
-----------

Vous pouvez paramétrer votre *IDE* de manière à synchroniser le répertoire local
``/home/sk/AuthSrvSecBundle`` avec le répertoire distant ``./vendor/cnam/authsrvsec-bundle``
d'un projet existant. Ainsi vous pourrez facilement constater le résultat
de vos modifications avant un *commit*.

5. Créer une Merge request
--------------------------

.. important::
   Ne soumettez jamais de **Merge Requests** depuis un fork mais bien depuis le
   projet du bundle dans le groupe **STARTER_KIT_PHP-2015**.

Vous pouvez créer une Merge Request en cliquant sur **New merge request** en
haut à droite de l'écran ou directement en cliquant sur le lien retourné par
GitLab dans votre terminal.

.. note::
   Indiquez le temps que vous avez passé sur cette merge request, en utilisant
   la commande ``/spend`` en commentaire. Ce temps est déclaratif et purement
   informatif, cela permettra simplement au projet d'indiquer à terme le temps
   passé. Nous pourrons par exemple ainsi calculer le coût que cela aurait pu
   représenter hors contexte communautaire, ou estimer également le temps
   gagné grâce au SK PHP.

Prendre en compte les remarques après une revue de code
-------------------------------------------------------

La revue de code (de l'anglais code review) a pour objectif de trouver des bugs
ou des vulnérabilités potentielles ou de corriger des erreurs de conception
afin d'améliorer la qualité, la maintenabilité et la sécurité du logiciel.

Ainsi, après avoir créé une Merge request sur GitLab, un autre développeur
va devoir vérifier les modifications apportées.

Ses éventuelles remarques vont devoir être prises en compte et il faudra rendre
l'historique "propre".

Effectuer les modifications nécessaires sur les fichiers impactés par les
remarques de la revue de code sur la branche de la Merge Request

Une fois vos modifications effectuées, vous allez pouvoir réécrire l'histoire.

.. code-block:: sh

   git add <fichier>
   # Récupérer le hash du commit à modifier (format du type c3cedd0c4f93d7a13f)
   git log
   # Fusionner vos modifications avec le commit existant
   git commit --fixup <hash>
   # Réécrire proprement votre historique
   git rebase -i origin/master --autostash
   # Pousser votre travail sur le dépôt distant
   git push origin <branche> -f

.. warning::
   Attention, l'option --force (-f) va écraser l'ancien historique. Celle-ci
   est donc à utiliser avec précaution et seulement lors que vous êtes sûr
   de vous.

Pour en savoir plus, voir le `guide des bonnes pratiques <http://docs-projet.cnqd.cnamts.fr/docs/SK-PHP/latest/bonnes-pratiques.html#git>`_

.. _GitLab-issue-simple: https://gitlab.cnqd.cnamts.fr/groups/STARTER_KIT_PHP-2015/-/issues?label_name%5B%5D=Facile+%C3%A0+traiter+%2F+Premi%C3%A8re+contribution&scope=all&sort=popularity&state=opened
.. _GitLab-ajout-cle: https://gitlab.cnqd.cnamts.fr/profile/keys
.. _GitLab-tutorial-cle: https://gitlab.cnqd.cnamts.fr/help/ssh/README
