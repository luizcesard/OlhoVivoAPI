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
 * Bus exclusive pathway.
 * 
 * @method string getCod(void) @return busway unique id.
 * @method string getName(void) @return busway name.
 */
class Busway
{
    private $cod;
    private $name;
  
    public function __construct($id, $name)
    {
        $this->cod = (string)$id;
        $this->name = $name;
    }
  
    public function getCod() : string
    {
        return $this->cod;
    }
    public function getName() : string
    {
        return $this->name;
    }
}
