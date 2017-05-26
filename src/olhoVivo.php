<?php
/**
 * OlhoVivo-API package
 *
 * (c) Luiz César DS <luizcesard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LuizCesar\OlhoVivoAPI;


define('TOKEN', 'api_key'); //OlhoVivo Token
define('BASE_URI','http://api.olhovivo.sptrans.com.br/v2.1/');

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use LuizCesar\OlhoVivoAPI\Base\Area;
use LuizCesar\OlhoVivoAPI\Base\Coordinate;
use LuizCesar\OlhoVivoAPI\Base\Patterns;
use LuizCesar\OlhoVivoAPI\Bulletins\ArrivalForecast;
use LuizCesar\OlhoVivoAPI\Bulletins\BusForecast;
use LuizCesar\OlhoVivoAPI\Bulletins\CompaniesReport;
use LuizCesar\OlhoVivoAPI\Bulletins\LinesReport;
use LuizCesar\OlhoVivoAPI\Bulletins\PositionReport;
use LuizCesar\OlhoVivoAPI\Entities\Bus;
use LuizCesar\OlhoVivoAPI\Entities\BusLine;
use LuizCesar\OlhoVivoAPI\Entities\BusStop;
use LuizCesar\OlhoVivoAPI\Entities\Busway;
use LuizCesar\OlhoVivoAPI\Entities\Company;

class OlhoVivo
{
	const ALL_LANES   = ''          ;
	const BUSWAYS     = 'Corredor'  ;
	const OTHER_LANES = 'OutrasVias';
	const DIST_CENTER = 'BC'        ;
	const CENTER_DIST = 'CB'        ;

	private static $instance = null;
	
    /**
     * Guzzle Client object
	 * When correctly iniciated by the initialize method, stores session cookies.
     *
     * @var \GuzzleHttp\Client
     */
    private $client;
    
	//Singleton Client.
    protected function __construct()
    {
        $this->initialize();
    }
    private function __clone()
    {
	}
	private function __wakeup()
	{
	}

	public static function getInstance()
	{
		if(is_null(static::$instance))
			self::$instance = new OlhoVivo();
		
		return self::$instance;
	}
	
    /**
	 * This method is ran on object construction and in the case of the session
	 * expires.
	 * 
	 * @return 1 for success or 0 for fail;
	 * @throw Exception in case of HTTP Request/Response exception, wrong token
	 * or server fail to begin a session.
	 */
    private function initialize()
    {
        $return = 0;
        try {
            $this->client = new Client([
                'base_uri' => BASE_URI,
                'timeout' => 2.0,
				'cookies' => true //shared session for this client
            ]);
            
            $login = $this->client->request(
                'POST',
                'Login/Autenticar',
                ['query' => ['token' => TOKEN]]
                );
            if (!(json_decode($login->getBody()))) {
                throw new \Exception("Failed to login with this token.");
            } elseif (!($login->hasHeader('Set-Cookie'))) {
                throw new \Exception("Server didn't set credentials.");
            }

            $return++;
        } catch (RequestException $e) {
            throw new \Exception("HTTP request/response error: {$e->getMessage()}");
        } finally {
            return $return;
        }
    }
        
    private function execute($uri, $params = [], bool $decodeAsJson = true)
    {
        try {
            do {
				$request = ($this->client->request(
                    'GET',
                    $uri,
                    count($params)>0? ['query' => $params]:[]
                ))->getBody();
                $decoded = json_decode($request,true);
            } while (isset($decoded['Message']) && $this->initialize());
            return $decodeAsJson === true ? $decoded : $request;
        } catch (RequestException $e) {
			throw new \Exception("HTTP request/response error: {$e->getMessage()}");
        }
    }
    
    /**
	 * Search for BusLines.
	 * 
	 * @param searchString should be any string of line name or codes.
	 * @param way (optional) may by BusLine constans: WAY_FIRST_LEG or WAY_SECOND_LEG
	 * 
	 * @return an array of BusLine objects with possible matches.
	 */
    public function seekLines($searchString, int $way = null) : array
    {
		if(is_int($way) && 
			!($way == BusLine::WAY_FIRST_LEG || $way == BusLine::WAY_SECOND_LEG))
			throw new \Exception("Way must be one of BusLine constants.");
        $result = [];
		$params['termosBusca'] = $searchString;
		if(is_int($way)) $params['sentido'] = $way + 1;
        foreach ($this->execute('Linha/Buscar', $params) as $line) {
            $result[] = new BusLine(
                $line['cl'],
                $line['lc'],
                $line['lt'],
                $line['sl'] - 1, //get coherent
                $line['tl'],
                $line['tp'],
                $line['ts']
            );
        }
        return $result;
    }
    
    /**
	 * Search for a <e>Busways's</e> Bus Stop.
	 *  
	 * @param searchString should be address, name or line reference.
	 * @return an array of BusStop objects.
	 */
    public function seekBusStops($searchString) : array
    {
        return $this->busStopFromArray(
            $this->execute('Parada/Buscar',
                            ['termosBusca' => $searchString]
        ));
    }
    
    /**
	 * Returns all Bus Stops served by a given Bus Line.
	 * 
	 * @param BusLine object.
	 * @return an array of BusStop objects.
	 **/
    public function seekBusStopsByLine(BusLine $line) : array
    {
        return $this->busStopFromArray(
            $this->execute('Parada/BuscarParadasPorLinha',
                            ['codigoLinha' => $line->getCod()]
        ));
    }
    
    /**
	 * Returns all Bus Stops that are part of a given Busway.
	 * 
	 * @param Busway object.
	 * @return an array of BusStop objects.
	 */    
    public function seekBusStopsByBusway(Busway $busway) : array
    {
        return $this->busStopFromArray(
            $this->execute('Parada/BuscarParadasPorCorredor',
                            ['codigoCorredor' => $busway->getCod()]
        ));
    }
    
    private function busStopFromArray(array $busArray) : array
    {
        $busStopsArray = [];
        foreach ($busArray as $stop) {
            $busStopsArray[] = new BusStop(
                $stop['cp'],
                $stop['ed'],
                new Coordinate($stop['py'], $stop['px'],
                $stop['np'])
            );
        }
        return $busStopsArray;
    }
    
    /**
	 * Get all city's busways.
	 * 
	 * @return array of Busway objects.
	 */
    public function getBusways() : array
    {
        $result = [];
        foreach ($this->execute('Corredor') as $busway) {
            $result[] = new Busway(
                $busway['cc'],
                $busway['nc']
            );
        }
        return $result;
    }
        
    /**
	 * Get a CompaniesReport for the actual time.
	 * 
	 * It sounds strange to issue a report for a static list, but the SPTrans
	 * implemented this way.
	 * 
	 * @return CompaniesReport object.
	 */
    public function getCompaniesReport() : CompaniesReport
	{
		$response = $this->execute('Empresa');
		
		$companiesByArea = [];
		foreach($response['e'] as $areaPool){
			$i = $areaPool['a'];
			foreach($areaPool['e'] as $company){
				$companiesByArea[$i][]=new Company(
					$company['a'],
					$company['c'],
					$company['n']
				);
			}
		}
		return new CompaniesReport($response['hr'],$companiesByArea);
	}
	
    /**
	 * Get a LineReport for the actual time for a given BusLine.
	 * 
	 * @param BusLine object.
	 * @return LinesReport object.
	 */
    public function getLinesReport(Company $company=null, BusLine $line = null) : LinesReport
    {
		$params = [];
		if($line) $params['codigoLinha'] = $line->getCod();
		if($company){
			$params['codigoEmpresa'] = $company->getId();
			$response = $this->execute('Posicao/Garagem', $params);
		} else if($line){
			$response = $this->execute('Posicao/Linha',$params);
		} else{
			$response = $this->execute('Posicao', $params);
		}

        $data = new \SplObjectStorage();
        if($line && !$company){
			foreach ($response['vs'] as $bus) {
				$buses[] = new Bus(
					$bus['p'],
					$bus['a'],
					new PositionReport(
						$bus['ta'],
						new Coordinate($bus['py'], $bus['px'])
					)
				);
			}
			$data[$line] = $buses;
		} else {
			foreach($response['l'] as $line){
				$busLine = new BusLine(
					$line['cl'],
					false,
					substr($line['c'],0,4),
					$line['sl'] - 1, //get coherent
					substr($line['c'],5,strlen($line['c'])-5),
					$line['lt0'],
					$line['lt1']
				);
				foreach ($line['vs'] as $bus) {
				$buses[] = new Bus(
						$bus['p'],
						$bus['a'],
						new PositionReport(
							$bus['ta'],
							new Coordinate($bus['py'], $bus['px'])
						)
					);
				}
				$data[$busLine] = $buses;
			}
		}		
        return new LinesReport($response['hr'], $data);
    }
    
    /**
	 * Get the most recent ArrivalForecast for a given BusLine on a given
	 * BusStop.
	 * 
	 * @param BusLine object.
	 * @param BusStop object.
	 * 
	 * @return ArrivalForecast object.
	 */
    public function getArrivalForecastByLineAndStop(BusLine $line, BusStop $stop) : ArrivalForecast
    {
        $response = $this->execute('Previsao',
                                ['codigoParada' => $stop->getCod(),
                                 'codigoLinha'  => $line->getCod()
                                ]
        );
        $forecast = new \SplObjectStorage();
        if(isset($response['p']))
			foreach ($response['p']['l'] as $aLine) {
				$buses = [];
				foreach($aLine['vs'] as $busForecast){
					$buses[] = new BusForecast(
						$busForecast['t'],
						new Bus($busForecast['p'],
								$busForecast['a'],
								new PositionReport(
									$busForecast['ta'],
									new Coordinate($busForecast['py'], $busForecast['px'])
								)
						)
					);
				}
			}
        $forecast[$line] = $buses;
        return new ArrivalForecast($response['hr'], $forecast);
    }
    
    /**
	 * Get the most recent ArrivalForecast for a given BusLine on all its
	 * served BusStop.
	 * 
	 * @param BusLine object.
	 * 
	 * @return ArrivalForecast object.
	 */    
    public function getArrivalForecastByLine(BusLine $line) : ArrivalForecast
    {
        $response = $this->execute('Previsao/Linha', ['codigoLinha'  => $line->getCod()]);
        $stopsForecasts = new \SplObjectStorage();
        if(isset($response['ps']))
			foreach ($response['ps'] as $stopForecast) {
				$stop = new BusStop(
					$stopForecast['cp'],
					$stopForecast['np'],
					new Coordinate($stopForecast['py'], $stopForecast['px'])
				);
				$buses = [];
				foreach ($stopForecast['vs'] as $busForecast) {
					$buses[] = new BusForecast(
						$busForecast['t'],
						new Bus($busForecast['p'],
								$busForecast['a'],
								new PositionReport(
									$busForecast['ta'],
									new Coordinate($busForecast['py'], $busForecast['px'])
								)
						)
					);
				}
				$stopsForecasts[$stop] = $buses;
			}
        
        return new ArrivalForecast($response['hr'], $stopsForecasts, ArrivalForecast::ARRIVALS_BY_BUSLINE);
    }
    
    /**
	 * Get the most recent ArrivalForecast for all BusLine that serve a given
	 * BusStop.
	 * 
	 * @param BusStop object.
	 * 
	 * @return ArrivalForecast object.
	 */    
    public function getArrivalForecastByStop(BusStop $stop) : ArrivalForecast
    {
        $response = $this->execute('Previsao/Parada', ['codigoParada'  => $stop->getCod()]);

        $linesForecasts = new \SplObjectStorage();
        if(isset($response['p']))
			foreach ($response['p']['l'] as $lineForecast) {
				$line = new BusLine(
					$lineForecast['cl'],
					false,
					substr($lineForecast['c'], 0, 4),
					$lineForecast['sl'] - 1,
					substr($lineForecast['c'], 5, 2),
					$lineForecast['lt0'],
					$lineForecast['lt1']
				);
				$buses = [];
				foreach ($lineForecast['vs'] as $busForecast) {
					$buses[] = new BusForecast(
						$busForecast['t'],
						new Bus($busForecast['p'],
								$busForecast['a'],
								new PositionReport(
									$busForecast['ta'],
									new Coordinate($busForecast['py'], $busForecast['px'])
								)
						)
					);
				}
				
				$linesForecasts[$line] = $buses;
			}
        var_dump($linesForecasts);
        return new ArrivalForecast($response['hr'], $linesForecasts);
    }

    /**
	 * Get information about the actual traffic flow.
	 *  
	 * @param lanes must be one of the consts: ALL_LANES, BUSWAYS,OTHER_LANES
	 * @param way (optional) must be one of the consts: DIST_CENTER,CENTER_DIST;
	 * 
	 * @return string correspondent to a KMZ file or null.
	 */ 
    public function getKmzTrafficInfo(string $lanes = self::ALL_LANES, string $way = '') : ?string
	{
		$str = 'KMZ'.
			(strlen($lanes)?'/':'').$lanes.
			(strlen($way)?'/':'').$way;

		return $this->execute($str,[],false);
	}
}
