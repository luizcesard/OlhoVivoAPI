<?php
/**
 * OlhoVivo-API package
 *
 * (c) Luiz César DS <luizcesard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LuizCesar\OlhoVivoAPI\Model;

use LuizCesar\OlhoVivoAPI\Model\Bus;
use LuizCesar\OlhoVivoAPI\Model\Coordinate;

/**
 * Simple forecast of a bus arrival.
 * 
 * @method Bus getBus(void) @return the Bus that will arrive.
 * @method string getTime(void) @return the time at which the bus is forecasted.
 */
class BusForecast
{
    private $bus;
    private $time;
  
    public function __construct($time, Bus $bus)
    {
        $this->bus = $bus;
        $this->timeAssertion($time);
    }
  
    private function timeAssertion($time)
    {
        if (!preg_match('/[0-2]*[0-9]:[0-5][0-9]/', $time)) {
            throw new \Exception("Time must be formatted as: 00:00");
        }
        $this->time = $time;
    }
  
    public function getBus() : Bus
    {
        return $this->bus;
    }
    public function getTime() : string
    {
        return $this->time;
    }
}
