<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Uber
 * @author     Eddy Nguyen <contact@eddynguye.com>
 * @copyright  2017 Eddy Nguyen
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Uber records.
 *
 * @since  1.6
 */
class UberModelJobs extends JModelList
{
/**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see        JController
	* @since      1.6
	*/
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.`id`',
				'ordering', 'a.`ordering`',
				'state', 'a.`state`',
				'created_by', 'a.`created_by`',
				'modified_by', 'a.`modified_by`',
				'customer_name', 'a.`customer_name`',
				'customer_phone', 'a.`customer_phone`',
				'number_passenger', 'a.`number_passenger`',
				'flight_number', 'a.`flight_number`',
				'district', 'a.`district`',
				'sold', 'a.`sold`',
				'pick_up_location', 'a.`pick_up_location`',
				'pick_up_time', 'a.`pick_up_time`',
				'drop_location', 'a.`drop_location`',
				'price', 'a.`price`','driver_id', 'a.`driver_id`',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);
		// Filtering district
		$this->setState('filter.district', $app->getUserStateFromRequest($this->context.'.filter.district', 'filter_district', '', 'string'));
		// Filtering sold
		$this->setState('filter.sold', $app->getUserStateFromRequest($this->context.'.filter.sold', 'filter_sold', '', 'string'));
		// Filtering pick_up_time
		$this->setState('filter.pick_up_time_from_dateformat', $app->getUserStateFromRequest($this->context.'.filter.pick_up_time_from_dateformat', 'filter_pick_up_time_from_dateformat', '', 'string'));
		$this->setState('filter.pick_up_time_to_dateformat', $app->getUserStateFromRequest($this->context.'.filter.pick_up_time_to_dateformat', 'filter_pick_up_time_to_dateformat', '', 'string'));


		// Load the parameters.
		$params = JComponentHelper::getParams('com_uber');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.customer_name', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return   string A store id.
	 *
	 * @since    1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__uber_job` AS a');

		// Join over the users for the checked out user
		$query->select("uc.name AS uEditor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Join over the user field 'modified_by'
		$query->select('`modified_by`.name AS `modified_by`');
		$query->join('LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`');

		// Filter by published state
		$published = $this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.customer_name LIKE ' . $search . '  OR  a.customer_phone LIKE ' . $search . '  OR  a.flight_number LIKE ' . $search . '  OR  a.pick_up_location LIKE ' . $search . '  OR  a.drop_location LIKE ' . $search . ' OR  a.id LIKE ' . $search . ' )');
			}
		}
		
		// Filtering sold
		$filter_sold = $this->state->get("filter.sold");

		if ($filter_sold !== null && (is_numeric($filter_sold) || !empty($filter_sold)))
		{
			if ($filter_sold == 1) {
				$query->where("a.`driver_id` != 0");
				$query->where("a.`status` = 0");
			} elseif ($filter_sold == 2) {
				$query->where("a.`driver_id` != 0");
				$query->where("a.`status` = 1");
			} elseif ($filter_sold == 3) {
				$query->where("a.`driver_id` = 0");
				
			}
				
		}

		// Filtering district
		$filter_district = $this->state->get("filter.district");

		if ($filter_district !== null && (is_numeric($filter_district) || !empty($filter_district)))
		{
			$query->where("a.`district` = '".$db->escape($filter_district)."'");
		}

		// Filtering pick_up_time
		$filter_pick_up_time_from = $this->state->get("filter.pick_up_time_from_dateformat");

		if ($filter_pick_up_time_from !== null && !empty($filter_pick_up_time_from))
		{
			$query->where("a.`pick_up_time` >= '".$db->escape($filter_pick_up_time_from)."'");
		}
		$filter_pick_up_time_to = $this->state->get("filter.pick_up_time_to_dateformat");

		if ($filter_pick_up_time_to !== null  && !empty($filter_pick_up_time_to))
		{
			$query->where("a.`pick_up_time` <= '".$db->escape($filter_pick_up_time_to)."'");
		}
		if (!$filter_pick_up_time_to && !$filter_pick_up_time_from) {
			$query->where($db->quoteName('pick_up_time') . ' >= ( CURDATE() - INTERVAL 2 DAY )');
		}
			
		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
	
		foreach ($items as $oneItem)
		{
			$oneItem->number_seat_real = 	$oneItem->number_seat;
			$oneItem->number_seat = JText::_('COM_UBER_DRIVERS_NUMBER_SEAT_OPTION_' . strtoupper($oneItem->number_seat));	
			// Get the title of every option selected.

			$options      = explode(',', $oneItem->district);

			$options_text = array();

			foreach ((array) $options as $option)
			{
				$options_text[] = JText::_('COM_UBER_JOBS_DISTRICT_OPTION_' . strtoupper($option));
			}

			$oneItem->district = !empty($options_text) ? implode(', ', $options_text) : $oneItem->district;
		}

		return $items;
	}
}
