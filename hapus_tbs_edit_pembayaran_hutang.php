<?php 
//memasukan file db.php
include 'db.php';


$id = $_POST['id'];
$no_faktur_pembelian = $_POST['no_faktur_pembelian'];




//menghapus seluruh data yang ada pada tabel tbs_pembelian berdasarkan id
$query = $db->query("DELETE FROM tbs_pembayaran_hutang WHERE id = '$id'");

//jika $query benar maka akan menuju file formpembelian.php , jika salah maka failed
if ($query == TRUE)
{
echo "sukses";
}
else
{

}
//Untuk Memutuskan Koneksi Ke Database
mysqli_close($db);   
?>