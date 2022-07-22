<?php
$db=mysqli_connect("localhost", "root", "", "registration");
$id = isset($_GET['id'])? $_GET['id'] : "";
$res = mysqli_query($db, "SELECT * FROM images WHERE BINARY id='$id'");
$row = mysqli_fetch_array($res);
$username=$row['username'];
$result = mysqli_query($db, "SELECT * FROM users WHERE BINARY username='$username'");
$ress=mysqli_fetch_array($result);
list($a)=array($row['username']);
list($b)=array($ress['password']);
list($zo)=array($row['image']);
$f=array();
$dec=array();
$decrypted=array();
$k=array();
$c=0;
$d=0;
for ($x = 0; $x < strlen($a); $x++) {
    array_push($f,$a[$x]);
    $c=$c+ord($a[$x]);
}
for ($x = 0; $x < strlen($b); $x++) {
    array_push($f,$b[$x]);
    $d=$d+ord($b[$x]);
}
$h=array_unique($f);
$i=$c-$d;
sort($h);
$ab=explode(join("",$h), $zo);
for ($x=0;$x<sizeof($ab);$x++)
{
    $e=explode("~!@#$%^&*():<>",$ab[$x]);
    for($y=0;$y<sizeof($e);$y++)
    {
        array_push($k,$e[$y]);
    }
}
for($x=0;$x<sizeof($k);$x++)
{
    if($k[$x]=="")
    {
        array_push($dec,' ');
    }
    else
    {
        for($y=0;$y<$i;$y++)
        {
            $j=$h[0];
            for($z=0;$z<sizeof($h)-1;$z++)
            {
                $h[$z]=$h[$z+1];
            }
            $h[sizeof($h)]=$j;
        }
        array_push($dec,chr(hexdec($k[$x])-ord($h[0])));
    }
}
for($x=0;$x<sizeof($dec)-1;$x++)
{
    array_push($decrypted,$dec[$x]);
}
$decrypted=join($decrypted);
$filename = $decrypted;
if($row['type']=="application/pdf")
{
header('Content-type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');
@readfile($filename);
}
elseif(substr($row['type'],0,5)=="image")
{
echo "<img src=".$filename." >";
}
elseif(substr($row['type'],0,5)=="video")
{
?>
<center>
<video width=100% height=100% controls>
 <source src="<?php echo $filename; ?>" type="video/mp4">
 </video>
</center>
<?php 
}
else
{
ignore_user_abort(true);
set_time_limit(0); // disable the time limit for this script
 
$path = $filename; // change the path to fit your websites document structure
 
//$dl_file = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).]|[\.]{2,})", '', $row['image']);
//$dl_file = filter_var($dl_file, FILTER_SANITIZE_URL);
$fullPath = $path;

if ($fd = fopen ($fullPath, "r")) {
    $fsize = filesize($fullPath);
    $path_parts = pathinfo($fullPath);
    $ext = strtolower($path_parts["extension"]);
    switch ($ext) {
        case "pdf":
        header("Content-type: application/pdf");
        header("Content-Disposition: attachment; filename=\"".$filename."\""); // use 'attachment' to force a file download
        break;
        // add more headers for other content types here
        default;
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"".$filename."\"");
        break;
    }
    header("Content-length: $fsize");
    header("Cache-control: private"); //use this to open files directly
    while(!feof($fd)) {
        $buffer = fread($fd, 2048);
        echo $buffer;
    }
}
fclose ($fd);
exit;
}
?>