<?php
/*
By Ivan Hanloth
本文件为易传文件提取接口文件
2022/4/3
*/
header("Access-Control-Allow-Origin:*");
header("Content-type:text/json");

include dirname(__FILE__)."/../../common.php";
$r=$_REQUEST["key"];
$key=json_decode($r,true);
$key=$key["key"];
$dbcount=mysqli_query($db,"SELECT count(*) FROM `data` WHERE binary `gkey` = '{$key}'");
$dbcount=mysqli_fetch_row($dbcount);
if($dbcount[0]==0) {
	echo json_encode(array("code"=>"100","tip"=>"不存在此提取码或提取码已过期"),  JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
} else {
	$dbinfo=mysqli_query($db,"SELECT * FROM `data` WHERE binary `gkey` = '{$key}'");
	$dbinfo=mysqli_fetch_assoc($dbinfo);
    $times=$dbinfo["times"]-1;
	if($dbinfo["type"]==1){
    	$times=$dbinfo["times"];
	}
	$tillday=$dbinfo["tillday"];
	if($times<0) {
		echo json_encode(array("code"=>"100","tip"=>"不存在此提取码或提取码已过期"),  JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
	} else {
	    if($dbinfo["method"]==2){
	        $rdata=file_get_contents($dbinfo["data"]);
	    }else{
	        $rdata=$domain."download/?key=".$dbinfo["gkey"];
	    }
		echo json_encode( array("code"=>"200","times"=>$times,"type"=>$dbinfo["type"],"data"=>$rdata,"origin"=>$dbinfo["origin"],"tillday"=>$tillday),  JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
		if($times>0 and $dbinfo["type"]==1) {
			mysqli_query($db,"UPDATE `data` SET `times` = '{$times}' WHERE binary `gkey` = '{$key}'");
		} else {
			if($dbinfo["type"]==1 or $dbinfo["method"]==2) {
				$now=time();
				$deletetime=date('Y-m-d H:i:s', $now + 1800);
				mysqli_query($db,"UPDATE `data` SET `tillday` = '{$deletetime}',`times` = '{$times}' WHERE binary `gkey` = '{$key}'");
			} elseif($dbinfo["type"]==2) {
				mysqli_query($db,"DELETE FROM `{$dbname}`.`data` WHERE binary `gkey` = '{$key}'");
			};
		};
	};
};
?>