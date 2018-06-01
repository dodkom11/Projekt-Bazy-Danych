<?php
    session_start();
    
   
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>goFISHINGshop</title>
        <!-- Bootstrap core CSS -->
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <!-- Custom styles for this template -->
        <link href="css/simple-sidebar.css" rel="stylesheet">
        <link href="css/mycss.css" rel="stylesheet">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
    </head>
    <body class="bg-dark">
         <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
            <div class="container">
                <a class="navbar-brand" href="index.php"><i class="fas fa-hands-helping"></i>&nbsp;&nbsp;goFISHINGshop</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php"><i class="fas fa-home"></i>&nbsp;&nbsp;Strona Główna                                
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="zalogujsie.php"><i class="fas fa-sign-in-alt"></i>&nbsp;&nbsp;Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="reg.php"><i class="fa fa-user"></i>&nbsp;&nbsp;Rejestracja</a>
                            <span class="sr-only">(current)</span>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container" id="wrapper">
            <div class="card card-login mx-auto mt-5">
                <div class="card-header">Rejestracja</div>
                <div class="card-body">
                    <form action="logikaphp/rejestracja.php" method="post">
                       <div class="form-group">
                            <label>Imię</label>
                            <input required class="form-control" id="imie"  name="imie" type="text" placeholder="Imię" 
                            <?php
                            if (isset($_SESSION['imie'])){  
                                $imie = $_SESSION['imie'];   
                                echo "value = $imie";
                                unset($_SESSION['imie']); 
                                 }
                          ?>>
                        </div>
                       <div class="form-group">
                            <label>Nazwisko</label>
                            <input required class="form-control" id="nazwisko"  name="nazwisko" type="text" placeholder="Nazwisko"
                            <?php
                            if (isset($_SESSION['nazwisko'])){  
                                $nazwisko = $_SESSION['nazwisko'];   
                                echo "value = $nazwisko";
                                unset($_SESSION['nazwisko']); 
                                 }
                          ?>>
                        </div>

                        <div class="form-group">
                            <label>E-mail</label>   
                                                
                            <input required class="form-control" id="email" type="email" name="email" placeholder="E-mail"
                            <?php
                            if (isset($_SESSION['email'])){  
                                $email = $_SESSION['email'];   
                                echo "value = $email";
                                unset($_SESSION['email']); 
                                 }
                          ?>>
                        </div>


                       <div class="form-group">
                            <label>Nazwa użytkownika</label>
                            <input required class="form-control" id="login" type="text" name="login" placeholder="Nazwa użytkownika"
                            <?php
                            if (isset($_SESSION['login'])){  
                                $login = $_SESSION['login'];   
                                echo "value = $login";
                                unset($_SESSION['login']); 
                                 }
                          ?>>
                        </div>
                       <div class="form-group">
                            <label>Hasło</label>
                            <input required class="form-control" id="password"  name="password" type="password" placeholder="Hasło">
                        </div>
                       <div class="form-group">
                            <label>Potwierdź hasło</label>
                            <input required class="form-control" id="password2"  name="password2" type="password" placeholder="Potwierdź hasło">
                        
                        

                        </div>
                       <div class="form-group">
                            <label>Województwo</label>
                            <input required class="form-control" id="woj" type="text" name="woj" placeholder="Województwo"
                            <?php
                            if (isset($_SESSION['woj'])){  
                                $woj = $_SESSION['woj'];   
                                echo "value = $woj";
                                unset($_SESSION['woj']); 
                                 }
                          ?>>
                        </div>

                        
                       <div class="form-group">
                            <label>Miejscowosc</label>
                            <input required class="form-control" id="miejsc" type="text" name="miejsc" placeholder="Miejscowosc"
                            <?php
                            if (isset($_SESSION['miejsc'])){  
                                $miejsc = $_SESSION['miejsc'];   
                                echo "value = $miejsc";
                                unset($_SESSION['miejsc']); 
                                 }
                          ?>>
                        </div>

                        
                       <div class="form-group">
                            <label>Kod pocztowy</label>
                            <input required class="form-control" id="poczt" type="text" name="poczt" placeholder="Kod pocztowy"
                            <?php
                            if (isset($_SESSION['poczt'])){  
                                $poczt = $_SESSION['poczt'];   
                                echo "value = $poczt";
                                unset($_SESSION['poczt']); 
                                 }
                          ?>>
                        </div>

                        
                       <div class="form-group">
                            <label>Ulica</label>
                            <input required class="form-control" id="ulica" type="text" name="ulica" placeholder="Ulica"
                            <?php
                            if (isset($_SESSION['ulica'])){  
                                $ulica = $_SESSION['ulica'];   
                                echo "value = $ulica";
                                unset($_SESSION['ulica']); 
                                 }
                          ?>>
                        </div>


                       <div class="form-group">
                            <label>Nr domu</label>
                            <input required class="form-control" id="nr_domu" type="number" name="nr_domu" placeholder="Nr domu"
                            <?php
                            if (isset($_SESSION['nr_domu'])){  
                                $nr_domu = $_SESSION['nr_domu'];   
                                echo "value = $nr_domu";
                                unset($_SESSION['nr_domu']); 
                                 }
                          ?>>
                        </div>

                        <div class="form-group">
                            <label>Nr telefonu</label>
                            <input required class="form-control" id="nr_tel" type="number" name="nr_tel" placeholder="Nr telefonu"
                            <?php
                            if (isset($_SESSION['nr_tel'])){  
                                $nr_tel = $_SESSION['nr_tel'];   
                                echo "value = $nr_tel";
                                unset($_SESSION['nr_tel']); 
                                 }
                          ?>>
                        </div>



                       
                        <input type="submit" class="btn btn-primary btn-block" value="Utwórz konto" name="rejestruj"/>
                      
                    </form>
                     </div>
                   <?php

                        if(isset($_SESSION['error_code'])){  

                            if($_SESSION['error_code'] == 20001){
                                echo '<p>Wypełnij wszystkie pola!</p>' ;
                            }
                            if($_SESSION['error_code'] == 20002){
                                echo '<p>Hasla sie od siebie roznia</p>' ;
                            }
                            if($_SESSION['error_code'] == 20003){
                                echo '<p>Haslo zbyt krotkie (mniej niz 7 znakow)</p>' ;
                            }
                            if($_SESSION['error_code'] == 20004){
                                echo '<p>Haslo zbyt latwe</p>' ;
                            }
                            if($_SESSION['error_code'] == 20005){
                                echo '<p>Haslo powinno miec przynajmniej jedna litere, jedna cyfre i znak przestankowy</p>' ;
                            }
                            if($_SESSION['error_code'] == 20006){
                                echo '<p>Login jest zajęty, proszę wybierz inny</p>' ;
                            }
                            
                                
                            unset($_SESSION['error_code']);
                        } 
                        
                    ?>

                </div>
                </div>
               
            </div>
        </div>
        
        <!-- /#wrapper -->
        <!-- Bootstrap core JavaScript -->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="script/toogle.js"></script>
        <script src="script/showAndHide.js"></script>
    </body>
</html>

