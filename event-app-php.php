<?php require_once __DIR__ . '/vendor/autoload.php'; ?> 

<?php
// Functions

// Takes a substring of a date (in yyyy-mm-dd format) and turns it into a int
function parse_year($date){
  return intval(substr($date, 0, 4));
}
function parse_month($date){
  return intval(substr($date, 5, 2));
}
function parse_day($date){
  return intval(substr($date, 8, 2));
}

// Checks if event is upcoming by comparing the date to today's date
function is_upcoming($eventDate){
  if (parse_year(date('Y-m-d')) < parse_year($eventDate)){
    return true;
  } else if (parse_year(date('Y-m-d')) > parse_year($eventDate)) {
    return false;
  }

  if (parse_month(date('Y-m-d')) < parse_month($eventDate)){
    return true;
  } else if (parse_month(date('Y-m-d')) > parse_month($eventDate)) {
    return false;
  }

  if (parse_day(date('Y-m-d')) <= parse_day($eventDate)){
    return true;
  } else {
    return false;
  }
}



function refine_date($eventDate){
  $months = array(' ','January','February','March','April','May','June','July','August','September','October','November','December');
  $date = $months[parse_month($eventDate)] . ' ' . parse_day($eventDate) . ', ' . parse_year($eventDate);
  return $date;
}

?>

<?php

// ID of Facebook page to gather data from
$page_id = '604497632948882';

// App ID, Client Secret, Version of FB API, and Access token, to access the app
$app_id = '189331864907753';
$app_secret = '26535f5d34a00a18958cef2a635e6bd6';
$default_graph_version = 'v2.8';
$access_token = '189331864907753|UJ0Qj5fLLBn-Ysq-whXCWkGWtW0';

// Tells SDK what info to use
$fb = new Facebook\Facebook([
  'app_id' => $app_id,
  'app_secret' => $app_secret,
  'default_graph_version' => $default_graph_version,
]);

// Sets access token
$fb->setDefaultAccessToken($access_token);

// Fields to gather data from
$fields = 'name,about,events';

// Get data from API
try {
  $response = $fb->get('/' . $page_id . '?fields=' . $fields);
  $userNode = $response->getGraphUser();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

// Show event data on page
$has_upcoming_events = false;

foreach($userNode['events'] as $event){
  $date_num = substr(serialize($event['start_time']), 35, 10);
  if (is_upcoming($date_num) === true){
    $has_upcoming_events = true;
    echo '<a href="http://www.facebook.com/events/' . $event['id'] . '" target="_blank">';
    echo '<h3>' . $event['name'] . '</h3>';
    echo '</a>';
    if (isset($event['place']) === true){
      echo '<p><span>' . $event['place']['name'] . ' </span>';
    }
    if (isset($date_num) === true){
      echo '<span>| ' . refine_date($date_num) . '</span></p>';
    }
  }
}
 
if ($has_upcoming_events === false) {
  echo 'No upcoming events to show. Check back soon!';
}

?>
