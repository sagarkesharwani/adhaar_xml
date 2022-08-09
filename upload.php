<?php

// $user_id = 6;
include 'dbconn.php';

if (isset($_REQUEST['submit'])) {

    $name = mysqli_real_escape_string($con, $_FILES["file"]["name"]);
    $password = mysqli_real_escape_string($con, $_REQUEST["password"]);
    $target = mysqli_real_escape_string($con, 'zipfile/' . $_FILES["file"]["name"]);
    $pincode = mysqli_real_escape_string($con, $_REQUEST['pincode']);
    $city = mysqli_real_escape_string($con, $_REQUEST['city']);
    move_uploaded_file($_FILES["file"]["tmp_name"], $target);
    $adhaar_no = mysqli_real_escape_string($con, $_REQUEST['adhaar_no']);
    $rand = random_int(0, 11999);
    $zip = new ZipArchive;
    if ($zip->open('zipfile/' . $name) === TRUE) {
        $zip->setPassword($password);
        if ($zip->extractTo('zipfile/xml/' . $rand)) {;
            $zip->close();
            $dir = 'zipfile/xml/' . $rand;
            $files = scandir($dir, 1);
            $xml_file_name = $files['0'];
            $read_xml = simplexml_load_file($dir . "/" . $xml_file_name);
            $json_encode = json_encode($read_xml);
            $array = json_decode($json_encode, true);
            $xml_add = substr($array['@attributes']['referenceId'], 0, 4);
            $user_add = substr($adhaar_no, -4);
            $image = $array['UidData']['Pht'];
            $base_64 = base64_decode($image);
            file_put_contents('zipfile/xml/' . $rand . '/' . $rand . ".png", $base_64);
            if ($xml_add == $user_add && $pincode == $array['UidData']['Poa']['@attributes']['pc']) {
                $insertquery = "insert into kyc(user_id,kyc,adhaar_no,name,city,pincode,adhar_zip,zip_password,profile) values('3','1','$adhaar_no','SAGAR','$city','$pincode','$name','$password','$rand')";
                $iquery = mysqli_query($con, $insertquery);
                if ($iquery) {
?>
<script>
alert("KYC Done");
</script>
<?php
                } else {
                ?>
<script>
alert("KYC NOT DONE");
</script>
<?php

                }
            }
            ?>
<script>
window.location.replace('chekin.php');
</script>

<?php
        } else {
        ?>
<script>
window.location.replace('chekin.php');
alert('Password Incorrect');
</script>
<?php
        }
        // print_r($array);
    } else {
        echo "Failed";
    }
}