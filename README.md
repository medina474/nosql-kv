> Une base de données Key-Value Store est le modèle le plus simple du paradigme NoSQL :
> - Chaque donnée est enregistrée sous forme d’une paire clé → valeur.
> - La clé est un identifiant unique (souvent une chaîne de caractères ou un hash).
> - La valeur peut être de tout type : texte, nombre, objet JSON, données binaire (image, vidéo, objet sérialisé).

Elles sont vues comme des tables de hachage persistantes et distribuées.

### Caractéristiques

- Simplicité : un accès direct par clé, sans relations complexes ni jointures.
- Performance élevée : très rapide en lecture et écriture (souvent en mémoire).
- Scalabilité horizontale : ajout de nœuds pour répartir les clés (partitionnement/sharding).
- Tolérance aux pannes : réplication des paires clé/valeur sur plusieurs nœuds.
- Schéma flexible : la valeur peut changer de format sans contrainte stricte.

### Cas d’usage

- Cache en mémoire : accélération des performances d’applications,
- Sessions utilisateur : stockage temporaire des informations de connexion,
- Courtier de messages : stocke des messages et notifications,

### Solutions

- [Memcached](https://memcached.org/)
- [Redis](https://redis.io/open-source/),
- [DragonFly](https://www.dragonflydb.io/),
- [Amazon DynamoDB](https://aws.amazon.com/fr/dynamodb/),
- [KeyDB](https://docs.keydb.dev/),
- [Valkey](https://valkey.io/).
- [Riak KV](https://riak.com/products/riak-kv/index.html)
- [Microsoft Garnet](https://microsoft.github.io/garnet/)

## Redis

Redis, qui signifie _Remote Dictionary Server_, est un magasin de structures de données open source en mémoire, ce qui signifie qu'au lieu de stocker les données sur un disque dur, il les stocke dans la RAM.

Il est principalement utilisé comme base de données, gestionnaire de cache et courtier de messages (message broker), et prend en charge diverses structures de données.

Pour communiquer avec le serveur Redis, les clients Redis utilisent un protocole appelé Redis Serialization Protocol (RESP). Bien que conçu spécifiquement pour Redis, ce protocole peut être utilisé pour d'autres projets logiciels client-serveur.

RESP est un compromis entre les considérations suivantes :
- Simple à implémenter,
- Rapide à analyser,
- Lisible par l'homme.

RESP peut sérialiser différents types de données, notamment des entiers, des chaînes et des tableaux. Il propose également un type spécifique aux erreurs. Un client envoie une requête au serveur Redis sous la forme d'un tableau de chaînes. Le contenu du tableau est la commande et ses arguments que le serveur doit exécuter. Le type de réponse du serveur est spécifique à la commande.

### Persistance

Redis propose plusieurs mécanismes de persistance, c’est-à-dire des façons de conserver les données en mémoire sur le disque afin de ne pas tout perdre en cas de redémarrage ou de panne. Redis étant avant tout une base in-memory, la persistance est optionnelle et peut être configurée selon les besoins.

#### RDB (Redis Database Backup)

Redis prend périodiquement un snapshot complet de l’ensemble des données et l’écrit dans un fichier binaire (dump.rdb).

Cela se fait selon une règle configurée (SAVE ou BGSAVE) : par exemple toutes les 5 minutes si au moins 100 modifications ont eu lieu.

- Format compact, facile à transférer pour des sauvegardes ou réplications,
- Redémarrage rapide,
- Possibilité de perte de données si un plantage intervient entre deux sauvegardes (durabilité partielle).

#### AOF (Append-Only File)

Redis enregistre chaque opération d’écriture dans un fichier journal. Lors d’un redémarrage, Redis rejoue ce log pour reconstruire l’état de la base.

- Durabilité beaucoup plus fine que RDB (peu ou pas de perte de données).
- Log lisible humainement.
- Fichier AOF plus gros que RDB.
- Redémarrage plus lent (il faut rejouer toutes les opérations).

Redis effectue aussi un rewrite périodique du fichier AOF pour éviter qu’il ne devienne trop gros.

#### Combinaison RDB + AOF

Redis peut utiliser les deux mécanismes en même temps.

-  RDB sert pour les sauvegardes rapides et la réplication.
-  AOF garantit une meilleure durabilité en cas de crash.

Au redémarrage, Redis privilégie le fichier AOF si disponible, car il est souvent plus récent.

#### Sans persistance (mode cache pur)

Il est possible de désactiver complètement la persistance (pas de RDB, pas d’AOF). Redis se comporte alors comme un cache mémoire pur (similaire à Memcached).

Les données sont perdues au redémarrage.

### Les commandes

#### Opérations classiques

*Ajouter une clé*. Les clés comme les valeurs peuvent contenir des caractères spéciaux.

```
SET username "Alice"
```

Il est possible de définir une condition d'insertion
- NX : défini la clé-valeur seulement si elle **n**'existe **pas**;
- XX : défini la clé-valeur uniquement **si elle existe** déjà.

Il est possible de définir une durée de vie à notre clé
- EX 10 : défini le nombre de secondes
- PX 1000 : défini le nombre de millisecondes
- KEEPTTL : lors d'une mise à jour, garde la durée de vie précedemment définie

```
SET username "Alice" XX EX 10
```

Le nommage des clés a son importance. Si Redis ne fixe aucune contrainte, des conventions largement acceptées et utilisées ont été adoptée par la communauté.

Il est possible d'émuler une table avec une clé primaire en utilisant des clés nommées comme _adherent:1_ par exemple. L'ajout de 'tables' liées peut se faire avec des suffixes comme _adherent:1:abonnements_.

Le partitionnement des clés peut se faire au niveau applicatif, si l'instance Redis est partagée par plusieurs application. Par exemple _AppClub:user:1_

**Lire une clé**

```
GET username
```

**Tester l'exitence d'une clé**

```
EXISTS username
```

**Lire la durée de vie**

```
TTL username
```

**Supprimer une clé**

```
DEL username
```

Ajouts multiples


```shell
docker exec -it nosql-kv_redis-master_1 redis-cli
```
