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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

// Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_uber', JPATH_SITE);
$doc = JFactory::getDocument();
$doc->addScript(JUri::base() . '/media/com_uber/js/form.js');

$user    = JFactory::getUser();
$canEdit = UberHelpersUber::canUserEdit($this->item, $user);


?>

<div class="driver-edit front-end-edit">
	<?php if (!$canEdit) : ?>
		<h3>
			<?php throw new Exception(JText::_('COM_UBER_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
		</h3>
	<?php else : ?>
		<?php if (!empty($this->item->id)): ?>
			<h1><?php echo JText::sprintf('COM_UBER_EDIT_ITEM_TITLE', $this->item->id); ?></h1>
		<?php else: ?>
			<h1><?php echo JText::_('COM_UBER_ADD_ITEM_TITLE'); ?></h1>
		<?php endif; ?>

		<form id="form-driver"
			  action="<?php echo JRoute::_('index.php?option=com_uber&task=driver.save'); ?>"
			  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
			
	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />

	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />

	<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />

	<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />

	<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

				<?php echo $this->form->getInput('created_by'); ?>
				<?php echo $this->form->getInput('modified_by'); ?>
	<?php echo $this->form->renderField('phone'); ?>

	<?php echo $this->form->renderField('title'); ?>

	<?php echo $this->form->renderField('avatar'); ?>

	<?php if (!empty($this->item->avatar)) :
		foreach ((array) $this->item->avatar as $singleFile) : 
			if (!is_array($singleFile)) :
				echo '<a href="' . JRoute::_(JUri::root() . 'images' . DIRECTORY_SEPARATOR . $singleFile, false) . '">' . $singleFile . '</a> ';
			endif;
		endforeach;
	endif; ?>
	<input type="hidden" name="jform[avatar_hidden]" id="jform_avatar_hidden" value="<?php echo str_replace('Array,', '', implode(',', (array) $this->item->avatar)); ?>" />
	<?php echo $this->form->renderField('id_card'); ?>

	<?php echo $this->form->renderField('number_plates'); ?>

	<?php echo $this->form->renderField('number_seat'); ?>

	<?php echo $this->form->renderField('car_type'); ?>

	<?php echo $this->form->renderField('license'); ?>

	<?php echo $this->form->renderField('vehicle_registration'); ?>

	<?php if (!empty($this->item->vehicle_registration)) :
		foreach ((array) $this->item->vehicle_registration as $singleFile) : 
			if (!is_array($singleFile)) :
				echo '<a href="' . JRoute::_(JUri::root() . 'images' . DIRECTORY_SEPARATOR . $singleFile, false) . '">' . $singleFile . '</a> ';
			endif;
		endforeach;
	endif; ?>
	<input type="hidden" name="jform[vehicle_registration_hidden]" id="jform_vehicle_registration_hidden" value="<?php echo str_replace('Array,', '', implode(',', (array) $this->item->vehicle_registration)); ?>" />
	<?php echo $this->form->renderField('address'); ?>

	<?php echo $this->form->renderField('balance'); ?>

			<div class="control-group">
				<div class="controls">

					<?php if ($this->canSave): ?>
						<button type="submit" class="validate btn btn-primary">
							<?php echo JText::_('JSUBMIT'); ?>
						</button>
					<?php endif; ?>
					<a class="btn"
					   href="<?php echo JRoute::_('index.php?option=com_uber&task=driverform.cancel'); ?>"
					   title="<?php echo JText::_('JCANCEL'); ?>">
						<?php echo JText::_('JCANCEL'); ?>
					</a>
				</div>
			</div>

			<input type="hidden" name="option" value="com_uber"/>
			<input type="hidden" name="task"
				   value="driverform.save"/>
			<?php echo JHtml::_('form.token'); ?>
		</form>
	<?php endif; ?>
</div>
