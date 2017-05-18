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

/**
 * A Public transport company that runs in some Area.
 * 
 * @method ?int getOperationAreaId(void) @return the area code in which this 
 * company runs.
 * @method mixed getId(void) @return this company unique identifier.
 * @method string getName(void) @return the name of this company.
 */
class Company
{
	private $operationAreaId;
	private $id;
	private $name;
	
	public function __construct(int $operationAreaId = null, $id, string $name)
	{
		if(!($id && is_numeric($id)) && strlen($name) > 0)
			throw new \Exception("Invalid arguments for an Operator.");
		
		$this->operationAreaId = $operationAreaId;
		$this->id = $id;
		$this->name = $name;
	}
	
	public function getOperationAreaId() : ?int
	{
		return $this->operationAreaId;
	}
	public function getId()
	{
		return $this->id;
	}
	public function getName() : string
	{
		return $this->name;
	}
}
