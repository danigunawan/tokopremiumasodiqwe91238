<?php
include 'db.php';
include 'sanitasi.php';
include 'persediaan.function.php';
session_start();

    require_once 'cache_folder/cache.class.php';

    // setup 'default' cache
    $c = new Cache();

     // store a string
    $array_potongan = array();
    $kode_barang = stringdoang($_POST['kode_barang']);
    $sales = stringdoang($_POST['sales']);
    $level_harga = stringdoang($_POST['level_harga']);
    $no_faktur = stringdoang($_POST['no_faktur']);
    $i = 0;

        // QUERY CEK BARCODE DI SATUAN KONVERSI
                                    
    $query_satuan_konversi = $db->query("SELECT COUNT(*) AS jumlah_data,kode_barcode,kode_produk,konversi , id_satuan, harga_jual_konversi FROM satuan_konversi WHERE kode_barcode = '$kode_barang'  AND kode_barcode != '' ");
    $data_satuan_konversi = mysqli_fetch_array($query_satuan_konversi);     

        // QUERY CEK BARCODE DI SATUAN KONVERSI
         // IF APABILA ADA SATUAN KONVERSINYA 
      if ($data_satuan_konversi['jumlah_data'] != 0) { 
          
          $kode_barang = $data_satuan_konversi['kode_produk'];
      }
      else{

                            // QUERY CEK BARCODE DI MASTER DATA PRODUK
                                              
                  $querybarang = $db->query("SELECT COUNT(*) AS jumlah_data,kode_barcode,kode_barang FROM barang WHERE kode_barcode = '$kode_barang' AND kode_barcode != '' ");
                  $databarang = mysqli_fetch_array($querybarang);     

              // QUERY CEK BARCODE DI MASTER DATA PRODUK

                      // IF CEK BARCODE DI BARCODE
                    if ($databarang['jumlah_data'] > 0) {
                                   
                     $kode_barang =  $databarang['kode_barang'];
                    }else{

                    $kode_barang =  $kode_barcode;
                    }
      }
      // IF APABILA ADA SATUAN KONVERSINYA

    $tipe = $db->query("SELECT berkaitan_dgn_stok FROM barang WHERE kode_barang = '$kode_barang'");
    $data_tipe = mysqli_fetch_array($tipe);
    $ber_stok = $data_tipe['berkaitan_dgn_stok'];


// UNTUK MENGETAHUI JUMLAAH TBS SEBENARNYA
    $jumlah_tbs = 0;
    $query_stok_tbs = $db->query("SELECT jumlah_barang,satuan, subtotal, potongan FROM tbs_penjualan_order WHERE kode_barang = '$kode_barang' AND no_faktur_order = '$no_faktur'");
    while($data_stok_tbs = mysqli_fetch_array($query_stok_tbs)){


      $query_cek_satuan_konversi = $db->query("SELECT konversi FROM satuan_konversi WHERE kode_produk = '$kode_barang' AND id_satuan = '$data_stok_tbs[satuan]' ");
      $data_cek_satuan_konversi = mysqli_fetch_array($query_cek_satuan_konversi);

        $konversi = $data_cek_satuan_konversi['konversi'];
        if ($konversi == '') {
          $konversi = 1;
        }
        $jumlah_tbs_penjualan = $data_stok_tbs['jumlah_barang'] * $konversi;

        $jumlah_tbs = $jumlah_tbs_penjualan + $jumlah_tbs;
    }     
  //  UNTUK MENGETAHUI JUMLAAH TBS SEBENARNYA
   
   // Ambil total jumlah barang di detail penjualan
    $query_detail = $db->query("SELECT SUM(jumlah_barang) AS jumlah_barang FROM detail_penjualan_order WHERE no_faktur_order = '$no_faktur' AND kode_barang = '$kode_barang'");
    $data_detail = mysqli_fetch_array($query_detail);
   
   // Ambil total jumlah barang di detail penjualan
   
   //  stok
    $ambil_sisa = (cekStokHpp($kode_barang)  + $data_detail['jumlah_barang']) - $jumlah_tbs;


    $tahun_sekarang = date('Y');
    $bulan_sekarang = date('m');
    $tanggal_sekarang = date('Y-m-d');
    $jam_sekarang = date('H:i:sa');


    // generate a new cache file with the name 'newcache'
    $c->setCache('produk');


