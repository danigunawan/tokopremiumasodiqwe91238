<?php include 'session_login.php';
/* Database connection start */
include 'db.php';
/* Database connection end */
include 'sanitasi.php';
   
$suplier = stringdoang($_POST['suplier']);

// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;


$columns = array( 
// datatable column index  => database column name
  0 =>'no_faktur_order', 
  1 => 'kode_pelanggan',
  2 =>'tanggal', 
  3 => 'jam',
  4 => 'total',
  5 => 'keterangan',
  6 => 'Petugas_kasir',
  7 => 'id',
   
);


   // getting total number records without any search
if ($suplier == "") {
    $sql =" SELECT po.id, po.no_faktur_order, po.suplier, po.kode_gudang, po.tanggal, po.user, po.jam, po.total, po.status_order, po.keterangan, s.nama";
    $sql.=" FROM pembelian_order po INNER JOIN suplier s ON po.suplier = s.id WHERE po.status_order = 'Di Order' ";
}
else{
    $sql =" SELECT po.id, po.no_faktur_order, po.suplier, po.kode_gudang, po.tanggal, po.user, po.jam, po.total, po.status_order, po.keterangan, s.nama";
    $sql.=" FROM pembelian_order po INNER JOIN suplier s ON po.suplier = s.id WHERE po.status_order = 'Di Order' AND po.suplier = $suplier ";  
}

$query=mysqli_query($conn, $sql) or die("1.php: get employees");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if ($suplier == "") {
    $sql =" SELECT po.id, po.no_faktur_order, po.suplier, po.kode_gudang, po.tanggal, po.user, po.jam, po.total, po.status_order, po.keterangan, s.nama";
    $sql.=" FROM pembelian_order po INNER JOIN suplier s ON po.suplier = s.id WHERE po.status_order = 'Di Order' AND 1=1 ";
}
else{
    $sql =" SELECT po.id, po.no_faktur_order, po.suplier, po.kode_gudang, po.tanggal, po.user, po.jam, po.total, po.status_order, po.keterangan, s.nama";
    $sql.=" FROM pembelian_order po INNER JOIN suplier s ON po.suplier = s.id WHERE po.status_order = 'Di Order' AND po.suplier = $suplier AND 1=1 ";  
}

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
  $sql.=" AND ( po.no_faktur_order LIKE '".$requestData['search']['value']."%' ";    
  $sql.=" OR po.suplier LIKE '".$requestData['search']['value']."%' ";
  $sql.=" OR po.total LIKE '".$requestData['search']['value']."%' ";
  $sql.=" OR po.tanggal LIKE '".$requestData['search']['value']."%' ";
  $sql.=" OR s.nama LIKE '".$requestData['search']['value']."%' ";
  $sql.=" OR po.kode_gudang LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($conn, $sql) or die("2.php: get employees");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 

$sql.=" ORDER BY po.id ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */  
$query=mysqli_query($conn, $sql) or die("3.php: get employees");

$data = array();
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
  $nestedData=array(); 

    $nestedData[] = $row['no_faktur_order'];
    $nestedData[] = $row['suplier'] ." - ".$row['nama'];
    $nestedData[] = $row['tanggal'];
    $nestedData[] = $row['jam'];
    $nestedData[] = rp($row['total']);
    $nestedData[] = $row['keterangan'];
    $nestedData[] = $row['kode_gudang'];
    $nestedData[] = $row['suplier'];
    $nestedData[] = $row['id'];



  $data[] = $nestedData;
}



$json_data = array(
      "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
      "recordsTotal"    => intval( $totalData ),  // total number of records
      "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
      "data"            => $data   // total data array
      );

echo json_encode($json_data);  // send data as json format

?>



    

