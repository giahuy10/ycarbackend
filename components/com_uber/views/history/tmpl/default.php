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
$balance = UberHelpersUber::get_balance($user->id);

?>
<h2> Lịch sử giao dịch </h2>
<h3>Số dư: <?php echo number_format($balance)?> vnđ</h3>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script>
	function loadNowPlaying(){
	  $("#now_playing").load("index.php?option=com_uber&view=ajax&format=raw&task=history");
	}
	setInterval(function(){loadNowPlaying(); $('#loader').hide();}, 1000);
	</script>
	<div id="loader"><img style="width:50px;" src="https://media.tenor.com/images/d6cd5151c04765d1992edfde14483068/tenor.gif"> Đang load dữ liệu. Vui lòng chờ trong giây lát.</div>
	<div id="now_playing"></div>
	</div>
