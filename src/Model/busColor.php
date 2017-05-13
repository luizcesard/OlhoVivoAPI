<?php

namespace LuizCesar\OlhoVivoAPI\Model;

abstract class BusColor
{
	const CYAN = 0x00bfd0;
	const DARK_BLUE = 0x005ccf;
	const DARK_GREEN = 0x00812c;
	const DARK_RED = 0x990000;
	const GREEN = 0x4dff19;
	const GREY = 0x4d4d4d;
	const METAL = 0xb3b3b3;
	const ORANGE = 0xff5205;
	const RED = 0xff120d;
	const SILVER = 0xe6e6e6;
	const YELLOW = 0xffe600;
	
	public static function getBusColor($busId = 0)
	{
		switch((int)substr($busId,0,1)){
			case 1:
				return self::GREEN;
			case 2:
				return self::DARK_BLUE;
			case 3:
				return self::YELLOW;
			case 4:
				return self::RED;
			case 5:
				return self::DARK_GREEN;
			case 6:
				return self::CYAN;
			case 7:
				return self::DARK_RED;
			case 8:
				return self::ORANGE;
			default:
				return self::GREY;
		}
	}
	
	public static function getBusColorName(int $color) : string
	{
		foreach((new \ReflectionClass(__CLASS__))->getConstants() 
			as $colorName => $colorId)
			if($color === $colorId) return $colorName;
		
		return '';
	}
}
