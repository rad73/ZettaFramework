<?php

/**
 * Класс для работы с текстом в UTF-8 кодировке
 *
 */
abstract class System_String {

	private final function __construct() {}

	/**
	 * Перевод в нижний регистр
	 *
	 * @param string $string
	 * @return string
	 */
	public static function StrToLower($string) {
		return mb_strtolower($string, "UTF-8");
	}

	/**
	 * Перевод в верхний регистр
	 *
	 * @param string $string
	 * @return string
	 */
	public static function StrToUpper($string) {
		return mb_strtoupper($string, "UTF-8");
	}

	/**
	 * Перевод каждого слова в строке в верхний регистр
	 *
	 * @param string $string
	 * @return string
	 */
	public static function WordsToUpper($string) {
		return mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
	}

	/**
	 * Делаем первую букву в строке в верхнем регистре
	 *
	 * @param string $string
	 * @return string
	 */
	public static function UcFirst($string) {
		return self::StrToUpper(self::Substr($string, 0, 1)) . self::StrToLower(self::Substr($string, 1));
	}

	/**
	 * Транслитерация
	 *
	 * @param string $string
	 * @return string
	 */
	public static function Translit($string) {

		$replace = array ("а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "zh",
		"з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l", "м" => "m",
		"н" => "n", "о" => "o", "п" => "p", "р" => "r", "с" => "s", "т" => "t",
		"у" => "u", "ф" => "f", "х" => "h", "ц" => "ts", "ч" => "ch", "ш" => "sh",
		"щ" => "sch", "ъ" => "'", "ы" => "yi", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya" );


		return iconv("UTF-8", "UTF-8//IGNORE", strtr($string, $replace));

	}

	/**
	 * Транслитерация из латиницы в кирилицу
	 *
	 * @param string $string
	 * @return string
	 */
	public static function TranslitReverse($string) {

		$replace = array("a" => "а", "b" => "б", "v" => "в", "g" => "г", "d" => "д",
		"e" => "е", "j" => "ж", "z" => "з", "i" => "и", "y" => "й", "k" => "к",
		"l" => "л", "m" => "м", "n" => "н", "o" => "о", "p" => "п", "r" => "р", "w"	=> "в",
		"s" => "с", "t" => "т", "u" => "у", "f" => "ф", "h" => "х", "c" => "ц", "q" => "к");

		return iconv("UTF-8", "UTF-8//IGNORE", strtr($string, $replace));

	}

	public static function Substr($string, $start, $length = null) {
		return mb_substr($string, $start, $length, "UTF-8");
	}

}
