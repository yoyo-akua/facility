<?php
include("html/header.html");
include("db_setup.php");


?>
<br><br><br>
<div class="container">
    <div class="py-5 text-center">
      <h2>Neuen Demo-Eintrag anlegen</h2>
      <p class="lead">Blablabla</p>
    </div>
      
      <div class="col-md-7 col-lg-8">
        <h4 class="mb-3">Allgemeine Info</h4>
        <form class="was-validated" novalidate="" action="new_demo.php" method="post">
          <div class="row g-3">
            <div class="col-12">
              <label for="titel" class="form-label">Titel</label>
              <input type="text" class="form-control" name="titel" placeholder="Titel" value="" required="">
              <div class="invalid-feedback">
                Bitte Titel der Demo eintragen
              </div>
            </div>

            <div class="col-12">
              <label for="schlagwort" class="form-label">Kurzbeschreibung <span class="text-muted">in 3 Worten</span></label>
                <input type="text" class="form-control" name="schlagwort" placeholder="Kurzbeschreibung" required="">
              <div class="invalid-feedback">
                  Bitte beschreibe das Thema der Demo kurz in 3 Worten.
                </div>
            </div>

            <div class="col-md-6">
              <label for="kategorie" class="form-label">1. Demothema</label>
              <select class="form-select" name="kategorie" required="">
                <option value="">Auswählen...</option>
                <option value="Umwelt-/Klimaschutz">Umwelt-/Klimaschutz</option>
                <option value="Anti-Rassismus">Anti-Rassismus</option>
                <option value="Feminismus">Feminismus</option>
                <option value="Anti-Kapitalismus">Anti-Kapitalismus</option>
                <option value="LGBTQI+">LGBTQI+</option>
                <option value="Frieden">Frieden</option>
                <option value="Sonstige">Sonstige</option>
              </select>
              <div class="invalid-feedback">
                Bitte Kategorie auswählen.
              </div>
            </div>
            <div class="col-md-6">
              <label for="kategorie2" class="form-label">2. Demothema <span class="text-muted">(Optional)</span></label>
              <select class="form-select" name="kategorie2">
                <option value="">Auswählen...</option>
                <option value="Umwelt-/Klimaschutz">Umwelt-/Klimaschutz</option>
                <option value="Anti-Rassismus">Anti-Rassismus</option>
                <option value="Feminismus">Feminismus</option>
                <option value="Anti-Kapitalismus">Anti-Kapitalismus</option>
                <option value="LGBTQI+">LGBTQI+</option>
                <option value="Frieden">Frieden</option>
              </select>
            </div>
        </div>
        <br><br>
            <h4 class="mb-3">Treffpunkt</h4>
            <div class="row g-3">

            <div class="col-md-3">
              <label for="plz" class="form-label">Postleitzahl</label>
              <input type="text" class="form-control" name="zip" placeholder="01234" required="">
              <div class="invalid-feedback">
                Bitte trage eine Postleitzahl ein.
              </div>
            </div>

            <div class="col-md-9">
              <label for="ort" class="form-label">Ort</label>
              <input type="text" class="form-control" name="ort" placeholder="Musterstadt" required="">
              <div class="invalid-feedback">
                Bitte trage einen Ort ein.
              </div>
            </div>

            <div class="col-12">
              <label for="adresse" class="form-label">Adresse <span class="text-muted">(Optional)</span></label>
              <input type="text" class="form-control" name="adresse" placeholder="Musterstraße 1">
            </div>

            <div class="col-12">
               <label for="koordinaten" class="form-label">Koordinaten <span class="text-muted">(Optional)</span></label>
               <input type="text" class="form-control" name="koordinaten" placeholder="00.0000000000000000, 00.000000000000000">
            </div>
          </div>

          <br><br>
          <h4 class="mb-3">Zeitpunkt</h4>
          <div class="row g-3">

             <div class="col-md-6">
              <label for="beginn" class="form-label">Beginn</label>
              <input type="datetime-local" class="form-control" name="beginn" required="">
              <div class="invalid-feedback">
                Bitte trage einen Tag und eine Uhrzeit ein.
              </div>
            </div>
            <div class="col-md-6">
              <label for="ende" class="form-label">Ende <span class="text-muted">(Optional)</span></label>
              <input type="datetime-local" class="form-control" name="ende">
            </div>
          </div>


          <br><br>
          <h4 class="mb-3" style="display:inline-block">Beschreibung</h4> <span class="text-muted">(Optional)</span>

             <div class="col-12">
              <textarea class="form-control" rows="7" placeholder="Platz für mehr Details zu Ablauf, Kundgebungen, Inhalten, Routenverlauf, etc."></textarea>
            </div>
            
          
            

            <br><br>
          <h4 class="mb-3">Kontakt</h4>
          <div class="row g-3">

             <div class="col-12">
              <label for="veranstaltende" class="form-label">Veranstaltende</label>
              <input type="text" class="form-control" name="veranstaltende" required="" placeholder="Musterorganisation">
              <div class="invalid-feedback">
                Bitte trage den Namen der verantwortlichen Organisation ein.
              </div>
            </div>
            
            <div class="col-12">
              <label for="kontakt" class="form-label">Kontakt</label>
              <div class="input-group has-validation">
                <span class="input-group-text">@</span>
                <input type="text" class="form-control" name="kontakt" placeholder="musterorganisation@mailbox.de" required="">
              <div class="invalid-feedback">
                Bitte trage eine Email-Adresse für eventuelle Rückfragen ein.
                </div>
              </div>
            </div>
          </div>
          <hr class="my-4">

          <small class="text-muted">
            Bitte beachten: <br>
            1. Es gibt (noch) keine "Bearbeiten"- oder "Löschen"-Funktion, deshalb kontrolliert bitte noch mal, ob ihr euch mit den Angaben sicher seid - sollte sich dennoch noch mal was ändern könnt ihr mich natürlich gern kontaktieren unter diesem <a href="???.de">Kontakt</a><br>
            2. Bitte keine Demoeinträge für rassistische/xenophobe/sexistische oder anderweitig menschenverachtende Kackscheiße. Versteht sich hoffentlich von selbst. #liebsein &lt3<br><br>
          </small>
          <button class="w-100 btn btn-primary btn-lg" type="submit">Bestätigen</button>
        </form>
      </div>
    </div>

 
</div>
  



<?php
include("html/footer.html");
?>