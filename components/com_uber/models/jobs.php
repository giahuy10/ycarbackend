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
				'id', 'a.id',
				'ordering', 'a.ordering',
				'state', 'a.state',
				'created_by', 'a.created_by',
				'modified_by', 'a.modified_by',
				'customer_name', 'a.customer_name',
				'customer_phone', 'a.customer_phone',
				'number_passenger', 'a.number_passenger',
				'flight_number', 'a.flight_number',
				'district', 'a.district',
				'pick_up_location', 'a.pick_up_location',
				'pick_up_time', 'a.pick_up_time',
				'drop_location', 'a.drop_location',
				'price', 'a.price',
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
	 *
	 * @since    1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app  = JFactory::getApplication();
		$list = $app->getUserState($this->context . '.list');

		$ordering  = isset($list['filter_order'])     ? $list['filter_order']     : null;
		$direction = isset($list['filter_order_Dir']) ? $list['filter_order_Dir'] : null;

		$list['limit']     = (int) JFactory::getConfig()->get('list_limit', 20);
		$list['start']     = $app->input->getInt('start', 0);
		$list['ordering']  = $ordering;
		$list['direction'] = $direction;

		$app->setUserState($this->context . '.list', $list);
		$app->input->set('list', null);

		// List state information.
		parent::populateState($ordering, $direction);

        $app = JFactory::getApplication();

        $ordering  = $app->getUserStateFromRequest($this->context . '.ordercol', 'filter_order', $ordering);
        $direction = $app->getUserStateFromRequest($this->context . '.orderdirn', 'filter_order_Dir', $ordering);

        $this->setState('list.ordering', $ordering);
        $this->setState('list.direction', $direction);

        $start = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0, 'int');
        $limit = $app->getUserStateFromRequest($this->context . '.limit', 'limit', 0, 'int');

        if ($limit == 0)
        {
            $limit = $app->get('list_limit', 0);
        }

        $this->setState('list.limit', $limit);
        $this->setState('list.start', $start);
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
		$query
			->select(
				$this->getState(
					'list.select', 'DISTINCT a.*'
				)
			);

		$query->from('`#__uber_job` AS a');
		
		// Join over the users for the checked out user.
		$query->select('uc.name AS uEditor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the created by field 'created_by'
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

		// Join over the created by field 'modified_by'
		$query->join('LEFT', '#__users AS modified_by ON modified_by.id = a.modified_by');
		
		if (!JFactory::getUser()->authorise('core.edit', 'com_uber'))
		{
			$query->where('a.state = 1');
		}
		$query->where('a.driver_id = 0');
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
				$query->where('( a.customer_name LIKE ' . $search . '  OR  a.customer_phone LIKE ' . $search . '  OR  a.flight_number LIKE ' . $search . '  OR  a.drop_location LIKE ' . $search . ' )');
			}
		}
		

		// Filtering district
		$filter_district = $this->state->get("filter.district");
		if ($filter_district != '') {
			$query->where("a.district = '".$db->escape($filter_district)."'");
		}

		// Filtering pick_up_time
		// Checking "_dateformat"
		$filter_pick_up_time_from = $this->state->get("filter.pick_up_time_from_dateformat");
		$filter_Qpick_up_time_from = (!empty($filter_pick_up_time_from)) ? $this->isValidDate($filter_pick_up_time_from) : null;

		if ($filter_Qpick_up_time_from != null)
		{
			$query->where("a.pick_up_time >= '" . $db->escape($filter_Qpick_up_time_from) . "'");
		}

		$filter_pick_up_time_to = $this->state->get("filter.pick_up_time_to_dateformat");
		$filter_Qpick_up_time_to = (!empty($filter_pick_up_time_to)) ? $this->isValidDate($filter_pick_up_time_to) : null ;

		if ($filter_Qpick_up_time_to != null)
		{
			$query->where("a.pick_up_time <= '" . $db->escape($filter_Qpick_up_time_to) . "'");
		}

		// Add the list ordering clause.
		$orderCol  = 'pick_up_time';
		$orderDirn = 'asc';

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Method to get an array of data items
	 *
	 * @return  mixed An array of data on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
		
		foreach ($items as $item)
		{
			$item->number_seat_real = 	$item->number_seat;
			$item->number_seat = JText::_('COM_UBER_DRIVERS_NUMBER_SEAT_OPTION_' . strtoupper($item->number_seat));	
			//$item->pick_up_location	= json_decode($item->pick_up_location);
			//$item->pick_up_number = count((array)$item->pick_up_location);
			//$item->drop_location	= json_decode($item->drop_location);
			//$item->drop_number = count((array)$item->drop_location);
		

			// Get the title of every option selected
			$options      = explode(',', $item->district);
			$options_text = array();

			foreach ((array) $options as $option)
			{
				$options_text[] = JText::_('COM_UBER_JOBS_DISTRICT_OPTION_' . strtoupper($option));
			}

			$item->district = !empty($options_text) ? implode(',', $options_text) : $item->district;
		}

		return $items;
	}

	/**
	 * Overrides the default function to check Date fields format, identified by
	 * "_dateformat" suffix, and erases the field if it's not correct.
	 *
	 * @return void
	 */
	protected function loadFormData()
	{
		$app              = JFactory::getApplication();
		$filters          = $app->getUserState($this->context . '.filter', array());
		$error_dateformat = false;

		foreach ($filters as $key => $value)
		{
			if (strpos($key, '_dateformat') && !empty($value) && $this->isValidDate($value) == null)
			{
				$filters[$key]    = '';
				$error_dateformat = true;
			}
		}

		if ($error_dateformat)
		{
			$app->enqueueMessage(JText::_("COM_UBER_SEARCH_FILTER_DATE_FORMAT"), "warning");
			$app->setUserState($this->context . '.filter', $filters);
		}

		return parent::loadFormData();
	}

	/**
	 * Checks if a given date is valid and in a specified format (YYYY-MM-DD)
	 *
	 * @param   string  $date  Date to be checked
	 *
	 * @return bool
	 */
	private function isValidDate($date)
	{
		$date = str_replace('/', '-', $date);
		return (date_create($date)) ? JFactory::getDate($date)->format("Y-m-d") : null;
	}
}
