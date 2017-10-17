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
		
	js('input:hidden.driver_id').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('driver_idhidden')){
			js('#jform_driver_id option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_driver_id").trigger("liszt:updated");
	js('input:hidden.job_id').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('job_idhidden')){
			js('#jform_job_id option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_job_id").trigger("liszt:updated");
	});

	Joomla.submitbutton = function (task) {
		if (task == 'transaction.cancel') {
			Joomla.submitform(task, document.getElementById('transaction-form'));
		}
		else {
			
			if (task != 'transaction.cancel' && document.formvalidator.isValid(document.id('transaction-form'))) {
				
				Joomla.submitform(task, document.getElementById('transaction-form'));
			}
			else {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_uber&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="transaction-form" class="form-validate">

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_UBER_TITLE_TRANSACTION', true)); ?>
		<div class="row-fluid">
			<div class="span7 form-horizontal">
				<fieldset class="adminform">

									<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
				<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
				<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
				<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
				<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

				<?php echo $this->form->renderField('created_by'); ?>
				<?php echo $this->form->renderField('modified_by'); ?>				<?php echo $this->form->renderField('driver_id'); ?>

			<?php
				foreach((array)$this->item->driver_id as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="driver_id" name="jform[driver_idhidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>				<?php echo $this->form->renderField('type'); ?>
				<?php echo $this->form->renderField('value'); ?>
				<?php echo $this->form->renderField('comment'); ?>
				<?php echo $this->form->renderField('job_id'); ?>

			<?php
				foreach((array)$this->item->job_id as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="job_id" name="jform[job_idhidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>				<?php echo $this->form->renderField('created'); ?>


					<?php if ($this->state->params->get('save_history', 1)) : ?>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('version_note'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('version_note'); ?></div>
					</div>
					<?php endif; ?>
				</fieldset>
			</div>
			<div class="span5">
				<?php if ($this->item->job_id) {?>
					<h3>Thông tin chuyến xe</h3>
					<?php 
						// Get a db connection.
						$db = JFactory::getDbo();

						// Create a new query object.
						$query = $db->getQuery(true);

						// Select all records from the user profile table where key begins with "custom.".
						// Order it by the ordering field.
						$query->select('*');
						$query->from($db->quoteName('#__uber_job'));
						$query->where($db->quoteName('id') . ' = '. $this->item->job_id);
						

						// Reset the query using our newly populated query object.
						$db->setQuery($query);

						// Load the results as a list of stdClass objects (see later for more options on retrieving data).
						$results = $db->loadObject();
					?>
					<b>Giá: </b><?php echo number_format($results->price)?><br/>
					<b>Giá khuyến mại: </b><?php echo number_format($results->sale_price)?><br/>
					<b>Giá cho tài xế: </b><?php echo number_format($results->fee)?><br/>
					<b>Phí Ycar: </b><?php echo number_format($results->sale_price-$results->fee)?><br/>
					<?php 
						if ($results->invoice) {
							echo "<span style='color:red;'>Giá đã bao gồm thuế!</span>";
						}
					?>
				<?php }?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>

	</div>
</form>
