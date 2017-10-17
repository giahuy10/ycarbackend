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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'administrator/components/com_uber/assets/css/uber.css');
$document->addStyleSheet(JUri::root() . 'media/com_uber/css/list.css');

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_uber');
$saveOrder = $listOrder == 'a.`ordering`';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_uber&task=drivers.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'driverList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$sortFields = $this->getSortFields();
?>

<form action="<?php echo JRoute::_('index.php?option=com_uber&view=drivers'); ?>" method="post"
	  name="adminForm" id="adminForm">
	<?php if (!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php else : ?>
		<div id="j-main-container">
			<?php endif; ?>

            <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

			<div class="clearfix"></div>
			<table class="table table-striped" id="driverList">
				<thead>
				<tr>
					<?php if (isset($this->items[0]->ordering)): ?>
						<th width="1%" class="nowrap center hidden-phone">
                            <?php echo JHtml::_('searchtools.sort', '', 'a.`ordering`', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                        </th>
					<?php endif; ?>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value=""
							   title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
					</th>
					<?php if (isset($this->items[0]->state)): ?>
						<th width="1%" class="nowrap center">
								<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.`state`', $listDirn, $listOrder); ?>
</th>
					<?php endif; ?>

									<th class='left'>
				<?php echo JHtml::_('searchtools.sort',  'COM_UBER_DRIVERS_ID', 'a.`id`', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('searchtools.sort',  'COM_UBER_DRIVERS_PHONE', 'a.`phone`', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('searchtools.sort',  'COM_UBER_DRIVERS_TITLE', 'a.`title`', $listDirn, $listOrder); ?>
				</th>
				<th class='left  hidden-phone'>
				<?php echo JHtml::_('searchtools.sort',  'COM_UBER_DRIVERS_AVATAR', 'a.`avatar`', $listDirn, $listOrder); ?>
				</th>
				<th class='left  hidden-phone'>
				<?php echo JHtml::_('searchtools.sort',  'COM_UBER_DRIVERS_ID_CARD', 'a.`id_card`', $listDirn, $listOrder); ?>
				</th>
				<th class='left  hidden-phone'>
				<?php echo JHtml::_('searchtools.sort',  'COM_UBER_DRIVERS_NUMBER_PLATES', 'a.`number_plates`', $listDirn, $listOrder); ?>
				</th>
				<th class='left  hidden-phone'>
				<?php echo JHtml::_('searchtools.sort',  'COM_UBER_DRIVERS_NUMBER_SEAT', 'a.`number_seat`', $listDirn, $listOrder); ?>
				</th>
				<th class='left  hidden-phone'>
				<?php echo JHtml::_('searchtools.sort',  'COM_UBER_DRIVERS_CAR_TYPE', 'a.`car_type`', $listDirn, $listOrder); ?>
				</th>
				<th class='left  hidden-phone'>
				<?php echo JHtml::_('searchtools.sort',  'COM_UBER_DRIVERS_LICENSE', 'a.`license`', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('searchtools.sort',  'COM_UBER_DRIVERS_BALANCE', 'a.`balance`', $listDirn, $listOrder); ?>
				</th>

					
				</tr>
				</thead>
				<tfoot>
				<tr>
					<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
				</tfoot>
				<tbody>
				<?php foreach ($this->items as $i => $item) :
					$ordering   = ($listOrder == 'a.ordering');
					$canCreate  = $user->authorise('core.create', 'com_uber');
					$canEdit    = $user->authorise('core.edit', 'com_uber');
					$canCheckin = $user->authorise('core.manage', 'com_uber');
					$canChange  = $user->authorise('core.edit.state', 'com_uber');
					?>
					<tr class="row<?php echo $i % 2; ?>">

						<?php if (isset($this->items[0]->ordering)) : ?>
							<td class="order nowrap center hidden-phone">
								<?php if ($canChange) :
									$disableClassName = '';
									$disabledLabel    = '';

									if (!$saveOrder) :
										$disabledLabel    = JText::_('JORDERINGDISABLED');
										$disableClassName = 'inactive tip-top';
									endif; ?>
									<span class="sortable-handler hasTooltip <?php echo $disableClassName ?>"
										  title="<?php echo $disabledLabel ?>">
							<i class="icon-menu"></i>
						</span>
									<input type="text" style="display:none" name="order[]" size="5"
										   value="<?php echo $item->ordering; ?>" class="width-20 text-area-order "/>
								<?php else : ?>
									<span class="sortable-handler inactive">
							<i class="icon-menu"></i>
						</span>
								<?php endif; ?>
							</td>
						<?php endif; ?>
						<td class="hidden-phone">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<?php if (isset($this->items[0]->state)): ?>
							<td class="center">
								<?php echo JHtml::_('jgrid.published', $item->state, $i, 'drivers.', $canChange, 'cb'); ?>
</td>
						<?php endif; ?>

										<td>

					<?php echo $item->id; ?>
				</td>				<td>
				
					
				<a href="tel:<?php echo $item->phone ?>"><?php echo $item->phone ?></a>

				</td>				<td>
				<?php if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) : ?>
					<?php echo JHtml::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'drivers.', $canCheckin); ?>
				<?php endif; ?>
				<?php if ($canEdit) : ?>
					<a href="<?php echo JRoute::_('index.php?option=com_uber&task=driver.edit&id='.(int) $item->id); ?>">
					<?php echo $this->escape($item->title); ?></a>
				<?php else : ?>
					<?php echo $this->escape($item->title); ?>
				<?php endif; ?>

					
				</td>				<td class=" hidden-phone">

					<?php
						if (!empty($item->avatar)) :
							$avatarArr = explode(',', $item->avatar);
							foreach ($avatarArr as $fileSingle) :
								if (!is_array($fileSingle)) :
									$uploadPath = 'images' .DIRECTORY_SEPARATOR . $fileSingle;
									echo '<a href="' . JRoute::_(JUri::root() . $uploadPath, false) . '" target="_blank" title="See the avatar">' . $fileSingle . '</a> | ';
								endif;
							endforeach;
						else:
							echo $item->avatar;
						endif; ?>
				</td>				<td  class=" hidden-phone">

					<?php echo $item->id_card; ?>
				</td>				<td class=" hidden-phone">

					<?php echo $item->number_plates; ?>
				</td>				<td class=" hidden-phone">

					<?php echo $item->number_seat; ?>
				</td>				<td class=" hidden-phone">

					<?php echo $item->car_type; ?>
				</td>				<td class=" hidden-phone">

					<?php echo $item->license; ?>
				</td>				<td>

					<?php echo number_format($item->balance); ?>
				</td>

					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
            <input type="hidden" name="list[fullorder]" value="<?php echo $listOrder; ?> <?php echo $listDirn; ?>"/>
			<?php echo JHtml::_('form.token'); ?>
		</div>
</form>
<script>
    window.toggleField = function (id, task, field) {

        var f = document.adminForm, i = 0, cbx, cb = f[ id ];

        if (!cb) return false;

        while (true) {
            cbx = f[ 'cb' + i ];

            if (!cbx) break;

            cbx.checked = false;
            i++;
        }

        var inputField   = document.createElement('input');

        inputField.type  = 'hidden';
        inputField.name  = 'field';
        inputField.value = field;
        f.appendChild(inputField);

        cb.checked = true;
        f.boxchecked.value = 1;
        window.submitform(task);

        return false;
    };
</script>