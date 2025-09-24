<?php 
require_once ("config.php"); 
?>
<?php 

if (isset($_GET['submit'])) { 
    $search = $_GET['search']; 
    

     $words = explode(' ', $_GET['search']);
     $regex = implode('|', $words);
     $sql = "SELECT * FROM search WHERE description REGEXP '{$regex}' LIMIT 0, 10";
     $result = mysqli_query($conn, $sql);

     $data = array();
     if(mysqli_num_rows($result)) {
          while ($data = mysqli_fetch_array($result)) {
              $data[] = $row;


          $id = $data['id'];
          $title = $data['title'];
          $description = preg_replace('/(' . $search . ')/s', '<strong>\1</strong>', $data['description']);
          $url = $data['url'];
          echo '<center>' .$title." ".$description. '</center>';
          echo '<center>' .$url. ' <a href="https://priserna.com/search/cache.php?url=' .$url. '" title=Cache>Cache</a></center>'; 
          echo '<center><h3> you search for: &nbsp;'  .$search. '</h3></center>';
     }
    
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search</title>
</head>
<body>
    <center>
    <form action="" method="GET"> <!-- Use method="GET" -->
        <label for=""><h2>Search Engine</h2></label><br>
        <input type="text" name="search"> <br><br>
        <button type="submit" name="submit">Search</button><br><br>
    </form>
    <a href="submit.php">Submit url</a> &nbsp; <a href="crawler.php">Crawl url</a>

</body>
</center>
</html>
