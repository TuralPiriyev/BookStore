 <?php 
        $sql = 'Select title, price, cover_image from books';
        $result = mysqli_query($conn, $sql);
        $books = mysqli_fetch_all($result, MYSQLI_ASSOC);
        ?>

<div class="all-books-cont">
    <div class="title-all-books">
        <h1>All Books</h1>
    </div>
    <div class="result-sorting">
        <?php
         $result = mysqli_query($conn, "Select Count(id) from Books");
         $row = mysqli_fetch_assoc($result);
        ?>
        <div class = "same-pos"><h5 class = "result-books">Showing all <?php echo $row['Count(id)'];?>  results</h5></div>
        <div class = "same-pos2 "><input class = "search-book-inp" type="text" placeholder="Please Enter Book's or Author's name"><button class = "search-button">Search</button>
        </div>
    </div>
    <div class="books">
        <?php foreach ($books as $book): ?>
        <div class="book-item">
        <img src="<?php echo $book['cover_image']; ?>" alt="">
        <span class = "title-book"><?php echo $book['title']; ?></span></br></br>
        <span class = "price-book">$<?php echo $book['price']; ?></span>
       </div>
     <?php endforeach; ?>
    </div>
</div>