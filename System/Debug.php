<?php namespace System;

abstract class Debug {

	protected static $_memoryStart;
	protected static $_timeStart;

	/**
	 * Инициализируем переменные начала работы участка кода
	 */
	public static function Prepare() {

		self::$_memoryStart = memory_get_usage();
		self::$_timeStart = microtime(true);

	}

	/**
	 * Получаем переменные после работы кода
	 * @return array(
	 *    'time' => 0.1,	время в секундах
	 *    'memory' => 12	память в мегабайтах
	 * )
	 */
	public static function Get() {

		$memory_usage = round((memory_get_usage() - self::$_memoryStart) / 1024 / 1024, 2);
		$memory_usage = $memory_usage < 0 ? 0 : $memory_usage;

		return array(
			round(microtime(true) - self::$_timeStart, 2),
			$memory_usage
		);

	}

}
