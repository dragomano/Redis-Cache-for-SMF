<?php

/**
 * RedisImplementation
 *
 * @package Redis Cache for SMF
 * @author Bugo https://dragomano.ru/lessons/redis-cache-for-smf
 * @copyright 2022 Bugo
 * @license https://opensource.org/licenses/BSD-3-Clause BSD
 *
 * @version 0.1
 */

namespace SMF\Cache\APIs;

use Redis;
use SMF\Cache\CacheApi;
use SMF\Cache\CacheApiInterface;

if (!defined('SMF'))
	die('No direct access...');

/**
 * @package CacheAPI
 * @see https://github.com/phpredis/phpredis
 */
class RedisImplementation extends CacheApi implements CacheApiInterface
{
	const CLASS_KEY = 'cache_redis';

	/** @var Redis */
	private $redis = null;

	public function isSupported($test = false)
	{
		global $cache_redis;

		$supported = class_exists('Redis');

		if ($test)
			return $supported;

		return parent::isSupported() && $supported && !empty($cache_redis);
	}

	public function connect()
	{
		global $db_persist, $cache_redis;

		$this->redis = new Redis();

		$servers = explode(',', $cache_redis);
		$port = 0;

		$connected = false;
		$level = 0;

		while (!$connected && $level < count($servers)) {
			++$level;

			$server = trim($servers[array_rand($servers)]);

			if (empty($server))
				continue;

			if (strpos($server, '/') !== false) {
				$host = $server;
			} else {
				$server = explode(':', $server);
				$host = $server[0];
				$port = isset($server[1]) ? $server[1] : 6379;
			}

			$connected = empty($db_persist) ? $this->redis->connect($host, $port) : $this->redis->pconnect($host, $port);
		}

		return $connected;
	}

	public function getData($key, $ttl = null)
	{
		$key = $this->prefix . strtr($key, ':/', '-_');

		$value = $this->redis->get($key);

		if ($value === false)
			return null;

		return $value;
	}

	public function putData($key, $value, $ttl = null)
	{
		$key = $this->prefix . strtr($key, ':/', '-_');

		return $ttl ? $this->redis->setEx($key, $ttl, $value) : $this->redis->set($key, $value);
	}

	public function cleanCache($type = '')
	{
		$this->invalidateCache();

		return $this->redis->flushAll();
	}

	public function quit()
	{
		return $this->redis->close();
	}

	public function cacheSettings(array &$config_vars)
	{
		global $txt, $context;

		if (!in_array($txt[self::CLASS_KEY . '_settings'], $config_vars)) {
			$config_vars[] = $txt[self::CLASS_KEY . '_settings'];
			$config_vars[] = array(
				self::CLASS_KEY,
				$txt[self::CLASS_KEY . '_servers'],
				'file',
				'text',
				0,
				'subtext' => $txt[self::CLASS_KEY . '_servers_subtext']);
		}

		if (!isset($context['settings_post_javascript']))
			$context['settings_post_javascript'] = '';

		if (empty($context['settings_not_writable']))
			$context['settings_post_javascript'] .= '
			$("#cache_accelerator").change(function (e) {
				var cache_type = e.currentTarget.value;
				$("#' . self::CLASS_KEY . '").prop("disabled", cache_type !== "RedisImplementation");
			});';
	}

	public function getVersion()
	{
		$info = $this->redis->info();

		if (empty($info))
			return false;

		return $info['redis_version'];
	}
}
