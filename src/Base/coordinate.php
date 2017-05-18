<?php
/**
 * OlhoVivo-API package
 *
 * (c) Luiz César DS <luizcesard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LuizCesar\OlhoVivoAPI\Base;

/**
 * Geographical coordinates.
 * 
 * @method float getLat() @return latitude.
 * @method float getLon(void) @return longitude.
 */
class Coordinate
{
    private $lat;
    
    private $lon;
    
    public function __construct($lat, $lon)
    {
        $this->lat = (float) $lat;
        $this->lon = (float) $lon;
    }
    
    public function getLat() : float
    {
        return $this->lat;
    }
    public function getLon() : float
    {
        return $this->lon;
    }
}
