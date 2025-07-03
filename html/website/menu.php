<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['initialized'])) {
    session_regenerate_id(true);
    $_SESSION['initialized'] = true;
}

header('X-Frame-Options: SAMEORIGIN');

$pdf_path = "Lancaster's Dinner Menu 17 Jun 2024.pdf";

if (!file_exists($pdf_path) || pathinfo($pdf_path, PATHINFO_EXTENSION) !== 'pdf') {
    die("Error: Menu file not found or invalid.");
}
?>

<!doctype html>
<html lang="en-GB">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <title>Lancaster's Restaurant</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include('navbar.php'); ?>
    <section id="menu">
        <h1>Our Menu</h1>
        <div class="iframe-container">
            <iframe src="<?php echo htmlspecialchars($pdf_path); ?>" width="100%" height="1160px" style="border: none; max-width: 800px;">
                Your browser does not support embedded PDFs. Please <a href="<?php echo htmlspecialchars($pdf_path); ?>" target="_blank">download the menu</a> to view it.
            </iframe>
        </div>
    </section>
    <?php
echo $twig->render('footer.twig');
?>

</body>
</html>
