<?php
	class Demo{
		// Definiere Parameter, die eine Demo beschreiben.
		private $Demo_ID; # Primärschlüssel des Eintrags
		private $Titel; # Name der Demonstration 
		private $Schlagwort; # Kurzbeschreibung in 3 Worten
		private $Kategorie; # Kategorie des Demoanlasses
		private $Beschreibung; # Ausführliche Beschreibung & zusätzliche Details
		private $Veranstaltende; # Name der veranstaltenden Organisation/Institution/Person(en)
		private $Beginn; # Datum und Uhrzeit des Demobeginns
		private $Ende; # Datum und Uhrzeit des Demoendes
		private $PLZ; # Postleitzahl der Location
		private $Ort; # Ortsname der Location
		private $Adresse; # Adresse der Location
        private $Koordinaten; # Koordinaten der Location
        private $Kontakt; # Email-Kontakt der Veranstaltenden
		
		/*
        Diese Funktion ruft ein neues Objekt der Klasse Demo auf, wenn es für weitere Funktionen benötigt wird.
        Speichert alle Informationen zu einer bestimmten Demo (definiert über die $Demo_ID) in diesem neuen "Demo-Objekt".
        Die Variable $link enthält alle Informationen, die zur Verbindung mit der Datenbank notwendig sind und ist in der Datei db_setup.php definiert.
		*/
		public function Demo($Demo_ID){
			global $link;
			$query = "SELECT * FROM Demoliste WHERE Demo_ID = $Demo_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
				$this->Titel = $row->Titel;
				$this->Schlagwort = $row->Schlagwort;
				$this->Kategorie = $row->Kategorie;
				$this->Beschreibung = $row->Beschreibung;
				$this->Veranstaltende = $row->Veranstaltende;
				$this->Beginn = $row->Beginn;
				$this->Ende = $row->Ende;
				$this->PLZ = $row->PLZ;
				$this->Ort = $row->Ort;
				$this->Adresse = $row->Adresse;
                $this->Koordinaten = $row->Koordinaten;
				$this->Kontakt = $row->Kontakt;
			}
			$this->Demo_ID = $Demo_ID;
		}
		
		/*
        Diese Funktion erstellt einen neuen Datenbankeintrag in der Demoliste.
        Alle Parameter für die neue Demo werden abgespeichert.
        Außerdem wird ein Objekt der Klasse Demo aufgerufen, welches für weitere Funktionen verwendet werden kann.
        Die Variable $link enthält alle Informationen, die zur Verbindung mit der Datenbank notwendig sind und ist in der Datei db_setup.php definiert.
		*/
		public static function new_Demo($Titel,$Schlagwort,$Kategorie,$Beschreibung,$Veranstaltende,$Beginn,$Ende,$PLZ,$Ort,$Adresse,$Koordinaten,$Kontakt){
			global $link;
			$query = "INSERT INTO `Demoliste`(`Titel`, `Schlagwort`, `Kategorie`,`Beschreibung`,`Veranstaltende`,`Beginn`,`Ende`,`PLZ`,`Ort`,`Adresse`,`Koordinaten`,`Kontakt`) VALUES ('$Titel','$Schlagwort','$Kategorie','$Beschreibung','$Veranstaltende','$Beginn','$Ende','$PLZ','$Ort','$Adresse','$Koordinaten','$Kontakt')";
			mysqli_query($link,$query);
			
			$Demo_ID = mysqli_insert_id($link);
			$instance = new self($Demo_ID);
			return $instance;
		}
			

        // "Aufruf"-Funktion - gibt den Titel des Demoobjektes zurück für das die Funktion aufgerufen wurde.
		public function get_Titel(){
			return $this->Titel;
		}

        // "Aufruf"-Funktion - gibt das Schlagwort des Demoobjektes zurück für das die Funktion aufgerufen wurde.
		public function getSchlagwort(){
			return $this->Schlagwort;
		}

        // "Aufruf"-Funktion - gibt die Kategorie des Demoobjektes zurück für das die Funktion aufgerufen wurde.
		public function getKategorie(){
			return $this->Kategorie;
		}

        // "Aufruf"-Funktion - gibt die Beschreibung des Demoobjektes zurück für das die Funktion aufgerufen wurde.
		public function getBeschreibung(){
			return $this->Beschreibung;
		}

        // "Aufruf"-Funktion - gibt die Veranstaltenden des Demoobjektes zurück für das die Funktion aufgerufen wurde.
		public function getVeranstaltende(){
			return $this->Veranstaltende;
		}

        // "Aufruf"-Funktion - gibt den Beginn des Demoobjektes zurück für das die Funktion aufgerufen wurde.
		public function getBeginn(){
			return $this->Beginn;
		}

        // "Aufruf"-Funktion - gibt das Ende des Demoobjektes zurück für das die Funktion aufgerufen wurde.
		public function getEnde(){
			return $this->Ende;
		}

        // "Aufruf"-Funktion - gibt die PLZ des Demoobjektes zurück für das die Funktion aufgerufen wurde.
		public function getPLZ(){
			return $this->PLZ;
		}

        // "Aufruf"-Funktion - gibt den Ort des Demoobjektes zurück für das die Funktion aufgerufen wurde.
		public function getOrt(){
			return $this->Ort;
		}

        // "Aufruf"-Funktion - gibt die Adresse des Demoobjektes zurück für das die Funktion aufgerufen wurde.
		public function getAdresse(){
			return $this->Adresse;
		}
        
        // "Aufruf"-Funktion - gibt die Koordinaten des Demoobjektes zurück für das die Funktion aufgerufen wurde.
		public function getKoordinaten(){
			return $this->Koordinaten;
		}

        // "Aufruf"-Funktion - gibt den Kontakt des Demoobjektes zurück für das die Funktion aufgerufen wurde.
		public function getKontakt(){
			return $this->Kontakt;
		}
		

		/*
		"Einstellungs"-Funktion
		Aktualisiert den Titel der Demo (die über die $Demo_ID identifiziert wird) für welche die Funktion aufgerufen wurde.
        Gibt den aktualisieren Titel zurück.
        Die Variable $link enthält alle Informationen, die zur Verbindung mit der Datenbank notwendig sind und ist in der Datei db_setup.php definiert.
		*/
		public function setTitel($var){
			global $link;
			$query = "UPDATE Demoliste SET Titel='$var' WHERE Demo_ID = $this->Demo_ID";
			mysqli_query($link,$query);
			return $this->Titel = $var;
        }

        /*
		"Einstellungs"-Funktion
		Aktualisiert den Titel das Schlagwort (die über die $Demo_ID identifiziert wird) für welche die Funktion aufgerufen wurde.
        Gibt den aktualisierten Titel zurück.
        Die Variable $link enthält alle Informationen, die zur Verbindung mit der Datenbank notwendig sind und ist in der Datei db_setup.php definiert.
		*/
		public function setSchlagwort($var){
			global $link;
			$query = "UPDATE Demoliste SET Schlagwort='$var' WHERE Demo_ID = $this->Demo_ID";
			mysqli_query($link,$query);
			return $this->Schlagwort = $var;
        }

        /*
		"Einstellungs"-Funktion
		Aktualisiert die Kategorie der Demo (die über die $Demo_ID identifiziert wird) für welche die Funktion aufgerufen wurde.
        Gibt die aktualisierte Kategorie zurück.
        Die Variable $link enthält alle Informationen, die zur Verbindung mit der Datenbank notwendig sind und ist in der Datei db_setup.php definiert.
		*/
		public function setKategorie($var){
			global $link;
			$query = "UPDATE Demoliste SET Kategorie='$var' WHERE Demo_ID = $this->Demo_ID";
			mysqli_query($link,$query);
			return $this->Kategorie = $var;
        }

        /*
		"Einstellungs"-Funktion
		Aktualisiert die Beschreibung der Demo (die über die $Demo_ID identifiziert wird) für welche die Funktion aufgerufen wurde.
        Gibt die aktualisierte Beschreibung zurück.
        Die Variable $link enthält alle Informationen, die zur Verbindung mit der Datenbank notwendig sind und ist in der Datei db_setup.php definiert.
		*/
		public function setBeschreibung($var){
			global $link;
			$query = "UPDATE Demoliste SET Beschreibung='$var' WHERE Demo_ID = $this->Demo_ID";
			mysqli_query($link,$query);
			return $this->Beschreibung = $var;
        }

        /*
		"Einstellungs"-Funktion
		Aktualisiert die Veranstaltenden der Demo (die über die $Demo_ID identifiziert wird) für welche die Funktion aufgerufen wurde.
        Gibt die aktualisierten Veranstaltenden zurück.
        Die Variable $link enthält alle Informationen, die zur Verbindung mit der Datenbank notwendig sind und ist in der Datei db_setup.php definiert.
		*/
		public function setVeranstaltende($var){
			global $link;
			$query = "UPDATE Demoliste SET Veranstaltende='$var' WHERE Demo_ID = $this->Demo_ID";
			mysqli_query($link,$query);
			return $this->Veranstaltende = $var;
        }

        /*
		"Einstellungs"-Funktion
		Aktualisiert den Beginn der Demo (die über die $Demo_ID identifiziert wird) für welche die Funktion aufgerufen wurde.
        Gibt den aktualisierten Beginn zurück.
        Die Variable $link enthält alle Informationen, die zur Verbindung mit der Datenbank notwendig sind und ist in der Datei db_setup.php definiert.
		*/
		public function setBeginn($var){
			global $link;
			$query = "UPDATE Demoliste SET Beginn='$var' WHERE Demo_ID = $this->Demo_ID";
			mysqli_query($link,$query);
			return $this->Beginn = $var;
        }

        /*
		"Einstellungs"-Funktion
		Aktualisiert das Ende der Demo (die über die $Demo_ID identifiziert wird) für welche die Funktion aufgerufen wurde.
        Gibt das aktualisierte Ende zurück.
        Die Variable $link enthält alle Informationen, die zur Verbindung mit der Datenbank notwendig sind und ist in der Datei db_setup.php definiert.
		*/
		public function setEnde($var){
			global $link;
			$query = "UPDATE Demoliste SET Ende='$var' WHERE Demo_ID = $this->Demo_ID";
			mysqli_query($link,$query);
			return $this->Ende = $var;
        }

        /*
		"Einstellungs"-Funktion
		Aktualisiert die PLZ der Demo (die über die $Demo_ID identifiziert wird) für welche die Funktion aufgerufen wurde.
        Gibt die aktualisierte PLZ zurück.
        Die Variable $link enthält alle Informationen, die zur Verbindung mit der Datenbank notwendig sind und ist in der Datei db_setup.php definiert.
		*/
		public function setPLZ($var){
			global $link;
			$query = "UPDATE Demoliste SET PLZ='$var' WHERE Demo_ID = $this->Demo_ID";
			mysqli_query($link,$query);
			return $this->PLZ = $var;
        }

        /*
		"Einstellungs"-Funktion
		Aktualisiert den Ort der Demo (die über die $Demo_ID identifiziert wird) für welche die Funktion aufgerufen wurde.
        Gibt den aktualisierten Ort zurück.
        Die Variable $link enthält alle Informationen, die zur Verbindung mit der Datenbank notwendig sind und ist in der Datei db_setup.php definiert.
		*/
		public function setOrt($var){
			global $link;
			$query = "UPDATE Demoliste SET Ort='$var' WHERE Demo_ID = $this->Demo_ID";
			mysqli_query($link,$query);
			return $this->Ort = $var;
        }

        /*
		"Einstellungs"-Funktion
		Aktualisiert die Adresse der Demo (die über die $Demo_ID identifiziert wird) für welche die Funktion aufgerufen wurde.
        Gibt die aktualisierte Adresse zurück.
        Die Variable $link enthält alle Informationen, die zur Verbindung mit der Datenbank notwendig sind und ist in der Datei db_setup.php definiert.
		*/
		public function setAdresse($var){
			global $link;
			$query = "UPDATE Demoliste SET Adresse='$var' WHERE Demo_ID = $this->Demo_ID";
			mysqli_query($link,$query);
			return $this->Adresse = $var;
        }

        /*
		"Einstellungs"-Funktion
		Aktualisiert die Koordinaten der Demo (die über die $Demo_ID identifiziert wird) für welche die Funktion aufgerufen wurde.
        Gibt die aktualisierten Koordinaten zurück.
        Die Variable $link enthält alle Informationen, die zur Verbindung mit der Datenbank notwendig sind und ist in der Datei db_setup.php definiert.
		*/
		public function setKoordinaten($var){
			global $link;
			$query = "UPDATE Demoliste SET Koordinaten='$var' WHERE Demo_ID = $this->Demo_ID";
			mysqli_query($link,$query);
			return $this->Koordinaten = $var;
        }

        /*
		"Einstellungs"-Funktion
		Aktualisiert den Kontakt der Veranstaltenden der Demo (die über die $Demo_ID identifiziert wird) für welche die Funktion aufgerufen wurde.
        Gibt den aktualisierten Kontakt zurück.
        Die Variable $link enthält alle Informationen, die zur Verbindung mit der Datenbank notwendig sind und ist in der Datei db_setup.php definiert.
		*/
		public function setKontakt($var){
			global $link;
			$query = "UPDATE Demoliste SET Kontakt='$var' WHERE Demo_ID = $this->Demo_ID";
			mysqli_query($link,$query);
			return $this->Kontakt = $var;
        }



    }
?>