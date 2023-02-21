<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008 Filip Procházka (filip@prochazka.su)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Kdyby\Translation\Caching;

/**
 * @internal
 */
class PhpFileStorage extends \Nette\Caching\Storages\FileStorage implements \Nette\Caching\Storage
{

	public string $hint;

	/**
	 * Additional cache structure
	 */
	private const FILE = 'file';
	private const HANDLE = 'handle';

	private const NS_SEPARATOR = "\x00";

	/**
	 * Reads cache data from disk.
	 */
	protected function readData(array $meta): mixed
	{
		return [
			'file' => $meta[self::FILE],
			'handle' => $meta[self::HANDLE],
		];
	}

	/**
	 * Returns file name.
	 */
	protected function getCacheFile(string $key): string
	{
		$cacheKey = substr_replace(
			$key,
			trim(strtr($this->hint, '\\/@', '.._'), '.') . '-',
			strpos($key, self::NS_SEPARATOR) + 1,
			0
		);

		return parent::getCacheFile($cacheKey) . '.php';
	}

}
