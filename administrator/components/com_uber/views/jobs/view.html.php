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

jimport('joomla.application.component.view');

/**
 * View class for a list of Uber.
 *
 * @since  1.6
 */
 
class UberViewJobs extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
			
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		UberHelper::addSubmenu('jobs');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
		
	{
		$user      = JFactory::getUser();
		$access = array(11,8,7);
		$can_view = array_intersect($access,$user->groups);
		$state = $this->get('State');
		$canDo = UberHelper::getActions();

		JToolBarHelper::title(JText::_('COM_UBER_TITLE_JOBS'), 'jobs.png');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/job';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create') && $can_view)
			{
				JToolBarHelper::addNew('job.add', 'JTOOLBAR_NEW');

				if (isset($this->items[0]))
				{
					//JToolbarHelper::custom('jobs.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
				}
			}

			if ($canDo->get('core.edit') && isset($this->items[0]))
			{
				JToolBarHelper::editList('job.edit', 'JTOOLBAR_EDIT');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				//JToolBarHelper::custom('jobs.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				//JToolBarHelper::custom('jobs.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}
			elseif (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				JToolBarHelper::deleteList('', 'jobs.delete', 'JTOOLBAR_DELETE');
			}

			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				//JToolBarHelper::archiveList('jobs.archive', 'JTOOLBAR_ARCHIVE');
			}

			if (isset($this->items[0]->checked_out))
			{
				//JToolBarHelper::custom('jobs.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			}
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
			{
				JToolBarHelper::deleteList('', 'jobs.delete', 'JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::divider();
			}
			elseif ($canDo->get('core.edit.state'))
			{
				JToolBarHelper::trash('jobs.trash', 'JTOOLBAR_TRASH');
				JToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_uber');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_uber&view=jobs');
	}

	/**
	 * Method to order fields 
	 *
	 * @return void 
	 */
	protected function getSortFields()
	{
		return array(
			'a.`id`' => JText::_('JGRID_HEADING_ID'),
			'a.`ordering`' => JText::_('JGRID_HEADING_ORDERING'),
			'a.`state`' => JText::_('JSTATUS'),
			'a.`customer_name`' => JText::_('COM_UBER_JOBS_CUSTOMER_NAME'),
			'a.`customer_phone`' => JText::_('COM_UBER_JOBS_CUSTOMER_PHONE'),
			'a.`number_passenger`' => JText::_('COM_UBER_JOBS_NUMBER_PASSENGER'),
			'a.`flight_number`' => JText::_('COM_UBER_JOBS_FLIGHT_NUMBER'),
			'a.`district`' => JText::_('COM_UBER_JOBS_DISTRICT'),
			'a.`pick_up_location`' => JText::_('COM_UBER_JOBS_PICK_UP_LOCATION'),
			'a.`pick_up_time`' => JText::_('COM_UBER_JOBS_PICK_UP_TIME'),
			'a.`drop_location`' => JText::_('COM_UBER_JOBS_DROP_LOCATION'),
			'a.`price`' => JText::_('COM_UBER_JOBS_PRICE'),
		);
	}

    /**
     * Check if state is set
     *
     * @param   mixed  $state  State
     *
     * @return bool
     */
    public function getState($state)
    {
        return isset($this->state->{$state}) ? $this->state->{$state} : false;
    }
}
