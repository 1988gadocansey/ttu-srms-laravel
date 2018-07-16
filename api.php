<?php
$url = 'data.json'; // path to your JSON file
$data = file_get_contents("127.0.0.1/admissions/srms/forward"); // put the contents of the file into a variable
$records = json_decode($data); // decode the JSON feed

 
foreach ($records as $i) {
						$no++;
						echo '<tr>';
						echo"<td>$no</td>";
						echo '<td>'.$i["application_number"].'</td>';
						echo'<td>'.$i["lastname"]." ".$i["firstname"] .'</td>';
						echo'<td>'.$i["programme"].'</td>';
						echo'<td> '.$i["fees"].'</td>';
						echo'<td>'.$i["hall"].'</td>';
						echo'<td><a href="http://admissions.ttuportal.com">Print Letter</a></td>';
						echo '</tr>';
					}