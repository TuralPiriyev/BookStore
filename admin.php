<?php
   require_once "db.php";
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel</title>
<link rel="stylesheet" href="CSS/admin.css"/>
</head>
<body>

<div class="navbar">Admin Panel</div>

<div class="choose">
    <div id="users-btn">Users</div>
    <div id="books-btn">Books</div>
</div>

<?php
  if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['role']))
  {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $sql = "Insert into login(username, password, role) values('$username','$password','$role');";
    $result = mysqli_query($conn, $sql);
  }
?>
<div class="section" id="users-section">
    <h2>Add User</h2>
    <form action="" method="POST">
        <input type="text" placeholder="Username" name="username" required>
        <input type="password" placeholder="Password" name="password" required>
        <select name="role" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit">Add User</button>
    </form>

    <h3>Users List</h3>
    <table>
        <tr>
            <th>Username</th>
            <th>Password</th>
            <th>Role</th>
        </tr>
<?php
$sql = "SELECT * FROM login";
$result = mysqli_query($conn, $sql);

while($users = mysqli_fetch_assoc($result)) {
?>
<tr>
    <td><?php echo $users['username']; ?></td>
    <td><?php echo $users['password']; ?></td>
    <td><?php echo $users['role']; ?></td>
    <td>

        <form method="POST" action="edit_user.php" style="display:inline;">
            <input type="hidden" name="edit_id" value="<?php echo $users['Id']; ?>">
            <button type="submit">Edit</button>
        </form>


        <form method="POST" action="" style="display:inline;">
            <input type="hidden" name="delete_id" value="<?php echo $users['Id']; ?>">
            <button type="submit" name="delete_user">Delete</button>
        </form>
    </td>
</tr>
<?php }  ?>

<?php
   if(isset($_POST["delete_user"]))
   {
    $delete_id = mysqli_real_escape_string($conn, $_POST['delete_id']);
    $delete_sql = "Delete from login where Id = '$delete_id'";

    if(mysqli_query($conn, $delete_sql))
    {
        echo "User deleted successfully!";
    }
    else{echo "Error deleting user:" . mysqli_error($conn);}
   }
  ?>
    </table>
</div>
 <?php
   if(isset($_POST['title']) && isset($_POST['author']) && isset($_POST['pages']) && isset($_POST['price'])
    && isset($_POST['stock']) && isset($_POST['description']) && isset($_POST['image']) && isset($_POST['author']) && isset($_POST['BookId']) && isset($_POST['AuthorId']))
   {
    $bookId = mysqli_real_escape_string($conn, $_POST['BookId']);
    $authorId = mysqli_real_escape_string($conn, $_POST['AuthorId']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $pages = mysqli_real_escape_string($conn, $_POST['pages']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $image = mysqli_real_escape_string($conn, $_POST['image']);

    $sql = "insert into books ('title', 'pages', 'price', 'stock', 'description', 'cover_image') values ('$title', '$pages', '$price', '$stock', '$description', '$image');
            insert into authors('Name') values('$author');
            insert into book_authors('book_id', 'author_id') values('$bookId', '$authorId');";
    $result = mysqli_query($conn, $sql);
   }
 ?>
<div class="section" id="books-section">
    <h2>Add Book</h2>
    <form action="" method="POST">
        <input type="hidden" placeholder = "Book Id" name = "BookId" required>
        <input type="hidden" placeholder = "Author Id" name = "AuthorId" required>
        <input type="text" placeholder="Title" name="title" required>
        <input type="text" placeholder="Author" name="author" required>
        <input type="number" placeholder="Pages" name="pages" required>
        <input type="number" placeholder="Price" name="price" required>
        <input type="number" placeholder="Stock" name="stock" required>
        <input type="text" placeholder="Description" name="description" required>
        <input type="text" placeholder="Image Path" name="image" required>
        <button type="submit">Add Book</button>
    </form>

    <h3>Books List</h3>
    <table>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Pages</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Description</th>
            <th>Image</th>
        </tr>
         <?php 
          $sql = "SELECT 
            b.*, 
            a.name AS author_name
        FROM 
            books b
        JOIN 
            book_authors ba ON b.id = ba.book_id
        JOIN 
            authors a ON ba.author_id = a.id";
          $result = mysqli_query($conn,$sql);
          while($books = mysqli_fetch_assoc($result))
          {
        ?>
         <tr>
            <td><?php echo $books['title'] ?></td>
            <td><?php echo $books['author_name'] ?></td>
            <td><?php echo $books['pages'] ?></td>
            <td><?php echo $books['price'] ?></td>
            <td><?php echo $books['stock'] ?></td>
            <td><?php echo $books['description'] ?></td>
            <td><?php echo $books['cover_image'] ?></td>
             <td>

        <form method="POST" action="" style="display:inline;">
            <input type="hidden" name="edit_id" value="<?php echo $books['Id']; ?>">
            <button type="submit">Edit</button>
        </form>


        <form method="POST" action="" style="display:inline;">
            <input type="hidden" name="delete_id" value="<?php echo $books['Id']; ?>">
            <button type="submit" name="delete_user">Delete</button>
        </form>
    </td>
         </tr>
        <?php } ?>
    </table>
</div>

<script>
document.getElementById("books-section").style.display = "block";

document.getElementById("users-btn").addEventListener("click", function() {
    document.getElementById("users-section").style.display = "block";
    document.getElementById("books-section").style.display = "none";
});

document.getElementById("books-btn").addEventListener("click", function() {
    document.getElementById("books-section").style.display = "block";
    document.getElementById("users-section").style.display = "none";
});
</script>

</body>
</html>