if($c->isCached($kode_barang)) {
 // get cached data by its key
    $result = $c->retrieve($kode_barang);
    // grab array entry
    $nama_barang = stringdoang($result['nama_barang']);
    $harga_jual1 = angkadoang($result['harga_jual']);
    $harga_jual2 = angkadoang($result['harga_jual2']);
    $harga_jual3 = angkadoang($result['harga_jual3']);
    $harga_jual4 = angkadoang($result['harga_jual4']);
    $harga_jual5 = angkadoang($result['harga_jual5']);
    $harga_jual6 = angkadoang($result['harga_jual6']);
    $harga_jual7 = angkadoang($result['harga_jual7']);
    $jumlah_barang = angkadoang(1);
       
            // IF CEK BARCODE DI SATUAN KONVERSI

            if ($data_satuan_konversi['jumlah_data'] > 0) {

                $satuan = $data_satuan_konversi['id_satuan']; // satuan dari satuan konversi
                }else{

                  $satuan = stringdoang($result['satuan']); // satuan dasar
                }

            // IF CEK BARCODE DI SATUAN KONVERSI
}
else {
$query = $db->query("SELECT * FROM barang WHERE kode_barang = '$kode_barang'");
while ($data = $query->fetch_array()) {
 # code...
    // store an array
    $c->store($data['kode_barang'], array(
      'nama_barang' => $data['nama_barang'],
      'harga_beli' => $data['harga_beli'],
      'harga_jual' => $data['harga_jual'],
      'harga_jual2' => $data['harga_jual2'],
      'harga_jual3' => $data['harga_jual3'],
      'harga_jual4' => $data['harga_jual4'],
      'harga_jual5' => $data['harga_jual5'],
      'harga_jual6' => $data['harga_jual6'],
      'harga_jual7' => $data['harga_jual7'],
      'satuan' => $data['satuan'],
      'kategori' => $data['kategori'],
      'gudang' => $data['gudang'],
      'status' => $data['status'],
      'suplier' => $data['suplier'],
      'stok_awal' => $data['stok_awal'],
      'stok_opname' => $data['stok_opname'],
      'foto' => $data['foto'],
      'limit_stok' => $data['limit_stok'],
      'over_stok' => $data['over_stok'],


    ));

}
    $result = $c->retrieve($kode_barang);
        // grab array entry
    $nama_barang = stringdoang($result['nama_barang']);
    $harga_jual1 = angkadoang($result['harga_jual']);
    $harga_jual2 = angkadoang($result['harga_jual2']);
    $harga_jual3 = angkadoang($result['harga_jual3']);
    $harga_jual4 = angkadoang($result['harga_jual4']);
    $harga_jual5 = angkadoang($result['harga_jual5']);
    $harga_jual6 = angkadoang($result['harga_jual6']);
    $harga_jual7 = angkadoang($result['harga_jual7']);
    $jumlah_barang = angkadoang(1);
    
      
            // IF CEK BARCODE DI SATUAN KONVERSI

            if ($data_satuan_konversi['jumlah_data'] > 0) {

                $satuan = $data_satuan_konversi['id_satuan']; // satuan dari satuan konversi
                }else{

                  $satuan = stringdoang($result['satuan']); // satuan dasar
                }

            // IF CEK BARCODE DI SATUAN KONVERSI
}

