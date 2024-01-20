<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My cinema</title>
    <link rel="stylesheet" href="styles.css" type="text/css">
</head>

<body>
    <header>
        <?php
        $pdo = new PDO("mysql:host=localhost;dbname=cinema", 'root', 'root');
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        ?>
    </header>
    <main>
        <div class="filters__section">

            <div class="search__section">
                <h2>Search & filters</h2>
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="get">
                    <div class="filter-search">
                        <label for="search-bar">Search</label>
                        <input type="search" name="search" id="search-bar">
                    </div>

                    <div class="filter-genre">
                        <label for="genre-select">Genres</label>
                        <select name="genre" id="genre-select">
                            <option value="empty">--Select a genre--</option>
                            <?php
                            $unbufferedResult = $pdo->query("SELECT id, name FROM genre ORDER BY name");
                            foreach ($unbufferedResult as $row) {
                                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="filter-distributor">
                        <label for="distributor-select">Distributors</label>
                        <select name="distributor" id="distributor-select">
                            <option value="empty">--Select a distributor--</option>
                            <?php
                            $distributor = $pdo->query("SELECT DISTINCT id, name FROM distributor");
                            foreach ($distributor as $option) {
                                echo '<option value="' . $option['id'] . '">' . $option['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit">Recherche</button>
                </form>
            </div>
        </div>
        <?php
        // Récupérer la valeur du genre après avoir soumis le formulaire
        $genre_id = isset($_GET['genre']) ? intval($_GET['genre']) : 0;

        // Requête SQL pour récupérer les films du genre spécifié
        $query = "
    SELECT movie.id, movie.title, genre.id AS genre_id, genre.name AS genre_name
    FROM movie
    JOIN movie_genre ON movie.id = movie_genre.id_movie
    JOIN genre ON movie_genre.id_genre = genre.id
    WHERE genre.id = :genre_id
    ORDER BY title
";

        // Préparation de la requête avec un paramètre nommé
        $stmt = $pdo->prepare($query);

        // Liaison du paramètre avec la valeur récupérée du formulaire
        $stmt->bindParam(":genre_id", $genre_id, PDO::PARAM_INT);

        // Exécution de la requête
        $stmt->execute();

        // Vérification s'il y a des résultats à traiter
        if (!empty($stmt)) {
            echo "<div class='genre'>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo $row['title'] . '<br>';
            }
            echo "</div>";
        }

        // Fermeture de la connexion
        $pdo = null;
        ?>
    </main>

</body>

</html>