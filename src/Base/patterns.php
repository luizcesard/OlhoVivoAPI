<?php
/**
 * OlhoVivo-API package
 *
 * (c) Luiz César DS <luizcesard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LuizCesar\OlhoVivoAPI\Base;

/**
 * Validation Patterns for constructors.
 * 
 */
abstract class Patterns
{
	const TIME = '/^[0-2]*[0-9]:[0-5][0-9]$/';
	const TIME_ISO_8601 = '/^20[1-9]{2}-(0[1-9]||1[0-2])-([0-2][0-9]||3[0-1])T([0-1][0-9]||2[0-3])(:[0-5][0-9]){2}(Z||[-\+](0[0-9]||1[0-2]):[0-5][0-9])$/';
	
	const SIGN = '[NnCc0-9][A-Za-z0-9][0-9][0-9A-Za-z]';
	const TYPE = '[1-5][0-9]*';
	const CODE_SIGN = '/^'.self::SIGN.'$/';
	const TYPE_SIGN = '/^'.self::TYPE.'$/';
	const ROUTE_ID = '/^'.self::SIGN.'-'.self::TYPE.'$/';
	
	const WAY_ID = '[0-1]';
	const WAY = '/^'.self::WAY_ID.'$/';
	const TRIP_ID = '/^'.self::SIGN.'-'.self::TYPE.'-'.self::WAY_ID.'$/';
	
	const AREA_ID = '[1-8]';
	const AREA = '/^'.self::AREA_ID.'$/';
	const BUS_ID = '/^'.self::AREA_ID.'[0-9]{3,4}$/';
}
