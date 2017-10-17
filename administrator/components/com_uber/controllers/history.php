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

jimport('joomla.application.component.controllerform');

/**
 * History controller class.
 *
 * @since  1.6
 */
class UberControllerHistory extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'histories';
		parent::__construct();
	}
}
