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

# optional
session.save_handler = "redis"
session.save_path    = "tcp://127.0.0.1:6379"
```
