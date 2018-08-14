<?php
$output_dir = "videos/";
if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
{
	$fileName =$_POST['name'];
	$fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files	
	$filePath = $output_dir. $fileName. ".jpg";
		if (file_exists($filePath)) 
	{
        unlink($filePath);
    }
	$filePath2 = $output_dir. $fileName. ".mp4";
	if (file_exists($filePath2)) 
	{
        unlink($filePath2);
    }
	echo "Deleted File ".$fileName."<br>";
}
?>