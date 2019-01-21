<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);


require_once("phpexcel/Classes/PHPExcel/IOFactory.php");

require_once("db.php");
db_open(); 

if(isset($_POST['import']) ){
	$name_table = mp($_POST['name_table']);
	$name_table = trim($name_table);
	$name_table = str_replace(" ", "_", $name_table);
	
	if(isset($_FILES['userFile']['tmp_name']) && file_exists($_FILES['userFile']['tmp_name'])){
	foreach($_POST as $name => $value){$$name = mp($value);}
	 
	$inputFileName = $_FILES['userFile']['tmp_name'];
 	$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
 	$objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($inputFileName); 
	$sheetsavail = $objPHPExcel->getSheetNames();
	$sheet = $objPHPExcel->getSheet(0);
	$highestRow = $sheet->getHighestRow();
	$highestColumn = $sheet->getHighestColumn();
	$nextline = '';
	$countr = 0;
	$list = array();
	$table = "";
	$heads = "";
		
		 
	for ($row = 1; $row <= $highestRow; $row++) {
		$data = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
		$data = $data[0];
 		if($data[0] == ''){continue;}
	
		if( $countr == 0){ 
			$table = "z_temp_".date('YmdHis');
			if($name_table != ''){$table .= $name_table;}
			$query = "CREATE TABLE `".$table."` (
			`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
			";
			$colnum = 1;
			foreach($data as $ik => $iv){
				$iv = trim($iv);
				$iv = str_replace(" ", "_", $iv);
				if($iv  == ''){$colnum ++; $iv = 'col_'.$colnum;}
				$query.= "`{$iv}` VARCHAR(250)   NULL DEFAULT '0',";
				$headers[$ik] =   '`'.$iv.'`';
			}
			$query = rtrim($query, ',');
			$query .= ", 	PRIMARY KEY (`id`) );";
			
			qu($query);
			$heads = join(' , ',$headers);
			
			$add_rows_query = "Insert into {$table} ({$heads}) Values ";
		}
		else{
			$add_rows_query .=  " ( ";
			foreach($data as $ik => $iv){
				
				$iv = mp($iv);
				$add_rows_query.= " '{$iv}' , ";
		
			}
			
			$add_rows_query = rtrim($add_rows_query , ', ');
			
			$add_rows_query .= " ), ";
			
		}
		 $countr ++;
		
	}
		
		$add_rows_query = rtrim($add_rows_query , ', ');
		$add_rows_query .= ";";
		if($countr > 1){qu($add_rows_query);}
		
		$alert = "File uploaded successfully. Table " . $table . " created. <br>" . ($countr - 1) . " rows inserted";
	}else{
		$alert = "Please choose a file to upload.";
	}
}

?> 
  
<div style="width:500px; margin: 0 auto; ">
<?php if(isset($alert)){echo '<div id="alert">'. $alert.'</div>';}?>
<br>
<h2>Import Table Tool</h2>
	<div  >
		<form method="post" action="" enctype='multipart/form-data' >
			<div style="float:left;">
				<label for="name_table">Table Note:</label>
                <input type="text" name="name_table" id="name_table">
                <span style="font-size: 12px; color: #9C9B9B;"><br>(Optional. Will be apended to the end of your table name)</span><br><br>

              <input name="userFile" id = "userFile" type="file" ><br /><br />
			<input name="import" id ="import" type="submit" class="btn1"  value="Import">
		  </div>
		</form>
	</div> 
</div>
  
 
