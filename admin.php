<?php
   require_once "db.php";
   
    session_start();
  
    if(!isset($_SESSION['username'])) {
    // Nəzərə al: burada admin.php üçün tam ad yazılır (basename də istifadə oluna bilər)
    $_SESSION['redirect_after_login'] = 'admin.php';
    header('Location: login.php');
    exit;
}

   if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
    }

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
$covers_dir = __DIR__ . '/covers';
if(!is_dir($covers_dir)) mkdir($covers_dir, 0755, true);

if($act === "add_book" && isset($_POST['title']) && isset($_POST['author']) && isset($_POST['pages']) && isset($_POST['price'])
    && isset($_POST['stock']) && isset($_POST['description']))
{
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $pages = (int) $_POST['pages'];
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // fayl yoxlamasi
    if(!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['message'] = "Please select an image file.";
        header('Location: admin.php?tab=books#books');
        exit;
    }

    $file = $_FILES['image'];
    $maxSize = 2 * 1024 * 1024;
    $allowedExt = ['jpg','jpeg','png','gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if(!in_array($ext, $allowedExt) || $file['size'] > $maxSize) {
        $_SESSION['message'] = "Invalid image or too large (max 2MB).";
        header('Location: admin.php?tab=books#books');
        exit;
    }

    $newName = uniqid('cover_', true) . '.' . $ext;
    $destination = $covers_dir . '/' . $newName;
    if(!move_uploaded_file($file['tmp_name'], $destination)) {
        $_SESSION['message'] = "Error saving uploaded file.";
        header('Location: admin.php?tab=books#books');
        exit;
    }

    $cover_path = 'covers/' . $newName;
    $cover_path_escaped = mysqli_real_escape_string($conn, $cover_path);

    $add_book_sql = "INSERT INTO books(title, pages, price, stock, description, cover_image) 
                     VALUES('$title', $pages, $price, $stock, '$description', '$cover_path_escaped');";
    mysqli_query($conn, $add_book_sql);

    // author əlavə et və book_authors bağla (sənin mövcud məntiqə uyğunlaşdır)
    $add_author_sql = "INSERT INTO authors(name) VALUES('$author');";
    mysqli_query($conn, $add_author_sql);

    // book_id və author_id tapıb bağla
    $book_id = mysqli_insert_id($conn);
    $resAuthor = mysqli_query($conn, "SELECT Id FROM authors WHERE name = '$author' ORDER BY Id DESC LIMIT 1;");
    $arow = mysqli_fetch_assoc($resAuthor);
    $author_id = $arow['Id'] ?? null;
    if($author_id) mysqli_query($conn, "INSERT INTO book_authors(book_id, author_id) VALUES($book_id, $author_id);");

    $_SESSION['message'] = "$title book added successfully";
    header('Location: admin.php?tab=books#books');
    exit;
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
   
if($act === "edit_book" && isset($_POST['edit_id_book']) && isset($_POST['title']) && isset($_POST['pages']) && isset($_POST['price'])
    && isset($_POST['stock']) && isset($_POST['description']) && isset($_POST['author']))
{
    $id = (int) $_POST['edit_id_book'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $pages = (int) $_POST['pages'];
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $current_image = mysqli_real_escape_string($conn, $_POST['current_image'] ?? '');

   
    $sqlGet = "SELECT cover_image FROM books WHERE id = $id LIMIT 1;";
    $resGet = mysqli_query($conn, $sqlGet);
    $rowGet = mysqli_fetch_assoc($resGet);
    $existing_cover = $rowGet['cover_image'] ?? $current_image;

    $cover_path = $existing_cover; // default

    // Əgər yeni fayl yüklənibsə -> yoxla və köçür
    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $maxSize = 2 * 1024 * 1024;
        $allowedExt = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if(in_array($ext, $allowedExt) && $file['size'] <= $maxSize) {
            $newName = uniqid('cover_', true) . '.' . $ext;
            $destination = $covers_dir . '/' . $newName;
            if(move_uploaded_file($file['tmp_name'], $destination)) {
                $cover_path = 'covers/' . $newName;
            }
        } else {
            $_SESSION['message'] = "Invalid image or too large (max 2MB).";
            header('Location: admin.php?tab=books#books');
            exit;
        }
    }

    $cover_path_escaped = mysqli_real_escape_string($conn, $cover_path);

    $sql = "UPDATE books SET title = '$title', pages = $pages, price = $price, stock = $stock,
           description = '$description', cover_image = '$cover_path_escaped' WHERE id = $id;";
    mysqli_query($conn, $sql);

    // author update / bind (sənin mövcud məntiqə uyğun)
    $sql_find_auth = "SELECT author_id FROM book_authors WHERE book_id = $id LIMIT 1;";
    $result_auth_book = mysqli_query($conn, $sql_find_auth);
    $auth_book = mysqli_fetch_assoc($result_auth_book);
    $auth_id = $auth_book['author_id'] ?? null;

    if($auth_id) {
        mysqli_query($conn, "UPDATE authors SET name = '$author' WHERE Id = $auth_id;");
    } else {
        mysqli_query($conn, "INSERT INTO authors(name) VALUES('$author');");
        $new_author_id = mysqli_insert_id($conn);
        if($new_author_id) mysqli_query($conn, "INSERT INTO book_authors(book_id, author_id) VALUES($id, $new_author_id);");
    }

    $_SESSION['message'] = "$title updated successfully";
    header('Location: admin.php?tab=books#books');
    exit;
}


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
            <button class="edit_btn" type="button">Edit</button>
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
        <input type="file" name="image" accept="image/*" required>
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
            <input type="hidden" name="edit_id_book" value="<?php echo $books['Id']; ?>">
            <button class="edit_btn_book" type="button">Edit</button>
        </form>


        <form method="POST" action="admin.php" style="display:inline;">
            <input type="hidden" name = "action" value = "delete_book"/>
            <input type="hidden" name="delete_id" value="<?php echo $books['Id']; ?>">
            <button type="submit" name="delete_book">Delete</button>
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
         <button class ="close_popup" type="button">Close</button>
    </form>
  </div>
</div>  

<div id="booksPopup" class="users-popup" style="display:none;">
  <div class="users-popup-content">
    <form action="admin.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="edit_book">
        <input type="hidden" name="edit_id_book" value="">
        <!-- mövcud şəkilin path-i -->
        <input type="hidden" name="current_image" value="">
        <input type="text" placeholder="Title" name="title" required>
        <input type="text" placeholder="Author" name="author" required>
        <input type="number" placeholder="Pages" name="pages" required>
        <input type="number" placeholder="Price" name="price" required>
        <input type="number" placeholder="Stock" name="stock" required>
        <input type="text" placeholder="Description" name="description" required>
        <label>Choose new image (optional):</label>
        <input type="file" name="image" accept="image/*">
        <div id="currentImageLabel" style="margin:6px 0;font-size:0.9em;color:#333;"></div>
        <button class="close_popup" type="submit">Update Books</button>
        <button class="close_popup" type="button">Close</button>
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

document.querySelectorAll(".edit_btn_book").forEach(btn => {
  btn.addEventListener("click", function(e){
      e.preventDefault();

      const row = btn.closest("tr");
      const bookId = row.cells[1].innerText;
      const title = row.cells[2].innerText;
      const author = row.cells[3].innerText;
      const pages = row.cells[4].innerText;
      const price = row.cells[5].innerText;
      const stock = row.cells[6].innerText;
      const description = row.cells[7].innerText;
      // cover_image sahəsində "covers/xxxx.jpg" şəklində string olduğunu qəbul edirik
      const coverPath = row.cells[8].innerText.trim();

      const popup = document.getElementById("booksPopup");
      popup.style.display = "flex";

      popup.querySelector("input[name='title']").value = title;
      popup.querySelector("input[name='author']").value = author;
      popup.querySelector("input[name='pages']").value = pages;
      popup.querySelector("input[name='price']").value = price;
      popup.querySelector("input[name='stock']").value = stock;
      popup.querySelector("input[name='description']").value = description;
      popup.querySelector("input[name='edit_id_book']").value = bookId;

      // hidden current image üçün full path saxla
      const hidden = popup.querySelector("input[name='current_image']");
      if(hidden) hidden.value = coverPath;

      // label göstər
      const lbl = popup.querySelector("#currentImageLabel");
      lbl.textContent = coverPath ? ("Current image: " + coverPath) : "No image";
  });
});


document.querySelectorAll(".close_popup").forEach(btn => {
  btn.addEventListener("click", function(e){
    document.getElementById("usersPopup").style.display = "none";
    document.getElementById("booksPopup").style.display = "none";
  });
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


document.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);
  const tab = params.get("tab") || (location.hash ? location.hash.substring(1) : "users");
  showTab(tab);
});
</script>

<script>
    document.querySelectorAll(".edit_btn").forEach(btn => {
  btn.addEventListener("click", function(e){
      e.preventDefault(); 

      const row = btn.closest("tr"); 
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
