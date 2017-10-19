<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Uber
 * @author     Eddy Nguyen <contact@eddynguye.com>
 * @copyright  2017 Eddy Nguyen
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JLoader::register('UberHelper', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_uber' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'uber.php');

/**
 * Class UberFrontendHelper
 *
 * @since  1.6
 */
class UberHelpersUber
{
	/**
	 * Get an instance of the named model
	 *
	 * @param   string  $name  Model name
	 *
	 * @return null|object
	 */
	public static function check () {
		echo "hello";
	} 
    public static function get_seat_text($id) {
        switch ($id) {
			        case 1:
			            $seats = "5";
			            break;
			          case 2:
			             $seats = "7";
			            break;
			             case 3:
			             $seats = "8";
			            break;
			             case 4:
			             $seats = "9";
			            break;
			             case 5:
			             $seats = "12";
			            break;
			             case 6:
			            $seats = "16";
			            break;
			             case 7:
			             $seats= "24";
			            break;
			             case 8:
			             $seats = "29";
			            break;
			            case 9:
			             $seats = "35";
			            break;
			            case 10:
			             $seats = "45";
			            break;
			            default:
			                 $seats = "5";
			    }
			    return  $seats;
    }
    public static function send_sms($YourPhone,$Content) {
        	$APIKey="2A00924E0B265978F73EB9B28088DF";
		$SecretKey="C60751C63C7740DCD5F0886E3DCA18";
	
		
		
		$SendContent=urlencode($Content);
		$data="http://rest.esms.vn/MainService.svc/json/SendMultipleMessage_V4_get?Phone=$YourPhone&ApiKey=$APIKey&SecretKey=$SecretKey&Content=$SendContent&SmsType=2&Brandname=YCAR.VN";
		
		$curl = curl_init($data); 
		curl_setopt($curl, CURLOPT_FAILONERROR, true); 
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		$result = curl_exec($curl); 
			
	
		
    }
	public static function sendMessage($title, $content,$player_id){
		$headings = array(
			"en" => $title
			);
		$content = array(
			"en" => $content
			);	
		if ($player_id) {
		   
		    $fields = array(
			'app_id' => "9c611d5c-e51f-4f51-bb9f-b8d679153272",
			 'include_player_ids' => $player_id,
			 'data' => array("foo" => "bar"),
			'contents' => $content,
			'headings' => $headings
		    );
		}else {
		    $fields = array(
			'app_id' => "9c611d5c-e51f-4f51-bb9f-b8d679153272",
			'included_segments' => array('All'),
             'data' => array("foo" => "bar"),
			'contents' => $content,
			'headings' => $headings
		    );
		}
		
		
		$fields = json_encode($fields);
   // print("\nJSON sent:\n");
   // print($fields);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
												   'Authorization: Basic OTg5YjEzZDEtYTcxNC00MDA0LWFhMzYtNDhlMDU0NjhhMzIx'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		
		return $response;
	}
    
	public static function get_player_id ($driver_id) {
	     $db = JFactory::getDbo();
		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('player_id'));
		$query->from($db->quoteName('#__uber_playerid'));
		$query->where($db->quoteName('driver_id') . ' = '. $db->quote($driver_id));
	     $query->order('id DESC');
		

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadColumn();
		return $results;
	}
    public static function check_player_id($driver_id, $player_id) {
        $db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__uber_playerid'));
		$query->where($db->quoteName('driver_id') . ' = '. $db->quote($driver_id));
		$query->where($db->quoteName('player_id') . ' = '. $db->quote($player_id));
		

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadResult();
		return $results;
    }
	public static function get_seats ($id) {
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('number_seat'));
		$query->from($db->quoteName('#__uber_driver'));
		$query->where($db->quoteName('id') . ' = '. $db->quote($id));
		

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadResult();
		return $results;
	}
	public static function get_balance_id ($username) {
	$db = JFactory::getDbo();

	// Create a new query object.
	$query = $db->getQuery(true);

	// Select all records from the user profile table where key begins with "custom.".
	// Order it by the ordering field.
	$query->select($db->quoteName('balance'));
	$query->from($db->quoteName('#__uber_driver'));
	$query->where($db->quoteName('id') . ' = '. $db->quote($username));
	

	// Reset the query using our newly populated query object.
	$db->setQuery($query);

	// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$results = $db->loadResult();
	$results = number_format($results)."đ";
	return $results;
}
	public static function get_balance ($username) {
	$db = JFactory::getDbo();

	// Create a new query object.
	$query = $db->getQuery(true);

	// Select all records from the user profile table where key begins with "custom.".
	// Order it by the ordering field.
	$query->select($db->quoteName('balance'));
	$query->from($db->quoteName('#__uber_driver'));
	$query->where($db->quoteName('phone') . ' = '. $db->quote($username));
	

	// Reset the query using our newly populated query object.
	$db->setQuery($query);

	// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$results = $db->loadResult();
	return $results;
}
	public static function show_customer_info($job_detail, $driver_id) {
		$html="";
		if ($job_detail->driver_id == $driver_id) {
			$html.="<b>Khách hàng: </b>:".$job_detail->customer_name."<br/>";
			$html.="<b>Điện thoại: </b>:<a href='tel:".$job_detail->customer_phone."'>".$job_detail->customer_phone."</a><br/>";
		}
		return ($html);
	}
	public static function get_job_time ($driver_id, $job_time) {
	    // Get a db connection.
        $db = JFactory::getDbo();
        
        // Create a new query object.
        $query = $db->getQuery(true);
        
        // Select all records from the user profile table where key begins with "custom.".
        // Order it by the ordering field.
       $query->select($db->quoteName(array('id', 'pick_up_time')));
        $query->from($db->quoteName('#__uber_job'));
        $query->where($db->quoteName('driver_id') . ' = '. $driver_id);
        //$query->where($db->quoteName('pick_up_time') . ' >  now()');
        $query->order('pick_up_time DESC');
        
        // Reset the query using our newly populated query object.
        $db->setQuery($query);
        
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        $results = $db->loadObjectlist();
          $job_time = strtotime($job_time);
         $error =0; 
        foreach ($results as $job) {
           
            $time = strtotime($job->pick_up_time);
            $start = date("Y-m-d H:i:s", strtotime("-1 hour", $time));
            $end = date("Y-m-d H:i:s", strtotime("+1 hour", $time));
           
          
             $start = strtotime($start);
              $end = strtotime($end);
              
            if ($job_time > $start && $job_time < $end) {
                $error = $job->id;
                break;
            }
            
        }
        return $error;


	}
	public static function get_last_job($driver_id) {
		
			$db = JFactory::getDbo();

			// Create a new query object.
			$query = $db->getQuery(true);

			// Select all records from the user profile table where key begins with "custom.".
			// Order it by the ordering field.
			$query->select('*');
			$query->from($db->quoteName('#__uber_job'));
			$query->where($db->quoteName('driver_id') . ' = '. $driver_id);
			$query->where($db->quoteName('state') . ' = 1');
			$query->order('pick_up_time DESC');

			// Reset the query using our newly populated query object.
			$db->setQuery($query,0,1);

			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$results = $db->loadObject();
			return ($results);
	}
	public static function show_job_header ($job_detail) {
		
		$html = "<b>CX".$job_detail->id." </b>";
		$html .=' <span style="color:blue;font-weight: bold;">';
		$job_detail->number_seat = JText::_('COM_UBER_DRIVERS_NUMBER_SEAT_OPTION_' . strtoupper($job_detail->number_seat));	
		$html .= $job_detail->number_seat.'</span> | ';
		$html .= date ("G:i - d/m/Y",strtotime($job_detail->pick_up_time))	;
		$testDateStr = strtotime($job_detail->pick_up_time);
			$currentDate = date("Y-m-d H:i:s");
			$finalDate = date("Y-m-d H:i:s", strtotime("-2 hour", $testDateStr));
			if ($job_detail->available || $currentDate > $finalDate) {
				$html .=' | <span style="color:#0101b3; font-weight:bold">'.number_format($job_detail->fee).' vnđ </span><br/>';
		
			}
		//$html .= " Chuyến xe số ".$job_detail->id.": ";
		if ($job_detail->title) 
			$html .= $job_detail->title; 
		else	
			$html .=$job_detail->pick_up_location ."&rarr;".$job_detail->drop_location." ";
		
		
		
		
		if ($job_detail->way == 2) $html .= " <b>(2 chiều)</b> ";
		
		
		
		
		
		$html .="<br/>";
		return $html;
			
		
	}
	public static function check_account($data) {
		
		if(UberHelpersUber::check_data('phone',$data->phone)) {
			return ("Số điện thoại đã tồn tại!");
		}
		elseif(UberHelpersUber::check_data('id_card',$data->id_card)) {
			return ("Chứng minh nhân dân đã tồn tại!");
		}
		elseif(UberHelpersUber::check_data('number_plates',$data->number_plates)) {
			return ("Biển số xe đã tồn tại");
		}
		elseif(UberHelpersUber::check_data('license',$data->license)) {
			return ("Số bằng lái đã tồn tại");
		}else {
			return 1;
		}
		
	}
	public static function check_data ($field, $value) {
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__uber_driver'));
		$query->where($db->quoteName($field) . ' = '. $db->quote($value));
		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadResult();
		return ($results);
	}
	public static function show_job_add_location ($job_detail) {
		$html = "";
		if ($job_detail->add_location) {
				$html .="Đón/trả khách thêm tại:<br/>";
				$html .= "-".$job_detail->address_1."<br/>";
				if ($job_detail->address_2)
				$html .= "-".$job_detail->address_2."<br/>";
			
			}
		return $html;	
	}
	public static function show_job_fair($job_detail) {
		$html = "";
		
		$html .= 'Phải thu của khách: <span style="color:#e60000; font-weight:bold">'.number_format($job_detail->sale_price).' vnđ </span><br/>';
		$html .= '	Lái xe được hưởng: <span style="color:#0101b3; font-weight:bold">'.number_format($job_detail->fee).' vnđ </span><br/>';
		$fee =$job_detail->sale_price-$job_detail->fee;
		$html .= "	Phí của YCar: <span style='color:red'>".number_format($fee)."vnđ </span>";
				if ($fee < 0) {
					$html .="<i><small>(Ycar sẽ bù khoản tiền này vào tài khoản của lái xe)</small></i>";
				}else {
					$html .="<i>(Khoản tiền này sẽ được hệ thống tự động trừ vào tài khoản của lái xe)</i>";
				}
		$html .= "<br/>";		
		return $html;	
	}
	public static function show_job_customer($job_detail) {
		$html = "";
		$html .="<b>Tên khách hàng: </b>". $job_detail->customer_name."<br/>";
		$html .='<b>Số điện thoại: </b><a href="tel:'.$job_detail->customer_phone .'">'.$job_detail->customer_phone .'</a><br/>';
		return $html;
		
	}
	public static function show_job_comment($job_detail) {
		$html = "<div class='comment-block'>Ghi chú: <br/>";
		if ($job_detail->flight_number) {
			$html .="<b>Chuyến bay số: </b>".$job_detail->flight_number."<br/>";
		}
		if ($job_detail->comment)
			$html .=$job_detail->comment."<br/>";
		if ($job_detail->invoice_to_driver && $job_detail->invoice) {
			$html .="<b>Xuất hóa đơn cho khách hàng</b><br/>";
			$html .="Tên công ty: ".$job_detail->company."<br/>";
			$html .="Mã số thuế: ".$job_detail->mst."<br/>";
			$html .="Địa chỉ: ".$job_detail->address."<br/>";
			$html .="Địa chỉ nhận hóa đơn: ".$job_detail->address_inoivce."<br/>";
		}
		$html.="</div>";	
		return $html;
		
	}
	public static function get_bought_job_list () {
			$user = JFactory::getUser();
			$db = JFactory::getDbo();

			// Create a new query object.
			$query = $db->getQuery(true);

			// Select all records from the user profile table where key begins with "custom.".
			// Order it by the ordering field.
			$query->select('*');
			$query->from($db->quoteName('#__uber_job'));
			$query->where($db->quoteName('driver_id') . ' = '. $user->id);
			//$query->where($db->quoteName('status') . ' = 0');
			$query->where($db->quoteName('state') . ' = 1');
			//$query->where($db->quoteName('pick_up_time') . ' >= ( CURDATE() - INTERVAL 3 DAY )');
			$query->order('pick_up_time DESC');

			// Reset the query using our newly populated query object.
			$db->setQuery($query);

			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$results = $db->loadObjectList();
			foreach ($results as $item) {				
				$item->number_seat = JText::_('COM_UBER_DRIVERS_NUMBER_SEAT_OPTION_' . strtoupper($item->number_seat));			
			}

			
	}
	public static function get_job_detail($id) {
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select('*');
		$query->from($db->quoteName('#__uber_job'));
		$query->where($db->quoteName('id') . ' = '. $id);
		//$query->where($db->quoteName('state') . ' = 1');
		
		//$query->where($db->quoteName('driver_id') . ' = 0');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObject();
		
			return $results;
	}
		public static function get_driver_detail($id) {
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select('*');
		$query->from($db->quoteName('#__uber_driver'));
		$query->where($db->quoteName('id') . ' = '. $id);
		//$query->where($db->quoteName('state') . ' = 1');
		
		//$query->where($db->quoteName('driver_id') . ' = 0');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObject();
		
			return $results;
	}
	public static function is_bought ($userid, $itemid) {
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('id', 'status', 'driver_id')));
		$query->from($db->quoteName('#__uber_job'));
		$query->where($db->quoteName('id') . ' = '. $itemid);
		$query->where($db->quoteName('driver_id') . ' = '. $userid);
		$query->where($db->quoteName('driver_id') . ' != 0');
		

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObject();
		if ($results)
			return 1;
		else
			return 0;
	}
	public static function getModel($name)
	{
		$model = null;

		// If the file exists, let's
		if (file_exists(JPATH_SITE . '/components/com_uber/models/' . strtolower($name) . '.php'))
		{
			require_once JPATH_SITE . '/components/com_uber/models/' . strtolower($name) . '.php';
			$model = JModelLegacy::getInstance($name, 'UberModel');
		}

		return $model;
	}

	/**
	 * Gets the files attached to an item
	 *
	 * @param   int     $pk     The item's id
	 *
	 * @param   string  $table  The table's name
	 *
	 * @param   string  $field  The field's name
	 *
	 * @return  array  The files
	 */
	public static function getFiles($pk, $table, $field)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select($field)
			->from($table)
			->where('id = ' . (int) $pk);

		$db->setQuery($query);

		return explode(',', $db->loadResult());
	}

    /**
     * Gets the edit permission for an user
     *
     * @param   mixed  $item  The item
     *
     * @return  bool
     */
    public static function canUserEdit($item)
    {
        $permission = false;
        $user       = JFactory::getUser();

        if ($user->authorise('core.edit', 'com_uber'))
        {
            $permission = true;
        }
        else
        {
            if (isset($item->created_by))
            {
                if ($user->authorise('core.edit.own', 'com_uber') && $item->created_by == $user->id)
                {
                    $permission = true;
                }
            }
            else
            {
                $permission = true;
            }
        }

        return $permission;
    }
}
