<?php

include 'db.php';

$no_faktur = $_POST['no_faktur'];
$kode_barang = $_POST['kode_barang'];

$query = $db->query("SELECT kode_barang, no_faktur FROM tbs_pembelian WHERE kode_barang = '$kode_barang' AND no_faktur = '$no_faktur' AND no_faktur_order IS NULL");
$jumlah = mysqli_num_rows($query);


if ($jumlah > 0){

  echo "1";
}
else {

}
        //Untuk Memutuskan Koneksi Ke Database

        mysqli_close($db); 

 ?>

