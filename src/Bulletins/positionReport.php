<?php
/**
 * OlhoVivo-API package
 *
 * (c) Luiz César DS <luizcesard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LuizCesar\OlhoVivoAPI\Bulletins;

use LuizCesar\OlhoVivoAPI\Base\Coordinate;
use LuizCesar\OlhoVivoAPI\Base\Patterns;

/**
 * Reports a position at a given time.
 * 
 * @method DateTime getTime(void) @return the time of issue of this report.
 * @method Coordinate getPosition(void) @return a Coordinate object.
 */
class PositionReport
{
	private $time;
	private $position;
	
	/**
	 * @param time must comply with in ISO 8601 standard.
	 */
	public function __construct(string $time, Coordinate $position)
	{
		if(!preg_match(Patterns::TIME_ISO_8601,$time))
			throw new \Exception("Time must be on ISO 8601 pattern.");
		
		$this->time = new \DateTime($time, new \DateTimeZone('UTC'));
		$this->position = $position;
	}
	public function getTime() : \DateTime
	{
		return $this->time;
	}
	public function getPosition() : Coordinate
	{
		return $this->position;
	}
}
