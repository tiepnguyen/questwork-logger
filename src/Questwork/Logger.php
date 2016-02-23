<?php
namespace Questwork;

use DateTime;

class Logger implements Interfaces\Logger
{
	protected $filename;

	protected $handler;

	protected $datetime;

	protected $append;

	protected $onWrite;

	protected static $dateTime;

	public function __construct($filename, $options = [])
	{
		if (is_null($filename)) {
			trigger_error($message = 'Missing log file declaration');
			throw new Exception($message);
		} else if (is_string($filename)) {
			$this->filename = $filename;
		} else if (is_array($filename)) {
			$options = $filename;
		}
		$defaults = [
			'datetime' => 'Y-m-d h:i:s',
			'append' => TRUE,
			'on_write' => NULL
		];
		$options = array_merge($defaults, $options);
		$this->datetime = $options['datetime'];
		$this->append = $options['append'];
		$this->onWrite = $options['on_write'];
		if (($this->handler = fopen($this->filename, $this->append ? 'a' : 'w')) === FALSE) {
			trigger_error($message = 'Cannot open log file ' . $this->filename);
			throw new Exception($message);
		}
	}

	public function __destruct()
	{
		@fclose($this->handler);
	}

	public function write($message)
	{
		$datetime = $this->dateTime();
		$content = '[' . $datetime . '] ' . $message . PHP_EOL;
		if (!fwrite($this->handler, $content)) {
			trigger_error($message = 'Cannot write log file ' . $this->file);
			throw new Exception($message);
		}
		if (!is_null($this->onWrite)) {
			call_user_func($this->onWrite, $message);
		}
	}

	public function onWrite($callback)
	{
		$this->onWrite = $callback;
	}

	protected function dateTime()
	{
		if (is_null(self::$dateTime)) {
			self::$dateTime = new DateTime();
		}
		return self::$dateTime->format($this->datetime);
	}
}