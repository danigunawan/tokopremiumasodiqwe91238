<?php include 'session_login.php';


include 'header.php';
include 'sanitasi.php';
include 'db.php';

 ?>

<span id="demo">
	

</span>

 <script>
var myVar = setInterval(myTimer, 1000);

function myTimer() {

	$.get('cetak_bill_pesanan.php', function(data) {
		$("#demo").html(data);
	});
    
}
</script>


<?php 
include 'footer.php';
 ?>