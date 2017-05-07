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

use LuizCesar\OlhoVivoAPI\Model\Coordinate;

/**
 * The Bus object.
 * 
 * @method string getId(void) @return bus unique Id;
 * @method bool isAdapt(void) @return true if vehicle is accessible.
 * @method Coordinate getCoord(void) @return the refence to the actual position of this bus.
 */
class Bus
{
    private $id;
    private $isAdapt;
    private $coord;
  
    public function __construct($id, $isAdapt = false, Coordinate $coord)
    {
        $this->id = (string)$id;
        $this->isAdapt = (bool)$isAdapt;
        $this->coord = $coord;
    }
  
    public function getId() : string
    {
        return $this->id;
    }
    public function isAdapt() : bool
    {
        return (bool)$this->isAdapt;
    }
    public function getCoord() : Coordinate
    {
        return $this->coord;
    }
}
