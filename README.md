# Redis Cache for SMF

[![SMF 2.1](https://img.shields.io/badge/SMF-2.1-ed6033.svg?style=flat)](https://github.com/SimpleMachines/SMF2.1)
[![SMF 3.0 Alpha](https://img.shields.io/badge/SMF-3.0_Alpha-ed2533.svg?style=flat)](https://github.com/SimpleMachines/SMF/tree/release-3.0)
![License](https://img.shields.io/github/license/dragomano/redis-cache-for-smf)

- **Tested on:** PHP 8.2.17, Redis 6.0.2
- **Languages:** English, Russian

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
$txt['cache_redis_username'] = 'Username';
$txt['cache_redis_password'] = 'Password';
```

`Modifications.russian.php`:

```php
$txt['redisimplementation_cache'] = 'Redis';
$txt['cache_redis_settings'] = 'Настройки Redis';
$txt['cache_redis_servers'] = 'Сервера Redis';
$txt['cache_redis_servers_subtext'] = 'Например: 127.0.0.1:6379';
$txt['cache_redis_username'] = 'Имя пользователя';
$txt['cache_redis_password'] = 'Пароль';
```

`Subs-Admin.php` (SMF 2.1) or `Config.php` (SMF 3.0), add before `'db_show_debug' => `:

```php
		'cache_redis_password' => [
			'text' => implode("\n", [
				'/**',
				' * Database password for when connecting with Redis',
				' *',
				' * @var string',
				' */',
			]),
			'default' => '',
			'type' => 'string',
			'is_password' => true,
		],
```

Copy `RedisImplementation.php` to `Sources/Cache/APIs`, then select _Redis caching_ in settings.
