<?php
// Definimos la función getPokemonData que recibe un número entero $count y devuelve un array de pokemons
function getPokemonData($count)
{
    $pokemonArray = [];
    //generar un array de 5 pokemons aleatorios
    for ($i = 0; $i < $count; $i++) {
        // 1) genera número aleatorio entre 1 y 150
        $numAleatorio = rand(1, 151);
        // 2) lee el contenido de la api 
        $pokeCont = file_get_contents("https://pokeapi.co/api/v2/pokemon/$numAleatorio");
        if (!$pokeCont) {
            echo "VA MAL"; // Si no se puede obtener el contenido, continuamos con la siguiente iteración
        }
        // 3) lo decodifica
        $data = json_decode($pokeCont, true);
        if (!$data) {
            continue; // Si no se puede decodificar el JSON, continuamos con la siguiente iteración
        }
        // Llamo a la función isShiny para determinar si el pokemon es shiny o no
        $isShiny = isShiny();
        // Si el pokemon es shiny, uso la imagen shiny, sino la normal
        $imagen = $isShiny && isset($data["sprites"]["front_shiny"]) ? $data["sprites"]["front_shiny"] : $data["sprites"]["front_default"];

        // 4) Creo un objeto pokemon con los datos que necesito
        $objPokemon = [
            "id" => $data["id"],
            "nombre" => $data["name"],
            "imagen" => $imagen,
            "tipos" => $data["types"],
            "habilidades" => $data["abilities"],
            "isShiny" => $isShiny
        ];
        // 5) Agrego el objeto pokemon al array
        $pokemonArray[] = $objPokemon;
    }
    // 6) Devuelvo el array de pokemons
    return $pokemonArray;
}

// Esta función simula la probabilidad de que un pokemon sea shiny (1 de cada 20)
function isShiny()
{
    return rand(1, 20) == 1; // 1 de cada 20 es shiny
}

// Obtenemos 5 pokemons por defecto
$pokemons = getPokemonData(5);

//definimos la función renderCards que recibe un array de pokemons y genera el html
function renderCards($pokeArray)
{
    $primerTipo = null;
    // recibe un array de pokemons y genera el html
    echo "<section id='pokecartas'>";
    // Recorremos el array de pokemons
    foreach ($pokeArray as $pokemon) {
        $shinyClass = $pokemon["isShiny"] ? ' shiny' : ''; // Agregamos la clase shiny si el pokemon es shiny
        echo "<div class='carta$shinyClass'>";
        echo "<div class='img-container'>";
        echo "<img src='" . $pokemon["imagen"] . "' alt='" . $pokemon["nombre"] . "' loading='lazy' />";
        echo "</div>";
        echo "<div class='datos";
        foreach ($pokemon["tipos"] as $tipo) {
            if ($tipo["slot"] === 1) {
                $primerTipo = $tipo["type"]["name"];
                break;
            }
        }
        echo " " . $primerTipo;
        echo "'>";
        echo "<h3 class='pokemon-name'>" . ucfirst($pokemon["nombre"]) . "</h3>";
        echo "<p class='pokemon-id'>#" . $pokemon["id"] . "</p>";
        echo "<div class='tipos-pokemon'>";
        foreach ($pokemon["tipos"] as $tipo) {
            echo "<span class='tipo-" . $tipo["type"]["name"] . "'>" . $tipo["type"]["name"] . "</span>";
        }
        echo "</div>";
        echo "<h4>Habilidades:</h4>";
        echo "<ul class='habilidades'>";
        foreach ($pokemon["habilidades"] as $habilidad) {
            echo "<li>" . str_replace("-", " ", $habilidad["ability"]["name"]) . "</li>";
        }
        echo "</ul>";
        echo "</div>";
        echo "</div>";
    }

    echo "</section>";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PokeWeb</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1 class="pokemon-title">PokéCartas</h1>
    </header>
    <div id="container">
        <?php renderCards($pokemons); ?>
        <div id="botonera">
            <button id="btn-cargar" onclick="location.reload()">Cargar 5 Cartas</button>
        </div>
    </div>
</body>

</html>