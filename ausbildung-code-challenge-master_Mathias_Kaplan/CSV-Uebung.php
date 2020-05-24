<?php

function setzeTitel($dateiName) {
	echo($dateiName);
}

function leseCSV($dateiName) {
	
	$dateiInhalt = fopen($dateiName, "r");				//Einlesen der Datei
	
	echo("<table border=1 cellspacing=0>"); 			
	
	$zeile = fgetcsv($dateiInhalt, 500, "\n");			//speichert die erste Zeile, die die Spaltennamen enthaelt in $zeile,
														//
	$inhalte = explode(";" ,$zeile[0]);					//um sie in die einzelnen Namen zu zerlegen
	echo("<tr>");										//
	foreach ($inhalte as $zelle) {						//
		echo("<th>".$zelle."</th>");					//und diese als <th>-Inhalt zu benutzen
	}
	echo("</tr>");
	
	$csvInhalte = "";
	while ($zeile = fgetcsv($dateiInhalt, 500, "\n")) {											//liest die restlichen Zeilen aus
		$teilstr = str_replace(".jpg", ".jpg;", implode($zeile, "")); 							//da nur wenige Datensaetze in nur einer Zeile stehen, 
																								//werden allen ".jpg"s ein ";" angehaengt, damit es zu weniger Fehlern kommt
		$csvInhalte = $csvInhalte.$teilstr;
	}
	
	$tabellenZeilen = $csvInhalte;
	$csvAusgabe = "";
	$i = 0;
	while ($i < substr_count($csvInhalte, ".jpg")) {											//bis alle Datensaetze bearbeitet wurden, wird die Schleife ausgefuehrt
		$tabellenZeile = substr($tabellenZeilen, 0, strpos($tabellenZeilen, ".jpg") + 4);		//speichert einen Datensatz in $tabellenZeile
		if (substr_count($tabellenZeile, ";") >= 16) {											//zaehlt die Trennzeichen im Datensatz, wenn diese mehr als 16 betraegt (Spaltenanzahl),
			$ueberschuss = 16 - substr_count($tabellenZeile, ";");								//bedeutet das, dass die Beschreibung auch das Trennzeichen enthaelt
			$zeilenDaten = explode(";", $tabellenZeile);										//In dem Fall wird der Datensatz in seine Einzelteile zerlegt,
			$alternativZeile = $zeilenDaten[0].";".$zeilenDaten[1].";".$zeilenDaten[2];			//die ersten Datensaetze wieder zusammengebaut
			for ($u = 0; $u < $ueberschuss; $u++) {												//
				$alternativZeile = $alternativZeile.$zeilenDaten[4+u];							//und anschliessend die Beschreibung zusammengefasst
			}
			for ($z = 4 + $ueberschuss; $z <= substr_count($tabellenZeile, ";"); $z++) {		//Anschliessend wird der Rest wieder dahintergehaengt
				$alternativZeile = $alternativZeile.";".$zeilenDaten[$z];
			}
			$alternativZeile = $alternativZeile.";";											
			$csvAusgabe = $csvAusgabe."SEMIKOLON".$alternativZeile;								//und mit ersetzter Beschreibung an die gesamten Daten rangehaengt
		}else {
			$csvAusgabe = $csvAusgabe.$tabellenZeile.";";										//Falls das ganze nicht der Fall war wird der Datensatz direkt hinter die gesamten Daten gehaengt
		}
		$i++;
		$tabellenZeilen = strstr($tabellenZeilen, ".jpg;");
		$tabellenZeilen = substr($tabellenZeilen, 5, -1);
		
	}
	
	echo("<tr>");
	
	$i = 0;
	$zellenInhalte = explode(";", $csvAusgabe);									//Zerlegen der Datensaetze
	while ($i < substr_count($csvAusgabe, ";")) {
		$zellenInhalte = str_replace("SEMIKOLON", ";", $zellenInhalte);			//Beschreibung kann nun wieder mit einem Semikolon dargestellt werden
		echo("<td>".$zellenInhalte[$i]."</td>");
		$i++;
		if (($i % 16) == 0) {													//Nach 16 Daten (eine Zeile) wird eine neue Zeile angefangen
			echo("</tr>");
			if ($i < substr_count($csvAusgabe, ";")) {
				echo ("<tr>");
			}
		}
	}
	echo("</table>");
}

?>

<html>
	<head>
		<title>
			<?php	
				setzeTitel('Artikel.csv');		//Setzen des Titels
			?>
		</title>
	</head>
	<body>
		<?php	
			leseCSV('Artikel.csv');				//Aufruf der Funktion
		?>
	</body>
</html>
