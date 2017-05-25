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

use LuizCesar\OlhoVivoAPI\Base\Patterns;
use LuizCesar\OlhoVivoAPI\Entities\Bus;
use LuizCesar\OlhoVivoAPI\Entities\BusLine;

/**
 * Reports all actual buses on one or more lines at a specific time
 * 
 * @method string getTime(void) @return this report issue time on '##:##' format.
 * @method array getBusesByLine(BusLine) @return an array of each bus available
 * by line.
 * @method array getBusLines(void) @return an array of all BusLine objects on
 * this report.
 * @method SplObjectStorage getAllVehicles(void) @return arrays of buses indexed
 * by BusLine.
 */
class LinesReport
{
    private $time;
    private $data;
	
	/**
	 * @param time a simple string in ##:## format.
	 * @param data a SplObjectStorage with BusLines and Buses.
	 */
    public function __construct($time, \SplObjectStorage $data)
    {
        if (!preg_match(Patterns::TIME, $time)) {
            throw new \Exception("Time must be formatted as: 00:00");
        }
        $this->time = $time;

		foreach($data as $busLine){
			if(get_class($busLine) !== 'LuizCesar\OlhoVivoAPI\Entities\BusLine')
				throw new \Exception("Lines Report: Report must be indexed by BusLine objects.");
			foreach($data[$busLine] as $bus)
                if (get_class($bus) != 'LuizCesar\OlhoVivoAPI\Entities\Bus') {
                    throw new \Exception("Lines Report must have buses.");
                }
        }
        $this->data = $data;
    }
  
    public function getTime() : string
    {
        return $this->time;
    }
    public function getBusesByLine(BusLine $line) : array
    {
		
		if(!$line) throw new \Exception("getBusesByLine: a BusLine must be given.");
		foreach($this->data as $aBusLine)
			if($line->getCod() === $aBusLine->getCod())
				return !is_null($this->data[$aBusLine]) ? $this->data[$aBusLine] : [];
		return [];
    }
    public function getBusLines() : array
	{
		$busLines = [];
		foreach($this->data as $busLine)
			$busLines[] = $busLine;
		return $busLines;
	}
	public function getAllVehicles() : \SplObjectStorage
	{
		return $this->data;
	}
}
