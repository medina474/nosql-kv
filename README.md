> Une base de données Key-Value Store est le modèle le plus simple du paradigme NoSQL :
> - Chaque donnée est enregistrée sous forme d’une paire clé → valeur.
> - La clé est un identifiant unique (souvent une chaîne de caractères ou un hash).
> -  La valeur peut être de tout type : texte, nombre, objet JSON, données binaire (image, vidéo, objet sérialisé).

Elles sont vues comme des tables de hachage persistantes et distribuées.

### Caractéristiques principales :

- Simplicité : un accès direct par clé, sans relations complexes ni jointures.
- Performance élevée : très rapide en lecture et écriture (souvent en mémoire).
- Scalabilité horizontale : ajout de nœuds pour répartir les clés (partitionnement/sharding).
- Tolérance aux pannes : réplication des paires clé/valeur sur plusieurs nœuds.
- Schéma flexible : la valeur peut changer de format sans contrainte stricte.

### Les principales solutions :

- [Redis](https://redis.io/open-source/),
- [Amazon DynamoDB](https://aws.amazon.com/fr/dynamodb/),
- [KeyDB](https://docs.keydb.dev/),
- [DragonFly](https://www.dragonflydb.io/),
- [Valkey](https://valkey.io/).
- [Riak KV](https://riak.com/products/riak-kv/index.html)
- [Microsoft Garnet](https://microsoft.github.io/garnet/)
- [Memcached](https://memcached.org/)

### Cas d’usage :

- Cache en mémoire : accélération des performances d’applications,
- Sessions utilisateur : stockage temporaire des informations de connexion,

### Redis

Redis, qui signifie _Remote Dictionary Server_, est un magasin de structures de données open source en mémoire, ce qui signifie qu'au lieu de stocker les données sur un disque dur, il les stocke dans la RAM.

Il est principalement utilisé comme base de données, gestionnaire de cache et courtier de messages (message broker), et prend en charge diverses structures de données.

Pour communiquer avec le serveur Redis, les clients Redis utilisent un protocole appelé Redis Serialization Protocol (RESP). Bien que conçu spécifiquement pour Redis, ce protocole peut être utilisé pour d'autres projets logiciels client-serveur.

RESP est un compromis entre les considérations suivantes :
- Simple à implémenter,
- Rapide à analyser,
- Lisible par l'homme.

RESP peut sérialiser différents types de données, notamment des entiers, des chaînes et des tableaux. Il propose également un type spécifique aux erreurs. Un client envoie une requête au serveur Redis sous la forme d'un tableau de chaînes. Le contenu du tableau est la commande et ses arguments que le serveur doit exécuter. Le type de réponse du serveur est spécifique à la commande.
