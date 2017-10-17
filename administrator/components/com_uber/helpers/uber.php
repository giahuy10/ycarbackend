<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Uber
 * @author     Eddy Nguyen <contact@eddynguye.com>
 * @copyright  2017 Eddy Nguyen
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Uber helper.
 *
 * @since  1.6
 */
class UberHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  string
	 *
	 * @return void
	 */
	 
	public static function addSubmenu($vName = '')
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_UBER_TITLE_DRIVERS'),
			'index.php?option=com_uber&view=drivers',
			$vName == 'drivers'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_UBER_TITLE_JOBS'),
			'index.php?option=com_uber&view=jobs',
			$vName == 'jobs'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_UBER_TITLE_HISTORIES'),
			'index.php?option=com_uber&view=histories',
			$vName == 'histories'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_UBER_TITLE_TRANSACTIONS'),
			'index.php?option=com_uber&view=transactions',
			$vName == 'transactions'
		);

	}
	public static function send_notification($response_array){
				
	$url = 'https://api2.pushassist.com/notifications/';
	
	$headers = array(
		'X-Auth-Token: AC6p6MWPeuxcheQ18a4yhnO091nk7kL2',
		'X-Auth-Secret: tXtDyHfkDODfsvLMVvn1cfqPaCTl',
		'Content-Type: application/json'
	);
   
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response_array));
	curl_setopt($ch, CURLOPT_SSLVERSION, 4);
	$result = curl_exec($ch);

	if ($result === FALSE) {
		die('Curl failed: ' . curl_error($ch));
	}
	
	curl_close($ch);

	$result_arr = json_decode($result, true);
	
	return $result_arr;
} 
	public static function getDriver($id) {
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select("*");
		$query->from($db->quoteName('#__uber_driver'));
		$query->where($db->quoteName('id') . ' = '. $id);
		//$query->order('ordering ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObject();
		return ($results);
	}

	/**
	 * Gets the files attached to an item
	 *
	 * @param   int     $pk     The item's id
	 *
	 * @param   string  $table  The table's name
	 *
	 * @param   string  $field  The field's name
	 *
	 * @return  array  The files
	 */
	public static function getFiles($pk, $table, $field)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select($field)
			->from($table)
			->where('id = ' . (int) $pk);

		$db->setQuery($query);

		return explode(',', $db->loadResult());
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return    JObject
	 *
	 * @since    1.6
	 */
	public static function getActions()
	{
		$user   = JFactory::getUser();
		$result = new JObject;

		$assetName = 'com_uber';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}

