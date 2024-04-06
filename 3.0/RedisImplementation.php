<?php declare(strict_types=1);

/**
 * RedisImplementation
 *
 * @package Redis Cache for SMF
 * @author Bugo https://dragomano.ru/lessons/redis-cache-for-smf
 * @copyright 2022-2024 Bugo
 * @license https://opensource.org/licenses/BSD-3-Clause BSD
 *
 * @version 0.2
 */

namespace SMF\Cache\APIs;

use Redis;
use RedisException;
use SMF\Cache\CacheApi;
use SMF\Cache\CacheApiInterface;
use SMF\Config;
use SMF\Lang;
use SMF\Utils;

if (!defined('SMF'))
	die('No direct access...');

/**
 * @package CacheAPI
 * @see https://github.com/phpredis/phpredis
 */
class RedisImplementation extends CacheApi implements CacheApiInterface
{
	public const CLASS_KEY = 'cache_redis';

	private ?Redis $redis = null;

	private array $servers;

	private int $port = 6379;

	/**
	 * {@inheritDoc}
	 */
	public function __construct()
	{
		$this->servers = array_map(
			function ($server) {
				if (str_contains($server, '/')) {
					return [$server, 0];
				}

				$server = explode(':', $server);

				return [$server[0], isset($server[1]) ? (int) $server[1] : $this->port];
			},
			explode(',', Config::$cache_redis),
		);

		parent::__construct();
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSupported(bool $test = false): bool
	{
		$supported = class_exists('Redis');

		if ($test) {
			return $supported;
		}

		return parent::isSupported() && $supported && !empty($this->servers);
	}

	/**
	 * {@inheritDoc}
	 * @throws RedisException
	 */
	public function connect(): bool
	{
		$this->redis = new Redis();

		$connected = false;
		$level = 0;

		while (!$connected && $level < count($this->servers)) {
			++$level;

			$server = $this->servers[array_rand($this->servers)];

			if (empty($server[0])) {
				continue;
			}

			$server = $server[0];

			if (str_contains($server[0], '/')) {
				$host = $server[0];
			} else {
				$server = explode(':', $server);
				$host = $server[0];
				$this->port = $server[1] ?? $this->port;
			}

			if (empty(Config::$db_persist)) {
				$connected = $this->redis->connect($host, $this->port);
			} else {
				$connected = $this->redis->pconnect($host, $this->port);
			}
		}

		return $connected;
	}

	/**
	 * {@inheritDoc}
	 * @throws RedisException
	 */
	public function getData(string $key, ?int $ttl = null): mixed
	{
		$key = $this->prefix . strtr($key, ':/', '-_');

		$value = $this->redis->get($key);

		if ($value === false) {
			return null;
		}

		return $value;
	}

	/**
	 * {@inheritDoc}
	 * @throws RedisException
	 */
	public function putData(string $key, mixed $value, ?int $ttl = null): mixed
	{
		$key = $this->prefix . strtr($key, ':/', '-_');

		return $ttl ? $this->redis->setEx($key, $ttl, $value) : $this->redis->set($key, $value);
	}

	/**
	 * {@inheritDoc}
	 * @throws RedisException
	 */
	public function quit(): bool
	{
		return $this->redis->close();
	}

	/**
	 * {@inheritDoc}
	 * @throws RedisException
	 */
	public function cleanCache($type = ''): bool
	{
		$this->invalidateCache();

		return $this->redis->flushAll();
	}

	/**
	 * {@inheritDoc}
	 */
	public function cacheSettings(array &$config_vars): void
	{
		if (!in_array(Lang::$txt[self::CLASS_KEY . '_settings'], $config_vars)) {
			$config_vars[] = Lang::$txt[self::CLASS_KEY . '_settings'];
			$config_vars[] = array(
				self::CLASS_KEY,
				Lang::$txt[self::CLASS_KEY . '_servers'],
				'file',
				'text',
				0,
				'subtext' => Lang::$txt[self::CLASS_KEY . '_servers_subtext']);
		}

		if (!isset(Utils::$context['settings_post_javascript'])) {
			Utils::$context['settings_post_javascript'] = '';
		}

		if (empty(Utils::$context['settings_not_writable'])) {
			Utils::$context['settings_post_javascript'] .= '
			$("#cache_accelerator").change(function (e) {
				var cache_type = e.currentTarget.value;
				$("#' . self::CLASS_KEY . '").prop("disabled", cache_type !== "RedisImplementation");
			});';
		}
	}

	/**
	 * {@inheritDoc}
	 * @throws RedisException
	 */
	public function getVersion(): string|bool
	{
		if (!is_object($this->redis)) {
			return false;
		}

		$info = $this->redis->info();

		if (empty($info)) {
			return false;
		}

		return $info['redis_version'];
	}
}
