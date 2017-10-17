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

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_uber');

if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_uber'))
{
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>

<div class="item_fields">

	<table class="table">
		

		<tr>
			<th><?php echo JText::_('COM_UBER_FORM_LBL_DRIVER_PHONE'); ?></th>
			<td><?php echo $this->item->phone; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_UBER_FORM_LBL_DRIVER_TITLE'); ?></th>
			<td><?php echo $this->item->title; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_UBER_FORM_LBL_DRIVER_AVATAR'); ?></th>
			<td>
			<?php
			foreach ((array) $this->item->avatar as $singleFile) : 
				if (!is_array($singleFile)) : 
					$uploadPath = 'images' . DIRECTORY_SEPARATOR . $singleFile;
					 echo '<a href="' . JRoute::_(JUri::root() . $uploadPath, false) . '" target="_blank">' . $singleFile . '</a> ';
				endif;
			endforeach;
		?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_UBER_FORM_LBL_DRIVER_ID_CARD'); ?></th>
			<td><?php echo $this->item->id_card; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_UBER_FORM_LBL_DRIVER_NUMBER_PLATES'); ?></th>
			<td><?php echo $this->item->number_plates; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_UBER_FORM_LBL_DRIVER_NUMBER_SEAT'); ?></th>
			<td><?php echo $this->item->number_seat; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_UBER_FORM_LBL_DRIVER_CAR_TYPE'); ?></th>
			<td><?php echo $this->item->car_type; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_UBER_FORM_LBL_DRIVER_LICENSE'); ?></th>
			<td><?php echo $this->item->license; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_UBER_FORM_LBL_DRIVER_VEHICLE_REGISTRATION'); ?></th>
			<td>
			<?php
			foreach ((array) $this->item->vehicle_registration as $singleFile) : 
				if (!is_array($singleFile)) : 
					$uploadPath = 'images' . DIRECTORY_SEPARATOR . $singleFile;
					 echo '<a href="' . JRoute::_(JUri::root() . $uploadPath, false) . '" target="_blank">' . $singleFile . '</a> ';
				endif;
			endforeach;
		?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_UBER_FORM_LBL_DRIVER_ADDRESS'); ?></th>
			<td><?php echo $this->item->address; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_UBER_FORM_LBL_DRIVER_BALANCE'); ?></th>
			<td><?php echo $this->item->balance; ?></td>
		</tr>

	</table>

</div>

<?php if($canEdit && $this->item->checked_out == 0): ?>

	<a class="btn" href="<?php echo JRoute::_('index.php?option=com_uber&task=driver.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_UBER_EDIT_ITEM"); ?></a>

<?php endif; ?>

<?php if (JFactory::getUser()->authorise('core.delete','com_uber.driver.'.$this->item->id)) : ?>

	<a class="btn btn-danger" href="#deleteModal" role="button" data-toggle="modal">
		<?php echo JText::_("COM_UBER_DELETE_ITEM"); ?>
	</a>

	<div id="deleteModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="deleteModal" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3><?php echo JText::_('COM_UBER_DELETE_ITEM'); ?></h3>
		</div>
		<div class="modal-body">
			<p><?php echo JText::sprintf('COM_UBER_DELETE_CONFIRM', $this->item->id); ?></p>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal">Close</button>
			<a href="<?php echo JRoute::_('index.php?option=com_uber&task=driver.remove&id=' . $this->item->id, false, 2); ?>" class="btn btn-danger">
				<?php echo JText::_('COM_UBER_DELETE_ITEM'); ?>
			</a>
		</div>
	</div>

<?php endif; ?>