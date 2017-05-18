<?php
/**
 * OlhoVivo-API package
 *
 * (c) Luiz César DS <luizcesard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LuizCesar\OlhoVivoAPI\Bulletins;

use LuizCesar\OlhoVivoAPI\Base\Area;
use LuizCesar\OlhoVivoAPI\Base\Patterns;
use LuizCesar\OlhoVivoAPI\Entities\Company;

/**
 * CompaniesReport lists the list of all companies that serves transport by
 * Area.
 * 
 * @method string getReportTime(void) @return the time of issue of this report.
 * @method array getCompaniesByArea(int One_of_Area_constans) @return an array of
 * Companies or an array of areas and companies.
 * @method array getCompanies(void) @return an array of all companies.
 */
class CompaniesReport
{
	private $time;
	private $companiesByArea;
	
	/**
	 * 
	 * @param time must be a string the ##:## format
	 * @param companiesByArea must be an array indexed from ONE to Area::NUM_AREAS
	 * containing arrays of Companies in each index.
	 */
	public function __construct(string $time, array $companiesByArea)
	{
		if(!preg_match(Patterns::TIME,$time) || 
			count($companiesByArea) != Area::NUM_AREAS)
			throw new \Exception("Companies Report: Wrong data for construction.");
		
		foreach($companiesByArea as $key=>$areaPool)
			if(!($key>=1&&$key<=Area::NUM_AREAS))
				throw new \Exception("CompaniesReport: Invalid area.");
			foreach($areaPool as $company)
				if(get_class($company) !== 'LuizCesar\OlhoVivoAPI\Entities\Company')
					throw new \Exception("Companies Report: array must be have Company objects.");
		
		$this->time = $time;
		$this->companiesByArea = $companiesByArea;
	}
	
	public function getReportTime() : string
	{
		return $this->time;
	}
	public function getCompaniesByArea($area = 0) : array
	{
		if($area === 0)
			return $this->companiesByArea;
		else if($area > 0 && $area<=Area::NUM_AREAS)
			return $this->companiesByArea[$area];
		else
			throw new \Exception("Invalid Area.");
	}
	/**
	 * Note that a Company may appear more than one time if it runs on more than
	 * one city Area.
	 */
	public function getCompanies() : array
	{
		$companies = [];
		$companiesId = [];
		foreach($companiesByArea as $area)
			$companies = array_merge($companies,$area);
		return $companies;
	}
}
