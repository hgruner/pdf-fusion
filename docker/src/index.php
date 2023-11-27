

<html>
<head>
  <!-- CSS de Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">

  <!-- JS de Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body  class="container">
  <h1 class="text-center">Fusionner des fichiers PDF</h1>
    <form class="card mt-3" method="post" enctype="multipart/form-data">
    <div class="card-body">
      <h5 class="card-title">Fusionner des fichiers PDF</h5>
      <input type="file" name="pdf[]" multiple accept="application/pdf" class="form-control">
      <input type="submit" name="submit" value="Fusionner" class="btn btn-primary">
    </div>
  </form>
  <?php
// Vérifier si le formulaire a été soumis
if(isset($_POST['submit'])) {

    // Définir le dossier où sont stockés les fichiers PDF
    $dir = 'uploads/';

    // Définir le seuil de temps en secondes (24 heures = 86400 secondes)
    $threshold = time() - 86400;

    // Parcourir les fichiers du dossier
    foreach (glob($dir . '*.pdf') as $file) {
    // Vérifier si le fichier est plus vieux que le seuil
    if (filemtime($file) < $threshold) {
        // Supprimer le fichier
        unlink($file);
    }
    }

  // Créer un tableau pour stocker les noms des fichiers PDF
  $pdf_files = array();
  // Parcourir les fichiers envoyés
  foreach($_FILES['pdf']['name'] as $key => $name) {
    // Vérifier si le fichier est un PDF
    if($_FILES['pdf']['type'][$key] == 'application/pdf') {
      // Déplacer le fichier dans le dossier uploads
      $tmp_name = $_FILES['pdf']['tmp_name'][$key];
      $new_name = 'uploads/' . $name;
      move_uploaded_file($tmp_name, $new_name);
      // Ajouter le nom du fichier au tableau
      $pdf_files[] = $new_name;
    }
  }
  // Vérifier si au moins deux fichiers PDF ont été envoyés
  if(count($pdf_files) >= 2) {
    // Générer un nom aléatoire pour le fichier fusionné
    $merged_file = 'uploads/merged_' . uniqid() . '.pdf';
    // Fusionner les fichiers PDF avec PDFtk
    $command = 'pdftk ' . implode(' ', array_map(function($file) { return escapeshellarg($file); }, $pdf_files)) . ' cat output ' . escapeshellarg($merged_file);
    exec($command, $output, $return_var);
   if($return_var == 0) {
  // Afficher le lien de téléchargement du fichier fusionné dans une alerte de succès
  echo '<div class="alert alert-success" role="alert">';
  echo '<a href="' . $merged_file . '">Télécharger le fichier PDF fusionné (lien valable pendant 24h)</a>';
  echo '</div>';
} else {
  // Afficher un message d'erreur dans une alerte de danger
  echo '<div class="alert alert-danger" role="alert">';
  echo 'Une erreur est survenue lors de la fusion des fichiers PDF.';
  echo '</div>';
}
    // Supprimer les fichiers sources après la fusion
    foreach($pdf_files as $file) {
      unlink($file);
    }
  } else {
    // Afficher un message d'erreur    
    echo '<div class="alert alert-warning" role="alert">';
    echo 'Veuillez envoyer au moins deux fichiers PDF.';
    echo '</div>';
  }
}
?>
</body>
</html>
