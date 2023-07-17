<?php 

 // database details
 $host = "127.0.0.1";
 $username = "root";
 $password = "";
 $dbname = "fahrradverleih";

 // creating a connection
 $con = mysqli_connect($host, $username, $password, $dbname);

 // to ensure that the connection is made
 if (!$con)
 {
     die("Connection failed!" . mysqli_connect_error());
 }

// using sql to get all data of Fahrrad and Hersteller
$sqlAllBikes = "SELECT fahrrad.FAHRRADbezeichnung, fahrrad.FAHRRADhertelljahr, fahrrad.FAHRRADbid, fahrrad.FAHRRADfarbe, hersteller.HERSTELLERname, hersteller.HERSTELLERvorname 
        from fahrrad
        INNER JOIN hersteller ON fahrrad.HERSTELLER_idHERSTELLER=hersteller.idHERSTELLER;";



//using sql to get all bookings with Customer
$sqlAllBookings = "SELECT kunde.KUNDEname, kunde.KUNDEvorname, kunde.KUNDEnummer, fahrrad.FAHRRADbezeichnung, fahrrad.FAHRRADbid, verleih.VERLEIHvon, verleih.VERLEIHbis FROM verleih
                   INNER JOIN kunde On verleih.KUNDE_idKUNDE = kunde.idKUNDE
                   INNER JOIN fahrrad ON verleih.FAHRRAD_idFAHRRAD = fahrrad.idFAHRRAD;";



// Suchlogik
if (isset($_POST['submit'])) {
    $suchbegriff = $_POST['suchbegriff'];

    $sqlSearch = "SELECT kunde.KUNDEname, kunde.KUNDEvorname, kunde.KUNDEnummer, fahrrad.FAHRRADbezeichnung, fahrrad.FAHRRADbid, verleih.VERLEIHvon, verleih.VERLEIHbis FROM verleih
            INNER JOIN kunde ON verleih.KUNDE_idKUNDE = kunde.idKUNDE
            INNER JOIN fahrrad ON verleih.FAHRRAD_idFAHRRAD = fahrrad.idFAHRRAD
            WHERE kunde.KUNDEname LIKE ? OR kunde.KUNDEvorname LIKE ? OR kunde.KUNDEnummer LIKE ? OR fahrrad.FAHRRADbezeichnung LIKE ? OR verleih.VERLEIHvon LIKE ?";
            
    $stmt = $con->prepare($sqlSearch);
    $param = "%$suchbegriff%";
    $stmt->bind_param("sssss", $param, $param, $param, $param, $param);
    $stmt->execute();
    
    $resultSearch = $stmt->get_result();
} else {
    // Standardabfrage, um alle Buchungen mit Informationen zu erhalten
    $sqlSearch = "SELECT kunde.KUNDEname, kunde.KUNDEvorname, kunde.KUNDEnummer, fahrrad.FAHRRADbezeichnung, fahrrad.FAHRRADbid, verleih.VERLEIHvon, verleih.VERLEIHbis FROM verleih
            INNER JOIN kunde ON verleih.KUNDE_idKUNDE = kunde.idKUNDE
            INNER JOIN fahrrad ON verleih.FAHRRAD_idFAHRRAD = fahrrad.idFAHRRAD";
            
    $resultSearch = $con->query($sqlSearch);
}

//execute Query
$resultAllBikes = $con->query($sqlAllBikes);
$resultAllBookings = $con->query($sqlAllBookings)

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <title>Document</title>
</head>
<body>
    <div class="m-3">
        <div class="text-center mt-5 mb-5">
            <h1>LAP TEST Arbeit</h1>
        </div>
        <div class="container text-center">
            <div class="row align-items-center">
            <form method="POST" action="index.php">
            <div class="col-3">
                Suchbegriff: <input type="text" name="suchbegriff" class="form-control">
                </div>
                <div class="col-3">
                <input type="submit" name="submit" value="Suchen" class="btn btn-primary">
                </div>
            </form>
               
            </div>
        </div>
        <div class="mt-5">
            <h1 class="">Result:</h1>
            <table class="table table-responsive">
                <thead>
                    <tr>
                    <th scope="col">Kunden Name</th>
                    <th scope="col">Kunden Vorname</th>
                    <th scope="col">Kunden Nummer</th>
                    <th scope="col">Bezeichnung</th>
                    <th scope="col">B-ID</th>
                    <th scope="col">Von</th>
                    <th scope="col">Bis</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Put Result in Table -->
                    <?php while ($row = mysqli_fetch_assoc($resultSearch)) { ?>
                        <tr>
                            <td><?php echo $row['KUNDEname']; ?></td>
                            <td><?php echo $row['KUNDEvorname']; ?></td>
                            <td><?php echo $row['KUNDEnummer']; ?></td>
                            <td><?php echo $row['FAHRRADbezeichnung']; ?></td>
                            <td><?php echo $row['FAHRRADbid']; ?></td>
                            <td><?php echo $row['VERLEIHvon']; ?></td>
                            <td><?php echo $row['VERLEIHbis']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div>
            <h1 class="">ALL BIKES</h1>
            <table class="table table-responsive">
                <thead>
                    <tr>
                    <th scope="col">Bezeichnung</th>
                    <th scope="col">Baujahr</th>
                    <th scope="col">B-ID</th>
                    <th scope="col">Farbe</th>
                    <th scope="col">Herteller Name</th>
                    <th scope="col">Herteller Vorname</th> 
                    </tr>
                </thead>
                <tbody>
                    <!-- Put Result in Table -->
                    <?php while ($row = mysqli_fetch_assoc($resultAllBikes)) { ?>
                        <tr>
                            <td><?php echo $row['FAHRRADbezeichnung']; ?></td>
                            <td><?php echo $row['FAHRRADhertelljahr']; ?></td>
                            <td><?php echo $row['FAHRRADbid']; ?></td>
                            <td><?php echo $row['FAHRRADfarbe']; ?></td>
                            <td><?php echo $row['HERSTELLERname']; ?></td>
                            <td><?php echo $row['HERSTELLERvorname']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="mt-5">
            <h1 class="">ALL BOOKINGS</h1>
            <table class="table table-responsive">
                <thead>
                    <tr>
                    <th scope="col">Kunden Name</th>
                    <th scope="col">Kunden Vorname</th>
                    <th scope="col">Kunden Nummer</th>
                    <th scope="col">Bezeichnung</th>
                    <th scope="col">B-ID</th>
                    <th scope="col">Von</th>
                    <th scope="col">Bis</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Put Result in Table -->
                    <?php while ($row = mysqli_fetch_assoc($resultAllBookings)) { ?>
                        <tr>
                            <td><?php echo $row['KUNDEname']; ?></td>
                            <td><?php echo $row['KUNDEvorname']; ?></td>
                            <td><?php echo $row['KUNDEnummer']; ?></td>
                            <td><?php echo $row['FAHRRADbezeichnung']; ?></td>
                            <td><?php echo $row['FAHRRADbid']; ?></td>
                            <td><?php echo $row['VERLEIHvon']; ?></td>
                            <td><?php echo $row['VERLEIHbis']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    
</body>
</html>