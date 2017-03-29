<?php
header("Content-Type: text/html; charset=utf8");

// define API key
$key = "iN5eRty0uRAPiKeY";

// define user ID
$uid = "+GiovanniForte";

// define max results
$maxResults = "20";

// get feed of user public activities
$request = "https://www.googleapis.com/plus/v1/people/".$uid."/activities/public?maxResults=".$maxResults."&key=".$key;
$ch = curl_init($request);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec($ch);
curl_close($ch);

// decode JSON response
echo json_activities($data);

function json_activities($json){
	$html = '';
	$aj = json_decode($json);
	$activities = $aj->{'items'};

	foreach ($activities as $activity) {
		if($activity->{'annotation'} != ""){
			$body = $activity->{'annotation'};
		}else{
			$body = $activity->{'object'}->{'content'};
	}

	$atch = '';
	if (count($activity->{'object'}->{'attachments'})) {
		foreach  ($activity->{'object'}->{'attachments'} as $a) {
			switch ($a->objectType) {
				case 'article':
					$atch = '<a target="_blank" href="'.$a->url.'">'.$a->displayName.'</a>';
					break;
				case 'photo':
					$atch = '<a target="_blank" href="'.$a->fullImage->url.'"><img src="'.$a->image->url.'" /></a>';
					break;
				case 'video':
					$atch = '<a target="_blank" href="'.$a->url.'">'.$a->displayName.'</a>';
					break;
			}
		}
	}

	$date = date('j F Y', strtotime($activity->{'published'}));
	$url = $activity->{'url'};
	$name = $activity->actor->displayName;
	$profile = $activity->actor->url;
	$title = $activity->{'title'};

	// return HTML post
	$html .= '<a href="'.$profile.'">'.$name.'</a> <a href="'.$url.'" title="'.$title.'">'.$date.'</a><p>';
	if (!$body=='') $html .= $body.'<br/>';
	$html .= ' '.$atch.'</p><hr>';
	}

return $html;
}
?>
