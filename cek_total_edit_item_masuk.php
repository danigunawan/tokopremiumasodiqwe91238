<?php 

// memasukan file db.php
include 'db.php';

// mengirim data no faktur menggunakan metode POST
 $no_faktur = $_POST['no_faktur'];
 

// menampilakn hasil penjumlah subtotal ALIAS total penjualan dari tabel tbs_penjualan berdasarkan data no faktur
 $query = $db->query("SELECT SUM(subtotal) AS total_item_masuk FROM tbs_item_masuk WHERE no_faktur = '$no_faktur'");
 
 // menyimpan data sementara yg ada pada $query
 $data = mysqli_fetch_array($query);

// menampilkan dsata atau isi pada total penjualan
 echo $data['total_item_masuk'] ;

        //Untuk Memutuskan Koneksi Ke Database

        mysqli_close($db); 
        
  ?>


