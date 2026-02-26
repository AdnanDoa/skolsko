<?php 
         if(isset($_POST["dodaj"])) { 
            $dbhost = "localhost"; 
            $dbuser = "root"; 
            $dbpass =""; 
            $database="edukacija"; 
            $conn = new mysqli($dbhost,$dbuser,$dbpass,$database);               
 
            $ime = $_POST['ime']; 
            $prezime=$_POST['prezime']; 
            $adresa = $_POST['adresa'];             
            $datum = $_POST['datum']; 
            //$slika=$_POST['slika']; 
            //$sql = "INSERT INTO studenti ". "(ime,prezime,datum_prijave, adresa, slika) ". 
"VALUES('$ime','$prezime', '$datum', '$adresa','$imgContent')"; 
 
            $status = $statusMsg = ''; 
 
    $status = 'error';  
    if(!empty($_FILES["slika"]["name"])) {  
        // Get file info  
        $fileName = basename($_FILES["slika"]["name"]);  
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION);  
          
        // formati slika koji su podržani 
        $allowTypes = array('jpg','png','jpeg','gif','webp'); // niz u php-u 
        if(in_array($fileType, $allowTypes)){  
            $image = $_FILES['slika']['tmp_name'];  
            $imgContent = addslashes(file_get_contents($image));  
            $sql="INSERT INTO studenti ". "(ime,prezime,datum_prijave, adresa, slika) ". 
"VALUES('$ime','$prezime', '$datum', '$adresa','$imgContent')"; 
            // Unos podataka u polja tabele studenti 
            //Za unos vremena može se koristiti NOW() 
            $insert=$conn->query($sql); 
              
            if($insert){  
                $status = 'success';  
                $statusMsg = "Fajl je uspješno dodan.";  
            }else{  
                $statusMsg = "Fajl nije dodan. Pokušajte ponovo.";  
            }   
        }else{  
            $statusMsg = 'Samo JPG, JPEG, PNG, GIF i webp fajlovi su dozvoljeni za upload.';  
        }  
    }else{  
        $statusMsg = 'Odaberi sliku za upload.';  
    }                 
            if ($conn->errno) {                
               header("Location: index.html"); 
               echo '<script type="text/javascript">alert("Podaci nisu ispravno 
uneseni.");</script>';              
            } 
         
            header("Location: prikaz.html");  
            $conn->close(); //Zatvara se komunikacija sa bazom podataka 
             
         } 
      ?> 