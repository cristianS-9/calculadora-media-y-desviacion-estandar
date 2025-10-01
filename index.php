<?php
session_start();

if (!isset($_SESSION['numeros'])) {
    $_SESSION['numeros'] = [];
}
$resultado = null;
$error = null;

function calcularMedia($nums)
{
    $n = count($nums);
    return $n > 0 ? array_sum($nums) / $n : 0;
}
function calcularDesviacionEstandar($nums)
{
    $n = count($nums);
    if ($n === 0)
        return 0;
    $media = calcularMedia($nums);
    $suma = 0;
    foreach ($nums as $v)
        $suma += pow($v - $media, 2);
    return sqrt($suma / $n);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['agregar'])) {
        $raw = trim($_POST['numero'] ?? '');
        if ($raw === '') {
            $error = "Ingresa un número.";
        } elseif (!is_numeric($raw)) {
            $error = "El valor debe ser numérico.";
        } else {
            $_SESSION['numeros'][] = floatval($raw);
        }
    } elseif (isset($_POST['calcular'])) {
        if (empty($_SESSION['numeros'])) {
            $error = "No hay números para calcular.";
        } else {
            $media = calcularMedia($_SESSION['numeros']);
            $desv = calcularDesviacionEstandar($_SESSION['numeros']);
            $resultado = [
                'media' => round($media, 2),
                'desviacion' => round($desv, 2)
            ];
        }
    } elseif (isset($_POST['reset'])) {
        $_SESSION['numeros'] = [];
    } elseif (isset($_POST['eliminar'])) {
        $i = intval($_POST['indice']);
        if (isset($_SESSION['numeros'][$i])) {
            unset($_SESSION['numeros'][$i]);
            $_SESSION['numeros'] = array_values($_SESSION['numeros']); // reindexar
        }
    }

}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Calculadora Media y Desviación Estándar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <!-- Toggle de tema claro/oscuro -->
    <button id="theme-toggle" type="button" aria-label="Cambiar a modo oscuro" title="Cambiar tema" data-mode="light">
        <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="4" />
            <path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41M16.66 16.66l1.41 1.41M2 12h2m16 0h2M6.34 17.66l1.41-1.41M17.66 6.34l1.41-1.41" />
        </svg>
        <svg class="icon-moon" style="display:none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 0 1 11.21 3 7 7 0 1 0 21 12.79Z" />
        </svg>
        <span class="label-text">Light</span>
    </button>
    <div class="background">
        <div class="gradient-blob"></div>
        <div class="gradient-blob"></div>
        <div class="gradient-blob"></div>
    </div>

    <main>
        <h1>Calculadora Estadística</h1>
        <p class="subtitle">Ingresa números, calcula la media y la desviación estándar</p>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="cards-container">

            <div class="card card--results" data-rotation-factor="2">
                <div class="card-content">
                    <h2>Ingresar números</h2>
                    <p>Agrega valores numéricos. Puedes eliminar uno a uno o reiniciar la lista.</p>

                    <form method="post" class="formulario" autocomplete="off">
                        <input type="number" step="any" name="numero" placeholder="Ingresa un número" autofocus>

                        <div class="botones">
                            <button class="card-button" type="submit" name="agregar">Agregar</button>
                            <button class="card-button" type="submit" name="calcular">Calcular</button>
                            <button class="card-button" type="submit" name="reset">Reiniciar</button>
                        </div>
                    </form>


                    <div class="panel" style="flex-direction: column; gap: 1rem;">
                        <div class="left">
                            <div class="lista">
                                <h3>Números ingresados</h3>
                                <div class="cuadro" id="lista-numeros">
                                    <?php if (!empty($_SESSION['numeros'])): ?>
                                        <?php foreach ($_SESSION['numeros'] as $i => $num): ?>
                                            <div class="numero">
                                                <span><?= htmlspecialchars((string) $num) ?></span>
                                                <form method="post" style="display:inline;">
                                                    <input type="hidden" name="indice" value="<?= $i ?>">
                                                    <button type="submit" name="eliminar" title="Eliminar">❌</button>
                                                </form>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p style="opacity:0.7;">Ninguno aún</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card" data-rotation-factor="2">
                <div class="card-content">
                    <h2>Resultados</h2>
                    <p>Media y desviación estándar calculadas a partir de los números ingresados.</p>

                    <div class="resultado" style="margin-top: 1rem;">
                        <h3>Resultados:</h3>
                        <?php if ($resultado): ?>
                            <p><strong>Media:</strong></p>
                            <div class="valor"><?= htmlspecialchars((string) $resultado['media']) ?></div>

                            <p style="margin-top: 1rem;"><strong>Desviación estándar:</strong></p>
                            <div class="valor"><?= htmlspecialchars((string) $resultado['desviacion']) ?></div>
                        <?php else: ?>
                            <p style="opacity:0.8;">Aún no hay resultado. Presiona "Calcular" en la tarjeta izquierda.</p>
                        <?php endif; ?>
                    </div>

                    <div style="margin-top: auto; display:flex; gap:0.5rem; align-items:center;">
                        <div style="opacity:0.8; font-size:0.9rem;">Números totales:</div>
                        <div style="font-weight:700; font-size:1rem;"><?= count($_SESSION['numeros']) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
      <p>&copy; 2025 Cristian Giovanny Salgado Vasquez</p>
    </footer>

    <script src="main.js"></script>
</body>

</html>