<?php
if (!defined('_JEXEC'))
{
    // Initialize Joomla framework
    define('_JEXEC', 1);
}

// Load system defines
if (file_exists(dirname(__FILE__) . '/defines.php'))
{
    require_once dirname(__FILE__) . '/defines.php';
}
if (!defined('JPATH_BASE'))
{
    define('JPATH_BASE', dirname(__FILE__));
}
if (!defined('_JDEFINES'))
{
    require_once JPATH_BASE . '/includes/defines.php';
}

// Get the framework.
require_once JPATH_BASE . '/includes/framework.php';

$app = JFactory::getApplication('site');
$app->initialise();
// Create and populate an object.
$jinput = JFactory::getApplication()->input;
$booking = new stdClass();

$booking->is_airport = JRequest::getVar('is_airport');
$location = JRequest::getVar('location');
if ($booking->is_airport) {
	if ($booking->is_airport == 1) {
		$booking->start_point = "Sân bay Nội bài";
		$booking->end_point= $location;
	}else {
		$booking->start_point = $location;
		$booking->end_point= "Sân bay Nội bài";
	}
	
}else {
	$booking->start_point = JRequest::getVar('start_point');
	$booking->end_point=JRequest::getVar('end_point');
}

$booking->distance_km=JRequest::getVar('distance_km');
$booking->name=JRequest::getVar('name');
$booking->phone=JRequest::getVar('phone');
$booking->date=JRequest::getVar('date');
$booking->time=JRequest::getVar('time');
$booking->car_type=JRequest::getVar('car_type');
$booking->comment=JRequest::getVar('comment');

// Insert the object into the user profile table.
$result = JFactory::getDbo()->insertObject('#__uber_booking', $booking);
switch (JRequest::getVar('car_type')) {
    case 1:
        $car_type = "5 chỗ 1 chiều";
        break;
    case 2:
        $car_type = "5 chỗ 2 chiều";
        break;
    case 3:
        $car_type = "7 chỗ 1 chiều";
        break;
	 case 4:
        $car_type = "7 chỗ 2 chiều";
        break;	
    default:
        $car_type = "5 chỗ 1 chiều";
}
$mailer = JFactory::getMailer();

$config = JFactory::getConfig();
$sender = array( 
    $config->get( 'mailfrom' ),
    $config->get( 'fromname' ) 
);

$mailer->setSender($sender);
$recipient = array( 'anjakahuy@gmail.com',  'huynv@ltdvietnam.com' );

$mailer->addRecipient($recipient);
$body   = "<h2>Thông tin khách hàng</h2>
<table>
	<tr>
		<td>Tên khách hàng</td>
		<td>".JRequest::getVar('name')."</td>
	</tr>
	<tr>
		<td>Số điện thoại</td>
		<td>".JRequest::getVar('phone')."</td>
	</tr>
	<tr>
		<td>Điểm đón</td>
		<td>".$booking->start_point."</td>
	</tr>
	<tr>
		<td>Điểm trả khách</td>
		<td>".$booking->end_point."</td>
	</tr>
	<tr>
		<td>Ngày đón</td>
		<td>".JRequest::getVar('date')."</td>
	</tr>
	<tr>
		<td>Giờ đón</td>
		<td>".JRequest::getVar('time')."</td>
	</tr>
	<tr>
		<td>Loại xe</td>
		<td>".$car_type."</td>
	</tr>
	<tr>
		<td>Ghi chú</td>
		<td>".JRequest::getVar('comment')."</td>
	</tr>
</table>
";
$mailer->setSubject('Khách hàng đặt xe mới');
$mailer->isHtml(true);

$mailer->setBody($body);
$send = $mailer->Send();
if ( $send !== true ) {
    echo 'Error sending email: ';
} else {
    echo 'Mail sent';
}