<?php
namespace Questwork\Interfaces;

interface Logger
{
	public function write($message);

	public function onWrite($callback);
}