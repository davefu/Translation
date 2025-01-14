<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008 Filip Procházka (filip@prochazka.su)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Kdyby\Translation\LocaleResolver;

use Kdyby\Translation\Translator;
use Nette\Http\IRequest;

class AcceptHeaderResolver implements \Kdyby\Translation\IUserLocaleResolver
{

	const ACCEPT_LANGUAGE_HEADER = 'Accept-Language';

	/**
	 * @var \Nette\Http\IRequest
	 */
	private $httpRequest;

	/**
	 * @param \Nette\Http\IRequest $httpRequest
	 */
	public function __construct(IRequest $httpRequest)
	{
		$this->httpRequest = $httpRequest;
	}

	/**
	 * Detects language from the Accept-Language header.
	 * This method uses the code from Nette\Http\Request::detectLanguage.
	 *
	 * @see https://github.com/nette/http/blob/0d9ef49051fba799148ef877dd32928a68731766/src/Http/Request.php#L294-L326
	 *
	 * @param \Kdyby\Translation\Translator $translator
	 * @return string|NULL
	 */
	public function resolve(Translator $translator)
	{
		$header = $this->httpRequest->getHeader(self::ACCEPT_LANGUAGE_HEADER);
		if (!$header) {
			return NULL;
		}

		$langs = [];
		foreach ($translator->getAvailableLocales() as $locale) {
			$langs[] = $locale;
			if (strlen($locale) > 2) {
				$langs[] = substr($locale, 0, 2);
			}
		}

		if (!$langs) {
			return NULL;
		}

		$s = strtolower($header);  // case insensitive
		$s = strtr($s, '_', '-');  // cs_CZ means cs-CZ
		rsort($langs);             // first more specific
		preg_match_all('#(' . implode('|', $langs) . ')(?:-[^\s,;=]+)?\s*(?:;\s*q=([0-9.]+))?#', $s, $matches);

		if (!$matches[0]) {
			return NULL;
		}

		$max = 0;
		$lang = NULL;
		foreach ($matches[1] as $key => $value) {
			$q = $matches[2][$key] === '' ? 1.0 : (float) $matches[2][$key];
			if ($q > $max) {
				$max = $q;
				$lang = $value;
			}
		}

		return $lang;
	}

}
