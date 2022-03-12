# Redis Cache for SMF
[![SMF 2.1](https://img.shields.io/badge/SMF-2.1-ed6033.svg?style=flat)](https://github.com/SimpleMachines/SMF2.1)
![License](https://img.shields.io/github/license/dragomano/redis-cache-for-smf)

* **Tested on:** PHP 7.4.28, Redis 5.3.7
* **Languages:** English, Russian

Make sure that Redis installed on your server.

`php.ini`:

```ini
extension = redis
...
session.save_handler = "redis"
session.save_path    = "tcp://127.0.0.1:6379"
```

`Modifications.english.php`:

```php
$txt['redisimplementation_cache'] = 'Redis caching';
$txt['cache_redis_settings'] = 'Redis settings';
$txt['cache_redis_servers'] = 'Redis servers';
$txt['cache_redis_servers_subtext'] = 'Example: 127.0.0.1:6379';
```

`Modifications.russian.php`:

```php
$txt['redisimplementation_cache'] = 'Redis';
$txt['cache_redis_settings'] = 'Настройки Redis';
$txt['cache_redis_servers'] = 'Сервера Redis';
$txt['cache_redis_servers_subtext'] = 'Например: 127.0.0.1:6379';
```

Copy `RedisImplementation.php` to `Sources/Cache/APIs`, then select *Redis caching* in settings.
