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
$session = JFactory::getSession();
$canEdit = JFactory::getUser()->authorise('core.edit', 'com_uber');

if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_uber'))
{
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
$user       = JFactory::getUser();

?>



	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script>
	function loadNowPlaying(){
	  $("#now_playing").load("index.php?option=com_uber&view=ajax&format=raw&task=detail&job_id=<?php echo $this->item->id?>");
	}
	setInterval(function(){loadNowPlaying(); $('#loader').hide();}, 1000);
	</script>
	<div id="loader"><img style="width:50px;" src="https://media.tenor.com/images/d6cd5151c04765d1992edfde14483068/tenor.gif"> Đang load dữ liệu. Vui lòng chờ trong giây lát.</div>
	<div id="now_playing"></div>
	</div>

