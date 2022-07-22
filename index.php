<?php 
  session_start(); 

  if (!isset($_SESSION['username'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: login.php');
  }
  if (isset($_GET['logout'])) {
  	session_destroy();
  	unset($_SESSION['username']);
  	header("location: login.php");
  }
?>
<?php
  // Create database connection
  $namee=$_SESSION['username'];
  $db = mysqli_connect("localhost", "root", "", "registration");
  // Initialize message variable
  $msg = "";

  // If upload button is clicked ...
  if (isset($_POST['upload'])) {
    // Get image name
    $image = $_FILES['image']['name']; 
    $type = $_FILES['image']['type'];
    // Get text
    $image_text = mysqli_real_escape_string($db, $_POST['image_text']);

    // image file directory
    $target = basename($image);
  list($a)=array($_SESSION['username']);
  list($b)=array($_SESSION['pass']);
  list($zo)=array($image);
  $f=array();
  $enc=array();
  $h = array();
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
  for ($x=0;$x<strlen($zo);$x++)
  {
      if($h[$x]==' ')
      {
          array_push($enc,join("",$h));
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
          array_push($enc,dechex(ord($h[0])+ord($zo[$x])));
          array_push($enc,"~!@#$%^&*():<>");
      }
  }
  $imagee=join($enc);
      $sql = "INSERT INTO images (image, image_text,type,username) VALUES ('$imagee', '$image_text','$type','$namee')";
      // execute query
      mysqli_query($db, $sql);

      if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $msg = "Image uploaded successfully";
      }else{
        $msg = "Failed to upload image";
      }
    }
    $result = mysqli_query($db, "SELECT * FROM images WHERE BINARY username='$namee' ");
  if (isset($_POST['send'])) {
      $messageto = mysqli_real_escape_string($db, $_POST['messageto']);
      $sql = "INSERT INTO tex (messageto,username) VALUES ('$messageto','$namee')";
      mysqli_query($db, $sql);
    }
    $resultt = mysqli_query($db, "SELECT * FROM tex");
  ?>



  <!DOCTYPE html>
  <html>
  <head>
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style type="text/css">
    form div{
      margin-top: 5px;
    }
    #img_div{
      width: 80%;
      padding: 5px;
      margin: 15px auto;
      border: 1px solid #cbcbcb;
    }
    #img_div:after{
      content: "";
      display: block;
      clear: both;
    }
    img{
      float: left;
      margin: 5px;
      width: 300px;
      height: 140px;
    }
    .split {
    position: fixed;
    z-index: 1;
    top: 0;
    overflow-x: hidden;
    padding-top: 20px;
  }

  /* Control the left side */
  .left {
    height: 100%;
    width: 50%;
    left: 0;
  }
  .right {
    height: 100%;
    width: 50%;
    right: 0;
  }

  </style>
  </head>
  <body>
  <div class="split left">
  <div class="header">
    <h2>Home Page</h2>
  </div>
  <div class="content">
      <!-- notification message -->
      <?php if (isset($_SESSION['success'])) : ?>
        <div class="error success" >
          <h3>
            <?php 
              echo $_SESSION['success']; 
              unset($_SESSION['success']);
            ?>
          </h3>
        </div>
      <?php endif ?>

      <!-- logged in user information -->
      <?php  if (isset($_SESSION['username'])) : ?>
        <p>Welcome <strong><?php echo $_SESSION['username']; ?></strong></p>
        <p> <a href="index.php?logout='1'" style="color: red;">logout</a> </p>
    <form method="POST" action="index.php" enctype="multipart/form-data">
      <input type="hidden" name="size" value="1000000">
      <div>
        <input type="file" name="image">
      </div>
      <div>
        <textarea 
          id="text" 
          cols="15" 
          rows="4" 
          name="image_text" 
          placeholder="Hint for the uploaded file"></textarea>
      </div>
      <div>
        <button type="submit" name="upload">Upload</button>
      </div>
    </form>
    <?php
      while ($row = mysqli_fetch_array($result)) {
        list($a)=array($row['username']);
        list($b)=array($_SESSION['pass']);
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
              echo "<div id='img_div'>";
        echo "<li><a href='view.php?id=".$row['id']."' target='_blank'>".$decrypted."</a>";
                //echo "<img src='images/".$row['image']."' >";
                echo "<p>".$row['image_text']."</p>";
              echo "</div>";
      }
    ?>
      <?php endif ?>
    </div>
  <div class="split right">
    <div class="header">
    <h2>Chats</h2>
  </div>
  <div class="content">
    <form method="POST" action="index.php" enctype="multipart/form-data">
      <div>
        <textarea 
          id="text" 
          cols="15" 
          rows="2" 
          name="messageto" 
          placeholder="Send message globaly"></textarea>
      </div>
      <div>
        <button type="submit" name="send">Send</button>
      </div>
    </form>
      <?php
      while ($row = mysqli_fetch_array($resultt)) {
          echo "<div id='img_div'>";
          echo $row['username']." : ";
          echo $row['messageto'];
        echo "</div>";
      }
    ?>
  </div>
  </div>
  </body>
</html>