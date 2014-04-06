<?php $rd = strlen($rd) ? $rd : '.'; ?> 
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="<?php echo $rd; ?>/resources/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo $rd; ?>/resources/css/bootstrap-responsive.min.css">
        <link rel="stylesheet" href="<?php echo $rd; ?>/resources/css/styles.css">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <title>RackNews</title>
    </head>
    <body>
        <header class="navbar navbar-inverse navbar-fixed-top" id="navbar">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="https://github.com/axocomm/racknews">RackNews</a>
                </div>
                <nav class="navbar-collapse collapse" role="navigation">
                    <ul class="nav navbar-nav">
                        <li id="home"><a href="<?php echo $rd; ?>">Home</a></li>
                        <li id="documentation"><a href="<?php echo $rd; ?>/doc">Documentation</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        <div class="container" id="main">
