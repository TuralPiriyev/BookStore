<?php
   require_once "db.php";

    session_start();
    if(isset($_SESSION['message']))
    {
        echo "<script> alert('". $_SESSION['message'] ."');</script>";
        unset($_SESSION['message']);
    }

   if($_SERVER['REQUEST_METHOD'] === 'POST')
   {
    $act = $_POST['action'] ;


  //istifadeci elave
    if($act === 'add_user' && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['role']))
  {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $sql = "Insert into login(username, password, role) values('$username','$password','$role');";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_query($conn, $sql))
    {
        $_SESSION['message'] = "$username-$role added successfully";
        header('Location: admin.php?tab=users#users');
        exit;
    }
    else{echo "Error added user:" . mysqli_error($conn);}
  }

// istifadeci silme 
   if($act === 'delete_user' && isset($_POST["delete_user"]))
   {
    $delete_id = mysqli_real_escape_string($conn, $_POST['delete_id']);
    $delete_sql = "Delete from login where Id = '$delete_id'";

    if(mysqli_query($conn, $delete_sql))
    {
        $_SESSION['message'] = "User deleted successfully";
        header('Location: admin.php?tab=users#users');
        exit;
    }
    else{echo "Error deleting user:" . mysqli_error($conn);}

   }

   //istifadeci editlemek
   if($act === "edit_user" && isset($_POST['edit_id']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['role'])) 
   {
     $username = mysqli_real_escape_string($conn, $_POST['username']);
     $password = mysqli_real_escape_string($conn, $_POST['password']);
     $role = mysqli_real_escape_string($conn, $_POST['role']);
    $id = mysqli_real_escape_string($conn, $_POST['edit_id']);
    $edit_sql = "Update login set username = '$username', password = '$password', role = '$role' where id = $id ";
    $result = mysqli_query($conn, $edit_sql);
    
    if(mysqli_query($conn, $edit_sql))
    {
        $_SESSION['message'] = "$username-$role update successfully";
        header('Location: admin.php?tab=users#users');
        exit;
    }
    else{echo "Error update user:" . mysqli_error($conn);}
   }
   //kitab elave etmek 
   if($act === "add_book"  && isset($_POST['title']) && isset($_POST['author']) && isset($_POST['pages']) && isset($_POST['price'])
    && isset($_POST['stock']) && isset($_POST['description']) && isset($_POST['image']) && isset($_POST['author']))
   {
   
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $pages = mysqli_real_escape_string($conn, $_POST['pages']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $image = mysqli_real_escape_string($conn, $_POST['image']);
    
    $add_book_sql = "Insert into books(title, pages, price, stock, description, cover_image) values('$title', $pages, $price, $stock, '$description', 'covers/$image.jpg');";
    mysqli_query($conn, $add_book_sql);
    $add_author_sql = "Insert into authors(name) values('$author');";
    mysqli_query($conn, $add_author_sql);

    $select_for_binding_book = "Select Id  from books where title = '$title';";
    $result_book_Id = mysqli_query($conn, $select_for_binding_book);

    $select_for_binding_author = "Select Id from authors where name = '$author';";
    $result_author_Id = mysqli_query($conn, $select_for_binding_author);
    
    $book_toplu_Id = mysqli_fetch_assoc($result_book_Id);
    $author_toplu_Id = mysqli_fetch_assoc($result_author_Id);

    $book_id =  $book_toplu_Id ['Id'];
    $author_id =  $author_toplu_Id ['Id'];

    $add_author_book_sql = "Insert into book_authors(book_id, author_id) values( $book_id , $author_id);";
     mysqli_query($conn, $add_author_book_sql);
   // $result_author = mysqli_query($conn, $add_author_sql);

    if(mysqli_query($conn, $add_book_sql) && mysqli_query($conn, $add_author_sql) && mysqli_query($conn, $add_author_book_sql))
    {
        $_SESSION['message'] = "$title book added successfully";
        header('Location: admin.php?tab=books#books');
        exit;
    }
    else{echo "yoxlanislarda problem var:" . mysqli_error($conn);}
   }
   //kitab silme 
   if($act === "delete_book" && isset($_POST["delete_id"]))
   {
     $delete_id_book = mysqli_real_escape_string($conn, $_POST["delete_id"]);
     $delete_sql_book = "Delete from books where Id = $delete_id_book;";
     $select_auth = "Select author_id from book_authors where book_id = $delete_id_book;";
     $result_selected_Author = mysqli_query($conn, $select_auth);
     
     $author_toplu_id = mysqli_fetch_assoc($result_selected_Author);
     $delete_id_author = $author_toplu_id['author_id'];
     $delete_sql_author = "Delete from authors where Id = $delete_id_author;";
     $delete_sql_book_author = "Delete from book_authors where author_id = $delete_id_author and book_id = $delete_id_book; ";
      
     if( mysqli_query($conn, $delete_sql_book) && mysqli_query($conn, $delete_sql_author) && mysqli_query($conn, $delete_sql_book_author))
     {
       $_SESSION['message'] = "Book deleted successfully";
        header('Location: admin.php?tab=books#books');
        exit;
    }
    else{echo "Error deleting books:" . mysqli_error($conn);}

   }
   else {echo "silinmede error yarandi";}
   
   }
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel</title>
<link rel="stylesheet" href="CSS/admin.css"/>
<link rel = "stylesheet" href="CSS/pop-up.css"/>
</head>
<body>
    