if ($level_harga == 'harga_1')
{
  $harga_tbs = $harga_jual1;
}
else if ($level_harga == 'harga_2')
{
  $harga_tbs = $harga_jual2;
}
else if ($level_harga == 'harga_3')
{
  $harga_tbs = $harga_jual3;
}
else if ($level_harga == 'harga_4')
{
  $harga_tbs = $harga_jual4;
}
else if ($level_harga == 'harga_5')
{
  $harga_tbs = $harga_jual5;
}
else if ($level_harga == 'harga_6')
{
  $harga_tbs = $harga_jual6;
}
else if ($level_harga == 'harga_7')
{
  $harga_tbs = $harga_jual7;
}



              $query9 = $db->query("SELECT jumlah_prosentase,jumlah_uang FROM fee_produk WHERE nama_petugas = '$sales' AND kode_produk = '$kode_barang'");
              $cek9 = mysqli_fetch_array($query9);
              $prosentase = $cek9['jumlah_prosentase'];
              $nominal = $cek9['jumlah_uang'];
                                       ##
            // IF CEK BARCODE DI SATUAN KONVERSI

            if ($data_satuan_konversi['jumlah_data'] > 0) {

                  $jumlah_produk = $data_satuan_konversi['konversi'];
                  $stok_barang = $ambil_sisa - $data_satuan_konversi['konversi'];

                  // cari subtotal , langsung dikalikan dengan nilai konversinya
                  
                  $harga_fee = $harga_tbs;

                  if ($data_satuan_konversi['harga_jual_konversi'] == 0) {   

                    $harga_konversi = $harga_tbs * $data_satuan_konversi['konversi'];
                    $a = $harga_tbs * $data_satuan_konversi['konversi'];
                  }else{

                    $harga_konversi = $data_satuan_konversi['harga_jual_konversi'];
                    $a = $data_satuan_konversi['harga_jual_konversi'];
                  }
                  // cari subtotal
                  $jumlah_fee = $data_satuan_konversi['konversi'];

                }else{

                  $jumlah_produk = $jumlah_barang;
                  $stok_barang = $ambil_sisa - $jumlah_barang;
                  // cari subtotal
                  $a = $harga_tbs * $jumlah_barang;
                  // cari subtotal
                  
                  $jumlah_fee = $jumlah_barang;                  
                  $harga_fee = $harga_tbs;
                  $harga_konversi = 0;
                }

            $query_potongan = $db->query("SELECT SUM(potongan) AS potongan FROM tbs_penjualan_order WHERE kode_barang = '$kode_barang' AND no_faktur_order = '$no_faktur' AND satuan != '$satuan' ");
            $data_potongan = mysqli_fetch_array($query_potongan);
            $potongan_tbs_order = $data_potongan['potongan'];
          //  UNTUK MENGETAHUI JUMLAAH TBS SEBENARNYA

            // qUERY UNTUK CEK APAKAH SUDAH ADA APA BELUM DI TBS PENJUALAN    
            $query_tbs_penjualan = $db->query("SELECT COUNT(kode_barang) AS jumlah_data,SUM(subtotal) AS subtotal, SUM(potongan) AS potongan FROM tbs_penjualan_order WHERE kode_barang = '$kode_barang' AND no_faktur_order = '$no_faktur'  AND satuan = '$satuan'");
            $data_tbs_penjualan = mysqli_fetch_array($query_tbs_penjualan);
            // qUERY UNTUK CEK APAKAH SUDAH ADA APA BELUM DI TBS PENJUALAN  

            $subtotal_tbs_order = $data_tbs_penjualan['subtotal'] + $data_tbs_penjualan['potongan'];



                       // untuk cek potongan produk
        
                           // ambil setting_diskon_jumlah yang selisih antara jumlah produk dan syarat jumlah lebih dari nol
                $query = $db->query("SELECT potongan,  syarat_jumlah FROM setting_diskon_jumlah WHERE kode_barang = '$kode_barang' ");
                while ($data = mysqli_fetch_array($query)) {// while
                      
                      $i = $i + 1;

                      $hitung = ($jumlah_tbs + $jumlah_produk) - $data['syarat_jumlah'];

                      if ($hitung >= 0) {
                                // masukan data ke dalam array
                        $array = array("syarat_jumlah" => $data['syarat_jumlah'],"potongan" => $data['potongan']);

                        array_push($array_potongan, $array);
                      }else{
                                  // masukan data ke dalam array
                        $array = array("syarat_jumlah" => 0,"potongan" => 0);

                        array_push($array_potongan, $array);
                      }   
                  
                }// while                  

                if ($i == 0) {
                    // ambil data yang paling besar
                    $potongan_tampil = 0;

                }else{
                    // ambil data yang paling besar
                    $max = max($array_potongan);  
                    // ubah data dalam ventuk json encode
                    $json_encode = json_encode($max);
                    // ingin membaca format JSON di PHP maka JSON harus di convert ke Array Object dengan menggunakan json_decode
                    $data = json_decode($json_encode);   
                    // akan tampil potongan                
                    $potongan_tampil = $data->potongan;
                }

                if ($potongan_tbs_order == $potongan_tampil) {
                    $potongan_tampil = 0;
                    $subtotal_order = ($subtotal_tbs_order + $a) - $potongan_tampil; 

                }else{

                                              # code...
                          $query1 = $db->prepare("UPDATE tbs_penjualan_order SET subtotal = subtotal + potongan, potongan = 0 WHERE kode_barang = ? AND no_faktur_order = ? ");

                          $query1->bind_param("ss",
                          $kode_barang, $no_faktur);

                          $query1->execute();

                    $subtotal_order = ($subtotal_tbs_order + $a) - $potongan_tampil; 
                }



        // untuk cek potongan produk

