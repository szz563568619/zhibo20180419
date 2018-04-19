<?php

$cur = curl_init();
curl_setopt($cur, CURLOPT_URL, "http://124.224.239.197:16914/hqForPage/hqV.jsp");
curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
$data = curl_exec($cur);
curl_close($cur);
$data = iconv("GBK","UTF-8//IGNORE", $data);
$data = explode(',', $data);
echo json_encode($data);

?>