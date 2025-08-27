# Base de données Clés-Valeur

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
- Courtier de messages : stocke des messages et des notifications,

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

### L'environnement

L'environnement Docker est composé de 2 serveurs Redis : Un serveur maître (redis-master) et un autre pour la réplication de données (redis-replica).

Pour accéder au client Redis en ligne de commande, il faut exécuter la commande suivante, sur l'un ou l'autre des serveurs. Attention le serveur de réplication n'autorise que la lecture. Les modifications sont faites sur le serveur master.

```shell
docker exec -it nosql-kv_redis-master_1 redis-cli
```

### Les commandes

#### Opérations classiques

##### Ajouter une clé

Les clés comme les valeurs peuvent contenir des caractères spéciaux. Si la valeur contient des espaces il faut l'entourer de guillements.

```
SET username "Albert Einstein"
```

Il est possible de définir une condition d'insertion
- NX : défini la clé-valeur seulement si elle **n**'existe **pas**;
- XX : défini la clé-valeur uniquement **si elle existe** déjà.

Il est possible de définir une durée de vie à notre clé
- EX 10 : défini le nombre de secondes
- PX 1000 : défini le nombre de millisecondes
- KEEPTTL : lors d'une mise à jour, garde la durée de vie précedemment définie

```
SET username "Albert" XX EX 10
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

**Ajouts multiples**

Il faut utiliser les commandes `MSET` et `MGET`


```
MSET clef1 valeur1 clef2 valeur2
MGET clef1 clef2
```

**Incréments**

```
SET visites 1000
INCR visites
DECR visites
INCRBY visites 5
DECRBY visites 5f
```

### Listes

une liste est une structure de données ordonnée qui fonctionne comme une chaîne de valeurs (similaire à une file ou une pile). Chaque élément est une chaîne de caractères.

- Une liste Redis est ordonnée par l’ordre d’insertion.
- On peut y ajouter ou retirer des éléments au début (gauche) ou à la fin (droite).
- On peut accéder à une portion de la liste ou à des éléments précis par index.
- Redis stocke les listes en mémoire de façon compacte et efficace.

```
LPUSH nobel Einstein → ajoute un ou plusieurs éléments au début de la liste.
LPUSH nobel Marconi → ajoute un ou plusieurs éléments au début de la liste.
RPUSH nobel Becquerel Plank → ajoute un ou plusieurs éléments à la fin.
```

#### Lire des élements

```
LLEN nobel → renvoie la taille de la liste.
LINDEX nobel 3 → lit l’élément à un index donné (0 = premier).
LRANGE nobel 1 2 → renvoie une portion de la liste (indices inclusifs, -1 signifie jusqu'à la fin).
```

#### Retirer des éléments

```
LREM nobel 1 2 → Supprime des éléments
LPOP nobel → retire et renvoie le premier élément (gauche).
RPOP nobel → retire et renvoie le dernier élément (droite).
```

BLPOP et BRPOP sont des variantes qui bloquent en attente si la liste est vide (très utilisé pour faire une file de tâches).

#### Cas d’usage typiques

- File de messages (FIFO)
  - Les tâches sont ajouter dans la file d'attente avec RPUSH.
  - Des workers font un BLPOP pour traiter en FIFO.
- Historique récent (exemple : logs ou recherches)
  - LPUSH les nouvelles entrées.
  - On garde une taille fixe avec LTRIM (par ex. les 100 dernières actions).
- Pile (LIFO)
  - Avec LPUSH pour empiler et LPOP pour dépiler.

### Géospatial

```
GEOADD users 6.9362167 48.2899648 "Bernard" 6.943951 48.2891368 "Lenina" 6.9484328 48.2866295 "Mustafa" 6.9621793 48.2956319 "Helmholtz" 6.9685052 48.2967651 "Linda" 6.94559 48.2684914 "Winston" 6.9965543 48.2867593 "Julia" 6.9351951 48.3127686 "O'Brien"
```

GEODIST users Alice Bob km

GEORADIUS users 4.8357 45.7640 300 km WITHDIST

GEOPOS users Alice

GEOHASH users Alice

GEORADIUS users 6.9491129 48.2846556 1 KM

GEORADIUSBYMEMBER users Bernard 1 KM

GEOSEARCH users:geo FROMLONLAT 6.9491129 48.2846556 BYRADIUS 1500 m ASC COUNT 3

### Hashs

Les Hash sont des objets clés/valeurs

HSET user:1 name "Albert" age 30

HGET user:1 name

HGETALL user:1

### Sets

Les Sets sont des ensembles sans doublon. Ils permettent de stocker plusieurs valeurs.Les éléments des Sets sont appelés des membres.

#### Ajouter des membres

```
SADD tags redis database nosql
```

#### Cardinalité (nombre de membres)

```
SCARD tags
```

#### Lister les membres

```
SMEMBERS tags
```

#### Retirer un membre

```
SREM tags
```

#### Tester si un membre fait partie de l'ensemble

```
SISMEMBER tags "nosql"
```

Les Sets obéissent à la théorie des ensembles il est donc possible de combiner les ensembles entre eux.

SDIFF

SINTER

SUNION

### Sorted Sets

ZADD leaderboard 100 "Alice"

ZADD leaderboard 200 "Bob"

ZRANGE leaderboard 0 -1 WITHSCORES REV

### JSON

JSON.SET user:1 $ '{"id":1,"name":"Alice","age":30,"skills":["redis","docker"]}'
JSON.GET user:1 $.name
JSON.SET user:1 $.age 31
JSON.ARRAPPEND user:1 $.skills '"nosql"'
JSON.DEL user:1 $.age

### Transactions

```
MULTI
SET balance 100
INCR balance
DECR balance
EXEC
```

### Publication

Dans un premier terminal (abonné) :

SUBSCRIBE news

Dans un autre terminal (éditeur) :

PUBLISH news "Bonjour"