if ($ber_stok == 'Barang' OR $ber_stok == 'barang') {
    
    if ($stok_barang < 0 ) {
      echo 1;
    }

    else{

          if ($prosentase != 0){// if ($prosentase != 0){// 

                    if ($jumlah_tbs != 0) {// apablla barang ini sudah ada di tbs
                        
                           $jumlahFeemasuk = $jumlah_fee + $jumlah_tbs;
                                               
                          $subtotal_prosentase = $harga_fee * $jumlahFeemasuk;
                                                         
                          $fee_prosentase_produk = $prosentase * $subtotal_prosentase / 100;
                                                         
                          $komisi = $fee_prosentase_produk;
              

                        $query91 = $db->query("UPDATE tbs_fee_produk SET jumlah_fee = '$komisi' WHERE nama_petugas = '$sales' AND kode_produk = '$kode_barang'");
                    }

                    else
                    {

                        $subtotal_prosentase = $harga_tbs * $jumlah_barang;
                        
                        $fee_prosentase_produk = $prosentase * $subtotal_prosentase / 100;

                        $query10 = $db->query("INSERT INTO tbs_fee_produk (nama_petugas, no_faktur_order, kode_produk, nama_produk, jumlah_fee, tanggal, jam) VALUES ('$sales', '$no_faktur', '$kode_barang',
                          '$nama_barang', '$fee_prosentase_produk', '$tanggal_sekarang', '$jam_sekarang')");

                    }


          }//if ($prosentase != 0){// 

        elseif ($nominal != 0) {
                
               if ($jumlah_tbs != 0) {// apablla barang ini sudah ada di tbs
                    
                    $jumlahFeemasuk = $jumlah_fee + $jumlah_tbs;
                    
                    $fee_nominal_produk = $nominal * $jumlahFeemasuk;
                    
                    $komisi_nominal = $fee_nominal_produk; 
                    
                $query911 = $db->query("UPDATE tbs_fee_produk SET jumlah_fee = '$komisi_nominal' WHERE nama_petugas = '$user' AND kode_produk = '$kode_barang'");
                }
                
                else
                {
                
                $fee_nominal_produk = $nominal * $jumlah_fee;
                
                $query10 = $db->query("INSERT INTO tbs_fee_produk (nama_petugas, no_faktur_order, kode_produk, nama_produk, jumlah_fee, tanggal, jam) VALUES ('$user', '$no_faktur', '$kode_barang', '$nama_barang', '$fee_nominal_produk', '$tanggal_sekarang', '$jam_sekarang')");
                }

        }

                // apablla barang ini sudah ada di tbs
         if ($data_tbs_penjualan['jumlah_data'] != 0) {  
            
        $query1 = $db->prepare("UPDATE tbs_penjualan_order SET jumlah_barang = jumlah_barang + ?, subtotal = ? , potongan = ? WHERE kode_barang = ? AND no_faktur_order = ? AND satuan = ? ");

        $query1->bind_param("iisssi",
            $jumlah_barang,$subtotal_order, $potongan_tampil, $kode_barang, $no_faktur,$satuan);


        $query1->execute();

    }
    else
    {
            $perintah = $db->prepare("INSERT INTO tbs_penjualan_order (no_faktur_order,kode_barang,nama_barang,jumlah_barang,satuan,harga,subtotal,tanggal,jam,tipe_barang,harga_konversi,potongan) VALUES (?,?,
            ?,?,?,?,?,?,?,?,?,?)");
            
            
            $perintah->bind_param("sssisiisssii",
            $no_faktur, $kode_barang, $nama_barang, $jumlah_barang, $satuan, $harga_tbs, $subtotal_order,$tanggal_sekarang,$jam_sekarang,$ber_stok,$harga_konversi,$potongan_tampil);
           
            
            
            $perintah->execute();

    }


    } // END ELSE dari IF ($stok_barang < 0) {

} // END berkaitan dgn stok == Barang

