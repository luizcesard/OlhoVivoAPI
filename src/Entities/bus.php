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
use LuizCesar\OlhoVivoAPI\Entities\Company;

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
    public function isAcc() : bool
    {
        return (bool)$this->isAcc;
    }
    public function getCoord() : PositionReport
    {
        return $this->position;
    }
    public function getColor() : int
	{
		return $this->color;
	}
	public function getOwner() : string
	{
		switch((int)substr($this->id,0,2)){
			case 11:
				return Company::SANTA_BRIGIDA;
			case 12: case 82:
				return Company::GATO_PRETO;
			case 15: case 25:
				return Company::SPENCER;
			case 16: case 26:
				return Company::NORTE_BUSS;
			case 21: case 22: case 23:
				return Company::SAMBAIBA;
			case 31: case 32: case 33: case 73: case 74:
				return Company::VIP;
			case 35:
				return Company::QUALIBUS;
			case 36:
				return Company::TRANSUNIAO;
			case 39:
				return Company::ETU;
			case 41:
				return Company::AMBIENTAL;
			case 45:
				return Company::ALLIBUS;
			case 47:
				return Company::PESSEGO;
			case 48:
				return Company::EXPRESS;
			case 51: case 52: case 53:
				return Company::VIA_SUL;
			case 54:
				return Company::IMPERIAL;
			case 55:
				return Company::MOVE_BUSS;
			case 61:
				return Company::CIDADE_DUTRA;
			case 62:
				return Company::TUPI;
			case 63:
				return Company::MOBI;
			case 66: case 78:
				return Company::TRANSWOLFF;
			case 68:
				return Company::A2;
			case 72:
				return Company::CAMPO_BELO;
			case 76:
				return Company::GATUSA;
			case 77:
				return Company::TRANSKUBA;
			case 81:
				return Company::TRANSPASS;
			case 85:
				return Company::TRANSCAP;
			case 86:
				return Company::ALFA;
			default:
				return '';
		}
	}	
}
