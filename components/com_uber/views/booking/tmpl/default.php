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


$user       = JFactory::getUser();
$balance = UberHelpersUber::get_balance($user->username);
$jinput = JFactory::getApplication()->input;
$submit = $jinput->get('submit');
if ($submit) { ?>
	<div class="alert alert-success">
	
	  <strong>Success!</strong> Yêu cầu đặ xe đã được gửi. Chúng tôi sẽ xử lý trong thời gian sớm nhất! <br/>
		
	</div>	
<?php }
?>
<?php print_r($_POST); ?>

						
								
				
				
	