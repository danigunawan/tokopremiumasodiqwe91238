<?php session_start();

include 'db.php';

$session_id = session_id();
$kode_barang = $_POST['kode_barang'];

$query = $db->query("SELECT kode_barang FROM tbs_penjualan_order WHERE kode_barang = '$kode_barang' AND session_id = '$session_id'");
$jumlah = mysqli_num_rows($query);


if ($jumlah > 0){

  echo 1;
  
}
else {

}
        //Untuk Memutuskan Koneksi Ke Database

        mysqli_close($db); 

 ?>

