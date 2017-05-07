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

/**
 * A bus line.
 * 
 * @method string getCod(void) @return unique line code (different for each way).
 * @method bool isCircular(void) @return true if it is a one-way line.
 * @method string getCodSign(void) @return the visual line code (as seen on the sign).
 * @method int getWay(void) @return 1 for Primary->Secondary or 2 for way back.
 * @method string getType(void) @return '10' for std Line or one of
 * ['21','31','41'] for demand lines.
 * @method string getName1(void) @return Line's sign Name (shown if way == 1).
 * @method string getName2(void) @return Line's sign Name (shown if way == 2).
 * @method string getInfo(void) @return Line datails (if any), such as days served.
 * @method string getFullSignCode(void) @return formatted sign code as cccc-tt (c=>CodSign, t=>type)
 * @method string getActualSignName(void) @return way == 1 ? name1 : name 2
 */
class BusLine
{
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
      !preg_match('/^[0-9Nn][0-9]{2}[A-Za-z0-9]$/', $codSign) ||
      !preg_match('/^[1-2]$/', $way) ||
      !in_array($type, [10,21,31,41]) ||
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
    public function getActuralSignName() : string
    {
        return $this->way==1?$this->name1:$this->name2;
    }
}
