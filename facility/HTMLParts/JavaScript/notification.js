$(document).ready(function() {
	showNotification();
	setInterval(function(){ showNotification(); }, 20000);
});

function showNotification() {	
	if (!Notification) {
		$('body').append('<h4 style="color:red">*Browser does not support Web Notification</h4>');
		return;
	}
	if (Notification.permission !== "granted") {		
		Notification.requestPermission();
	} else {		
		$.ajax({
			url : "Functions/notification.php",
			type: "POST",
			success: function(data, textStatus, jqXHR) {
				var data = jQuery.parseJSON(data);
				if(data.result == true) {
					var sound = document.getElementById("notification"); 
					var data_notif = data.notif;
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