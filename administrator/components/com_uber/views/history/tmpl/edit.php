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
		
	js('input:hidden.job_id').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('job_idhidden')){
			js('#jform_job_id option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_job_id").trigger("liszt:updated");
	js('input:hidden.driver_id').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('driver_idhidden')){
			js('#jform_driver_id option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_driver_id").trigger("liszt:updated");
	});

	Joomla.submitbutton = function (task) {
		if (task == 'history.cancel') {
			Joomla.submitform(task, document.getElementById('history-form'));
		}
		else {
			
			if (task != 'history.cancel' && document.formvalidator.isValid(document.id('history-form'))) {
				
				Joomla.submitform(task, document.getElementById('history-form'));
			}
			else {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_uber&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="history-form" class="form-validate">

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_UBER_TITLE_HISTORY', true)); ?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">

									<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
				<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
				<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
				<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
				<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

				<?php echo $this->form->renderField('created_by'); ?>
				<?php echo $this->form->renderField('modified_by'); ?>				<?php echo $this->form->renderField('job_id'); ?>

			<?php
				foreach((array)$this->item->job_id as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="job_id" name="jform[job_idhidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>				<?php echo $this->form->renderField('driver_id'); ?>

			<?php
				foreach((array)$this->item->driver_id as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="driver_id" name="jform[driver_idhidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>				<?php echo $this->form->renderField('reason'); ?>
				<?php echo $this->form->renderField('confirmed'); ?>
				<?php echo $this->form->renderField('comment'); ?>
				<?php echo $this->form->renderField('comment2'); ?>


					<?php if ($this->state->params->get('save_history', 1)) : ?>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('version_note'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('version_note'); ?></div>
					</div>
					<?php endif; ?>
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>

	</div>
</form>
