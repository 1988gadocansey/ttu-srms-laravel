

<html>
<head><link rel="stylesheet" type="text/css" href="style2.css" />
<title>Reg_Voter</title>
</head>
<body>
<table>
	<tr>
		<td>No</td>
		<td>Index No</td>
		<td>Bill</td>
		<td>Paid</td>
		<td>Owe</td>
	</tr>
		<?php
		
		$count = 0;
		//$myPDO = new PDO('mysql:host=localhost;dbname=ttuporta_srms', 'root', '');
		$myPDO = new PDO('mysql:host=localhost;dbname=ttupor5_srms', 'ttupor5_srms', 'PRINT45dull');	
		$result = $myPDO->query("Select INDEXNO, BILLS from tpoly_students where STATUS = 'In school'");
		$akose = $result -> fetchAll();

			foreach ($akose as $row) {
				$count = $count + 1;
				$akose1st = $row['INDEXNO'];
					?>
					
					<tr>
						<td><?php echo $count; ?></td>
						<td><?php echo $row['INDEXNO']; ?></td>
						<td><?php echo $row['BILLS']; ?></td>
							
<?php
$resultName = $myPDO->prepare("Select sum(AMOUNT) as kk from tpoly_feedetails where INDEXNO like :akose1s");
$resultName->execute(array(':akose1s' => $akose1st));
$resultNam = $resultName -> fetchAll();

foreach ($resultNam as $rowName) {
	$rpaid=$rowName['kk'];
	$rowe=$row['BILLS']-$rowName['kk'];

$oweupdate = $myPDO->prepare("Update tpoly_students Set PAID = :rpaid, BILL_OWING = :bowe where INDEXNO like :akose1s");
$oweupdate->execute(array(':bowe' => $rowe, ':akose1s' => $akose1st, ':rpaid' => $rpaid));
	//update tpoly_students
	//set bill owind = :Owe
	//where indexno = :index no
//($result->fetchColumn();
?>

							<td><?php echo $rowName['kk']; ?></td>
							<td><?php echo $rowe; ?></td>
							<?php
							}
							?>
						</tr>			
														
							<?php
	//echo $row['Name'];

}

							?>
					</table>
					<?php
		
		header("Location: ".$_SERVER['PHP_SELF']); 
?>
</body>
</html>

