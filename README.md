# OlhoVivoAPI

[![License](https://img.shields.io/packagist/l/Longman/telegram-bot.svg)](https://github.com/php-telegram-bot/core/LICENSE.md)


PHP implementation of SPTrans' OlhoVivo API.

## Table of Contents
- [Introduction](#introduction)
- [Instructions](#instructions)
    -[Get you token](#get-your-token)
    -[Require this package with Composer](#require-this-package-with-composer)
    -[Configs](#configs)
- [Usage Example](#usage-example)
- [Troubleshooting](#troubleshooting)
- [License](#license)


## Introduction

This is a pure PHP Object Oriented implementation  of [SPTrans'](http://www.sptrans.com.br/) OlhoVivoAPI.
OlhoVivo official API provides real-time data of São Paulo's (BR) public transportation bus system.
All methods [documented](http://www.sptrans.com.br/desenvolvedores/APIOlhoVivo/Documentacao.aspx?1) in the official API have been implemented but the 'CarregarDetalhes' (that doesn't seen to work on the official plataform).

This API can:
- Search for Bus Lines.
- Search for Bus Stops.
- Search for Bus Stops served by a given Bus Line.
- List actual São Paulo's Busways (BRT).
- Report all vehicles available on a given Bus Line and they position at a time.
- Forecast buses arrivals on a given Bus Stop for a given Bus Line at a time.
- Forecast buses arrivals at all Bus Stops served by a given Bus Line.
- Forecast buses arrivals at a given Bus Stop for all Bus Lines that serve it at a time.

Objects of this package may be used locally together with [GTFS tables](http://www.sptrans.com.br/desenvolvedores/GTFS.aspx).

-----
This code is available on
[Github](https://github.com/luizcesard/OlhoVivoAPI). Pull requests are welcome.

## Instructions

### Get your token

1. Sign up into [SPTrans Devs Area](http://www.sptrans.com.br/desenvolvedores/Cadastro.aspx).
2. After [loging in](http://www.sptrans.com.br/desenvolvedores/Default.aspx), you'll have to create an application name and ask for a token.

### Require this package with Composer

Install this package through [Composer](https://getcomposer.org/).
Edit your project's `composer.json` file to require
`luizcesar/olho-vivo-api`.

Create *composer.json* file
```json
{
    "name": "yourproject/yourproject",
    "type": "project",
    "require": {
        "php": ">=5.6",
        "luizcesar/olho-vivo-api": "*"
    }
}
```
and run `composer update`

**or**

run this command in your command line:

```bash
composer require luizcesar/olho-vivo-api
```

### Configs

Open the olhoVivo.php and fill it with your api-key.

## Usage Example

```php
use LuizCesar\OlhoVivoAPI\OlhoVivo;

//get an instance of OlhoVivo object
$spTrans = new OlhoVivo();

$aBusLine = ($spTrans->seekLines("875A"))[0]; //The first match of a line search
$busStops = $spTrans->seekBusStopsByLine($aBusLine); //all stops served by $aBusLine
$aBusStop = $busStops[rand(0,count($busStops)-1)]; //get a random bus stop of $busStops'

//Get a Map of all Lines that have buses forecasted to arrive on the chosen bus stop.
$arrivalForecast = $spTrans->getArrivalForecastByStop($aBusStop);
$arrivalsMap = $arrivalForecast->getArrivalsMap(); //Map of Lines and array of buses

echo "Arrival forecast for bus stop at {$aBusStop->getName()}" . PHP_EOL .
	"Issued at: {$arrivalForecast->getTime()}" . PHP_EOL;

//The way to iterate over SplObjectStorage objects.
foreach($arrivalsMap as $line)
{
	echo "{$line->getFullSignCode()} {$line->getActuralSignName()}" . PHP_EOL .
	"\t_______________________________" . PHP_EOL .
	"\t| Time  | Bus Id | Accessible |" . PHP_EOL .
	"\t|-------|--------|------------|" . PHP_EOL;
	foreach($arrivalsMap[$line] as $busForecast)
		echo "\t| {$busForecast->getTime()} | {$busForecast->getBus()->getId()}  |    " .
		($busForecast->getBus()->isAdapt() == true ? ' YES' : ' NO ') . "    |" . PHP_EOL;
	echo "\t===============================" . PHP_EOL . PHP_EOL;
}
```

And an example output:

```
Arrival forecast for bus stop at R DR.  ALBUQUERQUE LINS
Issued at: 13:41
8000-10 TERM. LAPA
	_______________________________
	| Time  | Bus Id | Accessible |
	|-------|--------|------------|
	| 13:45 | 12125  |     NO     |
	| 13:46 | 12119  |     NO     |
	===============================

8622-10 MORRO DOCE
	_______________________________
	| Time  | Bus Id | Accessible |
	|-------|--------|------------|
	| 13:45 | 12125  |     NO     |
	| 13:46 | 12119  |     NO     |
	| 13:45 | 11771  |     NO     |
	===============================

8400-10 TERM. PIRITUBA
	_______________________________
	| Time  | Bus Id | Accessible |
	|-------|--------|------------|
	| 13:45 | 12125  |     NO     |
	| 13:46 | 12119  |     NO     |
	| 13:45 | 11771  |     NO     |
	| 13:46 | 11402  |     YES    |
	===============================

8615-10 PQ. DA LAPA
	_______________________________
	| Time  | Bus Id | Accessible |
	|-------|--------|------------|
	| 13:45 | 12125  |     NO     |
	| 13:46 | 12119  |     NO     |
	| 13:45 | 11771  |     NO     |
	| 13:46 | 11402  |     YES    |
	| 13:46 | 82496  |     YES    |
	===============================

8594-10 CID. D'ABRIL 
	_______________________________
	| Time  | Bus Id | Accessible |
	|-------|--------|------------|
	| 13:45 | 12125  |     NO     |
	| 13:46 | 12119  |     NO     |
	| 13:45 | 11771  |     NO     |
	| 13:46 | 11402  |     YES    |
	| 13:46 | 82496  |     YES    |
	| 13:48 | 11448  |     YES    |
	===============================

874T-10 LAPA
	_______________________________
	| Time  | Bus Id | Accessible |
	|-------|--------|------------|
	| 13:45 | 12125  |     NO     |
	| 13:46 | 12119  |     NO     |
	| 13:45 | 11771  |     NO     |
	| 13:46 | 11402  |     YES    |
	| 13:46 | 82496  |     YES    |
	| 13:48 | 11448  |     YES    |
	| 14:08 | 52214  |     YES    |
	===============================

875A-10 PERDIZES
	_______________________________
	| Time  | Bus Id | Accessible |
	|-------|--------|------------|
	| 13:45 | 12125  |     NO     |
	| 13:46 | 12119  |     NO     |
	| 13:45 | 11771  |     NO     |
	| 13:46 | 11402  |     YES    |
	| 13:46 | 82496  |     YES    |
	| 13:48 | 11448  |     YES    |
	| 14:08 | 52214  |     YES    |
	| 14:22 | 62106  |     NO     |
	| 14:46 | 62096  |     NO     |
	===============================
```
## Troubleshooting

Please, if you find any bugs, report them on [issues](https://github.com/luizcesard/OlhoVivoAPI/issues) page.

## License

Please see the [LICENSE](LICENSE.md) included in this repository for a full copy of the MIT license,
which this project is licensed under.
