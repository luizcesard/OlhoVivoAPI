<?php
/**
 * OlhoVivo-API package
 *
 * (c) Luiz César DS <luizcesard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LuizCesar\OlhoVivoAPI\Entities;

use LuizCesar\OlhoVivoAPI\Base\Coordinate;

/**
 * A bus stop.
 * 
 * @method Coordinate getCoord(void) @return stop location.
 * @method string getCod(void) @return stop unique id.
 * @method string getName(void) @return stop name or address.
 * @method string getRef(void) @return stop address references (if available).
 * @method mixed getTimeTable(void) @return SplObjectStorage or null.
 * @method setTimeTable(SplObjectStorage) @return true if timeTable is set.
 */
class BusStop
{
    private $coord;
    private $cod;
    private $na;
    private $ref;
	private $timeTable;
    
	/**
	 * @param timeTable is not obtained via the standard API, but is here for
	 * compatibility when used together with GTFS tables.
	 */
    public function __construct($stop_id, $stop_name, Coordinate $coord, $stop_ref = '', $timeTable = null)
    {
		if(!is_numeric($stop_id)) throw new \Exception("Invalid stop id.");
        $this->coord = $coord;
        $this->cod = (string)$stop_id;
        $this->na = $stop_name;
        $this->ref = $stop_ref;
		if (get_class($timeTable) == 'SplObjectStorage') $this->setTimeTable($timeTable);
    }
    
    public function getCoord() : Coordinate
    {
        return $this->coord;
    }
    public function getCod() : string
    {
        return $this->cod;
    }
    public function getName() : string
    {
        return $this->na;
    }
    public function getRef() : string
    {
        return $this->ref;
    }
    public function getTimeTable()
	{
		return $this->timeTable;
	}
	
	/**
	 * @param timeTable has BusLine as keys and an array of time strings as values
	 */
	public function setTimeTable(\SplObjectStorage $timeTable) : bool
	{
		if (!$timeTable || get_class($timeTable) !== 'SplObjectStorage' ||
			!(count($timeTable)>0))
			return false;
		
		foreach($timeTable as $key)
			if (get_class($key) != 'LuizCesar\OlhoVivoAPI\Entities\BusLine')
				return false;
		
		$this->timeTable = $timeTable;
		return true;
	}
}
