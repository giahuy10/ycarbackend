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
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'media/com_uber/css/form.css');
?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
		
	});

	Joomla.submitbutton = function (task) {
		if (task == 'job.cancel') {
			Joomla.submitform(task, document.getElementById('job-form'));
		}
		else {
			
			if (task != 'job.cancel' && document.formvalidator.isValid(document.id('job-form'))) {
				
				Joomla.submitform(task, document.getElementById('job-form'));
			}
			else {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_uber&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="job-form" class="form-validate">

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_UBER_TITLE_JOB', true)); ?>
		<div class="row-fluid">
			<div class="span7 form-horizontal">
				
				<fieldset class="adminform">

				<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
				<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
				<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
				<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
				<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
				<input type="hidden" name="jform[created]" value="<?php echo date('Y-m-d'); ?>" />

				<?php echo $this->form->renderField('created_by'); ?>
				<?php echo $this->form->renderField('modified_by'); ?>				
				<?php echo $this->form->renderField('available'); ?>	<?php echo $this->form->renderField('sent_notice'); ?>
				<?php echo $this->form->renderField('title'); ?>
				<?php echo $this->form->renderField('customer_name'); ?>
				<?php echo $this->form->renderField('customer_phone'); ?>
				<?php echo $this->form->renderField('number_passenger'); ?>
				<?php echo $this->form->renderField('is_airport'); ?>
				<?php echo $this->form->renderField('way'); ?>
				<?php echo $this->form->renderField('flight_number'); ?>
				<?php //echo $this->form->renderField('district'); ?>
				<?php echo $this->form->renderField('pick_up_location'); ?>
				<?php echo $this->form->renderField('pick_up_time'); ?>
				<?php echo $this->form->renderField('drop_location'); ?>
				<?php echo $this->form->renderField('number_seat'); ?>
				<?php if ($this->item->invoice) {?>
					<h3><span style="color:red">* Giá đã bao gồm hóa đơn </span></h3>
				<?php }?>
				<?php if ($this->item->add_location) {?>
					<?php $location_price = 0?>
					<?php if ($this->item->address_1) {
						$location_price+=30000;
					} if ($this->item->address_2) {
						$location_price+=30000;
						}?>
					<h3><span style="color:red">* Giá đã bao gồm <?php echo number_format($location_price)?>vnđ đón thêm điểm </span></h3>	
				<?php }?>
				<?php echo $this->form->renderField('price'); ?>
				<?php echo $this->form->renderField('sale_price'); ?>
				<?php echo $this->form->renderField('fee'); ?>
				<?php echo $this->form->renderField('comment'); ?>
				<?php echo $this->form->renderField('driver_id'); ?>

					<?php if ($this->state->params->get('save_history', 1)) : ?>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('version_note'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('version_note'); ?></div>
					</div>
					<?php endif; ?>
				</fieldset>
			</div>
			<div class="span5">
			<?php echo $this->form->renderField('invoice'); ?>
			<?php echo $this->form->renderField('invoice_to_driver'); ?>
			<?php echo $this->form->renderField('company'); ?>
			<?php echo $this->form->renderField('mst'); ?>
			<?php echo $this->form->renderField('address'); ?>
			<?php echo $this->form->renderField('address_inoivce'); ?>
			<hr/>
			<?php echo $this->form->renderField('add_location'); ?>
			<?php echo $this->form->renderField('address_1'); ?>
			<?php echo $this->form->renderField('address_2'); ?>
				
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>

	</div>
</form>
