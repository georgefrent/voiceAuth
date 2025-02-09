<?php
session_start();
require('config.php');
$loggedInUsername = $_SESSION['username'];

// Delete the user's audio files folder
$audioFolderPath = "uploaded_audio/" . $loggedInUsername;

if (is_dir($audioFolderPath)) {
    // Function to recursively delete files and directories
    function deleteFolder($folderPath) {
        $files = array_diff(scandir($folderPath), array('.', '..'));
        
        foreach ($files as $file) {
            $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;
            
            if (is_dir($filePath)) {
                // Recursively delete subdirectories
                deleteFolder($filePath);
            } else {
                // Delete the file
                unlink($filePath);
            }
        }
        // Finally, delete the directory itself
        rmdir($folderPath);
    }

    deleteFolder($audioFolderPath);
}

// Delete the user from the database
$sql = "DELETE FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $loggedInUsername); // Bind the username
    if ($stmt->execute()) {
        // Successfully deleted the user
        echo "Utilizatorul și fișierele audio ale acestuia au fost șterse cu succes.";
        // Destroy the session after deleting the user
        session_destroy();
    } else {
        echo "Eroare la ștergerea utilizatorului: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Eroare la pregătirea declarației: " . $conn->error;
}
header('Location: index.php');
exit();
?>
