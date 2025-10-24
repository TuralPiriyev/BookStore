<?php
   require_once "db.php";
   include "header.php";
   include "Pages/all-books.php";
   include "footer.php";


   mysqli_close($conn);
?>