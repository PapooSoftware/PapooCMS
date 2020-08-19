<?php

/**
 * Class style_hooks
 */
class style_hooks
{
	/**
	 * style_hooks constructor.
	 */
	public function __construct()
	{
	}

	/**
	 * @param $content
	 */
	public function libs_included(&$content)
	{
	}

	/**
	 * @param $content
	 */
	public function pre_smarty(&$content)
	{
	}

	/**
	 * @param $output
	 */
	public function pre_output(&$output)
	{
	}

	/**
	 * Diese Methode wird ausgeführt, sobald ein Abschluss im Formular-Manager stattfindet.
	 * @param array $lead
	 */
	public function onFormManagerLeadSaved(array $lead): void
	{
	}
}
