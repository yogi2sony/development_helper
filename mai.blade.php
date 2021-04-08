<!DOCTYPE html>
<html>
<head>
	<title>Send Email</title>
</head>
</html>
<body>
	<div style="background: #fffdc4; width: fit-content; padding: 10px;">
	<h4>Dear Member,</h4>
	<p><label style="font-style: italic;">Lead re-assigne successfully at {{ date("Y-m-d H:i:s A") }}</label><br>
		<label>Laed id: <b>{{$items->lead_sn}}</b></label><br>
		<label>Service: <b>{{$items->name}}</b></label>
	</p>

	<h4>Thanks !</h4><hr>
	<h5>LEAD-CRM-SYSTEM | ADMIN<br>
	yogeshsoni.developer@gmail.com<br>
	+91 9198316507</h5>
	</div>
</body>
