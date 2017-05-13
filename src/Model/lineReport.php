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

/**
 * Reports all actual buses for a line at a specific time
 * 
 * @method string getTime(void) @return this report issue time on '##:##' format.
 * @method Bus getBuses(void) @return each bus available.
 */
class LineReport
{
    private $time;
    private $buses;
  
    public function __construct($time, array $buses = [])
    {
        if (!preg_match('/[0-2]*[0-9]:[0-5][0-9]/', $time)) {
            throw new \Exception("Time must be formatted as: 00:00");
        }
  
        $this->time = $time;

        if (is_array($buses) && count($buses) > 0) {
            foreach ($buses as $bus) {
                if (get_class($bus) != 'LuizCesar\OlhoVivoAPI\Model\Bus') {
                    throw new \Exception("Line Report must have buses.");
                }
            }
        }
    
        $this->buses = $buses;
    }
  
    public function getTime() : string
    {
        return $this->time;
    }
    public function getBuses() : array
    {
        return $this->buses;
    }
}