else{


         if ($prosentase != 0){// if ($prosentase != 0){// 

                    if ($jumlah_tbs != 0) {// apablla barang ini sudah ada di tbs
                        
                           $jumlahFeemasuk = $jumlah_fee + $jumlah_tbs;
                                               
                          $subtotal_prosentase = $harga_fee * $jumlahFeemasuk;
                                                         
                          $fee_prosentase_produk = $prosentase * $subtotal_prosentase / 100;
                                                         
                          $komisi = $fee_prosentase_produk;
              

                        $query91 = $db->query("UPDATE tbs_fee_produk SET jumlah_fee = '$komisi' WHERE nama_petugas = '$sales' AND kode_produk = '$kode_barang'");
                    }

                    else
                    {

                        $subtotal_prosentase = $harga * $jumlah_barang;
                        
                        $fee_prosentase_produk = $prosentase * $subtotal_prosentase / 100;

                        $query10 = $db->query("INSERT INTO tbs_fee_produk (nama_petugas, no_faktur_order, kode_produk, nama_produk, jumlah_fee, tanggal, jam) VALUES ('$sales', '$no_faktur', '$kode_barang',
                          '$nama_barang', '$fee_prosentase_produk', '$tanggal_sekarang', '$jam_sekarang')");

                    }


          }//if ($prosentase != 0){// 

        elseif ($nominal != 0) {
                
               if ($jumlah_tbs != 0) {// apablla barang ini sudah ada di tbs
                    
                    $jumlahFeemasuk = $jumlah_fee + $jumlah_tbs;
                    
                    $fee_nominal_produk = $nominal * $jumlahFeemasuk;
                    
                    $komisi_nominal = $fee_nominal_produk; 
                    
                $query911 = $db->query("UPDATE tbs_fee_produk SET jumlah_fee = '$komisi_nominal' WHERE nama_petugas = '$user' AND kode_produk = '$kode_barang'");
                }
                
                else
                {
                
                $fee_nominal_produk = $nominal * $jumlah_fee;
                
                $query10 = $db->query("INSERT INTO tbs_fee_produk (nama_petugas, no_faktur_order, kode_produk, nama_produk, jumlah_fee, tanggal, jam) VALUES ('$user', '$no_faktur', '$kode_barang', '$nama_barang', '$fee_nominal_produk', '$tanggal_sekarang', '$jam_sekarang')");
                }

        }

     // apablla barang ini sudah ada di tbs
                  // apablla barang ini sudah ada di tbs
             if ($data_tbs_penjualan['jumlah_data'] != 0) {

        # code...
        $query1 = $db->prepare("UPDATE tbs_penjualan_order SET jumlah_barang = jumlah_barang + ?, subtotal = ?, potongan = ? WHERE kode_barang = ? AND no_faktur_order = ? AND satuan = ? ");

        $query1->bind_param("iisssi",
            $jumlah_barang,$subtotal_order,$potongan_tampil, $kode_barang, $no_faktur,$satuan);


        $query1->execute();

    }
    else
    {
            $perintah = $db->prepare("INSERT INTO tbs_penjualan_order (no_faktur_order,kode_barang,nama_barang,jumlah_barang,satuan,harga,subtotal,tanggal,jam,tipe_barang,harga_konversi,potongan) VALUES (?,?,
            ?,?,?,?,?,?,?,?,?,?)");
            
            
            $perintah->bind_param("sssisiisssii",
            $no_faktur, $kode_barang, $nama_barang, $jumlah_barang, $satuan, $harga, $subtotal_order,$tanggal_sekarang,$jam_sekarang,$ber_stok,$harga_konversi,$potongan_tampil);
           
            
            
            $perintah->execute();

    }


}// END berkaitan dgn stok == Jasa



    ?>