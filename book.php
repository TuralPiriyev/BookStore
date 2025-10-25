<?php
require_once "db.php";
include "header.php";

if(isset($_GET['title']))
{
    $title = mysqli_real_escape_string($conn, $_GET['title']);
    $sql = " SELECT 
            b.*, 
            a.name AS author_name
        FROM 
            books b
        JOIN 
            book_authors ba ON b.id = ba.book_id
        JOIN 
            authors a ON ba.author_id = a.id
        WHERE 
            b.title = '$title'
            ";
    $result = mysqli_query($conn, $sql);
    if($book = mysqli_fetch_assoc($result))
    {
     ?>
       <div class="allcont">
       <div class="self-book">
          <div class="image">
            <img src="<?php echo $book['cover_image']?>" alt="">
          </div>
          <div class="book-info">
             <div class = "info-cont">
               <h1 class = "title">"<?php echo $book['title']?>"</h1>
               <span class = "price">$<?php echo $book['price']?></span>
               <hr/>
               <span class = "author2">Author:</span>
               <span class = "author"><?php echo $book['author_name']?></span><br/><br/>
               <span class = "desc2">Description:</span>
               <span class = "description"><?php echo $book['description']?></span>
              </div> 
          </div>
       </div>
</div>
     <?php  
    } 
    else echo "Kitab Tapilmadi!";
}
include "footer.php";
mysqli_close($conn);
?>