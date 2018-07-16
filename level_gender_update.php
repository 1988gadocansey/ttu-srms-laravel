<html>
<head><link rel="stylesheet" type="text/css" href="style2.css" />
<title>Reg_Voter</title>
</head>
<body>
<table>
	<tr>
		<td>No</td>
		<td>Level</td>
		<td>Male</td>
		<td>Female</td>
		<td>Unknown</td>
		<td>Total</td>
		<td>Reg</td>
		<td>Paid</td>
		<td>Owing</td>
		<td>Faculty</td>
	</tr>
		<?php
		//////echo 1;
		$count = 0;
		//$myPDO = new PDO('mysql:host=localhost;dbname=ttuporta_srms', 'root', '');
		/////die($myPDO);
		$myPDO = new PDO('mysql:host=localhost;dbname=ttupor5_srms', 'ttupor5_srms', 'PRINT45dull');	
		//$result = $myPDO->query("Select LEVEL, BILLS from tpoly_students where STATUS <> 'In school'");
		$result = $myPDO->query("SELECT LEVEL, COUNT(CASE WHEN SEX = 'Male' THEN SEX END) AS Male, COUNT(CASE WHEN SEX = 'Female' THEN SEX END) AS Female, COUNT(LEVEL) as Total, COUNT(CASE WHEN REGISTERED = '1' THEN LEVEL END) AS Reg, SUM(PAID) AS Paid, SUM(BILL_OWING) AS Owing from tpoly_students where STATUS = 'In school' GROUP BY LEVEL");
		$akose = $result -> fetchAll();

			foreach ($akose as $row) {
				$count = $count + 1;
				$levelowe = $row['LEVEL'];
				$Maleowe = $row['Male'];
				$Femaleowe = $row['Female'];
				$Totalowe = $row['Total'];
				$Reglowe = $row['Reg'];
				$Paid = $row['Paid'];
				$Owing = $row['Owing'];
					?>
					
					<tr>
						<td><?php echo $count; ?></td>
						<td><?php echo $row['LEVEL']; ?></td>
						<td><?php echo $row['Male']; ?></td>
						<td><?php echo $row['Female']; ?></td>
						<td><?php echo $Totalowe-$Femaleowe-$Maleowe; ?></td>
						<td><?php echo $row['Total']; ?></td>
						<td><?php echo $row['Reg']; ?></td>
						<td><?php echo $row['Paid']; ?></td>
						<td><?php echo $row['Owing']; ?></td>
<?php
/////$resultName = $myPDO->prepare("Select sum(AMOUNT) as kk from tpoly_feedetails where INDEXNO like :akose1s");
/////$resultName->execute(array(':akose1s' => $akose1st));
/////$resultNam = $resultName -> fetchAll();
///////////////echo $akose1st;
//////foreach ($resultNam as $rowName) {
	/////$rowe=$row['BILLS']-$rowName['kk'];

$oweupdate = $myPDO->prepare("Update tpoly_levelgender Set Male = :Maleowe, Female = :Femaleowe, Total = :Totalowe, Reg = :Reglowe, Unknown = :Unknown, Paid = :Paid, Owing = :Owing where LEVEL like :levelowe");
$oweupdate->execute(array(':levelowe' => $levelowe, ':Maleowe' => $Maleowe, ':Femaleowe' => $Femaleowe, ':Totalowe' => $Totalowe, ':Reglowe' => $Reglowe, ':Paid' => $Paid, ':Owing' => $Owing, ':Unknown' => $Totalowe-$Femaleowe-$Maleowe));
	//update tpoly_students
	//set bill owind = :Owe
	//where indexno = :index no
//($result->fetchColumn();
?>

							
							<?php
							//////}
							?>
						</tr>			
														
							<?php
	//echo $row['Name'];

}

							?>
					</table>
</body>
</html>

