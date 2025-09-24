<?php 
require_once ("config.php"); 
?>
<?php 

     if(isset($_POST['submit'])) {
          $id = $_POST['id'];
          $title = $_POST['title'];
          $description = $_POST['description'];
          $url = $_POST['url'];



          $sql = "INSERT INTO search (id, title, description, url, date) VALUES('$id', '$title', '$description', '$url', '".date('Y-m-d H:i:s')."')";
          $result = mysqli_query($conn, $sql);

          if($result == true) {
               header("location:submit.php");
               exit();
               
          } else {
               echo 'data not inserted successfully'.mysqli_connect_error();
          }
     }

     

?>


<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Submit url</title>
</head>
<body>
     <center>
         <h2>Submit url</h2>
     <form action="submit.php" method="POST">          
          <input type="hidden" name="id" placeholder="Enter your id" required> <br> <br>
          <input type="text" name="title" placeholder="Enter your title" required> <br> <br>
          <input type="text" name="description" placeholder="Enter your description" required> <br> <br>
          <input type="url" name="url" placeholder="Enter your url" required><br> <br>

          <input type="submit" name="submit" value="Submit Here"> <br> <br>

     </form>

     <a href="search.php">Search Engine</a>
</body>
</center>
</html>
