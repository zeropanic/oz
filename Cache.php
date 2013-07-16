<?php

/**
 * Class \Oz\Cache allows you to store or retrieve the content of a variable
 * or store the output of the buffer. It can store the same cache identifier
 * with differents lifetimes. An it can purge all cache, all entries for an
 * identifier (if one is specified) or only one special lifetime of the
 * identifier is both are supplied.
 * 
 * How-to example 1 (caching a variable):
 * Assuming $cache is an instance of \Oz\Cache.
 * $cache->write($datas, 'myIdentifier', $cache::ONE_HOUR * 3);
 * Will cache the content of $datas for 3 hours with the cache id 'myIdentifier'.
 * you can retrieve the content cached above by using:
 * $cache->read('myIdentifier', $cache::ONE_HOUR * 3);
 * You also can cache the same identifier with differents lifetimes like this:
 * $cache->write($datas, 'myIdentifier', $cache::ONE_WEEK);
 * Will cache the same content but for one week.
 * To retrieve it, dont forget to specify the correct lifetime:
 * $cache->read('myIndentifier', $cache::ONE_WEEK);
 *
 * How-to example 2 (using output buffering):
 * Assuming $cache is an instance of \Oz\Cache.
 * $cache->startBuffer();
 * var_dump($datas);
 * $cache->stopBuffer('dumpedVariable', $cache::ONE_MINUTE * 30);
 * Will cache the output of var_dump($datas) under the identifier
 * 'dumpedVariable' and the lifetime of thirty minutes.
 * You can retrieve the content of your cache identifier exactly
 * as example 1 above by using:
 * $cache->read('dumpedvariable', $cache::ONE_MINUTE * 30);
 * But you cannot cache 2 or more times the result of the output buffering
 * with differents lifetimes. To do so, look at example 3.
 *
 * How-to example 3 (multiple caching with output buffering):
 * Assuming $cache is an instance of \Oz\Cache, initialize the buffer.
 * $cache->startBuffer();
 * Output something, like a var_dump().
 * var_dump($datas);
 * Fetch the content of the buffer and clean it.
 * $content = $cache->getCleanBuffer();
 * Write the content with multiple lifetimes.
 * $cache->write($content, 'dumpedVar', $cache::ONE_WEEK);
 * $cache->write($content, 'dumpedVar', $cache::ONE_MONTH);
 * You can retrieve the content as explained above in example 1 and 2.
 *
 * How-to example 4 (purging the cache directory):
 * Assuming $cache is an instance of \Oz\Cache.
 * You can purge all cache entries by using:
 * $cache->deleteCache();
 * Or you can delete all entries for the supplied identifier like this:
 * $cache->deleteCache('myIdentifier');
 * Or at least, you can specify a lifetime to preserve
 * all others entries for the supplied identifier like this:
 * $cache->delete('myIdentifier', $cache::ONE_WEEK);
 * 
 * @package  Cache
 * @author  zeropanic <zeropanic@myself.com>
 * @version  1.0
 * @namespace Oz
 * @implements \Oz\Cache\CacheInterface
 * @uses  \Oz\Di\Dic our dependency injection container for services
 * @uses  \Oz\Cache\CacheInterface the interface for the Cache class
 * @uses  \Oz\Cache\Exception\Logic the LogicException of Cache package
 * @uses  \Oz\Cache\Exception\InvalidArgument the InvalidArgumentException
 *        of Cache package.
 */

namespace Oz;

use
	/* Static dependencies */
	\Oz\Di\Dic,
	\Oz\Cache\CacheInterface,
	/* Exceptions */
	\Oz\Cache\Exception\Logic,
	\Oz\Cache\Exception\InvalidArgument;

class Cache implements CacheInterface
{
	/**
	 * $cacheDir store the path of the cache directory.
	 * @var string
	 * @access protected
	 */
	protected $cacheDir = '/cache';

	/**
	 * $buffer is the buffer runing?
	 * @var boolean
	 * @access  protected
	 */
	protected $buffer = false;

	/**
	 * __construct class constructor set the cache directory.
	 * @access  public
	 */
	public function __construct()
	{
		$this->cacheDir = Dic::getInstance()->getService('app')->getPath('cache');
	}

	/**
	 * write store the content of a variable in a file
	 * @access  public
	 * @param  mixed $datas    variable to store
	 * @param  mixed $id       identifier to store the variable
	 * @param  int   $lifetime lifetime in seconds of the stored variable
	 * @return void
	 */
	public function write($datas, $id, $lifetime = self::ONE_HOUR)
	{
		$fileDir = $this->buildFilePath($id);
		$filePath = $fileDir.DS.md5($lifetime).'.php';

		if (!is_dir($fileDir)) {
			mkdir($fileDir, 0777, true);
		}

		if (is_file($filePath)) {
			unlink($filePath);
		}

		file_put_contents($filePath, $this->formatDatas($datas));
	}

	/**
	 * formatDatas format the data to store before putting theses datas to the file
	 * @access  protected
	 * @param  mixed $datas datas to store, can be a var of any type except ressource
	 * @return string       the formated fatas given as argument
	 * @see  http://php.net/manual/en/function.var-export.php
	 */
	protected function formatDatas($datas)
	{
		$formattedDatas  = '<?php'."\n";
		$formattedDatas .= 'defined('OZ_APP_RUNING') or die('Forbidden Access');'."\n";
		$formattedDatas .= 'return \''.var_export($datas).'\';'; 

		return $formattedDatas;
	}

