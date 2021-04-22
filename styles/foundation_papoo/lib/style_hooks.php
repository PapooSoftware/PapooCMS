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

	/**
	 * Diese Methode wird ausgeführt, sobald das Markup für Felder eines Formulars gebaut wurde.
	 * @param array $fieldsets pass by reference
	 */
	public function onFormManagerFieldsLoaded(array &$fieldsets): void
	{
	}
}
