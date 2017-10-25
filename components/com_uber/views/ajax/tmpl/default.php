<?php
header('Access-Control-Allow-Origin: *'); 
?>
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
$task = $jinput->get('task');
$contents = file_get_contents("php://input");
$requests = json_decode($contents);
switch ($task) {
	case "list_job":
?>

<?php 
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select('*');
		$query->from($db->quoteName('#__uber_job'));
		$query->where('state = 1');
		
		$query->where('driver_id = 0');
		$query->order('pick_up_time ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();
	
		if ($results) {
	?>
	<?php foreach ($results as $i => $item) : ?>
			<?php $canview = UberHelpersUber::is_bought($user->id, $item->id);
			//echo $canview;
			?>
			<?php 
			$seats = UberHelpersUber::get_seats($user->id);
			?>
			<div class="job-item 
			
			<?php if ($seats < $item->number_seat) echo "disable"?>">
				<div>
		<?php echo UberHelpersUber::show_job_header($item)?>

		
			
			 
			 
		<?php if ($seats < $item->number_seat) {?>
			<?php
			//$date_available = strtotime($item->pick_up_time-3600);
			//echo date("Y-m-d h:i:s",$date_available);
			$testDateStr = strtotime($item->pick_up_time);
			$currentDate = date("Y-m-d H:i:s");
			$finalDate = date("Y-m-d H:i:s", strtotime("-2 hour", $testDateStr));
			
			if ($item->available || $currentDate > $finalDate) {?>
			
			
			<a href="index.php?option=com_uber&view=job&id=<?php echo $item->id?>" class="btn btn-info">Xem chi tiết</a>
			<?php } else { ?>
				<span style="color:red;font-style: italic;font-size: 12px;">Chuyến xe chưa được bán!</span>
			<?php }?>
		<?php } else {?>
			<span style="color:red;font-style: italic;font-size: 12px;">* Xe bạn không đủ chỗ</span>
		<?php }?>
			</div>
		</div>

		
		<?php endforeach; ?>
		
		<?php }
		else {
			echo "Hiện tại không có chuyến xe nào!";
		}
		?>
<?php 
		break; // END LIST JOBS
	case "login": // START LOGIN
			
			$username = $requests->username;
			$password = $requests->password;
			$db    = JFactory::getDbo();
			  $query = $db->getQuery(true)
				  ->select('id, password')
				  ->from('#__users')
				  ->where('username=' . $db->quote($username));
			  $db->setQuery($query);
			  $result = $db->loadObject();
			  if ($result)
			  {
				  $match = JUserHelper::verifyPassword($password, $result->password, $result->id);
				  if ($match === true)
				  {
					  // Bring this in line with the rest of the system
					  //$user = JUser::getInstance($result->id);
					  //echo 'Joomla! Authentication was successful!';
					  $user = JFactory::getUser($result->id);
					//$user->token = apiToken($user->id);
					$key=md5("YCAR123124124".$result->id);
					$user->token = hash('sha256', $key);
					$user->user_id = $user->id;
					$user->balance = UberHelpersUber::get_balance($user->username);
					 $user = json_encode($user);
					echo '{"userData": ' .$user . '}';
				  }
				  else
				  {
					  // Invalid password
					  // Prmitive error handling
					  echo '{"error":{"text":"Sai mật khẩu"}}';
				  }
			  } else {
				  // Invalid user
				  // Prmitive error handling
				 echo '{"error":{"text":"Tài khoản không tồn tại"}}';
			  }
			
				

			
		
		break; // END LOGIN
		
	case "detail_json": // START DETAIL JSON JOB
		$job_id = $jinput->get('job_id');
		$job_detail = UberHelpersUber::get_job_detail($job_id);
		
		$job_detail->ycarfee = number_format($job_detail->sale_price-$job_detail->fee); 
		$job_detail->sale_price = number_format($job_detail->sale_price);
		$job_detail->fee = number_format($job_detail->fee);
		if (!$job_detail->title) {
			$job_detail->title = $job_detail->pick_up_location." → ".$job_detail->drop_location;
		}
		
		$job_detail->pick_up_time =  date ("G:i - d/m/Y",strtotime($job_detail->pick_up_time))	;
		echo json_encode($job_detail);
		break; // START DETAIL JSON JOB
		
	case "detail": // START DETAIL JOB
		$job_id = $jinput->get('job_id');
		$job_detail = UberHelpersUber::get_job_detail($job_id);
		//var_dump($job_detail);
		if ((!$job_detail->driver_id && $job_detail->state == 1) ||  $job_detail->driver_id==$user->id) {?>
		
		<?php echo UberHelpersUber::show_job_header($job_detail);
		echo UberHelpersUber::show_job_add_location($job_detail);
			echo UberHelpersUber::show_job_fair($job_detail);
			echo UberHelpersUber::show_job_comment($job_detail);
			$fee = $job_detail->sale_price - $job_detail->fee;
		?>
		
			
			
				
			<h3>Thông tin khách hàng:</h3>
			<?php 
				$canview = UberHelpersUber::is_bought($user->id,$job_id);
				if ($canview) {?>
					<?php echo UberHelpersUber::show_job_customer($job_detail);?>
				<?php } else {?>
					<?php if ($balance < $fee) {?>
						<button class="btn btn-info" disabled>Tài khoản không đủ để mua chuyến xe này!</button>
					<?php } else {?>
					<?php $testDateStr = strtotime($job_detail->pick_up_time);
					$currentDate = date("Y-m-d H:i:s");
					$finalDate = date("Y-m-d H:i:s", strtotime("-2 hour", $testDateStr));
					
					if ($job_detail->available || $currentDate > $finalDate) {?>
					<button class="btn btn-info" id="buy_job" onclick="buy_job()">Mua chuyến xe để xem thông tin</button>
					
					<?php } else {?>
						<span style="color:red;font-style: italic;font-size: 12px;">Chuyến xe chưa được bán!</span>
					<?php }?>
					<script>
						function buy_job() {
					
						if (confirm("Bạn chắc chắn muốn mua chuyến đi này?") == true) {
							var xmlhttp = new XMLHttpRequest();
							xmlhttp.onreadystatechange = function() {
								if (this.readyState == 4 && this.status == 200) {
									alert("Chuyến xe đã là của bạn!")
								}
							};
							xmlhttp.open("GET", "index.php?option=com_uber&view=ajax&format=raw&task=buy_job&job_id=<?php echo $job_id?>", true);
							xmlhttp.send();
						
							}
							else{
								return false;
							}
						}
					</script>
					<?php }?>
				<?php }?>
		<?php }else {
			// NOT AVAILABLE
			echo "Chuyến xe đã được bán cho người khác!";
		}
		break;// END DETAIL JOB
	
	case "bought": // START BOUGHT JOB
			
			$driver_id = $requests->user_id;
			$db = JFactory::getDbo();

			// Create a new query object.
			$query = $db->getQuery(true);

			// Select all records from the user profile table where key begins with "custom.".
			// Order it by the ordering field.
			$query->select('*');
			$query->from($db->quoteName('#__uber_job'));
			$query->where($db->quoteName('driver_id') . ' = '. $driver_id);
			$query->where($db->quoteName('status') . ' = 0');
			$query->where($db->quoteName('state') . ' = 1');
			//$query->where($db->quoteName('pick_up_time') . ' >= ( CURDATE() - INTERVAL 3 DAY )');
			$query->order('pick_up_time DESC');

			// Reset the query using our newly populated query object.
			$db->setQuery($query);

			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$job_bought = $db->loadObjectList();
			$data=[];
			foreach ($job_bought as $item) {
				if ($item->title=="") {
					$item->title = $item->pick_up_location." -> ".$item->drop_location;
				}
				$item->ycarfee = $item->sale_price - $item->fee;
				$data[]=$item;
			}
			echo json_encode($data);
		
		break; // END BOUGHT JOB
	case "new": // START NEW JOB	
			$driver_id = $requests->user_id;
			$db = JFactory::getDbo();

			// Create a new query object.
			$query = $db->getQuery(true);

			// Select all records from the user profile table where key begins with "custom.".
			// Order it by the ordering field.
			$query->select('*');
			$query->from($db->quoteName('#__uber_job'));
			$query->where($db->quoteName('driver_id') . ' = 0');
			//$query->where($db->quoteName('status') . ' = 1');
			$query->where($db->quoteName('state') . ' = 1');
			//$query->where($db->quoteName('pick_up_time') . ' >= ( CURDATE() - INTERVAL 3 DAY )');
			$query->order('pick_up_time DESC');

			// Reset the query using our newly populated query object.
			$db->setQuery($query);

			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$job_bought = $db->loadObjectList();
			$data=[];
			foreach ($job_bought as $item) {
				if ($item->title=="") {
					$item->title = $item->pick_up_location." → ".$item->drop_location;
				}
				$item->ycarfee = number_format($item->sale_price-$item->fee); 
				$item->sale_price = number_format($item->sale_price);
				$item->fee = number_format($item->fee);			
				$item->pick_up_time =  date ("G:i - d/m/Y",strtotime($item->pick_up_time))	;
				$data[]=$item;
			}
			echo json_encode($data);
		break; // END NEW JOB
	case "taken_client": // START TAKE CLIENT FOR DRIVER
		$driver_id = $requests->user_id;
		$job_id = $requests->job_id;
		// Create an object for the record we are going to update.
			$object = new stdClass();

			// Must be a valid primary key value.
			$object->id = $job_id;
			$object->status = 1;
			

			// Update their details in the users table using id as the primary key.
			$result = JFactory::getDbo()->updateObject('#__uber_job', $object, 'id');
			 echo '{"success":{"text":"Đón khách thành công"}}';
			
		break; // END TAKE CLIENT FOR DRIVER
	case "cancel": // START CANCEL JOB
		$job_detail = UberHelpersUber::get_job_detail($requests->job_id);
		$profile = new stdClass();


		$profile->job_id = $requests->job_id;
		$profile->state = 1;
		$profile->driver_id =  $requests->user_id;
		$profile->reason = $requests->reason;
		$profile->value = $job_detail->sale_price - $job_detail->fee;
		$result = JFactory::getDbo()->insertObject('#__uber_history', $profile);
	
	
	// Create an object for the record we are going to update.
		$object = new stdClass();

		// Must be a valid primary key value.
		$object->id = $profile->job_id;
		$object->waiting_cancel = 1;
	

		// Update their details in the users table using id as the primary key.
		$result = JFactory::getDbo()->updateObject('#__uber_job', $object, 'id');
		$driver = JFactory::getUser($requests->user_id);
		$mailer = JFactory::getMailer();

		$config = JFactory::getConfig();
		$sender = array( 
			$config->get( 'mailfrom' ),
			$config->get( 'fromname' ) 
		);
	
		$mailer->setSender($sender);
		$recipient = array( 'huynv@ltdvietnam.com','anjakahuy@gmail' );

		$mailer->addRecipient($recipient);
		$body   = "<h2>Tài xế ".$driver->name." gửi yêu cầu hủy chuyến số ".$profile->job_id ."</h2>
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
		echo '{"success":{"text":"Hủy chuyến thành công"}}';
		break; // END CANCEL JOB
	case "completed": // START COMPLETED JOB	
		$driver_id = $requests->user_id;
			$db = JFactory::getDbo();

			// Create a new query object.
			$query = $db->getQuery(true);

			// Select all records from the user profile table where key begins with "custom.".
			// Order it by the ordering field.
			$query->select('*');
			$query->from($db->quoteName('#__uber_job'));
			$query->where($db->quoteName('driver_id') . ' = '. $driver_id);
			$query->where($db->quoteName('status') . ' = 1');
			$query->where($db->quoteName('state') . ' = 1');
			//$query->where($db->quoteName('pick_up_time') . ' >= ( CURDATE() - INTERVAL 3 DAY )');
			$query->order('pick_up_time DESC');

			// Reset the query using our newly populated query object.
			$db->setQuery($query);

			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$job_bought = $db->loadObjectList();
			$data=[];
			foreach ($job_bought as $item) {
				if ($item->title=="") {
					$item->title = $item->pick_up_location." -> ".$item->drop_location;
				}
				$data[]=$item;
			}
			echo json_encode($data);
		break; // END COMPLETED JOB
	case "history": // START HISTORY
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select('*');
		$query->from($db->quoteName('#__uber_transaction'));
		$query->where($db->quoteName('driver_id') . ' = '. $user->id);
		$query->where($db->quoteName('state') . ' = 1');
		$query->order('id DESC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$history_transactions = $db->loadObjectList();
		?>
			<table class="table table-bordered">
			<thead>
				<tr>
					<td>Ngày</td>
					<td>Loại</td>
					
					<td>Số tiền</td>
					<td>Ghi chú</td>
					
				</tr>
			</thead>
			<tbody>
			
				<?php foreach ($history_transactions as $item) {?>
					<tr>
						<td><?php echo $item->created?></td>
						<td><?php if ($item->type == 1) 
										echo "<span style='color:green'><b>Nạp tiền</b></span>";
										
									elseif ($item->type == 2) 
										echo "<span style='color:#0038cf'><b>Hủy chuyến</b></span>"; 
									else 
										echo "<span style='color:red'><b>Mua chuyến</b></span>"; 
									?>
									</td>
					
						<td><?php 
						if ($item->type==3) {
							$item->value = 0-$item->value;
							if ($item->value > 0)
								echo "+";
						}elseif ($item->type==1) {
							echo "+";
						}else {
							if ($item->value > 0)
								echo "+";
						}
							
						echo number_format($item->value)?></td>
						
						<td>
						<?php if ($item->job_id) echo "Chuyến số: ".$item->job_id."<br/>"?>
						<?php echo $item->comment?></td>
					</td>
				<?php }?>
			</tbody>
		</table>
		<?php
		break; // END HISTORY
	case "buy_job": // START BUYING JOB
	
		$job_id = $jinput->get('job_id');
		$job_detail = UberHelpersUber::get_job_detail($job_id);
		$driver_id = $user->id;
		//$balance = $jinput->get('balance');
		$fee = $job_detail->sale_price - $job_detail->fee;
		$object = new stdClass();

		// Must be a valid primary key value.
		$object->id = $job_id;
		$object->driver_id = $driver_id;
		$object->created = date('Y-m-d h:i:s');

		// Update their details in the users table using id as the primary key.
		$result = JFactory::getDbo()->updateObject('#__uber_job', $object, 'id');
		
		$object2 = new stdClass();

		// Must be a valid primary key value.
		$object2->id = $driver_id;
		$object2->balance = $balance-$fee;


		// Update their details in the users table using id as the primary key.
		$result2 = JFactory::getDbo()->updateObject('#__uber_driver', $object2, 'id');
		
		// Create and populate an object.
			$profile = new stdClass();
			$profile->driver_id = $driver_id;
			$profile->job_id=$job_id;
			$profile->value=$fee;
			$profile->type=3;
			$profile->state=1;
			$profile->created_by=$driver_id;
			
			

			// Insert the object into the user profile table.
			$result3 = JFactory::getDbo()->insertObject('#__uber_transaction', $profile);
					$order = new stdClass();
					$order->driver_id = $driver_id;
					$order->job_id=$job_id;
					$result4 = JFactory::getDbo()->insertObject('#__uber_orders', $order);	

		// SEND email:
		$mailer = JFactory::getMailer();
		$config = JFactory::getConfig();
		$sender = array( 
			$config->get( 'mailfrom' ),
			$config->get( 'fromname' ) 
		);

		$mailer->setSender($sender);
		$recipient = array('anjakahuy@gmail.com','huynv@ltdvietnam.com');

		$mailer->addRecipient($recipient);
		$body   = "Chuyến xe số ".$job_id." khách hàng ".$job_detail->customer_name." (<a href='tel:".$job_detail->customer_phone."'>".$job_detail->customer_phone."</a>) từ ".$job_detail->pick_up_location." đi ".$job_detail->drop_location." lúc ".$job_detail->pick_up_time." đã được mua bởi tài xế ".$user->name." mã số ".$driver_id." số điện thoại <a href='tel:".$user->username."'>".$user->username."</a>";
		$mailer->setSubject('Có tài xế mua chuyến đi');
		$mailer->isHtml(true);
		$mailer->setBody($body);
		$send = $mailer->Send();
		if ( $send !== true ) {
			echo 'Error sending email: ';
		} else {
			echo 'Mail sent';
		}
		// SEND SMS:


		
		$APIKey="D244732ABEDFAB68EF9A37E344FE96";
		$SecretKey="4D3EB6856F60EA6148A2A76925701C";
		$YourPhone="0906147557";
		$Content="Co tai xe mua chuyen di ".$user->username;
		
		$SendContent=urlencode($Content);
		$data="http://rest.esms.vn/MainService.svc/json/SendMultipleMessage_V4_get?Phone=$YourPhone&ApiKey=$APIKey&SecretKey=$SecretKey&Content=$SendContent&SmsType=4";
		
		$curl = curl_init($data); 
		curl_setopt($curl, CURLOPT_FAILONERROR, true); 
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		$result = curl_exec($curl); 
			
		$obj = json_decode($result,true);
		if($obj['CodeResult']==100)
		{
			print "<br>";
			print "CodeResult:".$obj['CodeResult'];
			print "<br>";
			print "CountRegenerate:".$obj['CountRegenerate'];
			print "<br>";     
			print "SMSID:".$obj['SMSID'];
			print "<br>";
		}
		else
		{
			print "ErrorMessage:".$obj['ErrorMessage'];
		}
		
		echo "Chuyến xe đã là của bạn!";
    

		break; // END BUY JOB	
	default:
		echo "default";

}// END SWTICH?>		