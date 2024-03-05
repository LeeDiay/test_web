<?php
    session_start();
    @include '../inc/config.php';
    @include './check_admin.php';
    @include '../logout.php';

    if(isset($_GET['id'])) {
        $assignment_id = $_GET['id'];

        $query = "SELECT * FROM assignments WHERE id = '$assignment_id'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);

        // Lấy danh sách người dùng đã nộp bài và thông tin về bài nộp của họ
        $submitted_users_query = "SELECT user.username, submitted_assignments.file_name FROM user INNER JOIN submitted_assignments ON user.username = submitted_assignments.uploader WHERE submitted_assignments.assignment_id = '$assignment_id'";
        $submitted_users_result = mysqli_query($conn, $submitted_users_query);
        $submitted_users = [];
        while ($submitted_user_row = mysqli_fetch_assoc($submitted_users_result)) {
            $submitted_users[] = $submitted_user_row;
        }
    } else {
        header("Location: show_assignment.php");
        exit();
    }
?>

<?php @include '../inc/admin/header.php'; ?>
<section class="p-5">
    <div class="container">
        <div class="d-flex col-md justify-content-center">
            <div class="card bg-light text-dark" style="width: 40rem;">
                <div class="card-body">
                    <h3 class="card-title"><?php echo $row['title']; ?></h3>
                    <p class="card-text"><strong>Description:</strong> <?php echo $row['description']; ?></p>
                    <p class="card-text"><strong>Deadline:</strong> <?php echo $row['due_date']; ?></p>
                    <p>
                        <div class="card-text"><strong>Document:</strong>
                            <?php
                                if (!empty($row['file_name'])) {
                                    echo '<a href="../uploads/' . $row['file_name'] . '" download>' . $row['file_name'] . '</a>';
                                } else {
                                    echo 'No file uploaded';
                                }
                            ?>
                        </div>
                    </p>
                    <p class="card-text"><strong>Download Assignments:</strong></p>

                    <ul>
                        <?php
                            if (!empty($submitted_users)) {
                                foreach ($submitted_users as $submitted_user) {
                                     echo '<li>User: '. $submitted_user['username'] . ' - File submitted: ' . '<a href="../submissions/' . $submitted_user['file_name'] . '" download>' . $submitted_user['file_name'] .'</a></li>';
                                }
                            } else {
                                echo '<li>No students submitted yet</li>';
                            }
                        ?>
                    </ul>
                    
                    <div class="d-flex justify-content-between">
                        <a href="assignment_page.php?id=<?php echo $assignment_id; ?>" class="btn btn-secondary">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php @include '../inc/footer.php'; ?>
