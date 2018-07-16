<html>
<head><link rel="stylesheet" type="text/css" href="style2.css" />
<title>Reg_Voter</title>
</head>
<body>
<table>
<tr>
							<td>Date</td>
							<td>Name B</td>
							<td>Amount</td>
							<td>Name</td>
							<td>Index No</td>
							<td>Name 2</td>
							<td>Index No</td>
							<td>Name 3</td>
							<td>Index No</td>
							</tr>
<?php
$count = 0;
//$myPDO = new PDO('mysql:host=localhost;dbname=ttuporta_srms', 'root', '');
		$myPDO = new PDO('mysql:host=localhost;dbname=ttupor5_srms', 'ttupor5_srms', 'PRINT45dull');	

$result = $myPDO->query("Select Date2, Name, Amount,  SUBSTRING_INDEX(Name, ' ',1), SUBSTRING_INDEX(Name, ' ',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(Name, ' ',2), ' ',-1) from Find_index");

$akose = $result -> fetchAll();

foreach ($akose as $row) {
$count = $count + 1;
$akose1st = $row["SUBSTRING_INDEX(Name, ' ',1)"];

$akose2 = $row["SUBSTRING_INDEX(SUBSTRING_INDEX(Name, ' ',2), ' ',-1)"];

//$akose22 = SUBSTRING_INDEX("kojo ennin", " ",-1);

$akoselast = $row["SUBSTRING_INDEX(Name, ' ',-1)"];

?>
					
						<tr>
							<td><?php echo $row['Date2']; ?></td>
							<td><?php echo $row['Name']; ?></td>
							<td><?php echo $row['Amount']; ?></td>
							
							

<?php
$resultName = $myPDO->prepare("Select NAME, INDEXNO from tpoly_students where NAME like :akose1s and NAME like :akose1t and NAME like :akose1a");
$resultName->execute(array(':akose1s' =>'%'.$akose1st.'%', ':akose1t' =>'%'.$akose2.'%', ':akose1a' =>'%'.$akoselast.'%'));
$resultNam = $resultName -> fetchAll();

foreach ($resultNam as $rowName) {
	
//($result->fetchColumn();
?>

							<td><?php echo $rowName['NAME']; ?></td>
							<td><?php echo $rowName['INDEXNO']; ?></td>
							<?php
							}
							?>
						</tr>			
														
							<?php
	//echo $row['Name'];

}
//GRAPHICS
//TEXTILES
//HOSPITALITY
//ACCOUNTANCY
//TOURISM
//MARKETING
//ELECTRICALS
//MECHANICAL
//STATISTICS
//CIVIL
//SEC_AND_MGMT


							//GN hnd ends
							//GN mature starts
							?>
					</table>
</body>
</html>

