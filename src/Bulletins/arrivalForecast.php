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

/**
 * ArrivalForecast objects may represent:
 * a.all buses forecasted to arrive at a certain BusStop, by BusLine; OR
 * b.the arrival forecast for all buses from a specific line on all BusStop's
 * served by that line.
 */
class ArrivalForecast
{
    private $time;
    private $arrayMap;
	private $type;
	
    public const ARRIVALS_BY_BUSSTOP = 1;
    public const ARRIVALS_BY_BUSLINE = 2;
  
	/**
	 * 
	 * @param time must be a string the ##:## format
	 * @param objMap should have BusLine objects as key if @param type is
	 * ArrivalForecast::ARRIVALS_BY_BUSSTOP or BusStop objects as key if
	 * @param type is ArrivalForecast::ARRIVALS_BY_BUSLINE.
	 */
    public function __construct(string $time, \Splobjectstorage $objMap, $typeOfReport = self::ARRIVALS_BY_BUSSTOP)
    {
        if (!preg_match(Patterns::TIME, $time)) {
            throw new \Exception("Time must be formatted as: 00:00");
        }
        $this->time = $time;
		
		//$objMap keys type confirmation
        $classes = ['LuizCesar\OlhoVivoAPI\Entities\BusLine','LuizCesar\OlhoVivoAPI\Entities\BusStop'];
		foreach($objMap as $key)
			if (get_class($key) != $classes[$typeOfReport-1]) {
				$a = (explode('\\',$classes[$typeOfReport-1]))[3];
				throw new \Exception("Splobjectstorage keys must be a $a.");
			}
        $this->arrayMap = $objMap;
		$this->type = $typeOfReport;
    }
    
    /**
	 * @method string getTime(void)
	 * 
	 * @return the time of issue of this forecast in the format '##:##'
	 */
    public function getTime() : string
    {
        return $this->time;
    }
    
    /**
	 * @method int getType(void)
	 * 
	 * @return the type of this report:
	 * ArrivalForecast::ARRIVALS_BY_BUSSTOP or ArrivalForecast::ARRIVALS_BY_BUSLINE
	 */
    public function getType() : int
	{
		return $this->type;
	}
    
    /**
	 * @method SplObjectStorage getArrivalMap(void)
	 * 
	 * If it is an ARRIVALS_BY_BUSSTOP report, @return this object's
	 * SplObjectStorage with BusLine as key and an array of BusForecast
	 * as values.
	 * 
	 * If it is an ARRIVALS_BY_BUSLINE report, @return this object's
	 * SplObjectStorage with BusStop as key and an array of BusForecast
	 * as values.
	 */
    public function getArrivalsMap() : \SplObjectStorage
    {
        return $this->arrayMap;
    }
}
