<?php

/**
 * Interface \Oz\Cache\CacheInterface describe the methods needed by
 * a cache service. Implementing theses methods will allow you to use
 * your cache service as you use \Oz\Cache.
 *
 * @package Cache
 * @author  zeropanic <zeropanic@myself.com>
 * @version  1.0
 * @namespace  Oz
 * @see  \Oz\Cache for more explanations on theses methods.
 */

namespace Oz\Cache;

interface CacheInterface
{
	/**
	 * @const ONE_MINUTE The number of seconds ine one minute.
	 */
	const ONE_MINUTE = 60;

	/**
	 * @const ONE_HOUR The number of seconds in one hour.
	 */
	const ONE_HOUR   = 3600;

	/**
	 * @const ONE_DAY The number of seconds in one day.
	 */
	const ONE_DAY    = 86400;

	/**
	 * @const ONE_WEEK The nmber of seconds in one week.
	 */
	const ONE_WEEK   = 604800;

	/**
	 * @const ONE_MONTH The number of seconds in one month (30 days).
	 */
	const ONE_MONTH	 = 2592000;

	/**
	 * @const ONE_YEAR The number of seconds ine one year (364 days).
	 */
	const ONE_YEAR 	 = 31449600;

	public function has($id, $lifetime);

	public function read($id, $lifetime);

	public function write($datas, $id, $lifetime = self::ONE_HOUR);

	public function startBuffer();

	public function stopBuffer($id, $lifetime);

	public function getCleanBuffer();

	public function deleteCache($id = '', $lifetime = null);
}

?>