<?php session_start();

include 'header.php';
include 'sanitasi.php';
include 'db.php';



$no_faktur_pembayaran = $_SESSION['no_faktur_pembayaran'];

    $query0 = $db->query("SELECT p.id,p.no_faktur_pembayaran,p.keterangan,p.total,p.nama_suplier,p.tanggal,p.tanggal_edit,p.jam,p.user_buat,p.user_edit,p.dari_kas,pel.nama_pelanggan,da.nama_daftar_akun FROM pembayaran_piutang p INNER JOIN pelanggan pel ON p.nama_suplier = pel.kode_pelanggan INNER JOIN daftar_akun da ON p.dari_kas = da.kode_daftar_akun  WHERE p.no_faktur_pembayaran = '$no_faktur_pembayaran' ");
    $data0 = mysqli_fetch_array($query0);

    $query1 = $db->query("SELECT * FROM perusahaan ");
    $data1 = mysqli_fetch_array($query1);


    $query3 = $db->query("SELECT SUM(jumlah_bayar) AS j_bayar FROM detail_pembayaran_piutang WHERE no_faktur_pembayaran = '$no_faktur_pembayaran'");
    $data3 = mysqli_fetch_array($query3);
    $j_bayar = $data3['j_bayar'];


 ?>

<div class="container">
    
    <div class="row"><!--row1-->
        <div class="col-sm-2">
        <br><br>
                <img src='save_picture/<?php echo $data1['foto']; ?>' class='img-rounded' alt='Cinque Terre' width='160' height='140`'> 
        </div><!--penutup colsm2-->

        <div class="col-sm-4">
                 <h3> <b> BUKTI PEMBAYARAN PIUTANG </b></h3>
                 <hr>
                 <h4> <b> <?php echo $data1['nama_perusahaan']; ?> </b> </h4> 
                 <p> <?php echo $data1['alamat_perusahaan']; ?> </p> 
                 <p> No.Telp:<?php echo $data1['no_telp']; ?> </p> 
                 
        </div><!--penutup colsm4-->

        <div class="col-sm-4">
                          <br><br><br><br><br>

    <table>
      <tbody>
       <tr><td>No Faktur <td>:&nbsp;</td><td><?php echo $data0['no_faktur_pembayaran']; ?></td></td></tr>
       <tr><td> Cara Bayar <td>:&nbsp;</td><td><?php echo $data0['nama_daftar_akun']; ?></td></td></tr>
       <tr><td> Tanggal <td>:&nbsp;</td><td><?php echo tanggal($data0['tanggal']);?></td></td></tr>
       <tr><td> Pelanggan <td>:&nbsp;</td><td><?php echo $data0['nama_pelanggan']; ?></td></td></tr>
       
      </tbody>
    </table>            
        </div><!--penutup colsm4-->

        <div class="col-sm-2">
                <br><br><br><br><br>
                User: <?php echo $_SESSION['user_name']; ?>  <br>

        </div><!--penutup colsm4-->


        
    </div><!--penutup row1-->
</div> <!-- end of container-->


<br>
<div class="container">

<table id="tableuser" class="table table-bordered">
        <thead>

           <th> Nomor Faktur </th>
           <th> Tanggal JT </th>
           <th> Jumlah Piutang </th>
           <th> Potongan </th>
           <th> Jumlah Bayar </th>
           
            
        </thead>
        
        <tbody>
        <?php

            $query5 = $db->query("SELECT * FROM detail_pembayaran_piutang WHERE no_faktur_pembayaran = '$no_faktur_pembayaran' ");
            //menyimpan data sementara yang ada pada $perintah
            while ($data5 = mysqli_fetch_array($query5))
            {
                //menampilkan data
            echo "<tr>
                <td>". $data5['no_faktur_pembayaran'] ."</td>
                <td>". $data5['tanggal_jt'] ."</td>
                <td>". rp($data5['kredit']) ."</td>
                <td>". rp($data5['potongan']) ."</td>
                <td>". rp($data5['jumlah_bayar']) ."</td>
            <tr>";

            }

//Untuk Memutuskan Koneksi Ke Database

mysqli_close($db); 

        ?>
        </tbody>

    </table>

    
 <div class="row">

    <div class="col-sm-6">Keterangan : <?php echo $data0['keterangan']; ?></div>
    
    <div class="col-sm-6">

    <table>
      <tbody>

        <tr><td>Subtotal <td> :&nbsp;</td><td> <?php echo rp($j_bayar); ?></td></td></tr>
        <tr><td><b><i>Terbilang </b></td><td> :</i>&nbsp;</td><td><i><?php echo kekata($data0['total']); ?></i></td></tr>

      </tbody>

    </table>
    </div>
   
    

</div>
<br><br>

 <div class="row">
          <div class="col-sm-9"><hr><b>&nbsp;Hormat Kami<br><br><br><br>( ...................... )</b></div>
     <div class="col-sm-3"><hr><b>&nbsp;&nbsp;Penerima<br><br><br><br>( ................... )</b></div>
</div>
        

</div> <!--end container-->




 <script>
$(document).ready(function(){
  window.print();
});
</script>





<?php include 'footer.php'; ?>