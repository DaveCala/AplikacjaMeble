<?php
require_once '../db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $imagePath = null;

    // Sprawdzenie obecności pola "is_variation"
    if (!isset($_POST['is_variation'])) {
        $response['message'] = 'Pole "Produkt z wariacjami" jest wymagane.';
        echo json_encode($response);
        exit;
    }

    $is_variation = $_POST['is_variation'] === 'true' ? 1 : 0;
    $price = $_POST['price'] ?? null;  // Cena (może być pusta)
    $description = $_POST['description'] ?? null;  // Opis (może być pusty)
    
    // Rozmiary i parametry techniczne
     $szerokosc = $_POST['szerokosc'] ?? null;
     $wysokosc = $_POST['wysokosc'] ?? null;
     $glebokosc = $_POST['glebokosc'] ?? null;
     $powierzchnia_spania = $_POST['powierzchnia_spania'] ?? null;
     $glebokosc_siedziska = $_POST['glebokosc_siedziska'] ?? null;
 
     // Dodatkowe cechy
     $wypelnienie_siedziska = $_POST['wypelnienie_siedziska'] ?? null;
     $funkcja_spania = $_POST['funkcja_spania'] ?? null;
     $pojemnik_na_posciel = $_POST['pojemnik_na_posciel'] ?? null;
     $regulowany_zaglowek = $_POST['regulowany_zaglowek'] ?? null;
     $czas_wysylki = $_POST['czas_wysylki'] ?? null;
 
     // Inne właściwości
     $ksztalt_naroznika = $_POST['ksztalt_naroznika'] ?? null;
     $styl = $_POST['styl'] ?? null;
     $rozmiar_kanapy = $_POST['rozmiar_kanapy'] ?? null;
     $obrotowe_siedzisko = $_POST['obrotowe_siedzisko'] ?? null;
     $regulowane_podlokietniki = $_POST['regulowane_podlokietniki'] ?? null;


    // Walidacja danych
    if (empty($title) || empty($category)) {
        $response['message'] = 'Wszystkie pola muszą być wypełnione.';
        echo json_encode($response);
        exit;
    }

    // Obsługa pliku obrazu
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../img/";
        if (!is_dir($targetDir) || !is_writable($targetDir)) {
            $response['message'] = 'Folder do przesyłania obrazów nie istnieje lub brak uprawnień.';
            echo json_encode($response);
            exit;
        }

        $imageFileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $imageFileName;

        // Sprawdzamy typ pliku
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        if (!in_array($fileType, $allowedTypes)) {
            $response['message'] = 'Nieprawidłowy format pliku. Dozwolone: JPG, JPEG, PNG, GIF.';
            echo json_encode($response);
            exit;
        }

        // Przesyłanie pliku
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $response['message'] = 'Błąd przesyłania pliku: ' . $_FILES['image']['error'];
            echo json_encode($response);
            exit;
        }

        $imagePath = $imageFileName;
    }

    // Dodanie produktu do bazy danych
try {
    // Jeśli produkt jest wariacją, zapisz cenę i opis, w przeciwnym razie będą NULL
    if ($is_variation == 1) {
        $stmt = $pdo->prepare("
            INSERT INTO products (
                title, category, image, isVariation, price, description,
                szerokosc, wysokosc, glebokosc, powierzchnia_spania, glebokosc_siedziska,
                wypelnienie_siedziska, funkcja_spania, pojemnik_na_posciel, regulowany_zaglowek,
                czas_wysylki, ksztalt_naroznika, styl, rozmiar_kanapy, obrotowe_siedzisko,
                regulowane_podlokietniki
            ) 
            VALUES (
                :title, :category, :image, :isVariation, :price, :description,
                :szerokosc, :wysokosc, :glebokosc, :powierzchnia_spania, :glebokosc_siedziska,
                :wypelnienie_siedziska, :funkcja_spania, :pojemnik_na_posciel, :regulowany_zaglowek,
                :czas_wysylki, :ksztalt_naroznika, :styl, :rozmiar_kanapy, :obrotowe_siedzisko,
                :regulowane_podlokietniki
            )
        ");
        
        // Bindowanie wartości
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':szerokosc', $szerokosc);
        $stmt->bindParam(':wysokosc', $wysokosc);
        $stmt->bindParam(':glebokosc', $glebokosc);
        $stmt->bindParam(':powierzchnia_spania', $powierzchnia_spania);
        $stmt->bindParam(':glebokosc_siedziska', $glebokosc_siedziska);
        $stmt->bindParam(':wypelnienie_siedziska', $wypelnienie_siedziska);
        $stmt->bindParam(':funkcja_spania', $funkcja_spania);
        $stmt->bindParam(':pojemnik_na_posciel', $pojemnik_na_posciel);
        $stmt->bindParam(':regulowany_zaglowek', $regulowany_zaglowek);
        $stmt->bindParam(':czas_wysylki', $czas_wysylki);
        $stmt->bindParam(':ksztalt_naroznika', $ksztalt_naroznika);
        $stmt->bindParam(':styl', $styl);
        $stmt->bindParam(':rozmiar_kanapy', $rozmiar_kanapy);
        $stmt->bindParam(':obrotowe_siedzisko', $obrotowe_siedzisko);
        $stmt->bindParam(':regulowane_podlokietniki', $regulowane_podlokietniki);
    } else {
        // Jeśli produkt nie jest wariacją, zapisz tylko podstawowe informacje
        $stmt = $pdo->prepare("
            INSERT INTO products (
                title, category, image, isVariation
            ) 
            VALUES (
                :title, :category, :image, :isVariation
            )
        ");
    }

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':image', $imagePath);
        $stmt->bindValue(':isVariation', $is_variation, PDO::PARAM_INT);
        


        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Produkt został pomyślnie dodany.';
        } else {
            $response['message'] = 'Nie udało się dodać produktu. Błąd SQL: ' . implode(" ", $stmt->errorInfo());
        }
    } catch (Exception $e) {
        $response['message'] = 'Błąd: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>
