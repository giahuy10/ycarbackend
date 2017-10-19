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
public static function format_price($price) {
    $price = number_format($price)."đ";
	return $price; 
}
public static function get_player_id ($driver_id) {
	     $db = JFactory::getDbo();
		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('player_id'));
		$query->from($db->quoteName('#__uber_playerid'));
		$query->where($db->quoteName('driver_id') . ' = '. $db->quote($driver_id));
	     $query->order('id DESC');
		

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadColumn();
		return $results;
	}
		public static function sendMessage($title, $content,$player_id){
		$headings = array(
			"en" => $title
			);
		$content = array(
			"en" => $content
			);	
		if ($player_id) {
		   
		    $fields = array(
			'app_id' => "9c611d5c-e51f-4f51-bb9f-b8d679153272",
			 'include_player_ids' => $player_id,
			 'data' => array("foo" => "bar"),
			'contents' => $content,
			'headings' => $headings
		    );
		}else {
		    $fields = array(
			'app_id' => "9c611d5c-e51f-4f51-bb9f-b8d679153272",
			'included_segments' => array('All'),
             'data' => array("foo" => "bar"),
			'contents' => $content,
			'headings' => $headings
		    );
		}
		
		
		$fields = json_encode($fields);
   // print("\nJSON sent:\n");
   // print($fields);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
												   'Authorization: Basic OTg5YjEzZDEtYTcxNC00MDA0LWFhMzYtNDhlMDU0NjhhMzIx'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		
		return $response;
	}
    public static function get_job_detail($id) {
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select('*');
		$query->from($db->quoteName('#__uber_job'));
		$query->where($db->quoteName('id') . ' = '. $id);
	
		
		//$query->where($db->quoteName('driver_id') . ' = 0');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObject();
		
			return $results;
	}

    public static function send_sms($YourPhone,$Content) {
        	$APIKey="2A00924E0B265978F73EB9B28088DF";
		$SecretKey="C60751C63C7740DCD5F0886E3DCA18";
	
		
		
		$SendContent=urlencode($Content);
		$data="http://rest.esms.vn/MainService.svc/json/SendMultipleMessage_V4_get?Phone=$YourPhone&ApiKey=$APIKey&SecretKey=$SecretKey&Content=$SendContent&SmsType=2&Brandname=YCAR.VN";
		
		$curl = curl_init($data); 
		curl_setopt($curl, CURLOPT_FAILONERROR, true); 
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		$result = curl_exec($curl); 
			
		$obj = json_decode($result,true);
		if($obj['CodeResult']==100)
		{
			print "<br>";
			print "CodeResult:".$obj['CodeResult'];
			print "<br>";
			print "CountRegenerate:".$obj['CountRegenerate'];
			print "<br>";     
			print "SMSID:".$obj['SMSID'];
			print "<br>";
		}
		else
		{
			print "ErrorMessage:".$obj['ErrorMessage'];
		}
		
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

