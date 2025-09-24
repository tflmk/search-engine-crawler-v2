<?php
if (isset($_GET["url"])){
  $newsarticle = $_GET["url"];
require_once ("config.php");
$sql="SELECT * FROM `search` WHERE `url`= '".$newsarticle."'";

$result = mysqli_query($conn,$sql);

echo "<table border='0'>
<tr>
<th>Cache text</th>
<th>Link</th>
<th>Cache date</th>

</tr>";

while($row = mysqli_fetch_array($result))
{
    echo "<tr>";
    echo "<td>" . $row['cachetext'] . "</td>";
    echo "<td>" . $row['url'] . "</td>";
    echo "<td>" . $row['date'] . "</td>";
    echo "</tr>";
}

echo "</table>";
}
?>