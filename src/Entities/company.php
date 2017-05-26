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
	
	/**
	 * Constants representing each actual Company.
	 */
	const SANTA_BRIGIDA = 'Santa Brígida';
	const GATO_PRETO 	= 'Gato Preto';
	const SPENCER 		= 'Spencer Transp Rod';
	const NORTE_BUSS 	= 'Norte Buss';
	const SAMBAIBA 		= 'Sambaíba';
	const VIP 			= 'VIP Transp Urb';
	const QUALIBUS 		= 'Qualibus';
	const TRANSUNIAO	= 'Transunião';
	const ETU 			= 'ETU EXPANDIR';
	const AMBIENTAL 	= 'Ambiental';
	const ALLIBUS 		= 'Allibus';
	const PESSEGO 		= 'Pêssego';
	const EXPRESS 		= 'Express Trans Urb';
	const VIA_SUL 		= 'Via Sul';
	const IMPERIAL 		= 'Imperial';
	const MOVE_BUSS 	= 'Move Buss';
	const CIDADE_DUTRA 	= 'Cidade Dutra';
	const TUPI 			= 'TUPI';
	const MOBI 			= 'Mobibrasil';
	const TRANSWOLFF 	= 'Transwolff';
	const A2 			= 'A2';
	const CAMPO_BELO 	= 'Campo Belo';
	const GATUSA 		= 'Gatusa';
	const TRANSKUBA 	= 'Transkuba';
	const TRANSPASS 	= 'TRANSPASS';
	const TRANSCAP		= 'TRANSCAP';
	const ALFA 			= 'Alfa';
	
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
