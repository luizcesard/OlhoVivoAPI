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

use LuizCesar\OlhoVivoAPI\Base\Area;
use LuizCesar\OlhoVivoAPI\Base\Coordinate;
use LuizCesar\OlhoVivoAPI\Base\Patterns;
use LuizCesar\OlhoVivoAPI\Bulletins\PositionReport;

/**
 * The Bus object.
 * 
 * @method string getId(void) @return bus unique Id;
 * @method bool isAcc(void) @return true if vehicle is accessible.
 * @method PositionReport getPosition(void) @return the refence to the PositionReport of this bus.
 * @method int getColor(void) @return the color constant of this Bus Area.
 */
class Bus
{
    private $id;
    private $isAcc;
    private $position;
	private $color;
  
    public function __construct($id, bool $isAcc = false, PositionReport $position)
    {
		if(!preg_match(Patterns::BUS_ID,$id))
			throw new \Exception("Bus: Invalid bus id");
		
        $this->id = (string)$id;
        $this->isAcc = (bool)$isAcc;
        $this->position = $position;
		$this->color = Area::getAreaColor($id);
		
    }
  
    public function getId() : string
    {
        return (string)$this->id;
    }
    public function isAdapt() : bool
    {
        return (bool)$this->isAdapt;
    }
    public function getCoord() : PositionReport
    {
        return $this->position;
    }
    public function getColor() : int
	{
		return $this->color;
	}
}
