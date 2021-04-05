<a href="https://www.codegrepper.com/code-examples/php/calculate+total+time+from+start+and+end+datetime+in+php">calculate total time</a>
<a href="https://www.codegrepper.com/code-examples/php/how+to+get+video+duration+in+php">video duration</a>
<?php

$date1 = new DateTime('2006-04-12T12:30:00');
$date2 = new DateTime('2006-04-14T11:30:00');

$diff = $date2->diff($date1);

$hours = $diff->h;
$hours = $hours + ($diff->days*24);

echo $hours;

/*
$currentTime = (new DateTime('01:00'))->modify('+1 day');
$startTime = new DateTime('22:00');
$endTime = (new DateTime('07:00'))->modify('+1 day');

if ($currentTime >= $startTime && $currentTime <= $endTime) {
    // Do something
}
*/

?>
