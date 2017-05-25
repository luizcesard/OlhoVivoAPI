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

use LuizCesar\OlhoVivoAPI\Base\Patterns;

/**
 * A bus line.
 * 
 * @method string getCod(void) @return unique line code (different for each way).
 * @method bool isCircular(void) @return true if it is a one-way line.
 * @method string getCodSign(void) @return the visual line code (as seen on the sign).
 * @method int getWay(void) @return @const WAY_FIRST_LEG for Primary->Secondary or @const WAY_SECOND_LEG for way back.
 * @method string getType(void) @return '10' for std Line.
 * @method string getName1(void) @return Line's sign Name (shown if way == 1).
 * @method string getName2(void) @return Line's sign Name (shown if way == 2).
 * @method string getInfo(void) @return Line datails (if any), such as days served.
 * @method string getFullSignCode(void) @return formatted sign code as cccc-tt (c=>CodSign, t=>type)
 * @method string getActualSignName(void) @return way == 1 ? name1 : name 2
 */
class BusLine
{
	  
	const WAY_FIRST_LEG = 0;
	const WAY_SECOND_LEG = 1;
	
    private $cod;
    private $isCircular;
    private $codSign;
    private $way;
    private $type;
    private $name1;
    private $name2;
    private $info;
	
    public function __construct($codLine, $isCircular=false, $codSign, $way, $type, $name1, $name2, $info='')
    {
        if (!$codLine || !is_bool($isCircular) ||
      !preg_match(Patterns::CODE_SIGN, $codSign) ||
      !preg_match(Patterns::WAY, $way) ||
      !preg_match(Patterns::TYPE_SIGN,$type) ||
      !strlen($name1) || !strlen($name2)) {
            throw new \Exception("Failed to get right parameters on BusLine.");
        }
    
        $this->cod = (string)$codLine;
        $this->isCircular = $isCircular;
        $this->codSign = (string)$codSign;
        $this->way = $way;
        $this->type = (string)$type;
        $this->name1 = $name1;
        $this->name2 = $name2;
        $this->info = $info;
    }
  
    public function getCod() : string
    {
        return $this->cod;
    }
    public function isCircular() : bool
    {
        return $this->isCircular;
    }
    public function getCodSign() : string
    {
        return $this->codSign;
    }
    public function getWay() : int
    {
        return $this->way;
    }
    public function getType() : string
    {
        return $this->type;
    }
    public function getName1() : string
    {
        return $this->name1;
    }
    public function getName2() : string
    {
        return $this->name2;
    }
    public function getInfo() : string
    {
        return $this->info;
    }
  
    public function getFullSignCode() : string
    {
        return $this->codSign . '-' . $this->type;
    }
    public function getActualSignName() : string
    {
        return $this->way==self::WAY_FIRST_LEG?$this->name1:$this->name2;
    }
}
