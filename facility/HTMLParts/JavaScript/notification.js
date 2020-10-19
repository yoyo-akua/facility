/*
Notifications are used to display a message that new medical information are available.
Notifications are defined for
	- new lab results
	- new patients referred to lab
	- stock outs in dispensary
	- ???
*/

/* 	
This function is called automatically by each php file.
It calls the showNotification function every 20 seconds (notification intervall).
*/
$(document).ready(function() {
	showNotification();
	setInterval(function(){ showNotification(); }, 20000);
});

/*
Function creates notifications if new information are available.
It performs an error handling, too. 
*/
function showNotification() {	
	
	/*
	Gives user a hint that the current browser does not support the notification technology.
	*/
	if (!Notification) {
		$('body').append('<h4 style="color:red">*Browser does not support Web Notification</h4>');
		return;
	}

	/*
	Checks, if the browser permissions, which are necessary for the notification function, are given.
	If not, the user is asked for these permissions.
	*/
	if (Notification.permission !== "granted") {		
		Notification.requestPermission();

	/*
	Calls the notification.php asynchronously (in the background).
	The notification php checks in the database if new information are available and a new notification message should be created.
	In that case, 
		- all new information within the last notification intervall (see above) are provided in the array "data" (defined in notification.php).
		- the notifications' contents (title, message and icon) are saved in array data_notif.
	*/
	} else {		
		$.ajax({
			url : "Functions/notification.php",
			type: "POST",
			success: function(data, textStatus, jqXHR) {
				var data = jQuery.parseJSON(data);
				if(data.result == true) {
					var sound = document.getElementById("notification"); 
					var data_notif = data.notif;

					/*
					For each notification's content a new notification is created.
					It contains an icon, a title and a message.
					Variable theurl contains ???
					"requireInteraction: true" requires the user to accept the new notification before it is closed. 
					*/
					for (var i = data_notif.length - 1; i >= 0; i--) {
						sound.play();
						var theurl = data_notif[i]['url'];
						var notifikasi = new Notification(data_notif[i]['title'], {
							icon: data_notif[i]['icon'],
							body: data_notif[i]['msg'],
							requireInteraction: true,
						});
						notifikasi.onclick = function () {
							window.open(theurl); 
							notifikasi.close();     
						};
					};
				} 
			},
			error: function(jqXHR, textStatus, errorThrown)	{}
		}); 
	}
};