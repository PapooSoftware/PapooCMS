// Form
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
<input type="text" name="plz" value="<?php echo $_GET['plz']; ?>" maxlength="5"/><br />
<input type="text" name="radius" value="<?php echo $_GET['radius']; ?>" maxlength="5"/><br />
<input type="submit" name="suche" value="Suche" />
</form>

// PHP-Code
// This script is based on kilometer. If you use miles simply convert the radius to kilometer (radius=radius*1.609344)
// 'Name of database-table: 'geodb'. Please change if you use other tablename.

<?php
if(isset($_GET['suche']))
{
    $plz = $_GET['plz'];
    $radius = $_GET['radius'];

    $conn = mysql_connect('127.0.0.1', 'root', '') or die('db connect error: ' . mysql_error());
    mysql_select_db('igonow', $conn) or die('could not select database');

    $sqlstring = "SELECT * FROM geodb WHERE plz = '".$plz."'";
    $result = mysql_query($sqlstring);

    $row = mysql_fetch_assoc($result);

    $lng = $row["longitude"] / 180 * M_PI;
    $lat = $row["latitude"] / 180 * M_PI;

    mysql_free_result($result);

    $sqlstring2 = "SELECT DISTINCT geodb.plz,geodb.ort,(6367.41*SQRT(2*(1-cos(RADIANS(geodb.latitude))*cos(".$lat.")*(sin(RADIANS(geodb.longitude))*sin(".$lng.")+cos(RADIANS(geodb.longitude))*cos(".$lng."))-sin(RADIANS(geodb.latitude))* sin(".$lat.")))) AS Distance FROM geodb AS geodb WHERE (6367.41*SQRT(2*(1-cos(RADIANS(geodb.latitude))*cos(".$lat.")*(sin(RADIANS(geodb.longitude))*sin(".$lng.")+cos(RADIANS(geodb.longitude))*cos(".$lng."))-sin(RADIANS(geodb.latitude))*sin(".$lat."))) <= '".$radius."') ORDER BY Distance";

    $result = mysql_query($sqlstring2) or die('query failed: ' . mysql_error());

    $str = "<table width=\"300\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
    $str .= "<tr>";
    $str .= "<th>PLZ</th>";
    $str .= "<th>Ort</th>";
    $str .= "<th>Entfernung</th>";
    $str .= "</tr>";

    while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
    {
        $str .= "<tr><td>".$row["plz"]."</td><td>".$row["ort"]."</td><td>".round($row['Distance'])."km</td></tr>";
    }

    $str .= "</table>";

    mysql_free_result($result);
    mysql_close($conn);
    echo $str;
}
?>