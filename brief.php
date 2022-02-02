<?php
    // config
    class Tranche {
        public $borneMin;
        public $borneMax;
        public $tarif;

        function __construct($bmin, $bmax, $tar){
            $this->borneMin = $bmin;
            $this->borneMax = $bmax;
            $this->tarif = $tar;
        }
    }
    $tva = 14;
    $timbre = 0.45;
    $redevance = [
        "small" => 22.65,
        "medium" => 37.05,
        "large" => 46.20
    ];
    //$redevance = array("small" => 22.65, "medium" => 37.05, "large" => 46.20);
    $tarifs = [
        new Tranche(0, 100, 0.794), 
        new Tranche(101, 150, 0.883),
        new Tranche(151, 210, 0.9451),
        new Tranche(211, 310, 1.0489),
        new Tranche(311, 510, 1.2915),
        new Tranche(511, null, 1.4975)
    ];

    $oldIndex;
    $newIndex;
    $calibre;
    $consommation;
    $numtranche;
    $somMT = 0;
    $montantsFacture = array(); // tableau où on va stocker les montants facturés
    $montantsHT = array(); // tableau où on va stocker les montants HT

    if (isset($_POST["submit"])) {
        $oldIndex = $_POST["oldIndex"];
        $newIndex = $_POST["newIndex"];
        $calibre = $_POST["calibre"];
        $consommation = $newIndex - $oldIndex;

        // $consommation <= 150

        if($consommation <= 150) {
            // $consommation <= 100
            if($consommation <= $tarifs[0]->borneMax) {
                $montantsFacture[0] = $consommation;
                $montantsHT[0] = $consommation * $tarifs[0]->tarif;
                $numtranche=1;
            }

            // 100 < $consommation <= 150
            else {
                $montantsFacture[0] = 100;
                $montantsFacture[1] = $consommation - $montantsFacture[0];
                $montantsHT[0] = $montantsFacture[0] * $tarifs[0]->tarif;
                $montantsHT[1] = $montantsFacture[1] * $tarifs[1]->tarif;
                $numtranche=2;
            }
        }

        // $consommation > 150
        else {
            if($consommation <= $tarifs[2]->borneMax) {
                $montantsFacture[0] = $consommation;
                $montantsHT[0] = $consommation * $tarifs[2]->tarif;
                $numtranche=3;
            }
            else if($consommation <= $tarifs[3]->borneMax) {
                $montantsFacture[0] = $consommation;
                $montantsHT[0] = $consommation * $tarifs[3]->tarif;
                $numtranche=4;
            }
            else if($consommation <= $tarifs[4]->borneMax) {
                $montantsFacture[0] = $consommation;
                $montantsHT[0] = $consommation * $tarifs[4]->tarif;
                $numtranche=5;
            }
            else{
                $montantsFacture[0] = $consommation;
                $montantsHT[0] = $consommation * $tarifs[5]->tarif;
                $numtranche=6;
            }
        }
    }
?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="brief 11.css">
    <title> Calcul facture</title>
</head>
<body>

<header>
    <nav>
        <img id="img" src="BG_bleu-electricite.jpg" alt="background">
        <img src="Screenshot_2022-02-01_104845-removebg-preview.png" alt="logo" id="logo">

            <form action="brief.php" method="POST">

                <div id="div1">


                    <input type="text" id="inp1" name="oldIndex" placeholder="Old index">

                    <input type="text" id="inp2" name="newIndex" placeholder="New index">
                    
                    <div id="select1">

                        <input type="radio" name="calibre"  value="22.65">
                        <label for="mgn">5-10</label>

                        <input type="radio" name="calibre"  value="37.05">
                        <label for="mgn">15-20</label>

                        <input type="radio" name="calibre"  value="46.20">
                        <label for="mgn">>30</label>

                    </div>

                    <button type="submit" value="calcul" id="btn" name="submit">Calculer</button>
                </div>

            </form>
    </nav>
</header>

<main>
    <table>
        <tr>
            <th scope="col"></th>
            <th scope="col">Facturé</th>
            <th scope="col">P.U</th>
            <th scope="col">Montant HT</th>
            <th scope="col">Taux TVA</th>
            <th scope="col">Montant Taxes</th>
        </tr>
        <tr>
            <th scope="row">Consommation electricite</th>
            <?php
        if (isset($_POST["submit"])) {
            foreach($montantsFacture as $key => $value) {

        ?>
        <tr>
            <td>Tranche <?php if($numtranche>=3) 
            { echo $numtranche;
            } 
            else { echo $key+1; }
            ?>
            </td>
            <td><?php echo $value ?></td>
            <td><?php echo $tarifs[$key]->tarif ?></td>
            <td><?php echo $montantsHT[$key] ?></td>
            <td><?php echo $tva . "%";?></td>
            <td><?php echo $montantsHT[$key] * $tva /100 ?></td>
        </tr>
        
        <?php
            }
        }
        
        ?>

        </tr>
        <tr>
            <th scope="row">Redevance Fixe Electicite</th>
            <td></td>
            <td></td>
            <td><?php echo $calibre?></td>
            <td><?php echo $tva . "%"?></td>
            <td><?php echo $calibre * $tva /100 ?></td>
        </tr>
        <tr>
            <th scope="row">Taxes pour le compte d'Etat (Total TVA + Timbre)</th>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><?php echo $somMT ?></td>

        </tr>
        <tr>
            <th scope="row">Sous Total</th>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <th scope="row">TOTAL ELECTRICITE</th>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>
</body>
</html>