	/**
	 * has check wether the cache entry is valid for the given identifier and lifetime
	 * @access  public
	 * @param  type  $id       identifier of the file
	 * @param  type  $lifetime lifetime in seconds of the file
	 * @return boolean         true if the file exists and is still valid, false if not
	 */
	public function has($id, $lifetime)
	{
		$fileDir = $this->buildFilePath($id);
		$filePath = $fileDir.DS.md5($lifetime).'.php';

		if (is_file($filePath)) {

			if ($this->isValid($filePath, $lifetime)) {
				return true;
			} else {
				unlink($filePath);
				return false;
			}

		} else {
			return false;
		}
	}

	/**
	 * read allow to retrieve the content of a file for the given identifier and lifetime
	 * @access  public
	 * @param  mixed $id       the identifier of the file you want to read
	 * @param  int   $lifetime the lifetime in seconds of the the file you want to read
	 * @throws  \Oz\Cache\Exception\InvalidArgument If the identifier
	 *          and/or the lifetime is invalid
	 * @return mixed           the content of the file if it exists
	 */
	public function read($id, $lifetime)
	{
		$fileDir  = $this->buildFilePath($id);
		$filePath = $fileDir.DS.md5($lifetime).'.php';

		if (is_file($filePath)) {
			throw new InvalidArgument('Impossible to find the file '.$filePath
				.' associated to the identifier '.$id.' and the lifetime '.$lifetime);
		}

		return include $filePath;
	}

	/**
	 * isValid check whether a file is valid or not for the given path and lifetime
	 * @access  protected
	 * @param  string  $filePath path to the file to check
	 * @param  int  	 $lifetime description
	 * @return boolean           true if the file is valid, false if not
	 */
	protected function isValid($filePath, $lifetime)
	{
		$filemtime = filemtime($filePath);
		return ((time() - $filemtime) < $lifetime) ? true : false;
	}

	/**
	 * buildFilePath build the path of the parent directory for the given id.
	 * @access  protected
	 * @param  mixed $id identifier of the cache entry.
	 * @return string    the path of the parent directory for the given id.
	 */
	protected function buildFilePath($id)
	{
		$idMd5 = md5($id);
		$idLength = round(strlen($id) / 2);

		$path = $this->cacheDir.DS.chunk_split($idMd5, $idLength, DS);

		return rtrim($path, DS);
	}

	/**
	 * deleteCache delete all cache entries if no identifier and lifetime are given
	 * If one identifier is given, it will delete all entries for this identifier,
	 * except if a lifetime is specified then only the entry corresponding with
	 * the identifier and the lifetime will be deleted.
	 * @param  string $id       optional: the identifier you want to delete.
	 * @param  mixed  $lifetime optional: a lifetime (saving others lifetimes).
	 * @return void
	 */
	public function deleteCache($id = '', $lifetime = null)
	{
		if (empty($id) && is_null($lifetime)) {
			$this->purgeDirectory($this->cacheDir);
		} else {
			if (!empty($id)) {
			
				if (!is_null($lifetime)) {
					$filePath = $this->buildFilePath($id).DS.md5($lifetime).'.php';
					if (is_file($filePath)) {
						unlink($filePath);
					}
				} else {
					$this->purgeDirectory($this->buildFilePath($id));
				}

			} else {
				$this->purgeDirectory($this->cacheDir);
			}
		}
	}

	/**
	 * purgeDirectory recursively purge the given directory's path.
	 * @access  protected
	 * @param  string $dir directory's path to purge.
	 * @throws  \Oz\Cache\Exception\InvalidArgument If the given directory
	 *          dont exists.
	 * @return void
	 */
	protected function purgeDirectory($dir)
	{
		if (!is_dir($dir)) {
			throw new InvalidArgument('Impossible to find specified direcotry '.$dir);
		}
 
		$iterator = new \RecursiveDirectoryIterator($dir,\FilesystemIterator::SKIP_DOTS);
		 
		foreach(new \RecursiveIteratorIterator($iterator) as $file) {
			if (is_dir($file->getPathname())) {
				rmdir($file->getPathname());
			} else {
				unlink($file->getPathname());
			}
		}
	}

	/**
	 * startBuffer starts the buffer.
	 * @access  public
	 * @see http://php.net/manual/en/function.ob-start.php
	 * @return void
	 */
	public function startBuffer()
	{
		ob_start();
		$this->buffer = true;
	}

	/**
	 * stopBuffer stop the buffer and stores the content with the
	 * given identifier and lifetime.
	 * @access  public
	 * @param  mixed $id       the identifier for storing the
	 *                         content of the output buffering
	 * @param  int   $lifetime the lifetime for storing the
	 *                         content of the output buffering
	 * @throws  \Oz\Cache\Exception\Logic If the output buffering
	 *          	wasnt initialized with \Oz\Cache::starBuffer()
	 * @return void
	 */
	public function stopBuffer($id, $lifetime = self::ONE_HOUR)
	{
		if (!$this->buffer) {
			throw new Logic('You must start the buffer with
				\Oz\Cache::startBuffer() before trying to stop it.');
		}

		$content = ob_get_clean();
		$this->write($content, $id, $lifetime);
		$this->buffer = false;
	}

	/**
	 * getCleanBuffer allows you to fetch the content and the buffer
	 * and clean it at same time. This is useful when you want to
	 * multiple-caching the content of the buffer with differents
	 * lifetimes.
	 * @access  public
	 * @throws  \Oz\Cache\Exception\InvalidArgument If the buffer
	 *          wasnt initialized with \Oz\Cache::startBuffer().
	 * @return mixed content of the buffer.
	 */
	public function getCleanBuffer()
	{
		if (!$this->buffer) {
			throw new Logic('You must start the buffer with
				\Oz\Cache::startBuffer() before trying to stop it.');
		}

		$this->buffer = false;
		return ob_get_clean();
	}
}

?>