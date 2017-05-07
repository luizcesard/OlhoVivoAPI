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
define('BASE_URI','http://api.olhovivo.sptrans.com.br/v0/');

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use LuizCesar\OlhoVivoAPI\Model\ArrivalForecast;
use LuizCesar\OlhoVivoAPI\Model\Bus;
use LuizCesar\OlhoVivoAPI\Model\BusForecast;
use LuizCesar\OlhoVivoAPI\Model\BusLine;
use LuizCesar\OlhoVivoAPI\Model\BusStop;
use LuizCesar\OlhoVivoAPI\Model\Busway;
use LuizCesar\OlhoVivoAPI\Model\Coordinate;
use LuizCesar\OlhoVivoAPI\Model\LineReport;

class OlhoVivo
{

    /**
     * Guzzle Client object
	 * When correctly iniciated by the initialize method, stores session cookies.
     *
     * @var \GuzzleHttp\Client
     */
    private $client;
      
    public function __construct()
    {
        $this->initialize();
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
        
    private function execute($uri, $params = [])
    {
        try {
            do {
                $request = json_decode(($this->client->request(
                    'GET',
                    $uri,
                    count($params)>0? ['query' => $params]:[]
                ))->getBody(), true);
            } while (isset($request['Message']) && $this->initialize());
            return $request;
        } catch (RequestException $e) {
			throw new \Exception("HTTP request/response error: {$e->getMessage()}");
        }
    }
    
    /**
	 * Search for BusLines.
	 * 
	 * @param searchString should be any string of line name or codes.
	 * 
	 * @return an array of BusLine objects with possible matches.
	 */
    public function seekLines($searchString) : array
    {
        $result = [];
        foreach ($this->execute('Linha/Buscar', ['termosBusca' => $searchString]) as $line) {
            $result[] = new BusLine(
                $line['CodigoLinha'],
                $line['Circular'],
                $line['Letreiro'],
                $line['Sentido'],
                $line['Tipo'],
                $line['DenominacaoTPTS'],
                $line['DenominacaoTSTP'],
                $line['Informacoes']
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
                $stop['CodigoParada'],
                $stop['Endereco'],
                new Coordinate($stop['Latitude'], $stop['Longitude'],
                $stop['Nome'])
            );
        }
        return $busStopsArray;
    }
    
    /**
	 * Get all city's busways.
	 * 
	 * @return array of Busway objects.
	 */
    public function getCorridors() : array
    {
        $result = [];
        foreach ($this->execute('Corredor') as $busway) {
            $result[] = new Busway(
                $busway['CodCorredor'],
                $busway['Nome']
            );
        }
        return $result;
    }
    
    /**
	 * Get a LineReport for the actual time for a given BusLine.
	 * 
	 * @param BusLine object.
	 * @return LineReport object.
	 */
    public function getLineReport(BusLine $line) : LineReport
    {
        $result = [];
        $response = $this->execute('Posicao',
                            ['codigoLinha' => $line->getCod()]
        );
        foreach ($response['vs'] as $bus) {
            $buses[] = new Bus(
                $bus['p'],
                $bus['a'],
                new Coordinate($bus['py'], $bus['px'])
            );
        }
        return new LineReport($response['hr'], $buses);
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
        foreach ($response['p']['l'] as $aLine) {
		  foreach($aLine['vs'] as $busForecast){
			  $buses[] = new BusForecast(
				  $busForecast['t'],
				  new Bus($busForecast['p'],
						  $busForecast['a'],
						  new Coordinate($busForecast['py'], $busForecast['px'])
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
        foreach ($response['ps'] as $stopForecast) {
            $stop = new BusStop(
                $stopForecast['cp'],
                $stopForecast['np'],
                new Coordinate($stopForecast['py'], $stopForecast['px'])
            );
            
            foreach ($stopForecast['vs'] as $busForecast) {
                $buses[] = new BusForecast(
                $busForecast['t'],
                new Bus($busForecast['p'],
                        $busForecast['a'],
                        new Coordinate($busForecast['py'], $busForecast['px']))
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
        foreach ($response['p']['l'] as $lineForecast) {
            $line = new BusLine(
                $lineForecast['cl'],
                false,
                substr($lineForecast['c'], 0, 4),
                $lineForecast['sl'],
                substr($lineForecast['c'], 5, 2),
                $lineForecast['lt0'],
                $lineForecast['lt1']
            );
            
            foreach ($lineForecast['vs'] as $busForecast) {
                $buses[] = new BusForecast(
                $busForecast['t'],
                new Bus($busForecast['p'],
                        $busForecast['a'],
                        new Coordinate($busForecast['py'], $busForecast['px']))
                );
            }
            
            $linesForecasts[$line] = $buses;
        }
        
        return new ArrivalForecast($response['hr'], $linesForecasts);
    }
}