<div class="navbar">Admin Panel</div>

<div class="choose">
    <div id="users-btn">Users</div>
    <div id="books-btn">Books</div>
</div>

<div class="section" id="users-section">
    <h2>Add User</h2>
    <form action="admin.php" method="POST">
        <input type="hidden" name = "action" value = "add_user">
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

        <form method="POST" action="admin.php" style="display:inline;">
            <input type="hidden" name = "action" value = "edit_user"/>
            <input type="hidden" name="edit_id" value="<?php echo $users['Id']; ?>">
            <button class="edit_btn" type="submit">Edit</button>
        </form>


        <form method="POST" action="admin.php" style="display:inline;">
            <input type="hidden" name = "action" value = "delete_user"/>
            <input type="hidden" name="delete_id" value="<?php echo $users['Id']; ?>">
            <button  type="submit" name="delete_user">Delete</button>
        </form>
    </td>
</tr>
<?php }  ?>
 
    </table>



</div>
<div class="section" id="books-section">
    <h2>Add Book</h2>
    <form action="admin.php" method="POST">
        <input type="hidden" name = "action" value = "add_book"/>
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
            <th>Author_Id</th>
            <th>Book_Id</th>
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
            a.Id As aauthor_id,  a.name AS author_name
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
            <td><?php echo $books['aauthor_id']?></td>
            <td><?php echo $books['Id']?></td>
            <td><?php echo $books['title'] ?></td>
            <td><?php echo $books['author_name'] ?></td>
            <td><?php echo $books['pages'] ?></td>
            <td><?php echo $books['price'] ?></td>
            <td><?php echo $books['stock'] ?></td>
            <td><?php echo $books['description'] ?></td>
            <td><?php echo $books['cover_image'] ?></td>
             <td>

        <form method="POST" action="admin.php" style="display:inline;">
            <input type="hidden" name = "action" value = "edit_book"/>
            <input type="hidden" name="edit_id" value="<?php echo $books['Id']; ?>">
            <button type="submit">Edit</button>
        </form>


        <form method="POST" action="admin.php" style="display:inline;">
            <input type="hidden" name = "action" value = "delete_book"/>
            <input type="hidden" name="delete_id" value="<?php echo $books['Id']; ?>">
            <button type="submit" name="delete_user">Delete</button>
        </form>
    </td>
         </tr>
        <?php } ?>
    </table>
</div>

<div id="usersPopup" class="users-popup" style = "display:none;">
  <div class="users-popup-content">
    <form action="admin.php" method="POST">
        <input type="hidden" name = "action" value = "edit_user">
        <input type="text" placeholder="Username" name="username" required>
        <input type="password" placeholder="Password" name="password" required>
        <select name="role" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
        <button class = "close_popup" type="submit">Update User</button>
    </form>
  </div>
</div>  

<script>
document.getElementById("users-btn").addEventListener("click", function() {
    document.getElementById("users-section").style.display = "block";
    document.getElementById("books-section").style.display = "none";
});

document.getElementById("books-btn").addEventListener("click", function() {
    document.getElementById("books-section").style.display = "block";
    document.getElementById("users-section").style.display = "none";
});

document.querySelectorAll(".edit_btn").forEach(btn => {
  btn.addEventListener("click", function(){
      document.getElementById("usersPopup").style.display = "flex";
  });
});

document.getElementById("close_popup").addEventListener("click", function()
{
    document.getElementById("usersPopup").style.display = "none";
});


function showTab(tab) {
  document.getElementById("users-section").style.display = "none";
  document.getElementById("books-section").style.display = "none";
  if (tab === 'users') {
    document.getElementById("users-section").style.display = "block";
  } else if (tab === 'books') {
    document.getElementById("books-section").style.display = "block";
  }
}

// səhifə yüklənəndə URL-dən tab oxuyur
document.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);
  const tab = params.get("tab") || (location.hash ? location.hash.substring(1) : "users");
  showTab(tab);
});
</script>

<script>
    document.querySelectorAll(".edit_btn").forEach(btn => {
  btn.addEventListener("click", function(e){
      e.preventDefault(); // formun submit olmasını dayandırır

      const row = btn.closest("tr"); // kliklənmiş düymənin satırı
      const username = row.cells[0].innerText;
      const password = row.cells[1].innerText;
      const role = row.cells[2].innerText;
      const userId = row.querySelector("input[name='edit_id']").value;

      const popup = document.getElementById("usersPopup");
      popup.style.display = "flex";

      // Pop-up input-larını doldur
      popup.querySelector("input[name='username']").value = username;
      popup.querySelector("input[name='password']").value = password;
      popup.querySelector("select[name='role']").value = role;

      // Hidden input üçün id əlavə et
      let hiddenId = popup.querySelector("input[name='edit_id']");
      if(!hiddenId){
          hiddenId = document.createElement("input");
          hiddenId.type = "hidden";
          hiddenId.name = "edit_id";
          popup.querySelector("form").appendChild(hiddenId);
      }
      hiddenId.value = userId;
  });
});

</script>

</body>
</html>
