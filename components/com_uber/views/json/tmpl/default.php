<?php
header('Access-Control-Allow-Origin: *'); 

// JSON API FOR APPP
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

$response = new stdClass();
$user       = JFactory::getUser();
$balance = UberHelpersUber::get_balance($user->username);
$jinput = JFactory::getApplication()->input;
$task = $jinput->get('task');
$contents = file_get_contents("php://input");
$requests = json_decode($contents);
//$requests->user_id = 5;

$driver_id = $requests->user_id;
if ($driver_id) {
    $player_ids = UberHelpersUber::get_player_id($driver_id);
}
function format_price($price) {
	$price = number_format($price)."đ";
	return $price; 
}
switch ($task) {
    case "balance":
         $profile = new stdClass();
        $profile->balance = UberHelpersUber::get_balance_id($requests->user_id);
        echo json_encode($profile);
    case "test":
             //UberHelpersUber::get_job_time(5,'2017-10-17 14:11:00');
           
        break;
    case "saveplayerid": //START SAVE PLAYER ID
        $profile = new stdClass();
        $profile->driver_id = $requests->user_id;
         $profile->player_id = $requests->player_id;
         $check_player_id = UberHelpersUber::check_player_id($profile->driver_id,$profile->player_id);
         if (!$check_player_id) {
             	$result6 = JFactory::getDbo()->insertObject('#__uber_playerid', $profile);
         }
        
        echo json_encode($profile);
        break; // END SAVE PLAYER ID
    case "removeplayerid": //START SAVE PLAYER ID
       
     
         $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        
        // delete all custom keys for user 1001.
        $conditions = array(
            $db->quoteName('driver_id') . ' = '.$requests->user_id, 
            $db->quoteName('player_id') . ' = ' . $db->quote($requests->player_id)
        );
        
        $query->delete($db->quoteName('#__uber_playerid'));
        $query->where($conditions);
        
        $db->setQuery($query);
        
        $result = $db->execute();
         
        $response->res_type=1;
		$response->res_message="Xoa ok";
		
		echo json_encode($response);
       
        break; // END SAVE PLAYER ID    
	case "news": // START NEWS
		$db    = JFactory::getDbo();
			  $query = $db->getQuery(true)
				  ->select('*')
				  ->from('#__content')
				  ->where('catid= 8 and state = 1');
			  $db->setQuery($query);
			  $result = $db->loadObjectlist();
			  echo json_encode($result);
		break; // END NEWS
	case "topup": // START TOPUP CONTENT
				$db    = JFactory::getDbo();
			  $query = $db->getQuery(true)
				  ->select('*')
				  ->from('#__content')
				  ->where('id= 1');
			  $db->setQuery($query);
			  $result = $db->loadObject();
			  echo json_encode($result);
		break; // END TOPUP CONTENT
	case "changepassword": // START CHANGE PASSWORD
			$username = $requests->username;
			$oldpassword = $requests->oldpassword;
			$password = $requests->password;
			$password2 = $requests->password2;
			$db    = JFactory::getDbo();
			  $query = $db->getQuery(true)
				  ->select('id, password')
				  ->from('#__users')
				  ->where('username=' . $db->quote($username));
			  $db->setQuery($query);
			  $result = $db->loadObject();
			  if ($result)
			  {
				  $match = JUserHelper::verifyPassword($oldpassword, $result->password, $result->id);
				  if ($match === true)
				  {
					  $pass= JUserHelper::hashPassword($password);
					  $result->password = $pass;
					  $updatepassword = JFactory::getDbo()->updateObject('#__users', $result, 'id');
					   $result = json_encode($result);
						echo '{"userData": ' .$result . '}';
					
				  }else {
					   echo '{"error":{"text":"Sai mật khẩu cũ"}}';
				  }
			  }else {
				  echo '{"error":{"text":"Tài khoản không tồn tại"}}';
			  }  
		break;
	case "signup": // START SIGN UP 
		
		$check_account = UberHelpersUber::check_account($requests);
		if ($check_account == 1) {
		jimport('joomla.user.helper');
		$requests->password = JUserHelper::hashPassword($requests->password);
		$requests->state = 1;
		$result4 = JFactory::getDbo()->insertObject('#__uber_driver', $requests);
			$response->res_type=1;
			$response->res_message="YCar sẽ duyệt tài khoản của bạn trong thời gian sớm nhất!";
			
		}else {
			$response->res_type=2;
			$response->res_message=$check_account;
		}
		echo json_encode($response);
		break; // END SIGN UP
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
					$user->player_id = "";
					$user->balance = format_price($user->balance);
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
		
		$job_detail->ycarfee = format_price($job_detail->sale_price-$job_detail->fee); 
		$job_detail->sale_price = format_price($job_detail->sale_price);
		$job_detail->fee = format_price($job_detail->fee);
		if (!$job_detail->title) {
			$job_detail->title = $job_detail->pick_up_location." → ".$job_detail->drop_location;
		
				
		}
			if ($job_detail->way == 2) {
				$job_detail->title.=" (2 chiều)";
			}
		$job_detail->headerblock = UberHelpersUber::show_job_header($job_detail);
		
		$job_detail->pick_up_time =  date ("G:i - d/m/Y",strtotime($job_detail->pick_up_time))	;
		
		$job_detail->testDateStr = strtotime($job_detail->pick_up_time);
		$job_detail->currentDate = date("Y-m-d H:i:s");
		$job_detail->finalDate = date("Y-m-d H:i:s", strtotime("-2 hour", $job_detail->testDateStr));
					
		$job_detail->can_buy = "ok";			
		if (!$job_detail->driver_id && $job_detail->state == 1 && ($job_detail->available || $currentDate > $finalDate)) {
			$job_detail->button = "";
			$job_detail->can_buy = "";	
		}elseif ($job_detail->driver_id) {
			$job_detail->can_buy = 'Đã có tài xế mua chuyến xe này';
			$job_detail->button = "disable hidden";
		}else {
			$job_detail->can_buy = 'Chuyến xe chưa được bán';
			$job_detail->button = "disable hidden";
		} 
		
		echo json_encode($job_detail);
		break; // START DETAIL JSON JOB
		
	
	
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
			    $item->seats= UberHelpersUber::get_seat_text($item->number_seat);
			    	$item->ycarfee = format_price($item->sale_price-$item->fee); $item->pick_up_time =  date ("G:i - d/m/Y",strtotime($item->pick_up_time))	;
				$item->sale_price = format_price($item->sale_price);
				$item->fee = format_price($item->fee);			
				if ($item->title=="") {
					$item->title = $item->pick_up_location." -> ".$item->drop_location;
				}
					if ($item->way == 2) {
            				$item->title.=" (2 chiều)";
            			}
				//$item->ycarfee = $item->sale_price - $item->fee;
				$item->take_client_text ="Đối tác vui lòng bấm vào nút Đón Khách khi đã đón được khách";
				$data[]=$item;
			}
			echo json_encode($data);
		
		break; // END BOUGHT JOB
	case "new": // START NEW JOB	
			$driver_id = $requests->user_id;
			$driver_seats = UberHelpersUber::get_seats($driver_id);
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
			    
			   $item->seats= UberHelpersUber::get_seat_text($item->number_seat);
			    $check_error_time = UberHelpersUber::get_job_time($driver_id,$item->pick_up_time);	
				if ($item->title=="") {
					$item->title = $item->pick_up_location." → ".$item->drop_location;
					
				}
					if ($item->way == 2) {
            				$item->title.=" (2 chiều)";
            			}
            	switch ($item->seats) {
            	    
            	}	
				$item->ycarfee = format_price($item->sale_price-$item->fee); 
				$item->sale_price = format_price($item->sale_price);
				$item->fee = format_price($item->fee);			
			
				
				$item->testDateStr = strtotime($item->pick_up_time);
        		$item->currentDate = date("Y-m-d H:i:s");
        		$item->finalDate = date("Y-m-d H:i:s", strtotime("-2 hour", $item->testDateStr));
        		$item->currentDate = strtotime($item->currentDate);	
        		$item->finalDate = strtotime($item->finalDate);
        		$item->can_buy = "ok";	
        		if ($driver_seats < $item->number_seat) {
        		    $item->can_buy = 'Xe của bạn không đủ chỗ';
        			$item->button = "disable hidden";
        		}elseif ($item->driver_id) {
        			$item->can_buy = 'Đã có tài xế mua chuyến xe này';
        			$item->button = "disable hidden";
        		}elseif (!$item->available && $item->currentDate < $item->finalDate) {
        		    	$item->can_buy = 'Chuyến xe chưa được bán';
        			$item->button = "disable hidden";
        		}elseif ($check_error_time) {
        		    $item->can_buy = 'Bạn đã mua chuyến số '.$check_error_time.' trùng với khung giờ chuyến xe này';
        			$item->button = "disable hidden";
        		}
        		else {
        			$item->button = "";
        			$item->can_buy = "";	
        		}
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
			 $response->res_type=1;
		$response->res_message="Chúc mừng bạn đã đón khách thành công!";
		echo json_encode($response);
			
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
			//echo 'Error sending email: ';
		} else {
			//echo 'Mail sent';
		}
		$response->res_type=1;
		$response->res_message="Yêu cầu hủy chuyến đã được gửi. YCar sẽ xử lý trong thời gian sớm nhất!";
		echo json_encode($response);
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
			    $item->seats= UberHelpersUber::get_seat_text($item->number_seat);
			    	$item->ycarfee = format_price($item->sale_price-$item->fee); $item->pick_up_time =  date ("G:i - d/m/Y",strtotime($item->pick_up_time))	;
				$item->sale_price = format_price($item->sale_price);
				$item->fee = format_price($item->fee);			
				if ($item->title=="") {
					$item->title = $item->pick_up_location." -> ".$item->drop_location;
				}
					if ($item->way == 2) {
            				$item->title.=" (2 chiều)";
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
		$query->where($db->quoteName('driver_id') . ' = '. $requests->user_id);
		$query->where($db->quoteName('state') . ' = 1');
		$query->order('id DESC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$history_transactions = $db->loadObjectList();
		$history_transactions_data= array();
		 foreach ($history_transactions as $item) {
				$item->created =  date ("G:i - d/m/Y",strtotime($item->created));
							if ($item->type == 1) {
								$item->type_text = "Nạp tiền";
								$item->sign = "+";
							}			
							elseif ($item->type == 2) {
								$item->type_text = "Hủy chuyến";
								$item->value = 0-$item->value;
								if ($item->value > 0) {
									$item->sign = "-";
								}else {
									$item->sign = "+";
								}
							}			
							else {
								$item->type_text = "Mua chuyến";
								$item->value = 0-$item->value;
								if ($item->value > 0) {
									$item->sign = "+";
								}else {
									$item->sign = "-";
								}
							}
							$item->value = format_price(abs($item->value));							
					$history_transactions_data[] = $item;
				 }
				echo json_encode($history_transactions_data); 
			
		break; // END HISTORY
	case "buy_job": // START BUYING JOB
	
		$job_id = $requests->job_id;
		$driver_id = $requests->user_id;
		$job_detail = UberHelpersUber::get_job_detail($job_id);
		$driver_detail = UberHelpersUber::get_driver_detail($driver_id);
		if (!$job_detail->driver_id) {
			$user       = JFactory::getUser($driver_id);
			$balance = UberHelpersUber::get_balance($user->username);
			$fee = $job_detail->sale_price - $job_detail->fee;
			if ($balance >= $fee) {
				
				// UPDATE DRIVER FOR JOB
				$object = new stdClass();
				$object->id = $job_id;
				$object->driver_id = $driver_id;
				$object->created = date('Y-m-d h:i:s');
				$result = JFactory::getDbo()->updateObject('#__uber_job', $object, 'id');
				
				// UPDATE DRIVER'S BALANCE
				$object2 = new stdClass();
				$object2->id = $driver_id;
				$object2->balance = $balance-$fee;
				$result2 = JFactory::getDbo()->updateObject('#__uber_driver', $object2, 'id');
				
				// CREATE TRANSACTION.
					$profile = new stdClass();
					$profile->driver_id = $driver_id;
					$profile->job_id=$job_id;
					$profile->value=$fee;
					$profile->type=3;
					$profile->state=1;
					$profile->created_by=$driver_id;
					$result3 = JFactory::getDbo()->insertObject('#__uber_transaction', $profile);
					
				// CREATE ORDER
					$order = new stdClass();
					$order->driver_id = $driver_id;
					$order->job_id=$job_id;
					$order->pick_up_time=$job_detail->pick_up_time;
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
		
				
			
				$message = "Tai xe YCAR ".$driver_detail->title.", BKS: ".$driver_detail->number_plates.", DT: ".$driver_detail->phone." da nhan chuyen xe ma ".$job_id.". Tai xe se don quy khach luc ".date ("G:i - d/m/Y",strtotime($job_detail->pick_up_time)).". Hotline: 0917999941.";
				UberHelpersUber::send_sms($job_detail->customer_phone,$message);
			    	$title = "Thay đổi số dư tài khoản";
				if ($fee >= 0) {
				    $content = "-".format_price(abs($fee));
				}else {
				    $content = "+".format_price(abs($fee));
				}
			    $content.=" : Mua chuyến xe CX".$job_id;
				
				UberHelpersUber::sendMessage($title,$content,$player_ids);
				$response->res_type=1;
				$response->res_message="Chúc mừng: Chuyến xe đã là của bạn. Vui lòng xem thông tin khách hàng tại mục Chuyến xe đã mua";
				
				
			}else {
				$response->res_type=2;
			$response->res_message="Rất tiếc: Tài khoản của bạn không đủ để mua chuyến xe này. Xin vui lòng nạp thêm. Liên hệ 0917999941";
			}
			
		}else {
			$response->res_type=2;
			$response->res_message="Rất tiếc: Đã có tài xế mua chuyến xe này";
		}
		echo json_encode($response);

		break; // END BUY JOB	
	default:
		echo "default";

}// END SWTICH?>		