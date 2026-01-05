<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Log in</title>

        <!-- Favicon -->
        <link rel="shortcut icon" href="<?=base_url()?>public/images/icon.ico">
        <!-- favicon ends --->
        
        <!--- LOAD FILES -->
        <?php if($_SERVER['HTTP_HOST'] == "localhost" || (stristr($_SERVER['HTTP_HOST'], "192.168.") !== FALSE)|| (stristr($_SERVER['HTTP_HOST'], "127.0.0.") !== FALSE)): ?>
        <link rel="stylesheet" href="<?=base_url()?>public/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?=base_url()?>public/font-awesome/css/font-awesome.min.css">

        <script src="<?=base_url()?>public/js/jquery.min.js"></script>
        <script src="<?=base_url()?>public/bootstrap/js/bootstrap.min.js"></script>

        <?php else: ?>
        
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <?php endif; ?> 
        
        <!-- CSS -->
        <link rel="stylesheet" href="<?=base_url()?>public/css/form-elements.css">
        <link rel="stylesheet" href="<?=base_url()?>public/css/style.css">
        <link rel="stylesheet" href="<?=base_url()?>public/css/main.css">

        <style>
            /* Mobile Icon Animation */
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
            }
            
            .mobile-icon-container {
                animation: float 3s ease-in-out infinite;
            }
            
            /* Input Group Styling */
            .input-group-addon {
                background-color: #f8f9fa;
                border: 1px solid #ddd;
                color: #5a5a5a;
                min-width: 45px;
            }
            
            .input-group .form-control {
                border-left: none;
            }
            
            .input-group .form-control:focus {
                border-color: #5fcf80;
                box-shadow: none;
            }
            
            /* Button Enhancement */
            .form-bottom .btn {
                transition: all 0.3s ease;
            }
            
            .form-bottom .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            }
            
            /* Title Animation */
            .title-text {
                animation: fadeInDown 1s ease-out;
            }
            
            @keyframes fadeInDown {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>

    <body>

        <!-- Top content -->
        <div class="top-content">

            <div class="inner-bg">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-8 col-sm-offset-2 text">
                            <div style="text-align: center; margin-bottom: 30px;">
                                <div class="mobile-icon-container" style="display: inline-block; background: rgba(255,255,255,0.1); padding: 20px; border-radius: 50%; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                    <i class="fa fa-mobile" style="font-size: 80px; color: white;"></i>
                                </div>
                                <h1 class="title-text" style="color: white; font-size: 48px; font-weight: 300; margin: 0;">
                                    <i class="fa fa-mobile" style="font-size: 42px; margin-right: 15px;"></i>
                                    Mobile Shop POS
                                </h1>
                                <p style="color: rgba(255,255,255,0.8); font-size: 18px; margin-top: 10px;">
                                    Point of Sale System
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-3">
                            <div class="bg-primary text-center">
                                <span id="errMsg"></span>
                            </div>
                            <div class="form-bottom">
                                <form id="loginForm">
                                    <div class="form-group">
                                        <label class="sr-only" for="email">E-mail</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                            <input type="email" placeholder="Email" class="form-control checkField" id="email" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="sr-only" for="password">Password</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                            <input type="password" placeholder="Password" class="form-control checkField" id="password" >
                                        </div>
                                    </div>
                                    <button type="submit" class="btn">
                                        <i class="fa fa-sign-in"></i> Log in!
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    

                </div>
            </div>

        </div>


        <!-- Javascript -->
        <script src="<?=base_url()?>public/js/main.js?v=<?=time()?>"></script>
        <script src="<?=base_url()?>public/js/access.js?v=<?=time()?>"></script>
        <script src="<?=base_url()?>public/js/jquery.backstretch.min.js"></script>
        <!--Javascript--->

    </body>

</html>