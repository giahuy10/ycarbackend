<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Uber
 * @author     sugar lead <anjakahuy@gmail.com>
 * @copyright  2017 sugar lead
 * @license    bản quyền mã nguồn mở GNU phiên bản 2
 */
// No direct access
defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;
/**
 * history Table class
 *
 * @since  1.6
 */
class UberTablehistory extends JTable
{
	
	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 */
	public function __construct(&$db)
	{
		JObserverMapper::addObserverClassToClass('JTableObserverContenthistory', 'UberTablehistory', array('typeAlias' => 'com_uber.history'));
		parent::__construct('#__uber_history', 'id', $db);
	}

	/**
	 * Overloaded bind function to pre-process the params.
	 *
	 * @param   array  $array   Named array
	 * @param   mixed  $ignore  Optional array or list of parameters to ignore
	 *
	 * @return  null|string  null is operation was satisfactory, otherwise returns an error
	 *
	 * @see     JTable:bind
	 * @since   1.5
	 */
	public function bind($array, $ignore = '')
	{
	    $date = JFactory::getDate();
		$task = JFactory::getApplication()->input->get('task');
	    
		$input = JFactory::getApplication()->input;
		$task = $input->getString('task', '');

		if ($array['id'] == 0 && empty($array['created_by']))
		{
			$array['created_by'] = JFactory::getUser()->id;
		}

		if ($array['id'] == 0 && empty($array['modified_by']))
		{
			$array['modified_by'] = JFactory::getUser()->id;
		}

		if ($task == 'apply' || $task == 'save')
		{
			$array['modified_by'] = JFactory::getUser()->id;
		}

		// Support for multiple field: job_id
		if (isset($array['job_id']))
		{
			if (is_array($array['job_id']))
			{
				$array['job_id'] = implode(',',$array['job_id']);
			}
			elseif (strpos($array['job_id'], ',') != false)
			{
				$array['job_id'] = explode(',',$array['job_id']);
			}
			elseif (empty($array['job_id']))
			{
				$array['job_id'] = '';
			}
		}
		else
		{
			$array['job_id'] = '';
		}

		// Support for multiple field: driver_id
		if (isset($array['driver_id']))
		{
			if (is_array($array['driver_id']))
			{
				$array['driver_id'] = implode(',',$array['driver_id']);
			}
			elseif (strpos($array['driver_id'], ',') != false)
			{
				$array['driver_id'] = explode(',',$array['driver_id']);
			}
			elseif (empty($array['driver_id']))
			{
				$array['driver_id'] = '';
			}
		}
		else
		{
			$array['driver_id'] = '';
		}

		// Support for multiple field: confirmed
		if (isset($array['confirmed']))
		{
			if (is_array($array['confirmed']))
			{
				$array['confirmed'] = implode(',',$array['confirmed']);
			}
			elseif (strpos($array['confirmed'], ',') != false)
			{
				$array['confirmed'] = explode(',',$array['confirmed']);
			}
			elseif (empty($array['confirmed']))
			{
				$array['confirmed'] = '';
			}
		}
		else
		{
			$array['confirmed'] = '';
		}

		if (isset($array['params']) && is_array($array['params']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}

		if (isset($array['metadata']) && is_array($array['metadata']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['metadata']);
			$array['metadata'] = (string) $registry;
		}

		if (!JFactory::getUser()->authorise('core.admin', 'com_uber.history.' . $array['id']))
		{
			$actions         = JAccess::getActionsFromFile(
				JPATH_ADMINISTRATOR . '/components/com_uber/access.xml',
				"/access/section[@name='history']/"
			);
			$default_actions = JAccess::getAssetRules('com_uber.history.' . $array['id'])->getData();
			$array_jaccess   = array();

			foreach ($actions as $action)
			{
                if (key_exists($action->name, $default_actions))
                {
                    $array_jaccess[$action->name] = $default_actions[$action->name];
                }
			}

			$array['rules'] = $this->JAccessRulestoArray($array_jaccess);
		}

		// Bind the rules for ACL where supported.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$this->setRules($array['rules']);
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * This function convert an array of JAccessRule objects into an rules array.
	 *
	 * @param   array  $jaccessrules  An array of JAccessRule objects.
	 *
	 * @return  array
	 */
	private function JAccessRulestoArray($jaccessrules)
	{
		$rules = array();

		foreach ($jaccessrules as $action => $jaccess)
		{
			$actions = array();

			if ($jaccess)
			{
				foreach ($jaccess->getData() as $group => $allow)
				{
					$actions[$group] = ((bool)$allow);
				}
			}

			$rules[$action] = $actions;
		}

		return $rules;
	}

	/**
	 * Overloaded check function
	 *
	 * @return bool
	 */
	public function check()
	{
		$user       = JFactory::getUser();
		// If there is an ordering column and this is a new row then get the next ordering value
		if (property_exists($this, 'ordering') && $this->id == 0)
		{
			$this->ordering = self::getNextOrder();
		}
	
		if ($this->confirmed == 2) {
				$object = new stdClass();
				$object->state=-2;
				$object->id = $this->job_id;
				$object->driver_id = 0;
				$result = JFactory::getDbo()->updateObject('#__uber_job', $object, 'id');
			    
			}
		if ($this->confirmed == 2 && $this->resolved!=2){
		
        		$job_detail = UberHelper::get_job_detail($this->job_id);
				$message = "Chuyen xe YCAR ma so ".$this->job_id." cua quy khach da duoc HUY. Moi y kien dong gop quy khach vui long lien he: 0917999941";
				
				UberHelper::send_sms($job_detail->customer_phone,$message);
				$this->resolved = 1;
				//$this->comment2 =$query->__toString();
		    
		}	
		if ($this->confirmed == 1 && !$this->resolved) {

			$object = new stdClass();
			// Must be a valid primary key value.
			$object->id = $this->job_id;
			$object->waiting_cancel = 0;
			$object->driver_id = 0;
			

			// Update their details in the users table using id as the primary key.
			$result = JFactory::getDbo()->updateObject('#__uber_job', $object, 'id');
			
			
			$db = JFactory::getDbo();

			// Create a new query object.
			$query = $db->getQuery(true);

			// Select all records from the user profile table where key begins with "custom.".
			// Order it by the ordering field.
			$query->select($db->quoteName(array('fee', 'sale_price')));
			$query->from($db->quoteName('#__uber_job'));
			$query->where($db->quoteName('id') . ' = '. $this->job_id);
			//$query->order('ordering ASC');

			// Reset the query using our newly populated query object.
			$db->setQuery($query);

			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$job_detail = $db->loadObject();
			
			$profile = new stdClass();


			$profile->job_id = $this->job_id;
			$profile->driver_id = $this->driver_id;
			$profile->type = 2;
			$profile->value = $job_detail->sale_price - $job_detail->fee;
			$profile->comment = $this->comment;
			$profile->state = 1;
			$profile->created_by = $user->id;
			//$profile->job_id = $this->job_id;
		
			$result2 = JFactory::getDbo()->insertObject('#__uber_transaction', $profile);
			$this->resolved = 1;
			$player_ids = UberHelper::get_player_id($profile->driver_id);
				if ($profile->value >= 0) {
				    $return_money = "+".UberHelper::format_price(abs($profile->value));
				}else {
				    $return_money = "-".UberHelper::format_price(abs($profile->value));
				}
			$title = "Xác nhận hủy chuyến xe";
			$message ="Yêu cầu hủy chuyến xe CX".$profile->job_id." đã được xử lý. Phí chuyến xe đã được trả lại: ".$return_money;
			UberHelper::sendMessage($title,$message,$player_ids);
		}
		

		return parent::check();
	}

	/**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table.  The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param   mixed    $pks     An optional array of primary key values to update.  If not
	 *                            set the instance property value is used.
	 * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param   integer  $userId  The user id of the user performing the operation.
	 *
	 * @return   boolean  True on success.
	 *
	 * @since    1.0.4
	 *
	 * @throws Exception
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		// Initialise variables.
		$k = $this->_tbl_key;

		// Sanitize input.
		ArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state  = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k)
			{
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else
			{
				throw new Exception(500, JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
			}
		}

		// Build the WHERE clause for the primary keys.
		$where = $k . '=' . implode(' OR ' . $k . '=', $pks);

		// Determine if there is checkin support for the table.
		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time'))
		{
			$checkin = '';
		}
		else
		{
			$checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
		}

		// Update the publishing state for rows with the given primary keys.
		$this->_db->setQuery(
			'UPDATE `' . $this->_tbl . '`' .
			' SET `state` = ' . (int) $state .
			' WHERE (' . $where . ')' .
			$checkin
		);
		$this->_db->execute();

		// If checkin is supported and all rows were adjusted, check them in.
		if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
		{
			// Checkin each row.
			foreach ($pks as $pk)
			{
				$this->checkin($pk);
			}
		}

		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks))
		{
			$this->state = $state;
		}

		return true;
	}

	/**
	 * Define a namespaced asset name for inclusion in the #__assets table
	 *
	 * @return string The asset name
	 *
	 * @see JTable::_getAssetName
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;

		return 'com_uber.history.' . (int) $this->$k;
	}

	/**
	 * Returns the parent asset's id. If you have a tree structure, retrieve the parent's id using the external key field
	 *
	 * @param   JTable   $table  Table name
	 * @param   integer  $id     Id
	 *
	 * @see JTable::_getAssetParentId
	 *
	 * @return mixed The id on success, false on failure.
	 */
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		// We will retrieve the parent-asset from the Asset-table
		$assetParent = JTable::getInstance('Asset');

		// Default: if no asset-parent can be found we take the global asset
		$assetParentId = $assetParent->getRootId();

		// The item has the component as asset-parent
		$assetParent->loadByName('com_uber');

		// Return the found asset-parent-id
		if ($assetParent->id)
		{
			$assetParentId = $assetParent->id;
		}

		return $assetParentId;
	}

	/**
	 * Delete a record by id
	 *
	 * @param   mixed  $pk  Primary key value to delete. Optional
	 *
	 * @return bool
	 */
	public function delete($pk = null)
	{
		$this->load($pk);
		$result = parent::delete($pk);
		
		return $result;
	}
}
