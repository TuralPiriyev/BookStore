 <?php 
       if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);

    // Hem kitabin, hem de authorun adına gore axtarış
    $sql = "
        SELECT DISTINCT b.title, b.price, b.cover_image
        FROM books b
        JOIN book_authors ba ON b.id = ba.book_id
        JOIN authors a ON a.id = ba.author_id
        WHERE b.title LIKE '%$search%' OR a.name LIKE '%$search%'
    ";
} 
elseif(isset($_POST['Clean']))
{
    $sql = "SELECT title, price, cover_image FROM books";
}
  
else {
    // Əgər axtarış boşdursa, bütün kitabları göstər
    $sql = "SELECT title, price, cover_image FROM books";
}
  $result = mysqli_query($conn, $sql);
  $books = mysqli_fetch_all($result, MYSQLI_ASSOC);
        ?>

<div class="all-books-cont">
    <div class="title-all-books">
        <h1>All Books</h1>
    </div>
    <div class="result-sorting">
        <?php
         $bookCount = count($books);
        ?>
        <div class = "same-pos"><h5 class = "result-books">Showing all <?php echo $bookCount;?>  results</h5></div>
        <div class = "same-pos2 ">
            <form method="GET"> 
            <input class = "search-book-inp" type="text" name = "search" placeholder="Please Enter Book's or Author's name" >
            <button class = "search-button" type = "submit">Search</button>
            <button class = "search-button" type = "submit" name = "clean">Clean</button>
            </form>
        </div>
    </div>
    <div class="books">
        <?php foreach ($books as $book): ?>
        <a  href="book.php?title=<?php echo urlencode($book['title']); ?>">
         <div class="book-item">
            <img src="<?php echo $book['cover_image']; ?>" alt="">
            <span class = "title-book"><?php echo $book['title']; ?></span></br></br>
            <span class = "price-book">$<?php echo $book['price']; ?></span>
         </div>
        </a>
     <?php endforeach; ?>
    </div>
</div>
