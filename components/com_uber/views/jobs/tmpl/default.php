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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_uber') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'jobform.xml');
$canEdit    = $user->authorise('core.edit', 'com_uber') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'jobform.xml');
$canCheckin = $user->authorise('core.manage', 'com_uber');
$canChange  = $user->authorise('core.edit.state', 'com_uber');
$canDelete  = $user->authorise('core.delete', 'com_uber');

$seats = UberHelpersUber::get_seats($user->username);
$balance = UberHelpersUber::get_balance($user->username);
?>


<form action="<?php echo JRoute::_('index.php?option=com_uber&view=jobs'); ?>" method="post"
      name="adminForm" id="adminForm">

	<?php //echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>

		<?php foreach ($this->items as $i => $item) : ?>
			<?php $canview = UberHelpersUber::is_bought($user->id, $item->id);
			//echo $canview;
			?>
			<div class="job-item 
			<?php if ($seats < $item->number_seat_real) echo "disable"?>">
				<div>
		
			<span style="
    color: <?php if (date('Ymd') == date('Ymd', strtotime($item->pick_up_time))) {
		echo "#019801";
	}
	 else {
		 echo "#71005b";
	 }
	?>;
    font-weight: bold;
"><?php echo date("d", strtotime($item->pick_up_time)); ?>/<?php echo date("m", strtotime($item->pick_up_time)); ?> <?php echo date("G:i", strtotime($item->pick_up_time)); ?>


</span> <span style="color:blue;font-weight: bold;"><?php echo $item->number_seat; ?></span> <?php echo $item->number_passenger; ?> khách<br/>
					
					<?php echo $item->pick_up_location?> &rarr; <?php echo $item->drop_location?>
					<!--
					<?php $i=0; foreach($item->pick_up_location as $location){						
										echo $location->com_point;			
											$i++;
											if ($i < $item->pick_up_number)
												echo " | ";
									} ?>  (<?php echo $item->pick_up_number?> điểm) =>
						<?php $k=0; foreach($item->drop_location as $location){						
										echo $location->com_point;			
											$k++;
											if ($k < $item->drop_number)
												echo " | ";
									} ?>			
				 (<?php echo $item->drop_number?> điểm)
								<?php //echo $item->district; ?>
			
			-->

		
			<?php if ($item->comment) {?>
				<br/>
			Ghi chú: <?php echo $item->comment?><br/>
			<?php }?>
			 
			 
		<?php if ($seats >= $item->number_seat_real) {?>
			<?php
			//$date_available = strtotime($item->pick_up_time-3600);
			//echo date("Y-m-d h:i:s",$date_available);
			$testDateStr = strtotime($item->pick_up_time);
			$currentDate = date("Y-m-d H:i:s");
			$finalDate = date("Y-m-d H:i:s", strtotime("-2 hour", $testDateStr));
			
			if ($item->available || $currentDate > $finalDate) {?>
			<br/>
			<span style="color:red;font-weight: bold;"><?php echo number_format($item->price-$item->fee); ?> đ</span> 	
			<a href="index.php?option=com_uber&view=job&id=<?php echo $item->id?>" class="btn btn-info">Mua Ngay</a>
			<?php } else { ?>
				<br/><span style="color:red;font-style: italic;font-size: 12px;">Chuyến xe chưa được bán!</span>
			<?php }?>
		<?php } else {?>
			<span style="color:red;font-style: italic;font-size: 12px;">* Xe bạn không đủ chỗ</span>
		<?php }?>
			</div>
		</div>
				

					
			

					
			
				
				
					
					

					
				

				

					
			
			

		
		<?php endforeach; ?>
		

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>

<?php if($canDelete) : ?>
<script type="text/javascript">

	jQuery(document).ready(function () {
		jQuery('.delete-button').click(deleteItem);
	});

	function deleteItem() {

		if (!confirm("<?php echo JText::_('COM_UBER_DELETE_MESSAGE'); ?>")) {
			return false;
		}
	}
</script>
<?php endif; ?>
