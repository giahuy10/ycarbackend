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
// Create and populate an object.

if ($submit) { 

	$profile = new stdClass();


	$profile->job_id = $jinput->get('job_id');
	$profile->state = 1;
	$profile->driver_id = $jinput->get('driver_id');
	$profile->reason = JRequest::getVar('reason');
	$profile->value = JRequest::getVar('value');
	$result = JFactory::getDbo()->insertObject('#__uber_history', $profile);
	
	
	// Create an object for the record we are going to update.
		$object = new stdClass();

		// Must be a valid primary key value.
		$object->id = $profile->job_id;
		$object->waiting_cancel = 1;
	

		// Update their details in the users table using id as the primary key.
		$result = JFactory::getDbo()->updateObject('#__uber_job', $object, 'id');
	
		$mailer = JFactory::getMailer();

		$config = JFactory::getConfig();
		$sender = array( 
			$config->get( 'mailfrom' ),
			$config->get( 'fromname' ) 
		);

		$mailer->setSender($sender);
		$recipient = array( 'lyht@onecard.vn','thuydt@onecard.vn','huynv@ltdvietnam.com' );

		$mailer->addRecipient($recipient);
		$body   = "<h2>Tài xế ".$user->name." gửi yêu cầu hủy chuyến số ".$profile->job_id ."</h2>
		<b>Lý do: </b>".$profile->reason;

		$mailer->setSubject('Tài xế hủy chuyến');
		$mailer->isHtml(true);

		$mailer->setBody($body);
		$send = $mailer->Send();
		if ( $send !== true ) {
			echo 'Error sending email: ';
		} else {
			//echo 'Mail sent';
		}
?>
	<div class="alert alert-success">
	
	  <strong>Success!</strong> Yêu cầu hủy chuyến xe đã được gửi. Chúng tôi sẽ xử lý trong thời gian sớm nhất! <br/>
		
	</div>	
<?php }
?>
<h2>Các chuyến đã mua</h2>
		<?php 
			$db = JFactory::getDbo();

			// Create a new query object.
			$query = $db->getQuery(true);

			// Select all records from the user profile table where key begins with "custom.".
			// Order it by the ordering field.
			$query->select('*');
			$query->from($db->quoteName('#__uber_job'));
			$query->where($db->quoteName('driver_id') . ' = '. $user->id);
			$query->where($db->quoteName('status') . ' = 0');
			$query->where($db->quoteName('state') . ' = 1');
			$query->where($db->quoteName('pick_up_time') . ' >= ( CURDATE() - INTERVAL 3 DAY )');
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
							<br/>
							<div class="">
						
						
							<form style="display:inline-block" method="post" id="form_<?php echo $item->id?>" action="<?php echo JRoute::_('index.php?option=com_uber&view=completed&Itemid=130')?>" onsubmit="return confirm('Bạn đã đón khách?');">
									
									<input type="submit" name="submit" value="Đón khách" class="btn btn-success"/>
									
									<input type="hidden" name="Itemid" value="130"/>
									<input type="hidden" name="option" value="com_uber"/>
									<input type="hidden" name="view" value="completed"/>
									<input type="hidden" name="job_id" value="<?php echo $item->id?>"/>
									

							</form>
							<button class="btn btn-danger" onclick="cancel(<?php echo $item->id?>)">Hủy chuyến</button>
							<form method="post" style="display:none;" id="cancel_form_<?php echo $item->id?>" action="<?php echo JRoute::_('index.php?option=com_uber&view=buy&Itemid=129')?>" onsubmit="return confirm('Bạn chắc chắn muốn hủy chuyến này?');">
									<label>Lý do hủy chuyến</label><br/>
									<textarea required name="reason"></textarea>
									<br/>
									<input type="submit" name="submit" value="Xác nhận" class="btn btn-danger"/>
									
									<input type="hidden" name="Itemid" value="129"/>
									<input type="hidden" name="option" value="com_uber"/>
									<input type="hidden" name="value" value="<?php echo $item->sale_price-$item->fee?>"/>
									<input type="hidden" name="view" value="buy"/>
									<input type="hidden" name="job_id" value="<?php echo $item->id?>"/>
									<input type="hidden" name="driver_id" value="<?php echo $user->id?>"/>
									

							</form>
							
								<div class="clearfix"></div>
							<span style="color:red; font-weight:bold">* Đối tác vui lòng bấm vào nút Đón Khách khi đã đón được khách.</span>
						
						</div>
					  </div>
					</div>
				  </div>
				  <?php  $check++;?>
			
				
			<?php }
				?>
				</div>
				<script>
					function cancel(id) {
						$("#cancel_form_"+id).toggle();
					}
				</script>
				
		