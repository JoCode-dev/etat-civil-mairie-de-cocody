<?php

class FileHelper{

   static public function upload(string $direcotyPath='/assets/uploads/'):?string
   {
     if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
            // Validation du type de fichier
            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
            $fileType = mime_content_type($_FILES['fichier']['tmp_name']);
            
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception('Seuls les fichiers PDF, JPEG et PNG sont autorisés');
            }

            // Création du répertoire si inexistant
            $uploadDir = __DIR__ . $direcotyPath;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Génération d'un nom de fichier sécurisé
            $fileExt = pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('doc_', true) . '.' . $fileExt;
            $filePath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['fichier']['tmp_name'], $filePath)) {
                $fichierPath = $uploadDir.'/' . $fileName;
                  return $fichierPath;
            } else {
                throw new Exception('Erreur lors du téléchargement du fichier');
            }
           
        }else{
            return null;
        }
   }
    static function getRelativePath($path): String{
        $path = isset($path)?$path:'assets/img/image_available.jpg';
        $isAdmin = str_contains( strval($_SERVER['REQUEST_URI']),'/admin');
        $path =$isAdmin? "../".$path:$path;
        $path = str_replace(DIRECTORY_SEPARATOR,'/',$path);
        return $path;
    }
}