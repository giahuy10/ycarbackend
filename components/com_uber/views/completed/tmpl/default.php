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
if ($submit) {
	$job_id = $jinput->get('job_id');
	// Create and populate an object.
	$profile = new stdClass();
	$profile->id=$job_id;
	$profile->status=1;

	// Insert the object into the user profile table.
	$result = JFactory::getDbo()->updateObject('#__uber_job', $profile, 'id');?>
	<div class="alert alert-success">
	
	  <strong>Success!</strong> Chúc mừng bạn đã hoàn thành chuyến đi. <br/>
		
	</div>
<?php }
?>
<h2>Các chuyến đã hoàn thành</h2>
		<?php 
			$db = JFactory::getDbo();

			// Create a new query object.
			$query = $db->getQuery(true);

			// Select all records from the user profile table where key begins with "custom.".
			// Order it by the ordering field.
			$query->select('*');
			$query->from($db->quoteName('#__uber_job'));
			$query->where($db->quoteName('driver_id') . ' = '. $user->id);
			$query->where($db->quoteName('status') . ' = 1');
			$query->where($db->quoteName('state') . ' = 1');
			$query->order('pick_up_time DESC');

			// Reset the query using our newly populated query object.
			$db->setQuery($query);

			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$results = $db->loadObjectList();
			$check = 0;?>
			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
			<?php 
			foreach ($results as $item) {
				
			
			?>
				
				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="headingThree">
					  <h4 class="panel-title">
						<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $item->id?>" aria-expanded="false" aria-controls="collapse<?php echo $item->id?>">
						 <?php echo UberHelpersUber::show_job_header($item);?>
						</a>
					  </h4>
					</div>
					<div id="collapse<?php echo $item->id?>" class="panel-collapse collapse <?php if ($check == 0) echo " in"?>" role="tabpanel" aria-labelledby="heading<?php echo $job_detail->id?>">
					  <div class="panel-body">
						<?php 
								echo UberHelpersUber::show_customer_info($item,$user->id);
								echo UberHelpersUber::show_job_add_location($item);
								echo UberHelpersUber::show_job_fair($item);
								echo UberHelpersUber::show_job_comment($item);
								
							?>
							
						
					  </div>
					</div>
				  </div>
				  <?php  $check++;?>
			
				
			<?php }
				?>
				</div>