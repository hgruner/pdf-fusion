<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fusionner des fichiers PDF</title>
  <!-- CSS de Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">

  <!-- JS de Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body  class="container">
  <h1 class="text-center">Fusionner des fichiers PDF</h1>
    <form class="card mt-3" method="post" enctype="multipart/form-data">
    <div class="card-body">
      <h5 class="card-title">Sélectionnez au moins deux fichiers</h5>
<!-- On crée un élément personnalisé qui va remplacer l'input de type file et servir de zone de dépôt -->
<div id="custom-file-input" class="custom-file-input" onclick="document.getElementById('file-input').click()">
  <!-- On ajoute un élément qui contient le texte -->
  <p>Déposez vos fichiers ici ou cliquer pour en ajouter</p>
  <!-- On ajoute un élément qui va contenir les icônes de fichier -->
  <div class="file-icons"></div>
</div>
<!-- On masque l'input de type file avec du CSS -->
<input type="file" id="file-input" name="pdf[]" multiple accept="application/pdf" class="form-control" style="display: none;">
<input type="submit" name="submit" value="Fusionner" class="btn btn-primary">
  </form>
<script>
// On récupère les éléments du DOM
var customFileInput = document.getElementById("custom-file-input");
var fileInput = document.getElementById("file-input");

// On ajoute un écouteur d'événement sur le changement de valeur de l'input
fileInput.addEventListener("change", function() {
  // On récupère l'élément qui contient les icônes de fichier
  var fileIcons = document.querySelector(".custom-file-input .file-icons");

  // On vide le contenu de l'élément
  fileIcons.innerHTML = "";

  // On parcourt la liste des fichiers sélectionnés
  for (var i = 0; i < fileInput.files.length; i++) {
    // On crée un élément qui va représenter une icône de fichier
    var fileIcon = document.createElement("div");
    fileIcon.className = "file-icon";

    // On crée un élément qui va contenir le nom du fichier
    var fileName = document.createElement("p");
    fileName.className = "file-name";
    fileName.textContent = fileInput.files[i].name;

    // On ajoute les éléments à l'élément qui contient les icônes de fichier
    fileIcons.appendChild(fileIcon);
    fileIcons.appendChild(fileName);
  }
});

// On ajoute les écouteurs d'événements sur l'élément personnalisé
customFileInput.addEventListener("dragenter", function(e) {
  // On change le style de l'élément personnalisé pour indiquer qu'on peut y déposer un fichier
  customFileInput.style.backgroundColor = "#eee";
  // On empêche le comportement par défaut
  e.preventDefault();
});

customFileInput.addEventListener("dragover", function(e) {
  // On empêche le comportement par défaut
  e.preventDefault();
});

customFileInput.addEventListener("drop", function(e) {
  // On rétablit le style de l'élément personnalisé
  customFileInput.style.backgroundColor = "white";
  // On empêche le comportement par défaut
  e.preventDefault();
  // On récupère les fichiers déposés
  var files = e.dataTransfer.files;
  // On crée un nouvel objet DataTransfer
  var dataTransfer = new DataTransfer();
  // On ajoute les fichiers existants à l'objet DataTransfer
  for (var i = 0; i < fileInput.files.length; i++) {
    dataTransfer.items.add(fileInput.files[i]);
  }
  // On ajoute les fichiers déposés à l'objet DataTransfer
  for (var i = 0; i < files.length; i++) {
    dataTransfer.items.add(files[i]);
  }
  // On affecte l'objet FileList à l'input de type file
  fileInput.files = dataTransfer.files;
  // On déclenche l'événement change sur l'input de type file pour mettre à jour les icônes de fichier
  fileInput.dispatchEvent(new Event("change"));
});

// On récupère l'élément qui contient le texte
var text = document.querySelector(".custom-file-input p");

// On ajoute un écouteur d'événement sur le changement de valeur de l'input
fileInput.addEventListener("change", function() {
  // On vérifie si l'input de type file contient des fichiers
  if (fileInput.files.length > 0) {
    // Si oui, on masque le texte avec la propriété display
    text.style.display = "none";
  } else {
    // Si non, on affiche le texte avec la propriété display
    text.style.display = "block";
  }
});


  </script>  
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
